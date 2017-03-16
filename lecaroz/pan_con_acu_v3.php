<?php
// LISTADO DE CONSUMOS ACUMULADOS EN VALORES DEL MES VERSION 2
// Tabla 'mov_inv_real � mov_inv_virtual'
// Menu 'Panader�as->Producci�n'

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

function buscar_fac($mov, $dif, $costo_dif, $index, $num_cia, $codmp, $unidades) {
	$cantidad = 0;
	$total = 0;
	
	$existencia = $unidades;
	
	// Buscar en las facturas
	for ($i = $index + 1; $i < count($mov); $i++)
		if ($mov[$i]['tipo_mov'] == "f" && $mov[$i]['num_cia'] == $num_cia && $mov[$i]['codmp'] == $codmp) {
			$cantidad += $mov[$i]['cantidad'];
			$total += $mov[$i]['total_mov'];
			
			$existencia += $mov[$i]['cantidad'];
			
			if (/*$cantidad >= abs($unidades)*/$existencia >= 0)
				return $total / $cantidad;
		}
		else if ($mov[$i]['tipo_mov'] == "t" && $mov[$i]['num_cia'] == $num_cia && $mov[$i]['codmp'] == $codmp)
			$existencia -= $mov[$i]['cantidad'];
	// Buscar en las diferencias
	if ($dif)
		for ($i = 0; $i < count($dif); $i++)	
			if ($dif[$i]['tipo_mov'] == "f" && $dif[$i]['num_cia'] == $num_cia && $dif[$i]['codmp'] == $codmp) {
				$cantidad += $dif[$i]['cantidad'];
				$total += $dif[$i]['cantidad'] * $costo_dif;
				
				$existencia += $dif[$i]['cantidad'];
				
				if (/*$cantidad >= abs($unidades)*/$existencia >= 0)
					return $total / $cantidad;
			}
			else if ($dif[$i]['tipo_mov'] == "t" && $dif[$i]['num_cia'] == $num_cia && $dif[$i]['codmp'] == $codmp)
				$existencia -= $dif[$i]['cantidad'];
	
	return FALSE;
}

function buscar_dif($mov, $num_cia, $codmp) {
	for ($i = 0; $i < count($mov); $i++)
		if ($mov[$i]['num_cia'] == $num_cia && $mov[$i]['codmp'] == $codmp)
			return $i;
	
	return FALSE;
}

function costo_dif($mov, $num_cia, $codmp, $costo_promedio) {
	if (!$mov)
		return $costo_promedio;
	
	$cantidad = 0;
	$valor = 0;
	for ($i = 0; $i < count($mov); $i++)
		if ($mov[$i]['num_cia'] == $num_cia && $mov[$i]['codmp'] == $codmp && $mov[$i]['tipo_mov'] == "f") {
			$cantidad += $mov[$i]['cantidad'];
			$valor += $mov[$i]['total_mov'];
		}
	
	return $cantidad > 0 ? $valor / $cantidad : $costo_promedio;
}

// --------------------------------- Generar pantalla --------------------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/pan/pan_con_acu_v3.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

// -------------------------------- Buscar datos de compa��a -------------------------------------------------
if (!isset($_GET['fecha'])) {
	$tpl->newBlock("datos");
	$tpl->assign("fecha",date("d/m/Y",mktime(0,0,0,date("m"),0,date("Y"))));
	
	// Si viene de una p�gina que genero error
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
$today = mktime(0, 0, 0, $fecha[2], $fecha[1], $fecha[3]);
$limit = mktime(0, 0, 0, 1, 1, 2007);

// [11-Feb-2009] Si la fecha es menor al 1 de marzo de 2009 usar el viejo catalogo de materias primas, si no usar el normal
if (mktime(0, 0, 0, $fecha[2], $fecha[1], $fecha[3]) < mktime(0, 0, 0, 3, 1, 2009))
	$catalogo_mat_primas = 'catalogo_mat_primas_old';
else
	$catalogo_mat_primas = 'catalogo_mat_primas';

// Saldos anteriores
$sql = "SELECT num_cia, codmp, nombre, existencia, precio_unidad, controlada, tipo FROM historico_inventario LEFT JOIN $catalogo_mat_primas USING (codmp) LEFT JOIN control_avio USING (num_cia, codmp) WHERE";
// Si es para una compa��a en espec�fico
$sql .= $_GET['num_cia'] > 0 ? " num_cia = $_GET[num_cia] AND" : " num_cia <= 300 AND";
$sql .= " fecha = '$fecha_historico' AND codmp NOT IN (90) GROUP BY num_cia, codmp, nombre, existencia, precio_unidad, controlada, tipo, num_orden ORDER BY num_cia, num_orden";
$saldo_ant = $db->query($sql);

// Saldo actual
$sql = "SELECT num_cia, fecha, codmp, nombre, descripcion, cod_turno, tipo_mov, cantidad, precio_unidad, total_mov FROM mov_inv_real JOIN $catalogo_mat_primas USING (codmp) WHERE";
// Si es para una compa��a en espec�fico
$sql .= $_GET['num_cia'] > 0 ? " num_cia = $_GET[num_cia] AND" : " num_cia <= 300 AND";
$sql .= " fecha BETWEEN '$fecha1' AND '$fecha2' AND descripcion != 'DIFERENCIA INVENTARIO' AND codmp NOT IN (90) ORDER BY num_cia, controlada DESC, tipo, codmp, fecha, tipo_mov, cantidad DESC";
$saldo = $db->query($sql);

// Diferencias del mes
$sql = "SELECT num_cia, fecha, codmp, nombre, descripcion, cod_turno, tipo_mov, cantidad, precio_unidad, total_mov FROM mov_inv_real JOIN $catalogo_mat_primas USING (codmp) WHERE";
// Si es para una compa��a en espec�fico
$sql .= $_GET['num_cia'] > 0 ? " num_cia = $_GET[num_cia] AND" : " num_cia <= 300 AND";
$sql .= " fecha BETWEEN '$fecha1' AND '$fecha2' AND descripcion = 'DIFERENCIA INVENTARIO' AND codmp NOT IN (90) ORDER BY num_cia, controlada DESC, tipo, codmp, fecha, tipo_mov";
$difInv = $db->query($sql);

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
			if ($today < $limit) {
				$sql = "SELECT sum(importe) FROM movimiento_gastos WHERE num_cia = $num_cia AND fecha BETWEEN '$fecha1' AND '$fecha2' AND codgastos IN (23, 9, 76)";
				$mer = $db->query($sql);
				// Gastos de caja de codigo 28 (ABARROTES)
				$abarrotes_julild_salida = $db->query("SELECT sum(importe) FROM gastos_caja WHERE num_cia = $num_cia AND fecha BETWEEN '$fecha1' AND '$fecha2' AND cod_gastos = 28 AND tipo_mov = 'FALSE'");
				$abarrotes_julild_entrada = $db->query("SELECT sum(importe) FROM gastos_caja WHERE num_cia = $num_cia AND fecha BETWEEN '$fecha1' AND '$fecha2' AND cod_gastos = 28 AND tipo_mov = 'TRUE'");
				$abarrotes_julild = $abarrotes_julild_salida[0]['sum'] - $abarrotes_julild_entrada[0]['sum'];
				// Mercancias (c�lculo)
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
			}
			else {
				$sql = "SELECT cod_turno, sum(importe) FROM movimiento_gastos WHERE num_cia = $num_cia AND fecha BETWEEN '$fecha1' AND '$fecha2' AND codgastos IN (23, 9, 76)";
				$sql .= " GROUP BY cod_turno";
				$tmp = $db->query($sql);
				
				$mercancias = 0;
				foreach ($tmp as $reg)
					$mercancias += $reg['sum'];
				
				$consumo_mes += $mercancias;
				$tpl->assign("mercancias", $mercancias != 0 ? number_format($mercancias, 2, ".", ",") : "&nbsp;");
				// Desglozar mercancias
				foreach ($tmp as $reg)
					if ($reg['cod_turno'] > 0) {
						$mer_tur[$reg['cod_turno']] = $reg['sum'];
						$consumo[$reg['cod_turno']] += $mer_tur[$reg['cod_turno']];
						$tpl->assign($reg['cod_turno'] . "_mercancias", $mer_tur[$reg['cod_turno']] != 0 ? number_format($mer_tur[$reg['cod_turno']], 2, ".", ",") : "&nbsp;");
					}
					else {
						// [6-Feb-2007] 60% de las mercancias no codificadas al Bizcochero
						$mer_tur[3] += round($reg['sum'] * 0.60, 2);
						$consumo[3] += round($reg['sum'] * 0.20, 2);
						$tpl->assign(3 . "_mercancias", $mer_tur[3] != 0 ? number_format($mer_tur[3], 2, ".", ",") : "&nbsp;");
						// [6-Feb-2007] 20% de las mercancias no codificadas al Repostero
						$mer_tur[4] += round($reg['sum'] * 0.20, 2);
						$consumo[4] += round($reg['sum'] * 0.20, 2);
						$tpl->assign(4 . "_mercancias", $mer_tur[4] != 0 ? number_format($mer_tur[4], 2, ".", ",") : "&nbsp;");
						// [6-Feb-2007] 20% de las mercancias no codificadas al Gelatinero
						$mer_tur[9] += round($reg['sum'] * 0.20, 2);
						$consumo[9] += round($reg['sum'] * 0.20, 2);
						$tpl->assign(9 . "_mercancias", $mer_tur[9] != 0 ? number_format($mer_tur[9], 2, ".", ",") : "&nbsp;");
					}
			}
			
			// Consumo Total
			foreach ($consumo as $key => $value)
				$tpl->assign($key . "_consumo_total", $value != 0 ? number_format($consumo[$key], 2, ".", ",") : "&nbsp;");
			$tpl->assign("consumo_total", $consumo_mes != 0 ? number_format($consumo_mes, 2, ".", ",") : "&nbsp;");
			
			// Producci�n y Consumo / Producci�n
			foreach ($pro_tur as $key => $value) {
				$tpl->assign($key . "_produccion", $value != 0 ? number_format($value, 2, ".", ",") : "&nbsp;");
				$tpl->assign($key . "_con_pro", $value != 0 && $consumo[$key] != 0 ? number_format($consumo[$key] / $value, 3, ".", ",") : "&nbsp;");
			}
			$tpl->assign("total_produccion", $total_pro != 0 ? number_format($total_pro, 2, ".", ",") : "&nbsp;");
			$tpl->assign("con_pro", $total_pro != 0 && $consumo_mes != 0 ? number_format($consumo_mes / $total_pro, 3, ".", ",") : "&nbsp;");
		}
		
		// Nueva compa��a
		$num_cia = $saldo_ant[$j]['num_cia'];
		
		$tpl->newBlock("listado");
		$tpl->assign("num_cia", $num_cia);
		$nombre_cia = $db->query("SELECT nombre, nombre_corto FROM catalogo_companias WHERE num_cia = $num_cia");
		$tpl->assign("nombre_cia", $nombre_cia[0]['nombre']);
		$tpl->assign("nombre_corto", $nombre_cia[0]['nombre_corto']);
		$tpl->assign("dia", $fecha[1]);
		$tpl->assign("mes", mes_escrito($fecha[2]));
		$tpl->assign("anio", $fecha[3]);
		
		// MP / Producci�n (Balance)
		$sql = "SELECT mp_pro FROM balances_pan WHERE num_cia = $num_cia AND mes = $fecha[2] AND anio = $fecha[3]";
		$mp_pro = $db->query($sql);
		$tpl->assign("mp_pro", $mp_pro ? number_format($mp_pro[0]['mp_pro'], 3, ".", ",") : "&nbsp;");
		
		$consumo       = array(1 => 0, 2 => 0, 3 => 0, 4 => 0, 8 => 0, 9 => 0, 10 => 0);	// Consumo por turno
		$consumo_mes   = 0;																	// Consumo total del mes
		$faltante      = array(1 => 0, 2 => 0, 3 => 0, 4 => 0, 8 => 0, 9 => 0, 10 => 0);	// Faltantes por turno
		$sobrante      = array(1 => 0, 2 => 0, 3 => 0, 4 => 0, 8 => 0, 9 => 0, 10 => 0);	// Sobrantes por turno
		$no_controlado = array(1 => 0, 2 => 0, 3 => 0, 4 => 0, 8 => 0, 9 => 0, 10 => 0);	// No controlados por turno
		$pro_tur       = array(1 => 0, 2 => 0, 3 => 0, 4 => 0, 8 => 0, 9 => 0, 10 => 0);	// Producci�n por turno
		$total_pro     = 0;																	// Producci�n total
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
	
	$unidades_ant = $unidades;
	$costo_ant = $costo_promedio;
	
	$consumo_turno = array(1 => 0, 2 => 0, 3 => 0, 4 => 0, 8 => 0, 9 => 0, 10 => 0);	// Consumo por turno del producto
	$consumo_total_mp = 0;		// Consumo total de la materia prima
	$consumo_parcial_mp = 0;	// Consumo parcial de la materia prima
	$consumo_nc = 0;			// Consumo total de la materia prima no controlada
	$dif_fal = 0;
	$dif_sob = 0;
	
	$arrastre = FALSE;	// Bandera. Indica si debe arrastrarse el costo promedio
	
	// Calcular el costo de la diferencia apartir de las entradas
	$costo_dif = costo_dif($saldo, $num_cia, $codmp, $costo_promedio);
	
	for ($k = 0; $k < $nummov; $k++) {
		if ($saldo[$k]['num_cia'] == $saldo_ant[$j]['num_cia'] && $saldo[$k]['codmp'] == $saldo_ant[$j]['codmp']) {
			// Salidas
			if ($saldo[$k]['tipo_mov'] == "t") {
				$unidades -= $saldo[$k]['cantidad'];
				
				// Si la existencia actual negativa, calcular costo promedio
				if ($unidades < 0) {
					// Buscar costo de las siguientes facturas que satisfagan la existencia negativa actual si no hay arrastre del costo
					if ($arrastre == TRUE)
						$proximo_costo = $costo_promedio;
					else
						$proximo_costo = buscar_fac($saldo, $difInv, $costo_dif, $k, $num_cia, $codmp, $unidades);
					
					// Dividir valores de salida
					$val_1 = ($saldo[$k]['cantidad'] + $unidades) * $costo_promedio;
					$val_2 = abs($unidades) * $proximo_costo;
					$val_sal = $val_1 + $val_2;
					
					$costo_promedio = $proximo_costo;
					$costo_ant = $costo_promedio;
				}
				// Calcular arrastre normalmente
				else {
					$val_sal = $saldo[$k]['cantidad'] * $costo_promedio;
					$costo_ant = $costo_promedio;
				}
				
				$unidades_ant = $unidades;
				
				// Si no es diferencia y es materia prima controlada
				if ($saldo_ant[$j]['controlada'] == "TRUE") {
					if ($saldo[$k]['cod_turno'] != "") {
						$consumo_turno[$saldo[$k]['cod_turno']] += $val_sal;
						$consumo[$saldo[$k]['cod_turno']] += $val_sal;
						$consumo_parcial_mp += $val_sal;
					}
					else
						$dif_fal += $val_sal;
					$consumo_total_mp += $val_sal;
				}
				// Si es materia prima no controlada
				else if ($saldo_ant[$j]['controlada'] == "FALSE")
					$consumo_nc += $val_sal;
			}
			// Entradas
			else if ($saldo[$k]['tipo_mov'] == "f") {
				@$precio_unidad = $saldo[$k]['total_mov'] / $saldo[$k]['cantidad'];	// Costo unitario de la entrada
				
				$unidades += $saldo[$k]['cantidad'];
				
				$costo_ant = $costo_promedio;
				
				// Si la existencia anterior y actual son negativas, no calcular costo promedio y poner bandera de arrastre en TRUE
				if ($unidades_ant < 0 && $unidades < 0)
					$arrastre = TRUE;
				// Si la existencia anterior es negativa y la actual es positiva, no calcular costo promedio y poner bandera de arrastre en FALSE
				else if ($unidades_ant < 0 && $unidades >= 0)
					$arrastre = FALSE;
				// Calcular costo promedio normalmente
				else
					@$costo_promedio = ($unidades_ant * $costo_ant + $saldo[$k]['cantidad'] * $precio_unidad) / ($unidades_ant + $saldo[$k]['cantidad']);
					
				$unidades_ant = $unidades;
			}
		}
	}
	// Buscar diferencia
	$idDif = buscar_dif($difInv, $num_cia, $codmp);
	if ($idDif !== FALSE) {
		// Diferencia en contra
		if ($difInv[$idDif]['tipo_mov'] == "t") {
			$unidades -= $difInv[$idDif]['cantidad'];
			
			$val_sal = $difInv[$idDif]['cantidad'] * $costo_promedio;
			
			$costo = $costo_ant;
			$costo_ant = $costo_promedio;
			
			$unidades_ant = $unidades;
			
			if ($saldo_ant[$j]['controlada'] == "TRUE")
				$dif_fal += $val_sal;
			else
				$consumo_nc += $val_sal;
			$consumo_total_mp += $val_sal;
		}
		// Diferencia a favor
		else if ($difInv[$idDif]['tipo_mov'] == "f") {
			@$precio_unidad = $costo_dif;
			
			$unidades += $difInv[$idDif]['cantidad'];
			
			// Si la existencia anterior y actual son negativas, no calcular costo promedio y poner bandera de arrastre en TRUE
			if ($unidades_ant < 0 && $unidades < 0)
				$arrastre = TRUE;
			// Si la existencia anterior es negativa y la actual es positiva, no calcular costo promedio y poner bandera de arrastre en FALSE
			else if ($unidades_ant < 0 && $unidades >= 0)
				$arrastre = FALSE;
			// Si la existencia anterior es positiva, el precio de la diferencia sera el costo promedio
			else if ($unidades_ant > 0)
				$precio_unidad = $costo_promedio;
			// Calcular costo promedio normalmente
			else
				$costo_promedio = ($unidades_ant * $costo_ant + $difInv[$idDif]['cantidad'] * $precio_unidad) / ($unidades_ant + $difInv[$idDif]['cantidad']);
		}
	}
	
	// Si el producto tuvo movimientos en el mes, mostrar en pantalla
	if (round($consumo_total_mp, 2) > 0/* && $saldo_ant[$j]['controlada'] == "TRUE"*/) {
		if ($saldo_ant[$j]['controlada'] == "TRUE")
			$tpl->newBlock("fila");
		else if ($saldo_ant[$j]['controlada'] == "FALSE")
			$tpl->newBlock('fila_nc');
		$tpl->assign("codmp", $codmp);
		$tpl->assign("nombre", $nombremp);
		$tpl->assign("precio_unidad", number_format($costo_promedio, 4, ".", ","));
		
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
				else if ($pro_tur[3] > 0) {
					$consumo_turno[3] += $dif_fal;
					$consumo[3] += $dif_fal;
				}
				else {
					$consumo_turno[1] += $dif_fal / 2;
					$consumo[1] += $dif_fal / 2;
					$consumo_turno[2] += $dif_fal / 2;
					$consumo[2] += $dif_fal / 2;
				}
			}
		}
		
		// Mostrar consumos por turno del producto en curso
		foreach ($consumo_turno as $key => $value)
			$tpl->assign($key, $value != 0 ? number_format($value, 2, ".",",") : "&nbsp;");
		$tpl->assign("consumo", number_format($consumo_total_mp, 2, ".", ","));
	}
	// Si es materia prima no controlada
	/*else */if (round($consumo_nc, 2) > 0 && $saldo_ant[$j]['controlada'] == "FALSE") {
		// Materia prima no controlada
		if ($saldo_ant[$j]['tipo'] == 1) {
			$no_con = array();
			
			$no_con[1] = $pro_tur[3] > 0 || $pro_tur[4] > 0 ? 0.025 * $consumo_nc : 0.50 * $consumo_nc;
			$no_con[2] = $pro_tur[3] > 0 ? 0.025 * $consumo_nc : 0.50 * $consumo_nc;
			$no_con[3] = $pro_tur[4] > 0 ? 0.15 * $consumo_nc : ($pro_tur[3] > 0 ? 0.95 * $consumo_nc : 0);
			$no_con[4] = $pro_tur[4] > 0 ? 0.80 * $consumo_nc : 0;
			
			$no_controlado[1] += $no_con[1];
			$no_controlado[2] += $no_con[2];
			$no_controlado[3] += $no_con[3];
			$no_controlado[4] += $no_con[4];
		}
		// Material de empaque
		else {
			$no_con = array();
			
			$no_con[3] = $pro_tur[4] > 0 ? $consumo_nc * 0.20 : ($pro_tur[3] > 0 ? $consumo_nc * 0.90 : 0);
			$no_con[4] = $pro_tur[4] > 0 ? $consumo_nc * 0.70 : 0;
			$no_con[10] = $pro_tur[3] > 0 ? $consumo_nc * 0.10 : $consumo_nc;
			
			$no_controlado[3] += $no_con[3];
			$no_controlado[4] += $no_con[4];
			$no_controlado[10] += $no_con[10];
		}
	}
}
// Ultimas filas de hoja de consumo (para la ultima compa��a)
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
	if ($today < $limit) {
		$sql = "SELECT sum(importe) FROM movimiento_gastos WHERE num_cia = $num_cia AND fecha BETWEEN '$fecha1' AND '$fecha2' AND codgastos IN (23, 9, 76)";
		$mer = $db->query($sql);
		// Gastos de caja de codigo 28 (ABARROTES)
		$abarrotes_julild_salida = $db->query("SELECT sum(importe) FROM gastos_caja WHERE num_cia = $num_cia AND fecha BETWEEN '$fecha1' AND '$fecha2' AND cod_gastos = 28 AND tipo_mov = 'FALSE'");
		$abarrotes_julild_entrada = $db->query("SELECT sum(importe) FROM gastos_caja WHERE num_cia = $num_cia AND fecha BETWEEN '$fecha1' AND '$fecha2' AND cod_gastos = 28 AND tipo_mov = 'TRUE'");
		$abarrotes_julild = $abarrotes_julild_salida[0]['sum'] - $abarrotes_julild_entrada[0]['sum'];
		// Mercancias (c�lculo)
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
	}
	else {
		$sql = "SELECT cod_turno, sum(importe) FROM movimiento_gastos WHERE num_cia = $num_cia AND fecha BETWEEN '$fecha1' AND '$fecha2' AND codgastos IN (23, 9, 76)";
		$sql .= " GROUP BY cod_turno ORDER BY cod_turno";
		$tmp = $db->query($sql);
		
		$mercancias = 0;
		if ($tmp)
			foreach ($tmp as $reg)
				$mercancias += $reg['sum'];
		
		$consumo_mes += $mercancias;
		$tpl->assign("mercancias", $mercancias != 0 ? number_format($mercancias, 2, ".", ",") : "&nbsp;");
		// Desglozar mercancias
		if ($tmp)
		foreach ($tmp as $reg)
				if ($reg['cod_turno'] > 0) {
					$mer_tur[$reg['cod_turno']] = $reg['sum'];
					$consumo[$reg['cod_turno']] += $mer_tur[$reg['cod_turno']];
					$tpl->assign($reg['cod_turno'] . "_mercancias", $mer_tur[$reg['cod_turno']] != 0 ? number_format($mer_tur[$reg['cod_turno']], 2, ".", ",") : "&nbsp;");
				}
				else {
					// [6-Feb-2007] 60% de las mercancias no codificadas al Bizcochero
					$mer_tur[3] += round($reg['sum'] * 0.60, 2);
					$consumo[3] += round($reg['sum'] * 0.60, 2);
					$tpl->assign(3 . "_mercancias", $mer_tur[3] != 0 ? number_format($mer_tur[3], 2, ".", ",") : "&nbsp;");
					// [6-Feb-2007] 20% de las mercancias no codificadas al Repostero
					$mer_tur[4] += round($reg['sum'] * 0.20, 2);
					$consumo[4] += round($reg['sum'] * 0.20, 2);
					$tpl->assign(4 . "_mercancias", $mer_tur[4] != 0 ? number_format($mer_tur[4], 2, ".", ",") : "&nbsp;");
					// [6-Feb-2007] 20% de las mercancias no codificadas al Gelatinero
					$mer_tur[9] += round($reg['sum'] * 0.20, 2);
					$consumo[9] += round($reg['sum'] * 0.20, 2);
					$tpl->assign(9 . "_mercancias", $mer_tur[9] != 0 ? number_format($mer_tur[9], 2, ".", ",") : "&nbsp;");
				}
	}
	
	// Consumo Total
	foreach ($consumo as $key => $value)
		$tpl->assign($key . "_consumo_total", $value != 0 ? number_format($consumo[$key], 2, ".", ",") : "&nbsp;");
	$tpl->assign("consumo_total", $consumo_mes != 0 ? number_format($consumo_mes, 2, ".", ",") : "&nbsp;");
	
	// Producci�n y Consumo / Producci�n
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