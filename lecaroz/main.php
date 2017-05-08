<?php
include './includes/class.session.inc.php';
include './includes/class.TemplatePower.inc.php';

$session = new sessionclass();
$session->validar_sesion();

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/main.tpl" );

$tpl->prepare();

// Asignar las páginas que se cargaran en los frames

$tpl->assign("topframe", "menu.php?v=" . mt_rand());
//$tpl->assign("mainframe", "blank.php?v=" . mt_rand());
$tpl->assign("bottomframe", "status_v2.php?v=" . mt_rand());

// Imprimir el resultado
$tpl->printToScreen();
?>