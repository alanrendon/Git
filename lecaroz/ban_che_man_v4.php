<?php
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

$session = new sessionclass($dsn);
$db = new DBclass($dsn, "autocommit=yes");

$users = array(28, 29, 30, 31);


// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/ban/ban_che_man_v4.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (isset($_POST['cuenta'])) {
	// Almacenar datos temporalmente
	$_SESSION['che_man'] = $_POST;

	// Validar facturas
	foreach ($_POST['num_fact'] as $i => $num_fact)
		if ($num_fact > 0 && $db->query("SELECT num_fact FROM facturas WHERE num_proveedor = $_POST[num_pro] AND num_fact = $num_fact")) {
			$tpl->newBlock("valid");
			$tpl->assign("mensaje", "La factura no. $num_fact para el proveedor $_POST[num_pro] ya existe en la Base de Datos");
			$tpl->assign("campo", "num_fact[$i]");
			$tpl->printToScreen();
			die;
		}

	$tmp = $db->query("SELECT folio FROM folios_cheque WHERE num_cia = $_POST[num_cia] AND cuenta = $_POST[cuenta] ORDER BY folio DESC LIMIT 1");
	if (!$tmp) {
		$tpl->newBlock("valid");
		$tpl->assign("mensaje", "La compañía no tiene folio inicial");
		$tpl->assign("campo", "num_cia");
		$tpl->printToScreen();
		die;
	}
	$folio = $tmp ? $tmp[0]['folio'] + 1 : 51;

	$facturas = "";
	foreach ($_POST['num_fact'] as $i => $fac)
		if ($fac > 0 && str_replace(",", "", $_POST['total'][$i]) > 0)
			$facturas .= "$fac ";
	$facturas = trim($facturas);

	$data['num_cia'] = $_POST['num_cia'];
	$data['cuenta'] = $_POST['cuenta'];
	$data['fecha'] = $_POST['fecha'];
	$data['cod_mov'] = $_POST['pago'] == 2 ? 41 : 5;
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
	$data['poliza'] = $_POST['cuenta'] == 2 && $_POST['pago'] == 2 ? "TRUE" : "FALSE";

	if ($_POST['cuenta'] == 2 && $_POST['pago'] == 2 && $_POST['num_cia'] < 900) {
		$transfer['num_cia']       = $_POST['num_cia'];
		$transfer['num_proveedor'] = $_POST['num_pro'];
		$transfer['folio']         = $folio;
		$transfer['importe']       = str_replace(",", "", $_POST['total_cheque']);
		$transfer['fecha_gen']     = $_POST['fecha'];
		$transfer['tipo']          = $_POST['tipo'];
		$transfer['status']        = "0";
		$transfer['folio_archivo'] = 0;
		$transfer['cuenta']        = 2;
		$transfer['iduser']        = $_SESSION['iduser'];
		$transfer['concepto']      = substr(trim(strtoupper($_POST['concepto'])), 0, 30);
		$transfer['ban_gen']       = 'TRUE';
	}

	$sql = $db->preparar_insert("cheques", $data) . ";\n";
	$sql .= $db->preparar_insert("estado_cuenta", $data) . ";\n";
	$sql .= $db->preparar_insert("folios_cheque", $data) . ";\n";
	$sql .= $db->preparar_insert("movimiento_gastos", $data) . ";\n";
	if (isset($transfer)) $sql .= $db->preparar_insert("transferencias_electronicas", $transfer) . ";\n";
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
			$fact['cuenta'] = $_POST['cuenta'];

			$sql .= $db->preparar_insert("facturas", $fact) . ";\n";
			$sql .= $db->preparar_insert("facturas_pagadas", $fact) . ";\n";
		}

	$db->query($sql);

	unset($_SESSION['che_man']);
	$_SESSION['concepto'] = $data['concepto'];
	$tpl->newBlock("redir");
	$tpl->printToScreen();
	die;
}

$tpl->newBlock("data");

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

// Generar listado de compañías
$cia = $db->query("SELECT num_cia, nombre FROM catalogo_companias WHERE num_cia BETWEEN " . (in_array($_SESSION['iduser'], $users) ? "900 AND 950" : "1 AND 800") . " ORDER BY num_cia ASC");
foreach ($cia as $reg) {
	$tpl->newBlock("cia");
	$tpl->assign("num_cia", $reg['num_cia']);
	$tpl->assign("nombre", $reg['nombre']);
}

function buscar($num_cia, $deps) {
	if (!$deps)
		return 0;

	foreach ($deps as $dep)
		if ($num_cia == $dep['num_cia'])
			return $dep['importe'];

	return 0;
}

// Generar listado de saldos de Banorte
$saldo1 = $db->query("SELECT num_cia, saldo_libros FROM saldos WHERE cuenta = 1 AND num_cia BETWEEN " . (in_array($_SESSION['iduser'], $users) ? "900 AND 950" : "1 AND 800") . " ORDER BY num_cia ASC");
if ($saldo1)
	foreach ($saldo1 as $reg) {
		$tpl->newBlock("saldo1");
		$tpl->assign("num_cia", $reg['num_cia']);
		$tpl->assign("saldo", number_format($reg['saldo_libros'], 2, ".", ","));
		$tpl->assign("saldo_real", "0.00");
	}

// Generar listado de saldos de Santander
$saldo2 = $db->query("SELECT num_cia, saldo_libros FROM saldos WHERE cuenta = 2 AND num_cia BETWEEN " . (in_array($_SESSION['iduser'], $users) ? "900 AND 950" : "1 AND 800") . " ORDER BY num_cia ASC");
$deps2 = $db->query("SELECT num_cia, sum(importe) AS importe FROM estado_cuenta WHERE cuenta = 2 AND num_cia BETWEEN " . (in_array($_SESSION['iduser'], $users) ? "900 AND 950" : "1 AND 800") . " AND fecha_con IS NULL AND tipo_mov = 'FALSE' GROUP BY num_cia ORDER BY num_cia");
if ($saldo2)
	foreach ($saldo2 as $reg) {
		$tpl->newBlock("saldo2");
		$tpl->assign("num_cia", $reg['num_cia']);
		$tpl->assign("saldo", number_format($reg['saldo_libros'], 2, ".", ","));
		$tpl->assign("saldo_real", number_format($reg['saldo_libros'] - buscar($reg['num_cia'], $deps2), 2, ".", ","));
	}

// Generar listado de proveedores
$pro = $db->query("SELECT num_proveedor AS num_pro, nombre, trans, san FROM catalogo_proveedores ORDER BY num_proveedor ASC");
foreach ($pro as $reg) {
	$tpl->newBlock("pro");
	$tpl->assign("num_pro", $reg['num_pro']);
	$tpl->assign("nombre", str_replace(array("\""), array("'"), $reg['nombre']));
	$tpl->assign("trans", $reg['trans'] == "t" ? "true" : "false");
	$tpl->assign("tipo", $reg['san'] == "t" ? "FALSE" : "TRUE");
}

// Generar listado de gastos
$gasto = $db->query("SELECT codgastos, descripcion FROM catalogo_gastos ORDER BY codgastos ASC");
foreach ($gasto as $reg) {
	$tpl->newBlock("gasto");
	$tpl->assign("codgastos", $reg['codgastos']);
	$tpl->assign("descripcion", $reg['descripcion']);
}

// Si viene de una página que genero error
if (isset($_GET['codigo_error'])) {
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
