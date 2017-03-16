<?php
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';
include './includes/class.TemplatePower.inc.php';

$session = new sessionclass($dsn);
$db = new DBclass($dsn, "autocommit=yes");

//if ($_SESSION['iduser'] != 1) die("Modificando pantalla");

$descripcion_error[1] = "No hay resultados";

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body", "./plantillas/ren/ren_rec_sus.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (isset($_POST['idrecibo'])) {
	$renta = get_val($_POST['renta']);
	$agua = get_val($_POST['agua']);
	$mant = get_val($_POST['mantenimiento']);
	$iva = get_val($_POST['iva']);
	$ret_isr = get_val($_POST['ret_isr_imp']);
	$ret_iva = get_val($_POST['ret_iva_imp']);
	$neto = get_val($_POST['neto']);
	$fisr = isset($_POST['ret_isr']) ? 'TRUE' : 'FALSE';
	$fiva = isset($_POST['ret_iva']) ? 'TRUE' : 'FALSE';
	
	// Validar que el folio no este ya usado
	if ($db->query("SELECT num_recibo FROM recibos_rentas AS rr LEFT JOIN catalogo_arrendatarios AS art ON (local = art.id) LEFT JOIN catalogo_arrendadores AS arr USING (cod_arrendador) WHERE cod_arrendador = $_POST[cod_arr] AND num_recibo = $_POST[num_recibo]")) {
		header("location: ./ren_rec_sus.php?arr=$_POST[cod_arr]&num_recibo=$_POST[num_recibo_old]&folio=$_POST[num_recibo]");
		die;
	}
	
	// Modificar catálogo
	$sql = "UPDATE catalogo_arrendatarios SET renta_con_recibo = $renta, agua = $agua, mantenimiento = $mant, retencion_isr = '$fisr', retencion_iva = '$fiva' WHERE id = $_POST[idlocal];\n";
	// Dar de baja el recibo a sustituir
	$sql .= "UPDATE recibos_rentas SET status = 0 WHERE id = $_POST[idrecibo];\n";
	// Insertar recibo sustituto
	$sql .= "INSERT INTO recibos_rentas (num_recibo, renta, agua, mantenimiento, iva, isr_retenido, iva_retenido, neto, fecha, bloque, impreso, fecha_pago, concepto, local, status)";
	$sql .= " SELECT $_POST[num_recibo], $renta, $agua, $mant, $iva, $ret_isr, $ret_iva, $neto, fecha, bloque, impreso, fecha_pago, 'SUSTITUYE A $_POST[num_recibo_old]', local, 1";
	$sql .= " FROM recibos_rentas WHERE id = $_POST[idrecibo];\n";
	
	$db->query($sql);
	
	// Obtener homoclave
	$homoclave = $db->query("SELECT homoclave FROM catalogo_arrendadores WHERE cod_arrendador = $_POST[cod_arr]");
	$tpl->newBlock('impRecibo');
	$tpl->assign('arr', $homoclave[0]['homoclave']);
	$tpl->assign('ini', $_POST['num_recibo']);
	$tpl->assign('fin', $_POST['num_recibo']);
	$tpl->printToScreen();
	die;
}

if (isset($_GET['arr'])) {
	$sql = "SELECT rr.id AS idrecibo, num_recibo, local AS idlocal, num_local, nombre_local, cod_arrendador, arr.nombre AS nombre_arr, nombre_arrendatario AS nombre_art, renta_con_recibo";
	$sql .= " AS renta, rr.agua, rr.mantenimiento, retencion_isr AS ret_isr, retencion_iva AS ret_iva FROM recibos_rentas AS rr LEFT JOIN catalogo_arrendatarios AS art ON (art.id = local)";
	$sql .= " LEFT JOIN catalogo_arrendadores AS arr USING (cod_arrendador) WHERE cod_arrendador = $_GET[arr] AND num_recibo = $_GET[num_recibo]";
	$result = $db->query($sql);
	
	if (!$result) {
		header('location: ./ren_rec_sus.php?codigo_error=1');
		die;
	}
	
	$tpl->newBlock('recibo');
	$tpl->assign('cod_arr', $result[0]['cod_arrendador']);
	$tpl->assign('nombre_arr', $result[0]['nombre_arr']);
	$tpl->assign('idrecibo', $result[0]['idrecibo']);
	$tpl->assign('num_recibo', $result[0]['num_recibo']);
	$tpl->assign('idlocal', $result[0]['idlocal']);
	$tpl->assign('num_local', $result[0]['num_local']);
	$tpl->assign('nombre_local', $result[0]['nombre_local']);
	$tpl->assign('nombre_art', $result[0]['nombre_art']);
	$tpl->assign('renta', $result[0]['renta'] != 0 ? number_format($result[0]['renta'], 2, '.', ',') : '');
	$tpl->assign('agua', $result[0]['agua'] != 0 ? number_format($result[0]['agua'], 2, '.', ',') : '');
	$tpl->assign('mantenimiento', $result[0]['mantenimiento'] != 0 ? number_format($result[0]['mantenimiento'], 2, '.', ',') : '');
	$tpl->assign('isr_checked', $result[0]['ret_isr'] == 't' ? ' checked' : '');
	$tpl->assign('iva_checked', $result[0]['ret_iva'] == 't' ? ' checked' : '');
	$iva = ($result[0]['renta'] + $result[0]['mantenimiento']) * 0.15;
	$ret_isr = $result[0]['ret_isr'] == 't' ? $result[0]['renta'] * 0.10 : '';
	$ret_iva = $result[0]['ret_iva'] == 't' ? $result[0]['renta'] * 0.10 : '';
	$neto = $result[0]['renta'] + $result[0]['mantenimiento'] + $iva + $result[0]['agua'] - $ret_isr - $ret_iva;
	$tpl->assign('iva', $iva != 0 ? number_format($iva, 2, '.', ',') : '');
	$tpl->assign('ret_isr_imp', $ret_isr != 0 ? number_format($ret_isr, 2, '.', ',') : '');
	$tpl->assign('ret_iva_imp', $ret_iva != 0 ? number_format($ret_iva, 2, '.', ',') : '');
	$tpl->assign('neto', $neto != 0 ? number_format($neto, 2, '.', ',') : '');
	
	if (isset($_GET['folio'])) {
		$tpl->newBlock('error_folio');
		$tpl->assign('folio', $_GET['folio']);
	}
	
	$tpl->printToScreen();
	die;
}

$tpl->newBlock('datos');

if (isset($_GET['codigo_error'])) {
	$tpl->newBlock("error");
	$tpl->newBlock("message");
	$tpl->assign( "message", $descripcion_error[$_GET['codigo_error']]);	
}

$tpl->printToScreen();
?>