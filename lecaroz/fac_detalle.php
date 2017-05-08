<?php
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

$session = new sessionclass($dsn);
$db = new DBclass($dsn, "autocommit=yes");

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower("./plantillas/header.tpl");

// Incluir el cuerpo del documento
$tpl->assignInclude("body", "./plantillas/pan/fac_detalle.tpl");
$tpl->prepare();

$tpl->assign('cuenta', $_GET['cuenta'] == 1 ? 'BANORTE' : 'SANTANDER');
$tpl->assign('fecha', $_GET['fecha_cheque']);
$tpl->assign('folio', $_GET['folio_cheque']);
$tpl->assign('fecha_con', $_GET['fecha_con'] != '' ? $_GET['fecha_con'] : '&nbsp;');

$tpl->printToScreen();
?>