<?php
include './includes/class.db.inc.php';
include './includes/dbstatus.php';

$db = new DBclass($dsn, "autocommit=yes");

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

/***************************************************************************************************************/
/* ACTUALIZAR EXISTENCIAS Y COSTOS EN HISTORICO                                                                */
/***************************************************************************************************************/
$flags = $db->query("SELECT * FROM flags");
if ($flags[0]['actualizar_historico'] == "t") {
	$dia = (int)date("d");
	$mes = (int)date("m", mktime(0, 0, 0, date("m") - 1, 1, date("Y")));
	$anio = (int)date("Y");
	
	// Obtener la ultima fecha del historico
	$ultima_fecha = $db->query("SELECT fecha FROM historico_inventario ORDER BY fecha DESC LIMIT 1");
	ereg("([0-9]{1,2})/([0-9]{1,2})/([0-9]{2,4})", $ultima_fecha[0]['fecha'], $temp);
	
	$fecha1 = date("d/m/Y", mktime(0, 0, 0, $temp[2] + 1, 1, $temp[3]));
	$fecha2 = date("d/m/Y", mktime(0, 0, 0, $temp[2] + 2, 0, $temp[3]));
	$fecha_historico = /*date("d/m/Y", mktime(0, 0, 0, $mes, 0, $anio))*/$ultima_fecha[0]['fecha'];
	
	$sql = "SELECT num_cia, codmp, nombre, existencia, precio_unidad, controlada, tipo FROM historico_inventario LEFT JOIN catalogo_mat_primas USING (codmp) WHERE";
	// Si es para una compañía en específico
	$sql .= " num_cia < 100 AND fecha = '$fecha_historico' ORDER BY num_cia, controlada DESC, tipo, codmp";
	$saldo_ant = $db->query($sql);
	
	// Saldo actual (omitir diferencias)
	$sql = "SELECT num_cia, fecha, codmp, nombre, descripcion, cod_turno, tipo_mov, cantidad, precio_unidad, total_mov FROM mov_inv_real LEFT JOIN catalogo_mat_primas USING (codmp) WHERE";
	// Si es para una compañía en específico
	$sql .= " num_cia < 100 AND fecha BETWEEN '$fecha1' AND '$fecha2' AND descripcion != 'DIFERENCIA INVENTARIO' ORDER BY num_cia, controlada DESC, tipo, codmp, fecha, tipo_mov, cantidad DESC";
	$saldo = $db->query($sql);
	
	$sql = "SELECT num_cia, fecha, codmp, nombre, descripcion, cod_turno, tipo_mov, cantidad, precio_unidad, total_mov FROM mov_inv_real LEFT JOIN catalogo_mat_primas USING (codmp) WHERE";
	// Si es para una compañía en específico
	$sql .= " num_cia < 100 AND fecha BETWEEN '$fecha1' AND '$fecha2' AND descripcion = 'DIFERENCIA INVENTARIO' ORDER BY num_cia, controlada DESC, tipo, codmp, fecha, tipo_mov";
	$difInv = $db->query($sql);
	
	$nummp = count($saldo_ant);
	$nummov = count($saldo);
	
	$sql = "";
	
	$num_cia = NULL;
	for ($j = 0; $j < $nummp; $j++) {
		if ($num_cia != $saldo_ant[$j]['num_cia'])
			$num_cia = $saldo_ant[$j]['num_cia'];
		
		$codmp = $saldo_ant[$j]['codmp'];
		$nombremp = $saldo_ant[$j]['nombre'];
		
		// Datos del producto
		$valores_ant = $saldo_ant[$j]['existencia'] * $saldo_ant[$j]['precio_unidad'];
		
		$unidades = $saldo_ant[$j]['existencia'];
		$valores  = $saldo_ant[$j]['existencia'] * $saldo_ant[$j]['precio_unidad'];
		
		$unidades_entrada = 0;
		$valores_entrada  = 0;
		$unidades_salida  = 0;
		$valores_salida   = 0;
		$costo_promedio   = $saldo_ant[$j]['precio_unidad'];
		$costo_ant = $costo_promedio;	// Costo anterior a un movimiento
		$unidades_ant = $unidades;	// Unidades anteriores a un movimiento
		
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
						$val_1 = ($saldo[$k]['cantidad'] + $unidades) * $costo_ant;
						$val_2 = abs($unidades) * $proximo_costo;
						$val_sal = $val_1 + $val_2;
						
						$costo_promedio = $proximo_costo;
						$costo_ant = $costo_promedio;
					}
					// Calcular arratre normalmente
					else {
						$val_sal = $saldo[$k]['cantidad'] * $costo_promedio;
						
						$costo_ant = $costo_promedio;
					}
					
					$unidades_ant = $unidades;
					
					// Calcular arratres de salida
					$unidades_salida += $saldo[$k]['cantidad']; 
					$valores_salida  += $val_sal;
				}
				// Entradas
				else if ($saldo[$k]['tipo_mov'] == "f") {
					@$precio_unidad = $saldo[$k]['total_mov'] / $saldo[$k]['cantidad'];	// Costo unitario de la entrada
					
					$unidades += $saldo[$k]['cantidad'];
					
					$valor_ant = $unidades_ant * $costo_promedio;
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
					
					// Actualizar existencia anteriora a la existencia actual
					$unidades_ant = $unidades;
					
					// Calcular arrastres de entrada
					$unidades_entrada += $saldo[$k]['cantidad'];
					$valores_entrada  += $saldo[$k]['cantidad'] * $precio_unidad;
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
				
				$unidades_salida += $difInv[$idDif]['cantidad'];
				$valores_salida  += $difInv[$idDif]['cantidad'] * $costo_promedio;
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
				
				$unidades_entrada += $difInv[$idDif]['cantidad'];
				$valores_entrada  += $difInv[$idDif]['cantidad'] * $precio_unidad;
			}
		}
		/***********************************************************************************************************/
		/* CODIGO PARA CORREGIR UNICAMENTE AVIO, HISTORICOS Y DEMAS                                                */
		/* DESCOMENTAR LOS SCRIPTS PARA CORREGIR                                                                   */
		/***********************************************************************************************************/
		$sql .= "UPDATE historico_inventario SET existencia = " . ($unidades != 0 ? $unidades : "0") . ", precio_unidad = " . ($costo_promedio != 0 ? $costo_promedio : "0") . " WHERE num_cia = $num_cia AND codmp = $codmp AND fecha = '$fecha2';\n";
		/***********************************************************************************************************/
	}
	$sql .= "UPDATE flags SET actualizar_historico = FALSE;\n";
	$db->query($sql);
}

/***************************************************************************************************************/
/* ACTUALZIAR EXISTENCIAS EN INVENTARIOS                                                                       */
/***************************************************************************************************************/

$dia = (int)date("d");
$mes = (int)date("m");
$anio = (int)date("Y");

$fecha1 = date("d/m/Y", mktime(0, 0, 0, $mes, 1, $anio));
$fecha2 = date("d/m/Y", mktime(0, 0, 0, $mes, $dia, $anio));
$fecha_historico = date("d/m/Y", mktime(0, 0, 0, $mes, 0, $anio));

$sql = "SELECT num_cia, codmp, nombre, existencia, precio_unidad, controlada, tipo FROM historico_inventario LEFT JOIN catalogo_mat_primas USING (codmp) WHERE";
// Si es para una compañía en específico
$sql .= " num_cia < 100 AND fecha = '$fecha_historico' ORDER BY num_cia, controlada DESC, tipo, codmp";
$saldo_ant = $db->query($sql);

// Saldo actual (omitir diferencias)
$sql = "SELECT num_cia, fecha, codmp, nombre, descripcion, cod_turno, tipo_mov, cantidad, precio_unidad, total_mov FROM mov_inv_real LEFT JOIN catalogo_mat_primas USING (codmp) WHERE";
// Si es para una compañía en específico
$sql .= " num_cia < 100 AND fecha BETWEEN '$fecha1' AND '$fecha2' AND descripcion != 'DIFERENCIA INVENTARIO' ORDER BY num_cia, controlada DESC, tipo, codmp, fecha, tipo_mov, cantidad DESC";
$saldo = $db->query($sql);

$sql = "SELECT num_cia, fecha, codmp, nombre, descripcion, cod_turno, tipo_mov, cantidad, precio_unidad, total_mov FROM mov_inv_real LEFT JOIN catalogo_mat_primas USING (codmp) WHERE";
// Si es para una compañía en específico
$sql .= " num_cia < 100 AND fecha BETWEEN '$fecha1' AND '$fecha2' AND descripcion = 'DIFERENCIA INVENTARIO' ORDER BY num_cia, controlada DESC, tipo, codmp, fecha, tipo_mov";
$difInv = $db->query($sql);

$nummp = count($saldo_ant);
$nummov = count($saldo);

$sql = "";

$num_cia = NULL;
for ($j = 0; $j < $nummp; $j++) {
	if ($num_cia != $saldo_ant[$j]['num_cia'])
		$num_cia = $saldo_ant[$j]['num_cia'];
	
	$codmp = $saldo_ant[$j]['codmp'];
	$nombremp = $saldo_ant[$j]['nombre'];
	
	// Datos del producto
	$valores_ant = $saldo_ant[$j]['existencia'] * $saldo_ant[$j]['precio_unidad'];
	
	$unidades = $saldo_ant[$j]['existencia'];
	$valores  = $saldo_ant[$j]['existencia'] * $saldo_ant[$j]['precio_unidad'];
	
	$unidades_entrada = 0;
	$valores_entrada  = 0;
	$unidades_salida  = 0;
	$valores_salida   = 0;
	$costo_promedio   = $saldo_ant[$j]['precio_unidad'];
	$costo_ant = $costo_promedio;	// Costo anterior a un movimiento
	$unidades_ant = $unidades;	// Unidades anteriores a un movimiento
	
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
					$val_1 = ($saldo[$k]['cantidad'] + $unidades) * $costo_ant;
					$val_2 = abs($unidades) * $proximo_costo;
					$val_sal = $val_1 + $val_2;
					
					$costo_promedio = $proximo_costo;
					$costo_ant = $costo_promedio;
				}
				// Calcular arratre normalmente
				else {
					$val_sal = $saldo[$k]['cantidad'] * $costo_promedio;
					
					$costo_ant = $costo_promedio;
				}
				
				$unidades_ant = $unidades;
				
				// Calcular arratres de salida
				$unidades_salida += $saldo[$k]['cantidad']; 
				$valores_salida  += $val_sal;
			}
			// Entradas
			else if ($saldo[$k]['tipo_mov'] == "f") {
				@$precio_unidad = $saldo[$k]['total_mov'] / $saldo[$k]['cantidad'];	// Costo unitario de la entrada
				
				$unidades += $saldo[$k]['cantidad'];
				
				$valor_ant = $unidades_ant * $costo_promedio;
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
				
				// Actualizar existencia anteriora a la existencia actual
				$unidades_ant = $unidades;
				
				// Calcular arrastres de entrada
				$unidades_entrada += $saldo[$k]['cantidad'];
				$valores_entrada  += $saldo[$k]['cantidad'] * $precio_unidad;
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
			
			$unidades_salida += $difInv[$idDif]['cantidad'];
			$valores_salida  += $difInv[$idDif]['cantidad'] * $costo_promedio;
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
			
			$unidades_entrada += $difInv[$idDif]['cantidad'];
			$valores_entrada  += $difInv[$idDif]['cantidad'] * $precio_unidad;
		}
	}
	/***********************************************************************************************************/
	/* CODIGO PARA CORREGIR UNICAMENTE AVIO, HISTORICOS Y DEMAS                                                */
	/* DESCOMENTAR LOS SCRIPTS PARA CORREGIR                                                                   */
	/***********************************************************************************************************/
	$sql .= "UPDATE inventario_real SET existencia = " . ($unidades != 0 ? $unidades : "0") . ", precio_unidad = " . ($costo_promedio != 0 ? $costo_promedio : "0") . " WHERE num_cia = $num_cia AND codmp = $codmp;\n";
	/***********************************************************************************************************/
}
$db->query($sql);

/***************************************************************************************************************/
/* ACTUALIZACION DE SALDOS EN LIBROS                                                                           */
/***************************************************************************************************************/
$cia = $db->query("SELECT * FROM saldos");
$sql = "";
for ($i=0; $i<count($cia); $i++) {
	$cheques = $db->query("SELECT sum(importe) FROM estado_cuenta WHERE num_cia = {$cia[$i]['num_cia']} AND tipo_mov = 'TRUE' AND fecha_con IS NULL AND cuenta = 1");
	$depositos = $db->query("SELECT sum(importe) FROM estado_cuenta WHERE num_cia = {$cia[$i]['num_cia']} AND tipo_mov = 'FALSE' AND fecha_con IS NULL AND cuenta = 1");
	$sql .= "UPDATE saldos SET saldo_libros = saldo_bancos - " . ($cheques[0]['sum'] > 0 ? $cheques[0]['sum'] : "0") . " + " . ($depositos[0]['sum'] > 0 ? $depositos[0]['sum'] : "0") . " WHERE num_cia = {$cia[$i]['num_cia']} AND cuenta = 1;\n";
}
$db->query($sql);


/***************************************************************************************************************/
/* ALMACENAR DIFERENCIA DE SALDOS CONCILIADOS                                                                  */
/***************************************************************************************************************/
function buscar_mov($array, $num_cia, $tipo_mov) {
	if ($array === FALSE)
		return 0;
	
	for ($i = 0; $i < count($array); $i++)
		if ($array[$i]['num_cia'] == $num_cia && $array[$i]['tipo_mov'] == $tipo_mov)
			return number_format($array[$i]['sum'], 2, ".", "");
	
	return 0;
}

for ($cuenta = 1; $cuenta <= 2; $cuenta++)
	$tabla_saldo = $cuenta == 1 ? "saldo_banorte" : "saldo_santander";
	$clabe_cuenta = $cuenta == 1 ? "clabe_cuenta" : "clabe_cuenta2";
	$tabla_movs = $cuenta == 1 ? "mov_banorte" : "mov_santander";
	
	$sql = "SELECT num_cia, nombre_corto, $clabe_cuenta, saldo_bancos, saldo FROM saldos LEFT JOIN $tabla_saldo USING (num_cia) LEFT JOIN catalogo_companias USING (num_cia) WHERE cuenta = $cuenta ORDER BY num_cia";
	$result = $db->query($sql);
	
	$fecha = date('d/m/Y');
	
	if ($result) {
		$cont = 0;
		
		$sql = "SELECT num_cia, tipo_mov, sum(importe) FROM $tabla_movs WHERE fecha_con IS NULL GROUP BY num_cia, tipo_mov ORDER BY num_cia, tipo_mov";
		$mov_pen = $db->query($sql);
		
		$total = 0;
		$cont = 0;
		foreach ($result as $saldo)
			if (round($saldo['saldo_bancos'] + buscar_mov($mov_pen, $saldo['num_cia'], 'f') - buscar_mov($mov_pen, $saldo['num_cia'], 't') - $saldo['saldo'], 2) != 0) {
				$pendientes = buscar_mov($mov_pen, $saldo['num_cia'], 'f') - buscar_mov($mov_pen, $saldo['num_cia'], 't');
				$saldo_final = $saldo['saldo_bancos'] + $pendientes;
				
				$data[$cont]['num_cia'] = $saldo['num_cia'];
				$data[$cont]['fecha'] = $fecha;
				$data[$cont]['cuenta'] = $cuenta;
				$data[$cont]['saldo_conciliado'] = get_val($saldo['saldo_bancos']);
				$data[$cont]['pendientes'] = get_val($pendientes);
				$data[$cont]['saldo_final'] = get_val($saldo_final);
				$data[$cont]['saldo_capturado'] = trim($saldo['saldo']) != '' ? get_val($saldo['saldo']) : NULL;
				$data[$cont]['diferencia'] = trim($saldo['saldo']) != '' ? $saldo_final - $saldo['saldo'] : NULL;
			}
		$db->query($db->multiple_insert('historico_saldos', $data));
		
}

/***************************************************************************************************************/
/* ACTUALIZACION DE CONSUMOS MENSUALES                                                                         */
/***************************************************************************************************************/

// Obtener listado de panaderias
/*$sql = "SELECT num_cia FROM catalogo_companias WHERE num_cia < 100 ORDER BY num_cia";
$cia = $db->query($sql);

$dia_actual = date("d");
$mes_actual = date("m");
$anio_actual = date("Y");

$mes = $dia < 5 ? date("m", mktime(0, 0, 0, $mes_actual - 1, 1, $anio_actual)) : date("m", mktime(0, 0, 0, $mes_actual, 1, $anio_actual));
$anio = $dia < 5 ? date("Y", mktime(0, 0, 0, $mes_actual - 1, 1, $anio_actual)) : date("Y", mktime(0, 0, 0, $mes_actual, 1, $anio_actual));

$fecha1 = $dia < 5 ? date("d/m/Y", mktime(0, 0, 0, $mes_actual - 1, 1, $anio_actual)) : date("d/m/Y", mktime(0, 0, 0, $mes_actual, 1, $anio_actual));
$fecha2 = $dia < 5 ? date("d/m/Y", mktime(0, 0, 0, $mes_actual, 0, $anio_actual)) : date("d/m/Y", mktime(0, 0, 0, $mes_actual + 1, 0, $anio_actual));

// Recorrer compañías
$sql = "";
for ($i=0; $i<count($cia); $i++) {
	$num_cia = $cia[$i]['num_cia'];
	
	// Obtener todas las materias primas
	$mp = $db->query("SELECT codmp FROM mov_inv_real WHERE num_cia = $num_cia AND fecha BETWEEN '$fecha1' AND '$fecha2' AND tipo_mov = 'TRUE' GROUP BY codmp ORDER BY codmp");
	
	for ($j=0; $j<count($mp); $j++) {
		$codmp = $mp[$j]['codmp'];
		
		$result = $db->query("SELECT SUM(cantidad) FROM mov_inv_real WHERE num_cia = $num_cia AND codmp = $codmp AND fecha BETWEEN '$fecha1' AND '$fecha2' AND tipo_mov = 'TRUE'");
		
		$consumo = $result[0]['sum'] > 0 ? round($result[0]['sum'],2) : 0;
		
		if ($id = $db->query("SELECT id FROM consumos_mensuales WHERE num_cia = $num_cia AND codmp = $codmp AND mes = $mes AND anio = $anio"))
			$sql .= "UPDATE consumos_mensuales SET consumo = $consumo WHERE id = {$id[0]['id']};\n";
		else
			$sql .= "INSERT INTO consumos_mensuales (num_cia,codmp,mes,anio,consumo) VALUES ($num_cia,$codmp,$mes,$anio,$consumo);\n";
	}
}
$db->query($sql);*/

$db->desconectar();

/***************************************************************************************************************/
/* RESPALDAR LA BASE DE DATOS                                                                                  */
/***************************************************************************************************************/
//shell_exec("pg_dump lecaroz | gzip > /root/backup/dump_lecaroz_" . date("Y_M_d") . ".sql.gz");
?>