<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

$db = new DBclass($dsn, 'autocommit=yes');
$db_scans = new DBclass('pgsql://root:pobgnj@127.0.0.1:5432/scans', 'autocommit=yes');	// Coneccion a la base de datos de las imagenes
$session = new sessionclass($dsn);

if (isset($_GET['del'])) {
	$db_scans->query('TRUNCATE img_tar_tmp');
	die;
}

if (isset($_POST['Nombre'])) {
	// [17-Mar-2009] Obtener último número de la lista de contactos
	$sql = 'SELECT "Numero" FROM "Directorio" WHERE "Status" = 1 ORDER BY "Numero" DESC LIMIT 1';
	$tmp = $db->query($sql);
	$Numero = $tmp ? $tmp[0]['Numero'] + 1 : 1;
	
	$sql = 'INSERT INTO "Directorio" ("Nombre", "Contacto", "Puesto", "Email", "Observaciones", "Numero", "iduser", "tsmod") VALUES';
	$sql .= " ('$_POST[Nombre]', '$_POST[Contacto]', '$_POST[Puesto]', '$_POST[Email]', '$_POST[Observaciones]', $Numero, $_SESSION[iduser], now());\n";
	
	foreach ($_POST['Telefono'] as $i => $tel)
		if (trim($tel) != '') {
			$sql .= 'INSERT INTO "TelefonosDirectorio" ("IdContacto", "Telefono", "TipoTel", "ObsTel", "Status") VALUES';
			$sql .= " ((SELECT \"last_value\" FROM \"Directorio_IdContacto_seq\"), '$tel', {$_POST['TipoTel'][$i]}, '{$_POST['ObsTel'][$i]}', 1);\n";
		}
	
	foreach ($_POST['Tipo'] as $tipo)
		if ($tipo > 0) {
			$sql .= 'INSERT INTO "TiposContacto" ("IdContacto", "IdTipo", "Status") VALUES';
			$sql .= "((SELECT \"last_value\" FROM \"Directorio_IdContacto_seq\"), $tipo, 1);\n";
		}
	
	$db->query($sql);
	
	// Obtener ID del contacto
	$sql = 'SELECT "last_value" FROM "Directorio_IdContacto_seq"';
	$id = $db->query($sql);
	
	// Insertar imagen de la tarjeta de presentación del contacto
	$sql = 'INSERT INTO "ImagenTarjeta" ("IdContacto", "Imagen") ' . "SELECT {$id[0]['last_value']}, img FROM img_tar_tmp;\n";
	$sql .= "TRUNCATE img_tar_tmp;\n";
	$db_scans->query($sql);
	
	die(header('location: AltaDirectorio.php'));
}

$tpl = new TemplatePower('smarty/templates/AltaDirectorio.tpl');
$tpl->prepare();

// Seleccionar script para menu
$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$tipos = $db->query('SELECT "IdTipo", "Tipo" FROM "CatalogoTiposContacto" WHERE "Status" = 1 ORDER BY "Tipo"');
if ($tipos)
	for ($i = 0; $i < 5; $i++) {
		$tpl->newBlock('campo_tipo');
		foreach ($tipos as $t) {
			$tpl->newBlock('tipo');
			$tpl->assign('id', $t['IdTipo']);
			$tpl->assign('tipo', $t['Tipo']);
		}
		if ($i < 5)
			$tpl->assign('campo_tipo.salto', '<br />');
	}

$tpl->printToScreen();
?>