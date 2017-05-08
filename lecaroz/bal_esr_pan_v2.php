<?php
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

$session = new sessionclass($dsn);

$db = new DBclass($dsn, "autocommit=yes");

//if ($_SESSION['iduser'] != 1) die("Modificando pantalla");

/**********************************************************************************/
/*** FUNCIONES SUPLEMENTARIAS                                                   ***/
/**********************************************************************************/
function microtime_float() {
   list($usec, $sec) = explode(" ", microtime());
   return ((float)$usec + (float)$sec);
}

function buscar_fal($array, $dia) {
	if (!$array)
		return 0;
	
	foreach ($array as $dato)
		if ($dato['dia'] == $dia || ($dia == 0 && $dato['dia'] == "0"))
			return $dato['dato'];
	
	return 0;
}

function mes_abreviado($mes) {
	switch ($mes) {
		case 1: $mes_abr = "Ene"; break;
		case 2: $mes_abr = "Feb"; break;
		case 3: $mes_abr = "Mar"; break;
		case 4: $mes_abr = "Abr"; break;
		case 5: $mes_abr = "May"; break;
		case 6: $mes_abr = "Jun"; break;
		case 7: $mes_abr = "Jul"; break;
		case 8: $mes_abr = "Ago"; break;
		case 9: $mes_abr = "Sep"; break;
		case 10: $mes_abr = "Oct"; break;
		case 11: $mes_abr = "Nov"; break;
		case 12: $mes_abr = "Dic"; break;
		default: $mes_abr = ""; break;
	}
	
	return $mes_abr;
}

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
			
			if ($existencia >= 0)
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
				
				if ($existencia >= 0)
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
/**********************************************************************************/
//$inicio = microtime_float();
$inicio_gral = microtime_float();
// Construir fecha inicial y final

// Hacer un nuevo objeto TemplatePower
//$tpl = new TemplatePower( "./plantillas/bal/bal_esr_pan_v2.tpl" );
$tpl = new TemplatePower( "./plantillas/bal/bal_esr_pan_v3.tpl" );
$tpl->prepare();

$mes    = $_GET['mes'];
$anio   = $_GET['anio'];
$fecha1 = date("d/m/Y", mktime(0, 0, 0, $_GET['mes'], 1, $_GET['anio']));
$fecha2 = date("d/m/Y", mktime(0, 0, 0, $_GET['mes'] + 1 ,0, $_GET['anio']));
$dias   = date("d", mktime(0, 0, 0, $_GET['mes'] + 1, 0, $_GET['anio']));

// [24-Ene-2007] Validar que no se generen balances si no ha terminado el mes actual
$ts = mktime(0, 0, 0, $mes, 1, $anio);
$limit  = mktime(0, 0, 0, date('n'), 1, date('Y'));
if ($ts >= $limit) {
	$tpl->newBlock('cerrar');
	$tpl->assign('msj', 'No se pueden generar balances porque no ha terminado el mes solicitado');
	$tpl->printToScreen();
	die;
}

// [4-Ene-2007] Validar que no se impriman balances si no estan todas las reservas
$agui = $db->query("SELECT num_cia FROM catalogo_companias WHERE num_cia < 100 AND num_cia IN (SELECT num_cia FROM total_panaderias WHERE fecha BETWEEN '$fecha1' AND '$fecha2' GROUP BY num_cia) AND num_cia NOT IN (SELECT num_cia FROM reservas_cias WHERE num_cia < 100 AND fecha = '$fecha1' AND cod_reserva = 1 GROUP BY num_cia) ORDER BY num_cia");
$imss = $db->query("SELECT num_cia FROM catalogo_companias WHERE num_cia < 100 AND num_cia IN (SELECT num_cia FROM total_panaderias WHERE fecha BETWEEN '$fecha1' AND '$fecha2' GROUP BY num_cia) AND num_cia NOT IN (SELECT num_cia FROM reservas_cias WHERE num_cia < 100 AND fecha = '$fecha1' AND cod_reserva = 4 GROUP BY num_cia) ORDER BY num_cia");
if ($anio > 2007 && $mes >= date('n', mktime(0, 0, 0, date('d') < 6 ? date('n') - 1 : date('n'), 1, date('Y'))) && ($agui || $imss)) {
	$tpl->newBlock('cerrar');
	$tpl->assign('msj', 'Existen reservas sin capturar');
	$tpl->printToScreen();
	die;
}

$ultima_fecha_mov = $db->query("SELECT fecha FROM mov_inv_real_temp GROUP BY fecha ORDER BY fecha LIMIT 1");
ereg("([0-9]{1,2})/([0-9]{1,2})/([0-9]{2,4})", $ultima_fecha_mov[0]['fecha'], $tmp);

$tabla_movs = /*mktime(0, 0, 0, $mes, 1, $anio) < mktime(0, 0, 0, $tmp[2], 1, $tmp[3]) ? "mov_inv_real" : "mov_inv_real_temp"*/"mov_inv_real";

// ***** ESTE PROCESO SOLO ES VALIDO A PARTIR DEL MES DE JUNIO DEL 2005
if (mktime(0,0,0,$mes,1,$anio) < mktime(0,0,0,5,5,2005))
	die("ESTE PROCESO SOLO ES VALIDO A PARTIR DEL MES DE JUNIO DEL 2005 ^_^");

// Obtener compañias
$sql = "SELECT num_cia,nombre,nombre_corto FROM catalogo_companias WHERE " . (isset($_GET['compania']) && $_GET['compania'] > 0 ? "num_cia = $_GET[compania]" : "num_cia < 100") . " ORDER BY num_cia";
$cia = $db->query($sql);

if (!$cia)
	die("No hay resultados");

for ($f = 0; $f < count($cia); $f++) {
	$num_cia = $cia[$f]['num_cia'];
	
	// Validar que se hayan actualizado los encargados
	if (!$db->query("SELECT id FROM encargados WHERE num_cia = $num_cia AND mes = $mes AND anio = $anio LIMIT 1"))
		$db->query("INSERT INTO encargados (num_cia,nombre_inicio,nombre_fin,mes,anio) SELECT num_cia,nombre_inicio,nombre_fin,$mes,$anio FROM encargados WHERE mes = " . ($mes > 1 ? $mes - 1 : 12) . " AND anio = " . ($mes > 1 ? $anio : $anio - 1));
	
	/******************************************************************************************************************/
	/************************************************** PRIMERA HOJA **************************************************/
	/******************************************************************************************************************/
	
	/**** AUXILIAR DE INVENTARIO ****/
	
	// Obtener saldos anteriores de hitorico_inventario
	$fecha_historico = date("d/m/Y",mktime(0,0,0,$mes,0,$anio));
	
	// Saldos anteriores
	$sql = "SELECT num_cia, codmp, nombre, existencia, precio_unidad, controlada, tipo FROM historico_inventario LEFT JOIN catalogo_mat_primas USING (codmp) LEFT JOIN control_avio USING (num_cia, codmp) WHERE";
	// Si es para una compañía en específico
	$sql .= " num_cia = $num_cia AND fecha = '$fecha_historico' AND codmp NOT IN (90) GROUP BY num_cia, codmp, nombre, existencia, precio_unidad, controlada, tipo, num_orden ORDER BY num_cia, num_orden";
	$saldo_ant = $db->query($sql);
	
	// Saldo actual
	$sql = "SELECT num_cia, fecha, codmp, nombre, descripcion, cod_turno, tipo_mov, cantidad, precio_unidad, total_mov FROM $tabla_movs JOIN catalogo_mat_primas USING (codmp) WHERE";
	// Si es para una compañía en específico
	$sql .= " num_cia = $num_cia AND fecha BETWEEN '$fecha1' AND '$fecha2' AND descripcion != 'DIFERENCIA INVENTARIO' AND codmp NOT IN (90) ORDER BY num_cia, controlada DESC, tipo, codmp, fecha, tipo_mov, cantidad DESC";
	$saldo = $db->query($sql);
	
	// Diferencias del mes
	$sql = "SELECT num_cia, fecha, codmp, nombre, descripcion, cod_turno, tipo_mov, cantidad, precio_unidad, total_mov FROM $tabla_movs JOIN catalogo_mat_primas USING (codmp) WHERE";
	// Si es para una compañía en específico
	$sql .= " num_cia = $num_cia AND fecha BETWEEN '$fecha1' AND '$fecha2' AND descripcion = 'DIFERENCIA INVENTARIO' AND codmp NOT IN (90) ORDER BY num_cia, controlada DESC, tipo, codmp, fecha, tipo_mov";
	$difInv = $db->query($sql);
	
	$nummp = count($saldo_ant);
	$nummov = count($saldo);
	
	// MP / Producción (Balance)
	$sql = "SELECT mp_pro, gas_pro FROM balances_pan WHERE num_cia = $num_cia AND mes = $mes AND anio = $anio";
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
	$con_pro       = array(1 => 0, 2 => 0, 3 => 0, 4 => 0, 8 => 0, 9 => 0, 10 => 0);
	
	$total_valores = 0;
	$total_valores_entrada = 0;
	$total_valores_salida = 0;
	
	$compra_directa = 0;
	
	// Produccion
	$sql = "SELECT cod_turnos, sum(imp_produccion) FROM produccion WHERE num_cia = $num_cia AND fecha BETWEEN '$fecha1' AND '$fecha2' GROUP BY cod_turnos ORDER BY cod_turnos";
	$produccion = $db->query($sql);
	
	// Obtener que turnos tuvieron produccion
	for ($i = 0; $i < count($produccion); $i++)
		if ($produccion[$i]['sum'] > 0) {
			$pro_tur[$produccion[$i]['cod_turnos']] = $produccion[$i]['sum'];
			$total_pro += $produccion[$i]['sum'];
		}
	
	for ($j = 0; $j < $nummp; $j++) {
		$codmp = $saldo_ant[$j]['codmp'];
		$nombremp = $saldo_ant[$j]['nombre'];
		
		$unidades = $saldo_ant[$j]['existencia'];
		$valores  = $saldo_ant[$j]['existencia'] * $saldo_ant[$j]['precio_unidad'];
		$valores_entrada = 0;
		$valores_salida = 0;
		
		$costo_promedio   = $saldo_ant[$j]['precio_unidad'];
		
		$unidades_ant = $unidades;
		$costo_ant = $costo_promedio;
		
		$consumo_turno = array(1 => 0, 2 => 0, 3 => 0, 4 => 0, 8 => 0, 9 => 0, 10 => 0);	// Consumo por turno del producto
		$consumo_total_mp = 0;		// Consumo total de la materia prima
		$consumo_parcial_mp = 0;	// Consumo parcial de la materia prima
		$consumo_nc = 0;			// Consumo total de la materia prima no controlada
		$dif_fal = 0;
		
		$arrastre = FALSE;	// Flag. Indica si debe arrastrarse el costo promedio
		
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
					
					$valores_salida += $val_sal;
					
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
					
					$valores_entrada += $saldo[$k]['cantidad'] * $precio_unidad;
					
					// Acumular todas las compras directas para posteriormente restarlas al datos de compras
					if ($saldo[$k]['descripcion'] == "COMPRA DIRECTA")
						$compra_directa += $saldo[$k]['cantidad'] * $precio_unidad;
					
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
				
				$valores_salida += $val_sal;
				
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
				
				$valores_entrada += $difInv[$idDif]['cantidad'] * $precio_unidad;
			}
		}
		$total_valores += $unidades * $costo_promedio;
		$total_valores_entrada += $valores_entrada;
		$total_valores_salida += $valores_salida;
		
		// Si el producto tuvo movimientos en el mes
		if (round($consumo_total_mp, 2) > 0 && $saldo_ant[$j]['controlada'] == "TRUE") {
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
		}
		// Si es materia prima no controlada
		else if (round($consumo_nc, 2) > 0 && $saldo_ant[$j]['controlada'] == "FALSE") {
			// Materia prima no controlada
			if ($saldo_ant[$j]['tipo'] == 1) {
				$no_controlado[1] += $pro_tur[3] > 0 ? 0.025 * $consumo_nc : 0.50 * $consumo_nc;
				$no_controlado[2] += $pro_tur[3] > 0 ? 0.025 * $consumo_nc : 0.50 * $consumo_nc;
				$no_controlado[3] += $pro_tur[4] > 0 ? 0.15 * $consumo_nc : ($pro_tur[3] > 0 ? 0.95 * $consumo_nc : 0);
				$no_controlado[4] += $pro_tur[4] > 0 ? 0.80 * $consumo_nc : 0;
			}
			// Material de empaque
			else {
				$no_controlado[3] += $pro_tur[4] > 0 ? $consumo_nc * 0.20 : ($pro_tur[3] > 0 ? $consumo_nc * 0.90 : 0);
				$no_controlado[4] += $pro_tur[4] > 0 ? $consumo_nc * 0.70 : 0;
				$no_controlado[10] += $pro_tur[3] > 0 ? $consumo_nc * 0.10 : $consumo_nc;
			}
		}
	}
	
	// Materia prima no controlada
	$total_mp_nc = 0;
	foreach ($no_controlado as $key => $value) {
		$total_mp_nc += $value;
		
		// Sumar a consumos
		$consumo[$key] += $value;
		$consumo_mes += $value;
	}
	
	// Mercancias
	if (mktime(0, 0, 0, $mes, 1, $anio) < mktime(0, 0, 0, 1, 1, 2007)) {
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
		// Desglozar mercancias
		foreach ($pro_tur as $key => $value)
			if ($key != 1 && $key != 2 && $key != 10) {
				@$porcentaje = ($pro_tur[$key] * 100) / ($total_pro - $pro_tur[1] - $pro_tur[2]);
				@$mer_tur[$key] = $mercancias * $porcentaje / 100;
				
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
		// Desglozar mercancias
		if ($tmp)
			foreach ($tmp as $reg)
				if ($reg['cod_turno'] > 0) {
					$mer_tur[$reg['cod_turno']] = $reg['sum'];
					$consumo[$reg['cod_turno']] += $mer_tur[$reg['cod_turno']];
				}
				else {
					$mer_tur[3] += $reg['sum'];
					$consumo[3] += $mer_tur[3];
				}
	}
	
	/*-------------------------------------------*/
	/*$fin = microtime_float();
	$tiempo = $fin - $inicio;
	echo "Tiempo de ejecución AVIO: ".round($tiempo,3)." segundos<br>";*/
	/*-------------------------------------------*/
	
	// ************* HOJA 1, SECCION 1 *************
	
	/*-------------------------------------------*/
	//$inicio = microtime_float();
	/*-------------------------------------------*/
	
	/**** BARREDURA ****/
	$sql = "SELECT sum(importe) FROM barredura WHERE num_cia = $num_cia AND fecha_pago BETWEEN '$fecha1' AND '$fecha2'";
	$barredura = $db->query($sql);
	
	/**** PASTILLAJE ****/
	$sql = "SELECT sum(pastillaje) FROM total_panaderias WHERE num_cia = $num_cia AND fecha BETWEEN '$fecha1' AND '$fecha2'";
	$pastillaje = $db->query($sql);
	
	/**** ABONO EMPLEADOS ****/
	$sql = "SELECT sum(importe) FROM prestamos WHERE num_cia = $num_cia AND fecha BETWEEN '$fecha1' AND '$fecha2' AND tipo_mov = 'TRUE'";
	$abono_empleados = $db->query($sql);
	
	/**** OTROS ****/
	$sql = "SELECT sum(otros) FROM total_panaderias WHERE num_cia = $num_cia AND fecha BETWEEN '$fecha1' AND '$fecha2'";
	$otros = $db->query($sql);
	
	// Los balances de enero del 2005 en otros no incluian abono a empleados, asi que no se restara
	if ($mes == 1 && $anio == 2005)
		$otros[0]['sum'] = $otros[0]['sum'] - $barredura[0]['sum'] > 0 ? $otros[0]['sum'] - $barredura[0]['sum'] : $otros[0]['sum'];
	// Restar a otros las entradas de abono a empleados y barredura
	else
		$otros[0]['sum'] = $otros[0]['sum'] - $abono_empleados[0]['sum'] - $barredura[0]['sum'];
	
	/**** TOTAL OTROS ****/
	$total_otros = $pastillaje[0]['sum'] + $otros[0]['sum'] + $barredura[0]['sum'] + $abono_empleados[0]['sum'];
	
	/**** ABONO REPARTO ****/
	$sql = "SELECT sum(abono) FROM mov_expendios WHERE num_cia = $num_cia AND fecha BETWEEN '$fecha1' AND '$fecha2'";
	$abono_reparto = $db->query($sql);
	
	// [08-Feb-2007] Obtener abono reparto del año anterior para comparativo
	$fecha1_ant = date("d/m/Y", mktime(0, 0, 0, $_GET['mes'], 1, $_GET['anio'] - 1));
	$fecha2_ant = date("d/m/Y", mktime(0, 0, 0, $_GET['mes'] + 1, 0, $_GET['anio'] - 1));
	$sql = "SELECT sum(abono) FROM mov_expendios WHERE num_cia = $num_cia AND fecha BETWEEN '$fecha1_ant' AND '$fecha2_ant'";
	$abono_reparto_ant = $db->query($sql);
	
	$p_abono_reparto = $abono_reparto_ant[0]['sum'] > 0 ? abs(($abono_reparto[0]['sum'] * 100 / $abono_reparto_ant[0]['sum']) - 100) : 0;
	$m_abono_reparto = $abono_reparto[0]['sum'] > $abono_reparto_ant[0]['sum'] ? "<span style=\"color:#0000CC\"> SUBIO " . ($p_abono_reparto <= 800 ? number_format($p_abono_reparto, 2) . '%' : '') . "</span>" : ($abono_reparto[0]['sum'] < $abono_reparto_ant[0]['sum'] ? "<span style=\"color:#CC0000\"> BAJO " . ($p_abono_reparto <= 800 ? number_format($p_abono_reparto, 2) . '%' : '') . "</span>" : '');
	
	/**** ERRORES ****/
	$sql = "SELECT sum(am_error + pm_error) FROM captura_efectivos WHERE num_cia = $num_cia AND fecha >= '$fecha1' AND fecha <= '$fecha2'";
	$errores = $db->query($sql);
	
	/**** VENTA EN PUERTA ****/
	$sql = "SELECT sum(venta_puerta) FROM total_panaderias WHERE num_cia = $num_cia AND fecha BETWEEN '$fecha1' AND '$fecha2'";
	$venta_puerta = $db->query($sql);
	$venta_puerta[0]['sum'] = $venta_puerta[0]['sum'] + $errores[0]['sum'];
	
	// [08-Feb-2007] Obtener venta en puerta del año anterior para comparativo
	$sql = "SELECT sum(venta_puerta) FROM total_panaderias WHERE num_cia = $num_cia AND fecha BETWEEN '$fecha1_ant' AND '$fecha2_ant'";
	$venta_puerta_ant = $db->query($sql);
	
	$p_venta_puerta = $venta_puerta_ant[0]['sum'] > 0 ? abs((($venta_puerta[0]['sum'] - $errores[0]['sum']) * 100 / $venta_puerta_ant[0]['sum']) - 100) : 0;
	$m_venta_puerta = $venta_puerta[0]['sum'] - $errores[0]['sum'] > $venta_puerta_ant[0]['sum'] ? '<span style="color:#0000CC"> SUBIO ' . number_format($p_venta_puerta, 2) . '%</span>' : ($venta_puerta[0]['sum'] - $errores[0]['sum'] < $venta_puerta_ant[0]['sum'] ? '<span style="color:#CC0000"> BAJO ' . number_format($p_venta_puerta, 2) . '%</span>' : '');
	
	/**** VENTAS NETAS ****/
	$ventas_netas = $venta_puerta[0]['sum'] + $total_otros + $abono_reparto[0]['sum'] - $errores[0]['sum'];
	
	// [09-Feb-2007] Obtener las ventas netas del año anterior para comparativo
	$sql = "SELECT ventas_netas FROM balances_pan WHERE num_cia = $num_cia AND mes = $mes AND anio = " . ($anio - 1);
	$tmp = $db->query($sql);
	$ventas_netas_ant = $tmp ? $tmp[0]['ventas_netas'] : 0;
	
	$p_ventas_netas = $ventas_netas_ant > 0 ? abs(($ventas_netas * 100 / $ventas_netas_ant) - 100) : 0;
	$m_ventas_netas = $ventas_netas > $ventas_netas_ant ? '<span style="color:#0000CC"> SUBIO ' . number_format($p_ventas_netas, 2) . '%</span>' : ($ventas_netas < $ventas_netas_ant ? '<span style="color:#CC0000"> BAJO ' . number_format($p_ventas_netas, 2) . '%</span>' : '');
	
	// [24-Ene-2007] Si no hay ventas, terminar el proceso
	if ($ventas_netas == 0) {
		/*$tpl->newBlock('cerrar');
		$tpl->printToScreen();
		die;*/
		continue;
	}
	
	/*-------------------------------------------*/
	/*$fin = microtime_float();
	$tiempo = $fin - $inicio;
	echo "Tiempo de ejecución VENTAS: ".round($tiempo,3)." segundos<br>";*/
	/*-------------------------------------------*/
	
	// ************* HOJA 1, SECCION 2 *************
	
	/*-------------------------------------------*/
	$inicio = microtime_float();
	/*-------------------------------------------*/
	
	/**** INVENTARIO ANTERIOR ****/
	if ($mes == 1 && $anio == 2005)
		$sql = "SELECT sum(precio_unidad*existencia) FROM historico_inventario WHERE num_cia = $num_cia AND codmp NOT IN (90) AND fecha = '$fecha_historico'";
	else
		$sql = "SELECT inv_act AS sum FROM balances_pan WHERE num_cia = $num_cia AND mes = " . ($mes == 1 ? 12 : $mes - 1) . " AND anio = " . ($mes == 1 ? $anio - 1 : $anio);
	$inv_ant = $db->query($sql);
	
	/**** COMPRAS ****/
	$compras = $total_valores_entrada;
	// Restar las compras directas
	$compras -= $compra_directa;
	
	/**** INVENTARIO ACTUAL ****/
	$inv_act = $total_valores;
	
	/**** MATERIA PRIMA UTILIZADA ****/
	$mat_prima_utilizada = $inv_ant[0]['sum'] + $compras + $mercancias - $inv_act;
	
	/**** MANO DE OBRA ****/
	$sql = "SELECT sum(raya_pagada) FROM total_produccion WHERE numcia = $num_cia AND fecha_total BETWEEN '$fecha1' AND '$fecha2'";
	$mano_obra = $db->query($sql);
	
	/**** PANADEROS ****/
	$sql = "SELECT sum(importe) FROM movimiento_gastos WHERE num_cia = $num_cia AND fecha >= '$fecha1' AND fecha <= '$fecha2' AND codgastos = 3";
	$panaderos = $db->query($sql);
	
	/**** GASTOS DE FABRICACION ****/
	$sql = "SELECT sum(importe) FROM catalogo_gastos JOIN movimiento_gastos USING (codgastos) WHERE num_cia = $num_cia AND fecha BETWEEN '$fecha1' AND '$fecha2' AND codigo_edo_resultados = 1 GROUP BY num_cia";
	$gastos_fab = $db->query($sql);
	
	/**** COSTO DE PRODUCCION ****/
	$costo_produccion = $mat_prima_utilizada + $mano_obra[0]['sum'] + $panaderos[0]['sum'] + $gastos_fab[0]['sum'];
	
	/**** UTILIDAD BRUTA ****/
	$utilidad_bruta = $ventas_netas - $costo_produccion;
	
	/*-------------------------------------------*/
	/*$fin = microtime_float();
	$tiempo = $fin - $inicio;
	echo "Tiempo de ejecución INVENTARIO: ".round($tiempo,3)." segundos<br>";*/
	/*-------------------------------------------*/
	
	// ************* HOJA 1, SECCION 3 *************
	
	/*-------------------------------------------*/
	//$inicio = microtime_float();
	/*-------------------------------------------*/
	
	/**** PAN COMPRADO ****/
	$ts_bal = mktime(0, 0, 0, $mes, 1, $anio);
	$ts_limit = mktime(0, 0, 0, 9, 1, 2006);
	if (/*$_GET['mes'] >= 9 && $_GET['anio'] >= 2006*/$ts_bal >= $ts_limit) {
		//PAN COMPRADO CON DESCUENTO
		$sql = "SELECT sum(importe) AS pan_comprado FROM movimiento_gastos WHERE num_cia = $num_cia AND fecha BETWEEN '$fecha1' AND '$fecha2' AND codgastos = 5 AND captura = 'FALSE'";
		$temp = $db->query($sql);
		//PAN COMPRADO SIN DESCUENTO
		$sql = "SELECT sum(importe) AS pan_comprado FROM movimiento_gastos WHERE num_cia = $num_cia AND fecha BETWEEN '$fecha1' AND '$fecha2' AND codgastos = 152 AND captura = 'FALSE'";
		$temp1 = $db->query($sql);
		//PAN COMPRADO 10% DESCUENTO
		$sql = "SELECT sum(importe) AS pan_comprado FROM movimiento_gastos WHERE num_cia = $num_cia AND fecha BETWEEN '$fecha1' AND '$fecha2' AND codgastos = 159 AND captura = 'FALSE'";
		$comp10 = $db->query($sql);
		//PAN COMPRADO 10% DESCUENTO
		$sql = "SELECT sum(importe) AS pan_comprado FROM movimiento_gastos WHERE num_cia = $num_cia AND fecha BETWEEN '$fecha1' AND '$fecha2' AND codgastos = 177 AND captura = 'FALSE'";
		$comp15 = $db->query($sql);
		
		
		$pan_comprado = -1 * $temp[0]['pan_comprado'];
		$pan_comprado += -1 * $temp1[0]['pan_comprado'];
		$pan_comprado += -1 * $comp10[0]['pan_comprado'];
		$pan_comprado += -1 * $comp15[0]['pan_comprado'];
	}
	else {
		//PAN COMPRADO CON DESCUENTO
		$sql = "SELECT sum(importe) + sum(importe) * ((SELECT porcentaje FROM porcentaje_pan_comprado WHERE num_cia = $num_cia) / 100) AS pan_comprado FROM movimiento_gastos WHERE num_cia = $num_cia AND fecha BETWEEN '$fecha1' AND '$fecha2' AND codgastos = 5 AND captura = 'FALSE'";
		$temp = $db->query($sql);
		//PAN COMPRADO SIN DESCUENTO
		$sql = "SELECT sum(importe) + sum(importe) * ((SELECT porcentaje FROM porcentaje_pan_comprado WHERE num_cia = $num_cia) / 100) AS pan_comprado FROM movimiento_gastos WHERE num_cia = $num_cia AND fecha BETWEEN '$fecha1' AND '$fecha2' AND codgastos = 152 AND captura = 'FALSE'";
		$temp1 = $db->query($sql);
		
		$pan_comprado = -1 * $temp[0]['pan_comprado'] / 1.25;
		$pan_comprado += -1 * $temp1[0]['pan_comprado'];
	}
	
	/**** GASTOS GENERALES ****/
	$sql = "SELECT sum(importe) FROM catalogo_gastos JOIN movimiento_gastos USING (codgastos) WHERE num_cia = $num_cia AND fecha BETWEEN '$fecha1' AND '$fecha2' AND codigo_edo_resultados = 2 AND codgastos NOT IN (9, 76" . (mktime(0, 0, 0, $mes, 1, $anio) > mktime(0, 0, 0, 9, 1, 2006) ? ", 140, 141" : ", 141") . ") GROUP BY num_cia";
	$gastos_gral = $db->query($sql);
	@$gastos_gral[0]['sum'] *= -1;
	
	/**** GASTOS DE CAJA (no incluir cod. 28 abarrotes julild) ****/
	$egresos = $db->query("SELECT sum(importe) FROM gastos_caja WHERE num_cia = $num_cia AND fecha BETWEEN '$fecha1' AND '$fecha2' AND tipo_mov = 'FALSE' AND clave_balance = 'TRUE' AND cod_gastos NOT IN (28)");
	$ingresos = $db->query("SELECT sum(importe) FROM gastos_caja WHERE num_cia = $num_cia AND fecha BETWEEN '$fecha1' AND '$fecha2' AND tipo_mov = 'TRUE' AND clave_balance = 'TRUE' AND cod_gastos NOT IN (28)");
	$gastos_caja = $ingresos[0]['sum'] - $egresos[0]['sum'];
	
	/**** [27-Mar-2007] Comisiones bancarias ****/
	$comisiones = 0;
	if (mktime(0, 0, 0, $mes, 1, $anio) >= mktime(0, 0, 0, 3, 1, 2007)) {
		/*$sql = "(SELECT ec.tipo_mov, sum(importe) AS importe FROM estado_cuenta AS ec LEFT JOIN catalogo_mov_bancos AS cm USING (cod_mov) WHERE fecha BETWEEN '$fecha1' AND '$fecha2'";
		$sql .= " AND entra_bal = 'TRUE' AND cuenta = 1 AND num_cia = $num_cia GROUP BY ec.tipo_mov) UNION (SELECT ec.tipo_mov, sum(importe) FROM estado_cuenta AS ec LEFT";
		$sql .= " JOIN catalogo_mov_santander AS cm USING (cod_mov) WHERE fecha BETWEEN '$fecha1' AND '$fecha2' AND entra_bal = 'TRUE' AND cuenta = 2 AND num_cia = $num_cia GROUP BY ec.tipo_mov)";*/
		$sql = "(SELECT tipo_mov, sum(importe) AS importe FROM estado_cuenta AS ec WHERE num_cia = $num_cia AND fecha BETWEEN '$fecha1' AND '$fecha2' AND cuenta = 1 AND cod_mov IN (SELECT cod_mov FROM";
		$sql .= " catalogo_mov_bancos WHERE entra_bal = 'TRUE' GROUP BY cod_mov) GROUP BY tipo_mov) UNION (SELECT tipo_mov, sum(importe) AS importe FROM estado_cuenta AS ec WHERE num_cia = $num_cia";
		$sql .= " AND fecha BETWEEN '$fecha1' AND '$fecha2' AND cuenta = 2 AND cod_mov IN (SELECT cod_mov FROM catalogo_mov_santander WHERE entra_bal = 'TRUE' GROUP BY cod_mov) GROUP BY";
		$sql .= " tipo_mov)";
		$result = $db->query($sql);
		
		if ($result)
			foreach ($result as $reg)
				$comisiones += $reg['tipo_mov'] == 'f' ? $reg['importe'] : -$reg['importe'];
	}
	
	/**** RESERVAS ****/
	$sql = "SELECT sum(importe) FROM reservas_cias WHERE num_cia = $num_cia AND fecha = '$fecha1'";
	$reservas = $db->query($sql);
	$reservas[0]['sum'] *= -1;
	
	/**** PAGOS HECHOS POR ANTICIPADO ****/
	$sql = "SELECT sum(importe) FROM pagos_anticipados WHERE num_cia = $num_cia AND (fecha_ini, fecha_fin) OVERLAPS (DATE '$fecha1', DATE '$fecha2')";
	$pagos_anticipados = $db->query($sql);
	$pagos_anticipados[0]['sum'] *= -1;
	
	/**** GASTOS PAGADOS POR OTRAS COMPAÑIAS ****/
	$cia_gasto_egreso = $db->query("SELECT sum(monto) FROM gastos_otras_cia WHERE num_cia_egreso = $num_cia AND fecha BETWEEN '$fecha1' AND '$fecha2'");
	$cia_gasto_ingreso = $db->query("SELECT sum(monto) FROM gastos_otras_cia WHERE num_cia_ingreso = $num_cia AND fecha BETWEEN '$fecha1' AND '$fecha2'");
	$gastos_otros = $cia_gasto_egreso[0]['sum'] - $cia_gasto_ingreso[0]['sum'];
	
	/**** TOTAL DE GASTOS ****/
	$gastos_totales = $pan_comprado + $gastos_gral[0]['sum'] + $gastos_caja + $reservas[0]['sum'] + $pagos_anticipados[0]['sum'] + $gastos_otros;
	
	/**** INGRESOS EXTRAORDINARIOS ****/
	if (empty($_GET['no_gastos'])) {
		$sql = "SELECT sum(importe) FROM estado_cuenta WHERE num_cia = $num_cia AND fecha BETWEEN '$fecha1' AND '$fecha2' AND cod_mov = 18";
		$ingresos_ext = $db->query($sql);
	}
	else
		$ingresos_ext[0]['sum'] = 0;
	
	/**** UTILIDAD NETA ****/
	$utilidad_neta = $gastos_totales + $ingresos_ext[0]['sum'] + $utilidad_bruta + $comisiones;
	
	// [09-Feb-2007] Obtener utilidad neta del año pasado para comparativo
	$tmp = $db->query("SELECT utilidad_neta FROM balances_pan WHERE num_cia = $num_cia AND mes = $mes AND anio = " . ($anio - 1));
	$utilidad_neta_ant = $tmp ? $tmp[0]['utilidad_neta'] : 0;
	
	@$p_utilidad_neta = /*$utilidad_neta_ant*/$utilidad_neta - $utilidad_neta_ant - $ingresos_ext[0]['sum'] /*>*/!= 0 ? abs(($utilidad_neta * 100 / $utilidad_neta_ant) - 100) : 0;
	$m_utilidad_neta = /*$utilidad_neta > $utilidad_neta_ant*/$utilidad_neta - $utilidad_neta_ant - $ingresos_ext[0]['sum'] > 0 ? '<span style="color:#0000CC"> ' . number_format($p_utilidad_neta, 2) . '%</span>' : (/*$utilidad_neta < $utilidad_neta_ant*/$utilidad_neta - $utilidad_neta_ant - $ingresos_ext[0]['sum'] < 0 ? '<span style="color:#CC0000"> ' . number_format($p_utilidad_neta, 2) . '%</span>' : '');
	
	/*-------------------------------------------*/
	/*$fin = microtime_float();
	$tiempo = $fin - $inicio;
	echo "Tiempo de ejecución GASTOS: ".round($tiempo,3)." segundos<br>";*/
	/*-------------------------------------------*/
	
	// ************* HOJA 1, SECCION 5 *************
	
	/*-------------------------------------------*/
	//$inicio = microtime_float();
	/*-------------------------------------------*/
	
	/**** PRODUCCION TOTAL ****/
	$sql = "SELECT sum(total_produccion) FROM total_produccion WHERE numcia = $num_cia AND fecha_total BETWEEN '$fecha1' AND '$fecha2'";
	$produccion_total = $db->query($sql);
	
	/**** GANANCIA ****/
	$sql = "SELECT sum(pan_p_venta - pan_p_expendio) FROM mov_expendios WHERE num_cia = $num_cia AND fecha BETWEEN '$fecha1' AND '$fecha2'";
	$ganancia = $db->query($sql);
	
	/**** PORCENTAJE DE GANANCIA ****/
	$sql = "SELECT (sum(pan_p_venta) - sum(pan_p_expendio)) * 100 / sum(pan_p_venta) AS sum FROM mov_expendios WHERE num_cia = $num_cia AND fecha BETWEEN '$fecha1' AND '$fecha2' AND pan_p_venta > 0";
	$porc_ganancia = $db->query($sql);
	
	/**** FALTANTE DE PAN ****/
	$faltante_pan = 0;
	/*for ($d = 1; $d <= $dias; $d++) {
		$pro = $db->query("SELECT sum(total_produccion) FROM total_produccion WHERE numcia = $num_cia AND fecha_total = '$d/$mes/$anio'");
//		$pc = $db->query("SELECT sum(importe) + sum(importe) * ((SELECT porcentaje FROM porcentaje_pan_comprado WHERE num_cia = $num_cia) / 100) AS pan_comprado FROM movimiento_gastos WHERE num_cia = $num_cia AND fecha = '$d/$mes/$anio' AND codgastos = 5 AND captura = 'FALSE'");
		$pc = $db->query("SELECT (sum(importe) / (100 - (SELECT porcentaje FROM porcentaje_pan_comprado WHERE num_cia = $num_cia))) * 100 AS pan_comprado FROM movimiento_gastos WHERE num_cia = $num_cia AND fecha = '$d/$mes/$anio' AND codgastos = 5 AND captura = 'FALSE'");
		// SE AGREGO PAN COMPRADO SIN DESCUENTO (2005/08/04) POR IVAN HELSING
		$pc1 = $db->query("SELECT sum(importe) AS pan_comprado FROM movimiento_gastos WHERE num_cia = $num_cia AND fecha = '$d/$mes/$anio' AND codgastos = 152 AND captura = 'FALSE'");
	
		$prueba_pan_ant = $db->query("SELECT sum(importe) FROM prueba_pan WHERE num_cia = $num_cia AND fecha = '" . date("d/m/Y", mktime(0, 0, 0, $mes, $d - 1, $anio)) . "'");
		$total_pan = $pro[0]['sum'] + $pc[0]['pan_comprado'] + $prueba_pan_ant[0]['sum'] + $pc1[0]['pan_comprado']; // SE AGREGO EL PC1
		$pan_quebrado = $pro[0]['sum'] * 0.02;
		$vp = $db->query("SELECT sum(venta_puerta) FROM total_panaderias WHERE num_cia = $num_cia AND fecha = '$d/$mes/$anio'");
		$reparto = $db->query("SELECT sum(pan_p_venta) FROM mov_expendios WHERE num_cia = $num_cia AND fecha = '$d/$mes/$anio'");
		$desc_pastel = $db->query("SELECT sum(desc_pastel) FROM captura_efectivos WHERE num_cia = $num_cia AND fecha = '$d/$mes/$anio'");
		$sobrante = $total_pan - $vp[0]['sum'] - $reparto[0]['sum'] - $pan_quebrado /*- $desc_pastel[0]['sum']*/;	// SE QUITO LA DEGUSTACION. 07/09/2005
		/*$prueba_pan = $db->query("SELECT sum(importe) FROM prueba_pan WHERE num_cia = $num_cia AND fecha = '$d/$mes/$anio'");
		$faltante = $prueba_pan[0]['sum'] - $sobrante;
		$faltante_pan += $faltante;
	}*/
	// CAMBIO HECHO EL DIA 28 DE FEBRERO DEL 2006 PARA CALCULAR EL FALTANTE DE PAN
	$pro = $db->query("SELECT sum(total_produccion) AS dato, extract(day FROM fecha_total) AS dia FROM total_produccion WHERE numcia = $num_cia AND fecha_total BETWEEN '$fecha1' AND '$fecha2' GROUP BY fecha_total ORDER BY fecha_total");
	$pc = $db->query("SELECT (sum(importe) / (100 - (SELECT porcentaje FROM porcentaje_pan_comprado WHERE num_cia = $num_cia))) * 100 AS dato, extract(day FROM fecha) AS dia FROM movimiento_gastos WHERE num_cia = $num_cia AND fecha BETWEEN '$fecha1' AND '$fecha2' AND codgastos = 5 AND captura = 'FALSE' GROUP BY fecha ORDER BY fecha");
	// (04/Mayo/2006) Pan comprado con descuento del 10%
	$pc2 = $db->query("SELECT (sum(importe) / (100 - 10)) * 100 AS dato, extract(day FROM fecha) AS dia FROM movimiento_gastos WHERE num_cia = $num_cia AND fecha BETWEEN '$fecha1' AND '$fecha2' AND codgastos = 159 AND captura = 'FALSE' GROUP BY fecha ORDER BY fecha");
	$pc1 = $db->query("SELECT sum(importe) AS dato, extract(day FROM fecha) AS dia FROM movimiento_gastos WHERE num_cia = $num_cia AND fecha BETWEEN '$fecha1' AND '$fecha2' AND codgastos = 152 AND captura = 'FALSE' GROUP BY fecha ORDER BY fecha");
	$prueba_pan_ant = $db->query("SELECT sum(importe) AS dato, CASE WHEN fecha < '$fecha1' THEN 0 ELSE extract(day from fecha) END AS dia FROM prueba_pan WHERE num_cia = $num_cia AND fecha BETWEEN '$fecha_historico' AND '$fecha2' GROUP BY fecha ORDER BY fecha");
	$vp = $db->query("SELECT sum(venta_puerta) AS dato, extract(day FROM fecha) AS dia FROM total_panaderias WHERE num_cia = $num_cia AND fecha BETWEEN '$fecha1' AND '$fecha2' GROUP BY fecha ORDER BY fecha");
	$reparto = $db->query("SELECT sum(pan_p_venta) AS dato, extract(day FROM fecha) AS dia FROM mov_expendios WHERE num_cia = $num_cia AND fecha BETWEEN '$fecha1' AND '$fecha2' GROUP BY fecha ORDER BY fecha");
	//$desc_pastel = $db->query("SELECT sum(desc_pastel) AS dato, extract(day FROM fecha) AS dia FROM captura_efectivos WHERE num_cia = $num_cia AND fecha BETWEEN '$fecha1' AND '$fecha2' GROUP BY extract(day FROM fecha) ORDER BY extract(day FROM fecha)");
	$prueba_pan = $db->query("SELECT sum(importe) AS dato, extract(day FROM fecha) AS dia FROM prueba_pan WHERE num_cia = $num_cia AND fecha BETWEEN '$fecha1' AND '$fecha2' GROUP BY fecha ORDER BY fecha");
	$ts_bal = mktime(0, 0, 0, $mes, 1, $anio);
	$ts_limit = mktime(0, 0, 0, 9, 1, 2006);
	for ($d = 1; $d <= $dias; $d++) {
		if (/*$anio >= 2006 && $mes >= 4*/$ts_bal > mktime(0, 0, 0, 4, 30, 2006))
			$total_pan = buscar_fal($pro, $d) + buscar_fal($pc, $d) + buscar_fal($prueba_pan_ant, $d - 1) + buscar_fal($pc1, $d) + buscar_fal($pc2, $d);
		else
			$total_pan = buscar_fal($pro, $d) + buscar_fal($pc, $d) + buscar_fal($prueba_pan_ant, $d - 1) + buscar_fal($pc1, $d);
		$sobrante = $total_pan - buscar_fal($vp, $d) - buscar_fal($reparto, $d) - buscar_fal($pro, $d) * (/*$_GET['mes'] >= 9 && $_GET['anio'] >= 2006*/$ts_bal >= $ts_limit ? 0 : 0.02);	// [03/Octubre/2006] Se quito el 2% de pan quebrado
		$faltante = buscar_fal($prueba_pan, $d) - $sobrante /*buscar_fal($desc_pastel, $d)*/;
		$faltante_pan += $faltante;
	}
	
	/**** DEVOLUCIONES ****/
	$sql = "SELECT sum(devolucion) FROM mov_expendios WHERE num_cia = $num_cia AND fecha BETWEEN '$fecha1' AND '$fecha2'";
	$devoluciones = $db->query($sql);
	
	/**** REZAGO INICIAL ****/
	$sql = "SELECT sum(rezago) FROM mov_expendios WHERE num_cia = $num_cia AND fecha = '$fecha_historico'";
	$rezago_inicial = $db->query($sql);
	
	/**** REZAGO FINAL ****/
	$sql = "SELECT sum(rezago) FROM mov_expendios WHERE num_cia = $num_cia AND fecha = '$fecha2'";
	$rezago_final = $db->query($sql);
	
	/**** CAMBIO REZAGO ****/
	$cambio_rezago = $rezago_final[0]['sum'] - $rezago_inicial[0]['sum'];
	
	/**** EFECTIVO ****/
	$sql = "SELECT sum(efectivo) FROM total_panaderias WHERE num_cia = $num_cia AND fecha BETWEEN '$fecha1' AND '$fecha2'";
	$efectivo = $db->query($sql);
	
	/*-------------------------------------------*/
	/*$fin = microtime_float();
	$tiempo = $fin - $inicio;
	echo "Tiempo de ejecución PRODUCCION: ".round($tiempo,3)." segundos<br>";*/
	/*-------------------------------------------*/
	
	/**** DATO HISTORICO DE CLIENTES ****/
	
	/*-------------------------------------------*/
	//$inicio = microtime_float();
	/*-------------------------------------------*/
	
	// Insertar o actualizar historico
	$sql = "SELECT sum(ctes) FROM captura_efectivos WHERE num_cia = $num_cia AND fecha BETWEEN '$fecha1' AND '$fecha2'";
	$temp = $db->query($sql);
	$clientes = ($temp[0]['sum'] > 0) ? $temp[0]['sum'] : "NULL";
	$prom = $clientes > 0 ? $venta_puerta[0]['sum'] / $clientes : "NULL";
	$por_efe = $produccion_total[0]['sum'] > 0 ? $efectivo[0]['sum'] / $produccion_total[0]['sum'] : "0";
	if ($id = $db->query("SELECT id FROM historico WHERE num_cia = $num_cia AND mes = $mes AND anio = $anio"))
		$sql = "UPDATE historico SET utilidad = $utilidad_neta, venta = " . ($venta_puerta[0]['sum'] - $errores[0]['sum']) . ", reparto = " . ($abono_reparto[0]['sum'] != 0 ? $abono_reparto[0]['sum'] : 0) . ", clientes = $clientes, gasto_ext = '" . ($ingresos_ext[0]['sum'] != 0 ? "TRUE" : "FALSE") . "', ingresos = " . ($ingresos_ext[0]['sum'] != 0 ? $ingresos_ext[0]['sum'] : 0) . ", por_efe = $por_efe WHERE id = {$id[0]['id']}";
	else
		$sql = "INSERT INTO historico (num_cia, mes, anio, utilidad, venta, reparto, clientes, gasto_ext, ingresos, por_efe) VALUES ($num_cia, $mes, $anio, $utilidad_neta, " . ($venta_puerta[0]['sum'] - $errores[0]['sum']) . ", " . ($abono_reparto[0]['sum'] != 0 ? $abono_reparto[0]['sum'] : 0) . ", $clientes, '" . ($ingresos_ext[0]['sum'] != 0 ? "TRUE" : "FALSE") . "', " . ($ingresos_ext[0]['sum'] != 0 ? $ingresos_ext[0]['sum'] : 0) . ", $por_efe)";
	$db->query($sql);
	
	$sql = "SELECT sum(ctes) FROM captura_efectivos WHERE num_cia = $num_cia AND fecha BETWEEN '$fecha1' AND '$fecha2'";
	$clientes = $db->query($sql);
	$prom = $clientes[0]['sum'] > 0 ? $venta_puerta[0]['sum'] / $clientes[0]['sum'] : 0;
	
	// ************* HOJA 1, SECCION 4 *************
	
	/**** M. PRIMA / VENTAS - PAN COMPRADO ****/
	@$mp_vtas = $mat_prima_utilizada / ($ventas_netas + $pan_comprado);
	
	// [13-Dic-2007] Obtener produccion del gelatinero para descontar al total de la producción
	$gel = $db->query("SELECT sum(total_produccion) AS gel FROM total_produccion WHERE numcia = $num_cia AND codturno = 9 AND fecha_total BETWEEN '$fecha1' AND '$fecha2'");
	
	/**** UTILIDAD / PRODUCCION ****/
	@$utilidad_produccion = $utilidad_neta / ($produccion_total[0]['sum'] - $gel[0]['gel']);
	
	/**** MATERIA PRIMA / PRODUCCION ****/
	@$mp_produccion = $mat_prima_utilizada / ($produccion_total[0]['sum'] - $gel[0]['gel']);
	
	/**** GAS / PRODUCCION ****/
	// Gastos de código 90 GAS
	$sql = "SELECT sum(importe) FROM catalogo_gastos JOIN movimiento_gastos USING (codgastos) WHERE num_cia = $num_cia AND codgastos = 90 AND fecha BETWEEN '$fecha1' AND '$fecha2' AND codigo_edo_resultados = 1";
	$gas = $db->query($sql);
	if ($gas[0]['sum'] == 0 || $gas[0]['sum'] == '') {
		// Gastos de código 128 GAS NATURAL
		$sql = "SELECT sum(importe) FROM catalogo_gastos JOIN movimiento_gastos USING (codgastos) WHERE num_cia = $num_cia AND codgastos = 128 AND fecha BETWEEN '$fecha1' AND '$fecha2' AND codigo_edo_resultados = 1";
		$gas = $db->query($sql);
		
		if ($gas[0]['sum'] != 0 || $gas[0]['sum'] != '') {
			// Buscar buscar hasta que mes anterior al actual se pago gas natural
			$sql = "SELECT fecha FROM catalogo_gastos JOIN movimiento_gastos USING (codgastos) WHERE num_cia = $num_cia AND codgastos = 128 AND fecha < '$fecha1' AND codigo_edo_resultados = 1 ORDER BY fecha DESC LIMIT 1";
			$tmp = $db->query($sql);
			
			ereg("([0-9]{1,2})/([0-9]{1,2})/([0-9]{2,4})", $tmp[0]['fecha'], $tmp);
			$fecha_tmp1 = date("d/m/Y", mktime(0, 0, 0, $tmp[2] + 1, 1, $tmp[3]));
			$fecha_tmp2 = date("d/m/Y", mktime(0, 0, 0, $mes, 0, $anio));
			
			// Obtener produccion de los meses que no se pago el gas
			$sql = "SELECT sum(total_produccion) FROM total_produccion WHERE numcia = $num_cia AND fecha_total BETWEEN '$fecha_tmp1' AND '$fecha_tmp2' AND codturno NOT IN (9)";
			$tmp = $db->query($sql);
			$pro_ant = $tmp[0]['sum'] > 0 ? $tmp[0]['sum'] : 0;
		}
	}
	else
		$pro_ant = 0;
	
	// Descuentos de gas
	$sql = "SELECT sum(importe) FROM gastos_caja WHERE num_cia = $num_cia AND cod_gastos = 92 AND fecha BETWEEN '$fecha1' AND '$fecha2' AND tipo_mov = 'TRUE'";
	$des_gas = $db->query($sql);
	
	@$gas_produccion = ($gas[0]['sum'] - $des_gas[0]['sum']) / ($produccion_total[0]['sum'] - $gel[0]['gel'] + $pro_ant);
	
	// Porcentaje gas / produccion del mes pasado
	$sql = "SELECT gas_pro FROM balances_pan WHERE num_cia = $num_cia AND mes = " .( $mes > 1 ? $mes - 1 : 12) . " AND anio = " . ($mes > 1 ? $anio : $anio - 1);
	$ult_gas_pro = $db->query($sql);
	
	/**** ENCARGADOS ****/
	$sql = "SELECT * FROM encargados WHERE num_cia = $num_cia AND mes = $mes AND anio = $anio";
	$encargado = $db->query($sql);
	
	/**** EMPLEADOS AFILIADOS AL IMSS (AGREGADO EL 23 DE NOVIEMBRE DE 2005) ****/
	if ($mes == date("n", mktime(0, 0, 0, date("n"), 0, date("Y"))) && $anio == date("Y", mktime(0, 0, 0, date("n"), 0, date("Y"))) && date("d") < 6) {
		$temp = $db->query("SELECT count(id) FROM catalogo_trabajadores WHERE num_cia = $num_cia AND num_afiliacion IS NOT NULL AND fecha_baja IS NULL");
		$emp_afi = $temp[0]['count'];
	}
	else {
		$temp = $db->query("SELECT emp_afi FROM balances_pan WHERE num_cia = $num_cia AND mes = $mes AND anio = $anio");
		$emp_afi = $temp ? $temp[0]['emp_afi'] : 0;
	}
	
	/*-------------------------------------------*/
	/*$fin = microtime_float();
	$tiempo = $fin - $inicio;
	echo "Tiempo de ejecución PORCENTAJES: ".round($tiempo,3)." segundos<br>";*/
	/*-------------------------------------------*/
	
	/**** HISTORICO DE BALANCES ANTERIORES ****/
	$historico_actual = $db->query("SELECT * FROM historico WHERE num_cia = $num_cia AND mes <= $mes AND anio = $anio ORDER BY mes");
	$historico_anterior = $db->query("SELECT * FROM historico WHERE num_cia = $num_cia AND anio = " . ($anio - 1) . " ORDER BY mes");
	
	$historico = $db->query("SELECT utilidad, ingresos FROM historico WHERE num_cia = $num_cia AND anio = " . ($anio - 1) . " AND mes = $mes");
	$utilidad_anterior = $historico ? $historico[0]['utilidad'] - $historico[0]['ingresos'] - ($anio - 1 == 2004 ? ($num_cia == 44 ? 25000 : ($num_cia == 144 ? 8000 : ($num_cia == 40 ? 5000 : ($num_cia == 131 ? 2000 : ($num_cia == 16 ? 15000 : ($num_cia == 154 ? 10000 : ($num_cia == 41 ? 40000 : ($num_cia == 173 ? 15000 : 0)))))))) : 0) : 0;
	
	@$p_utilidad_neta = $utilidad_neta - $utilidad_anterior - $ingresos_ext[0]['sum'] != 0 ? abs((($utilidad_neta - $ingresos_ext[0]['sum']) * 100 / $utilidad_anterior) - 100) : 0;
	$m_utilidad_neta = $utilidad_neta - $utilidad_anterior - $ingresos_ext[0]['sum'] > 0 ? '<span style="color:#0000CC"> ' . number_format($p_utilidad_neta, 2) . '%</span>' : ($utilidad_neta - $utilidad_anterior - $ingresos_ext[0]['sum'] < 0 ? '<span style="color:#CC0000"> ' . number_format($p_utilidad_neta, 2) . '%</span>' : '');
	
	/**** ALMACENAR DATOS EN EL HISTORICO DE BALANCES ****/
	$bal['num_cia'] = $num_cia;
	$bal['mes'] = $mes;
	$bal['anio'] = $anio;
	$bal['venta_puerta'] = $venta_puerta[0]['sum'] != 0 ? $venta_puerta[0]['sum'] : "0";
	$bal['bases'] = "0";
	$bal['barredura'] = $barredura[0]['sum'] != 0 ? $barredura[0]['sum'] : "0";
	$bal['pastillaje'] = $pastillaje[0]['sum'] != 0 ? $pastillaje[0]['sum'] : "0";
	$bal['abono_emp'] = $abono_empleados[0]['sum'] != 0 ? $abono_empleados[0]['sum'] : "0";
	$bal['otros'] = $otros[0]['sum'] != 0 ? $otros[0]['sum'] : "0";
	$bal['total_otros'] = $total_otros != 0 ? $total_otros : "0";
	$bal['abono_reparto'] = $abono_reparto[0]['sum'] != 0 ? $abono_reparto[0]['sum'] : "0";
	$bal['errores'] = $errores[0]['sum'] != 0 ? $errores[0]['sum'] : "0";
	$bal['ventas_netas'] = $ventas_netas != 0 ? $ventas_netas : "0";
	$bal['inv_ant'] = $inv_ant[0]['sum'] != 0 ? $inv_ant[0]['sum'] : "0";
	$bal['compras'] = $compras != 0 ? $compras : "0";
	$bal['mercancias'] = $mercancias != 0 ? $mercancias : "0";
	$bal['inv_act'] = $inv_act != 0 ? $inv_act : "0";
	$bal['mat_prima_utilizada'] = $mat_prima_utilizada != 0 ? $mat_prima_utilizada : "0";
	$bal['mano_obra'] = $mano_obra[0]['sum'] != 0 ? $mano_obra[0]['sum'] : "0";
	$bal['panaderos'] = $panaderos[0]['sum'] != 0 ? $panaderos[0]['sum'] : "0";
	$bal['gastos_fab'] = $gastos_fab[0]['sum'] != 0 ? $gastos_fab[0]['sum'] : "0";
	$bal['costo_produccion'] = $costo_produccion != 0 ? $costo_produccion : "0";
	$bal['utilidad_bruta'] = $utilidad_bruta != 0 ? $utilidad_bruta : "0";
	$bal['pan_comprado'] = $pan_comprado != 0 ? $pan_comprado : "0";
	$bal['gastos_generales'] = $gastos_gral[0]['sum'] != 0 ? $gastos_gral[0]['sum'] : "0";
	$bal['gastos_caja'] = $gastos_caja != 0 ? $gastos_caja : "0";
	$bal['reserva_aguinaldos'] = $reservas[0]['sum'] != 0 ? $reservas[0]['sum'] : "0";
	$bal['gastos_otras_cias'] = $gastos_otros != 0 ? $gastos_otros : "0";
	$bal['total_gastos'] = $gastos_totales != 0 ? $gastos_totales : "0";
	$bal['ingresos_ext'] = $ingresos_ext[0]['sum'] != 0 ? $ingresos_ext[0]['sum'] : "0";
	$bal['utilidad_neta'] = $utilidad_neta != 0 ? $utilidad_neta : "0";
	$bal['mp_vtas'] = $mp_vtas != 0 ? $mp_vtas : "0";
	$bal['utilidad_pro'] = $utilidad_produccion != 0 ? $utilidad_produccion : "0";
	$bal['mp_pro'] = $mp_produccion != 0 ? $mp_produccion : "0";
	$bal['produccion_total'] = $produccion_total[0]['sum'] != 0 ? $produccion_total[0]['sum'] : "0";
	$bal['faltante_pan'] = $faltante_pan != 0 ? $faltante_pan : "0";
	$bal['rezago_ini'] = $rezago_inicial[0]['sum'] != 0 ? $rezago_inicial[0]['sum'] : "0";
	$bal['rezago_fin'] = $rezago_final[0]['sum'] != 0 ? $rezago_final[0]['sum'] : "0";
	$bal['var_rezago'] = $cambio_rezago != 0 ? $cambio_rezago : "0";
	$bal['efectivo'] = $efectivo[0]['sum'] != 0 ? $efectivo[0]['sum'] : "0";
	$bal['gas_pro'] = $gas_produccion != 0 ? $gas_produccion : "0";
	$bal['pagos_anticipados'] = $pagos_anticipados[0]['sum'] != 0 ? $pagos_anticipados[0]['sum'] : "0";
	$bal['emp_afi'] = $emp_afi;
	
	if (empty($_GET['no_gastos'])) {
		if ($id = $db->query("SELECT id FROM balances_pan WHERE num_cia = $num_cia AND mes = $mes AND anio = $anio"))
			$db->query($db->preparar_update("balances_pan", $bal, "id = {$id[0]['id']}"));
		else
			$db->query($db->preparar_insert("balances_pan", $bal));
	}
	
	// ************* MOSTRAR EN PANTALLA LA HOJA 1 *************
	$tpl->newBlock("reporte");
	
	/**** ENCABEZADO ****/
	$tpl->assign("num_cia", $num_cia);
	$tpl->assign("nombre_cia", $cia[$f]['nombre']);
	$tpl->assign("nombre_corto", $cia[$f]['nombre_corto']);
	$tpl->assign("anio", $anio);
	$tpl->assign("mes", mes_escrito($mes));
	
	/**** PRIMERA SECCION ****/
	$tpl->assign("venta_puerta", $venta_puerta[0]['sum'] != 0 ? "<font color=\"#" . ($venta_puerta[0]['sum'] > 0 ? "0000FF" : "FF0000") . "\">" . number_format($venta_puerta[0]['sum'], 2, ".", ",") . "</font>" : "&nbsp;");
	$tpl->assign('m_venta_puerta', $m_venta_puerta);
	$tpl->assign("p_venta_puerta", $venta_puerta[0]['sum'] != 0 ? "<font color=\"#FF9900\">(" . number_format($venta_puerta[0]['sum'] * 100 / $ventas_netas, 2, ".", ",") . "%)</font>" : "");
	$tpl->assign("barredura", $barredura[0]['sum'] != 0 ? "<font color=\"#" . ($barredura[0]['sum'] > 0 ? "0000FF" : "FF0000") . "\">" . number_format($barredura[0]['sum'], 2, ".", ",") . "</font>" : "&nbsp;");
	$tpl->assign("pastillaje", $pastillaje[0]['sum'] != 0 ? "<font color=\"#" . ($pastillaje[0]['sum'] > 0 ? "0000FF" : "FF0000") . "\">" . number_format($pastillaje[0]['sum'], 2, ".", ",") . "</font>" : "&nbsp;");
	$tpl->assign("abono_emp", $abono_empleados[0]['sum'] != 0 ? "<font color=\"#" . ($abono_empleados[0]['sum'] > 0 ? "0000FF" : "FF0000") . "\">" . number_format($abono_empleados[0]['sum'], 2, ".", ",") . "</font>" : "&nbsp;");
	$tpl->assign("otros", $otros[0]['sum'] != 0 ? "<font color=\"#" . ($otros[0]['sum'] > 0 ? "0000FF" : "FF0000") . "\">" . number_format($otros[0]['sum'], 2, ".", ",") . "</font>" : "&nbsp;");
	$tpl->assign("total_otros", $total_otros != 0 ? "<font color=\"#" . ($total_otros > 0 ? "0000FF" : "FF0000") . "\">" . number_format($total_otros, 2, ".", ",") . "</font>" : "&nbsp;");
	$tpl->assign("abono_reparto", $abono_reparto[0]['sum'] != 0 ? "<font color=\"#" . ($abono_reparto[0]['sum'] > 0 ? "0000FF" : "FF0000") . "\">" . number_format($abono_reparto[0]['sum'], 2, ".", ",") . "</font>" : "&nbsp;");
	$tpl->assign('m_abono_reparto', $m_abono_reparto);
	$tpl->assign("p_abono_reparto", $abono_reparto[0]['sum'] != 0 ? "<font color=\"#FF9900\">(" . number_format($abono_reparto[0]['sum'] * 100 / $ventas_netas, 2, ".", ",") . "%)</font>" : "");
	$tpl->assign("errores", $errores[0]['sum'] != 0 ? "<font color=\"#" . ($errores[0]['sum'] > 0 ? "0000FF" : "FF0000") . "\">" . number_format($errores[0]['sum'], 2, ".", ",") . "</font>" : "&nbsp;");
	$tpl->assign("ventas_netas", $ventas_netas != 0 ? "<font color=\"#" . ($ventas_netas > 0 ? "0000FF" : "FF0000") . "\">" . number_format($ventas_netas, 2, ".", ",") . "</font>" : "&nbsp;");
	$tpl->assign('m_ventas_netas', $m_ventas_netas);
	
	/**** SEGUNDA SECCION ****/
	$tpl->assign("inventario_anterior", $inv_ant[0]['sum'] != 0 ? "<font color=\"#" . ($inv_ant[0]['sum'] > 0 ? "0000FF" : "FF0000") . "\">" . number_format($inv_ant[0]['sum'], 2, ".", ",") . "</font>" : "&nbsp;");
	$tpl->assign("compras", $compras != 0 ? "<font color=\"#" . ($compras > 0 ? "0000FF" : "FF0000") . "\">" . number_format($compras, 2, ".", ",") . "</font>" : "&nbsp;");
	$tpl->assign("mercancias", $mercancias != 0 ? "<font color=\"#" . ($mercancias > 0 ? "0000FF" : "FF0000") . "\">" . number_format($mercancias, 2, ".", ",") . "</font>" : "&nbsp;");
	$tpl->assign("inventario_actual", $inv_act != 0 ? "<font color=\"#" . ($inv_act > 0 ? "0000FF" : "FF0000") . "\">" . number_format($inv_act, 2, ".", ",") . "</font>" : "&nbsp;");
	$tpl->assign("mat_prima_utilizada", $mat_prima_utilizada != 0 ? "<font color=\"#" . ($mat_prima_utilizada > 0 ? "0000FF" : "FF0000") . "\">" . number_format($mat_prima_utilizada, 2, ".", ",") . "</font>" : "&nbsp;");
	$tpl->assign("mano_obra", $mano_obra[0]['sum'] != 0 ? "<font color=\"#" . ($mano_obra[0]['sum'] > 0 ? "0000FF" : "FF0000") . "\">" . number_format($mano_obra[0]['sum'], 2, ".", ",") . "</font>" : "&nbsp;");
	$tpl->assign("panaderos", $panaderos[0]['sum'] != 0 ? "<font color=\"#" . ($panaderos[0]['sum'] > 0 ? "0000FF" : "FF0000") . "\">" . number_format($panaderos[0]['sum'], 2, ".", ",") . "</font>" : "&nbsp;");
	$tpl->assign("gastos_fabricacion", $gastos_fab[0]['sum'] != 0 ? "<font color=\"#" . ($gastos_fab[0]['sum'] > 0 ? "0000FF" : "FF0000") . "\">" . number_format($gastos_fab[0]['sum'], 2, ".", ",") . "</font>" : "&nbsp;");
	$tpl->assign("costo_produccion", $costo_produccion != 0 ? "<font color=\"#" . ($costo_produccion > 0 ? "0000FF" : "FF0000") . "\">" . number_format($costo_produccion, 2, ".", ",") . "</font>" : "&nbsp;");
	$tpl->assign("utilidad_bruta", $utilidad_bruta != 0 ? "<font color=\"#" . ($utilidad_bruta > 0 ? "0000FF" : "FF0000") . "\">" . number_format($utilidad_bruta, 2, ".", ",") . "</font>" : "&nbsp;");
	
	/**** TERCERA SECCION ****/
	$tpl->assign("pan_comprado", $pan_comprado != 0 ? "<font color=\"#" . ($pan_comprado > 0 ? "0000FF" : "FF0000") . "\">" . number_format($pan_comprado, 2, ".", ",") . "</font>" : "&nbsp;");
	$tpl->assign("gastos_generales", $gastos_gral[0]['sum'] != 0 ? "<font color=\"#" . ($gastos_gral[0]['sum'] > 0 ? "0000FF" : "FF0000") . "\">" . number_format($gastos_gral[0]['sum'], 2, ".", ",") . "</font>" : "&nbsp;");
	$tpl->assign("gastos_caja", $gastos_caja != 0 ? "<font color=\"#" . ($gastos_caja > 0 ? "0000FF" : "FF0000") . "\">" . number_format($gastos_caja, 2, ".", ",") . "</font>" : "&nbsp;");
	$tpl->assign("comisiones", $comisiones != 0 ? "<font color=\"#" . ($comisiones > 0 ? "0000FF" : "FF0000") . "\">" . number_format($comisiones, 2, ".", ",") . "</font>" : "&nbsp;");
	$tpl->assign("reserva_aguinaldos", $reservas[0]['sum'] != 0 ? "<font color=\"#" . ($reservas[0]['sum'] > 0 ? "0000FF" : "FF0000") . "\">" . number_format($reservas[0]['sum'], 2, ".", ",") . "</font>" : "&nbsp;");
	$tpl->assign("pagos_anticipados", $pagos_anticipados[0]['sum'] != 0 ? "<font color=\"#" . ($pagos_anticipados[0]['sum'] > 0 ? "0000FF" : "FF0000") . "\">" . number_format($pagos_anticipados[0]['sum'], 2, ".", ",") . "</font>" : "&nbsp;");
	$tpl->assign("gastos_otras_cias", $gastos_otros != 0 ? "<font color=\"#" . ($gastos_otros > 0 ? "0000FF" : "FF0000") . "\">" . number_format($gastos_otros, 2, ".", ",") . "</font>" : "&nbsp;");
	$tpl->assign("total_gastos", $gastos_totales != 0 ? "<font color=\"#" . ($gastos_totales > 0 ? "0000FF" : "FF0000") . "\">" . number_format($gastos_totales, 2, ".", ",") . "</font>" : "&nbsp;");
	$tpl->assign("ingresos_ext", $ingresos_ext[0]['sum'] != 0 ? "<font color=\"#" . ($ingresos_ext[0]['sum'] > 0 ? "0000FF" : "FF0000") . "\">" . number_format($ingresos_ext[0]['sum'], 2, ".", ",") . "</font>" : "&nbsp;");
	$tpl->assign("utilidad_mes", $utilidad_neta != 0 ? "<font color=\"#" . ($utilidad_neta > 0 ? "0000FF" : "FF0000") . "\">" . number_format($utilidad_neta, 2, ".", ",") . "</font>" . ($utilidad_neta - $utilidad_anterior - $ingresos_ext[0]['sum'] != 0 && $utilidad_anterior != 0 ? $utilidad_neta - $utilidad_anterior - $ingresos_ext[0]['sum'] > 0 ? "&nbsp;&nbsp;MEJORO" : "&nbsp;&nbsp;EMPEORO" : "") : "&nbsp;");
	$tpl->assign('m_utilidad_neta', $m_utilidad_neta);
	
	/**** CUARTO SECCION ****/
	$tpl->assign("mp_vtas", $mp_vtas != 0 ? "<font color=\"#" . ($mp_vtas > 0 ? "0000FF" : "FF0000") . "\">" . number_format($mp_vtas, 3, ".", ",") . "</font>" : "&nbsp;");
	$tpl->assign("utilidad_produccion", $utilidad_produccion != 0 ? "<font color=\"#" . ($utilidad_produccion > 0 ? "0000FF" : "FF0000") . "\">" . number_format($utilidad_produccion, 3, ".", ",") . "</font>" : "&nbsp;");
	$tpl->assign("mp_produccion", $mp_produccion != 0 ? "<font color=\"#" . ($mp_produccion > 0 ? "0000FF" : "FF0000") . "\">" . number_format($mp_produccion, 3, ".", ",") . "</font>" : "&nbsp;");
	$tpl->assign("gas_produccion", $gas_produccion != 0 ? "<font color=\"#" . ($gas_produccion > 0 ? "0000FF" : "FF0000") . "\">" . number_format($gas_produccion, 5, ".", ",") . "</font>" : "&nbsp;");
	$tpl->assign("dif_mes_gas", $ult_gas_pro ? $gas_produccion - $ult_gas_pro[0]['gas_pro'] != 0 ? "<font color=\"#" . ($gas_produccion - $ult_gas_pro[0]['gas_pro'] > 0 ? "FF0000\">Subio" : "0000FF\">Bajo") . "</font>" : "=" : "");
	foreach ($pro_tur as $key => $value)
		if ($value > 0 && $key <= 9)
			$tpl->assign("con_pro_" . $key, $value != 0 ? number_format($consumo[$key] / $value, 3, ".", ",") : "&nbsp;");
	// [03/Nov/2006] Produccion por turno
	$pro_tur = $db->query("SELECT codturno, sum(total_produccion) AS pro FROM total_produccion WHERE numcia = $num_cia AND codturno IN (1, 2, 3, 4, 8, 9) AND fecha_total BETWEEN '$fecha1' AND '$fecha2' GROUP BY codturno ORDER BY codturno");
	if ($pro_tur)
		foreach ($pro_tur as $reg)
			$tpl->assign("pro_$reg[codturno]", $reg['pro'] != 0 ? number_format($reg['pro'], 0, ".", ",") : '&nbsp;');
	$tpl->assign("inicio", $encargado[0]['nombre_inicio']);
	$tpl->assign("termino", $encargado[0]['nombre_fin']);
	
	/**** SUB-SECCION RESERVAS ****/
	/*-------------------------------------------*/
	//$inicio = microtime_float();
	/*-------------------------------------------*/
	
	$sql = "SELECT cod_reserva, descripcion, importe, codgastos FROM reservas_cias LEFT JOIN catalogo_reservas ON (tipo_res = cod_reserva) WHERE num_cia = $num_cia AND fecha = '$fecha1' ORDER BY cod_reserva";
	$result = $db->query($sql);
	
	if ($result) {
		// Crear títulos
		$tpl->assign("titulo_reserva", "Reserva");
		$tpl->assign("titulo_importe", "Importe");
		// Si el año es 2005, crear titulos de pagado y diferencia
		if ($anio == 2005) {
			$tpl->assign("titulo_pagado", "Pagado");
			$tpl->assign("titulo_diferencia", "Diferencia");
		}
		
		$total_result = 0;
		$total_pagado = 0;
		$total_diferencia = 0;
		$diferencia = 0;
		
		$imss_infonavit = FALSE;
		for ($r = 0; $r < count($result); $r++) {
			$tpl->assign("nombre_reserva" . ($r + 1), $result[$r]['descripcion']);
			$tpl->assign("importe_reserva" . ($r + 1), number_format($result[$r]['importe'], 2, ".", ","));
			$total_result += $result[$r]['importe'];
			
			if ($result[$r]['cod_reserva'] == 4) $imss_infonavit = TRUE;
			
			// Si el año es 2005, buscar importes pagados de este mes para el homologo en gastos
			if ($anio == 2005 && $result[$r]['codgastos'] > 0) {
				$sql = "SELECT sum(importe) AS pagado FROM movimiento_gastos WHERE num_cia = $num_cia AND codgastos = {$result[$r]['codgastos']} AND fecha BETWEEN '$fecha1' AND '$fecha2'";
				$pagado = $db->query($sql);
				
				if ($pagado[0]['pagado'] != 0) {
					// Calcular diferencia
					$diferencia = $result[$r]['importe'] - $pagado[0]['pagado'];
					$tpl->assign("pagado" . ($r + 1), number_format($pagado[0]['pagado'], 2, ".", ","));
					$tpl->assign("diferencia" . ($r + 1), number_format($diferencia, 2, ".", ","));
					
					$total_pagado += $pagado[0]['pagado'];
					$total_diferencia += $diferencia;
				}
			}
		}
		$tpl->assign("nombre_reserva" . ($r + 1), "<strong>Total</strong>");
		$tpl->assign("importe_reserva" . ($r + 1), "<strong>" . number_format($total_result, 2, ".", ",") . "</strong>");
		$tpl->assign("pagado".($r+1),$total_pagado != 0 ? "<strong>" . number_format($total_pagado,2,".",",") . "</strong>" : "");
		$tpl->assign("diferencia" . ($r + 1), $total_diferencia != 0 ? "<strong>" . number_format($total_diferencia, 2, ".", ",") . "</strong>" : "");
		if ($diferencia != 0 && $anio == 2005) {
			if ($utilidad_neta > 0)
				$tpl->assign("utilidad_mes","<font color='#0000FF'>".number_format($utilidad_neta,2,".",",")."</font>&nbsp;&nbsp;".(($utilidad_neta-$utilidad_anterior-$ingresos_ext[0]['sum']+$diferencia == 0)?"":(($utilidad_neta-$utilidad_anterior-$ingresos_ext[0]['sum']+$diferencia > 0)?"MEJORO":"EMPEORO")));
			else
				$tpl->assign("utilidad_mes","<font color='#FF0000'>".number_format($utilidad_neta,2,".",",")."</font>&nbsp;&nbsp;".(($utilidad_neta-$utilidad_anterior-$ingresos_ext[0]['sum']+$diferencia == 0)?"":(($utilidad_neta-$utilidad_anterior-$ingresos_ext[0]['sum']+$diferencia > 0)?"MEJORO":"EMPEORO")));
		}
		
		// Obtener cuantos empleados de la compañía estan afiliados al IMSS (Agregado el 23)
		if ($r < 9 && $imss_infonavit && mktime(0, 0, 0, $mes, 1, $anio) >= mktime(0, 0, 0, 11, 1, 2005)) {
			$tpl->assign("nombre_reserva" . ($r + 2), "<strong>Empl. Aseg.</strong>");
			$tpl->assign("importe_reserva" . ($r + 2), $emp_afi);
			//$tpl->assign("pagado" . ($r + 2), "Empl. Afi.");
		}
	}
	/*-------------------------------------------*/
	/*$fin = microtime_float();
	$tiempo = $fin - $inicio;
	echo "Tiempo de ejecución RESERVAS: ".round($tiempo,3)." segundos<br>";*/
	/*-------------------------------------------*/
	
	/*****************************/
	
	/**** QUINTA SECCION ****/
	$tpl->assign("produccion_total", $produccion_total[0]['sum'] != 0 ? "<font color=\"#" . ($produccion_total[0]['sum'] > 0 ? "0000FF" : "FF0000") . "\">" . number_format($produccion_total[0]['sum'], 2, ".", ",") . "</font>" : "&nbsp;");
	$tpl->assign("ganancia", $ganancia[0]['sum'] != 0 ? "<font color=\"#" . ($ganancia[0]['sum'] > 0 ? "0000FF" : "FF0000") . "\">" . number_format($ganancia[0]['sum'], 2, ".", ",") . "</font>" : "&nbsp;");
	$tpl->assign("porc_ganancia", $porc_ganancia[0]['sum'] != 0 ? "<font color=\"#" . ($porc_ganancia[0]['sum'] > 0 ? "0000FF" : "FF0000") . "\">" . number_format($porc_ganancia[0]['sum'], 2, ".", ",") . "</font>" : "&nbsp;");
	$tpl->assign("faltante_pan", $faltante_pan != 0 ? "<font color=\"#" . ($faltante_pan > 0 ? "0000FF" : "FF0000") . "\">" . number_format($faltante_pan, 2, ".", ",") . "</font>" : "&nbsp;");
	$tpl->assign("devoluciones", $devoluciones[0]['sum'] != 0 ? "<font color=\"#" . ($devoluciones[0]['sum'] > 0 ? "0000FF" : "FF0000") . "\">" . number_format($devoluciones[0]['sum'], 2, ".", ",") . "</font>" : "&nbsp;");
	$tpl->assign("rezago_inicial", $rezago_inicial[0]['sum'] != 0 ? "<font color=\"#" . ($rezago_inicial[0]['sum'] > 0 ? "0000FF" : "FF0000") . "\">" . number_format($rezago_inicial[0]['sum'], 2, ".", ",") . "</font>" : "&nbsp;");
	$tpl->assign("rezago_final", $rezago_final[0]['sum'] != 0 ? "<font color=\"#" . ($rezago_final[0]['sum'] > 0 ? "0000FF" : "FF0000") . "\">" . number_format($rezago_final[0]['sum'], 2, ".", ",") . "</font>" : "&nbsp;");
	$tpl->assign("cambio", $cambio_rezago != 0 ? $cambio_rezago > 0 ? "Subio" : "Bajo" : "&nbsp;");
	$tpl->assign("cambio_rezago", $cambio_rezago != 0 ? "<font color=\"#" . ($cambio_rezago > 0 ? "0000FF" : "FF0000") . "\">" . number_format($cambio_rezago, 2, ".", ",") . "</font>" : "&nbsp;");
	$tpl->assign("efectivo", $efectivo[0]['sum'] != 0 ? "<font color=\"#" . ($efectivo[0]['sum'] > 0 ? "0000FF" : "FF0000") . "\">" . number_format($efectivo[0]['sum'], 2, ".", ",") . "</font>" : "&nbsp;");
	$tpl->assign("utilidad_anio_ant", "<font color='#" . (($utilidad_anterior > 0) ? "0000FF" : "FF0000") . "'>" . number_format($utilidad_anterior, 2, ".", ",") . "</font>");
	$tpl->assign('utilidad_mes_value', number_format($utilidad_neta, 2));
	
	/**** SEXTA SECCION ****/
	
	/**** UTILIDAD AÑO ANTERIOR ****/
	$vta = 0;
	$abono = 0;
	$clientes = 0;
	for ($h = 0; $h < count($historico_anterior); $h++) {
		$tpl->assign("tant_" . $historico_anterior[$h]['mes'], mes_abreviado($historico_anterior[$h]['mes']));
		// MOD. 13/09/2005. LAS SIGUIENTES COMPAÑÏAS, EN EL AÑO 2004, SE LES RESTA LAS SIGUIENTES CANTIDADES:
		// 44  HORNO        - 25,000.00
		// 144 HORNO        - 8,000.00
		// 40  CARRASCO     - 5,000.00
		// 131 CARRASCO     - 2,000.00
		// 16  CANTERA      - 15,000.00
		// 154 CANTERA      - 10,000.00
		// 41  LA JOYA      - 40,000.00
		// 173 LA JOYA      - 15,000.00
		$tpl->assign("ant_" . $historico_anterior[$h]['mes'], "<font color='#0000FF'>" . number_format($historico_anterior[$h]['utilidad'] - (isset($_GET['no_gastos']) ? $historico_anterior[$h]['ingresos'] : 0) - ($anio - 1 == 2004 ? ($num_cia == 44 ? 25000 : ($num_cia == 144 ? 8000 : ($num_cia == 40 ? 5000 : ($num_cia == 131 ? 2000 : ($num_cia == 16 ? 15000 : ($num_cia == 154 ? 10000 : ($num_cia == 41 ? 40000 : ($num_cia == 173 ? 15000 : 0)))))))) : 0), 2, ".", ",") . "</font>" . (($historico_anterior[$h]['ingresos'] != 0 && empty($_GET['no_gastos'])) ? " <font color='#FF0000'>(" . number_format($historico_anterior[$h]['ingresos'], 2, ".", ",") . ")</font>" : ""));
		$tpl->assign("vta_ant_" . $historico_anterior[$h]['mes'], number_format($historico_anterior[$h]['venta'], 2, ".", ","));
		$tpl->assign("abono_ant_" . $historico_anterior[$h]['mes'], number_format($historico_anterior[$h]['reparto'], 2, ".", ","));
		$tpl->assign("clientes_ant_" . $historico_anterior[$h]['mes'], number_format($historico_anterior[$h]['clientes'], 2, ".", ","));
		$tpl->assign("prom_ant_" . $historico_anterior[$h]['mes'], $historico_anterior[$h]['clientes'] != 0 ? number_format($historico_anterior[$h]['venta'] / $historico_anterior[$h]['clientes'], 2, ".", ",") : "&nbsp;");
		
		$vta += $historico_anterior[$h]['venta'];
		$abono += $historico_anterior[$h]['reparto'];
		$clientes += $historico_anterior[$h]['clientes'];
	}
	$tpl->assign("tot_vta_ant", $vta > 0 ? number_format($vta, 2, ".", ",") : "");
	$tpl->assign("tot_abono_ant", $abono > 0 ? number_format($abono, 2, ".", ",") : "");
	$tpl->assign("tot_clientes_ant", $clientes > 0 ? number_format($clientes, 2, ".", ",") : "");
	
	/**** UTILIDAD AÑO ACTUAL ****/
	$vta = 0;
	$abono = 0;
	$clientes = 0;	
	for ($h=0; $h<count($historico_actual); $h++) {
		$tpl->assign("tact_" . $historico_actual[$h]['mes'], mes_abreviado($historico_actual[$h]['mes']));
		$tpl->assign("act_" . $historico_actual[$h]['mes'], "<font color='#0000FF'>" . number_format($historico_actual[$h]['utilidad'] - (isset($_GET['no_gastos']) ? $historico_actual[$h]['ingresos'] : 0), 2, ".", ",") . "</font>" . (($historico_actual[$h]['ingresos'] != 0 && empty($_GET['no_gastos'])) ? " <font color='#FF0000'>(" . number_format($historico_actual[$h]['ingresos'], 2, ".", ",").")</font>" : ""));
		$tpl->assign("vta_" . $historico_actual[$h]['mes'], number_format($historico_actual[$h]['venta'], 2, ".", ","));
		$tpl->assign("abono_" . $historico_actual[$h]['mes'], number_format($historico_actual[$h]['reparto'], 2, ".", ","));
		$tpl->assign("por_efe_" . $historico_actual[$h]['mes'], number_format($historico_actual[$h]['por_efe'], 2, ".", ","));
		$tpl->assign("clientes_" . $historico_actual[$h]['mes'], number_format($historico_actual[$h]['clientes'], 2, ".", ","));
		$tpl->assign("prom_" . $historico_actual[$h]['mes'], $historico_actual[$h]['clientes'] != 0 ? number_format($historico_actual[$h]['venta'] / $historico_actual[$h]['clientes'], 2, ".", ",") : "&nbsp;");
		
		$vta += $historico_actual[$h]['venta'];
		$abono += $historico_actual[$h]['reparto'];
		$clientes += $historico_actual[$h]['clientes'];
	}
	$tpl->assign("tot_vta", $vta > 0 ? number_format($vta, 2, ".", ",") : "");
	$tpl->assign("tot_abono", $abono > 0 ? number_format($abono, 2, ".", ",") : "");
	$tpl->assign("tot_clientes", $clientes > 0 ? number_format($clientes, 2, ".", ",") : "");
	
	/*-------------------------------------------------*/
	// Prueba Efectivo [04-10-2006]
	/*-------------------------------------------------*/
	/*$gastos_cap = $db->query("SELECT sum(importe) FROM movimiento_gastos WHERE num_cia = $num_cia AND fecha BETWEEN '$fecha1' AND '$fecha2' AND captura = 'FALSE'");
	$raya_pagada = $db->query("SELECT sum(raya_pagada) FROM total_panaderias WHERE num_cia = $num_cia AND fecha BETWEEN '$fecha1' AND '$fecha2'");
	$tpl->assign("pe", number_format($ventas_netas - $gastos_cap[0]['sum'] - $efectivo[0]['sum'] - $raya_pagada[0]['sum'], 2, ".", ","));*/
	
	// *********************************************************
	
	// ************* HOJA 2 RELACION DE GASTOS EXTRAS *************
	/*-------------------------------------------*/
	//$inicio = microtime_float();
	/*-------------------------------------------*/
	
	$tpl->newBlock("gastos_extras");
	$tpl->assign("num_cia", $num_cia);
	$tpl->assign("nombre_cia", $cia[$f]['nombre']);
	$tpl->assign("dia", $dias);
	$tpl->assign("anio", $anio);
	$tpl->assign("mes", mes_escrito($mes));
	
	// Fechas del mes anterior
	$fecha1_ant = date("d/m/Y", mktime(0, 0, 0, $mes - 1, 1, $anio));
	$fecha2_ant = date("d/m/Y", mktime(0, 0, 0, $mes, 0, $anio));
	$fecha1_anio_ant = date("d/m/Y", mktime(0, 0, 0, $mes, 1, $anio - 1));
	$fecha2_anio_ant = date("d/m/Y", mktime(0, 0, 0, $mes + 1, 0, $anio - 1));
	
	$mes_ant = date("n", mktime(0, 0, 0, $mes, 0, $anio));
	$anio_ant = date("Y", mktime(0, 0, 0, $mes, 0, $anio));
	$anio_anio_ant = $anio - 1;
	
	$sql = "SELECT codgastos, descripcion, codigo_edo_resultados AS tipo, extract(month FROM fecha) AS mes, extract(year FROM fecha) AS anio, sum(importe) AS importe FROM movimiento_gastos";
	$sql .= " LEFT JOIN catalogo_gastos USING (codgastos) WHERE num_cia = $num_cia AND (fecha BETWEEN '$fecha1_ant' AND '$fecha2' OR fecha BETWEEN '$fecha1_anio_ant' AND '$fecha2_anio_ant')";
	$sql .= " AND codigo_edo_resultados IN (1, 2) AND codgastos NOT IN (141) AND (importe >= 0.01 OR importe <= -0.01)";
	// [26/Oct/2006] Ya no aplicara el código 140 IMPUESTOS, en su lugar entreran los códigos 179, 180, 181, 182, 183 || [03/Nov/2006] Comentado por correcion mas abajo
	//$sql .= mktime(0, 0, 0, $mes, 1, $anio) > mktime(0, 0, 0, 9, 1, 2006) ? " AND codgastos NOT IN (140)" : "";
	//**********************************************************************************************************************
	$sql .= " GROUP BY codgastos, descripcion, codigo_edo_resultados, extract(month FROM fecha), extract(year FROM fecha)";
	$sql .= " ORDER BY codigo_edo_resultados, codgastos, extract(year FROM fecha) DESC, extract(month FROM fecha) DESC";
	$result = $db->query($sql);
	
	$lineas_gastos = 11;
	
	$total_importe = 0;
	$total_mes_ant = 0;
	$total_anio_ant = 0;
	
	$tipo = NULL;
	$codtmp = NULL;
	foreach ($result as $cod) {
		if ($tipo != $cod['tipo']) {
			if ($tipo != NULL) {
				$tpl->assign("tipo_gasto.importe", number_format($imp, 2, ".", ","));
				$tpl->assign("tipo_gasto.mes_ant", number_format($mes_par, 2, ".", ","));
				$tpl->assign("tipo_gasto.anio_ant", number_format($anio_par, 2, ".", ","));
			}
			
			$tipo = $cod['tipo'];
			$nombre_tipo = $tipo == 1 ? "GASTOS DE OPERACI&Oacute;N" : "GASTOS GENERALES";
			
			$tpl->newBlock("tipo_gasto");
			$tpl->assign("tipo_gasto", $nombre_tipo);
			$tpl->assign("title_mes", mes_escrito($mes));
			$tpl->assign("title_anio", $anio);
			$tpl->assign("title_mes_ant", mes_escrito($mes_ant));
			$tpl->assign("title_anio_ant", $anio_ant);
			$tpl->assign("title_mes_anio_ant", mes_escrito($mes));
			$tpl->assign("title_anio_anio_ant", $anio_anio_ant);
			
			$imp = 0;
			$mes_par = 0;
			$anio_par = 0;
			
			// Generar OBLIGATORIAMENTE el primer gasto de operación como GAS
			if ($tipo == 1) {
				$row = /*FALSE*/NULL;
				
				foreach ($result as $value) {
					if (in_array($value['codgastos'], array(90, 128))) {
						if (/*!$row*/$row != $value['codgastos']) {
							$tpl->newBlock("fila_gasto");
							$tpl->assign("codgastos", $value['codgastos']);
							$tpl->assign("concepto", $value['descripcion']);
							$row = /*!$row*/$value['codgastos'];
							$lineas_gastos++;
						}
						$tpl->assign($value['mes'] == $mes && $value['anio'] == $anio ? "importe" : ($value['mes'] == $mes_ant ? "mes_ant" : "anio_ant"), number_format($value['importe'], 2, ".", ","));
						$tpl->assign('num_cia', $num_cia);
						$tpl->assign($value['mes'] == $mes ? "mes" : "_mes_ant", $value['mes']);
						$tpl->assign($value['anio'] == $anio && $value['mes'] == $mes ? "anio" : ($value['anio'] == $anio_ant && $value['mes'] == $mes_ant ? "_anio_mes_ant" : "_anio_ant"), $value['anio']);
						
						$imp += $value['mes'] == $mes && $value['anio'] == $anio ? $value['importe'] : 0;
						$mes_par += $value['mes'] == $mes_ant ? $value['importe'] : 0;
						$anio_par += $value['mes'] == $mes && $value['anio'] == $anio_anio_ant ? $value['importe'] : 0;
						
						$total_importe += $value['mes'] == $mes && $value['anio'] == $anio ? $value['importe'] : 0;
						$total_mes_ant += $value['mes'] == $mes_ant ? $value['importe'] : 0;
						$total_anio_ant += $value['mes'] == $mes && $value['anio'] == $anio_anio_ant ? $value['importe'] : 0;
					}
					if ($value['tipo'] == 2)
						break;
				}
			}
		}
		if ($codtmp != $cod['codgastos'] && !in_array($cod['codgastos'], array(90, 128))) {
			$codtmp = $cod['codgastos'];
			$tpl->newBlock(in_array($codtmp, array(9, 23, 76)) ? "fila_mer" : "fila_gasto");
			$tpl->assign("codgastos", /*(in_array($cod['codgastos'], array(9, 23, 76)) ? "<span style=\"color:#0000DD\">" : "") . */$cod['codgastos']/* . (in_array($cod['codgastos'], array(9, 23, 76)) ? "</span>" : "")*/);
			$tpl->assign("concepto", /*(in_array($cod['codgastos'], array(9, 23, 76)) ? "<span style=\"color:#0000DD\">" : "") . */$cod['descripcion']/* . (in_array($cod['codgastos'], array(9, 23, 76)) ? "</span>" : "")*/);
			$lineas_gastos++;
		}
		// [03/Nov/2006] Tomar el codigo 140 - IMPUESTOS si gastos de antes del mes de Octubre del 2006, si no omitirlo
		if (!in_array($cod['codgastos'], array(90, 128, 140)) || ($cod['codgastos'] == 140 && mktime(0, 0, 0, $cod['mes'], 1, $cod['anio']) < mktime(0, 0, 0, 10, 1, 2006))) {
				$tpl->assign($cod['mes'] == $mes && $cod['anio'] == $anio ? "importe" : ($cod['mes'] == $mes_ant ? "mes_ant" : "anio_ant"), $cod['importe'] != 0 ? ($cod['importe'] < 0 ? '<span style="color:#CC0000;">' : '') . number_format($cod['importe'], 2, ".", ",") . ($cod['importe'] < 0 ? '</span>' : '') : "&nbsp;");
				$tpl->assign('num_cia', $num_cia);
				$tpl->assign($cod['mes'] == $mes ? "mes" : "_mes_ant", $cod['mes']);
				$tpl->assign($cod['anio'] == $anio && $cod['mes'] == $mes ? "anio" : ($cod['anio'] == $anio_ant && $cod['mes'] == $mes_ant ? "_anio_mes_ant" : "_anio_ant"), $cod['anio']);
				
				$imp += $cod['mes'] == $mes && $cod['anio'] == $anio ? $cod['importe'] : 0;
				$mes_par += $cod['mes'] == $mes_ant ? $cod['importe'] : 0;
				$anio_par += $cod['mes'] == $mes && $cod['anio'] == $anio_anio_ant ? $cod['importe'] : 0;
				
				$total_importe += $cod['mes'] == $mes && $cod['anio'] == $anio ? $cod['importe'] : 0;
				$total_mes_ant += $cod['mes'] == $mes_ant ? $cod['importe'] : 0;
				$total_anio_ant += $cod['mes'] == $mes && $cod['anio'] == $anio_anio_ant ? $cod['importe'] : 0;
			}
	}
	if ($tipo != NULL) {
		$tpl->assign("tipo_gasto.importe", number_format($imp, 2, ".", ","));
		$tpl->assign("tipo_gasto.mes_ant", number_format($mes_par, 2, ".", ","));
		$tpl->assign("tipo_gasto.anio_ant", number_format($anio_par, 2, ".", ","));
	}
	$tpl->assign("gastos_extras.total_importe", number_format($total_importe, 2, ".", ","));
	$tpl->assign("gastos_extras.total_mes_ant", number_format($total_mes_ant, 2, ".", ","));
	$tpl->assign("gastos_extras.total_anio_ant", number_format($total_anio_ant, 2, ".", ","));
	
	// 1er bloque de gastos extras (GASTOS DE OPERACION)
	/*$tpl->newBlock("tipo_gasto");
	$tpl->assign("tipo_gasto", "GASTOS DE OPERACI&Oacute;N");
	// Generar OBLIGATORIAMENTE el primer gastos de operación como GAS
	$sql = "SELECT sum(importe) FROM catalogo_gastos JOIN movimiento_gastos USING (codgastos) WHERE num_cia = $num_cia AND codgastos = 90 AND fecha BETWEEN '$fecha1' AND '$fecha2' AND codigo_edo_resultados = 1";
	$importe = $db->query($sql);
	$sql = "SELECT sum(importe) FROM catalogo_gastos JOIN movimiento_gastos USING (codgastos) WHERE num_cia = $num_cia AND codgastos = 90 AND fecha BETWEEN '$fecha1_ant' AND '$fecha2_ant' AND codigo_edo_resultados = 1";
	$mes_ant = $db->query($sql);
	
	$imp = 0;
	$mes_par = 0;

	if (round($importe[0]['sum'], 2) != 0 || round($mes_ant[0]['sum'], 2) != 0) {
		$tpl->newBlock("fila_gasto");
		$tpl->assign("concepto", "GAS");
		$tpl->assign("importe", $importe[0]['sum'] != 0 ? number_format($importe[0]['sum'], 2, ".", ",") : "&nbsp;");
		$tpl->assign("mes_ant", $mes_ant[0]['sum'] != 0 ? number_format($mes_ant[0]['sum'], 2, ".", ",") : "&nbsp;");
		$resta = $importe[0]['sum'] - $mes_ant[0]['sum'];
		$variacion = $importe[0]['sum'] != 0 ? $resta * 100 / $importe[0]['sum'] : 0;
		$tpl->assign("variacion", $variacion > -100 && $variacion < 100 && $variacion != 0 ? number_format(abs($variacion), 2, ".", ",") . ($variacion > 0 ? " MAS" : " MENOS") : "&nbsp;");
		
		$imp += $importe[0]['sum'];
		$mes_par += $mes_ant[0]['sum'];
		
		$total_importe += $importe[0]['sum'];
		$total_mes_ant += $mes_ant[0]['sum'];
	}
	// Generar gastos de operación restantes
	$sql = "SELECT DISTINCT ON (codgastos) * FROM catalogo_gastos JOIN movimiento_gastos USING (codgastos) WHERE num_cia = $num_cia AND fecha BETWEEN '$fecha1' AND '$fecha2' AND codgastos != 90 AND codigo_edo_resultados = 1 ORDER BY codgastos";
	$codigos = $db->query($sql);
	if ($codigos) {
		for ($i = 0; $i < count($codigos); $i++) {
			$sql = "SELECT sum(importe) FROM catalogo_gastos JOIN movimiento_gastos USING (codgastos) WHERE num_cia = $num_cia AND codgastos = {$codigos[$i]['codgastos']} AND fecha BETWEEN '$fecha1' AND '$fecha2' AND codigo_edo_resultados = 1";
			$importe = $db->query($sql);
			$sql = "SELECT sum(importe) FROM catalogo_gastos JOIN movimiento_gastos USING (codgastos) WHERE num_cia = $num_cia AND codgastos = {$codigos[$i]['codgastos']} AND fecha BETWEEN '$fecha1_ant' AND '$fecha2_ant' AND codigo_edo_resultados = 1";
			$mes_ant = $db->query($sql);
			if (round($importe[0]['sum'], 2) != 0) {
				$tpl->newBlock("fila_gasto");
				$tpl->assign("codgastos", $codigos[$i]['codgastos']);
				$tpl->assign("concepto", $codigos[$i]['descripcion']);
				$tpl->assign("importe", number_format($importe[0]['sum'], 2, ".", ","));
				$tpl->assign("mes_ant",$mes_ant[0]['sum'] != 0 ? number_format($mes_ant[0]['sum'], 2, ".", ",") : "&nbsp;");
				$resta = $importe[0]['sum'] - $mes_ant[0]['sum'];
				$variacion = $importe[0]['sum'] != 0 ? $resta * 100 / $importe[0]['sum'] : 0;
				$tpl->assign("variacion", $variacion > -100 && $variacion < 100 && $variacion != 0 ? number_format(abs($variacion), 2, ".", ",") . ($variacion > 0 ? " MAS" : " MENOS") : "&nbsp;");
				
				$imp += $importe[0]['sum'];
				$mes_par += $mes_ant[0]['sum'];
				
				$total_importe += $importe[0]['sum'];
				$total_mes_ant += $mes_ant[0]['sum'];
			}
		}
	}
	$tpl->assign("tipo_gasto.importe", number_format($imp, 2, ".", ","));
	$tpl->assign("tipo_gasto.mes_ant", number_format($mes_par, 2, ".", ","));
	$resta = $imp - $mes_par;
	$variacion = $imp != 0 ? $resta * 100 / $imp : 0;
	$tpl->assign("tipo_gasto.variacion", $variacion > -100 && $variacion < 100 && $variacion != 0 ? number_format(abs($variacion), 2, ".", ",") : "&nbsp;");
	
	// 2o bloque de gastos extras (GASTOS GENERALES)
	$sql = "SELECT DISTINCT ON (codgastos) * FROM catalogo_gastos JOIN movimiento_gastos USING (codgastos) WHERE num_cia = $num_cia AND fecha BETWEEN '$fecha1' AND '$fecha2' AND codigo_edo_resultados = 2 ORDER BY codgastos";
	$codigos = $db->query($sql);
	
	$imp = 0;
	$mes_par = 0;
	
	if ($codigos) {
		$tpl->newBlock("tipo_gasto");
		$tpl->assign("tipo_gasto", "GASTOS GENERALES");
		for ($i = 0; $i < count($codigos); $i++) {
			$sql = "SELECT sum(importe) FROM catalogo_gastos JOIN movimiento_gastos USING (codgastos) WHERE num_cia = $num_cia AND codgastos = {$codigos[$i]['codgastos']} AND fecha BETWEEN '$fecha1' AND '$fecha2' AND codigo_edo_resultados = 2";
			$importe = $db->query($sql);
			$sql = "SELECT sum(importe) FROM catalogo_gastos JOIN movimiento_gastos USING (codgastos) WHERE num_cia = $num_cia AND codgastos = {$codigos[$i]['codgastos']} AND fecha BETWEEN '$fecha1_ant' AND '$fecha2_ant' AND codigo_edo_resultados = 2";
			$mes_ant = $db->query($sql);
			
			if (round($importe[0]['sum'], 2) != 0) {
				$tpl->newBlock("fila_gasto");
				$tpl->assign("codgastos", $codigos[$i]['codgastos']);
				$tpl->assign("concepto", $codigos[$i]['descripcion']);
				$tpl->assign("importe", number_format($importe[0]['sum'], 2, ".", ","));
				$tpl->assign("mes_ant", $mes_ant[0]['sum'] != 0 ? number_format($mes_ant[0]['sum'], 2, ".", ",") : "&nbsp;");
				$resta = $importe[0]['sum'] - $mes_ant[0]['sum'];
				$variacion = $importe[0]['sum'] != 0 ? $resta * 100 / $importe[0]['sum'] : 0;
				$tpl->assign("variacion", $variacion > -100 && $variacion < 100 && $variacion != 0 ? number_format(abs($variacion), 2, ".", ",") . ($variacion > 0 ? " MAS" : " MENOS") : "&nbsp;");
				
				$imp += $importe[0]['sum'];
				$mes_par += $mes_ant[0]['sum'];
				
				$total_importe += $importe[0]['sum'];
				$total_mes_ant += $mes_ant[0]['sum'];
			}
		}
	}
	$tpl->assign("tipo_gasto.importe", number_format($imp, 2, ".", ","));
	$tpl->assign("tipo_gasto.mes_ant", number_format($mes_par, 2, ".", ","));
	$resta = $imp - $mes_par;
	$variacion = ($imp != 0)?$resta * 100 / $imp:0;
	$tpl->assign("tipo_gasto.variacion", $variacion > -100 && $variacion < 100 && $variacion != 0 ? number_format($variacion, 2, ".", ",") : "&nbsp;");
	
	// Totales de Gastos
	$tpl->assign("gastos_extras.total_importe", number_format($total_importe, 2, ".", ","));
	$tpl->assign("gastos_extras.total_mes_ant", number_format($total_mes_ant,2, ".", ","));
	$resta = $total_importe - $total_mes_ant;
	$variacion = $total_importe != 0 ? $resta * 100 / $total_importe : 0;
	$tpl->assign("gastos_extras.prom_variacion", $variacion > -100 && $variacion < 100 && $variacion != 0 ? number_format(abs($variacion), 2, ".", ",") : "&nbsp;");*/
	
	// 3er bloque de gastos extras (GASTOS POR CAJA) (Omitir codigo 28 - Abarrotes Julild)
	/*$sql = "SELECT cod_gastos, descripcion, tipo_mov, sum(importe) AS importe FROM gastos_caja LEFT JOIN catalogo_gastos_caja ON (catalogo_gastos_caja.id = cod_gastos)";
	$sql .= " WHERE num_cia = $num_cia AND fecha BETWEEN '$fecha1' AND '$fecha2' AND clave_balance = 'TRUE' GROUP BY cod_gastos, descripcion, tipo_mov ORDER BY cod_gastos";
	$result = $db->query($sql);*/
	
	$sql = "SELECT cod_gastos, descripcion, tipo_mov, extract(month FROM fecha) AS mes, extract(year FROM fecha) AS anio, sum(importe) AS importe FROM gastos_caja";
	$sql .= " LEFT JOIN catalogo_gastos_caja ON (catalogo_gastos_caja.id = cod_gastos) WHERE num_cia = $num_cia AND (fecha BETWEEN '$fecha1_ant' AND '$fecha2' OR";
	$sql .= " fecha BETWEEN '$fecha1_anio_ant' AND '$fecha2_anio_ant') AND clave_balance = 'TRUE' GROUP BY cod_gastos, descripcion, extract(month FROM fecha), extract(year FROM fecha), tipo_mov ORDER BY";
	$sql .= " cod_gastos, extract(year FROM fecha) DESC, extract(month FROM fecha) DESC, tipo_mov";
	$result = $db->query($sql);
	
	if ($result) {
		$total_importe = 0;
		$total_mes_ant = 0;
		$total_anio_ant = 0;
		
		$lineas_gastos += 3;
		
		$tpl->newBlock("gastos_caja");
		$tpl->assign("title_mes", mes_escrito($mes));
		$tpl->assign("title_anio", $anio);
		$tpl->assign("title_mes_ant", mes_escrito($mes_ant));
		$tpl->assign("title_anio_ant", $anio_ant);
		$tpl->assign("title_mes_anio_ant", mes_escrito($mes));
		$tpl->assign("title_anio_anio_ant", $anio_anio_ant);
		
		$codtmp = NULL;
		foreach ($result as $cod) {
			if ($codtmp != $cod['cod_gastos']) {
				$codtmp = $cod['cod_gastos'];
				$tpl->newBlock("fila_caja");
				$tpl->assign("concepto", $cod['descripcion']);
				$lineas_gastos++;
				
				$tmp = array("importe" => 0, "mes_ant" => 0, "anio_ant" => 0);
			}
			$etiqueta = $cod['mes'] == $mes && $cod['anio'] == $anio ? "importe" : ($cod['mes'] == $mes_ant ? "mes_ant" : "anio_ant");
			$tmp[$etiqueta] += $cod['tipo_mov'] == "t" ? $cod['importe'] : -$cod['importe'];
			$tpl->assign($etiqueta, number_format($tmp[$etiqueta], 2, ".", ","));
			
			$total_importe += $cod['mes'] == $mes && $cod['anio'] == $anio ? ($cod['tipo_mov'] == "t" ? $cod['importe'] : -$cod['importe']) : 0;
			$total_mes_ant += $cod['mes'] == $mes_ant ? ($cod['tipo_mov'] == "t" ? $cod['importe'] : -$cod['importe']) : 0;
			$total_anio_ant += $cod['mes'] == $mes && $cod['anio'] == $anio_anio_ant ? ($cod['tipo_mov'] == "t" ? $cod['importe'] : -$cod['importe']) : 0;
		}
		$tpl->assign("gastos_caja.total_importe", number_format($total_importe, 2, ".", ","));
		$tpl->assign("gastos_caja.total_mes_ant", number_format($total_mes_ant, 2, ".", ","));
		$tpl->assign("gastos_caja.total_anio_ant", number_format($total_anio_ant, 2, ".", ","));
	}
	
	$hojas = $lineas_gastos > 79 ? 1 : 0;
	
	/*if ($result) {
		$tpl->newBlock("gastos_caja");
		$total = 0;
		$codtmp = NULL;
		foreach ($result as $cod) {
			if ($codtmp != $cod['cod_gastos']) {
				$codtmp = $cod['cod_gastos'];
				
				$tpl->newBlock("fila_caja");
				$tpl->assign("concepto", $cod['descripcion']);
				$importe = 0;
			}
			$importe += $cod['tipo_mov'] == "t" ? $cod['importe'] : -$cod['importe'];
			$total += $cod['tipo_mov'] == "t" ? $cod['importe'] : -$cod['importe'];
			$tpl->assign("importe", number_format($importe, 2, ".", ","));
		}
		$tpl->assign("gastos_caja.importe", number_format($total, 2, ".", ","));
	}*/
	
	/*$sql = "SELECT DISTINCT ON (cod_gastos) cod_gastos, descripcion FROM gastos_caja JOIN catalogo_gastos_caja ON (catalogo_gastos_caja.id = cod_gastos) WHERE num_cia = $num_cia AND fecha BETWEEN '$fecha1' AND '$fecha2' AND clave_balance = 'TRUE' AND cod_gastos NOT IN (28) ORDER BY cod_gastos";
	$cod_caja = $db->query($sql);
	if ($cod_caja) {
		$tpl->newBlock("gastos_caja");
		$total = 0;
		for ($g=0; $g<count($cod_caja); $g++) {
			$tpl->newBlock("fila_caja");
			// Obtener ingresos
			$sql = "SELECT SUM(importe) FROM gastos_caja WHERE num_cia = $num_cia AND fecha BETWEEN '$fecha1' AND '$fecha2' AND cod_gastos = {$cod_caja[$g]['cod_gastos']} AND clave_balance = 'TRUE' AND tipo_mov = 'TRUE'";
			$ingresos = $db->query($sql);
			$sql = "SELECT SUM(importe) FROM gastos_caja WHERE num_cia=$num_cia AND fecha BETWEEN '$fecha1' AND '$fecha2' AND cod_gastos = {$cod_caja[$g]['cod_gastos']} AND clave_balance = 'TRUE' AND tipo_mov = 'FALSE'";
			$egresos = $db->query($sql);
			$importe = $ingresos[0]['sum'] - $egresos[0]['sum'];
			$tpl->assign("concepto", $cod_caja[$g]['descripcion']);
			$tpl->assign("importe", number_format($importe, 2, ".", ","));
			$total += $importe;

		}
		$tpl->assign("gastos_caja.importe", number_format($total, 2, ".", ","));
	}*/
	
	/*-------------------------------------------*/
	/*$fin = microtime_float();
	$tiempo = $fin - $inicio;
	echo "Tiempo de ejecución GASTOS CAJA : ".round($tiempo,3)." segundos<br>";*/
	/*-------------------------------------------*/
	
	/**************************************** ESTADO DE RESULTADOS COMPARATIVO *************************************************/
	$tpl->assign("reporte.mes_anio_ant", mes_escrito($mes));
	$tpl->assign("reporte.anio_anio_ant", $anio - 1);
	$tpl->assign("reporte.mes_ant" , mes_escrito($mes - 1 > 0 ? $mes - 1 : 12));
	$tpl->assign("reporte.anio_ant", $mes - 1 > 0 ? $anio : $anio - 1);
	$tpl->assign("reporte.mes_act" , mes_escrito($mes));
	$tpl->assign("reporte.anio_act", $anio);
	
	$tpl->gotoBlock("reporte");
	
	// Obtener datos del balance anterior
	$sql = "SELECT * FROM balances_pan WHERE num_cia = $num_cia AND mes = " . ($mes - 1 > 0 ? $mes - 1 : 12)." AND anio = " . ($mes - 1 > 0 ? $anio : $anio - 1);
	$bal_ant = $db->query($sql);
	
	$sql = "SELECT * FROM balances_pan WHERE num_cia = $num_cia AND mes = $mes AND anio = " . ($anio - 1);
	$bal_anio_ant = $db->query($sql);
	
	if ($bal_ant) {
		// Balance anterior
		$tpl->assign("vta_pta_ant",($bal_ant[0]['venta_puerta'] != 0)?"<font color='#".(($bal_ant[0]['venta_puerta'] > 0)?"0000FF":"FF0000")."'>".number_format($bal_ant[0]['venta_puerta'],2,".",",")."</font>":"&nbsp;");
		$tpl->assign("bases_ant",($bal_ant[0]['bases'] != 0)?"<font color='#".(($bal_ant[0]['bases'] > 0)?"0000FF":"FF0000")."'>".number_format($bal_ant[0]['bases'],2,".",",")."</font>":"&nbsp;");
		$tpl->assign("barredura_ant",($bal_ant[0]['barredura'] != 0)?"<font color='#".(($bal_ant[0]['barredura'] > 0)?"0000FF":"FF0000")."'>".number_format($bal_ant[0]['barredura'],2,".",",")."</font>":"&nbsp;");
		$tpl->assign("pastillaje_ant",($bal_ant[0]['pastillaje'] != 0)?"<font color='#".(($bal_ant[0]['pastillaje'] > 0)?"0000FF":"FF0000")."'>".number_format($bal_ant[0]['pastillaje'],2,".",",")."</font>":"&nbsp;");
		$tpl->assign("abono_emp_ant",($bal_ant[0]['abono_emp'] != 0)?"<font color='#".(($bal_ant[0]['abono_emp'] > 0)?"0000FF":"FF0000")."'>".number_format($bal_ant[0]['abono_emp'],2,".",",")."</font>":"&nbsp;");
		$tpl->assign("otros_ant",($bal_ant[0]['otros'] != 0)?"<font color='#".(($bal_ant[0]['otros'] > 0)?"0000FF":"FF0000")."'>".number_format($bal_ant[0]['otros'],2,".",",")."</font>":"&nbsp;");
		$tpl->assign("total_otros_ant",($bal_ant[0]['total_otros'] != 0)?"<font color='#".(($bal_ant[0]['total_otros'] > 0)?"0000FF":"FF0000")."'>".number_format($bal_ant[0]['total_otros'],2,".",",")."</font>":"&nbsp;");
		$tpl->assign("abono_reparto_ant",($bal_ant[0]['abono_reparto'] != 0)?"<font color='#".(($bal_ant[0]['abono_reparto'] > 0)?"0000FF":"FF0000")."'>".number_format($bal_ant[0]['abono_reparto'],2,".",",")."</font>":"&nbsp;");
		$tpl->assign("errores_ant",($bal_ant[0]['errores'] != 0)?"<font color='#".(($bal_ant[0]['errores'] > 0)?"0000FF":"FF0000")."'>".number_format($bal_ant[0]['errores'],2,".",",")."</font>":"&nbsp;");
		$tpl->assign("ventas_netas_ant",($bal_ant[0]['ventas_netas'] != 0)?"<font color='#".(($bal_ant[0]['ventas_netas'] > 0)?"0000FF":"FF0000")."'>".number_format($bal_ant[0]['ventas_netas'],2,".",",")."</font>":"&nbsp;");
		
		$tpl->assign("inventario_anterior_ant",($bal_ant[0]['inv_ant'] != 0)?"<font color='#".(($bal_ant[0]['inv_ant'] > 0)?"0000FF":"FF0000")."'>".number_format($bal_ant[0]['inv_ant'],2,".",",")."</font>":"&nbsp;");
		$tpl->assign("compras_ant",($bal_ant[0]['compras'] != 0)?"<font color='#".(($bal_ant[0]['compras'] > 0)?"0000FF":"FF0000")."'>".number_format($bal_ant[0]['compras'],2,".",",")."</font>":"&nbsp;");
		$tpl->assign("mercancias_ant",($bal_ant[0]['mercancias'] != 0)?"<font color='#".(($bal_ant[0]['mercancias'] > 0)?"0000FF":"FF0000")."'>".number_format($bal_ant[0]['mercancias'],2,".",",")."</font>":"&nbsp;");
		$tpl->assign("inventario_actual_ant",($bal_ant[0]['inv_act'] != 0)?"<font color='#".(($bal_ant[0]['inv_act'] > 0)?"0000FF":"FF0000")."'>".number_format($bal_ant[0]['inv_act'],2,".",",")."</font>":"&nbsp;");
		$tpl->assign("mat_prima_utilizada_ant",($bal_ant[0]['mat_prima_utilizada'] != 0)?"<font color='#".(($bal_ant[0]['mat_prima_utilizada'] > 0)?"0000FF":"FF0000")."'>".number_format($bal_ant[0]['mat_prima_utilizada'],2,".",",")."</font>":"&nbsp;");
		$tpl->assign("mano_obra_ant",($bal_ant[0]['mano_obra'] != 0)?"<font color='#".(($bal_ant[0]['mano_obra'] > 0)?"0000FF":"FF0000")."'>".number_format($bal_ant[0]['mano_obra'],2,".",",")."</font>":"&nbsp;");
		$tpl->assign("panaderos_ant",($bal_ant[0]['panaderos'] != 0)?"<font color='#".(($bal_ant[0]['panaderos'] > 0)?"0000FF":"FF0000")."'>".number_format($bal_ant[0]['panaderos'],2,".",",")."</font>":"&nbsp;");
		$tpl->assign("gastos_fabricacion_ant",($bal_ant[0]['gastos_fab'] != 0)?"<font color='#".(($bal_ant[0]['gastos_fab'] > 0)?"0000FF":"FF0000")."'>".number_format($bal_ant[0]['gastos_fab'],2,".",",")."</font>":"&nbsp;");
		$tpl->assign("costo_produccion_ant",($bal_ant[0]['costo_produccion'] != 0)?"<font color='#".(($bal_ant[0]['costo_produccion'] > 0)?"0000FF":"FF0000")."'>".number_format($bal_ant[0]['costo_produccion'],2,".",",")."</font>":"&nbsp;");
		
		$tpl->assign("utilidad_bruta_ant",($bal_ant[0]['utilidad_bruta'] != 0)?"<font color='#".(($bal_ant[0]['utilidad_bruta'] > 0)?"0000FF":"FF0000")."'>".number_format($bal_ant[0]['utilidad_bruta'],2,".",",")."</font>":"&nbsp;");
		
		$tpl->assign("pan_comprado_ant",($bal_ant[0]['pan_comprado'] != 0)?"<font color='#".(($bal_ant[0]['pan_comprado'] > 0)?"0000FF":"FF0000")."'>".number_format($bal_ant[0]['pan_comprado'],2,".",",")."</font>":"&nbsp;");
		$tpl->assign("gastos_generales_ant",($bal_ant[0]['gastos_generales'] != 0)?"<font color='#".(($bal_ant[0]['gastos_generales'] > 0)?"0000FF":"FF0000")."'>".number_format($bal_ant[0]['gastos_generales'],2,".",",")."</font>":"&nbsp;");
		$tpl->assign("gastos_caja_ant",($bal_ant[0]['gastos_caja'] != 0)?"<font color='#".(($bal_ant[0]['gastos_caja'] > 0)?"0000FF":"FF0000")."'>".number_format($bal_ant[0]['gastos_caja'],2,".",",")."</font>":"&nbsp;");
		$tpl->assign("reserva_aguinaldos_ant",($bal_ant[0]['reserva_aguinaldos'] != 0)?"<font color='#".(($bal_ant[0]['reserva_aguinaldos'] > 0)?"0000FF":"FF0000")."'>".number_format($bal_ant[0]['reserva_aguinaldos'],2,".",",")."</font>":"&nbsp;");
		$tpl->assign("pagos_anticipados_ant",($bal_ant[0]['pagos_anticipados'] != 0)?"<font color='#".(($bal_ant[0]['pagos_anticipados'] > 0)?"0000FF":"FF0000")."'>".number_format($bal_ant[0]['pagos_anticipados'],2,".",",")."</font>":"&nbsp;");
		$tpl->assign("gastos_otras_cias_ant",($bal_ant[0]['gastos_otras_cias'] != 0)?"<font color='#".(($bal_ant[0]['gastos_otras_cias'] > 0)?"0000FF":"FF0000")."'>".number_format($bal_ant[0]['gastos_otras_cias'],2,".",",")."</font>":"&nbsp;");
		$tpl->assign("total_gastos_ant",($bal_ant[0]['total_gastos'] != 0)?"<font color='#".(($bal_ant[0]['total_gastos'] > 0)?"0000FF":"FF0000")."'>".number_format($bal_ant[0]['total_gastos'],2,".",",")."</font>":"&nbsp;");
		
		$tpl->assign("ingresos_ext_ant",($bal_ant[0]['ingresos_ext'] != 0)?"<font color='#".(($bal_ant[0]['ingresos_ext'] > 0)?"0000FF":"FF0000")."'>".number_format($bal_ant[0]['ingresos_ext'],2,".",",")."</font>":"&nbsp;");
		
		$tpl->assign("utilidad_mes_ant",($bal_ant[0]['utilidad_neta'] != 0)?"<font color='#".(($bal_ant[0]['utilidad_neta'] > 0)?"0000FF":"FF0000")."'>".number_format($bal_ant[0]['utilidad_neta'],2,".",",")."</font>":"&nbsp;");
		
		$tpl->assign("mp_vtas_ant",($bal_ant[0]['mp_vtas'] != 0)?"<font color='#".(($bal_ant[0]['mp_vtas'] > 0)?"0000FF":"FF0000")."'>".number_format($bal_ant[0]['mp_vtas'],3,".",",")."</font>":"&nbsp;");
		$tpl->assign("utilidad_produccion_ant",($bal_ant[0]['utilidad_pro'] != 0)?"<font color='#".(($bal_ant[0]['utilidad_pro'] > 0)?"0000FF":"FF0000")."'>".number_format($bal_ant[0]['utilidad_pro'],3,".",",")."</font>":"&nbsp;");
		$tpl->assign("mp_produccion_ant",($bal_ant[0]['mp_pro'] != 0)?"<font color='#".(($bal_ant[0]['mp_pro'] > 0)?"0000FF":"FF0000")."'>".number_format($bal_ant[0]['mp_pro'],3,".",",")."</font>":"&nbsp;");
		
		$tpl->assign("produccion_total_ant",($bal_ant[0]['produccion_total'] != 0)?"<font color='#".(($bal_ant[0]['produccion_total'] > 0)?"0000FF":"FF0000")."'>".number_format($bal_ant[0]['produccion_total'],2,".",",")."</font>":"&nbsp;");
		$tpl->assign("faltante_pan_ant",($bal_ant[0]['faltante_pan'] != 0)?"<font color='#".(($bal_ant[0]['faltante_pan'] > 0)?"0000FF":"FF0000")."'>".number_format($bal_ant[0]['faltante_pan'],2,".",",")."</font>":"&nbsp;");
		$tpl->assign("rezago_inicial_ant",($bal_ant[0]['rezago_ini'] != 0)?"<font color='#".(($bal_ant[0]['rezago_ini'] > 0)?"0000FF":"FF0000")."'>".number_format($bal_ant[0]['rezago_ini'],2,".",",")."</font>":"&nbsp;");
		$tpl->assign("rezago_final_ant",($bal_ant[0]['rezago_fin'] != 0)?"<font color='#".(($bal_ant[0]['rezago_fin'] > 0)?"0000FF":"FF0000")."'>".number_format($bal_ant[0]['rezago_fin'],2,".",",")."</font>":"&nbsp;");
		$tpl->assign("var_rezago_ant",($bal_ant[0]['var_rezago'] != 0)?"<font color='#".(($bal_ant[0]['var_rezago'] > 0)?"0000FF":"FF0000")."'>".number_format($bal_ant[0]['var_rezago'],2,".",",")."</font>":"&nbsp;");
		$tpl->assign("efectivo_ant",($bal_ant[0]['efectivo'] != 0)?"<font color='#".(($bal_ant[0]['efectivo'] > 0)?"0000FF":"FF0000")."'>".number_format($bal_ant[0]['efectivo'],2,".",",")."</font>":"&nbsp;");
		
		// Año anterior
		$tpl->assign("venta_puerta_aa",($bal_anio_ant[0]['venta_puerta'] != 0)?"<font color='#".(($bal_anio_ant[0]['venta_puerta'] > 0)?"0000FF":"FF0000")."'>".number_format($bal_anio_ant[0]['venta_puerta'],2,".",",")."</font>":"&nbsp;");
		$tpl->assign("bases_aa",($bal_anio_ant[0]['bases'] != 0)?"<font color='#".(($bal_anio_ant[0]['bases'] > 0)?"0000FF":"FF0000")."'>".number_format($bal_anio_ant[0]['bases'],2,".",",")."</font>":"&nbsp;");
		$tpl->assign("barredura_aa",($bal_anio_ant[0]['barredura'] != 0)?"<font color='#".(($bal_anio_ant[0]['barredura'] > 0)?"0000FF":"FF0000")."'>".number_format($bal_anio_ant[0]['barredura'],2,".",",")."</font>":"&nbsp;");
		$tpl->assign("pastillaje_aa",($bal_anio_ant[0]['pastillaje'] != 0)?"<font color='#".(($bal_anio_ant[0]['pastillaje'] > 0)?"0000FF":"FF0000")."'>".number_format($bal_anio_ant[0]['pastillaje'],2,".",",")."</font>":"&nbsp;");
		$tpl->assign("abono_emp_aa",($bal_anio_ant[0]['abono_emp'] != 0)?"<font color='#".(($bal_anio_ant[0]['abono_emp'] > 0)?"0000FF":"FF0000")."'>".number_format($bal_anio_ant[0]['abono_emp'],2,".",",")."</font>":"&nbsp;");
		$tpl->assign("otros_aa",($bal_anio_ant[0]['otros'] != 0)?"<font color='#".(($bal_anio_ant[0]['otros'] > 0)?"0000FF":"FF0000")."'>".number_format($bal_anio_ant[0]['otros'],2,".",",")."</font>":"&nbsp;");
		$tpl->assign("total_otros_aa",($bal_anio_ant[0]['total_otros'] != 0)?"<font color='#".(($bal_anio_ant[0]['total_otros'] > 0)?"0000FF":"FF0000")."'>".number_format($bal_anio_ant[0]['total_otros'],2,".",",")."</font>":"&nbsp;");
		$tpl->assign("abono_reparto_aa",($bal_anio_ant[0]['abono_reparto'] != 0)?"<font color='#".(($bal_anio_ant[0]['abono_reparto'] > 0)?"0000FF":"FF0000")."'>".number_format($bal_anio_ant[0]['abono_reparto'],2,".",",")."</font>":"&nbsp;");
		$tpl->assign("errores_aa",($bal_anio_ant[0]['errores'] != 0)?"<font color='#".(($bal_anio_ant[0]['errores'] > 0)?"0000FF":"FF0000")."'>".number_format($bal_anio_ant[0]['errores'],2,".",",")."</font>":"&nbsp;");
		$tpl->assign("ventas_netas_aa",($bal_anio_ant[0]['ventas_netas'] != 0)?"<font color='#".(($bal_anio_ant[0]['ventas_netas'] > 0)?"0000FF":"FF0000")."'>".number_format($bal_anio_ant[0]['ventas_netas'],2,".",",")."</font>":"&nbsp;");
		
		$tpl->assign("inventario_anterior_aa",($bal_anio_ant[0]['inv_ant'] != 0)?"<font color='#".(($bal_anio_ant[0]['inv_ant'] > 0)?"0000FF":"FF0000")."'>".number_format($bal_anio_ant[0]['inv_ant'],2,".",",")."</font>":"&nbsp;");
		$tpl->assign("compras_aa",($bal_anio_ant[0]['compras'] != 0)?"<font color='#".(($bal_anio_ant[0]['compras'] > 0)?"0000FF":"FF0000")."'>".number_format($bal_anio_ant[0]['compras'],2,".",",")."</font>":"&nbsp;");
		$tpl->assign("mercancias_aa",($bal_anio_ant[0]['mercancias'] != 0)?"<font color='#".(($bal_anio_ant[0]['mercancias'] > 0)?"0000FF":"FF0000")."'>".number_format($bal_anio_ant[0]['mercancias'],2,".",",")."</font>":"&nbsp;");
		$tpl->assign("inventario_actual_aa",($bal_anio_ant[0]['inv_act'] != 0)?"<font color='#".(($bal_anio_ant[0]['inv_act'] > 0)?"0000FF":"FF0000")."'>".number_format($bal_anio_ant[0]['inv_act'],2,".",",")."</font>":"&nbsp;");
		$tpl->assign("mat_prima_utilizada_aa",($bal_anio_ant[0]['mat_prima_utilizada'] != 0)?"<font color='#".(($bal_anio_ant[0]['mat_prima_utilizada'] > 0)?"0000FF":"FF0000")."'>".number_format($bal_anio_ant[0]['mat_prima_utilizada'],2,".",",")."</font>":"&nbsp;");
		$tpl->assign("mano_obra_aa",($bal_anio_ant[0]['mano_obra'] != 0)?"<font color='#".(($bal_anio_ant[0]['mano_obra'] > 0)?"0000FF":"FF0000")."'>".number_format($bal_anio_ant[0]['mano_obra'],2,".",",")."</font>":"&nbsp;");
		$tpl->assign("panaderos_aa",($bal_anio_ant[0]['panaderos'] != 0)?"<font color='#".(($bal_anio_ant[0]['panaderos'] > 0)?"0000FF":"FF0000")."'>".number_format($bal_anio_ant[0]['panaderos'],2,".",",")."</font>":"&nbsp;");
		$tpl->assign("gastos_fabricacion_aa",($bal_anio_ant[0]['gastos_fab'] != 0)?"<font color='#".(($bal_anio_ant[0]['gastos_fab'] > 0)?"0000FF":"FF0000")."'>".number_format($bal_anio_ant[0]['gastos_fab'],2,".",",")."</font>":"&nbsp;");
		$tpl->assign("costo_produccion_aa",($bal_anio_ant[0]['costo_produccion'] != 0)?"<font color='#".(($bal_anio_ant[0]['costo_produccion'] > 0)?"0000FF":"FF0000")."'>".number_format($bal_anio_ant[0]['costo_produccion'],2,".",",")."</font>":"&nbsp;");
		
		$tpl->assign("utilidad_bruta_aa",($bal_anio_ant[0]['utilidad_bruta'] != 0)?"<font color='#".(($bal_anio_ant[0]['utilidad_bruta'] > 0)?"0000FF":"FF0000")."'>".number_format($bal_anio_ant[0]['utilidad_bruta'],2,".",",")."</font>":"&nbsp;");
		
		$tpl->assign("pan_comprado_aa",($bal_anio_ant[0]['pan_comprado'] != 0)?"<font color='#".(($bal_anio_ant[0]['pan_comprado'] > 0)?"0000FF":"FF0000")."'>".number_format($bal_anio_ant[0]['pan_comprado'],2,".",",")."</font>":"&nbsp;");
		$tpl->assign("gastos_generales_aa",($bal_anio_ant[0]['gastos_generales'] != 0)?"<font color='#".(($bal_anio_ant[0]['gastos_generales'] > 0)?"0000FF":"FF0000")."'>".number_format($bal_anio_ant[0]['gastos_generales'],2,".",",")."</font>":"&nbsp;");
		$tpl->assign("gastos_caja_aa",($bal_anio_ant[0]['gastos_caja'] != 0)?"<font color='#".(($bal_anio_ant[0]['gastos_caja'] > 0)?"0000FF":"FF0000")."'>".number_format($bal_anio_ant[0]['gastos_caja'],2,".",",")."</font>":"&nbsp;");
		$tpl->assign("reserva_aguinaldos_aa",($bal_anio_ant[0]['reserva_aguinaldos'] != 0)?"<font color='#".(($bal_anio_ant[0]['reserva_aguinaldos'] > 0)?"0000FF":"FF0000")."'>".number_format($bal_anio_ant[0]['reserva_aguinaldos'],2,".",",")."</font>":"&nbsp;");
		$tpl->assign("pagos_anticipados_aa",($bal_anio_ant[0]['pagos_anticipados'] != 0)?"<font color='#".(($bal_anio_ant[0]['pagos_anticipados'] > 0)?"0000FF":"FF0000")."'>".number_format($bal_anio_ant[0]['pagos_anticipados'],2,".",",")."</font>":"&nbsp;");
		$tpl->assign("gastos_otras_cias_aa",($bal_anio_ant[0]['gastos_otras_cias'] != 0)?"<font color='#".(($bal_anio_ant[0]['gastos_otras_cias'] > 0)?"0000FF":"FF0000")."'>".number_format($bal_anio_ant[0]['gastos_otras_cias'],2,".",",")."</font>":"&nbsp;");
		$tpl->assign("total_gastos_aa",($bal_anio_ant[0]['total_gastos'] != 0)?"<font color='#".(($bal_anio_ant[0]['total_gastos'] > 0)?"0000FF":"FF0000")."'>".number_format($bal_anio_ant[0]['total_gastos'],2,".",",")."</font>":"&nbsp;");
		
		$tpl->assign("ingresos_ext_aa",($bal_anio_ant[0]['ingresos_ext'] != 0)?"<font color='#".(($bal_anio_ant[0]['ingresos_ext'] > 0)?"0000FF":"FF0000")."'>".number_format($bal_anio_ant[0]['ingresos_ext'],2,".",",")."</font>":"&nbsp;");
		
		$tpl->assign("utilidad_mes_aa",($bal_anio_ant[0]['utilidad_neta'] != 0)?"<font color='#".(($bal_anio_ant[0]['utilidad_neta'] > 0)?"0000FF":"FF0000")."'>".number_format($bal_anio_ant[0]['utilidad_neta'],2,".",",")."</font>":"&nbsp;");
		
		$tpl->assign("mp_vtas_aa",($bal_anio_ant[0]['mp_vtas'] != 0)?"<font color='#".(($bal_anio_ant[0]['mp_vtas'] > 0)?"0000FF":"FF0000")."'>".number_format($bal_anio_ant[0]['mp_vtas'],3,".",",")."</font>":"&nbsp;");
		$tpl->assign("utilidad_produccion_aa",($bal_anio_ant[0]['utilidad_pro'] != 0)?"<font color='#".(($bal_anio_ant[0]['utilidad_pro'] > 0)?"0000FF":"FF0000")."'>".number_format($bal_anio_ant[0]['utilidad_pro'],3,".",",")."</font>":"&nbsp;");
		$tpl->assign("mp_produccion_aa",($bal_anio_ant[0]['mp_pro'] != 0)?"<font color='#".(($bal_anio_ant[0]['mp_pro'] > 0)?"0000FF":"FF0000")."'>".number_format($bal_anio_ant[0]['mp_pro'],3,".",",")."</font>":"&nbsp;");
		
		$tpl->assign("produccion_total_aa",($bal_anio_ant[0]['produccion_total'] != 0)?"<font color='#".(($bal_anio_ant[0]['produccion_total'] > 0)?"0000FF":"FF0000")."'>".number_format($bal_anio_ant[0]['produccion_total'],2,".",",")."</font>":"&nbsp;");
		$tpl->assign("faltante_pan_aa",($bal_anio_ant[0]['faltante_pan'] != 0)?"<font color='#".(($bal_anio_ant[0]['faltante_pan'] > 0)?"0000FF":"FF0000")."'>".number_format($bal_anio_ant[0]['faltante_pan'],2,".",",")."</font>":"&nbsp;");
		$tpl->assign("rezago_inicial_aa",($bal_anio_ant[0]['rezago_ini'] != 0)?"<font color='#".(($bal_anio_ant[0]['rezago_ini'] > 0)?"0000FF":"FF0000")."'>".number_format($bal_anio_ant[0]['rezago_ini'],2,".",",")."</font>":"&nbsp;");
		$tpl->assign("rezago_final_aa",($bal_anio_ant[0]['rezago_fin'] != 0)?"<font color='#".(($bal_anio_ant[0]['rezago_fin'] > 0)?"0000FF":"FF0000")."'>".number_format($bal_anio_ant[0]['rezago_fin'],2,".",",")."</font>":"&nbsp;");
		$tpl->assign("var_rezago_aa",($bal_anio_ant[0]['var_rezago'] != 0)?"<font color='#".(($bal_anio_ant[0]['var_rezago'] > 0)?"0000FF":"FF0000")."'>".number_format($bal_anio_ant[0]['var_rezago'],2,".",",")."</font>":"&nbsp;");
		$tpl->assign("efectivo_aa",($bal_anio_ant[0]['efectivo'] != 0)?"<font color='#".(($bal_anio_ant[0]['efectivo'] > 0)?"0000FF":"FF0000")."'>".number_format($bal_anio_ant[0]['efectivo'],2,".",",")."</font>":"&nbsp;");
		
		// Diferencia
		/*$tpl->assign("venta_puerta_dif",($venta_puerta[0]['sum']-$bal_ant[0]['venta_puerta'] != 0)?"<font color='#".(($venta_puerta[0]['sum']-$bal_ant[0]['venta_puerta'] > 0)?"0000FF":"FF0000")."'>".number_format($venta_puerta[0]['sum']-$bal_ant[0]['venta_puerta'],2,".",",")."</font>":"&nbsp;");
		//$tpl->assign("bases_dif",($bases[0]['sum']-$bal_ant[0]['bases'] != 0)?"<font color='#".(($bases[0]['sum']-$bal_ant[0]['bases'] > 0)?"0000FF":"FF0000")."'>".number_format($bases[0]['sum']-$bal_ant[0]['bases'],2,".",",")."</font>":"&nbsp;");
		$tpl->assign("barredura_dif",($barredura[0]['sum'] - $bal_ant[0]['barredura'] != 0)?"<font color='#".(($barredura[0]['sum'] - $bal_ant[0]['barredura'] > 0)?"0000FF":"FF0000")."'>".number_format($barredura[0]['sum'] - $bal_ant[0]['barredura'],2,".",",")."</font>":"&nbsp;");
		$tpl->assign("pastillaje_dif",($pastillaje[0]['sum']-$bal_ant[0]['pastillaje'] != 0)?"<font color='#".(($pastillaje[0]['sum']-$bal_ant[0]['pastillaje'] > 0)?"0000FF":"FF0000")."'>".number_format($pastillaje[0]['sum']-$bal_ant[0]['pastillaje'],2,".",",")."</font>":"&nbsp;");
		$tpl->assign("abono_emp_dif",($abono_empleados[0]['sum']-$bal_ant[0]['abono_emp'] != 0)?"<font color='#".(($abono_empleados[0]['sum']-$bal_ant[0]['abono_emp'] > 0)?"0000FF":"FF0000")."'>".number_format($abono_empleados[0]['sum']-$bal_ant[0]['abono_emp'],2,".",",")."</font>":"&nbsp;");
		$tpl->assign("otros_dif",($otros[0]['sum']-$bal_ant[0]['otros'] != 0)?"<font color='#".(($otros[0]['sum']-$bal_ant[0]['otros'] > 0)?"0000FF":"FF0000")."'>".number_format($otros[0]['sum']-$bal_ant[0]['otros'],2,".",",")."</font>":"&nbsp;");
		$tpl->assign("total_otros_dif",($total_otros-$bal_ant[0]['total_otros'] != 0)?"<font color='#".(($total_otros-$bal_ant[0]['total_otros'] > 0)?"0000FF":"FF0000")."'>".number_format($total_otros-$bal_ant[0]['total_otros'],2,".",",")."</font>":"&nbsp;");
		$tpl->assign("abono_reparto_dif",($abono_reparto[0]['sum']-$bal_ant[0]['abono_reparto'] != 0)?"<font color='#".(($abono_reparto[0]['sum']-$bal_ant[0]['abono_reparto'] > 0)?"0000FF":"FF0000")."'>".number_format($abono_reparto[0]['sum']-$bal_ant[0]['abono_reparto'],2,".",",")."</font>":"&nbsp;");
		$tpl->assign("errores_dif",($errores[0]['sum']-$bal_ant[0]['errores'] != 0)?"<font color='#".(($errores[0]['sum']-$bal_ant[0]['errores'] > 0)?"0000FF":"FF0000")."'>".number_format($errores[0]['sum']-$bal_ant[0]['errores'],2,".",",")."</font>":"&nbsp;");
		$tpl->assign("ventas_netas_dif",($ventas_netas-$bal_ant[0]['ventas_netas'] != 0)?"<font color='#".(($ventas_netas-$bal_ant[0]['ventas_netas'] > 0)?"0000FF":"FF0000")."'>".number_format($ventas_netas-$bal_ant[0]['ventas_netas'],2,".",",")."</font>":"&nbsp;");
		
		$tpl->assign("inventario_anterior_dif",($inv_ant[0]['sum']-$bal_ant[0]['inv_ant'] != 0)?"<font color='#".(($inv_ant[0]['sum']-$bal_ant[0]['inv_ant'] > 0)?"0000FF":"FF0000")."'>".number_format($inv_ant[0]['sum']-$bal_ant[0]['inv_ant'],2,".",",")."</font>":"&nbsp;");
		$tpl->assign("compras_dif",($compras-$bal_ant[0]['compras'] != 0)?"<font color='#".(($compras-$bal_ant[0]['compras'] > 0)?"0000FF":"FF0000")."'>".number_format($compras-$bal_ant[0]['compras'],2,".",",")."</font>":"&nbsp;");
		$tpl->assign("mercancias_dif",($mercancias[0]['sum']-$bal_ant[0]['mercancias'] != 0)?"<font color='#".(($mercancias[0]['sum']-$bal_ant[0]['mercancias'] > 0)?"0000FF":"FF0000")."'>".number_format($mercancias[0]['sum']-$bal_ant[0]['mercancias'],2,".",",")."</font>":"&nbsp;");
		$tpl->assign("inventario_actual_dif",($inv_act-$bal_ant[0]['inv_act'] != 0)?"<font color='#".(($inv_act-$bal_ant[0]['inv_act'] > 0)?"0000FF":"FF0000")."'>".number_format($inv_act-$bal_ant[0]['inv_act'],2,".",",")."</font>":"&nbsp;");
		$tpl->assign("mat_prima_utilizada_dif",($mat_prima_utilizada-$bal_ant[0]['mat_prima_utilizada'] != 0)?"<font color='#".(($mat_prima_utilizada-$bal_ant[0]['mat_prima_utilizada'] > 0)?"0000FF":"FF0000")."'>".number_format($mat_prima_utilizada-$bal_ant[0]['mat_prima_utilizada'],2,".",",")."</font>":"&nbsp;");
		$tpl->assign("mano_obra_dif",($mano_obra[0]['sum']-$bal_ant[0]['mano_obra'] != 0)?"<font color='#".(($mano_obra[0]['sum']-$bal_ant[0]['mano_obra'] > 0)?"0000FF":"FF0000")."'>".number_format($mano_obra[0]['sum']-$bal_ant[0]['mano_obra'],2,".",",")."</font>":"&nbsp;");
		$tpl->assign("panaderos_dif",($panaderos[0]['sum']-$bal_ant[0]['panaderos'] != 0)?"<font color='#".(($panaderos[0]['sum']-$bal_ant[0]['panaderos'] > 0)?"0000FF":"FF0000")."'>".number_format($panaderos[0]['sum']-$bal_ant[0]['panaderos'],2,".",",")."</font>":"&nbsp;");
		$tpl->assign("gastos_fabricacion_dif",($gastos_fab[0]['sum']-$bal_ant[0]['gastos_fab'] != 0)?"<font color='#".(($gastos_fab[0]['sum']-$bal_ant[0]['gastos_fab'] > 0)?"0000FF":"FF0000")."'>".number_format($gastos_fab[0]['sum']-$bal_ant[0]['gastos_fab'],2,".",",")."</font>":"&nbsp;");
		$tpl->assign("costo_produccion_dif",($costo_produccion-$bal_ant[0]['costo_produccion'] != 0)?"<font color='#".(($costo_produccion-$bal_ant[0]['costo_produccion'] > 0)?"0000FF":"FF0000")."'>".number_format($costo_produccion-$bal_ant[0]['costo_produccion'],2,".",",")."</font>":"&nbsp;");
		
		$tpl->assign("utilidad_bruta_dif",($utilidad_bruta-$bal_ant[0]['utilidad_bruta'] != 0)?"<font color='#".(($utilidad_bruta-$bal_ant[0]['utilidad_bruta'] > 0)?"0000FF":"FF0000")."'>".number_format($utilidad_bruta-$bal_ant[0]['utilidad_bruta'],2,".",",")."</font>":"&nbsp;");
		
		$tpl->assign("pan_comprado_dif",($pan_comprado-$bal_ant[0]['pan_comprado'] != 0)?"<font color='#".(($pan_comprado-$bal_ant[0]['pan_comprado'] > 0)?"0000FF":"FF0000")."'>".number_format($pan_comprado-$bal_ant[0]['pan_comprado'],2,".",",")."</font>":"&nbsp;");
		$tpl->assign("gastos_generales_dif",($gastos_gral[0]['sum']-$bal_ant[0]['gastos_generales'] != 0)?"<font color='#".(($gastos_gral[0]['sum']-$bal_ant[0]['gastos_generales'] > 0)?"0000FF":"FF0000")."'>".number_format($gastos_gral[0]['sum']-$bal_ant[0]['gastos_generales'],2,".",",")."</font>":"&nbsp;");
		$tpl->assign("gastos_caja_dif",($gastos_caja-$bal_ant[0]['gastos_caja'] != 0)?"<font color='#".(($gastos_caja-$bal_ant[0]['gastos_caja'] > 0)?"0000FF":"FF0000")."'>".number_format($gastos_caja-$bal_ant[0]['gastos_caja'],2,".",",")."</font>":"&nbsp;");
		$tpl->assign("reserva_aguinaldos_dif",($reservas[0]['sum']-$bal_ant[0]['reserva_aguinaldos'] != 0)?"<font color='#".(($reservas[0]['sum']-$bal_ant[0]['reserva_aguinaldos'] > 0)?"0000FF":"FF0000")."'>".number_format($reservas[0]['sum']-$bal_ant[0]['reserva_aguinaldos'],2,".",",")."</font>":"&nbsp;");
		$tpl->assign("pagos_anticipados_dif",($pagos_anticipados[0]['sum']-$bal_ant[0]['pagos_anticipados'] != 0)?"<font color='#".(($pagos_anticipados[0]['sum']-$bal_ant[0]['pagos_anticipados'] > 0)?"0000FF":"FF0000")."'>".number_format($pagos_anticipados[0]['sum']-$bal_ant[0]['pagos_anticipados'],2,".",",")."</font>":"&nbsp;");
		$tpl->assign("gastos_otras_cias_dif",($gastos_otros-$bal_ant[0]['gastos_otras_cias'] != 0)?"<font color='#".(($gastos_otros-$bal_ant[0]['gastos_otras_cias'] > 0)?"0000FF":"FF0000")."'>".number_format($gastos_otros-$bal_ant[0]['gastos_otras_cias'],2,".",",")."</font>":"&nbsp;");
		$tpl->assign("total_gastos_dif",($gastos_totales-$bal_ant[0]['total_gastos'] != 0)?"<font color='#".(($gastos_totales-$bal_ant[0]['total_gastos'] > 0)?"0000FF":"FF0000")."'>".number_format($gastos_totales-$bal_ant[0]['total_gastos'],2,".",",")."</font>":"&nbsp;");
		
		$tpl->assign("ingresos_ext_dif",($ingresos_ext[0]['sum']-$bal_ant[0]['ingresos_ext'] != 0)?"<font color='#".(($ingresos_ext[0]['sum']-$bal_ant[0]['ingresos_ext'] > 0)?"0000FF":"FF0000")."'>".number_format($ingresos_ext[0]['sum']-$bal_ant[0]['ingresos_ext'],2,".",",")."</font>":"&nbsp;");
		
		$tpl->assign("utilidad_mes_dif",($utilidad_neta-$bal_ant[0]['utilidad_neta'] != 0)?"<font color='#".(($utilidad_neta-$bal_ant[0]['utilidad_neta'] > 0)?"0000FF":"FF0000")."'>".number_format(($bal_ant[0]['utilidad_neta'] > 0 ? $utilidad_neta-$bal_ant[0]['utilidad_neta'] : $utilidad_neta+$bal_ant[0]['utilidad_neta']),2,".",",")."</font>":"&nbsp;");
		
		$tpl->assign("mp_vtas_dif",($bal_ant[0]['mp_vtas'] != 0)?"<font color='#".(($bal_ant[0]['mp_vtas'] > 0)?"0000FF":"FF0000")."'>".number_format($bal_ant[0]['mp_vtas'],3,".",",")."</font>":"&nbsp;");
		$tpl->assign("utilidad_produccion_dif",($bal_ant[0]['utilidad_pro'] != 0)?"<font color='#".(($bal_ant[0]['utilidad_pro'] > 0)?"0000FF":"FF0000")."'>".number_format($bal_ant[0]['utilidad_pro'],3,".",",")."</font>":"&nbsp;");
		$tpl->assign("mp_produccion_dif",($bal_ant[0]['mp_pro'] != 0)?"<font color='#".(($bal_ant[0]['mp_pro'] > 0)?"0000FF":"FF0000")."'>".number_format($bal_ant[0]['mp_pro'],3,".",",")."</font>":"&nbsp;");
		
		$tpl->assign("produccion_total_dif",($produccion_total[0]['sum']-$bal_ant[0]['produccion_total'] != 0)?"<font color='#".(($produccion_total[0]['sum']-$bal_ant[0]['produccion_total'] > 0)?"0000FF":"FF0000")."'>".number_format($produccion_total[0]['sum']-$bal_ant[0]['produccion_total'],2,".",",")."</font>":"&nbsp;");
		$tpl->assign("faltante_pan_dif",($faltante_pan-$bal_ant[0]['faltante_pan'] != 0)?"<font color='#".(($faltante_pan-$bal_ant[0]['faltante_pan'] > 0)?"0000FF":"FF0000")."'>".number_format($faltante_pan-$bal_ant[0]['faltante_pan'],2,".",",")."</font>":"&nbsp;");
		$tpl->assign("rezago_inicial_dif",($rezago_inicial[0]['sum']-$bal_ant[0]['rezago_ini'] != 0)?"<font color='#".(($rezago_inicial[0]['sum']-$bal_ant[0]['rezago_ini'] > 0)?"0000FF":"FF0000")."'>".number_format($rezago_inicial[0]['sum']-$bal_ant[0]['rezago_ini'],2,".",",")."</font>":"&nbsp;");
		$tpl->assign("rezago_final_dif",($rezago_final[0]['sum']-$bal_ant[0]['rezago_fin'] != 0)?"<font color='#".(($rezago_final[0]['sum']-$bal_ant[0]['rezago_fin'] > 0)?"0000FF":"FF0000")."'>".number_format($rezago_final[0]['sum']-$bal_ant[0]['rezago_fin'],2,".",",")."</font>":"&nbsp;");
		$tpl->assign("var_rezago_dif",($cambio_rezago-$bal_ant[0]['var_rezago'] != 0)?"<font color='#".(($cambio_rezago-$bal_ant[0]['var_rezago'] > 0)?"0000FF":"FF0000")."'>".number_format($cambio_rezago-$bal_ant[0]['var_rezago'],2,".",",")."</font>":"&nbsp;");
		$tpl->assign("efectivo_dif",($efectivo[0]['sum']-$bal_ant[0]['efectivo'] != 0)?"<font color='#".(($efectivo[0]['sum']-$bal_ant[0]['efectivo'] > 0)?"0000FF":"FF0000")."'>".number_format($efectivo[0]['sum']-$bal_ant[0]['efectivo'],2,".",",")."</font>":"&nbsp;");*/
	}
	
	/******************************************* LISTADO DE GASTOS PAGADOS A OFICINAS **********************************************/
	$sql = "SELECT id, fecha, codgastos, descripcion, facturas, concepto, importe, folio, a_nombre,(SELECT descripcion FROM facturas_pagadas WHERE num_cia = cheques.num_cia AND";
	$sql .= " folio_cheque = cheques.folio AND cuenta = cheques.cuenta LIMIT 1) AS desc FROM cheques JOIN catalogo_gastos USING(codgastos) WHERE num_cia = $num_cia AND fecha BETWEEN '$fecha1' AND '$fecha2'";
	$sql .= " AND codgastos NOT IN (33, 134, 154, 999";
	// [26/Oct/2006] Ya no aplicara el código 140 IMPUESTOS, en su lugar entreran los códigos 179, 180, 181, 182, 183
	$sql .= mktime(0, 0, 0, $mes, 1, $anio) > mktime(0, 0, 0, 9, 1, 2006) ? ", 140" : "";
	//**********************************************************************************************************************
	$sql .= ") AND (fecha_cancelacion IS NULL OR fecha_cancelacion > '$fecha2') ORDER BY codgastos, fecha";
	$result = $db->query($sql);
	
	if ($result){
		$hojas += count($result) > 29;
		
		$tpl->newBlock("listado_gastos");
		$tpl->assign('num_gastos', count($result));
		
		$tpl->assign("num_cia",$num_cia);
		$tpl->assign("nombre_cia",$cia[$f]['nombre']);
		$tpl->assign("dia",$dias);
		$tpl->assign("mes",mes_escrito($mes));
		$tpl->assign("anio",$anio);
		
		$codgastos = NULL;
		$count = 0;
		$total = 0;
		for ($i=0; $i<count($result); $i++) {
			if ($codgastos != $result[$i]['codgastos']) {
				if ($codgastos != NULL && $count > 1) {
					$tpl->newBlock("total");
					$tpl->assign("total_gasto",number_format($subtotal,2,".",","));
				}
				
				$codgastos = $result[$i]['codgastos'];
				
				$tpl->newBlock("gasto");
				
				$subtotal = 0;
				$count = 0;
			}
			$tpl->newBlock("fila");
			$tpl->assign('i', $i);
			$tpl->assign('id', $result[$i]['id']);
			$tpl->assign("fecha",$result[$i]['fecha']);
			$tpl->assign("codgastos",$result[$i]['codgastos']);
			$tpl->assign("descripcion",$result[$i]['descripcion']);
			$tpl->assign("a_nombre",$result[$i]['a_nombre']);
			$tpl->assign("facturas",$result[$i]['facturas']);
			$tpl->assign("concepto", (($result[$i]['facturas'] > 0) ? "<br>" : "") . ($result[$i]['desc'] != "" ? $result[$i]['desc'] : $result[$i]['concepto']));
			$tpl->assign("importe",number_format($result[$i]['importe'],2,".",","));
			$tpl->assign("folio",$result[$i]['folio']);
			
			$subtotal += $result[$i]['importe'];
			$total += $result[$i]['importe'];
			
			$count++;
		}
		if ($codgastos != NULL && $count > 1) {
			$tpl->newBlock("total");
			$tpl->assign("total_gasto",number_format($subtotal,2,".",","));
		}
		$tpl->assign("listado_gastos.total_gastos",number_format($total,2,".",","));
	}
	
	if (count($cia) > 1)
		$tpl->newBlock("salto");
	if ($hojas == 1)
		$tpl->newBlock("salto");
}

$tpl->printToScreen();

$fin_gral = microtime_float();
$tiempo = $fin_gral - $inicio_gral;
//echo "Tiempo de ejecución: ".round($tiempo,3)." segundos";
?>