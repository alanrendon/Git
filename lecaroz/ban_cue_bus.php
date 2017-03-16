<?php
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';
include './includes/class.TemplatePower.inc.php';

$session = new sessionclass($dsn);
$db = new DBclass($dsn, "autocommit=yes");

//if ($_SESSION['iduser'] == 1) die("Pantalla no disponible...");

$users = array(28, 29, 30, 31,);

if (isset($_GET['num_cia'])) {
	$result = $db->query("SELECT id FROM estados_cuenta_recibidos WHERE num_cia = $_GET[num_cia] AND cuenta = $_GET[cuenta] AND mes = $_GET[mes] AND anio = $_GET[anio]");
	
	if ($result)
		die('0');
	
	$sql = "INSERT INTO estados_cuenta_recibidos (num_cia, cuenta, mes, anio, iduser) VALUES ($_GET[num_cia], $_GET[cuenta], $_GET[mes], $_GET[anio], $_SESSION[iduser])";
	$db->query($sql);
	
	die('1');
}

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body", "./plantillas/ban/ban_cue_bus.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt", "$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

$tpl->assign(date('n', mktime(0, 0, 0, date('n'), 0, date('Y'))), ' selected');
$tpl->assign('anio', date('Y', mktime(0, 0, 0, date('n'), 0, date('Y'))));

// Si viene de una página que genero error
if (isset($_GET['codigo_error'])) {
	$tpl->newBlock("error");
	$tpl->newBlock("message");
	$tpl->assign( "message", $descripcion_error[$_GET['codigo_error']]);
}

$sql = "SELECT num_cia, nombre, clabe_cuenta, clabe_cuenta2, nombre_contador, direccion FROM catalogo_companias LEFT JOIN catalogo_contadores USING (idcontador) " . (!in_array($_SESSION['iduser'], array(1, 10)) ? ' WHERE num_cia BETWEEN ' . ($_SESSION['tipo_usuario'] == 2 ? '900 AND 998' : '1 AND 899') : '') . " ORDER BY num_cia";
$result = $db->query($sql);

foreach ($result as $reg)
	if ($reg['clabe_cuenta'] != '') {
		$tpl->newBlock('cuenta');
		$tpl->assign('cuenta', $reg['clabe_cuenta']);
		$tpl->assign('banco', 'BANORTE');
		$tpl->assign('num_cia', $reg['num_cia']);
		$tpl->assign('nombre', $reg['nombre']);
		$tpl->assign('clave_banco', 1);
		$tpl->assign('contador', $reg['nombre_contador']);
		$tpl->assign('dir', ereg_replace('[^a-zA-Z0-9\.\#\,\s]' , ' ', $reg['direccion']));
	}

foreach ($result as $reg)
	if ($reg['clabe_cuenta2'] != '') {
		$tpl->newBlock('cuenta');
		$tpl->assign('cuenta', $reg['clabe_cuenta2']);
		$tpl->assign('banco', 'SANTANDER');
		$tpl->assign('num_cia', $reg['num_cia']);
		$tpl->assign('nombre', $reg['nombre']);
		$tpl->assign('clave_banco', 2);
		$tpl->assign('contador', $reg['nombre_contador']);
		$tpl->assign('dir', ereg_replace('[^a-zA-Z0-9\.\#\,\s]' , ' ', $reg['direccion']));
	}

$tpl->printToScreen();
?>