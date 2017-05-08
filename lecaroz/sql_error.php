<?php
// ERRORES DE SQL Y BASE DE DATOS

// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db3.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/sql_error.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (isset($_GET['error']) && $_GET['error'] == "db_error") {
	$tpl->newBlock("db_error");
	$tpl->assign("funcion",$_SESSION['funcion']);
	$tpl->assign("error",$_SESSION['db_error']);
}

if (isset($_GET['error']) && $_GET['error'] == "sql_error") {
	$tpl->newBlock("sql_error");
	$tpl->assign("funcion",$_SESSION['funcion']);
	$tpl->assign("error",substr($_SESSION['sql_error'],strpos($_SESSION['sql_error'],"ERROR:")+8));
	$tpl->assign("script",$_SESSION['script']);
}

$tpl->printToScreen();
die;
?>
