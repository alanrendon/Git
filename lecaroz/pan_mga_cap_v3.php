<?php
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';
include './includes/class.TemplatePower.inc.php';

$session = new sessionclass($dsn);
$db = new DBclass($dsn, "autocommit=yes");

//if (!in_array($_SESSION['iduser'], array(1))) die("la estoy modificando");

$descripcion_error[1] = "";
$numfilas = 25;

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body", "./plantillas/pan/pan_mga_cap_v3.tpl");
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
	$ts_bal = $tmp ? mktime(0, 0, 0, $tmp[0]['mes'] + 1, 0, $tmp[0]['anio']) : mktime(0, 0, 0, date("n") - 1, 0, date("Y"));
	ereg("([0-9]{1,2})/([0-9]{1,2})/([0-9]{2,4})", $fecha, $tmp);
	$ts_cap = mktime(0, 0, 0, $tmp[2], $tmp[1], $tmp[3]);
	$ts_now = mktime(0, 0, 0, date("n"), date("d") - 1, date("Y"));

	$fecha1 = "01/$tmp[2]/$tmp[3]";
	$fecha2 = $fecha;

	if ($ts_cap <= $ts_bal && $_SESSION['iduser'] != 1) {
		$tpl->newBlock("valid");
		$tpl->assign("mensaje", "No se pueden capturar gastos del mes pasado porque ya se generaron balances");
		$tpl->assign("campo", "fecha");
		$tpl->printToScreen();
		die;
	}
	else if ($ts_cap > $ts_now) {
		$tpl->newBlock("valid");
		$tpl->assign("mensaje", "No se pueden capturar gastos de dias posteriores al de ayer");
		$tpl->assign("campo", "fecha");
		$tpl->printToScreen();
		die;
	}

	// *** Validar limites ***
	// Obtener limites para la panadería
	$tmp = $db->query("SELECT num_cia, codgastos, limite FROM catalogo_limite_gasto WHERE num_cia = $num_cia");
	if ($tmp) {
		$lim = array();
		foreach ($tmp as $reg)
			$lim[$reg['codgastos']] = $reg['limite'];

		for ($i = 0; $i < $numfilas; $i++)
			if (get_val($_POST['codgastos'][$i]) > 0 && get_val($_POST['importe'][$i]) > 0 && isset($lim[get_val($_POST['codgastos'][$i])])) {
				// Obtener importe capturado del mes
				$tmp = $db->query("SELECT sum(importe) FROM movimiento_gastos WHERE num_cia = $num_cia AND codgastos = {$_POST['codgastos'][$i]} AND fecha BETWEEN '$fecha1' AND '$fecha2' AND captura = 'FALSE'");
				$imp = $tmp[0]['sum'] > 0 ? $tmp[0]['sum'] + get_val($_POST['importe'][$i]) : get_val($_POST['importe'][$i]);

				if ($imp > $lim[get_val($_POST['codgastos'][$i])]) {
					$tpl->newBlock("valid");
					$tpl->assign("mensaje", "El importe total del mes para '{$_POST['desc'][$i]}' no puede ser mayor al limite " . number_format($lim[get_val($_POST['codgastos'][$i])], 2, ".", ","));
					$tpl->assign("campo", "fecha");
					$tpl->printToScreen();
					die;
				}
			}
	}

	$turnos = array('fd' => 1, 'fn' => 2, 'bd' => 3, 'rep' => 4, 'pic' => 8, 'gel' => 9);

	for ($i = 0; $i < $numfilas; $i++) {
		$codgastos = get_val($_POST['codgastos'][$i]);
		$importe = get_val($_POST['importe'][$i]);
		if ($codgastos > 0 && $importe > 0) {
			$concepto = trim(strtoupper($_POST['concepto'][$i]));
			if (in_array($codgastos, array(23, 9, 76)))
				foreach ($turnos as $turno => $cod) {
					$imp_turno = get_val($_POST[$turno][$i]);
					if ($imp_turno > 0) {
						$sql .= "INSERT INTO movimiento_gastos (codgastos, num_cia, fecha, importe, captura, concepto, cod_turno) VALUES ($codgastos, $num_cia, '$fecha', $imp_turno,";
						$sql .= " 'FALSE', '$concepto', $cod);\n";
					}
				}
			else
				$sql .= "INSERT INTO movimiento_gastos (codgastos, num_cia, fecha, importe, captura, concepto) VALUES ($codgastos, $num_cia, '$fecha', $importe, 'FALSE', '$concepto');\n";
		}
	}
	$total = get_val($_POST['total']);

	$tmp = $db->query("SELECT tipo_cia FROM catalogo_companias WHERE num_cia = {$num_cia}");

	$tipo_cia = $tmp[0]['tipo_cia'];

	if ($tipo_cia == 1)
	{
		if ($id = $db->query("SELECT id FROM total_panaderias WHERE num_cia = $num_cia AND fecha = '$fecha'"))
			$sql .= "UPDATE total_panaderias SET gastos = gastos + $total, efectivo = efectivo - $total WHERE id = {$id[0]['id']};\n";
		else {
			$sql .= "INSERT INTO total_panaderias (num_cia, fecha, venta_puerta, pastillaje, otros, abono, gastos, raya_pagada, venta_pastel, abono_pastel, efectivo,";
			$sql .= " efe, exp, gas, pro, pas) VALUES ($num_cia, '$fecha', 0, 0, 0, 0, $total, 0, 0, 0, -$total, 'FALSE', 'FALSE', 'TRUE', 'FALSE', 'FALSE');\n";
		}
	}
	else if ($tipo_cia == 2)
	{
		if ($id = $db->query("SELECT idtotal_rosticeria AS id FROM total_companias WHERE num_cia = $num_cia AND fecha = '$fecha'"))
			$sql .= "UPDATE total_companias SET gastos = gastos + $total, efectivo = efectivo - $total WHERE idtotal_rosticeria = {$id[0]['id']};\n";
		else {
			$sql .= "INSERT INTO total_companias (num_cia, fecha, venta, gastos, efectivo) VALUES ($num_cia, '$fecha', 0, $total, -$total);\n";
		}
	}
	else if ($tipo_cia == 4)
	{

	}

	$db->query($sql);

	$tpl->newBlock("redir");
	$tpl->printToScreen();
	die;
}

$tpl->newBlock("captura");

// Filas de captura
for ($i = 0; $i < $numfilas; $i++) {
	$tpl->newBlock("fila");
	$tpl->assign("i", $i);
	$tpl->assign("back", $i > 0 ? $i - 1 : $numfilas - 1);
	$tpl->assign("next", $i < $numfilas - 1 ? $i + 1 : 0);
}

// Catálogo de Compañías
$sql = "SELECT num_cia, nombre_corto FROM catalogo_companias";
$sql .= !in_array($_SESSION['iduser'], array(1, 4, 18, 19, 27)) ? " LEFT JOIN catalogo_operadoras USING (idoperadora) WHERE iduser = $_SESSION[iduser]" : "";
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
/*$sql = "SELECT num_cia, codgastos, limite FROM catalogo_limite_gasto";
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
}*/

// Si viene de una página que genero error
if (isset($_GET['codigo_error'])) {
	$tpl->newBlock("error");
	$tpl->newBlock("message");
	$tpl->assign( "message", $descripcion_error[$_GET['codigo_error']]);
}

$tpl->printToScreen();
?>
