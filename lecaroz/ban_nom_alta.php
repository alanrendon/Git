<?php
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';
include './includes/class.TemplatePower.inc.php';

$session = new sessionclass($dsn);
$db = new DBclass($dsn, "autocommit=yes");

$descripcion_error[1] = "El campo nombre no puede estar en blanco o relleno de espacios";

if (isset($_POST['num'])) {
	$nombre = trim(strtoupper($_POST['nombre']));
	
	if ($nombre == "") {
		header("location: ./ban_nom_alta.php?codigo_error=1");
		die;
	}
	
	$db->query("INSERT INTO catalogo_nombres (num, nombre, status) VALUES ($_POST[num], '$nombre', 1)");
	header("location: ./ban_nom_alta.php");
	die;
}


// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body", "./plantillas/ban/ban_nom_alta.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

$nums = $db->query("SELECT num FROM catalogo_nombres WHERE status = 1 ORDER BY num");
function lastNum() {
	global $nums;
	
	if (!$nums)
		return 1;
	
	$cont = 1;
	foreach ($nums as $num)
		if ($num['num'] == $cont)
			$cont++;
		else
			return $cont;
	
	return $cont;
}

$num = lastNum();
$tpl->assign("num", $num);

// Si viene de una página que genero error
if (isset($_GET['codigo_error'])) {
	$tpl->newBlock("error");
	$tpl->newBlock("message");
	$tpl->assign( "message", $descripcion_error[$_GET['codigo_error']]);	
}

$tpl->printToScreen();
?>