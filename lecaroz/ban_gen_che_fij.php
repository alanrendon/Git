<?php
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';
include './includes/class.TemplatePower.inc.php';

$session = new sessionclass($dsn);
$db = new DBclass($dsn, "autocommit=yes");

$descripcion_error[1] = "No hay resultados";
$mensaje[1] = "Se han generado los pagos con exito";

if (isset($_GET['fecha'])) {
	$num_cia = array();
	foreach ($_GET['num_cia'] as $reg)
		if ($reg > 0)
			$num_cia[] = $reg;
	
	$num_pro = array();
	foreach ($_GET['num_pro'] as $reg)
		if ($reg > 0)
			$num_pro[] = $reg;
	
	$no_pro = array();
	foreach ($_GET['no_pro'] as $reg)
		if ($reg > 0)
			$no_pro[] = $reg;
	
	$no_cia = array();
	foreach ($_GET['no_cia'] as $reg)
		if ($reg > 0)
			$no_cia[] = $reg;
	
	$sql = "SELECT num_cia, pre_cheques.num_proveedor AS num_pro, catalogo_proveedores.nombre AS nombre_pro, codgastos, catalogo_gastos.descripcion AS nombre_gas, concepto, importe,";
	$sql .= " iva, ret_iva, isr, total, trans, san FROM pre_cheques LEFT JOIN catalogo_companias USING (num_cia) LEFT JOIN catalogo_proveedores ON";
	$sql .= " (pre_cheques.num_proveedor = catalogo_proveedores.num_proveedor) LEFT JOIN catalogo_gastos USING (codgastos)";
	
	$condiciones = array();
	
	$condiciones[] = 'pre_cheques.num_cia BETWEEN ' . ($_SESSION['tipo_usuario'] == 2 ? '900 AND 998' : '1 AND 899');
	
	if (count($num_cia) > 0) {
		$condiciones[] = 'pre_cheques.num_cia IN (' . implode(', ', $num_cia) . ')';
	}
	
	if (count($num_pro) > 0) {
		$condiciones[] = 'pre_cheques.num_proveedor IN (' . implode(', ', $num_pro) . ')';
	}
	
	if (count($no_pro) > 0) {
		$condiciones[] = 'pre_cheques.num_proveedor NOT IN (' . implode(', ', $no_pro) . ')';
	}
	
	if (count($no_cia) > 0) {
		$condiciones[] = 'pre_cheques.num_cia NOT IN (' . implode(', ', $no_cia) . ')';
	}
	
	if (count($condiciones) > 0) {
		$sql .= ' WHERE ' . implode(' AND ', $condiciones);
	}
	
	$sql .= " ORDER BY num_cia, codgastos";
	$result = $db->query($sql);
	
	if (!$result) {
		header("location: ./ban_gen_che_fij.php?codigo_error=1");
		die;
	}
	
	$sql = "";
	$num_cia = NULL;
	for ($i = 0; $i < count($result); $i++) {
		if ($num_cia != $result[$i]['num_cia']) {
			$num_cia = $result[$i]['num_cia'];
			
			// Obtener ultimo folio
			$tmp = $db->query("SELECT folio FROM folios_cheque WHERE num_cia = $num_cia AND cuenta = $_GET[cuenta] ORDER BY folio DESC LIMIT 1");
			$last_folio = $tmp ? $tmp[0]['folio'] + 1 : 1;
		}
		// Cheque
		$sql .= "INSERT INTO cheques (cod_mov, num_proveedor, num_cia, fecha, folio, importe, iduser, a_nombre, imp, concepto, codgastos, proceso, cuenta, poliza) VALUES (";
		$sql .= ($result[$i]['trans'] == "t" ? 41 : 5) . ", {$result[$i]['num_pro']}, {$result[$i]['num_cia']}, '$_GET[fecha]', $last_folio, {$result[$i]['total']}, $_SESSION[iduser], '{$result[$i]['nombre_pro']}',";
		$sql .= " 'FALSE', '" . (trim($result[$i]['concepto']) == "" ? $result[$i]['nombre_gas'] : trim($result[$i]['concepto'])) . "', {$result[$i]['codgastos']},";
		$sql .= " 'TRUE', $_GET[cuenta], '" . ($result[$i]['trans'] == "t" ? "TRUE" : "FALSE") . "');\n";
		if ($result[$i]['trans'] == "t"/* && $_GET['cuenta'] == 2*/) {
			$sql .= "INSERT INTO transferencias_electronicas (num_cia, num_proveedor, folio, importe, fecha_gen, tipo, status, cuenta) VALUES ({$result[$i]['num_cia']}, {$result[$i]['num_pro']},";
			$sql .= " $last_folio, {$result[$i]['total']}, '$_GET[fecha]', '" . ($result[$i]['san'] == "t" ? "FALSE" : "TRUE") . "', 0, $_GET[cuenta]);\n";
		}
		// Estado de cuenta
		$sql .= "INSERT INTO estado_cuenta (num_cia, fecha, tipo_mov, importe, cod_mov, folio, concepto, cuenta, iduser) VALUES (";
		$sql .= "{$result[$i]['num_cia']}, '$_GET[fecha]', 'TRUE', {$result[$i]['total']}, " . ($result[$i]['trans'] == "t" ? 41 : 5) . ", $last_folio, '";
		$sql .= (trim($result[$i]['concepto']) == "" ? $result[$i]['nombre_gas'] : trim($result[$i]['concepto'])) . "', $_GET[cuenta], $_SESSION[iduser]);\n";
		// Gastos
		$sql .= "INSERT INTO movimiento_gastos (codgastos, num_cia, fecha, importe, captura, folio, concepto) VALUES (";
		$sql .= "{$result[$i]['codgastos']}, {$result[$i]['num_cia']}, '$_GET[fecha]', {$result[$i]['total']}, 'TRUE', $last_folio, '";
		$sql .= (trim($result[$i]['concepto']) == "" ? $result[$i]['nombre_gas'] : trim($result[$i]['concepto'])) . "');\n";
		// Folios
		$sql .= "INSERT INTO folios_cheque (folio, num_cia, reservado, utilizado, fecha, cuenta) VALUES ($last_folio, {$result[$i]['num_cia']}, 'FALSE', 'TRUE', '$_GET[fecha]', $_GET[cuenta]);\n";
		// Actualizar saldos
		$sql .= "UPDATE saldos SET saldo_libros = saldo_libros - {$result[$i]['total']} WHERE num_cia = {$result[$i]['num_cia']} AND cuenta = $_GET[cuenta];\n";
		
		if ($result[$i]['num_cia'] <= 899) {
			// Facturas
			$num_fact = $result[$i]['num_cia'] . date("dm") . rand(0, 9);
			$piva = $result[$i]['iva'] * 100 / $result[$i]['importe'];
			$iva = $result[$i]['iva'] != 0 ? $result[$i]['iva'] : 0;
			$prisr = $result[$i]['isr'] * 100 / $result[$i]['importe'];
			$priva = $result[$i]['ret_iva'] * 100 / $result[$i]['importe'];
			$tipo_fac = stristr($result[$i]['nombre_gas'], "HONORARIO") !== FALSE ? 1 : (stristr($result[$i]['nombre_gas'], "RENTA") ? 2 : 3);
			$concepto = trim($result[$i]['concepto']) == "" ? $result[$i]['nombre_gas'] : trim($result[$i]['concepto']);
			$sql .= "INSERT INTO facturas (num_proveedor, num_cia, num_fact, fecha, importe, piva, iva, pretencion_isr, pretencion_iva, codgastos,";
			$sql .= " total, tipo_factura, fecha_captura, iduser, concepto) VALUES ({$result[$i]['num_pro']}, {$result[$i]['num_cia']}, '$num_fact', '$_GET[fecha]',";
			$sql .= " {$result[$i]['importe']}, $piva, $iva, $prisr, $priva, {$result[$i]['codgastos']}, {$result[$i]['total']}, $tipo_fac, CURRENT_DATE, $_SESSION[iduser],";
			$sql .= " '$concepto');\n";
			// Facturas pagadas
			$sql .= "INSERT INTO facturas_pagadas (num_cia, num_proveedor, num_fact, total, descripcion, fecha, fecha_cheque, folio_cheque, codgastos, proceso, imp, cuenta) VALUES";
			$sql .= " ({$result[$i]['num_cia']}, {$result[$i]['num_pro']}, '$num_fact', {$result[$i]['total']}, '$concepto', '$_GET[fecha]', '$_GET[fecha]', $last_folio,";
			$sql .= " {$result[$i]['codgastos']}, 'TRUE', 'TRUE', $_GET[cuenta]);\n";
		}
		else {
			$num_fact = $result[$i]['num_cia'] . date("dm") . rand(0, 9);
			$iva = $result[$i]['iva'] != 0 ? $result[$i]['iva'] : 0;
			$prisr = $result[$i]['isr'] * 100 / $result[$i]['importe'];
			$priva = $result[$i]['ret_iva'] * 100 / $result[$i]['importe'];
			$risr = $result[$i]['isr'] > 0 ? $result[$i]['isr'] : 0;
			$riva = $result[$i]['ret_iva'] > 0 ? $result[$i]['ret_iva'] : 0;
			$concepto = trim($result[$i]['concepto']) == "" ? $result[$i]['nombre_gas'] : trim($result[$i]['concepto']);
			$sql .= "INSERT INTO facturas_zap (num_cia, num_proveedor, num_fact, fecha, concepto, codgastos, importe, iva, pisr, isr, pivaret, ivaret, total, iduser, por_aut, copia_fac, folio, cuenta, tspago) VALUES";
			$sql .= " ({$result[$i]['num_cia']}, {$result[$i]['num_pro']}, $num_fact, '$_GET[fecha]', '$concepto', {$result[$i]['codgastos']}, {$result[$i]['importe']}, $iva,";
			$sql .= " $prisr, $risr, $priva, $riva, {$result[$i]['total']}, $_SESSION[iduser], TRUE, TRUE, $last_folio, $_GET[cuenta], now());\n";
		}
		
		$last_folio++;
	}
	
	$db->query($sql);
	
	header("location: ./ban_gen_che_fij.php?mensaje=1");
	die;
}

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/ban/ban_gen_che_fij.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

$tpl->assign("fecha", date("d/m/Y"));

// Si viene de una página que genero error
if (isset($_GET['codigo_error'])) {
	$tpl->newBlock("error");
	$tpl->newBlock("message");
	$tpl->assign( "message", $descripcion_error[$_GET['codigo_error']]);	
}

// Si viene de una página que genero error
if (isset($_GET['mensaje'])) {
	$tpl->newBlock("message");
	$tpl->assign( "message", $mensaje[$_GET['mensaje']]);	
}


$tpl->printToScreen();
?>