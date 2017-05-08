<?php
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';
include './includes/class.TemplatePower.inc.php';

$session = new sessionclass($dsn);
$db = new DBclass($dsn, "autocommit=yes");

if (isset($_POST['id'])) {
	$sql = "DELETE FROM catalogo_nombres WHERE id IN (";
	foreach ($_POST['id'] as $i => $id)
		$sql .= $id . ($i < count($_POST['id']) - 1 ? ", " : ")");
	
	$db->query($sql);
	header("location: ./ban_nom_con.php");
	die;
}

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body", "./plantillas/ban/ban_nom_con.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

$result = $db->query("SELECT * FROM catalogo_nombres WHERE status = 1 ORDER BY num");

if ($result)
	foreach ($result as $reg) {
		$tpl->newBlock("fila");
		foreach ($reg as $tag => $value)
			$tpl->assign($tag, $value);
	}

// Si viene de una página que genero error
if (isset($_GET['codigo_error'])) {
	$tpl->newBlock("error");
	$tpl->newBlock("message");
	$tpl->assign( "message", $descripcion_error[$_GET['codigo_error']]);	
}

$tpl->printToScreen();
?>