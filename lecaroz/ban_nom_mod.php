<?php
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';
include './includes/class.TemplatePower.inc.php';

$session = new sessionclass($dsn);
$db = new DBclass($dsn, "autocommit=yes");

$descripcion_error[1] = "El campo nombre no puede estar en blanco o relleno de espacios";

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body", "./plantillas/ban/ban_nom_mod.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (isset($_POST['id'])) {
	$nombre = trim(strtoupper($_POST['nombre']));
	
	if ($nombre == "") {
		header("location: ./ban_nom_mod.php?id=$_POST[id]&codigo_error=1");
		die;
	}
	
	$db->query("UPDATE catalogo_nombres SET nombre = '$nombre' WHERE id = $_POST[id]");
	
	$tpl->newBlock("cerrar");
	$tpl->printToScreen();
	die;
}

$result = $db->query("SELECT id, num, nombre FROM catalogo_nombres WHERE id = $_GET[id]");

$tpl->newBlock("datos");
foreach ($result as $reg)
	foreach ($reg as $tag => $value)
		$tpl->assign($tag, $value);

// Si viene de una página que genero error
if (isset($_GET['codigo_error'])) {
	$tpl->newBlock("error");
	$tpl->newBlock("message");
	$tpl->assign( "message", $descripcion_error[$_GET['codigo_error']]);	
}

$tpl->printToScreen();
?>