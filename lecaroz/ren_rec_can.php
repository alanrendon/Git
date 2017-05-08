<?php
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';
include './includes/class.TemplatePower.inc.php';

$session = new sessionclass($dsn);
$db = new DBclass($dsn, "autocommit=yes");

//if ($_SESSION['iduser'] != 1) die("Modificando pantalla");

$descripcion_error[1] = "No hay resultados";

if (isset($_POST['id'])) {
	$db->query("UPDATE recibos_rentas SET status = 0 WHERE id = $_POST[id]");
	header('location: ./ren_rec_can.php');
	die;
}

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body", "./plantillas/ren/ren_rec_can.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (isset($_GET['arr'])) {
	$sql = "SELECT rr.id, num_recibo, cod_arrendador, nombre, num_local, nombre_local, nombre_arrendatario, renta, rr.agua, rr.mantenimiento, iva, isr_retenido, iva_retenido, neto,";
	$sql .= " concepto FROM recibos_rentas AS rr LEFT JOIN catalogo_arrendatarios AS art ON (art.id = local) LEFT JOIN catalogo_arrendadores USING (cod_arrendador) WHERE";
	$sql .= " cod_arrendador = $_GET[arr] AND num_recibo = $_GET[num]";
	$result = $db->query($sql);
	
	if (!$result) {
		header('location: ./ren_rec_can.php?codigo_error=1');
		die;
	}
	
	$tpl->newBlock('result');
	$tpl->assign('id', $result[0]['id']);
	$tpl->assign('cod_arr', $result[0]['cod_arrendador']);
	$tpl->assign('nombre', $result[0]['nombre']);
	$tpl->assign('num_recibo', $result[0]['num_recibo']);
	$tpl->assign('num_local', $result[0]['num_local']);
	$tpl->assign('nombre_local', $result[0]['nombre_local']);
	$tpl->assign('art', $result[0]['nombre_arrendatario']);
	$tpl->assign('renta', $result[0]['renta'] != 0 ? number_format($result[0]['renta'], 2, '.', ',') : '&nbsp;');
	$tpl->assign('agua', $result[0]['agua'] != 0 ? number_format($result[0]['agua'], 2, '.', ',') : '&nbsp;');
	$tpl->assign('mant', $result[0]['mantenimiento'] != 0 ? number_format($result[0]['mantenimiento'], 2, '.', ',') : '&nbsp;');
	$tpl->assign('iva', $result[0]['iva'] != 0 ? number_format($result[0]['iva'], 2, '.', ',') : '&nbsp;');
	$tpl->assign('ret_isr', $result[0]['isr_retenido'] != 0 ? number_format($result[0]['isr_retenido'], 2, '.', ',') : '&nbsp;');
	$tpl->assign('ret_iva', $result[0]['iva_retenido'] != 0 ? number_format($result[0]['iva_retenido'], 2, '.', ',') : '&nbsp;');
	$tpl->assign('neto', $result[0]['neto'] ? number_format($result[0]['neto'], 2, '.', ',') : '&nbsp;');
	if (trim($result[0]['concepto'] != '')) {
		$tpl->newBlock('concepto');
		$tpl->assign('concepto', $result[0]['concepto']);
	}
	$tpl->printToScreen();
	die;
}

$tpl->newBlock('datos');

if (!in_array($_SESSION['iduser'], array(1, 4, 19)))
	$tpl->assign('disabled', ' disabled');

$arrs = $db->query("SELECT cod_arrendador, nombre FROM catalogo_arrendadores ORDER BY cod_arrendador");
foreach ($arrs as $arr) {
	$tpl->newBlock("arr");
	$tpl->assign("cod", $arr['cod_arrendador']);
	$tpl->assign("nombre", $arr['nombre']);
}

// Si viene de una página que genero error
if (isset($_GET['codigo_error'])) {
	$tpl->newBlock("error");
	$tpl->newBlock("message");
	$tpl->assign("message", $descripcion_error[$_GET['codigo_error']]);	
}

$tpl->printToScreen();
?>