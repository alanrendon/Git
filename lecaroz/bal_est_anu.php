<?php
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';
include './includes/class.TemplatePower.inc.php';

$session = new sessionclass($dsn);
$db = new DBclass($dsn, "autocommit=yes");

$descripcion_error[1] = "No hay resultados";

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/bal/bal_est_anu.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (isset($_GET['num_cia'])) {
	$fecha1 = "01/01/$_GET[anio]";
	$fecha2 = $_GET['anio'] < date("Y") ? "31/12/$_GET[anio]" : date("d/m/Y", mktime(0, 0, 0, date("n"), 0, $_GET['anio']));
	
	$sql = "(SELECT num_cia, nombre_corto, sum(utilidad_neta) AS bal FROM balances_pan LEFT JOIN catalogo_companias USING (num_cia) WHERE anio = $_GET[anio]";
	$sql .= $_GET['num_cia'] > 0 ? " AND num_cia = $_GET[num_cia]" : " AND num_cia < 100";
	$sql .= " GROUP BY num_cia, nombre_corto)";
	$sql .= " UNION "; 
	$sql .= "(SELECT num_cia, nombre_corto, sum(utilidad_neta) AS bal FROM balances_ros LEFT JOIN catalogo_companias USING (num_cia) WHERE anio = $_GET[anio]";
	$sql .= $_GET['num_cia'] > 0 ? " AND num_cia = $_GET[num_cia]" : " AND num_cia BETWEEN 100 AND 200 OR num_cia IN (702, 704)";
	$sql .= " GROUP BY num_cia, nombre_corto)";
	$sql .= " ORDER BY num_cia";
	$bal = $db->query($sql);
	
	$sql = "SELECT num_cia, sum(saldo_libros) AS saldo_libros FROM saldos WHERE";
	$sql .= $_GET['num_cia'] > 0 ? " num_cia = $_GET[num_cia]" : " num_cia BETWEEN 1 AND 800";
	$sql .= " GROUP BY num_cia ORDER BY num_cia";
	$saldo = $db->query($sql);
	
	$sql = "SELECT num_cia, sum(total) AS pro FROM pasivo_proveedores";
	$sql .= $_GET['num_cia'] > 0 ? " WHERE num_cia = $_GET[num_cia]" : "";
	$sql .= " GROUP BY num_cia ORDER BY num_cia";
	$pro = $db->query($sql);
	
	if (!$bal) {
		header("location: ./bal_com_nombal.php?codigo_error=1");
		die;
	}
	
	function buscarSaldo($num_cia) {
		global $saldo;
		
		if (!$saldo)
			return 0;
		
		for ($i = 0; $i < count($saldo); $i++)
			if ($num_cia == $saldo[$i]['num_cia'])
				return $saldo[$i]['saldo_libros'];
		
		return 0;
	}
	
	function buscarPro($num_cia) {
		global $pro;
		
		if (!$pro)
			return 0;
		
		for ($i = 0; $i < count($pro); $i++)
			if ($num_cia == $pro[$i]['num_cia'])
				return $pro[$i]['pro'];
		
		return 0;
	}
	
	$numfilas_x_hoja = 60;
	$numfilas = $numfilas_x_hoja;
	for ($i = 0; $i < count($bal); $i++) {
		if ($numfilas == $numfilas_x_hoja) {
			$tpl->newBlock("listado");
			$numfilas = 0;
		}
		
		$tpl->newBlock("fila");
		$tpl->assign("num_cia", $bal[$i]['num_cia']);
		$tpl->assign("nombre_cia", $bal[$i]['nombre_corto']);
		
		// Movimientos de estados de cuenta para calcular el saldo a principios de año
		$sql = "SELECT tipo_mov, sum(importe) AS importe FROM estado_cuenta WHERE num_cia = {$bal[$i]['num_cia']} AND fecha BETWEEN '$fecha1' AND CURRENT_DATE";
		$sql .= " GROUP BY tipo_mov";
		$movs = $db->query($sql);
		
		// Movimientos de estados de cuenta para calcular el saldo a principios de mes
		$sql = "SELECT tipo_mov, sum(importe) AS importe FROM estado_cuenta WHERE num_cia = 1 AND fecha BETWEEN cast('$fecha2' as timestamp) + interval '1 day'";
		$sql .= " AND CURRENT_DATE GROUP BY tipo_mov";
		$movs1 = $db->query($sql);
		
		// Obtener saldo a proveedores de principio de año
		$sql = "SELECT sum(total) AS importe FROM facturas_pagadas WHERE num_cia = {$bal[$i]['num_cia']} AND fecha_cheque >= '$fecha1' AND fecha_mov < '$fecha1'";
		$saldo_ini_pro = $db->query($sql);
		
		// Obtener saldo a proveedores de principio de mes
		$sql = "SELECT sum(total) AS importe FROM pasivo_proveedores WHERE num_cia = {bal[$i]['num_cia']} AND fecha_mov <= '$fecha2'";
		$tmp1 = $db->query($sql);
		$sql = "SELECT sum(total) AS importe FROM facturas_pagadas WHERE num_cia = {$bal[$i]['num_cia']} AND fecha_mov <= '$fecha2' AND fecha_cheque > '$fecha2'";
		$tmp2 = $db->query($sql);
		
		
		
		$saldo_lib = buscarSaldo($bal[$i]['num_cia']);
		$saldo_ini = $saldo_lib;
		if ($movs)
			foreach ($movs as $mov)
				$saldo_ini += $mov['tipo_mov'] == 't' ? $mov['importe'] : -$mov['importe'];
		
		$saldo_fin = $saldo_lib;
		if ($movs1)
			foreach ($movs1 as $mov)
				$saldo_fin += $mov['tipo_mov'] == 't' ? $mov['importe'] : -$mov['importe'];
		
		$sql = "SELECT sum(importe) FROM otros_depositos WHERE num_cia = {$bal[$i]['num_cia']} AND fecha BETWEEN '$fecha1' AND '$fecha2'";
		$otros_dep = $db->query($sql);
		
		$sql = "SELECT tipo_mov, sum(importe) FROM gastos_caja WHERE num_cia = {$bal[$i]['num_cia']} AND fecha BETWEEN '$fecha1' AND '$fecha2' GROUP BY tipo_mov";
		$gastos_caja = $db->query($sql);
		
		$general = $otros_dep[0]['sum'];
		if ($gastos_caja)
			foreach ($gastos_caja as $reg)
				$general += $reg['tipo_mov'] == 't' ? $reg['sum'] : -$reg['sum'];
		
		$saldo_pro = buscarPro($bal[$i]['num_cia']);
		$saldo_actual = $saldo_lib - $saldo_pro;
		$diferencia = $general - $bal[$i]['bal'];
		
		$tpl->assign("saldo_ini", $saldo_ini != 0 ? number_format($saldo_ini, 2, ".", ",") : "&nbsp;");
		$tpl->assign("saldo_ini_pro", $saldo_ini_pro[0]['importe'] != 0 ? number_format($saldo_ini_pro[0]['importe'], 2, ".", ",") : "&nbsp;");
		$tpl->assign("dif_ini", $saldo_ini - $saldo_ini_pro[0]['importe'] != 0 ? number_format($saldo_ini - $saldo_ini_pro[0]['importe'], 2, ".", ",") : "&nbsp;");
		$tpl->assign("saldo", $saldo_lib != 0 ? number_format($saldo_lib, 2, ".", ",") : "&nbsp;");
		$tpl->assign("sal_pro", $saldo_pro != 0 ? number_format($saldo_pro, 2, ".", ",") : "&nbsp;");
		$tpl->assign("dif_sal", $saldo_lib - $saldo_pro != 0 ? number_format($saldo_lib - $saldo_pro, 2, ".", ",") : "&nbsp;");
		$tpl->assign("dif_tot", ($saldo_lib - $saldo_pro) - ($saldo_ini - $saldo_ini_pro[0]['importe']) != 0 ? number_format(($saldo_lib - $saldo_pro) - ($saldo_ini - $saldo_ini_pro[0]['importe']), 2, ".", ",") : "&nbsp;");
		$tpl->assign("bal", $bal[$i]['bal'] != 0 ? number_format($bal[$i]['bal'], 2, ".", ",") : "&nbsp;");
		$tpl->assign("tmp", $general != 0 ? number_format($general, 2, ".", ",") : "&nbsp;");
		$tpl->assign("og", $diferencia != 0 ? number_format($diferencia) : "&nbsp;");
		$tpl->assign("dif", $diferencia != 0 ? number_format($diferencia + ($saldo_lib - $saldo_pro) - ($saldo_ini - $saldo_ini_pro[0]['importe']), 2, ".", ",") : "&nbsp;");
		
		$numfilas++;
		
		if ($numfilas >= $numfilas_x_hoja)
			$tpl->assign("listado.salto", "<br style=\page-break-after:always;\">");
	}
	$tpl->printToScreen();
	die;
}

$tpl->newBlock("datos");
$tpl->assign("anio", date("Y"));

// Si viene de una página que genero error
if (isset($_GET['codigo_error'])) {
	$tpl->newBlock("error");
	$tpl->newBlock("message");
	$tpl->assign( "message",$descripcion_error[$_GET['codigo_error']]);	
}

$tpl->printToScreen();
?>