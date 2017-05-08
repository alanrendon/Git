<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';
include './includes/cheques.inc.php';

// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);
$db = new DBclass($dsn, "autocommit=yes");

function buscar_dep($array, $fecha) {
	if (!$array)
		return FALSE;
	
	for ($i = 0; $i < count($array); $i++)
		if ($array[$i]['fecha'] == $fecha)
			return $i;
	
	return FALSE;
}

function buscar_fac($array, $fecha) {
	if (!$array)
		return 0;
	
	foreach ($array as $i => $reg)
		if ($reg['fecha'] == $fecha)
			return $reg['importe'];
	
	return 0;
}
 
// --------------------------------- Descripcion de errores --------------------------------------------------
$descripcion_error[1] = "No hay facturas";
$descripcion_error[2] = "No hay registros";

// --------------------------------- Generar pantalla --------------------------------------------------------
// Procesar datos
$num_cia = $_GET['num_cia'];
$mes = $_GET['mes'];
$anio = $_GET['anio'];
$dia1 = $_GET['dia1'] > 0 && $_GET['dia1'] < 32 ? $_GET['dia1'] : 1;
$dia2 = $_GET['dia2'] >= $_GET['dia1'] && $_GET['dia2'] < 32 ? $_GET['dia2'] : 1;

// Fechas de inicio y fin
$fecha1 = date("d/m/Y", mktime(0, 0, 0, $mes, $dia1, $anio));
$fecha2 = date("d/m/Y", mktime(0, 0, 0, $mes, $dia2, $anio));

// Seleccionar plantilla
$plantilla = $_GET['tamano'] == "carta" ? "factura_carta.tpl" : "factura_oficio.tpl";

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower("./plantillas/ban/$plantilla");
$tpl->prepare();

// Buscar diferencias del mes
$dif = 0;	// Diferencia que arrastrara durante el proceso de impresión
if ($dia1 > 1) {
	$_fecha1 = "01/$mes/$anio";
	$_fecha2 = date("d/m/Y", mktime(0, 0, 0, $mes, $dia1 - 1, $anio));
	
	// Depositos del mes
	$sql = "SELECT fecha, sum(importe) AS importe FROM estado_cuenta WHERE num_cia = $num_cia AND fecha BETWEEN '$_fecha1' AND '$_fecha2' AND cod_mov IN (1, 16,44) GROUP BY fecha ORDER BY fecha";
	$result = $db->query($sql);
	
	// Depositos alternativos del mes
	$sql = "SELECT fecha, dep1, dep2 FROM depositos_alternativos WHERE num_cia = $num_cia AND fecha BETWEEN '$_fecha1' AND '$_fecha2' GROUP BY fecha, dep1, dep2 ORDER BY fecha";
	$alt = $db->query($sql);
	
	// Obtener todas las facturas de los clientes
	$sql = "SELECT fecha, sum(importe_total) AS importe FROM facturas_clientes WHERE num_cia = $num_cia AND fecha BETWEEN '$_fecha1' AND '$_fecha2' GROUP BY fecha ORDER BY fecha";
	$fac_cli = $db->query($sql);
	
	// Obtener facturas impresas para esos dias
	$sql = "SELECT fecha, sum(importe) AS importe FROM facturas_diarias WHERE num_cia = $num_cia AND fecha BETWEEN '$_fecha1' AND '$_fecha2' GROUP BY fecha ORDER BY fecha";
	$fac_dia = $db->query($sql);
	
	// Conjuntar depositos normales y alternativos
	$count = $result ? count($result) : 0;
	$numfilas = $alt ? count($alt) : 0;
	// Tomar todos los depositos
	if (!isset($_GET['alt']))
		for ($i = 0; $i < $numfilas; $i++)
			// Si ya existe un deposito con esa fecha, sumar el alternativo
			if ($index = buscar_dep($result, $alt[$i]['fecha']))
				$result[$index]['importe'] += $alt[$i]['dep1'] + $alt[$i]['dep2'];
			// Si no existe, agregarlo a los depositos
			else {
				$result[$count]['fecha'] = $alt[$i]['fecha'];
				$result[$count]['importe'] = $alt[$i]['dep1'] + $alt[$i]['dep2'];
				$count++;
			}
	// Tomar solo depositos alternativos
	else {
		$count = 0;
		$result = array();
		for ($i = 0; $i < $numfilas; $i++) {
			$result[$count]['fecha'] = $alt[$i]['fecha'];
			$result[$count]['importe'] = $alt[$i]['dep1'] + $alt[$i]['dep2'];
			$count++;
		}
	}
	
	if ($result)
		foreach ($result as $reg)
			$dif = $reg['importe'] + $dif - buscar_fac($fac_cli, $reg['fecha']) - buscar_fac($fac_dia, $reg['fecha']);
}

// Obtener todas las facturas de los clientes
$sql = "SELECT fecha, sum(importe_total) AS importe FROM facturas_clientes WHERE num_cia = $num_cia AND fecha BETWEEN '$fecha1' AND '$fecha2' GROUP BY fecha ORDER BY fecha";
$fac_cli = $db->query($sql);

// Depositos del mes
$sql = "SELECT fecha, sum(importe) AS importe FROM estado_cuenta WHERE num_cia = $num_cia AND fecha BETWEEN '$fecha1' AND '$fecha2' AND cod_mov IN (1, 16) GROUP BY fecha ORDER BY fecha";
$result = $db->query($sql);

// Depositos alternativos del mes
$sql = "SELECT fecha, dep1, dep2 FROM depositos_alternativos WHERE num_cia = $num_cia AND fecha BETWEEN '$fecha1' AND '$fecha2' GROUP BY fecha, dep1, dep2 ORDER BY fecha";
$alt = $db->query($sql);

// Obtener porcentajes por factura
$sql = "SELECT * FROM catalogo_porcentajes_facturas WHERE num_cia = $num_cia ORDER BY porcentaje DESC";
$porc = $db->query($sql);

// Conjuntar depositos normales y alternativos
$count = $result ? count($result) : 0;
$numfilas = $alt ? count($alt) : 0;
// Tomar todos los depositos
if (!isset($_GET['alt']))
	for ($i = 0; $i < $numfilas; $i++)
		// Si ya existe un deposito con esa fecha, sumar el alternativo
		if ($index = buscar_dep($result, $alt[$i]['fecha']))
			$result[$index]['importe'] += $alt[$i]['dep1'] + $alt[$i]['dep2'];
		// Si no existe, agregarlo a los depositos
		else {
			$result[$count]['fecha'] = $alt[$i]['fecha'];
			$result[$count]['importe'] = $alt[$i]['dep1'] + $alt[$i]['dep2'];
			$count++;
		}
// Tomar solo depositos alternativos
else {
	$count = 0;
	$result = array();
	for ($i = 0; $i < $numfilas; $i++) {
		$result[$count]['fecha'] = $alt[$i]['fecha'];
		$result[$count]['importe'] = $alt[$i]['dep1'] + $alt[$i]['dep2'];
		$count++;
	}
}

if (count($result) < 1) {
	$tpl->newBlock("cerrar");
	$tpl->assign("No hay facturas por imprimir");
	$tpl->printToScreen();
	die;
}

// Aplica IVA?
$sql = "SELECT aplica_iva FROM catalogo_companias WHERE num_cia = $num_cia";
$temp = $db->query($sql);
$iva = $temp && $temp[0]['aplica_iva'] == "t" ? 0.15 : 0.00;

$sql = "";
foreach ($result as $reg) {
	if (round($reg['importe'] + $dif - buscar_fac($fac_cli, $reg['fecha']), 2) >= 0) {
		$total = round($reg['importe'] + $dif - buscar_fac($fac_cli, $reg['fecha']), 2);
		$dif = 0;
	}
	else {
		$dif = round($reg['importe'] + $dif - buscar_fac($fac_cli, $reg['fecha']), 2);
		continue;
	}
	
	$subtotal = round($total / (1 + $iva), 2);
	
	if ($total == 0)
		continue;
	
	$tpl->newBlock("factura");
	ereg("([0-9]{1,2})/([0-9]{1,2})/([0-9]{2,4})", $reg['fecha'], $fecha);
	
	$tpl->assign("dia1", $fecha[1]);
	$tpl->assign("dia2", $fecha[1]);
	$tpl->assign("mes", mes_escrito($fecha[2]));
	$tpl->assign("anio", $fecha[3]);
	
	$tpl->assign("nombre", "PUBLICO EN GENERAL");
	$tpl->assign("direccion", "Conocida");
	$tpl->assign("rfc", "&nbsp;");
	
	// Desglozar por porcentajes
	if (!$porc) {
		$tpl->assign("cantidad1", "1");
		$tpl->assign("descripcion1", "Venta del día: " . $reg['fecha']);
		$tpl->assign("pu1", "&nbsp;");
		$tpl->assign("importe1", number_format($total, 2, ".", ","));
	}
	else {
		foreach ($porc as $j => $porc) {
		//for ($j = 0; $j < count($porc); $j++) {
			$tpl->assign("cantidad" . ($j + 1), "1");
			$tpl->assign("descripcion" . ($j + 1), "Venta del día: " . $reg['fecha']);
			$tpl->assign("pu" . ($j + 1), "&nbsp;");
			$tpl->assign("importe" . ($j + 1) , number_format(round($subtotal * $porc['porcentaje'] / 100, 2), 2, ".", ","));
		}
	}
	
	$tpl->assign("total_escrito", num2string($total));
	$tpl->assign("subtotal", number_format($subtotal, 2, ".", ","));
	$tpl->assign("iva", $iva == 0.15 ? number_format($total - $subtotal, 2, ".", ",") : "&nbsp;");
	$tpl->assign("total",number_format($total, 2, ".", ","));
	
	$sql .= "INSERT INTO facturas_diarias (num_cia, fecha, importe) VALUES ($_GET[num_cia], '{$reg['fecha']}', $total);\n";
}
$db->query($sql);

$tpl->printToScreen();
?>