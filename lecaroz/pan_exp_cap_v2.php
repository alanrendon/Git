<?php
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

$session = new sessionclass($dsn);
$db = new DBclass($dsn, "autocommit=yes");

$descripcion_error[1] = "La panadería no tiene expendios";

if (isset($_POST['num_cia'])) {
	$fecha = $_POST['fecha'];
	$num_cia = $_POST['num_cia'];
	$total_abono = str_replace(",", "", $_POST['TotalAbono']);
	
	$sql = "";
	for ($i = 0; $i < count($_POST['NumExpendio']); $i++) {
		$sql .= "INSERT INTO mov_expendios (num_cia, num_expendio, fecha, nombre_expendio, porc_ganancia, pan_p_venta, pan_p_expendio, abono, devolucion, rezago, rezago_anterior) VALUES";
		$sql .= " ($num_cia, {$_POST['NumExpendio'][$i]}, '$fecha', '{$_POST['NombreExpendio'][$i]}', {$_POST['PorGanancia'][$i]},";
		$sql .= " " . get_val($_POST['PanVenta'][$i]) . ",";
		$sql .= " " . get_val($_POST['PanExpendio'][$i]) . ",";
		$sql .= " " . get_val($_POST['Abono'][$i]) . ",";
		$sql .= " " . get_val($_POST['Devolucion'][$i]) . ",";
		$sql .= " " . get_val($_POST['RezagoFinal'][$i]) . ",";
		$sql .= " " . get_val($_POST['RezagoInicial'][$i]) . ");\n";
	}
	if ($id = $db->query("SELECT id FROM total_panaderias WHERE num_cia = $num_cia AND fecha = '$fecha'"))
		$sql .= "UPDATE total_panaderias SET abono = abono + $total_abono, efectivo = efectivo + $total_abono, exp = 'TRUE' WHERE id = {$id[0]['id']};\n";
	else {
		$sql .= "INSERT INTO total_panaderias (num_cia, fecha, venta_puerta, pastillaje, otros, abono, gastos, raya_pagada, venta_pastel, abono_pastel, efectivo, efe, exp, gas, pro, pas)";
		$sql .= " VALUES ($num_cia, '$fecha', 0, 0, 0, $total_abono, 0, 0, 0, 0, $total_abono, 'FALSE', 'TRUE', 'FALSE', 'FALSE', 'FALSE');\n";
	}
	//print_r($_POST);echo $sql;die;
	$db->query($sql);
	
	header("location: ./pan_exp_cap_v2.php");
	die;
}

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/pan/pan_exp_cap_v2.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (!isset($_GET['num_cia'])) {
	$tpl->newBlock("datos");
	
	// Obtener compañías por capturista
	if ($_SESSION['iduser'] != 1 && $_SESSION['iduser'] != 4)
		$sql = "SELECT num_cia, nombre_corto FROM catalogo_companias LEFT JOIN catalogo_operadoras USING (idoperadora) WHERE iduser = $_SESSION[iduser] AND num_cia <= 300 ORDER BY num_cia";
	else
		$sql = "SELECT num_cia, nombre_corto FROM catalogo_companias WHERE num_cia <= 300 OR num_cia IN (702,703) ORDER BY num_cia";
	$cias = $db->query($sql);
	
	foreach ($cias as $cia) {
		$tpl->newBlock("cia");
		$tpl->assign("num_cia", $cia['num_cia']);
		$tpl->assign("nombre_cia", $cia['nombre_corto']);
	}
	
	// Si viene de una página que genero error
	if (isset($_GET['codigo_error'])) {
		$tpl->newBlock("error");
		$tpl->newBlock("message");
		$tpl->assign( "message", $descripcion_error[$_GET['codigo_error']]);	
	}

	$tpl->printToScreen();
	die();
}

// Obtener expendios
$sql = "SELECT num_cia, nombre, porciento_ganancia, num_referencia, num_expendio, importe_fijo, total_fijo, aut_dev,";
$sql .= " (SELECT rezago FROM mov_expendios WHERE num_cia = catalogo_expendios.num_cia AND num_expendio = catalogo_expendios.num_expendio ORDER BY fecha DESC LIMIT 1) AS rezago";
$sql .= " FROM catalogo_expendios WHERE num_cia = $_GET[num_cia] ORDER BY num_referencia";
$result = $db->query($sql);

if (!$result) {
	header("location: ./pan_exp_cap_v2.php?codigo_error=1");
	die;
}

// Obtener la ultima fecha de captura
$ultima_fecha = $db->query("SELECT fecha FROM mov_expendios WHERE num_cia = $_GET[num_cia] ORDER BY fecha DESC LIMIT 1");
if ($ultima_fecha) {
	ereg("([0-9]{1,2})/([0-9]{1,2})/([0-9]{2,4})", $ultima_fecha[0]['fecha'], $temp);
	$fecha = date("d/m/Y", mktime(0, 0, 0, $temp[2], $temp[1] + 1, $temp[3]));
}
else
	$fecha = date("01/d/Y");

$tpl->newBlock("captura");
$nombre_cia = $db->query("SELECT nombre_corto FROM catalogo_companias WHERE num_cia = $_GET[num_cia]");
$tpl->assign("num_cia", $_GET['num_cia']);
$tpl->assign("nombre_cia", $nombre_cia[0]['nombre_corto']);
$tpl->assign("fecha", $fecha);

$TotalRezagoInicial = 0;
$TotalPanVenta = 0;
$TotalDevolucion = 0;
$TotalPanExpendio = 0;
$TotalAbono = 0;
$TotalRezagoFinal = 0;
for ($i = 0; $i < count($result); $i++) {
	$tpl->newBlock("fila");
	$tpl->assign("i", $i);
	$tpl->assign("next", $i < count($result) - 1 ? $i + 1 : 0);
	$tpl->assign("back", $i > 0 ? $i - 1 : count($result) - 1);
	$tpl->assign("NumExpendio", $result[$i]['num_expendio']);
	$tpl->assign("NumRef", $result[$i]['num_referencia']);
	$tpl->assign("Nombre", $result[$i]['nombre']);
	$tpl->assign("PorGanancia", $result[$i]['porciento_ganancia']);
	$tpl->assign("ImporteFijo", $result[$i]['importe_fijo']);
	$tpl->assign("TotalFijo", $result[$i]['total_fijo']);
	$tpl->assign("readonly", $result[$i]['total_fijo'] == "t" ? "readonly" : "");
	$tpl->assign('readonly_dev', $result[$i]['aut_dev'] == 't' ? '' : 'readonly');
	
	$tpl->assign("RezagoInicial", number_format($result[$i]['rezago'], 2, ".", ","));
	$tpl->assign("RezagoFinal", number_format($result[$i]['rezago'], 2, ".", ","));
	$TotalRezagoInicial += $result[$i]['rezago'];
	$TotalRezagoFinal += $result[$i]['rezago'];
}
$tpl->assign("captura.TotalRezagoInicial", number_format($TotalRezagoInicial, 2, ".", ","));
$tpl->assign("captura.TotalPanVenta", number_format($TotalPanVenta, 2, ".", ","));
$tpl->assign("captura.TotalDevolucion", number_format($TotalDevolucion, 2, ".", ","));
$tpl->assign("captura.TotalPanExpendio", number_format($TotalPanExpendio, 2, ".", ","));
$tpl->assign("captura.TotalAbono", number_format($TotalAbono, 2, ".", ","));
$tpl->assign("captura.TotalRezagoFinal", number_format($TotalRezagoFinal, 2, ".", ","));

$tpl->printToScreen();
?>