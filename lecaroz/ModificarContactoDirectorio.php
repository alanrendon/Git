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

$tpl = new TemplatePower('smarty/templates/ModificarContactoDirectorio.tpl');
$tpl->prepare();

if (isset($_POST['IdContacto'])) {
	$sql = 'UPDATE "Directorio" SET';
	$sql .= ' "Nombre" = \'' . $_POST['Nombre'] . '\',';
	$sql .= ' "Contacto" = \'' . $_POST['Contacto'] . '\',';
	$sql .= ' "Puesto" = \'' . $_POST['Puesto'] . '\',';
	$sql .= ' "Email" = \'' . $_POST['Email'] . '\',';
	$sql .= ' "Observaciones" = \'' .$_POST['Observaciones'] . '\',';
	$sql .= ' "iduser" = ' . $_SESSION['iduser'] . ',';
	$sql .= ' "tsmod" = now()';
	$sql .= ' WHERE "IdContacto" = ' . $_POST['IdContacto'] . ";\n";
	
	$sql .= 'UPDATE "TelefonosDirectorio" SET "Status" = 0 WHERE "IdContacto" = ' . $_POST['IdContacto'] . ";\n";
	
	foreach ($_POST['Telefono'] as $i => $tel)
		if (trim($tel) != '') {
			if ($id = $db->query('SELECT "IdTelefono" FROM "TelefonosDirectorio" WHERE "IdContacto" = ' . $_POST['IdContacto'] . ' AND "Telefono" = \'' . $tel . '\'')) {
				$sql .= 'UPDATE "TelefonosDirectorio" SET "Status" = 1, "TipoTel" = ' . $_POST['TipoTel'][$i] . ', "ObsTel" = \'' . $_POST['ObsTel'][$i] . '\'';
				$sql .= ' WHERE "IdTelefono" = ' . $id[0]['IdTelefono'] . ";\n";
			}
			else {
				$sql .= 'INSERT INTO "TelefonosDirectorio" ("IdContacto", "Telefono", "TipoTel", "ObsTel", "Status") VALUES';
				$sql .= " ($_POST[IdContacto], '$tel', {$_POST['TipoTel'][$i]}, '{$_POST['ObsTel'][$i]}', 1);\n";
			}
		}
	
	$sql .= 'UPDATE "TiposContacto" SET "Status" = 0 WHERE "IdContacto" = ' . $_POST['IdContacto'] . ";\n";
	
	foreach ($_POST['Tipo'] as $tipo)
		if ($tipo > 0) {
			if ($id = $db->query('SELECT "IdTipoContacto" FROM "TiposContacto" WHERE "IdContacto" = ' . $_POST['IdContacto'] . ' AND "IdTipo" = ' . $tipo))
				$sql .= 'UPDATE "TiposContacto" SET "Status" = 1 WHERE "IdTipoContacto" = ' . $id[0]['IdTipoContacto'] . ";\n";
			else {
				$sql .= 'INSERT INTO "TiposContacto" ("IdContacto", "IdTipo", "Status") VALUES';
				$sql .= "($_POST[IdContacto], $tipo, 1);\n";
			}
		}
	//echo "<pre>$sql</pre>";die;
	$db->query($sql);
	
	// Insertar imagen de la tarjeta de presentación del contacto
	$sql = 'DELETE FROM "ImagenTarjeta" WHERE "IdContacto" = ' . $_POST['IdContacto'] . ";\n";
	$sql .= 'INSERT INTO "ImagenTarjeta" ("IdContacto", "Imagen") ' . "SELECT $_POST[IdContacto], img FROM img_tar_tmp;\n";
	$sql .= "TRUNCATE img_tar_tmp;\n";
	$db_scans->query($sql);
	
	$tpl->newBlock('close');
	$tpl->assign('id', $_POST['IdContacto']);
	$tpl->printToScreen();
	
	die;
}

$tpl = new TemplatePower('smarty/templates/ModificarContactoDirectorio.tpl');
$tpl->prepare();

$sql = 'SELECT "IdContacto", "Nombre", "Contacto", "Puesto", "Email", "Observaciones" FROM "Directorio" WHERE "IdContacto" = ' . $_GET['id'];
$result = $db->query($sql);
$reg = $result[0];

$tpl->newBlock('mod');

foreach ($reg as $k => $v)
	$tpl->assign($k, $v);

$sql = 'SELECT "Telefono", "TipoTel", "ObsTel" FROM "TelefonosDirectorio" WHERE "IdContacto" = ' . $_GET['id'] . ' AND "Status" = 1 ORDER BY "IdTelefono"';
$telefonos = $db->query($sql);

if ($telefonos)
	foreach ($telefonos as $k => $v) {
		$tpl->assign('Telefono' . $k, $v['Telefono']);
		$tpl->assign('ObsTel' . $k, $v['ObsTel']);
		$tpl->assign('TipoTel' . $k . '_' . $v['TipoTel'], ' selected');
	}

$sql = 'TRUNCATE img_tar_tmp;' . "\n";
$sql .= 'INSERT INTO img_tar_tmp ("img") SELECT "Imagen" FROM "ImagenTarjeta" WHERE "IdContacto" = ' . $_GET['id'] . ";\n";
$db_scans->query($sql);

if ($db_scans->query('SELECT img FROM img_tar_tmp'))
	$tpl->assign('img', '<img src="Tarjeta.php?width=240" class="tarjeta" />');
else
	$tpl->assign('&nbsp;');

$sql = 'SELECT "IdTipo" FROM "TiposContacto" WHERE "IdContacto" = ' . $_GET['id'] . ' AND "Status" = 1 ORDER BY "IdTipo" DESC';
$tmp = $db->query($sql);
$tiposContacto = array();
if ($tmp)
	foreach ($tmp as $t)
		$tiposContacto[] = $t['IdTipo'];

$tipos = $db->query('SELECT "IdTipo", "Tipo" FROM "CatalogoTiposContacto" WHERE "Status" = 1 ORDER BY "Tipo" DESC');
if ($tipos)
	for ($i = 0; $i < 5; $i++) {
		$tipo = array_pop($tiposContacto);
		
		$tpl->newBlock('campo_tipo');
		foreach ($tipos as $t) {
			$tpl->newBlock('tipo');
			$tpl->assign('id', $t['IdTipo']);
			$tpl->assign('tipo', $t['Tipo']);
			
			if ($t['IdTipo'] == $tipo)
				$tpl->assign('selected', ' selected');
		}
		if ($i < 5)
			$tpl->assign('campo_tipo.salto', '<br />');
	}

$tpl->printToScreen();
?>