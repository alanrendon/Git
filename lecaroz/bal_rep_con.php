<?php
// ACTUALIZACION DE INVENTARIOS (VER. 2)
// Menu 'No definido'

// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);

// Conectarse a la base de datos
$db = new DBclass($dsn);

$descripcion_error[1] = "No hay resultados";

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/bal/bal_rep_con.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (!isset($_GET['num_cia'])) {
	$tpl->newBlock("datos");
	$tpl->assign(date("n"), "selected");
	$tpl->assign("anio", date("Y"));

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
	die;
}

if ($_GET['tipo'] == 1 || ($_GET['num_cia'] != "" && $_GET['num_cia'] < 100)) {
	$sql = "SELECT num_cia, nombre_corto, produccion_total, mat_prima_utilizada, utilidad_neta, ingresos, gas_pro, venta_puerta, abono_reparto, mp_pro FROM balances_pan LEFT JOIN historico USING (num_cia, mes ,anio) LEFT JOIN catalogo_companias USING (num_cia) WHERE mes = $_GET[mes] AND anio = $_GET[anio] AND produccion_total > 0";
	$sql .= $_GET['num_cia'] > 0 ? " AND num_cia = $_GET[num_cia]" : " AND num_cia < 100";
	$sql .= " ORDER BY produccion_total DESC";
	$result = $db->query($sql);

	if (!$result) {
		header("location: ./bal_rep_con.php?codigo_error=1");
		die;
	}

	$tpl->newBlock("listado");
	$tpl->assign("mes", mes_escrito($_GET['mes']));
	$tpl->assign("anio", $_GET['anio']);

	$fecha1 = "1/$_GET[mes]/$_GET[anio]";
	$fecha2 = date("d/m/Y", mktime(0, 0, 0, $_GET['mes'] + 1, 0, $_GET['anio']));

	// Datos de gas
	// Gastos de código 90
	$sql = "SELECT num_cia, sum(importe) FROM movimiento_gastos LEFT JOIN catalogo_gastos USING (codgastos) WHERE num_cia < 100 AND codgastos = 90 AND fecha BETWEEN '$fecha1' AND '$fecha2' AND codigo_edo_resultados = 1 GROUP BY num_cia";
	$gas = $db->query($sql);

	// Descuentos de gas
	$sql = "SELECT num_cia, sum(importe) FROM gastos_caja WHERE num_cia < 100 AND cod_gastos = 92 AND fecha BETWEEN '$fecha1' AND '$fecha2' AND tipo_mov = 'TRUE' GROUP BY num_cia";
	$des_gas = $db->query($sql);

	// Gastos de operacion
	$sql = "SELECT num_cia, sum(importe) FROM movimiento_gastos LEFT JOIN catalogo_gastos USING (codgastos) WHERE num_cia < 100 AND fecha BETWEEN '$fecha1' AND '$fecha2' AND codigo_edo_resultados = 1 GROUP BY num_cia ORDER BY num_cia";
	$gastos_op = $db->query($sql);

	// Gastos generales
	$sql = "SELECT num_cia, sum(importe) FROM movimiento_gastos LEFT JOIN catalogo_gastos USING (codgastos) WHERE num_cia < 100 AND fecha BETWEEN '$fecha1' AND '$fecha2' AND codigo_edo_resultados = 2 GROUP BY num_cia ORDER BY num_cia";
	$gastos_gral = $db->query($sql);

	// Sueldo a empleados
	$sql = "SELECT num_cia, sum(importe) FROM movimiento_gastos WHERE num_cia < 100 AND fecha BETWEEN '$fecha1' AND '$fecha2' AND codgastos = 1 GROUP BY num_cia ORDER BY num_cia";
	$sueldo_emp = $db->query($sql);

	function buscar($num_cia, $array) {
		for ($i = 0; $i < count($array); $i++)
			if ($array[$i]['num_cia'] == $num_cia)
				return $array[$i]['sum'];

		return FALSE;
	}

	for ($i = 0; $i < count($result); $i++) {
		$tpl->newBlock("fila");
		$tpl->assign("num_cia", $result[$i]['num_cia']);
		$tpl->assign("nombre_cia", $result[$i]['nombre_corto']);
		$tpl->assign("venta_puerta", number_format($result[$i]['venta_puerta'], 2, ".", ","));
		$tpl->assign("venta_reparto", number_format($result[$i]['abono_reparto'], 2, ".", ","));
		$tpl->assign("produccion", number_format($result[$i]['produccion_total'], 2, ".", ","));
		$tpl->assign("mp_utilizada", ($result[$i]['mp_pro'] != 0 ? '<span style="color:#C00; float:left; font-size:6pt;">(' . number_format($result[$i]['mp_pro'], 3) . ')</span>   ' : '') . number_format($result[$i]['mat_prima_utilizada'], 2, ".", ","));
		$utilidad_neta = $result[$i]['utilidad_neta'] - $result[$i]['ingresos'];
		$tpl->assign("utilidad_neta", "<font color=\"" . ($utilidad_neta > 0 ? "0000FF" : "FF0000") . "\">" . number_format($utilidad_neta, 2, ".", ","));

		$total_gas = buscar($result[$i]['num_cia'], $gas) - buscar($result[$i]['num_cia'], $des_gas);
		$tpl->assign("gas", $total_gas != 0 ? ($result[$i]['gas_pro'] != 0 ? '<span style="color:#C00; float:left; font-size:6pt;">(' . number_format($result[$i]['gas_pro'], 3) . ')</span>   ' : '') . number_format($total_gas, 2, ".", ",") : "&nbsp;");

		$total_op = buscar($result[$i]['num_cia'], $gastos_op);
		$tpl->assign("gastos_op", $total_op != 0 ? number_format($total_op, 2, ".", ",") : "&nbsp;");

		$total_gral = buscar($result[$i]['num_cia'], $gastos_gral);
		$tpl->assign("gastos_gral", $total_gral != 0 ? number_format($total_gral, 2, ".", ",") : "&nbsp;");

		$total_sueldo = buscar($result[$i]['num_cia'], $sueldo_emp);
		$tpl->assign("sueldo_emp", number_format($total_sueldo, 2, ".", ","));
	}
}
else {
	$sql = "SELECT num_cia, nombre_corto, ventas_netas, mat_prima_utilizada, utilidad_neta, ingresos FROM balances_ros LEFT JOIN historico USING (num_cia, mes ,anio) LEFT JOIN catalogo_companias USING (num_cia) WHERE mes = $_GET[mes] AND anio = $_GET[anio] AND ventas_netas > 0";
	$sql .= $_GET['num_cia'] > 0 ? " AND num_cia = $_GET[num_cia]" : " AND (num_cia BETWEEN 100 AND 200 OR num_cia BETWEEN 702 AND 720)";
	$sql .= " ORDER BY ventas_netas DESC";
	$result = $db->query($sql);

	if (!$result) {
		header("location: ./bal_rep_con.php?codigo_error=1");
		die;
	}

	$tpl->newBlock("listado_pollos");
	$tpl->assign("mes", mes_escrito($_GET['mes']));
	$tpl->assign("anio", $_GET['anio']);

	$fecha1 = "1/$_GET[mes]/$_GET[anio]";
	$fecha2 = date("d/m/Y", mktime(0, 0, 0, $_GET['mes'] + 1, 0, $_GET['anio']));

	// Datos de gas
	// Gastos de código 90
	$sql = "SELECT num_cia, sum(importe) FROM movimiento_gastos LEFT JOIN catalogo_gastos USING (codgastos) WHERE (num_cia BETWEEN 100 AND 200 OR num_cia BETWEEN 702 AND 750) AND codgastos = 90 AND fecha BETWEEN '$fecha1' AND '$fecha2' AND codigo_edo_resultados = 1 GROUP BY num_cia";
	$gas = $db->query($sql);

	// Descuentos de gas
	$sql = "SELECT num_cia, sum(importe) FROM gastos_caja WHERE (num_cia BETWEEN 100 AND 200 OR num_cia BETWEEN 702 AND 750) AND cod_gastos = 92 AND fecha BETWEEN '$fecha1' AND '$fecha2' AND tipo_mov = 'TRUE' GROUP BY num_cia";
	$des_gas = $db->query($sql);

	// Gastos de operacion
	$sql = "SELECT num_cia, sum(importe) FROM movimiento_gastos LEFT JOIN catalogo_gastos USING (codgastos) WHERE (num_cia BETWEEN 100 AND 200 OR num_cia BETWEEN 702 AND 750) AND fecha BETWEEN '$fecha1' AND '$fecha2' AND codigo_edo_resultados = 1 GROUP BY num_cia ORDER BY num_cia";
	$gastos_op = $db->query($sql);

	// Gastos generales
	$sql = "SELECT num_cia, sum(importe) FROM movimiento_gastos LEFT JOIN catalogo_gastos USING (codgastos) WHERE (num_cia BETWEEN 100 AND 200 OR num_cia BETWEEN 702 AND 750) AND fecha BETWEEN '$fecha1' AND '$fecha2' AND codigo_edo_resultados = 2 GROUP BY num_cia ORDER BY num_cia";
	$gastos_gral = $db->query($sql);

	// Sueldo a empleados
	$sql = "SELECT num_cia, sum(importe) FROM movimiento_gastos WHERE (num_cia BETWEEN 100 AND 200 OR num_cia BETWEEN 702 AND 750) AND fecha BETWEEN '$fecha1' AND '$fecha2' AND codgastos = 1 GROUP BY num_cia ORDER BY num_cia";
	$sueldo_emp = $db->query($sql);

	function buscar($num_cia, $array) {
		for ($i = 0; $i < count($array); $i++)
			if ($array[$i]['num_cia'] == $num_cia)
				return $array[$i]['sum'];

		return FALSE;
	}

	for ($i = 0; $i < count($result); $i++) {
		$tpl->newBlock("fila_pollos");
		$tpl->assign("num_cia", $result[$i]['num_cia']);
		$tpl->assign("nombre_cia", $result[$i]['nombre_corto']);
		$tpl->assign("ventas", number_format($result[$i]['ventas_netas'], 2, ".", ","));
		$tpl->assign("mp_utilizada", number_format($result[$i]['mat_prima_utilizada'], 2, ".", ","));
		$utilidad_neta = $result[$i]['utilidad_neta'] - $result[$i]['ingresos'];
		$tpl->assign("utilidad_neta", "<font color=\"" . ($utilidad_neta > 0 ? "0000FF" : "FF0000") . "\">" . number_format($utilidad_neta, 2, ".", ","));

		$total_gas = buscar($result[$i]['num_cia'], $gas) - buscar($result[$i]['num_cia'], $des_gas);
		$tpl->assign("gas", $total_gas != 0 ? number_format($total_gas, 2, ".", ",") : "&nbsp;");

		$total_op = buscar($result[$i]['num_cia'], $gastos_op);
		$tpl->assign("gastos_op", $total_op != 0 ? number_format($total_op, 2, ".", ",") : "&nbsp;");

		$total_gral = buscar($result[$i]['num_cia'], $gastos_gral);
		$tpl->assign("gastos_gral", $total_gral != 0 ? number_format($total_gral, 2, ".", ",") : "&nbsp;");

		$total_sueldo = buscar($result[$i]['num_cia'], $sueldo_emp);
		$tpl->assign("sueldo_emp", $total_sueldo != 0 ? number_format($total_sueldo, 2, ".", ",") : "&nbsp;");
	}
}

$tpl->printToScreen();
$db->desconectar();
?>
