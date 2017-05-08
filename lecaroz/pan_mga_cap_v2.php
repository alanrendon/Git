<?php
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';
include './includes/class.TemplatePower.inc.php';

$session = new sessionclass($dsn);
$db = new DBclass($dsn, "autocommit=yes");

//if ($_SESSION['iduser'] != 1) die("la estoy modificando");

$descripcion_error[1] = "";
$numfilas = 25;


// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body", "./plantillas/pan/pan_mga_cap_v2.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (isset($_POST['num_cia'])) {
	$sql = "";
	$num_cia = $_POST['num_cia'];
	$fecha = $_POST['fecha'];
	
	// Validar fecha
	$tmp = $db->query("SELECT mes, anio FROM balances_pan WHERE num_cia = $num_cia ORDER BY anio DESC, mes DESC LIMIT 1");
	$ts_bal = $tmp ? mktime(0, 0, 0, $tmp[0]['mes'] + 1, 0, $tmp[0]['anio']) : mktime(0, 0, 0, date("n"), 0, date("Y"));
	ereg("([0-9]{1,2})/([0-9]{1,2})/([0-9]{2,4})", $fecha, $tmp);
	$ts_cap = mktime(0, 0, 0, $tmp[2], $tmp[1], $tmp[3]);
	$ts_now = mktime(0, 0, 0, date("n"), date("d") - 1, date("Y"));
	
	if ($ts_cap <= $ts_bal && !in_array($_SESSION['iduser'], array(1, 4, 19))) {
		$tpl->newBlock("valid");
		$tpl->assign("mensaje", "No se pueden capturar gastos del mes pasado porque ya se generaron balances");
		$tpl->assign("campo", "fecha");
		$tpl->printToScreen();
		die;
	}
	else if ($ts_cap > $ts_now && !in_array($_SESSION['iduser'], array(1, 4, 19))) {
		$tpl->newBlock("valid");
		$tpl->assign("mensaje", "No se pueden capturar gastos de dias posteriores al de ayer");
		$tpl->assign("campo", "fecha");
		$tpl->printToScreen();
		die;
	}
	
	for ($i = 0; $i < $numfilas; $i++) {
		$codgastos = get_val($_POST['codgastos'][$i]);
		$importe = get_val($_POST['importe'][$i]);
		if ($codgastos > 0 && $importe > 0) {
			$concepto = trim(strtoupper($_POST['concepto'][$i]));
			$cod_turno = isset($_POST['cod_turno'][$i]) && $_POST['cod_turno'][$i] > 0 ? $_POST['cod_turno'][$i] : "NULL";
			$sql .= "INSERT INTO movimiento_gastos (codgastos, num_cia, fecha, importe, captura, concepto, cod_turno) VALUES ($codgastos, $num_cia, '$fecha', $importe,";
			$sql .= " 'FALSE', '$concepto', $cod_turno);\n";
			
		}
	}
	$total = get_val($_POST['total']);
	if ($id = $db->query("SELECT id FROM total_panaderias WHERE num_cia = $num_cia AND fecha = '$fecha'"))
		$sql .= "UPDATE total_panaderias SET gastos = gastos + $total, efectivo = efectivo - $total WHERE id = {$id[0]['id']};\n";
	else {
		$sql .= "INSERT INTO total_panaderias (num_cia, fecha, venta_puerta, pastillaje, otros, abono, gastos, raya_pagada, venta_pastel, abono_pastel, efectivo,";
		$sql .= " efe, exp, gas, pro, pas) VALUES ($num_cia, '$fecha', 0, 0, 0, 0, $total, 0, 0, 0, -$total, 'FALSE', 'FALSE', 'TRUE', 'FALSE', 'FALSE');\n";
	}
	
	$db->query($sql);
	
	$tpl->newBlock("redir");
	$tpl->printToScreen();
	die;
}

$turnos = $db->query("SELECT cod_turno AS cod, descripcion AS nombre FROM catalogo_turnos WHERE cod_turno IN (1, 2, 3, 4, 8, 9) ORDER BY cod");

$tpl->newBlock("captura");

// Filas de captura
for ($i = 0; $i < $numfilas; $i++) {
	$tpl->newBlock("fila");
	$tpl->assign("i", $i);
	$tpl->assign("back", $i > 0 ? $i - 1 : $numfilas - 1);
	$tpl->assign("next", $i < $numfilas - 1 ? $i + 1 : 0);
	
	foreach ($turnos as $turno) {
		$tpl->newBlock("turno");
		$tpl->assign("cod", $turno['cod']);
		$tpl->assign("nombre", $turno['nombre']);
	}
}

// Catálogo de Compañías
$sql = "SELECT num_cia, nombre_corto FROM catalogo_companias";
$sql .= !in_array($_SESSION['iduser'], array(1, 4, 18, 19)) ? " LEFT JOIN catalogo_operadoras USING (idoperadora) WHERE iduser = $_SESSION[iduser]" : "";
$sql .= " ORDER BY num_cia";
$cias = $db->query($sql);
foreach ($cias as $cia) {
	$tpl->newBlock("cia");
	foreach ($cia as $tag => $value)
		$tpl->assign($tag, $value);
}

// Catálogo de Gastos
$gastos = $db->query("SELECT codgastos, descripcion AS desc FROM catalogo_gastos ORDER BY codgastos");
foreach ($gastos as $gasto) {
	$tpl->newBlock("gasto");
	foreach ($gasto as $tag => $value)
		$tpl->assign($tag, $value);
}

// Límites
$sql = "SELECT num_cia, codgastos, limite FROM catalogo_limite_gasto";
$sql .= !in_array($_SESSION['iduser'], array(1, 4, 18, 19)) ? " LEFT JOIN catalogo_companias USING (num_cia) LEFT JOIN catalogo_operadoras USING (idoperadora) WHERE iduser = $_SESSION[iduser]" : "";
$sql .= " ORDER BY num_cia, codgastos";
$limites = $db->query($sql);
$num_cia = NULL;
foreach ($limites as $limite) {
	if ($num_cia != $limite['num_cia']) {
		$num_cia = $limite['num_cia'];
		
		$tpl->newBlock("limites_cia");
		$tpl->assign("num_cia", $num_cia);
	}
	$tpl->newBlock("limite");
	foreach ($limite as $tag => $value)
		$tpl->assign($tag, $value);
}

// Si viene de una página que genero error
if (isset($_GET['codigo_error'])) {
	$tpl->newBlock("error");
	$tpl->newBlock("message");
	$tpl->assign( "message", $descripcion_error[$_GET['codigo_error']]);	
}

$tpl->printToScreen();
?>