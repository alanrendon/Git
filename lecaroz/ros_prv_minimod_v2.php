<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';
include './includes/class.TemplatePower.inc.php';

// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);
$db = new DBclass($dsn, "autocommit=yes");

// --------------------------------- Generar pantalla --------------------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/ros/ros_prv_minimod_v2.tpl");
$tpl->prepare();

if (isset($_POST['codmp'])) {
	if ($id = $db->query("SELECT id FROM precios_guerra WHERE num_cia = {$_SESSION['psr']['num_cia']} AND codmp = $_POST[codmp]"))
		$sql = "UPDATE precios_guerra SET precio_venta = $_POST[precio] WHERE id = {$id[0]['id']}";
	else
		$sql = "INSERT INTO precios_guerra (num_cia, codmp, num_proveedor, precio_venta) VALUES ({$_SESSION['psr']['num_cia']}, $_POST[codmp], 13, $_POST[precio])";
	$db->query($sql);
	
	$tpl->newBlock("cerrar");
	$tpl->assign("i", $_POST['i']);
	$tpl->assign("precio", $_POST['precio']);
	$tpl->printToScreen();
	die;
}

$mp = $db->query("SELECT nombre FROM catalogo_mat_primas WHERE codmp = $_GET[codmp]");
$precio = $db->query("SELECT precio_venta FROM precios_guerra WHERE num_cia = {$_SESSION['psr']['num_cia']} AND codmp = $_GET[codmp]");

$tpl->newBlock("datos");
$tpl->assign("i", $_GET['i']);
$tpl->assign("codmp", $_GET['codmp']);
$tpl->assign("nombre", $_GET['codmp'] != 1601 ? $mp[0]['nombre'] : "POLLOS ADOBADOS");
$tpl->assign("precio", $precio ? number_format($precio[0]['precio_venta'], 2, ".", ",") : "");

$tpl->printToScreen();
die;
?>