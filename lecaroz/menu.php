<?php
// MENU.PHP -- Script que se encarga de generar el men principal segun el nivel del usuario.
// Usa 'sessionclass.php' para iniciar y autentificar la sesin.
// Usa 'class.TemplatePower.inc.php' para generar las plantillas de p�ina

include './includes/class.session.inc.php';
include './includes/class.TemplatePower.inc.php';

$session = new sessionclass();
$session->validar_sesion();

// Verificar si hay que cambiar la variable de sesion que contiene el menu actual
if (isset($_GET['menu']))
	$_SESSION['menu'] = $_GET['menu'];

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/menu.tpl" );

$tpl->prepare();

// Generar menu segun nivel de usuario
if (isset($_SESSION['menu'])) {
	$tpl->newBlock("menu");
	$tpl->assign("menunav","$_SESSION[menu]_nav.js?v=" . mt_rand());
}
else
	$tpl->newBlock("nomenu");

// Imprimir el resultado
$tpl->printToScreen();
?>
