<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

$db = new DBclass($dsn, "autocommit=yes");
$session = new sessionclass($dsn);

$users = array(28, 29, 30, 31);

$descripcion_error[1] = "No hay resultados";

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/ban/ban_dif_sal_pro.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
if (isset($_SESSION['menu']))
	$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (isset($_GET['num_cia'])) {
	$mes = $_GET['mes'];
	$anio = $_GET['anio'];
	$fecha = date('d/m/Y', mktime(0, 0, 0, $mes + 1, 0, $anio));
	
	$sql = 'SELECT num_cia, nombre_corto AS nombre_cia, sum(saldo_libros) AS saldo FROM saldos s LEFT JOIN catalogo_companias cc USING (num_cia) WHERE num_cia BETWEEN ' . ($_SESSION['tipo_usuario'] == 2 ? '900 AND 998' : '1 AND 899');
	$sql .= $_GET['num_cia'] > 0 ? " AND num_cia = $_GET[num_cia]" : '';
	$sql .= $_GET['cuenta'] > 0 ? " AND cuenta = $_GET[cuenta]" : '';
	$sql .= ' GROUP BY num_cia, nombre_cia ORDER BY num_cia';
	$result = $db->query($sql);
	
	if (!$result)
		die(header('location: ./ban_dif_sal_pro.php?codigo_error=1'));
	
	$tpl->newBlock('listado');
	$tpl->assign('mes', mes_escrito($mes));
	$tpl->assign('anio', $anio);
	$total = array('saldo' => 0, 'pasivo' => 0, 'dif' => 0);
	foreach ($result as $cia) {
		$sql = "SELECT sum(CASE WHEN tipo_mov = 'FALSE' THEN -importe ELSE importe END) AS mov FROM estado_cuenta WHERE num_cia = $cia[num_cia] AND fecha > '$fecha'";
		$movs = $db->query($sql);
		
		$saldo = round(floatval($cia['saldo']), 2);
		if ($movs)
			foreach ($movs as $mov)
				$saldo += round(floatval($mov['mov']), 2);
		
		$sql = "SELECT sum(total) AS total FROM (SELECT sum(total) AS total FROM pasivo_proveedores WHERE num_cia = $cia[num_cia] AND fecha <= '$fecha' UNION SELECT sum(total) AS total FROM facturas_pagadas WHERE num_cia = $cia[num_cia] AND fecha <= '$fecha' AND fecha_cheque > '$fecha') pasivo";
		$tmp = $db->query($sql);
		$pasivo = $tmp ? round(floatval($tmp[0]['total']), 2) : 0;
		
		$dif = $saldo - $pasivo;
		
		$total['saldo'] += $saldo;
		$total['pasivo'] += $pasivo;
		$total['dif'] += $dif;
		
		$tpl->newBlock('fila');
		$tpl->assign('num_cia', $cia['num_cia']);
		$tpl->assign('nombre', $cia['nombre_cia']);
		$tpl->assign('saldo', $saldo != 0 ? number_format($saldo, 2, '.', ',') : '&nbsp;');
		$tpl->assign('pasivo', $pasivo != 0 ? number_format($pasivo, 2, '.', ',') : '&nbsp;');
		$tpl->assign('dif', $dif != 0 ? number_format($dif, 2, '.', ',') : '&nbsp;');
		
		$tpl->assign('listado.saldo', number_format($total['saldo'], 2, '.', ','));
		$tpl->assign('listado.pasivo', number_format($total['pasivo'], 2, '.', ','));
		$tpl->assign('listado.dif', number_format($total['dif'], 2, '.', ','));
	}
	
	die($tpl->printToScreen());
}

$tpl->newBlock("datos");

$tpl->assign(date('n', mktime(0, 0, 0, date('n'), 0)), ' selected');
$tpl->assign('anio', date('Y', mktime(0, 0, 0, date('n'), 0)));

// Si viene de una página que genero error
if (isset($_GET['codigo_error'])) {
	$tpl->newBlock("error");
	$tpl->newBlock("message");
	$tpl->assign( "message", $descripcion_error[$_GET['codigo_error']]);	
}

if (isset($_GET['mensaje'])) {
	$tpl->newBlock("message");
	$tpl->assign("message", $_GET['mensaje']);
}

$tpl->printToScreen();
die();
?>