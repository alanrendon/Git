<?php
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';
include './includes/class.TemplatePower.inc.php';

$session = new sessionclass($dsn);
$db = new DBclass($dsn, "autocommit=yes");

//if ($_SESSION['iduser'] != 1) die(header('location: offline.htm'));

$descripcion_error[1] = "No hay resultados";

// [AJAX] Obtener nombre de compañía
if (isset($_GET['c'])) {
	$sql = "SELECT nombre_corto AS nombre FROM catalogo_companias WHERE num_cia = $_GET[c]";
	$result = $db->query($sql);
	
	die(trim($result[0]['nombre']));
}

if (isset($_GET['num_cia'])) {
	$sql = "DELETE FROM produccion WHERE num_cia = $_GET[num_cia] AND fecha >= '$_GET[fecha]';\n";
	$sql .= "DELETE FROM total_produccion WHERE numcia = $_GET[num_cia] AND fecha_total >= '$_GET[fecha]';\n";
	$sql .= "UPDATE total_panaderias SET efectivo = efectivo + raya_pagada, raya_pagada = 0, pro = FALSE WHERE num_cia = $_GET[num_cia] AND fecha >= '$_GET[fecha]';\n";
	
	$db->query($sql);
	
	die(header('location: ./pan_pro_dia_del.php'));
}

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body", "./plantillas/pan/pan_pro_dia_del.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

$tpl->assign('fecha', date('d/m/Y', mktime(0, 0, 0, date('n'), date('d') - 2, date('Y'))));

$tpl->printToScreen();
?>