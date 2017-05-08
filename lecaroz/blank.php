<?php
include './includes/class.session.inc.php';
include './includes/class.TemplatePower.inc.php';

$session = new sessionclass();
$session->validar_sesion();

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/blank.tpl" );

$tpl->prepare();

if (isset($_SESSION['menu'])) {
	$tpl->newBlock("menu");
	$tpl->assign("menucnt", "$_SESSION[menu]_cnt.js?v=" . mt_rand());
}

// Imprimir el resultado
$tpl->printToScreen();
?>
