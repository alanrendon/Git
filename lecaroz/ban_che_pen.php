<?php
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

$db = new DBclass($dsn, "autocommit=yes");
$session = new sessionclass($dsn);

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );
$tpl->assignInclude("body","./plantillas/ban/ban_che_pen.tpl");
$tpl->prepare();

$sql = "SELECT estado_cuenta.fecha AS fecha, a_nombre, estado_cuenta.folio AS folio, estado_cuenta.importe AS importe FROM estado_cuenta LEFT JOIN cheques USING (num_cia,folio) WHERE estado_cuenta.num_cia = $_GET[num_cia] AND tipo_mov = 'TRUE' AND fecha_con IS NULL AND estado_cuenta.cod_mov IN (5, 41) AND estado_cuenta.cuenta = $_GET[cuenta] AND cheques.cuenta = $_GET[cuenta] ORDER BY estado_cuenta.num_cia";
$result = $db->query($sql);

$sql = "SELECT nombre_corto FROM catalogo_companias WHERE num_cia = $_GET[num_cia]";
$cia = $db->query($sql);

$tpl->assign("num_cia", $_GET['num_cia']);
$tpl->assign("nombre_cia", $cia[0]['nombre_corto']);

$total = 0;
for ($i = 0; $i < count($result); $i++) {
	$tpl->newBlock("fila");
	$tpl->assign("fecha", $result[$i]['fecha']);
	$tpl->assign("beneficiario", $result[$i]['a_nombre']);
	$tpl->assign("folio", $result[$i]['folio']);
	$tpl->assign("importe", number_format($result[$i]['importe'], 2, ".", ","));
	
	$total += $result[$i]['importe'];
}
$tpl->assign("_ROOT.total", number_format($total, 2, ".", ","));

$tpl->printToScreen();
?>