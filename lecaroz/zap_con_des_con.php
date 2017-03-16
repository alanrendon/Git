<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

$db = new DBclass($dsn, "autocommit=yes");
$session = new sessionclass($dsn);

$descripcion_error[1] = "No hay resultados";

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/zap/zap_con_des_con.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
if (isset($_SESSION['menu']))
	$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

$sql = "SELECT * FROM cat_conceptos_descuentos ORDER BY cod";
$result = $db->query($sql);

foreach ($result as $reg) {
	$tpl->newBlock('fila');
	$tpl->assign('cod', $reg['cod']);
	$tpl->assign('concepto', $reg['concepto']);
	$tpl->assign('tipo', $reg['tipo'] == 1 ? 'COMPRA' : 'PAGO');
}

$tpl->printToScreen();
?>