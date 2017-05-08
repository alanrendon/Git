<?php
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

$session = new sessionclass($dsn);
$db = new DBclass($dsn, "autocommit=yes");

if (isset($_GET['precio'])) {
	$db->query("UPDATE precio_fac_rancho SET precio = $_GET[precio]");
	header("location: ./pan_pre_lec.php");
	die;
}

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower("./plantillas/header.tpl");

// Incluir el cuerpo del documento
$tpl->assignInclude("body", "./plantillas/pan/pan_pre_lec.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
if (isset($_SESSION['menu']))
	$tpl->assign("menucnt", "$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

$precio = $db->query("SELECT precio FROM precio_fac_rancho");
$tpl->assign("precio", $precio ? number_format($precio[0]['precio'], 2, ".", "") : "");

$tpl->printToScreen();
?>