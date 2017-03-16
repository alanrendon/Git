<?php
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';
include './includes/class.TemplatePower.inc.php';

$session = new sessionclass($dsn);
$db = new DBclass($dsn, "autocommit=yes");

//if ($_SESSION['iduser'] != 1) die(header('location: offline.htm'));

$descripcion_error[1] = "No hay resultados";

// [AJAX] Obtener nombre de compaρνa
if (isset($_GET['c'])) {
	$sql = "SELECT nombre_corto AS nombre FROM catalogo_companias WHERE num_cia = $_GET[c]";
	$result = $db->query($sql);
	
	die(trim($result[0]['nombre']));
}

// [AJAX] Obtener nombre del proveedor
if (isset($_GET['p'])) {
	$sql = "SELECT nombre FROM catalogo_proveedores WHERE num_proveedor = $_GET[p]";
	$result = $db->query($sql);
	
	die(trim($result[0]['nombre']));
}

if (isset($_GET['accion']) && $_GET['accion'] == 'acre') {
	$sql = '';
	foreach ($_POST['ok'] as $id)
		$sql .= "UPDATE notas_credito_zap SET status = 1, iduser = $_SESSION[iduser], lastmod = now() WHERE id = $id;\n";
	
	$db->query($sql);
	
	die(header("location: $_SERVER[HTTP_REFERER]"));
}

if (isset($_GET['accion']) && $_GET['accion'] == 'del') {
	$sql = '';
	foreach ($_POST['x'] as $id)
		$sql .= "DELETE FROM notas_credito_zap WHERE id = $id;\n";
	
	$db->query($sql);
	
	die(header("location: $_SERVER[HTTP_REFERER]"));
}

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body", "./plantillas/zap/zap_not_cre_con.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (isset($_GET['num_cia'])) {
	$sql = 'SELECT nc.id, nc.num_cia, cc.nombre_corto AS nombre_cia, nc.num_proveedor AS num_pro, cp.nombre AS nombre_pro, nc.fecha, nc.folio, nc.concepto, nc.importe, nc.status, num_cia_apl, cca.nombre_corto AS nombre_cia_apl, folio_cheque, nc.cuenta FROM notas_credito_zap nc LEFT JOIN catalogo_proveedores cp USING (num_proveedor) LEFT JOIN catalogo_companias cc USING (num_cia) LEFT JOIN catalogo_companias cca ON (cca.num_cia = nc.num_cia_apl)';
	if ($_GET['num_cia'] > 0 || $_GET['num_pro'] > 0 || $_GET['folio'] > 0 || $_GET['fecha1'] != '' || $_GET['status'] >= 0) {
		$sql .= ' WHERE';
		$sql .= $_GET['num_cia'] > 0 ? " nc.num_cia = $_GET[num_cia]" : '';
		$sql .= $_GET['num_pro'] > 0 ? ($_GET['num_cia'] > 0 ? ' AND' : '') . " nc.num_proveedor = $_GET[num_pro]" : '';
		$sql .= $_GET['folio'] > 0 ? ($_GET['num_cia'] > 0 || $_GET['num_pro'] > 0 ? ' AND' : '') . " nc.folio = $_GET[folio]" : '';
		$sql .= $_GET['fecha1'] != '' ? ($_GET['num_cia'] > 0 || $_GET['num_pro'] > 0 || $_GET['folio'] > 0 ? ' AND' : '') . " nc.fecha " . ($_GET['fecha2'] != '' ? "BETWEEN '$_GET[fecha1]' AND '$_GET[fecha2]'" : "= '$_GET[fecha1]'") : '';
		$sql .= $_GET['status'] >= 0 ? ($_GET['num_cia'] > 0 || $_GET['num_pro'] > 0 || $_GET['folio'] > 0 || $_GET['fecha1'] != '' ? ' AND' : '') . " nc.status = $_GET[status]" : '';
	}
	$sql .= ' ORDER BY nc.num_cia, num_pro, folio';
	$result = $db->query($sql);
	
	if (!$result) die(header('location: ./zap_not_cre_con.php?codigo_error=1'));
	
	$tpl->newBlock('result');
	foreach ($result as $reg) {
		$tpl->newBlock('row');
		$tpl->assign('id', $reg['id']);
		$tpl->assign('ok_dis', $reg['status'] > 0 ? ' disabled' : '');
		$tpl->assign('x_dis', $reg['folio_cheque'] > 0 ? ' disabled' : '');
		$tpl->assign('mod_dis', $reg['folio_cheque'] > 0 ? ' disabled' : '');
		$tpl->assign('num_cia', $reg['num_cia']);
		$tpl->assign('nombre_cia', $reg['nombre_cia']);
		$tpl->assign('num_pro', $reg['num_pro']);
		$tpl->assign('nombre_pro', $reg['nombre_pro']);
		$tpl->assign('fecha', $reg['fecha']);
		$tpl->assign('folio', $reg['folio']);
		$tpl->assign('concepto', $reg['concepto'] != '' ? $reg['concepto'] : '&nbsp;');
		$tpl->assign('importe', number_format($reg['importe'], 2, '.', ','));
		switch ($reg['status']) {
			case 0: $status = '&nbsp;'; break;
			case 1: $status = 'ACREDITADO'; break;
			case 2: $status = 'APLICADO'; break;
		}
		$tpl->assign('status', $status);
		$tpl->assign('num_cia_apl', $reg['num_cia_apl'] != '' ? $reg['num_cia_apl'] : '&nbsp;');
		$tpl->assign('nombre_cia_apl', $reg['nombre_cia_apl'] != '' ? $reg['nombre_cia_apl'] : '&nbsp;');
		$tpl->assign('folio_cheque', $reg['folio_cheque'] > 0 ? $reg['folio_cheque'] : '&nbsp;');
		$tpl->assign('banco', $reg['cuenta'] > 0 ? ($reg['cuenta'] == 1 ? 'BANORTE' : 'SANTANDER') : '&nbsp;');
	}
	
	die($tpl->printToScreen());
}

$tpl->newBlock('datos');

// Si viene de una pαgina que genero error
if (isset($_GET['codigo_error'])) {
	$tpl->newBlock("error");
	$tpl->newBlock("message");
	$tpl->assign( "message", $descripcion_error[$_GET['codigo_error']]);	
}

$tpl->printToScreen();
?>