<?php
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';
include './includes/class.TemplatePower.inc.php';

$session = new sessionclass($dsn);
$db = new DBclass($dsn, "autocommit=yes");

//if ($_SESSION['iduser'] != 1) die(header('location: offline.htm'));

$descripcion_error[1] = "No hay resultados";

// [AJAX] Obtener compaρνa
if (isset($_GET['c'])) {
	$sql = "SELECT nombre_corto FROM catalogo_companias WHERE num_cia = $_GET[c]";
	$result = $db->query($sql);
	
	echo $result ? $result[0]['nombre_corto'] : '';
	die;
}

$numfilas = 20;

// Insertar datos
if (isset($_POST['num_cia'])) {
	$sql = 'SELECT num_cia, nombre_corto AS nombre, fecha FROM "VencimientoFacturas"';
	if ($_GET['num_cia'] > 0 || $_GET['fecha1'] != '' || $_GET['status'] == '') {
		$sql .= ' WHERE'
		
	}
}

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body", "./plantillas/ban/ban_ven_fac_con.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

$tpl->newBlock('datos');

$tpl->printToScreen();
?>