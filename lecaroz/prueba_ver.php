<?php
include './includes/class.TemplatePower.inc.php';

$tpl = new TemplatePower( "./prueba_ver.tpl" );
$tpl->prepare();

$tpl->newBlock("v1");
$tpl->printToScreen();
sleep(10);
unset($tpl);

$tpl = new TemplatePower( "./prueba_ver.tpl" );
$tpl->prepare();
$tpl->newBlock("v2");
$tpl->printToScreen();
?>