<?php
// LISTADO DE CONSUMOS ACUMULADOS EN VALORES DEL MES VERSION 2
// Tabla 'mov_inv_real ó mov_inv_virtual'
// Menu 'Panaderías->Producción'

// --------------------------------- INCLUDES ----------------------------------------------------------------
//include './includes/class.db3.inc.php';
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);

// --------------------------------- Descripcion de errores --------------------------------------------------
$descripcion_error[1] = "No hay resultados";

// --------------------------------- Generar pantalla --------------------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/pan/pan_con_acu_v2.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

// -------------------------------- Buscar datos de compañía -------------------------------------------------
if (!isset($_GET['fecha'])) {
	$tpl->newBlock("datos");
	$tpl->assign("fecha",date("d/m/Y",mktime(0,0,0,date("m"),0,date("Y"))));
	
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
}

// -------------------------------- Mostrar listado ---------------------------------------------------------
// Conectarse a la base de datos
$db = new DBclass($dsn);

// Variables
ereg("([0-9]{1,2})/([0-9]{1,2})/([0-9]{2,4})", $_GET['fecha'], $fecha);
$fecha1 = "1/$fecha[2]/$fecha[3]";
$fecha2 = $_GET['fecha'];
$fecha_historico = date("d/m/Y", mktime(0, 0, 0, $fecha[2], 0, $fecha[3]));

// Saldos anteriores
$sql = "SELECT num_cia, codmp, nombre, existencia, precio_unidad, controlada, tipo FROM historico_inventario LEFT JOIN catalogo_mat_primas USING (codmp) LEFT JOIN control_avio USING (num_cia, codmp) WHERE";
// Si es para una compañía en específico
$sql .= $_GET['num_cia'] > 0 ? " num_cia = $_GET[num_cia] AND" : " num_cia < 100 AND";
//$sql .= " controlada = 'TRUE' AND fecha = '$fecha_historico' GROUP BY num_cia, codmp, nombre, existencia, precio_unidad, num_orden ORDER BY num_cia, num_orden";
$sql .= " fecha = '$fecha_historico' AND codmp NOT IN (90) GROUP BY num_cia, codmp, nombre, existencia, precio_unidad, controlada, tipo, num_orden ORDER BY num_cia, num_orden";
$saldo_ant = $db->query($sql);

// Saldo actual
$sql = "SELECT num_cia, fecha, codmp, nombre, descripcion, cod_turno, tipo_mov, cantidad, precio_unidad, total_mov FROM mov_inv_real JOIN catalogo_mat_primas USING (codmp) WHERE";
// Si es para una compañía en específico
$sql .= $_GET['num_cia'] > 0 ? " num_cia = $_GET[num_cia] AND" : " num_cia < 100 AND";
//$sql .= " controlada = 'TRUE' AND fecha BETWEEN '$fecha1' AND '$fecha2' ORDER BY num_cia, controlada DESC, tipo, codmp, fecha, tipo_mov";
$sql .= " fecha BETWEEN '$fecha1' AND '$fecha2' AND codmp NOT IN (90) ORDER BY num_cia, controlada DESC, tipo, codmp, fecha, tipo_mov";
$saldo = $db->query($sql);

$nummp = count($saldo_ant);
$nummov = count($saldo);

$num_cia = NULL;
$total_valores_anteriores = 0;
$total_valores_entrada = 0;
$total_valores_salida = 0;
$total_valores = 0;
for ($j = 0; $j < $nummp; $j++) {
	if ($num_cia != $saldo_ant[$j]['num_cia']) {
		// Ultimas filas de hoja de consumo
		if ($num_cia != NULL) {
			$tpl->gotoBlock("listado");
			
			// Consumos de materia prima controlada (con faltantes y sobrantes)
			foreach ($consumo as $key => $value) {
				$tpl->assign($key . "_consumo", $value != 0 ? number_format($value, 2, ".", ",") : "&nbsp;");
			}
			$tpl->assign("total_consumo", number_format($consumo_mes, 2, ".", ","));
			
			// Materia prima no controlada
			$total_mp_nc = 0;
			foreach ($no_controlado as $key => $value) {
				$tpl->assign($key . "_no_control", $value != 0 ? number_format($value, 2, ".", ",") : "&nbsp;");
				$total_mp_nc += $value;
				
				// Sumar a consumos
				$consumo[$key] += $value;
				$consumo_mes += $value;
			}
			$tpl->assign("total_no_control", $total_mp_nc != 0 ? number_format($total_mp_nc, 2, ".", ",") : "&nbsp;");
			
			// Mercancias
			$sql = "SELECT sum(importe) FROM movimiento_gastos WHERE num_cia = $num_cia AND fecha BETWEEN '$fecha1' AND '$fecha2' AND codgastos IN (23, 9, 76)";
			$mer = $db->query($sql);
			// Gastos de caja de codigo 28 (ABARROTES)
			$abarrotes_julild_salida = $db->query("SELECT sum(importe) FROM gastos_caja WHERE num_cia = $num_cia AND fecha BETWEEN '$fecha1' AND '$fecha2' AND cod_gastos = 28 AND tipo_mov = 'FALSE'");
			$abarrotes_julild_entrada = $db->query("SELECT sum(importe) FROM gastos_caja WHERE num_cia = $num_cia AND fecha BETWEEN '$fecha1' AND '$fecha2' AND cod_gastos = 28 AND tipo_mov = 'TRUE'");
			$abarrotes_julild = $abarrotes_julild_salida[0]['sum'] - $abarrotes_julild_entrada[0]['sum'];
			// Mercancias (cálculo)
			$mercancias = $mer[0]['sum'] + $abarrotes_julild;
			$consumo_mes += $mercancias;
			$tpl->assign("mercancias", $mercancias != 0 ? number_format($mercancias, 2, ".", ",") : "&nbsp;");
			// Desglozar mercancias
			foreach ($pro_tur as $key => $value)
				if ($key != 1 && $key != 2 && $key != 10) {
					@$porcentaje = ($pro_tur[$key] * 100) / ($total_pro - $pro_tur[0] - $pro_tur[1]);
					@$mer_tur[$key] = $mercancias * $porcentaje / 100;
					$tpl->assign($key . "_mercancias", $mer_tur[$key] != 0 ? number_format($mer_tur[$key], 2, ".", ",") : "&nbsp;");
					
					$consumo[$key] += $mer_tur[$key];
				}
			
			// Consumo Total
			foreach ($consumo as $key => $value)
				$tpl->assign($key . "_consumo_total", $value != 0 ? number_format($consumo[$key], 2, ".", ",") : "&nbsp;");
			$tpl->assign("consumo_total", $consumo_mes != 0 ? number_format($consumo_mes, 2, ".", ",") : "&nbsp;");
			
			// Producción y Consumo / Producción
			foreach ($pro_tur as $key => $value) {
				$tpl->assign($key . "_produccion", $value != 0 ? number_format($value, 2, ".", ",") : "&nbsp;");
				$tpl->assign($key . "_con_pro", $value != 0 && $consumo[$key] != 0 ? number_format($consumo[$key] / $value, 3, ".", ",") : "&nbsp;");
			}
			$tpl->assign("total_produccion", $total_pro != 0 ? number_format($total_pro, 2, ".", ",") : "&nbsp;");
			$tpl->assign("con_pro", $total_pro != 0 && $consumo_mes != 0 ? number_format($consumo_mes / $total_pro, 3, ".", ",") : "&nbsp;");
		}
		
		// Nueva compañía
		$num_cia = $saldo_ant[$j]['num_cia'];
		
		$tpl->newBlock("listado");
		$tpl->assign("num_cia", $num_cia);
		$nombre_cia = $db->query("SELECT nombre, nombre_corto FROM catalogo_companias WHERE num_cia = $num_cia");
		$tpl->assign("nombre_cia", $nombre_cia[0]['nombre']);
		$tpl->assign("nombre_corto", $nombre_cia[0]['nombre_corto']);
		$tpl->assign("dia", $fecha[1]);
		$tpl->assign("mes", mes_escrito($fecha[2]));
		$tpl->assign("anio", $fecha[3]);
		
		// MP / Producción (Balance)
		$sql = "SELECT mp_pro FROM balances_pan WHERE num_cia = $num_cia AND mes = $fecha[2] AND anio = $fecha[3]";
		$mp_pro = $db->query($sql);
		$tpl->assign("mp_pro", $mp_pro ? number_format($mp_pro[0]['mp_pro'], 3, ".", ",") : "&nbsp;");
		
		$consumo       = array(1 => 0, 2 => 0, 3 => 0, 4 => 0, 8 => 0, 9 => 0, 10 => 0);	// Consumo por turno
		$consumo_mes   = 0;																	// Consumo total del mes
		$faltante      = array(1 => 0, 2 => 0, 3 => 0, 4 => 0, 8 => 0, 9 => 0, 10 => 0);	// Faltantes por turno
		$sobrante      = array(1 => 0, 2 => 0, 3 => 0, 4 => 0, 8 => 0, 9 => 0, 10 => 0);	// Sobrantes por turno
		$no_controlado = array(1 => 0, 2 => 0, 3 => 0, 4 => 0, 8 => 0, 9 => 0, 10 => 0);	// No controlados por turno
		$pro_tur       = array(1 => 0, 2 => 0, 3 => 0, 4 => 0, 8 => 0, 9 => 0, 10 => 0);	// Producción por turno
		$total_pro     = 0;																	// Producción total
		$mer_tur       = array(1 => 0, 2 => 0, 3 => 0, 4 => 0, 8 => 0, 9 => 0, 10 => 0);	// Mercancias por turno
		$mercancias    = 0;																	// Total de mercancias
		
		// Produccion
		$sql = "SELECT cod_turnos, sum(imp_produccion) FROM produccion WHERE num_cia = $num_cia AND fecha BETWEEN '$fecha1' AND '$fecha2' GROUP BY cod_turnos ORDER BY cod_turnos";
		$produccion = $db->query($sql);
		
		// Obtener que turnos tuvieron produccion
		for ($i = 0; $i < count($produccion); $i++)
			if ($produccion[$i]['sum'] > 0) {
				$pro_tur[$produccion[$i]['cod_turnos']] = $produccion[$i]['sum'];
				$total_pro += $produccion[$i]['sum'];
			}
	}
	
	$codmp = $saldo_ant[$j]['codmp'];
	$nombremp = $saldo_ant[$j]['nombre'];
	
	$unidades = $saldo_ant[$j]['existencia'];
	$valores  = $saldo_ant[$j]['existencia'] * $saldo_ant[$j]['precio_unidad'];
	
	$costo_promedio   = $saldo_ant[$j]['precio_unidad'];
	
	$consumo_turno = array(1 => 0, 2 => 0, 3 => 0, 4 => 0, 8 => 0, 9 => 0, 10 => 0);	// Consumo por turno del producto
	$consumo_total_mp = 0;		// Consumo total de la materia prima
	$consumo_parcial_mp = 0;	// Consumo parcial de la materia prima
	$consumo_nc = 0;			// Consumo total de la materia prima no controlada
	$dif_fal = 0;
	$dif_sob = 0;
	
	for ($k = 0; $k < $nummov; $k++) {
		if ($saldo[$k]['num_cia'] == $saldo_ant[$j]['num_cia'] && $saldo[$k]['codmp'] == $saldo_ant[$j]['codmp']) {
			// Salidas
			if ($saldo[$k]['tipo_mov'] == "t") {
				$unidades -= $saldo[$k]['cantidad'];
				$valores -= $saldo[$k]['cantidad'] * $costo_promedio;
				
				// Si no es diferencia y es materia prima controlada
				if ($saldo_ant[$j]['controlada'] == "TRUE") {
					if ($saldo[$k]['cod_turno'] != "") {
						$consumo_turno[$saldo[$k]['cod_turno']] += $saldo[$k]['cantidad'] * $costo_promedio;
						$consumo[$saldo[$k]['cod_turno']] += $saldo[$k]['cantidad'] * $costo_promedio;
						$consumo_parcial_mp += $saldo[$k]['cantidad'] * $costo_promedio;
					}
					// Si es diferencia
					else
						$dif_fal += $saldo[$k]['cantidad'] * $costo_promedio;
					$consumo_total_mp += $saldo[$k]['cantidad'] * $costo_promedio;
				}
				// Si es materia prima no controlada
				else if ($saldo_ant[$j]['controlada'] == "FALSE")
					$consumo_nc += $saldo[$k]['cantidad'] * $costo_promedio;
			}
			// Entradas
			else if ($saldo[$k]['tipo_mov'] == "f") {
				@$precio_unidad = $saldo[$k]['total_mov'] / $saldo[$k]['cantidad'];
				$unidades += $saldo[$k]['cantidad'];
				$valores += $saldo[$k]['cantidad'] * $precio_unidad;
				if ($unidades != 0)
					$costo_promedio = $valores / $unidades;
			}
		}
	}
	// Si el producto tuvo movimientos en el mes, mostrar en pantalla
	if (round($consumo_total_mp, 2) > 0 && $saldo_ant[$j]['controlada'] == "TRUE") {
		$tpl->newBlock("fila");
		$tpl->assign("codmp", $codmp);
		$tpl->assign("nombre", $nombremp);
		$tpl->assign("precio_unidad", number_format($costo_promedio, 2, ".", ","));
		
		// Sumar consumo de la materia prima al consumo del mes
		$consumo_mes += $consumo_total_mp;
		
		// Si hay faltantes, calcular el promedio para el turno y sumarlo a consumo
		if ($dif_fal > 0) {
			// Si hubo consumo en los turnos
			if (array_sum($consumo_turno) > 0)
				foreach ($consumo_turno as $key => $value) {
					$promedio = $value * 100 / $consumo_parcial_mp;
					$consumo_turno[$key] += $dif_fal * $promedio / 100;
					$consumo[$key] += $dif_fal * $promedio / 100;
				}
			// En caso contrario sumar diferencia al repostero
			else {
				if ($pro_tur[4] > 0) {
					$consumo_turno[4] += $dif_fal;
					$consumo[4] += $dif_fal;
				}
				else {
					$consumo_turno[3] += $dif_fal;
					$consumo[3] += $dif_fal;
				}
			}
		}
		
		// Mostrar consumos por turno del producto en curso
		foreach ($consumo_turno as $key => $value)
			$tpl->assign($key, $value != 0 ? number_format($value, 2, ".",",") : "&nbsp;");
		$tpl->assign("consumo", number_format($consumo_total_mp, 2, ".", ","));
	}
	// Si es materia prima no controlada
	else if (round($consumo_nc, 2) > 0 && $saldo_ant[$j]['controlada'] == "FALSE") {
		// Materia prima no controlada
		if ($saldo_ant[$j]['tipo'] == 1) {
			$no_controlado[1] += 0.025 * $consumo_nc;
			$no_controlado[2] += 0.025 * $consumo_nc;
			$no_controlado[3] += $pro_tur[4] > 0 ? 0.15 * $consumo_nc : 0.95 * $consumo_nc;
			$no_controlado[4] += $pro_tur[4] > 0 ? 0.80 * $consumo_nc : 0;
		}
		// Material de empaque
		else {
			$no_controlado[3] += $pro_tur[4] > 0 ? $consumo_nc * 0.20 : $consumo_nc * 0.90;
			$no_controlado[4] += $pro_tur[4] > 0 ? $consumo_nc * 0.70 : 0;
			$no_controlado[10] += $consumo_nc * 0.10;
		}
	}
}
// Ultimas filas de hoja de consumo (para la ultima compañía)
if ($num_cia != NULL) {
	$tpl->gotoBlock("listado");
	
	// Consumos de materia prima controlada (con faltantes y sobrantes)
	foreach ($consumo as $key => $value) {
		$tpl->assign($key . "_consumo", $value != 0 ? number_format($value, 2, ".", ",") : "&nbsp;");
	}
	$tpl->assign("total_consumo", number_format($consumo_mes, 2, ".", ","));
	
	// Materia prima no controlada
	$total_mp_nc = 0;
	foreach ($no_controlado as $key => $value) {
		$tpl->assign($key . "_no_control", $value != 0 ? number_format($value, 2, ".", ",") : "&nbsp;");
		$total_mp_nc += $value;
		
		// Sumar a consumos
		$consumo[$key] += $value;
		$consumo_mes += $value;
	}
	$tpl->assign("total_no_control", $total_mp_nc != 0 ? number_format($total_mp_nc, 2, ".", ",") : "&nbsp;");
	
	// Mercancias
	$sql = "SELECT sum(importe) FROM movimiento_gastos WHERE num_cia = $num_cia AND fecha BETWEEN '$fecha1' AND '$fecha2' AND codgastos IN (23, 9, 76)";
	$mer = $db->query($sql);
	// Gastos de caja de codigo 28 (ABARROTES)
	$abarrotes_julild_salida = $db->query("SELECT sum(importe) FROM gastos_caja WHERE num_cia = $num_cia AND fecha BETWEEN '$fecha1' AND '$fecha2' AND cod_gastos = 28 AND tipo_mov = 'FALSE'");
	$abarrotes_julild_entrada = $db->query("SELECT sum(importe) FROM gastos_caja WHERE num_cia = $num_cia AND fecha BETWEEN '$fecha1' AND '$fecha2' AND cod_gastos = 28 AND tipo_mov = 'TRUE'");
	$abarrotes_julild = $abarrotes_julild_salida[0]['sum'] - $abarrotes_julild_entrada[0]['sum'];
	// Mercancias (cálculo)
	$mercancias = $mer[0]['sum'] + $abarrotes_julild;
	$consumo_mes += $mercancias;
	$tpl->assign("mercancias", $mercancias != 0 ? number_format($mercancias, 2, ".", ",") : "&nbsp;");
	// Desglozar mercancias
	foreach ($pro_tur as $key => $value)
		if ($key != 1 && $key != 2 && $key != 10) {
			@$porcentaje = ($pro_tur[$key] * 100) / ($total_pro - $pro_tur[1] - $pro_tur[2]);
			@$mer_tur[$key] = $mercancias * $porcentaje / 100;
			$tpl->assign($key . "_mercancias", $mer_tur[$key] != 0 ? number_format($mer_tur[$key], 2, ".", ",") : "&nbsp;");
			
			$consumo[$key] += $mer_tur[$key];
		}
	
	// Consumo Total
	foreach ($consumo as $key => $value)
		$tpl->assign($key . "_consumo_total", $value != 0 ? number_format($consumo[$key], 2, ".", ",") : "&nbsp;");
	$tpl->assign("consumo_total", $consumo_mes != 0 ? number_format($consumo_mes, 2, ".", ",") : "&nbsp;");
	
	// Producción y Consumo / Producción
	foreach ($pro_tur as $key => $value) {
		$tpl->assign($key . "_produccion", $value != 0 ? number_format($value, 2, ".", ",") : "&nbsp;");
		$tpl->assign($key . "_con_pro", $value != 0 && $consumo[$key] != 0 ? number_format($consumo[$key] / $value, 3, ".", ",") : "&nbsp;");
	}
	$tpl->assign("total_produccion", $total_pro != 0 ? number_format($total_pro, 2, ".", ",") : "&nbsp;");
	$tpl->assign("con_pro", $total_pro != 0 && $consumo_mes != 0 ? number_format($consumo_mes / $total_pro, 3, ".", ",") : "&nbsp;");
}

$tpl->printToScreen();
$db->desconectar();
?>