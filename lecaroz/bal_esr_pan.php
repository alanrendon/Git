<?php
// ESTADO DE RESULTADOS DE PANADERIAS
// Tablas 'compra_directa', 'hoja_dia_rost', 'movimiento_gastos', 'total_companias'
// Menu 'No definido'

//define ('IDSCREEN',2); // ID de pantalla

// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db3.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);

// --------------------------------- Validar acceso de usuario a la pantalla ---------------------------------
//$session->validar_pantalla(IDSCREEN);

// --------------------------------- Obtener información de la pantalla --------------------------------------
//$session->info_pantalla();

// --------------------------------- Generar pantalla --------------------------------------------------------

// --------------------------------- FUNCIONES ------------------------------------
/*function microtime_float() {
   list($usec, $sec) = explode(" ", microtime());
   return ((float)$usec + (float)$sec);
}*/

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

// Obtener datos de la compañía
if (!isset($_GET['compania']) && !isset($_GET['todas'])) {
	// Hacer un nuevo objeto TemplatePower
	$tpl = new TemplatePower( "./plantillas/header.tpl" );
	
	// Incluir el cuerpo del documento
	$tpl->assignInclude("body","./plantillas/bal/bal_esr_pan_con.tpl");
	$tpl->prepare();
	
	$tpl->assign(date("n",mktime(0,0,0,date("m")-1,1,date("Y"))),"selected");
	$tpl->assign("anio",date("Y"));
	
	// Seleccionar script para menu
	$tpl->newBlock("menu");
	if (isset($_SESSION['menu']))
		$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
	$tpl->gotoBlock("_ROOT");
	
	$tpl->printToScreen();
	die;
}

// Construir fecha inicial y fecha final
$dia = date("d");
$mes = $_GET['mes'];
$anio = $_GET['anio'];
$fecha1 = date("d/m/Y",mktime(0,0,0,$_GET['mes'],1,$_GET['anio']));
$fecha2 = date("d/m/Y",mktime(0,0,0,$_GET['mes']+1,0,$_GET['anio']));
$dias = date("d",mktime(0,0,0,$_GET['mes']+1,0,$_GET['anio']));


if (isset($_GET['todas'])) {
	$sql = "SELECT num_cia,nombre,nombre_corto FROM catalogo_companias WHERE num_cia < 100 ORDER BY num_cia ASC";
	//$sql = "SELECT num_cia,nombre,nombre_corto FROM catalogo_companias WHERE num_cia BETWEEN 2 AND 100 ORDER BY num_cia ASC";
	$nombre_cia = ejecutar_script($sql,$dsn);
}
else {
	$sql = "SELECT num_cia,nombre,nombre_corto FROM catalogo_companias WHERE num_cia = $_GET[compania]";
	$nombre_cia = ejecutar_script($sql,$dsn);
}

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/bal/bal_esr_pan.tpl" );
$tpl->prepare();

$inicio = microtime_float();
for ($f=0; $f<count($nombre_cia); $f++) {
	$cia = $nombre_cia[$f]['num_cia'];
	
	// Validar que se hayan actualizado los encargados
	if (!ejecutar_script("SELECT * FROM encargados WHERE num_cia = $cia AND mes = $mes AND anio = $anio LIMIT 1",$dsn))
		ejecutar_script("INSERT INTO encargados (num_cia,nombre_inicio,nombre_fin,mes,anio) SELECT num_cia,nombre_inicio,nombre_fin,$mes,$anio FROM encargados WHERE mes = ".($mes > 1 ? $mes -1 : 12)." AND anio = ".($mes > 1 ? $anio : $anio - 1),$dsn);
	
	// -------------------------------------------- TRAZAR REPORTE -------------------------------------------------------------------------
	$tpl->newBlock("reporte");
	
	// Totales de gastos
	$super_gran_total = 0;
	
	$sql = "SELECT DISTINCT ON (codgastos) * FROM catalogo_gastos JOIN movimiento_gastos USING (codgastos) WHERE num_cia=$cia AND fecha>='$fecha1' AND fecha<='$fecha2' AND codigo_edo_resultados=1 ORDER BY codgastos";
	$codigos = ejecutar_script($sql,$dsn);
	if ($codigos) {
		$gran_total = 0;
		for ($i=0; $i<count($codigos); $i++) {
			$sql = "SELECT sum(importe) FROM catalogo_gastos JOIN movimiento_gastos USING (codgastos) WHERE num_cia=$cia AND codgastos=".$codigos[$i]['codgastos']." AND fecha>='$fecha1' AND fecha<='$fecha2' AND codigo_edo_resultados=1";
			$result = ejecutar_script($sql,$dsn);
			$gran_total += $result[0]['sum'];
			$super_gran_total += $result[0]['sum'];
		}
	}
	
	$sql = "SELECT DISTINCT ON (codgastos) * FROM catalogo_gastos JOIN movimiento_gastos USING (codgastos) WHERE num_cia=$cia AND fecha>='$fecha1' AND fecha<='$fecha2' AND codigo_edo_resultados=2 ORDER BY codgastos";
	$codigos = ejecutar_script($sql,$dsn);
	if ($codigos) {
		//$tpl->newBlock("gastos_gral");
		$gran_total = 0;
		for ($i=0; $i<count($codigos); $i++) {
			$sql = "SELECT sum(importe) FROM catalogo_gastos JOIN movimiento_gastos USING (codgastos) WHERE num_cia=$cia AND codgastos=".$codigos[$i]['codgastos']." AND fecha>='$fecha1' AND fecha<='$fecha2' AND codigo_edo_resultados=2";
			$result = ejecutar_script($sql,$dsn);
			$gran_total += $result[0]['sum'];
			$super_gran_total += $result[0]['sum'];
		}
	}
	
	// Gastos de oficina
	$sql = "SELECT * FROM gastos_caja WHERE num_cia = $cia AND fecha >= '$fecha1' AND fecha <= '$fecha2' AND clave_balance = 'TRUE' ORDER BY fecha ASC";
	$gastos_cia = ejecutar_script($sql,$dsn);
	if ($gastos_cia) {
		$total_egreso  = 0;
		$total_ingreso = 0;
		
		for ($j=0; $j<count($gastos_cia); $j++) {
			if ($gastos_cia[$j]['tipo_mov'] == "f") {
				$total_egreso  += $gastos_cia[$j]['importe'];
			}
			else if ($gastos_cia[$j]['tipo_mov'] == "t") {
				$total_ingreso += $gastos_cia[$j]['importe'];
			}
		}
	}
	
	/**************************** NUEVO METODO PARA CALCULAR EXISTENCIAS (V.1) ****************************/
	if ($anio == 2005 && $mes < 4 && $cia != 21 && $cia != 29 && $cia != 69) {
		// Obtener saldos anteriores de hitorico_inventario
		$fecha_historico = date("d/m/Y",mktime(0,0,0,$mes,0,$anio));
		
		// Saldos anteriores
		$sql = "SELECT num_cia, codmp, nombre, existencia, precio_unidad, controlada, tipo FROM historico_inventario LEFT JOIN catalogo_mat_primas USING (codmp) LEFT JOIN control_avio USING (num_cia, codmp) WHERE";
		// Si es para una compañía en específico
		$sql .= " num_cia = $cia AND";
		//$sql .= " controlada = 'TRUE' AND fecha = '$fecha_historico' GROUP BY num_cia, codmp, nombre, existencia, precio_unidad, num_orden ORDER BY num_cia, num_orden";
		$sql .= " fecha = '$fecha_historico' AND codmp NOT IN (90) GROUP BY num_cia, codmp, nombre, existencia, precio_unidad, controlada, tipo, num_orden ORDER BY num_cia, num_orden";
		$saldo_ant = ejecutar_script($sql,$dsn);
		
		// Saldo actual
		$sql = "SELECT num_cia, fecha, codmp, nombre, descripcion, cod_turno, tipo_mov, cantidad, precio_unidad, total_mov FROM mov_inv_real JOIN catalogo_mat_primas USING (codmp) WHERE";
		// Si es para una compañía en específico
		$sql .= " num_cia = $cia AND";
		//$sql .= " controlada = 'TRUE' AND fecha BETWEEN '$fecha1' AND '$fecha2' ORDER BY num_cia, controlada DESC, tipo, codmp, fecha, tipo_mov";
		$sql .= " fecha BETWEEN '$fecha1' AND '$fecha2' AND codmp NOT IN (90) ORDER BY num_cia, controlada DESC, tipo, codmp, fecha, tipo_mov";
		$saldo = ejecutar_script($sql,$dsn);
		
		$nummp = count($saldo_ant);
		$nummov = count($saldo);
		
		// MP / Producción (Balance)
		$sql = "SELECT mp_pro, gas_pro FROM balances_pan WHERE num_cia = $cia AND mes = $mes AND anio = $anio";
		$mp_pro = ejecutar_script($sql,$dsn);
		
		$consumo       = array(1 => 0, 2 => 0, 3 => 0, 4 => 0, 8 => 0, 9 => 0, 10 => 0);	// Consumo por turno
		$consumo_mes   = 0;
		$no_controlado = array(1 => 0, 2 => 0, 3 => 0, 4 => 0, 8 => 0, 9 => 0, 10 => 0);	// No controlados por turno
		$pro_tur       = array(1 => 0, 2 => 0, 3 => 0, 4 => 0, 8 => 0, 9 => 0, 10 => 0);	// Producción por turno
		$total_pro     = 0;																	// Producción total
		$mer_tur       = array(1 => 0, 2 => 0, 3 => 0, 4 => 0, 8 => 0, 9 => 0, 10 => 0);	// Mercancias por turno
		$mercancias    = 0;																	// Total de mercancias
		$con_pro       = array(1 => 0, 2 => 0, 3 => 0, 4 => 0, 8 => 0, 9 => 0, 10 => 0);
		
		$gran_total_valores = 0;
		$gran_total_valores_entrada = 0;
		$gran_total_valores_salida = 0;
		
		// Produccion
		$sql = "SELECT cod_turnos, sum(imp_produccion) FROM produccion WHERE num_cia = $cia AND fecha BETWEEN '$fecha1' AND '$fecha2' GROUP BY cod_turnos ORDER BY cod_turnos";
		$produccion = ejecutar_script($sql,$dsn);
		
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
			
			$consumo_turno = array(1 => 0, 2 => 0, 3 => 0, 4 => 0, 8 => 0, 9 => 0, 10 => 0);	// Consumo por turno del producto
			$consumo_total_mp = 0;		// Consumo total de la materia prima
			$consumo_parcial_mp = 0;	// Consumo parcial de la materia prima
			$consumo_nc = 0;			// Consumo total de la materia prima no controlada
			$dif_fal = 0;
			
			for ($k = 0; $k < $nummov; $k++) {
				if ($saldo[$k]['num_cia'] == $saldo_ant[$j]['num_cia'] && $saldo[$k]['codmp'] == $saldo_ant[$j]['codmp']) {
					// Salidas
					if ($saldo[$k]['tipo_mov'] == "t") {
						$unidades -= $saldo[$k]['cantidad'];
						$valores -= $saldo[$k]['cantidad'] * $costo_promedio;
						
						$valores_salida  += $saldo[$k]['cantidad'] * $costo_promedio;
						
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
						
						$valores_entrada  += $saldo[$k]['cantidad'] * $precio_unidad;
						
						if ($unidades != 0)
							$costo_promedio = $valores / $unidades;
					}
				}
			}
			$gran_total_valores += $valores;
			$gran_total_valores_entrada += $valores_entrada;
			$gran_total_valores_salida += $valores_salida;
			
			// Si el producto tuvo movimientos en el mes, mostrar en pantalla
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
						else {
							$consumo_turno[3] += $dif_fal;
							$consumo[3] += $dif_fal;
						}
					}
				}
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
		
		// Materia prima no controlada
		$total_mp_nc = 0;
		foreach ($no_controlado as $key => $value) {
			$total_mp_nc += $value;
			
			// Sumar a consumos
			$consumo[$key] += $value;
			$consumo_mes += $value;
		}
		
		// Mercancias
		$sql = "SELECT sum(importe) FROM movimiento_gastos WHERE num_cia = $cia AND fecha BETWEEN '$fecha1' AND '$fecha2' AND codgastos IN (23, 9, 76)";
		$mer = ejecutar_script($sql,$dsn);
		// Gastos de caja de codigo 28 (ABARROTES)
		$abarrotes_julild_salida = ejecutar_script("SELECT sum(importe) FROM gastos_caja WHERE num_cia = $cia AND fecha BETWEEN '$fecha1' AND '$fecha2' AND cod_gastos = 28 AND tipo_mov = 'FALSE'",$dsn);
		$abarrotes_julild_entrada = ejecutar_script("SELECT sum(importe) FROM gastos_caja WHERE num_cia = $cia AND fecha BETWEEN '$fecha1' AND '$fecha2' AND cod_gastos = 28 AND tipo_mov = 'TRUE'",$dsn);
		$abarrotes_julild = $abarrotes_julild_salida[0]['sum'] - $abarrotes_julild_entrada[0]['sum'];
		// Mercancias (cálculo)
		$mercancias = $mer[0]['sum'] + $abarrotes_julild;
		$consumo_mes += $mercancias;
		// Desglozar mercancias
		foreach ($pro_tur as $key => $value)
			if ($key != 1 && $key != 2 && $key != 10) {
				@$porcentaje = ($pro_tur[$key] * 100) / ($total_pro - $pro_tur[0] - $pro_tur[1]);
				@$mer_tur[$key] = $mercancias * $porcentaje / 100;
				
				$consumo[$key] += $mer_tur[$key];
			}
		
		// Producción y Consumo / Producción
		foreach ($pro_tur as $key => $value)
			if ($value > 0 && $key <= 4)
				$tpl->assign("con_pro_" . $key, $value != 0 ? number_format($consumo[$key] / $value, 3, ".", ",") : "&nbsp;");
	}
	else {
		// Obtener saldos anteriores de hitorico_inventario
		$fecha_historico = date("d/m/Y",mktime(0,0,0,$mes,0,$anio));
		
		// Saldos anteriores
		$sql = "SELECT num_cia, codmp, nombre, existencia, precio_unidad, controlada, tipo FROM historico_inventario LEFT JOIN catalogo_mat_primas USING (codmp) LEFT JOIN control_avio USING (num_cia, codmp) WHERE";
		// Si es para una compañía en específico
		$sql .= " num_cia = $cia AND fecha = '$fecha_historico' AND codmp NOT IN (90) GROUP BY num_cia, codmp, nombre, existencia, precio_unidad, controlada, tipo, num_orden ORDER BY num_cia, num_orden";
		$saldo_ant = ejecutar_script($sql, $dsn);
		
		// Saldo actual
		$sql = "SELECT num_cia, fecha, codmp, nombre, descripcion, cod_turno, tipo_mov, cantidad, precio_unidad, total_mov FROM mov_inv_real JOIN catalogo_mat_primas USING (codmp) WHERE";
		// Si es para una compañía en específico
		$sql .= " num_cia = $cia AND fecha BETWEEN '$fecha1' AND '$fecha2' AND descripcion != 'DIFERENCIA INVENTARIO' AND codmp NOT IN (90) ORDER BY num_cia, controlada DESC, tipo, codmp, fecha, tipo_mov, cantidad DESC";
		$saldo = ejecutar_script($sql, $dsn);
		
		// Diferencias del mes
		$sql = "SELECT num_cia, fecha, codmp, nombre, descripcion, cod_turno, tipo_mov, cantidad, precio_unidad, total_mov FROM mov_inv_real JOIN catalogo_mat_primas USING (codmp) WHERE";
		// Si es para una compañía en específico
		$sql .= " num_cia = $cia AND fecha BETWEEN '$fecha1' AND '$fecha2' AND descripcion = 'DIFERENCIA INVENTARIO' AND codmp NOT IN (90) ORDER BY num_cia, controlada DESC, tipo, codmp, fecha, tipo_mov";
		$difInv = ejecutar_script($sql, $dsn);
		
		$nummp = count($saldo_ant);
		$nummov = count($saldo);
		
		// MP / Producción (Balance)
		$sql = "SELECT mp_pro, gas_pro FROM balances_pan WHERE num_cia = $cia AND mes = $mes AND anio = $anio";
		$mp_pro = ejecutar_script($sql, $dsn);
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
		
		$gran_total_valores = 0;
		$gran_total_valores_entrada = 0;
		$gran_total_valores_salida = 0;
		
		// Produccion
		$sql = "SELECT cod_turnos, sum(imp_produccion) FROM produccion WHERE num_cia = $cia AND fecha BETWEEN '$fecha1' AND '$fecha2' GROUP BY cod_turnos ORDER BY cod_turnos";
		$produccion = ejecutar_script($sql, $dsn);
		
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
			$fresa = 0;
			
			$arrastre = FALSE;	// Flag. Indica si debe arrastrarse el costo promedio
			
			// Calcular el costo de la diferencia apartir de las entradas
			$costo_dif = costo_dif($saldo, $cia, $codmp, $costo_promedio);
			
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
								$proximo_costo = buscar_fac($saldo, $difInv, $costo_dif, $k, $cia, $codmp, $unidades);
							
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
						
						if ($cia == 58 && $codmp == 291)
							$fresa += $saldo[$k]['cantidad'] * $precio_unidad;
						
						$unidades_ant = $unidades;
					}
				}
			}
			// Buscar diferencia
			$idDif = buscar_dif($difInv, $cia, $codmp);
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
					
					if ($cia == 58 && $codmp == 291)
						$fresa = $difInv[$idDif]['cantidad'] * $precio_unidad;
				}
			}
			$gran_total_valores += $unidades * $costo_promedio;
			$gran_total_valores_entrada += $valores_entrada;
			$gran_total_valores_salida += $valores_salida;
			
			// Si el producto tuvo movimientos en el mes, mostrar en pantalla
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
					$no_controlado[3] += $pro_tur[4] > 0 ? 0.15 * $consumo_nc : $pro_tur[3] > 0 ? 0.95 * $consumo_nc : 0;
					$no_controlado[4] += $pro_tur[4] > 0 ? 0.80 * $consumo_nc : 0;
				}
				// Material de empaque
				else {
					$no_controlado[3] += $pro_tur[4] > 0 ? $consumo_nc * 0.20 : $pro_tur[3] > 0 ? $consumo_nc * 0.90 : 0;
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
		$sql = "SELECT sum(importe) FROM movimiento_gastos WHERE num_cia = $cia AND fecha BETWEEN '$fecha1' AND '$fecha2' AND codgastos IN (23, 9, 76)";
		$mer = ejecutar_script($sql,$dsn);
		// Gastos de caja de codigo 28 (ABARROTES)
		$abarrotes_julild_salida = ejecutar_script("SELECT sum(importe) FROM gastos_caja WHERE num_cia = $cia AND fecha BETWEEN '$fecha1' AND '$fecha2' AND cod_gastos = 28 AND tipo_mov = 'FALSE'",$dsn);
		$abarrotes_julild_entrada = ejecutar_script("SELECT sum(importe) FROM gastos_caja WHERE num_cia = $cia AND fecha BETWEEN '$fecha1' AND '$fecha2' AND cod_gastos = 28 AND tipo_mov = 'TRUE'",$dsn);
		$abarrotes_julild = $abarrotes_julild_salida[0]['sum'] - $abarrotes_julild_entrada[0]['sum'];
		// Mercancias (cálculo)
		$mercancias = $mer[0]['sum'] + $abarrotes_julild;
		$consumo_mes += $mercancias;
		// Desglozar mercancias
		foreach ($pro_tur as $key => $value)
			if ($key != 1 && $key != 2 && $key != 10) {
				@$porcentaje = ($pro_tur[$key] * 100) / ($total_pro - $pro_tur[0] - $pro_tur[1]);
				@$mer_tur[$key] = $mercancias * $porcentaje / 100;
				
				$consumo[$key] += $mer_tur[$key];
			}
		
		// Producción y Consumo / Producción
		foreach ($pro_tur as $key => $value)
			if ($value > 0 && $key <= 4)
				$tpl->assign("con_pro_" . $key, $value != 0 ? number_format($consumo[$key] / $value, 3, ".", ",") : "&nbsp;");
	}
	
	/********************************* PRIMER BLOQUE ********************************************/
	// Barredura
	$sql = "SELECT SUM(importe) FROM barredura WHERE num_cia=$cia AND fecha_pago BETWEEN '$fecha1' AND '$fecha2'";
	$temp = ejecutar_script($sql,$dsn);
	$barredura = ($temp[0]['sum'] > 0)?$temp[0]['sum']:0;
	// Pastillaje
	$pastillaje = ejecutar_script("SELECT SUM(pastillaje) FROM total_panaderias WHERE num_cia = $cia AND fecha >= '$fecha1' AND fecha <= '$fecha2'",$dsn);
	// Abono Empleados
	$abono_empleados = ejecutar_script("SELECT SUM(importe) FROM prestamos WHERE num_cia = $cia AND fecha >= '$fecha1' AND fecha <= '$fecha2' AND tipo_mov = 'TRUE'",$dsn);
	// Otros
	$otros = ejecutar_script("SELECT SUM(otros) FROM total_panaderias WHERE num_cia = $cia AND fecha >= '$fecha1' AND fecha <= '$fecha2'",$dsn);
	
	if ($mes == 1 && $anio == 2005)// Para balances de Enero de 2005 que no incluian abono empleados
		$otros[0]['sum'] = $otros[0]['sum'] - $barredura > 0 ? $otros[0]['sum'] - $barredura : $otros[0]['sum'];
	else
		$otros[0]['sum'] = $otros[0]['sum'] - $abono_empleados[0]['sum'] - $barredura;
	
	// Total Otros
	$total_otros = $pastillaje[0]['sum'] + $otros[0]['sum'] + $barredura + $abono_empleados[0]['sum'];
	// Abono Reparto
	$abono_reparto = ejecutar_script("SELECT SUM(abono) FROM mov_expendios WHERE num_cia = $cia AND fecha >= '$fecha1' AND fecha <= '$fecha2'",$dsn);
	// Errores
	$errores = ejecutar_script("SELECT SUM(am_error + pm_error) FROM captura_efectivos WHERE num_cia = $cia AND fecha >= '$fecha1' AND fecha <= '$fecha2'",$dsn);
	// Venta en puerta
	$temp = ejecutar_script("SELECT SUM(venta_puerta) FROM total_panaderias WHERE num_cia = $cia AND fecha >= '$fecha1' AND fecha <= '$fecha2'",$dsn);
	$venta_puerta = $temp[0]['sum'] + $errores[0]['sum'];
	// VENTAS NETAS
	$ventas_netas = $venta_puerta + $total_otros + $abono_reparto[0]['sum'] - $errores[0]['sum'];
	
	/********************************* SEGUNDO BLOQUE ********************************************/
	// Inventario Anterior
	if ($mes == 1 && $anio == 2005)
		$inv_ant = ejecutar_script("SELECT sum(precio_unidad*existencia) FROM historico_inventario WHERE num_cia = $cia AND codmp NOT IN (90) AND fecha = '$fecha_historico'",$dsn);
	else
		$inv_ant = ejecutar_script("SELECT inv_act AS sum FROM balances_pan WHERE num_cia = $cia AND mes = ".($mes == 1 ? 12 : $mes - 1)." AND anio = ".($mes == 1 ? $anio - 1 : $anio),$dsn);
	// Compras
	$compras = $gran_total_valores_entrada;
	
	// Hacerlo para la fresa del mes de abril en adelante
	if ($cia == 58 && $mes >= 4)
		$compras -= $fresa;
	
	// Mercancias
	//$mercancias = ejecutar_script("SELECT sum(importe) FROM movimiento_gastos WHERE num_cia = $cia AND fecha >= '$fecha1' AND fecha <= '$fecha2' AND codgastos = 23",$dsn);
	// Mercancias, vino y leche
	//$abarrotes_julild_salida = ejecutar_script("SELECT sum(importe) FROM gastos_caja WHERE num_cia = $cia AND fecha BETWEEN '$fecha1' AND '$fecha2' AND cod_gastos = 28 AND tipo_mov = 'FALSE'",$dsn);
	//$abarrotes_julild_entrada = ejecutar_script("SELECT sum(importe) FROM gastos_caja WHERE num_cia = $cia AND fecha BETWEEN '$fecha1' AND '$fecha2' AND cod_gastos = 28 AND tipo_mov = 'TRUE'",$dsn);
	
	//$abarrotes_julild = $abarrotes_julild_salida[0]['sum'] - $abarrotes_julild_entrada[0]['sum'];
	
	//$mercancias = ejecutar_script("SELECT sum(importe) FROM movimiento_gastos WHERE num_cia = $cia AND fecha >= '$fecha1' AND fecha <= '$fecha2' AND codgastos IN (23,9,76)",$dsn);
	//$mercancias[0]['sum'] += $abarrotes_julild;
	
	// Inventario Actual
	$inv_act = $gran_total_valores;
	
	// Materia Prima Utilizada
	$mat_prima_utilizada = $inv_ant[0]['sum'] + $compras + $mercancias/*[0]['sum']*/ - $gran_total_valores;
	
	// Mano de Obra
	$sql = "SELECT SUM(raya_pagada) FROM total_produccion WHERE numcia = $cia AND fecha_total >= '$fecha1' AND fecha_total <= '$fecha2'";
	$temp = ejecutar_script($sql,$dsn);
	$mano_obra = $temp[0]['sum'];
	
	// Panaderos
	$sql = "SELECT SUM(importe) FROM movimiento_gastos WHERE num_cia = $cia AND fecha >= '$fecha1' AND fecha <= '$fecha2' AND codgastos = 3";
	$temp = ejecutar_script($sql,$dsn);
	$panaderos = $temp[0]['sum'];
	
	// Gastos de Fabricación
	$sql = "SELECT SUM(importe) FROM catalogo_gastos JOIN movimiento_gastos USING (codgastos) WHERE num_cia=$cia AND fecha>='$fecha1' AND fecha<='$fecha2' AND codigo_edo_resultados=1 GROUP BY num_cia";
	$temp = ejecutar_script($sql,$dsn);
	$gastos_fab = $temp[0]['sum'];
	
	// Costos de Elaboración
	$costo_produccion = $mat_prima_utilizada + $mano_obra + $panaderos + $gastos_fab;
	
	// UTILIDAD BRUTA
	$utilidad_bruta = $ventas_netas - $costo_produccion;
	
	/******************************** TERCER BLOQUE *************************************************/
	// Pan Comprado
	$sql = "SELECT SUM(importe) + SUM(importe)*((SELECT porcentaje_anterior FROM porcentaje_pan_comprado WHERE num_cia=$cia)/100) AS pan_comprado FROM movimiento_gastos WHERE num_cia=$cia AND fecha>='$fecha1' AND fecha<='$fecha2' AND codgastos=5 AND captura='FALSE'";
	$temp = ejecutar_script($sql,$dsn);
	$pan_comprado = -1 * $temp[0]['pan_comprado'] / 1.25;
	
	// Gastos Generales
	$sql = "SELECT SUM(importe) FROM catalogo_gastos JOIN movimiento_gastos USING (codgastos) WHERE num_cia=$cia AND fecha>='$fecha1' AND fecha<='$fecha2' AND codigo_edo_resultados=2 AND codgastos NOT IN (9,76) GROUP BY num_cia";
	$temp = ejecutar_script($sql,$dsn);
	$gastos_gral = -1 * $temp[0]['sum'];
	
	// Gastos de Caja
	$egresos = ejecutar_script("SELECT sum(importe) FROM gastos_caja WHERE num_cia=$cia AND fecha >= '$fecha1' AND fecha <= '$fecha2' AND tipo_mov = 'FALSE' AND clave_balance = 'TRUE'",$dsn);
	$ingresos = ejecutar_script("SELECT sum(importe) FROM gastos_caja WHERE num_cia=$cia AND fecha >= '$fecha1' AND fecha <= '$fecha2' AND tipo_mov = 'TRUE' AND clave_balance = 'TRUE'",$dsn);
	$gastos_caja = $ingresos[0]['sum'] - $egresos[0]['sum'];
	
	// Reservas
	if ($result = ejecutar_script("SELECT SUM(importe) FROM reservas_cias WHERE num_cia=$cia AND fecha='$fecha1'",$dsn))
		$reservas = -$result[0]['sum'];
	else
		$reservas = "";
	
	// Gastos por otras compañías
	$cia_gasto_egreso = ejecutar_script("SELECT sum(monto) FROM gastos_otras_cia WHERE num_cia_egreso = $cia AND fecha >= '$fecha1' AND fecha <= '$fecha2'",$dsn);
	// Gastos por otras compañías
	$cia_gasto_ingreso = ejecutar_script("SELECT sum(monto) FROM gastos_otras_cia WHERE num_cia_ingreso = $cia AND fecha >= '$fecha1' AND fecha <= '$fecha2'",$dsn);
	//$cia_recibe = ejecutar_script("SELECT sum(monto) FROM gastos_otras_cia WHERE num_cia_ingreso=$cia AND fecha >= '$fecha1' AND fecha <= '$fecha2' AND tipo_mov='TRUE'",$dsn);
	$gastos_otros = $cia_gasto_egreso[0]['sum'] - $cia_gasto_ingreso[0]['sum'];
	
	// Gastos totales
	
	$gastos_totales = $pan_comprado + $gastos_gral + $gastos_caja + $reservas + $gastos_otros;
	
	// Ingresos extraordinarios
	if (empty($_GET['no_gastos'])) {
		$sql = "SELECT SUM(importe) FROM estado_cuenta WHERE num_cia = $cia AND fecha BETWEEN '$fecha1' AND '$fecha2' AND cod_mov = 18";	// Los ingresos extraordinarios son todos los movimientos con codigo 18 Devolución de Impuesto
		$temp = ejecutar_script($sql,$dsn);
		$ingresos_ext = $temp[0]['sum'];
	}
	else
		$ingresos_ext = 0;
	
	// Utilidad Neta
	$utilidad_neta = $gastos_totales + $ingresos_ext + $utilidad_bruta;
	
	// Insertar o actualizar historico
	$sql = "SELECT SUM(ctes) FROM captura_efectivos WHERE num_cia = $cia AND fecha >= '$fecha1' AND fecha <= '$fecha2'";
	$temp = ejecutar_script($sql,$dsn);
	$clientes = ($temp[0]['sum'] > 0)?$temp[0]['sum']:"NULL";
	$prom = ($clientes > 0)?$venta_puerta/$clientes:"NULL";
	/*if (existe_registro("historico",array("num_cia","mes","anio"),array($cia,$mes,$anio),$dsn))
		ejecutar_script("UPDATE historico SET utilidad=$utilidad_neta,venta=".($venta_puerta - $errores[0]['sum']).",reparto=".(($abono_reparto[0]['sum'] != 0)?$abono_reparto[0]['sum']:0).",clientes=$clientes,gasto_ext='".(($ingresos_ext != 0)?"TRUE":"FALSE")."',ingresos=".(($ingresos_ext != 0)?$ingresos_ext:0)." WHERE num_cia=$cia AND mes=$mes AND anio=$anio",$dsn);
	else
		ejecutar_script("INSERT INTO historico (num_cia,mes,anio,utilidad,venta,reparto,clientes,gasto_ext,ingresos) VALUES ($cia,$mes,$anio,$utilidad_neta,".$venta_puerta.",".(($abono_reparto[0]['sum'] != 0)?$abono_reparto[0]['sum']:0).",$clientes,'".($ingresos_ext != 0?"TRUE":"FALSE")."',".($ingresos_ext != 0?$ingresos_ext:0).")",$dsn);*/
	
	/*************************************** QUINTO BLOQUE **************************************/
	// PRODUCCION TOTAL
	$sql = "SELECT SUM(total_produccion) FROM total_produccion WHERE numcia = $cia AND fecha_total >= '$fecha1' AND fecha_total <= '$fecha2'";
	$temp = ejecutar_script($sql,$dsn);
	$produccion_total = $temp[0]['sum'];
	
	// GANANCIA
	$sql = "SELECT SUM(pan_p_venta-pan_p_expendio) FROM mov_expendios WHERE num_cia = $cia AND fecha >= '$fecha1' AND fecha <= '$fecha2'";
	$temp = ejecutar_script($sql,$dsn);
	$ganancia = $temp[0]['sum'];
	
	// % GANANCIA
	$temp = ejecutar_script("SELECT SUM(pan_p_venta) FROM mov_expendios WHERE num_cia = $cia AND fecha >= '$fecha1' AND fecha <= '$fecha2'",$dsn);
	if ($temp[0]['sum'] > 0) {
		$sql = "SELECT (SUM(pan_p_venta)-SUM(pan_p_expendio))*100/SUM(pan_p_venta) AS porc FROM mov_expendios WHERE num_cia = $cia AND fecha >= '$fecha1' AND fecha <= '$fecha2'";
		$temp = ejecutar_script($sql,$dsn);
		$porcentaje_ganancia = ($temp)?$temp[0]['porc']:0;
	}
	else
		$porcentaje_ganancia = 0;
	
	// FALTANTE DE PAN
	$faltante_pan = 0;
	for ($d=1; $d<=$dias; $d++) {
		$pro = ejecutar_script("SELECT SUM(total_produccion) FROM total_produccion WHERE numcia=$cia AND fecha_total='$d/$_GET[mes]/$_GET[anio]'",$dsn);
		$pc = ejecutar_script("SELECT SUM(importe) + SUM(importe)*((SELECT porcentaje FROM porcentaje_pan_comprado WHERE num_cia=$cia)/100) AS pan_comprado FROM movimiento_gastos WHERE num_cia=$cia AND fecha='$d/$_GET[mes]/$_GET[anio]' AND codgastos=5 AND captura='FALSE'",$dsn);
		$prueba_pan_ant = ejecutar_script("SELECT SUM(importe) FROM prueba_pan WHERE num_cia=$cia AND fecha='".date("d/m/Y",mktime(0,0,0,$_GET['mes'],$d-1,$_GET['anio']))."'",$dsn);
		$total_pan = $pro[0]['sum'] + $pc[0]['pan_comprado'] + $prueba_pan_ant[0]['sum'];
		$pan_quebrado = $pro[0]['sum'] * 0.02;
		$vp = ejecutar_script("SELECT SUM(venta_puerta) FROM total_panaderias WHERE num_cia=$cia AND fecha='$d/$_GET[mes]/$_GET[anio]'",$dsn);
		$reparto = ejecutar_script("SELECT SUM(pan_p_venta) FROM mov_expendios WHERE num_cia=$cia AND fecha='$d/$_GET[mes]/$_GET[anio]'",$dsn);
		$desc_pastel = ejecutar_script("SELECT SUM(desc_pastel) FROM captura_efectivos WHERE num_cia=$cia AND fecha='$d/$_GET[mes]/$_GET[anio]'",$dsn);
		$sobrante = $total_pan - $vp[0]['sum'] - $reparto[0]['sum'] - $pan_quebrado - $desc_pastel[0]['sum'];
		$prueba_pan = ejecutar_script("SELECT SUM(importe) FROM prueba_pan WHERE num_cia=$cia AND fecha='$d/$_GET[mes]/$_GET[anio]'",$dsn);
		$faltante = $prueba_pan[0]['sum'] - $sobrante;
		$faltante_pan += $faltante;
	}
	$faltante_pan = $faltante_pan;
	
	// DEVOLUCIONES
	$sql = "SELECT SUM(devolucion) FROM mov_expendios WHERE num_cia = $cia AND fecha >= '$fecha1' AND fecha <= '$fecha2'";
	$temp = ejecutar_script($sql,$dsn);
	$devoluciones = $temp[0]['sum'];
	
	// REZAGO INICIAL
	$sql = "SELECT SUM(rezago) FROM mov_expendios WHERE num_cia = $cia AND fecha = '".date("d/m/Y",mktime(0,0,0,$mes,0,$anio))."'";
	$temp = ejecutar_script($sql,$dsn);
	$rezago_inicial = $temp[0]['sum'];
	
	// REZAGO FINAL
	$sql = "SELECT SUM(rezago) FROM mov_expendios WHERE num_cia = $cia AND fecha = '$fecha2'";
	$temp = ejecutar_script($sql,$dsn);
	$rezago_final = $temp[0]['sum'];
	
	// SUBIO REZAGO
	$subio_rezago = $rezago_final - $rezago_inicial;
	
	// EFECTIVO
	$sql = "SELECT SUM(efectivo) FROM total_panaderias WHERE num_cia=$cia AND fecha>='$fecha1' AND fecha<='$fecha2'";
	$temp = ejecutar_script($sql,$dsn);
	$efectivo = $temp[0]['sum'];
	
	// ENCARGADOS
	$sql = "SELECT * FROM encargados WHERE num_cia=$cia AND mes=$mes AND anio=$anio";
	$encargado = ejecutar_script($sql,$dsn);
	
	/*************************************** CUARTO BLOQUE **************************************/
	$temp = ejecutar_script("SELECT sum(importe) FROM catalogo_gastos JOIN movimiento_gastos USING (codgastos) WHERE num_cia=$cia AND codgastos=90 AND fecha>='$fecha1' AND fecha<='$fecha2' AND codigo_edo_resultados=1",$dsn);
	$_gas = ($temp[0]['sum'] > 0)?$temp[0]['sum']:0;
	
	// Obtener descuentos de gas
	$des_gas = ejecutar_script("SELECT sum(importe) FROM gastos_caja WHERE num_cia = $cia AND cod_gastos = 92 AND fecha BETWEEN '$fecha1' AND '$fecha2' AND tipo_mov = 'TRUE'",$dsn);
	
	@$mp_vtas = $mat_prima_utilizada / ($ventas_netas + $pan_comprado);
	@$utilidad_produccion = $utilidad_neta / $produccion_total;
	@$mp_produccion = $mat_prima_utilizada / $produccion_total;
	@$gas_produccion = ($_gas - $des_gas[0]['sum']) / $produccion_total;
	
	// Obtener ultimo gas_pro
	$ult_gas_pro = ejecutar_script("SELECT gas_pro FROM balances_pan WHERE num_cia = $cia AND mes = ".($mes > 1 ? $mes - 1 : 12)." AND anio = ".($mes > 1 ? $anio : $anio - 1),$dsn);
	$tpl->assign("dif_mes_gas", $gas_produccion - $ult_gas_pro[0]['gas_pro'] != 0 ? "<font color=\"#" . ($gas_produccion - $ult_gas_pro[0]['gas_pro'] > 0 ? "FF0000\">Subio" : "0000FF\">Bajo") . "</font>" : "=");
	
	// Historico anterior
	$historico_actual = ejecutar_script("SELECT * FROM historico WHERE num_cia = $cia AND mes <= $mes AND anio = $anio ORDER BY mes",$dsn);
	$historico_anterior = ejecutar_script("SELECT * FROM historico WHERE num_cia = $cia AND anio = ".($anio-1)." ORDER BY mes",$dsn);
	
	$temp = ejecutar_script("SELECT utilidad,ingresos FROM historico WHERE num_cia = $cia AND anio = ".($anio-1)." AND mes = $mes",$dsn);
	$utilidad_anterior = ($temp)?$temp[0]['utilidad']:0;
	$ingresos_anterior = ($temp)?$temp[0]['ingresos']:0;
	$utilidad_anterior = $utilidad_anterior - $ingresos_anterior;
	
	//$nombre_cia = obtener_registro("catalogo_companias",array("num_cia"),array($cia),"","",$dsn);
	$tpl->assign("num_cia",$cia);
	$tpl->assign("nombre_cia",$nombre_cia[$f]['nombre']);
	$tpl->assign("nombre_corto",$nombre_cia[$f]['nombre_corto']);
	$tpl->assign("anio",$anio);
	$tpl->assign("mes",mes_escrito($mes));
	
	/***************************** PRIMER BLOQUE **********************************/
	if ($venta_puerta > 0)
		$tpl->assign("venta_puerta","<font color='#0000FF'>".number_format($venta_puerta,2,".",",")."</font>");
	else if ($venta_puerta < 0)
		$tpl->assign("venta_puerta","<font color='#FF0000'>".number_format($venta_puerta,2,".",",")."</font>");
	
	$tpl->assign("barredura",$barredura != 0 ? "<font color='#".(($barredura > 0)?"0000FF":"FF0000")."'>".number_format($barredura,2,".",",")."</font>" : "");
	
	if ($pastillaje[0]['sum'] > 0)
		$tpl->assign("pastillaje","<font color='#0000FF'>".number_format($pastillaje[0]['sum'],2,".",",")."</font>");
	else if ($pastillaje[0]['sum'] < 0)
		$tpl->assign("pastillaje","<font color='#FF0000'>".number_format($pastillaje[0]['sum'],2,".",",")."</font>");
	
	if ($abono_empleados[0]['sum'] > 0)
		$tpl->assign("abono_emp","<font color='#0000FF'>".number_format($abono_empleados[0]['sum'],2,".",",")."</font>");
	else if ($abono_empleados[0]['sum'] < 0)
		$tpl->assign("abono_emp","<font color='#FF0000'>".number_format($abono_empleados[0]['sum'],2,".",",")."</font>");
	
	if ($otros[0]['sum'] > 0)
		$tpl->assign("otros","<font color='#0000FF'>".number_format($otros[0]['sum'],2,".",",")."</font>");
	else if ($otros[0]['sum'] < 0)
		$tpl->assign("otros","<font color='#FF0000'>".number_format($otros[0]['sum'],2,".",",")."</font>");
	
	if ($total_otros > 0)
		$tpl->assign("total_otros","<font color='#0000FF'>".number_format($total_otros,2,".",",")."</font>");
	else if ($total_otros < 0)
		$tpl->assign("total_otros","<font color='#FF0000'>".number_format($total_otros,2,".",",")."</font>");
	
	if ($abono_reparto[0]['sum'] > 0)
		$tpl->assign("abono_reparto","<font color='#0000FF'>".number_format($abono_reparto[0]['sum'],2,".",",")."</font>");
	else if ($abono_reparto[0]['sum'] < 0)
		$tpl->assign("abono_reparto","<font color='#FF0000'>".number_format($abono_reparto[0]['sum'],2,".",",")."</font>");
	
	if ($errores[0]['sum'] > 0)
		$tpl->assign("errores","<font color='#0000FF'>".number_format($errores[0]['sum'],2,".",",")."</font>");
	else if ($errores[0]['sum'] < 0)
		$tpl->assign("errores","<font color='#FF0000'>".number_format($errores[0]['sum'],2,".",",")."</font>");
	
	if ($ventas_netas > 0)
		$tpl->assign("ventas_netas","<font color='#0000FF'>".number_format($ventas_netas,2,".",",")."</font>");
	else if ($ventas_netas < 0)
		$tpl->assign("ventas_netas","<font color='#FF0000'>".number_format($ventas_netas,2,".",",")."</font>");
	
	/***************************** SEGUNDO BLOQUE **********************************/
	if ($inv_ant[0]['sum'] > 0)
		$tpl->assign("inventario_anterior","<font color='#0000FF'>".number_format($inv_ant[0]['sum'],2,".",",")."</font>");
	else if ($inv_ant[0]['sum'] < 0)
		$tpl->assign("inventario_anterior","<font color='#FF0000'>".number_format($inv_ant[0]['sum'],2,".",","));
	
	if ($compras > 0)
		$tpl->assign("compras","<font color='#0000FF'>".number_format($compras,2,".",",")."</font>");
	else if ($compras < 0)
		$tpl->assign("compras","<font color='#FF0000'>".number_format($compras,2,".",",")."</font>");
	
	if ($mercancias/*[0]['sum']*/ > 0)
		$tpl->assign("mercancias","<font color='#0000FF'>".number_format($mercancias/*[0]['sum']*/,2,".",",")."</font>");
	else if ($mercancias/*[0]['sum']*/ < 0)
		$tpl->assign("mercancias","<font color='#FF0000'>".number_format($mercancias/*[0]['sum']*/,2,".",","));
	
	if ($gran_total_valores > 0)
		$tpl->assign("inventario_actual","<font color='#0000FF'>".number_format($gran_total_valores,2,".",",")."</font>");
	else if ($gran_total_valores < 0)
		$tpl->assign("inventario_actual","<font color='#FF0000'>".number_format($gran_total_valores,2,".",",")."</font>");
	
	if ($mat_prima_utilizada > 0)
		$tpl->assign("mat_prima_utilizada","<font color='#0000FF'>".number_format($mat_prima_utilizada,2,".",",")."</font>");
	else if ($mat_prima_utilizada < 0)
		$tpl->assign("mat_prima_utilizada","<font color='#FF0000'>".number_format($mat_prima_utilizada,2,".",",")."</font>");
	
	if ($mano_obra > 0)
		$tpl->assign("mano_obra","<font color='#0000FF'>".number_format($mano_obra,2,".",",")."</font>");
	else if ($mano_obra < 0)
		$tpl->assign("mano_obra","<font color='#FF0000'>".number_format($mano_obra,2,".",",")."</font>");
	
	if ($panaderos > 0)
		$tpl->assign("panaderos","<font color='#0000FF'>".number_format($panaderos,2,".",",")."</font>");
	else if ($panaderos < 0)
		$tpl->assign("panaderos","<font color='#FF0000'>".number_format($panaderos,2,".",",")."</font>");
	
	if ($gastos_fab > 0)
		$tpl->assign("gastos_fabricacion","<font color='#0000FF'>".number_format($gastos_fab,2,".",",")."</font>");
	else if ($gastos_fab < 0)
		$tpl->assign("gastos_fabricacion","<font color='#FF0000'>".number_format($gastos_fab,2,".",",")."</font>");
	
	if ($costo_produccion > 0)
		$tpl->assign("costo_produccion","<font color='#0000FF'>".number_format($costo_produccion,2,".",",")."</font>");
	else if ($costo_produccion < 0)
		$tpl->assign("costo_produccion","<font color='#FF0000'>".number_format($costo_produccion,2,".",",")."</font>");
	
	if ($utilidad_bruta > 0)
		$tpl->assign("utilidad_bruta","<font color='#0000FF'>".number_format($utilidad_bruta,2,".",",")."</font>");
	else if ($utilidad_bruta < 0)
		$tpl->assign("utilidad_bruta","<font color='#FF0000'>".number_format($utilidad_bruta,2,".",","));
	
	/*************************************** TERCER BLOQUE ************************************************/
	if ($pan_comprado > 0)
		$tpl->assign("pan_comprado","<font color='#0000FF'>".number_format($pan_comprado,2,".",",")."</font>");
	else if ($pan_comprado < 0)
		$tpl->assign("pan_comprado","<font color='#FF0000'>".number_format($pan_comprado,2,".",",")."</font>");
	
	if ($gastos_gral > 0)
		$tpl->assign("gastos_generales","<font color='#0000FF'>".number_format($gastos_gral,2,".",",")."</font>");
	else if ($gastos_gral < 0)
		$tpl->assign("gastos_generales","<font color='#FF0000'>".number_format($gastos_gral,2,".",",")."</font>");
	
	if ($gastos_caja > 0)
		$tpl->assign("gastos_caja","<font color='#0000FF'>".number_format($gastos_caja,2,".",",")."</font>");
	else if ($gastos_caja < 0)
		$tpl->assign("gastos_caja","<font color='#FF0000'>".number_format($gastos_caja,2,".",",")."</font>");
	
	if ($reservas > 0)
		$tpl->assign("reserva_aguinaldos","<font color='#0000FF'>".number_format($reservas,2,".",".")."</font>");
	else if ($reservas < 0)
		$tpl->assign("reserva_aguinaldos","<font color='#FF0000'>".number_format($reservas,2,".",".")."</font>");
	
	if ($gastos_otros > 0)
		$tpl->assign("gastos_otras_cias","<font color='#0000FF'>".number_format($gastos_otros,2,".",",")."</font>");
	else if ($gastos_otros < 0)
		$tpl->assign("gastos_otras_cias","<font color='#FF0000'>".number_format($gastos_otros,2,".",",")."</font>");
	
	if ($gastos_totales > 0)
		$tpl->assign("total_gastos","<font color='#0000FF'>".number_format($gastos_totales,2,".",",")."</font>");
	else if ($gastos_totales < 0)
		$tpl->assign("total_gastos","<font color='#FF0000'>".number_format($gastos_totales,2,".",",")."</font>");
	
	if ($ingresos_ext > 0)
		$tpl->assign("ingresos_ext","<font color='#0000FF'>".number_format($ingresos_ext,2,".",",")."</font>");
	else if ($ingresos_ext < 0)
		$tpl->assign("ingresos_ext","<font color='#FF0000'>".number_format($ingresos_ext,2,".",",")."</font>");
	
	if ($utilidad_neta > 0)
		$tpl->assign("utilidad_mes","<font color='#0000FF'>".number_format($utilidad_neta,2,".",",")."</font>&nbsp;&nbsp;".(($utilidad_neta-$utilidad_anterior-$ingresos_ext == 0)?"":(($utilidad_neta-$utilidad_anterior-$ingresos_ext > 0)?"MEJORO":"EMPEORO")));
	else if ($utilidad_neta < 0)
		$tpl->assign("utilidad_mes","<font color='#FF0000'>".number_format($utilidad_neta,2,".",",")."</font>&nbsp;&nbsp;".(($utilidad_neta-$utilidad_anterior-$ingresos_ext == 0)?"":(($utilidad_neta-$utilidad_anterior-$ingresos_ext > 0)?"MEJORO":"EMPEORO")));
	
	/********************************** CUARTO BLOQUE *******************************************/
	$tpl->assign("mp_vtas","<font color='#".(($mp_vtas > 0)?"0000FF":"FF0000")."'>".number_format($mp_vtas,3,".",",")."</font>");
	$tpl->assign("utilidad_produccion","<font color='#".(($utilidad_produccion > 0)?"0000FF":"FF0000")."'>".number_format($utilidad_produccion,3,".",",")."</font>");
	$tpl->assign("mp_produccion","<font color='#".(($mp_produccion > 0)?"0000FF":"FF0000")."'>".number_format($mp_produccion,3,".",",")."</font>");
	$tpl->assign("gas_produccion","<font color='#".(($gas_produccion > 0)?"0000FF":"FF0000")."'>".number_format($gas_produccion,5,".",",")."</font>");
	
	
	/********************************** QUINTO BLOQUE *******************************************/
	if ($produccion_total > 0)
		$tpl->assign("produccion_total","<font color='#0000FF'>".number_format($produccion_total,2,".",",")."</font>");
	else if ($produccion_total < 0)
		$tpl->assign("produccion_total","<font color='#FF0000'>".number_format($produccion_total,2,".",",")."</font>");
	
	if ($ganancia > 0)
		$tpl->assign("ganancia","<font color='#0000FF'>".number_format($ganancia,2,".",",")."</font>");
	else if ($ganancia < 0)
		$tpl->assign("ganancia","<font color='#FF0000'>".number_format($ganancia,2,".",",")."</font>");
	
	if ($porcentaje_ganancia > 0)
		$tpl->assign("porc_ganancia","<font color='#0000FF'>".number_format($porcentaje_ganancia,2,".",",")."</font>");
	else if ($porcentaje_ganancia < 0)
		$tpl->assign("porc_ganancia","<font color='#FF0000'>".number_format($porcentaje_ganancia,2,".",",")."</font>");
	
	if ($faltante_pan > 0)
		$tpl->assign("faltante_pan","<font color='#0000FF'>".number_format($faltante_pan,2,".",",")."</font>");
	else if ($faltante_pan < 0)
		$tpl->assign("faltante_pan","<font color='#FF0000'>".number_format($faltante_pan,2,".",",")."</font>");
	
	if ($devoluciones > 0)
		$tpl->assign("devoluciones","<font color='#0000FF'>".number_format($devoluciones,2,".",",")."</font>");
	else if ($devoluciones < 0)
		$tpl->assign("devoluciones","<font color='#FF0000'>".number_format($devoluciones,2,".",",")."</font>");
	
	if ($devoluciones > 0)
		$tpl->assign("devoluciones","<font color='#0000FF'>".number_format($devoluciones,2,".",",")."</font>");
	else if ($devoluciones < 0)
		$tpl->assign("devoluciones","<font color='#FF0000'>".number_format($devoluciones,2,".",",")."</font>");
	
	if ($rezago_inicial > 0)
		$tpl->assign("rezago_inicial","<font color='#0000FF'>".number_format($rezago_inicial,2,".",",")."</font>");
	else if ($rezago_inicial < 0)
		$tpl->assign("rezago_inicial","<font color='#FF0000'>".number_format($rezago_inicial,2,".",",")."</font>");
	
	if ($rezago_final > 0)
		$tpl->assign("rezago_final","<font color='#0000FF'>".number_format($rezago_final,2,".",",")."</font>");
	else if ($rezago_final < 0)
		$tpl->assign("rezago_final","<font color='#FF0000'>".number_format($rezago_final,2,".",",")."</font>");
	
	if ($subio_rezago > 0) {
		$tpl->assign("estado","Subio");
		$tpl->assign("subio_rezago","<font color='#0000FF'>".number_format($subio_rezago,2,".",",")."</font>");
	}
	else if ($subio_rezago < 0) {
		$tpl->assign("estado","Bajo");
		$tpl->assign("subio_rezago","<font color='#FF0000'>".number_format($subio_rezago,2,".",",")."</font>");
	}
	
	if ($efectivo > 0)
		$tpl->assign("efectivo","<font color='#0000FF'>".number_format($efectivo,2,".",",")."</font>");
	else if ($efectivo < 0)
		$tpl->assign("efectivo","<font color='#FF0000'>".number_format($efectivo,2,".",",")."</font>");
	
	$tpl->assign("utilidad_anio_ant","<font color='#".(($utilidad_anterior > 0)?"0000FF":"FF0000")."'>".number_format($utilidad_anterior,2,".",",")."</font>");
	
	$tpl->assign("inicio",$encargado[0]['nombre_inicio']);
	$tpl->assign("termino",$encargado[0]['nombre_fin']);
	
	/************************************* RESERVAS *****************************************************/
	$sql = "SELECT cod_reserva,descripcion,importe,codgastos FROM reservas_cias LEFT JOIN catalogo_reservas ON (tipo_res = cod_reserva) WHERE num_cia = $cia AND fecha = '$fecha1' ORDER BY cod_reserva";
	$result = ejecutar_script($sql,$dsn);
	
	if ($result) {
		// Crear títulos
		$tpl->assign("titulo_reserva","Reserva");
		$tpl->assign("titulo_importe","Importe");
		// Si el año es 2005, crear titulos de pagado y diferencia
		if ($anio == 2005) {
			$tpl->assign("titulo_pagado","Pagado");
			$tpl->assign("titulo_diferencia","Diferencia");
		}
		
		$total_result = 0;
		$total_pagado = 0;
		$total_diferencia = 0;
		$diferencia = 0;
		for ($r=0; $r<count($result); $r++) {
			$tpl->assign("nombre_reserva".($r+1),$result[$r]['descripcion']);
			$tpl->assign("importe_reserva".($r+1),number_format($result[$r]['importe'],2,".",","));
			$total_result += $result[$r]['importe'];
			
			// Si el año es 2005, buscar importes pagados de este mes para el homologo en gastos
			if ($anio == 2005 && $result[$r]['codgastos'] > 0) {
				$sql = "SELECT sum(importe) AS pagado FROM movimiento_gastos WHERE num_cia = $cia AND codgastos = {$result[$r]['codgastos']} AND fecha BETWEEN '$fecha1' AND '$fecha2'";
				$pagado = ejecutar_script($sql,$dsn);
				
				if ($pagado[0]['pagado'] != 0) {
					// Calcular diferencia
					$diferencia = $result[$r]['importe'] - $pagado[0]['pagado'];
					$tpl->assign("pagado".($r+1),number_format($pagado[0]['pagado'],2,".",","));
					$tpl->assign("diferencia".($r+1),number_format($diferencia,2,".",","));
					
					$total_pagado += $pagado[0]['pagado'];
					$total_diferencia += $diferencia;
				}
			}
		}
		$tpl->assign("nombre_reserva".($r+1),"<strong>Total</strong>");
		$tpl->assign("importe_reserva".($r+1),"<strong>".number_format($total_result,2,".",",")."</strong>");
		$tpl->assign("pagado".($r+1),$total_pagado != 0 ? "<strong>".number_format($total_pagado,2,".",",")."</strong>" : "");
		$tpl->assign("diferencia".($r+1),$total_diferencia != 0 ? "<strong>".number_format($total_diferencia,2,".",",")."</strong>" : "");
		if ($diferencia != 0 && $anio == 2005) {
			if ($utilidad_neta > 0)
				$tpl->assign("utilidad_mes","<font color='#0000FF'>".number_format($utilidad_neta,2,".",",")."</font>&nbsp;&nbsp;".(($utilidad_neta-$utilidad_anterior-$ingresos_ext+$diferencia == 0)?"":(($utilidad_neta-$utilidad_anterior-$ingresos_ext+$diferencia > 0)?"MEJORO":"EMPEORO")));
			else
				$tpl->assign("utilidad_mes","<font color='#FF0000'>".number_format($utilidad_neta,2,".",",")."</font>&nbsp;&nbsp;".(($utilidad_neta-$utilidad_anterior-$ingresos_ext+$diferencia == 0)?"":(($utilidad_neta-$utilidad_anterior-$ingresos_ext+$diferencia > 0)?"MEJORO":"EMPEORO")));
		}
	}
	/****************************************************************************************************/
	
	/************************************* ALMACENAR TODOS LOS DATOS DE LA HOJA EN LA TABLA DE BALANCES *******************************/
	$bal['num_cia'] = $cia;
	$bal['mes'] = $mes;
	$bal['anio'] = $anio;
	$bal['venta_puerta'] = $venta_puerta;
	$bal['bases'] = 0;
	$bal['barredura'] = $barredura;
	$bal['pastillaje'] = $pastillaje[0]['sum'];
	$bal['abono_emp'] = $abono_empleados[0]['sum'];
	$bal['otros'] = $otros[0]['sum'];
	$bal['total_otros'] = $total_otros;
	$bal['abono_reparto'] = $abono_reparto[0]['sum'];
	$bal['errores'] = $errores[0]['sum'];
	$bal['ventas_netas'] = $ventas_netas;
	$bal['inv_ant'] = $inv_ant[0]['sum'];
	$bal['compras'] = $compras;
	$bal['mercancias'] = $mercancias[0]['sum'];
	$bal['inv_act'] = $inv_act;
	$bal['mat_prima_utilizada'] = $mat_prima_utilizada;
	$bal['mano_obra'] = $mano_obra;
	$bal['panaderos'] = $panaderos;
	$bal['gastos_fab'] = $gastos_fab;
	$bal['costo_produccion'] = $costo_produccion;
	$bal['utilidad_bruta'] = $utilidad_bruta;
	$bal['pan_comprado'] = $pan_comprado;
	$bal['gastos_generales'] = $gastos_gral;
	$bal['gastos_caja'] = $gastos_caja;
	$bal['reserva_aguinaldos'] = $reservas;
	$bal['gastos_otras_cias'] = $gastos_otros[0]['sum'];
	$bal['total_gastos'] = $gastos_totales;
	$bal['ingresos_ext'] = $ingresos_ext;
	$bal['utilidad_neta'] = $utilidad_neta;
	$bal['mp_vtas'] = $mp_vtas;
	$bal['utilidad_pro'] = $utilidad_produccion;
	$bal['mp_pro'] = $mp_produccion;
	$bal['produccion_total'] = $produccion_total;
	$bal['faltante_pan'] = $faltante_pan;
	$bal['rezago_ini'] = $rezago_inicial;
	$bal['rezago_fin'] = $rezago_final;
	$bal['var_rezago'] = $subio_rezago;
	$bal['efectivo'] = $efectivo;
	$bal['gas_pro'] = $gas_produccion;
	$bal['pagos_anticipados'] = "";
	
	/*if (empty($_GET['no_gastos'])) {
		if (!existe_registro("balances_pan",array("num_cia","mes","anio"),array($cia,$mes,$anio),$dsn)) {
			$db = new DBclass($dsn,"balances_pan",$bal);
			$db->generar_script_insert("");
			$db->ejecutar_script();
		}
		else {
			$db = new DBclass($dsn,"balances_pan",$bal);
			$db->generar_script_update("",array("num_cia","mes","anio"),array($cia,$mes,$anio));
			$db->ejecutar_script();
		}
	}*/
	
	/************************************* UTILIDAD ANTERIOR ********************************************/
	$vta = 0;
	$abono = 0;
	$clientes = 0;
	for ($h=0; $h<count($historico_anterior); $h++) {
		switch ($historico_anterior[$h]['mes']) {
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
		$tpl->assign("tant_".$historico_anterior[$h]['mes'],mes_escrito($historico_anterior[$h]['mes']));
		$tpl->assign("ant_".$historico_anterior[$h]['mes'],"<font color='#0000FF'>".number_format($historico_anterior[$h]['utilidad'],2,".",",")."</font>".(($historico_anterior[$h]['ingresos'] != 0)?" <font color='#FF0000'>(".number_format($historico_anterior[$h]['ingresos'],2,".",",").")</font>":""));
		$tpl->assign("vta_ant_".$historico_anterior[$h]['mes'],number_format($historico_anterior[$h]['venta'],2,".",","));
		$tpl->assign("abono_ant_".$historico_anterior[$h]['mes'],number_format($historico_anterior[$h]['reparto'],2,".",","));
		$tpl->assign("clientes_ant_".$historico_anterior[$h]['mes'],number_format($historico_anterior[$h]['clientes'],2,".",","));
		$tpl->assign("prom_ant_".$historico_anterior[$h]['mes'],($historico_anterior[$h]['clientes'] != 0)?number_format($historico_anterior[$h]['venta']/$historico_anterior[$h]['clientes'],2,".",","):"&nbsp;");
		
		$vta += $historico_anterior[$h]['venta'];
		$abono += $historico_anterior[$h]['reparto'];
		$clientes += $historico_anterior[$h]['clientes'];
	}
	$tpl->assign("tot_vta_ant",($vta > 0)?number_format($vta,2,".",","):"");
	$tpl->assign("tot_abono_ant",($abono > 0)?number_format($abono,2,".",","):"");
	$tpl->assign("tot_clientes_ant",($clientes > 0)?number_format($clientes,2,".",","):"");
	
	$vta = 0;
	$abono = 0;
	$clientes = 0;	
	for ($h=0; $h<count($historico_actual); $h++) {
		switch ($historico_actual[$h]['mes']) {
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
		$tpl->assign("tact_".$historico_actual[$h]['mes'],mes_escrito($historico_actual[$h]['mes']));
		$tpl->assign("act_".$historico_actual[$h]['mes'],"<font color='#0000FF'>".number_format($historico_actual[$h]['utilidad'],2,".",",")."</font>".(($historico_actual[$h]['ingresos'] != 0)?" <font color='#FF0000'>(".number_format($historico_actual[$h]['ingresos'],2,".",",").")</font>":""));
		$tpl->assign("vta_".$historico_actual[$h]['mes'],number_format($historico_actual[$h]['venta'],2,".",","));
		$tpl->assign("abono_".$historico_actual[$h]['mes'],number_format($historico_actual[$h]['reparto'],2,".",","));
		$tpl->assign("clientes_".$historico_actual[$h]['mes'],number_format($historico_actual[$h]['clientes'],2,".",","));
		$tpl->assign("prom_".$historico_actual[$h]['mes'],($historico_actual[$h]['clientes'] != 0)?number_format($historico_actual[$h]['venta']/$historico_actual[$h]['clientes'],2,".",","):"&nbsp;");
		
		$vta += $historico_actual[$h]['venta'];
		$abono += $historico_actual[$h]['reparto'];
		$clientes += $historico_actual[$h]['clientes'];
	}
	$tpl->assign("tot_vta",($vta > 0)?number_format($vta,2,".",","):"");
	$tpl->assign("tot_abono",($abono > 0)?number_format($abono,2,".",","):"");
	$tpl->assign("tot_clientes",($clientes > 0)?number_format($clientes,2,".",","):"");
	
	/******************************** (HOJA 2) RELACION DE GASTOS EXTRAS *************************************/
	$tpl->newBlock("gastos_extras");
	$tpl->assign("num_cia",$cia);
	$tpl->assign("nombre_cia",$nombre_cia[$f]['nombre']);
	$tpl->assign("dia",$dias);
	$tpl->assign("anio",$anio);
	$tpl->assign("mes",mes_escrito($mes));
	
	// Fechas del mes anterior
	$fecha1_ant = date("d/m/Y",mktime(0,0,0,$mes-1,1,$anio));
	$fecha2_ant = date("d/m/Y",mktime(0,0,0,$mes,0,$anio));
	
	$total_importe = 0;
	$total_mes_ant = 0;
	// 1er bloque de gastos extras (GASTOS DE OPERACION)
	$tpl->newBlock("tipo_gasto");
	$tpl->assign("tipo_gasto","GASTOS DE OPERACI&Oacute;N");
	// Generar OBLIGATORIAMENTE el primer gastos de operación como GAS
	$sql = "SELECT sum(importe) FROM catalogo_gastos JOIN movimiento_gastos USING (codgastos) WHERE num_cia=$cia AND codgastos=90 AND fecha>='$fecha1' AND fecha<='$fecha2' AND codigo_edo_resultados=1";
	$importe = ejecutar_script($sql,$dsn);
	$sql = "SELECT sum(importe) FROM catalogo_gastos JOIN movimiento_gastos USING (codgastos) WHERE num_cia=$cia AND codgastos=90 AND fecha>='$fecha1_ant' AND fecha<='$fecha2_ant' AND codigo_edo_resultados=1";
	$mes_ant = ejecutar_script($sql,$dsn);
	
	$imp = 0;
	$mes_par = 0;

	if (round($importe[0]['sum'],2) != 0 || round($mes_ant[0]['sum'],2) != 0) {
		$tpl->newBlock("fila_gasto");
		$tpl->assign("concepto","GAS");
		$tpl->assign("importe",($importe[0]['sum'] != 0)?number_format($importe[0]['sum'],2,".",","):"&nbsp;");
		$tpl->assign("mes_ant",($mes_ant[0]['sum'] != 0)?number_format($mes_ant[0]['sum'],2,".",","):"&nbsp;");
		$resta = $importe[0]['sum'] - $mes_ant[0]['sum'];
		$variacion = ($importe[0]['sum'] != 0)?$resta * 100 / $importe[0]['sum']:0;
		$tpl->assign("variacion",($variacion > -100 && $variacion < 100 && $variacion != 0)?number_format(abs($variacion),2,".",",").(($variacion > 0)?" MAS":" MENOS"):"&nbsp;");
		
		$imp += $importe[0]['sum'];
		$mes_par += $mes_ant[0]['sum'];
		
		$total_importe += $importe[0]['sum'];
		$total_mes_ant += $mes_ant[0]['sum'];
	}
	// Generar gastos de operación restantes
	$sql = "SELECT DISTINCT ON (codgastos) * FROM catalogo_gastos JOIN movimiento_gastos USING (codgastos) WHERE num_cia=$cia AND fecha>='$fecha1' AND fecha<='$fecha2' AND codgastos != 90 AND codigo_edo_resultados=1 ORDER BY codgastos";
	$codigos = ejecutar_script($sql,$dsn);
	if ($codigos) {
		for ($i=0; $i<count($codigos); $i++) {
			$sql = "SELECT sum(importe) FROM catalogo_gastos JOIN movimiento_gastos USING (codgastos) WHERE num_cia=$cia AND codgastos=".$codigos[$i]['codgastos']." AND fecha>='$fecha1' AND fecha<='$fecha2' AND codigo_edo_resultados=1";
			$importe = ejecutar_script($sql,$dsn);
			$sql = "SELECT sum(importe) FROM catalogo_gastos JOIN movimiento_gastos USING (codgastos) WHERE num_cia=$cia AND codgastos=".$codigos[$i]['codgastos']." AND fecha>='$fecha1_ant' AND fecha<='$fecha2_ant' AND codigo_edo_resultados=1";
			$mes_ant = ejecutar_script($sql,$dsn);
			if (round($importe[0]['sum'],2) != 0) {
				$tpl->newBlock("fila_gasto");
				$tpl->assign("codgastos",$codigos[$i]['codgastos']);
				$tpl->assign("concepto",$codigos[$i]['descripcion']);
				$tpl->assign("importe",number_format($importe[0]['sum'],2,".",","));
				$tpl->assign("mes_ant",($mes_ant[0]['sum'] != 0)?number_format($mes_ant[0]['sum'],2,".",","):"&nbsp;");
				$resta = $importe[0]['sum'] - $mes_ant[0]['sum'];
				$variacion = ($importe[0]['sum'] != 0)?$resta * 100 / $importe[0]['sum']:0;
				$tpl->assign("variacion",($variacion > -100 && $variacion < 100 && $variacion != 0)?number_format(abs($variacion),2,".",",").(($variacion > 0)?" MAS":" MENOS"):"&nbsp;");
				
				$imp += $importe[0]['sum'];
				$mes_par += $mes_ant[0]['sum'];
				
				$total_importe += $importe[0]['sum'];
				$total_mes_ant += $mes_ant[0]['sum'];
			}
		}
	}
	$tpl->assign("tipo_gasto.importe",number_format($imp,2,".",","));
	$tpl->assign("tipo_gasto.mes_ant",number_format($mes_par,2,".",","));
	$resta = $imp - $mes_par;
	$variacion = ($imp != 0)?$resta * 100 / $imp:0;
	$tpl->assign("tipo_gasto.variacion",($variacion > -100 && $variacion < 100 && $variacion != 0)?number_format(abs($variacion),2,".",","):"&nbsp;");
	
	// 2o bloque de gastos extras (GASTOS GENERALES)
	$sql = "SELECT DISTINCT ON (codgastos) * FROM catalogo_gastos JOIN movimiento_gastos USING (codgastos) WHERE num_cia=$cia AND fecha>='$fecha1' AND fecha<='$fecha2' AND codigo_edo_resultados=2 ORDER BY codgastos";
	$codigos = ejecutar_script($sql,$dsn);
	
	$imp = 0;
	$mes_par = 0;
	
	if ($codigos) {
		$tpl->newBlock("tipo_gasto");
		$tpl->assign("tipo_gasto","GASTOS GENERALES");
		for ($i=0; $i<count($codigos); $i++) {
			$sql = "SELECT sum(importe) FROM catalogo_gastos JOIN movimiento_gastos USING (codgastos) WHERE num_cia=$cia AND codgastos=".$codigos[$i]['codgastos']." AND fecha>='$fecha1' AND fecha<='$fecha2' AND codigo_edo_resultados=2";
			$importe = ejecutar_script($sql,$dsn);
			$sql = "SELECT sum(importe) FROM catalogo_gastos JOIN movimiento_gastos USING (codgastos) WHERE num_cia=$cia AND codgastos=".$codigos[$i]['codgastos']." AND fecha>='$fecha1_ant' AND fecha<='$fecha2_ant' AND codigo_edo_resultados=2";
			$mes_ant = ejecutar_script($sql,$dsn);
			
			if (round($importe[0]['sum'],2) != 0) {
				$tpl->newBlock("fila_gasto");
				$tpl->assign("codgastos",$codigos[$i]['codgastos']);
				$tpl->assign("concepto",$codigos[$i]['descripcion']);
				$tpl->assign("importe",number_format($importe[0]['sum'],2,".",","));
				$tpl->assign("mes_ant",($mes_ant[0]['sum'] != 0)?number_format($mes_ant[0]['sum'],2,".",","):"&nbsp;");
				$resta = $importe[0]['sum'] - $mes_ant[0]['sum'];
				$variacion = ($importe[0]['sum'] != 0)?$resta * 100 / $importe[0]['sum']:0;
				$tpl->assign("variacion",($variacion > -100 && $variacion < 100 && $variacion != 0)?number_format(abs($variacion),2,".",",").(($variacion > 0)?" MAS":" MENOS"):"&nbsp;");
				
				$imp += $importe[0]['sum'];
				$mes_par += $mes_ant[0]['sum'];
				
				$total_importe += $importe[0]['sum'];
				$total_mes_ant += $mes_ant[0]['sum'];
			}
		}
	}
	$tpl->assign("tipo_gasto.importe",number_format($imp,2,".",","));
	$tpl->assign("tipo_gasto.mes_ant",number_format($mes_par,2,".",","));
	$resta = $imp - $mes_par;
	$variacion = ($imp != 0)?$resta * 100 / $imp:0;
	$tpl->assign("tipo_gasto.variacion",($variacion > -100 && $variacion < 100 && $variacion != 0)?number_format($variacion,2,".",","):"&nbsp;");
	
	// Totales de Gastos
	$tpl->assign("gastos_extras.total_importe",number_format($total_importe,2,".",","));
	$tpl->assign("gastos_extras.total_mes_ant",number_format($total_mes_ant,2,".",","));
	$resta = $total_importe - $total_mes_ant;
	$variacion = ($total_importe != 0)?$resta * 100 / $total_importe:0;
	$tpl->assign("gastos_extras.prom_variacion",($variacion > -100 && $variacion < 100 && $variacion != 0)?number_format(abs($variacion),2,".",","):"&nbsp;");
	
	// 3er bloque de gastos extras (GASTOS POR CAJA) (Omitir codigo 28 - Abarrotes Julild)
	$sql = "SELECT DISTINCT ON (cod_gastos) cod_gastos,descripcion FROM gastos_caja JOIN catalogo_gastos_caja ON (catalogo_gastos_caja.id = cod_gastos) WHERE num_cia=$cia AND fecha BETWEEN '$fecha1' AND '$fecha2' AND clave_balance='TRUE' AND cod_gastos NOT IN (28) ORDER BY cod_gastos";
	$cod_caja = ejecutar_script($sql, $dsn);
	if ($cod_caja) {
		$tpl->newBlock("gastos_caja");
		$total = 0;
		for ($g=0; $g<count($cod_caja); $g++) {
			$tpl->newBlock("fila_caja");
			// Obtener ingresos
			$sql = "SELECT SUM(importe) FROM gastos_caja WHERE num_cia=$cia AND fecha BETWEEN '$fecha1' AND '$fecha2' AND cod_gastos=".$cod_caja[$g]['cod_gastos']." AND clave_balance='TRUE' AND tipo_mov='TRUE'";
			$ingresos = ejecutar_script($sql,$dsn);
			$sql = "SELECT SUM(importe) FROM gastos_caja WHERE num_cia=$cia AND fecha BETWEEN '$fecha1' AND '$fecha2' AND cod_gastos=".$cod_caja[$g]['cod_gastos']." AND clave_balance='TRUE' AND tipo_mov='FALSE'";
			$egresos = ejecutar_script($sql,$dsn);
			$importe = $ingresos[0]['sum'] - $egresos[0]['sum'];
			$tpl->assign("concepto",$cod_caja[$g]['descripcion']);
			$tpl->assign("importe",number_format($importe,2,".",","));
			$total += $importe;
		}
		$tpl->assign("gastos_caja.importe",number_format($total,2,".",","));
	}
	
	/**************************************** ESTADO DE RESULTADOS COMPARATIVO *************************************************/
	$tpl->assign("reporte.mes_ant",mes_escrito(($mes-1 > 0)?$mes-1:12));
	$tpl->assign("reporte.mes_act",mes_escrito($mes));
	
	$tpl->gotoBlock("reporte");
	
	// Obtener datos del balance anterior
	$sql = "SELECT * FROM balances_pan WHERE num_cia = $cia AND mes = ".(($mes-1 > 0)?$mes-1:12)." AND anio = ".(($mes-1 > 0)?$anio:$anio-1);
	$bal_ant = ejecutar_script($sql,$dsn);
	
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
		
		// Diferencia
		$tpl->assign("venta_puerta_dif",($venta_puerta-$bal_ant[0]['venta_puerta'] != 0)?"<font color='#".(($venta_puerta-$bal_ant[0]['venta_puerta'] > 0)?"0000FF":"FF0000")."'>".number_format($venta_puerta-$bal_ant[0]['venta_puerta'],2,".",",")."</font>":"&nbsp;");
		//$tpl->assign("bases_dif",($bases[0]['sum']-$bal_ant[0]['bases'] != 0)?"<font color='#".(($bases[0]['sum']-$bal_ant[0]['bases'] > 0)?"0000FF":"FF0000")."'>".number_format($bases[0]['sum']-$bal_ant[0]['bases'],2,".",",")."</font>":"&nbsp;");
		$tpl->assign("barredura_dif",($bal_ant[0]['barredura'] != 0)?"<font color='#".(($bal_ant[0]['barredura'] > 0)?"0000FF":"FF0000")."'>".number_format($bal_ant[0]['barredura'],2,".",",")."</font>":"&nbsp;");
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
		$tpl->assign("mano_obra_dif",($mano_obra-$bal_ant[0]['mano_obra'] != 0)?"<font color='#".(($mano_obra-$bal_ant[0]['mano_obra'] > 0)?"0000FF":"FF0000")."'>".number_format($mano_obra-$bal_ant[0]['mano_obra'],2,".",",")."</font>":"&nbsp;");
		$tpl->assign("panaderos_dif",($panaderos-$bal_ant[0]['panaderos'] != 0)?"<font color='#".(($panaderos-$bal_ant[0]['panaderos'] > 0)?"0000FF":"FF0000")."'>".number_format($panaderos-$bal_ant[0]['panaderos'],2,".",",")."</font>":"&nbsp;");
		$tpl->assign("gastos_fabricacion_dif",($gastos_fab-$bal_ant[0]['gastos_fab'] != 0)?"<font color='#".(($gastos_fab-$bal_ant[0]['gastos_fab'] > 0)?"0000FF":"FF0000")."'>".number_format($gastos_fab-$bal_ant[0]['gastos_fab'],2,".",",")."</font>":"&nbsp;");
		$tpl->assign("costo_produccion_dif",($costo_produccion-$bal_ant[0]['costo_produccion'] != 0)?"<font color='#".(($costo_produccion-$bal_ant[0]['costo_produccion'] > 0)?"0000FF":"FF0000")."'>".number_format($costo_produccion-$bal_ant[0]['costo_produccion'],2,".",",")."</font>":"&nbsp;");
		
		$tpl->assign("utilidad_bruta_dif",($utilidad_bruta-$bal_ant[0]['utilidad_bruta'] != 0)?"<font color='#".(($utilidad_bruta-$bal_ant[0]['utilidad_bruta'] > 0)?"0000FF":"FF0000")."'>".number_format($utilidad_bruta-$bal_ant[0]['utilidad_bruta'],2,".",",")."</font>":"&nbsp;");
		
		$tpl->assign("pan_comprado_dif",($pan_comprado-$bal_ant[0]['pan_comprado'] != 0)?"<font color='#".(($pan_comprado-$bal_ant[0]['pan_comprado'] > 0)?"0000FF":"FF0000")."'>".number_format($pan_comprado-$bal_ant[0]['pan_comprado'],2,".",",")."</font>":"&nbsp;");
		$tpl->assign("gastos_generales_dif",($gastos_gral-$bal_ant[0]['gastos_generales'] != 0)?"<font color='#".(($gastos_gral-$bal_ant[0]['gastos_generales'] > 0)?"0000FF":"FF0000")."'>".number_format($gastos_gral-$bal_ant[0]['gastos_generales'],2,".",",")."</font>":"&nbsp;");
		$tpl->assign("gastos_caja_dif",($gastos_caja-$bal_ant[0]['gastos_caja'] != 0)?"<font color='#".(($gastos_caja-$bal_ant[0]['gastos_caja'] > 0)?"0000FF":"FF0000")."'>".number_format($gastos_caja-$bal_ant[0]['gastos_caja'],2,".",",")."</font>":"&nbsp;");
		$tpl->assign("reserva_aguinaldos_dif",($reservas-$bal_ant[0]['reserva_aguinaldos'] != 0)?"<font color='#".(($reservas-$bal_ant[0]['reserva_aguinaldos'] > 0)?"0000FF":"FF0000")."'>".number_format($reservas-$bal_ant[0]['reserva_aguinaldos'],2,".",",")."</font>":"&nbsp;");
		$tpl->assign("gastos_otras_cias_dif",($gastos_otros-$bal_ant[0]['gastos_otras_cias'] != 0)?"<font color='#".(($gastos_otros-$bal_ant[0]['gastos_otras_cias'] > 0)?"0000FF":"FF0000")."'>".number_format($gastos_otros-$bal_ant[0]['gastos_otras_cias'],2,".",",")."</font>":"&nbsp;");
		$tpl->assign("total_gastos_dif",($gastos_totales-$bal_ant[0]['total_gastos'] != 0)?"<font color='#".(($gastos_totales-$bal_ant[0]['total_gastos'] > 0)?"0000FF":"FF0000")."'>".number_format($gastos_totales-$bal_ant[0]['total_gastos'],2,".",",")."</font>":"&nbsp;");
		
		$tpl->assign("ingresos_ext_dif",($ingresos_ext-$bal_ant[0]['ingresos_ext'] != 0)?"<font color='#".(($ingresos_ext-$bal_ant[0]['ingresos_ext'] > 0)?"0000FF":"FF0000")."'>".number_format($ingresos_ext-$bal_ant[0]['ingresos_ext'],2,".",",")."</font>":"&nbsp;");
		
		$tpl->assign("utilidad_mes_dif",($utilidad_neta-$bal_ant[0]['utilidad_neta'] != 0)?"<font color='#".(($utilidad_neta-$bal_ant[0]['utilidad_neta'] > 0)?"0000FF":"FF0000")."'>".number_format(($bal_ant[0]['utilidad_neta'] > 0 ? $utilidad_neta-$bal_ant[0]['utilidad_neta'] : $utilidad_neta+$bal_ant[0]['utilidad_neta']),2,".",",")."</font>":"&nbsp;");
		
		$tpl->assign("mp_vtas_dif",($bal_ant[0]['mp_vtas'] != 0)?"<font color='#".(($bal_ant[0]['mp_vtas'] > 0)?"0000FF":"FF0000")."'>".number_format($bal_ant[0]['mp_vtas'],3,".",",")."</font>":"&nbsp;");
		$tpl->assign("utilidad_produccion_dif",($bal_ant[0]['utilidad_pro'] != 0)?"<font color='#".(($bal_ant[0]['utilidad_pro'] > 0)?"0000FF":"FF0000")."'>".number_format($bal_ant[0]['utilidad_pro'],3,".",",")."</font>":"&nbsp;");
		$tpl->assign("mp_produccion_dif",($bal_ant[0]['mp_pro'] != 0)?"<font color='#".(($bal_ant[0]['mp_pro'] > 0)?"0000FF":"FF0000")."'>".number_format($bal_ant[0]['mp_pro'],3,".",",")."</font>":"&nbsp;");
		
		$tpl->assign("produccion_total_dif",($produccion_total-$bal_ant[0]['produccion_total'] != 0)?"<font color='#".(($produccion_total-$bal_ant[0]['produccion_total'] > 0)?"0000FF":"FF0000")."'>".number_format($produccion_total-$bal_ant[0]['produccion_total'],2,".",",")."</font>":"&nbsp;");
		$tpl->assign("faltante_pan_dif",($faltante_pan-$bal_ant[0]['faltante_pan'] != 0)?"<font color='#".(($faltante_pan-$bal_ant[0]['faltante_pan'] > 0)?"0000FF":"FF0000")."'>".number_format($faltante_pan-$bal_ant[0]['faltante_pan'],2,".",",")."</font>":"&nbsp;");
		$tpl->assign("rezago_inicial_dif",($rezago_inicial-$bal_ant[0]['rezago_ini'] != 0)?"<font color='#".(($rezago_inicial-$bal_ant[0]['rezago_ini'] > 0)?"0000FF":"FF0000")."'>".number_format($rezago_inicial-$bal_ant[0]['rezago_ini'],2,".",",")."</font>":"&nbsp;");
		$tpl->assign("rezago_final_dif",($rezago_final-$bal_ant[0]['rezago_fin'] != 0)?"<font color='#".(($rezago_final-$bal_ant[0]['rezago_fin'] > 0)?"0000FF":"FF0000")."'>".number_format($rezago_final-$bal_ant[0]['rezago_fin'],2,".",",")."</font>":"&nbsp;");
		$tpl->assign("var_rezago_dif",($subio_rezago-$bal_ant[0]['var_rezago'] != 0)?"<font color='#".(($subio_rezago-$bal_ant[0]['var_rezago'] > 0)?"0000FF":"FF0000")."'>".number_format($subio_rezago-$bal_ant[0]['var_rezago'],2,".",",")."</font>":"&nbsp;");
		$tpl->assign("efectivo_dif",($efectivo-$bal_ant[0]['efectivo'] != 0)?"<font color='#".(($efectivo-$bal_ant[0]['efectivo'] > 0)?"0000FF":"FF0000")."'>".number_format($efectivo-$bal_ant[0]['efectivo'],2,".",",")."</font>":"&nbsp;");
	}
	
	/******************************************* LISTADO DE GASTOS PAGADOS A OFICINAS **********************************************/
	$sql = "SELECT fecha,codgastos,descripcion,facturas,concepto,importe,folio,a_nombre FROM cheques JOIN catalogo_gastos USING(codgastos) WHERE num_cia=$cia AND fecha BETWEEN '$fecha1' AND '$fecha2' AND codgastos NOT IN (33,134,999) AND fecha_cancelacion IS NULL ORDER BY codgastos,fecha";
	$result = ejecutar_script($sql,$dsn);
	
	if ($result){
		$tpl->newBlock("listado_gastos");
		
		$tpl->assign("num_cia",$cia);
		$tpl->assign("nombre_cia",$nombre_cia[$f]['nombre']);
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
			$tpl->assign("fecha",$result[$i]['fecha']);
			$tpl->assign("codgastos",$result[$i]['codgastos']);
			$tpl->assign("descripcion",$result[$i]['descripcion']);
			$tpl->assign("a_nombre",$result[$i]['a_nombre']);
			$tpl->assign("facturas",$result[$i]['facturas']);
			$tpl->assign("concepto",(($result[$i]['facturas'] > 0)?"<br>":"").$result[$i]['concepto']);
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
}
$fin = microtime_float();
$tiempo = $fin - $inicio;
//echo "Tiempo de ejecución: ".round($tiempo,3)." segundos";
$tpl->printToScreen();

?>