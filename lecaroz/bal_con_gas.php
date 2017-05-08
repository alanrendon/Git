<?php
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';
include './includes/class.TemplatePower.inc.php';

$session = new sessionclass($dsn);
$db = new DBclass($dsn, "autocommit=yes");

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body", "./plantillas/bal/bal_con_gas.tpl");
$tpl->prepare();

$fecha1 = "01/$_GET[mes]/$_GET[anio]";
$fecha2 = date("d/m/Y", mktime(0, 0, 0, $_GET['mes'] + 1, 0, $_GET['anio']));
$desc = $db->query("SELECT descripcion FROM catalogo_gastos WHERE codgastos = $_GET[cod]");
$result = $db->query("SELECT fecha, movimiento_gastos.concepto, folio, a_nombre, movimiento_gastos.importe FROM movimiento_gastos LEFT JOIN cheques USING (num_cia, fecha, folio) WHERE num_cia = $_GET[num_cia] AND fecha BETWEEN '$fecha1' AND '$fecha2' AND movimiento_gastos.codgastos = $_GET[cod] ORDER BY captura, fecha");

$tpl->assign('cod', $_GET['cod']);
$tpl->assign('desc', $desc[0]['descripcion']);

$total = 0;
foreach ($result as $reg) {
	$tpl->newBlock('fila');
	$tpl->assign('fecha', $reg['fecha']);
	$tpl->assign('concepto', trim($reg['concepto']) != '' ? $reg['concepto'] : '&nbsp;');
	$tpl->assign('folio', $reg['folio'] > 0 ? $reg['folio'] : '&nbsp;');
	$tpl->assign('a_nombre', trim($reg['a_nombre']) != '' ? trim($reg['a_nombre']) : '&nbsp;');
	$tpl->assign('importe', number_format($reg['importe'], 2, '.', ','));
	$total += $reg['importe'];
}
$tpl->assign('_ROOT.total', number_format($total, 2, '.', ','));
$tpl->printToScreen();
?>