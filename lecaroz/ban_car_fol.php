<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);

$descripcion_error[1] = "No hay resultados";

//if ($_SESSION['iduser'] != 1) die(header('location: ./offline.htm'));

// Conectarse a la base de datos
$db = new DBclass($dsn, "autocommit=yes");

if (isset($_GET['c'])) {
	$sql = "SELECT nombre FROM catalogo_companias WHERE num_cia = $_GET[c] AND num_cia BETWEEN " . ($_SESSION['tipo_usuario'] == 2 ? '900 AND 998' : '1 AND 899');
	$result = $db->query($sql);
	
	$nombre = '';
	if ($result) $nombre = $result[0]['nombre'];
	
	echo $nombre;
	die;
}

if (isset($_POST['num_cia'])) {
	$tmp = $db->query("SELECT folio FROM cartas_foleadas WHERE num_cia = $_POST[num_cia] ORDER BY folio DESC LIMIT 1");
	$folio = $tmp ? $tmp[0]['folio'] + 1 : 1;
	$fecha = date('d/m/Y');
	
	$sql = "INSERT INTO cartas_foleadas (num_cia, folio, fecha, atencion, referencia, cuerpo, iduser, seguimiento, \"IdContacto\") VALUES ($_POST[num_cia], $folio, '$fecha', '" . strtoupper(trim($_POST['atencion'])) . "', '" . strtoupper(trim($_POST['referencia'])) . "', '$_POST[cuerpo]', $_SESSION[iduser], $_POST[seguimiento], $_POST[idcontacto])";
	$db->query($sql);
	
	// Hacer un nuevo objeto TemplatePower
	$tpl = new TemplatePower('./plantillas/ban/' . ($_SESSION['tipo_usuario'] == 2 ? '_carta_zap.tpl' : '_carta.tpl'));
	$tpl->prepare();
	
	$tpl->newBlock('hoja');
	
	$nombre_cia = $db->query("SELECT nombre FROM catalogo_companias WHERE num_cia = $_POST[num_cia]");
	
	$html_characters = array('á', 'é', 'í', 'ó', 'ú', 'ñ', 'Ñ', "\n");
	$html_character_entities = array('&aacute;', '&eacute;', '&iacute;', '&oacute;', '&uacute;', '&ntilde;', '&Ntilde;', '<br />');
	
	$tpl->assign('folio', $folio);
	$tpl->assign('dia', date('d'));
	$tpl->assign('mes', mes_escrito(date('n')));
	$tpl->assign('anyo', date('Y'));
	$tpl->assign('empresa', $nombre_cia[0]['nombre']);
	$tpl->assign('atencion', $_POST['atencion']);
	$tpl->assign('referencia', $_POST['referencia']);
	$tpl->assign('cuerpo', str_replace($html_characters, $html_character_entities, $_POST['cuerpo']));
	$tpl->assign('fecha', date('d/m/Y'));
	
	if ($_POST['tipo'] == 1)
		die($tpl->printToScreen());
	else {
		header('Content-type: application/vnd.ms-word');
		header('Content-Disposition: attachment; filename=carta_' . $_POST['num_cia'] . '_' . $folio . '.doc');
		
		die($tpl->getOutputContent());
	}
}

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/ban/ban_car_fol.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

$sql = 'SELECT "IdContacto", "Numero", "Nombre" FROM "Directorio"/* WHERE "iduser" ' . ($_SESSION['iduser'] < 28 ? '< 28' : '> 27') . '*/ ORDER BY "Nombre"';
$dir = $db->query($sql);

if ($dir)
	foreach ($dir as $d) {
		$tpl->newBlock('contacto');
		$tpl->assign('id', $d['IdContacto']);
		$tpl->assign('nombre', /*$d['Numero'] . ' ' . */$d['Nombre']);
	}

$tpl->printToScreen();
?>