<?php
include './includes/class.db.inc.php';
include './includes/dbstatus.php';

$db = new DBclass($dsn, "autocommit=yes");

$descripcion_error[1] = "No hay resultados";

function buscar_fac($index, $codmp, $unidades) {
	global $movs, $difInv, $costo_dif;
	$cantidad = 0;
	$total = 0;
	
	$existencia = $unidades;
	
	// Buscar en las facturas
	for ($i = $index + 1; $i < count($movs); $i++)
		if ($movs[$i]['tipo_mov'] == "f" && $movs[$i]['codmp'] == $codmp) {
			$cantidad += $movs[$i]['cantidad'];
			$total += $movs[$i]['total_mov'];
			
			$existencia += $movs[$i]['cantidad'];
			
			if ($existencia >= 0)
				return $total / $cantidad;
		}
		else if ($movs[$i]['tipo_mov'] == "t" && $movs[$i]['codmp'] == $codmp)
			$existencia -= $movs[$i]['cantidad'];
	
	// Buscar en las diferencias
	if ($difInv)
		foreach ($difInv as $reg)
			if ($reg['tipo_mov'] == "f" && $reg['codmp'] == $codmp) {
				$cantidad += $reg['cantidad'];
				$total += $reg['cantidad'] * $costo_dif;
				
				$existencia += $reg['cantidad'];
				
				if ($existencia >= 0)
					return $total / $cantidad;
			}
			else if ($reg['tipo_mov'] == "t" && $reg['codmp'] == $codmp)
				$existencia -= $reg['cantidad'];
	
	return FALSE;
}

function costo_dif($codmp, $costo_prom) {
	global $movs;
	
	if (!$movs)
		return $costo_prom;
	
	$cantidad = 0;
	$valor = 0;
	foreach ($movs as $mov)
		if ($mov['codmp'] == $codmp && $mov['tipo_mov'] == "f") {
			$cantidad += $mov['cantidad'];
			$valor += $mov['total_mov'];
		}
	
	return $cantidad > 0 ? $valor / $cantidad : $costo_prom;
}

$fecha_his = date("d/m/Y", mktime(0, 0, 0, $_GET['mes'], 0, $_GET['anio']));
$fecha1 = "01/$_GET[mes]/$_GET[anio]";
$fecha2 = date("d/m/Y", mktime(0, 0, 0, $_GET['mes'] + 1, 0, $_GET['anio']));
$table = isset($_GET['table']) ? $_GET['table'] : "real";
$query = "";

$cias = $db->query("SELECT num_cia FROM inventario_$table GROUP BY num_cia ORDER BY num_cia");

foreach ($cias as $cia) {
	$num_cia = $cia['num_cia'];
	
	$sql = "SELECT num_cia, codmp, nombre, existencia, precio_unidad, controlada, tipo FROM historico_inventario LEFT JOIN catalogo_mat_primas USING (codmp) WHERE num_cia = $num_cia AND fecha = '$fecha_his'";
	//$sql .= $_GET['codmp'] > 0 ? " AND codmp = $_GET[codmp]" : "";
	//$sql .= !isset($_GET['gas']) ? " AND codmp NOT IN (90)" : "";
	//$sql .= $_GET['ctrl'] > 0 ? ($_GET['ctrl'] == 1 ? " AND controlada = 'TRUE'" : " AND controlada = 'FALSE'") : "";
	//$sql .= $_GET['tipo'] > 0 ? " AND tipo = $_GET[tipo]" : "";
	$sql .= " ORDER BY num_cia, controlada DESC, tipo, codmp";
	$inv = $db->query($sql);

	if ($inv) {
		$tvalores_ini = 0;
		$tventrada = 0;
		$tvsalida = 0;
		$tvalores = 0;
		
		foreach ($inv as $reg) {
			$sql = "SELECT codmp, fecha, descripcion, cod_turno, tipo_mov, cantidad, precio_unidad, total_mov FROM mov_inv_$table WHERE num_cia = $num_cia AND";
			$sql .= " fecha BETWEEN '$fecha1' AND '$fecha2' AND codmp = $reg[codmp] AND descripcion != 'DIFERENCIA INVENTARIO' ORDER BY codmp, fecha, tipo_mov, cantidad DESC";
			$movs = $db->query($sql);
			
			if (isset($_GET['dif'])) {
				$sql = "SELECT codmp, fecha, descripcion, cod_turno, tipo_mov, cantidad, precio_unidad, total_mov FROM mov_inv_$table WHERE num_cia = $num_cia AND fecha = '$fecha2'";
				$sql .= " AND codmp = $reg[codmp] AND descripcion = 'DIFERENCIA INVENTARIO' ORDER BY codmp, fecha, tipo_mov, cantidad DESC";
				$difInv = $db->query($sql);
			}
			else
				$difInv = FALSE;
			
			$unidades = $reg['existencia'] != 0 ? $reg['existencia'] : 0;
			$valores  = $reg['existencia'] != 0 ? $reg['existencia'] * $reg['precio_unidad'] : 0;
			$tvalores_ini += $valores;
			
			$uentrada  = 0;	// Unidades de Entrada
			$ventrada  = 0;	// Valores de Entrada
			$usalida   = 0;	// Unidades de Salida
			$vsalida   = 0;	// Valores de Salida
			$dif       = 0;	// Diferencia de Costos en Entradas
			$costo_prom = $reg['precio_unidad'];	// Costo Promedio
			$costo_ant = $costo_prom;	// Costo anterior a un movimiento
			$unidades_ant = $unidades;	// Unidades anteriores a un movimiento
			
			$arrastre = FALSE;	// Flag. Indica si debe arrastrarse el costo promedio
			
			// Calcular el costo de la diferencia apartir de las entradas
			$costo_dif = costo_dif($reg['codmp'], $costo_prom);
			
			if ($movs)
				foreach ($movs as $i => $mov) {
					// Salidas
					if ($mov['tipo_mov'] == "t") {
						$unidades -= $mov['cantidad'];
						
						// Si la existencia actual negativa, calcular costo promedio
						if ($unidades < 0) {
							// Buscar costo de las siguientes facturas que satisfagan la existencia negativa actual si no hay arrastre del costo
							if ($arrastre == TRUE)
								$proximo_costo = $costo_prom;
							else
								$proximo_costo = buscar_fac($i, $reg['codmp'], $unidades);
							
							// Dividir valores de salida
							$val_1 = ($mov['cantidad'] + $unidades) * $costo_ant;
							$val_2 = abs($unidades) * $proximo_costo;
							$val_sal = $val_1 + $val_2;
							
							$costo = $costo_ant;
							$costo_prom = $proximo_costo;
							$costo_ant = $costo_prom;
						}
						// Calcular arrastre normalmente
						else {
							$val_sal = $mov['cantidad'] * $costo_prom;
							
							$costo = $costo_ant;
							$costo_ant = $costo_prom;
						}
						
						$unidades_ant = $unidades;
						
						// Calcular arrastres de salida
						$usalida += $mov['cantidad']; 
						$vsalida  += $val_sal;
					}
					// Entradas
					else if ($mov['tipo_mov'] == "f") {
						@$precio_unidad = $mov['total_mov'] / $mov['cantidad'];	// Costo unitario de la entrada
						$unidades += $mov['cantidad'];
						
						$valor_ant = $unidades_ant * $costo_prom;
						$costo_ant = $costo_prom;
						
						// Si la existencia anterior y actual son negativas, no calcular costo promedio y poner bandera de arrastre en TRUE
						if ($unidades_ant < 0 && $unidades < 0)
							$arrastre = TRUE;
						// Si la existencia anterior es negativa y la actual es positiva, no calcular costo promedio y poner bandera de arrastre en FALSE
						else if ($unidades_ant < 0 && $unidades >= 0)
							$arrastre = FALSE;
						// Calcular costo promedio normalmente
						else
							@$costo_prom = ($unidades_ant * $costo_ant + $mov['cantidad'] * $precio_unidad) / ($unidades_ant + $mov['cantidad']);
						
						// Actualizar existencia anterior a la existencia actual
						$unidades_ant = $unidades;
						
						// Diferencia de costo inicial y costo final
						$diferencia_costo = $unidades != 0 ? $precio_unidad - $costo_prom : 0;
						$dif += $diferencia_costo;
						
						// Calcular arrastres de entrada
						$uentrada += $mov['cantidad'];
						$ventrada  += $mov['cantidad'] * $precio_unidad;
					}
				}
			
			// Diferencia
			if ($difInv) {
				// Diferencia en contra
				if ($difInv[0]['tipo_mov'] == "t") {
					$unidades -= $difInv[0]['cantidad'];
					
					$val_sal = $difInv[0]['cantidad'] * $costo_prom;
					
					$costo = $costo_ant;
					$costo_ant = $costo_prom;
					
					$unidades_ant = $unidades;
					
					$usalida += $difInv[0]['cantidad'];
					$vsalida  += $difInv[0]['cantidad'] * $costo_prom;
				}
				// Dieferencia a favor
				else if ($difInv[0]['tipo_mov'] == "f") {
					@$precio_unidad = $costo_dif;
					
					$unidades += $difInv[0]['cantidad'];
					
					// Si la existencia anterior y actual son negativas, no calcular costo promedio y poner bandera de arrastre en TRUE
					if ($unidades_ant < 0 && $unidades < 0)
						$arrastre = TRUE;
					// Si la existencia anterior es negativa y la actual es positiva, no calcular costo promedio y poner bandera de arrastre en FALSE
					else if ($unidades_ant < 0 && $unidades >= 0)
						$arrastre = FALSE;
					// Si la existencia anterior es positiva, el precio de la diferencia sera el costo promedio
					else if ($unidades_ant > 0)
						$precio_unidad = $costo_prom;
					//Calcular costo promedio normalmente
					else
						$costo_prom = ($unidades_ant * $costo_ant + $difInv[0]['cantidad'] * $precio_unidad) / ($unidades_ant + $difInv[0]['cantidad']);
					
					// Diferencia de costo inicial y costo final
					$diferencia_costo = $unidades != 0 ? $precio_unidad - $costo_prom : 0;
					$dif += $diferencia_costo;
					
					$uentrada += $difInv[0]['cantidad'];
					$ventrada  += $difInv[0]['cantidad'] * $precio_unidad;
				}
			}
			
			$tventrada += $ventrada;
			$tvsalida += $vsalida;
			$tvalores += $unidades * $costo_prom;
			
			// Opciones extras
			if (isset($_GET['act_real']))
				$query .= "UPDATE inventario_real SET existencia = $unidades, precio_unidad = " . ($costo_prom != 0 ? $costo_prom : "0.00") . " WHERE num_cia = $num_cia AND codmp = $reg[codmp];\n";
			if (isset($_GET['act_virtual']))
				$query .= "UPDATE inventario_virtual SET existencia = $unidades, precio_unidad = " . ($costo_prom != 0 ? $costo_prom : "0.00") . " WHERE num_cia = $num_cia AND codmp = $reg[codmp];\n";
			if (isset($_GET['act_his']))
				$query .= "UPDATE historico_inventario SET existencia = $unidades, precio_unidad = " . ($costo_prom != 0 ? $costo_prom : "0.00") . " WHERE num_cia = $num_cia AND codmp = $reg[codmp] AND fecha = '$fecha2';\n";
		}
	}
}

echo "<pre>$query</pre>";
$db->query($query);
?>