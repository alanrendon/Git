<?php
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

$session = new sessionclass($dsn);
$db = new DBclass($dsn, "autocommit=yes");

if (isset($_POST['cuenta'])) {
	// Almacenar datos temporalmente
	$_SESSION['che_man'] = $_POST;
	
	// Validar facturas
	foreach ($_POST['num_fact'] as $num_fact)
		if ($num_fact > 0 && $db->query("SELECT num_fact FROM facturas WHERE num_proveedor = $_POST[num_pro] AND num_fact = $num_fact")) {
			header("location: ./ban_che_man_v2.php?codigo_error=1&factura=$_POST[factura1]");
			die;
		}
	
	$tmp = $db->query("SELECT folio FROM folios_cheque WHERE num_cia = $_POST[num_cia] AND cuenta = $_POST[cuenta] ORDER BY folio DESC LIMIT 1");
	$folio = $tmp ? $tmp[0]['folio'] + 1 : 1;
	
	$facturas = "";
	foreach ($_POST['num_fact'] as $i => $fac)
		if ($fac > 0 && str_replace(",", "", $_POST['total'][$i]) > 0)
			$facturas .= "$fac ";
	$facturas = trim($facturas);
	
	$data['num_cia'] = $_POST['num_cia'];
	$data['cuenta'] = $_POST['cuenta'];
	$data['fecha'] = $_POST['fecha'];
	$data['cod_mov'] = 5;
	$data['folio'] = $folio;
	$data['importe'] = str_replace(",", "", $_POST['total_cheque']);
	$data['num_proveedor'] = $_POST['num_pro'];
	$data['a_nombre'] = $_POST['nombre_pro'];
	$data['concepto'] = trim(strtoupper($_POST['concepto']));
	$data['tipo_mov'] = "TRUE";
	$data['iduser'] = $_SESSION['iduser'];
	$data['imp'] = "FALSE";
	$data['facturas'] = $facturas;
	$data['codgastos'] = $_POST['codgastos'];
	$data['proceso'] = "FALSE";
	$data['reservado'] = "FALSE";
	$data['utilizado'] = "TRUE";
	$data['captura'] = "TRUE";
	$data['poliza'] = "FALSE";
	
	$sql = $db->preparar_insert("cheques", $data) . ";\n";
	$sql .= $db->preparar_insert("estado_cuenta", $data) . ";\n";
	$sql .= $db->preparar_insert("folios_cheque", $data) . ";\n";
	$sql .= $db->preparar_insert("movimiento_gastos", $data) . ";\n";
	$sql .= "UPDATE saldos SET saldo_libros = saldo_libros - $data[importe] WHERE num_cia = $data[num_cia] AND cuenta = $data[cuenta];\n";
	
	foreach ($_POST['num_fact'] as $i => $fac)
		if ($fac > 0 && str_replace(",", "", $_POST['total'][$i]) > 0) {
			$fact['num_proveedor'] = $data['num_proveedor'];
			$fact['num_cia'] = $data['num_cia'];
			$fact['num_fact'] = $fac;
			$fact['fecha_mov'] = $data['fecha'];
			$fact['fecha_ven'] = $data['fecha'];
			$fact['fecha_pagon'] = $data['fecha'];
			$fact['fecha_cheque'] = $data['fecha'];
			$fact['folio_cheque'] = $folio;
			$fact['concepto'] = $data['concepto'];
			$fact['imp_sin_iva'] = str_replace(",", "", $_POST['importe'][$i]);
			$fact['porciento_iva'] = isset($_POST['iva' . $i]) ? "15" : "0";
			$fact['importe_iva'] = str_replace(",", "", $_POST['total'][$i]) - str_replace(",", "", $_POST['importe'][$i]);
			$fact['codgastos'] = $data['codgastos'];
			$fact['importe_total'] = str_replace(",", "", $_POST['total'][$i]);
			$fact['total'] = $fact['importe_total'];
			$fact['descripcion'] = $fact['concepto'];
			$fact['tipo_factura'] = 0;
			$fact['fecha_captura'] = date("d/m/Y");
			$fact['iduser'] = $data['iduser'];
			$fact['proceso'] = "FALSE";
			$fact['imp'] = "TRUE";
			
			$sql .= $db->preparar_insert("facturas", $fact) . ";\n";
			$sql .= $db->preparar_insert("facturas_pagadas", $fact) . ";\n";
		}
	
	$db->query($sql);
	
	unset($_SESSION['che_man']);
	$_SESSION['concepto'] = $data['concepto'];
	header("location: ./ban_che_man_v2.php");
	die;
}

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/ban/ban_che_man_v2.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (isset($_SESSION['che_man'])) {
	$tpl->assign("cuenta" . $_SESSION['che_man']['cuenta'], "selected");
	$tpl->assign("num_cia", $_SESSION['che_man']['num_cia']);
	$tpl->assign("nombre_cia", $_SESSION['che_man']['nombre_cia']);
	$tpl->assign("saldo", $_SESSION['che_man']['saldo']);
	$tpl->assign("fecha", $_SESSION['che_man']['fecha']);
	$tpl->assign("num_pro", $_SESSION['che_man']['num_pro']);
	$tpl->assign("nombre_pro", $_SESSION['che_man']['nombre_pro']);
	$tpl->assign("concepto", $_SESSION['che_man']['concepto']);
	$tpl->assign("codgastos", $_SESSION['che_man']['codgastos']);
	$tpl->assign("nombre_gasto", $_SESSION['che_man']['nombre_gasto']);
	$tpl->assign("total_cheque", $_SESSION['che_man']['total_cheque']);
	foreach ($_SESSION['che_man']['num_fact'] as $i => $fac) {
		$tpl->assign("num_fact", $fac);
		$tpl->assign("importe" . $i, $_SESSION['che_man']['importe'][$i]);
		$tpl->assign("iva" . $i, isset($_SESSION['che_man']['iva' . $i]) ? "checked" : "");
		$tpl->assign("total" . $i, $_SESSION['che_man']['total'][$i]);
	}
}
else {
	$tpl->assign("cuenta2", "selected");
	$tpl->assign("fecha", date("d/m/Y"));
	$tpl->assign("total_cheque", "0.00");
	if (isset($_SESSION['concepto']))
		$tpl->assign("concepto", $_SESSION['concepto']);
}
 
// Generar listado de compaρνas
$cia = $db->query("SELECT num_cia, nombre FROM catalogo_companias ORDER BY num_cia ASC");
foreach ($cia as $reg) {
	$tpl->newBlock("cia");
	$tpl->assign("num_cia", $reg['num_cia']);
	$tpl->assign("nombre", $reg['nombre']);
}

// Generar listado de saldos de Banorte
$saldo1 = $db->query("SELECT num_cia, saldo_libros FROM saldos WHERE cuenta = 1 ORDER BY num_cia ASC");
if ($saldo1)
	foreach ($saldo1 as $reg) {
		$tpl->newBlock("saldo1");
		$tpl->assign("num_cia", $reg['num_cia']);
		$tpl->assign("saldo", number_format($reg['saldo_libros'], 2, ".", ","));
	}

// Generar listado de saldos de Santander
$saldo2 = $db->query("SELECT num_cia, saldo_libros FROM saldos WHERE cuenta = 2 ORDER BY num_cia ASC");
if ($saldo2)
	foreach ($saldo2 as $reg) {
		$tpl->newBlock("saldo2");
		$tpl->assign("num_cia", $reg['num_cia']);
		$tpl->assign("saldo", number_format($reg['saldo_libros'], 2, ".", ","));
	}

// Generar listado de proveedores
$pro = $db->query("SELECT num_proveedor AS num_pro, nombre FROM catalogo_proveedores ORDER BY num_proveedor ASC");
foreach ($pro as $reg) {
	$tpl->newBlock("pro");
	$tpl->assign("num_pro", $reg['num_pro']);
	$tpl->assign("nombre", str_replace(array("\""), array("'"), $reg['nombre']));
}

// Generar listado de gastos
$gasto = $db->query("SELECT codgastos, descripcion FROM catalogo_gastos ORDER BY codgastos ASC");
foreach ($gasto as $reg) {
	$tpl->newBlock("gasto");
	$tpl->assign("codgastos", $reg['codgastos']);
	$tpl->assign("descripcion", $reg['descripcion']);
}

// Si viene de una pαgina que genero error
if (isset($_GET['codigo_error'])) {
	if (isset($_GET['factura']))
		$descripcion_error[1] = "La factura no. $_GET[factura] para el proveedor $_SESSION[num_proveedor] ya existe en la Base de Datos";
	$descripcion_error[2] = "La compaρνa no tiene folio inicial para cheques";
	$descripcion_error[3] = "La compaρνa no tiene saldo inicial";
	$descripcion_error[4] = "La compaρνa no tiene saldo para pagar el cheque";
	
	$tpl->newBlock("error");
	$tpl->newBlock("message");
	$tpl->assign("message",$descripcion_error[$_GET['codigo_error']]);	
}

if (isset($_GET['mensaje'])) {
	$tpl->newBlock("message");
	$tpl->assign("message", $_GET['mensaje']);
}

$tpl->printToScreen();
die;

?>