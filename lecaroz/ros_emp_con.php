<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

$session = new sessionclass($dsn);
$db = new DBclass($dsn, "autocommit=yes");

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/ros/ros_emp_con.tpl");
$tpl->prepare();

$emps = $db->query("SELECT num_emp, nombre, ap_paterno, ap_materno FROM catalogo_trabajadores WHERE num_cia = {$_SESSION['psr']['num_cia']} AND fecha_baja IS NULL ORDER BY nombre, ap_paterno");

if (!$emps) {
	$tpl->newBlock("no_emp");
	$tpl->printToScreen();
	die;
}

foreach ($emps as $emp) {
	$tpl->newBlock("emp");
	$tpl->assign("num_emp", $emp['num_emp']);
	$tpl->assign("nombre", trim("$emp[nombre] $emp[ap_paterno] $emp[ap_materno]"));
}

$tpl->printToScreen();
?>