<?php
// AUXILIAR DE MATERIAS PRIMAS
// Tablas 'historico_inventario', 'mov_inv_real'
// Menu 'No definido'

//define ('IDSCREEN',2); // ID de pantalla

// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);

//if ($_SESSION['iduser'] != 1) die("La estoy modificando...   GOMEN   ^_^");

// Funciones
function turno($cod_turno) {
	switch ($cod_turno) {
		case 1: $string = "FD"; break;
		case 2: $string = "FN"; break;
		case 3: $string = "BD"; break;
		case 4: $string = "REP"; break;
		case 8: $string = "PIC"; break;
		case 9: $string = "GEL"; break;
		case 10: $string = "DESP"; break;
		default: $string = "&nbsp;"; break;
	}
	
	return $string;
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

// --------------------------------- Descripcion de errores --------------------------------------------------
$descripcion_error[1] = "No hay resultados";

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower("./plantillas/header.tpl");

// Incluir el cuerpo del documento
$tpl->assignInclude("body", "./plantillas/pan/aux_inv_v3.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
if (isset($_SESSION['menu']))
	$tpl->assign("menucnt", "$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (!isset($_GET['listado'])) {
	$tpl->newBlock("datos");
	
	if (date("d") > 5) {
		$tpl->assign(date("n", mktime(0, 0, 0, date("m"), 1, date("Y"))), "selected");
		$tpl->assign("anio", date("Y"));
	}
	else {
		$tpl->assign(date("n", mktime(0, 0, 0, date("m") - 1, 1, date("Y"))), "selected");
		$tpl->assign("anio", date("Y", mktime(0, 0, 0, date("m") - 1, 1, date("Y"))));
	}

	$tpl->printToScreen();
	die;
}

// Crear conexion a la base de datos
$db = new DBclass($dsn);

// Construir fecha inicial y fecha final
$fecha_historico = date("d/m/Y", mktime(0, 0, 0, $_GET['mes'], 0, $_GET['anio']));
$fecha1 = date("d/m/Y", mktime(0, 0, 0, $_GET['mes'], 1, $_GET['anio']));
$fecha2 = date("d/m/Y", mktime(0, 0, 0, $_GET['mes'] + 1, 0, $_GET['anio']));

if ($_GET['listado'] == "desglozado") {
	$tpl->newBlock("listado_des");
	// Saldos anteriores
	$sql = "SELECT num_cia, codmp, nombre, existencia, precio_unidad FROM historico_inventario LEFT JOIN catalogo_mat_primas USING (codmp) WHERE";
	// Si es para una compañía en específico
	$sql .= $_GET['num_cia'] > 0 ? " num_cia = $_GET[num_cia] AND" : "";
	// Si es para un producto en específico
	$sql .= $_GET['codmp'] > 0 ? " codmp = $_GET[codmp] AND" : "";
	// Si es controlada o no controlada, si es materia prima o material de empaque
	$sql .= $_GET['controlada'] != "todas" && $_GET['codmp'] < 1 ? ($_GET['controlada'] == "si" ? " controlada = 'TRUE' AND" : " controlada = 'FALSE' AND" . ($_GET['tipo'] != "todas" ? ($_GET['tipo'] == "mp" ? " tipo = 1 AND" : " tipo = 2 AND") : "")) : "";
	$sql .= " fecha = '$fecha_historico' ORDER BY num_cia, controlada DESC, tipo, codmp";
	$saldo_ant = $db->query($sql);
	
	// Saldo actual (omitir diferencias)
	$sql = "SELECT num_cia, fecha, codmp, nombre, descripcion, cod_turno, tipo_mov, cantidad, precio_unidad, total_mov FROM mov_inv_real LEFT JOIN catalogo_mat_primas USING (codmp) WHERE";
	// Si es para una compañía en específico
	$sql .= $_GET['num_cia'] > 0 ? " num_cia = $_GET[num_cia] AND" : "";
	// Si es para un producto en específico
	$sql .= $_GET['codmp'] > 0 ? " codmp = $_GET[codmp] AND" : "";
	// Si es controlada o no controlada, si es materia prima o material de empaque
	$sql .= $_GET['controlada'] != "todas" && $_GET['codmp'] < 1 ? ($_GET['controlada'] == "si" ? " controlada = 'TRUE' AND" : " controlada = 'FALSE' AND" . ($_GET['tipo'] != "todas" ? ($_GET['tipo'] == "mp" ? " tipo = 1 AND" : " tipo = 2 AND") : "")) : "";
	$sql .= " fecha BETWEEN '$fecha1' AND '$fecha2' AND descripcion != 'DIFERENCIA INVENTARIO' ORDER BY num_cia, controlada DESC, tipo, codmp, fecha, tipo_mov, cantidad DESC";
	$saldo = $db->query($sql);
	
	// Diferencias del mes
	if (isset($_GET['dif'])) {
		$sql = "SELECT num_cia, fecha, codmp, nombre, descripcion, cod_turno, tipo_mov, cantidad, precio_unidad, total_mov FROM mov_inv_real LEFT JOIN catalogo_mat_primas USING (codmp) WHERE";
		// Si es para una compañía en específico
		$sql .= $_GET['num_cia'] > 0 ? " num_cia = $_GET[num_cia] AND" : "";
		// Si es para un producto en específico
		$sql .= $_GET['codmp'] > 0 ? " codmp = $_GET[codmp] AND" : "";
		// Si es controlada o no controlada, si es materia prima o material de empaque
		$sql .= $_GET['controlada'] != "todas" && $_GET['codmp'] < 1 ? ($_GET['controlada'] == "si" ? " controlada = 'TRUE' AND" : " controlada = 'FALSE' AND" . ($_GET['tipo'] != "todas" ? ($_GET['tipo'] == "mp" ? " tipo = 1 AND" : " tipo = 2 AND") : "")) : "";
		$sql .= " fecha BETWEEN '$fecha1' AND '$fecha2' AND descripcion = 'DIFERENCIA INVENTARIO' ORDER BY num_cia, controlada DESC, tipo, codmp, fecha, tipo_mov";
		$difInv = $db->query($sql);
	}
	else
		$difInv = FALSE;
	
	$nummp = count($saldo_ant);
	$nummov = count($saldo);
	
	$num_cia = NULL;
	for ($j = 0; $j < $nummp; $j++) {
		if ($num_cia != $saldo_ant[$j]['num_cia']) {
			$num_cia = $saldo_ant[$j]['num_cia'];
			
			$tpl->newBlock("cia_des");
			$tpl->assign("num_cia", $num_cia);
			$nombre_cia = $db->query("SELECT nombre_corto FROM catalogo_companias WHERE num_cia = $num_cia");
			$tpl->assign("nombre_cia", $nombre_cia[0]['nombre_corto']);
			$tpl->assign("mes", mes_escrito($_GET['mes'], TRUE));
			$tpl->assign("anio", $_GET['anio']);
		}
		
		$codmp = $saldo_ant[$j]['codmp'];
		$nombremp = $saldo_ant[$j]['nombre'];
		
		// Datos del producto
		$tpl->newBlock("mp_des");
		$tpl->assign("codmp", $codmp);
		$tpl->assign("nombremp", $nombremp);
		
		$tpl->assign("unidades_anteriores", number_format($saldo_ant[$j]['existencia'], 2, ".", ","));
		$valores_ant = $saldo_ant[$j]['existencia'] * $saldo_ant[$j]['precio_unidad'];
		$tpl->assign("valores_anteriores", number_format($valores_ant, 2, ".", ","));
		$tpl->assign("costo_anterior", number_format($saldo_ant[$j]['precio_unidad'], 4, ".", ","));
		
		$unidades = $saldo_ant[$j]['existencia'];
		$valores  = $saldo_ant[$j]['existencia'] * $saldo_ant[$j]['precio_unidad'];
		
		$unidades_entrada = 0;	// Unidades de entrada
		$valores_entrada  = 0;	// Valores de entrada
		$unidades_salida  = 0;	// Unidades de salida
		$valores_salida   = 0;	// Valores de salida
		$diferencia       = 0;	// Diferencia de costos en entradas
		$costo_promedio   = $saldo_ant[$j]['precio_unidad'];	// Costo promedio
		$costo_ant = $costo_promedio;	// Costo anterior a un movimiento
		$unidades_ant = $unidades;	// Unidades anteriores a un movimiento
		
		$arrastre = FALSE;	// Flag. Indica si debe arrastrarse el costo promedio
		
		// Calcular el costo de la diferencia apartir de las entradas
		$costo_dif = costo_dif($saldo, $num_cia, $codmp, $costo_promedio);
		
		for ($k = 0; $k < $nummov; $k++) {
			if ($saldo[$k]['num_cia'] == $num_cia && $saldo[$k]['codmp'] == $codmp) {
				$tpl->newBlock("fila_des");
				$tpl->assign("fecha", $saldo[$k]['fecha']);
				$tpl->assign("concepto", $saldo[$k]['descripcion']);
				$tpl->assign("turno", turno($saldo[$k]['cod_turno']));
				
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
						
						$costo = $costo_ant;
						$costo_promedio = $proximo_costo;
						$costo_ant = $costo_promedio;
					}
					// Calcular arratre normalmente
					else {
						$val_sal = $saldo[$k]['cantidad'] * $costo_promedio;
						
						$costo = $costo_ant;
						$costo_ant = $costo_promedio;
					}
					
					$unidades_ant = $unidades;
					
					$tpl->assign("costo_unitario", number_format($costo, 4, ".", ","));
					$tpl->assign("unidades_salida", number_format($saldo[$k]['cantidad'], 2, ".", ","));
					$tpl->assign("valores_salida", number_format($val_sal, 2, ".", ","));
					$tpl->assign("unidades_entrada", "&nbsp;");
					$tpl->assign("valores_entrada", "&nbsp;");
					
					$tpl->assign("unidades", number_format($unidades, 2, ".", ","));
					$tpl->assign("valores", number_format($unidades * $costo_promedio, 2, ".", ","));
					$tpl->assign("costo_promedio", number_format($costo_promedio, 4, ".", ","));
					$tpl->assign("diferencia_costo", "&nbsp;");
					
					// Calcular arratres de salida
					$unidades_salida += $saldo[$k]['cantidad']; 
					$valores_salida  += $val_sal;
				}
				// Entradas
				else if ($saldo[$k]['tipo_mov'] == "f") {
					@$precio_unidad = $saldo[$k]['total_mov'] / $saldo[$k]['cantidad'];	// Costo unitario de la entrada
					$tpl->assign("costo_unitario", number_format($precio_unidad, 4, ".", ","));
					$tpl->assign("unidades_entrada", number_format($saldo[$k]['cantidad'], 2, ".", ","));
					$tpl->assign("valores_entrada", number_format($saldo[$k]['cantidad'] * $precio_unidad, 2, ".", ","));
					$tpl->assign("unidades_salida", "&nbsp;");
					$tpl->assign("valores_salida", "&nbsp;");
					
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
					
					$tpl->assign("unidades", number_format($unidades, 2, ".", ","));
					$tpl->assign("valores", number_format($unidades * $costo_promedio, 2, ".", ","));
					$tpl->assign("costo_promedio", number_format($costo_promedio, 4, ".", ","));
					// Diferencia de costo inicial y costo final
					$diferencia_costo = $unidades != 0 ? $precio_unidad - $costo_promedio : 0;
					$diferencia += $diferencia_costo;
					$tpl->assign("diferencia_costo", $diferencia_costo != 0 ? number_format($diferencia_costo, 4, ".", ",") : "&nbsp;");
					
					// Calcular arrastres de entrada
					$unidades_entrada += $saldo[$k]['cantidad'];
					$valores_entrada  += $saldo[$k]['cantidad'] * $precio_unidad;
				}
			}
		}
		// Buscar diferencia
		$idDif = buscar_dif($difInv, $num_cia, $codmp);
		if ($idDif !== FALSE) {
			$tpl->newBlock("fila_des");
			$tpl->assign("fecha", $difInv[$idDif]['fecha']);
			$tpl->assign("concepto", $difInv[$idDif]['descripcion']);
			$tpl->assign("turno", "&nbsp;");
			
			// Diferencia en contra
			if ($difInv[$idDif]['tipo_mov'] == "t") {
				$unidades -= $difInv[$idDif]['cantidad'];
				
				$val_sal = $difInv[$idDif]['cantidad'] * $costo_promedio;
				
				$costo = $costo_ant;
				$costo_ant = $costo_promedio;
				
				$unidades_ant = $unidades;
				
				$tpl->assign("costo_unitario", number_format($costo, 4, ".", ","));
				$tpl->assign("unidades_salida", number_format($difInv[$idDif]['cantidad'], 2, ".", ","));
				$tpl->assign("valores_salida", number_format($val_sal, 2, ".", ","));
				$tpl->assign("unidades_entrada", "&nbsp;");
				$tpl->assign("valores_entrada", "&nbsp;");
				
				$tpl->assign("unidades", number_format($unidades, 2, ".", ","));
				$tpl->assign("valores", number_format($unidades * $costo_promedio, 2, ".", ","));
				$tpl->assign("costo_promedio", number_format($costo_promedio, 4, ".", ","));
				$tpl->assign("diferencia_costo", "&nbsp;");
				
				$unidades_salida += $difInv[$idDif]['cantidad'];
				$valores_salida  += $difInv[$idDif]['cantidad'] * $costo_promedio;
			}
			// Entradas
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
				//Calcular costo promedio normalmente
				else
					$costo_promedio = ($unidades_ant * $costo_ant + $difInv[$idDif]['cantidad'] * $precio_unidad) / ($unidades_ant + $difInv[$idDif]['cantidad']);
				
				$tpl->assign("costo_unitario", number_format($precio_unidad, 4, ".", ","));
				$tpl->assign("unidades_entrada", number_format($difInv[$idDif]['cantidad'], 2, ".", ","));
				$tpl->assign("valores_entrada", number_format($difInv[$idDif]['cantidad'] * $precio_unidad, 2, ".", ","));
				$tpl->assign("unidades_salida", "&nbsp;");
				$tpl->assign("valores_salida", "&nbsp;");
				
				$tpl->assign("unidades", number_format($unidades, 2, ".", ","));
				$tpl->assign("valores", number_format($unidades * $costo_promedio, 2, ".", ","));
				$tpl->assign("costo_promedio", number_format($costo_promedio, 4, ".", ","));
				// Diferencia de costo inicial y costo final
				$diferencia_costo = $unidades != 0 ? $precio_unidad - $costo_promedio : 0;
				$diferencia += $diferencia_costo;
				$tpl->assign("diferencia_costo", $diferencia_costo != 0 ? number_format($diferencia_costo, 4, ".", ",") : "&nbsp;");
				
				$unidades_entrada += $difInv[$idDif]['cantidad'];
				$valores_entrada  += $difInv[$idDif]['cantidad'] * $precio_unidad;
			}
		}
		
		// Mostrar totales
		$tpl->assign("mp_des.unidades_entrada", number_format($unidades_entrada, 2, ".", ","));
		$tpl->assign("mp_des.valores_entrada", number_format($valores_entrada, 2, ".", ","));
		$tpl->assign("mp_des.unidades_salida", number_format($unidades_salida, 2, ".", ","));
		$tpl->assign("mp_des.valores_salida", number_format($valores_salida, 2, ".", ","));
		$tpl->assign("mp_des.unidades", number_format($unidades, 2, ".", ","));
		$tpl->assign("mp_des.valores", number_format($unidades * $costo_promedio, 2, ".", ","));
		$tpl->assign("mp_des.costo_promedio", number_format($costo_promedio, 4, ".", ","));
		$tpl->assign("mp_des.total_diferencia", number_format($diferencia, 4, ".", ","));
		
		/***********************************************************************************************************/
		/* CODIGO PARA CORREGIR UNICAMENTE AVIO, HISTORICOS Y DEMAS                                                */
		/* DESCOMENTAR LOS SCRIPTS PARA CORREGIR                                                                   */
		/***********************************************************************************************************/
		
		/*$db->comenzar_transaccion();
		$sql = "UPDATE inventario_real SET existencia = $unidades, precio_unidad = $costo_promedio WHERE num_cia = $num_cia AND codmp = $codmp";
		$db->query($sql);
		$sql = "UPDATE inventario_virtual SET existencia = $unidades, precio_unidad = $costo_promedio WHERE num_cia = $num_cia AND codmp = $codmp";
		$db->query($sql);
		$db->terminar_transaccion();*/
		/***********************************************************************************************************/
	}
	$tpl->printToScreen();
	$db->desconectar();
	die;
	
}
else if ($_GET['listado'] == "totales") {
	$inv = "inventario_real";
	$mov_inv = "mov_inv_real";
	
	$tpl->newBlock("listado_totales");
	// Saldos anteriores
	$sql = "SELECT num_cia, codmp, nombre, existencia, precio_unidad, controlada, tipo FROM historico_inventario LEFT JOIN catalogo_mat_primas USING (codmp) WHERE";
	// Si es para una compañía en específico
	$sql .= $_GET['num_cia'] > 0 ? " num_cia = $_GET[num_cia] AND" : " num_cia < 100 AND";
	// Si es para un producto en específico
	$sql .= $_GET['codmp'] > 0 ? " codmp = $_GET[codmp] AND" : "";
	// Si es controlada o no controlada, si es materia prima o material de empaque
	$sql .= $_GET['controlada'] != "todas" && $_GET['codmp'] < 1 ? ($_GET['controlada'] == "si" ? " controlada = 'TRUE' AND" : " controlada = 'FALSE' AND" . ($_GET['tipo'] != "todas" ? ($_GET['tipo'] == "mp" ? " tipo = 1 AND" : " tipo = 2 AND") : "")) : "";
	// ¿Incluir Gas?
	$sql .= empty($_GET['gas']) ? " codmp NOT IN (90) AND" : "";
	$sql .= " fecha = '$fecha_historico' ORDER BY num_cia, controlada DESC, tipo, codmp";
	$saldo_ant = $db->query($sql);
	
	// Saldo actual (omitir diferencias)
	$sql = "SELECT num_cia, fecha, codmp, nombre, descripcion, cod_turno, tipo_mov, cantidad, precio_unidad, total_mov FROM $mov_inv LEFT JOIN catalogo_mat_primas USING (codmp) WHERE";
	// Si es para una compañía en específico
	$sql .= $_GET['num_cia'] > 0 ? " num_cia = $_GET[num_cia] AND" : " num_cia < 100 AND";
	// Si es para un producto en específico
	$sql .= $_GET['codmp'] > 0 ? " codmp = $_GET[codmp] AND" : "";
	// Si es controlada o no controlada, si es materia prima o material de empaque
	$sql .= $_GET['controlada'] != "todas" && $_GET['codmp'] < 1 ? ($_GET['controlada'] == "si" ? " controlada = 'TRUE' AND" : " controlada = 'FALSE' AND" . ($_GET['tipo'] != "todas" ? ($_GET['tipo'] == "mp" ? " tipo = 1 AND" : " tipo = 2 AND") : "")) : "";
	// ¿Incluir Gas?
	$sql .= empty($_GET['gas']) ? " codmp NOT IN (90) AND" : "";
	$sql .= " fecha BETWEEN '$fecha1' AND '$fecha2' AND descripcion != 'DIFERENCIA INVENTARIO' ORDER BY num_cia, controlada DESC, tipo, codmp, fecha, tipo_mov, cantidad DESC";
	$saldo = $db->query($sql);
	
	// Diferencias del mes
	if (isset($_GET['dif'])) {
		$sql = "SELECT num_cia, fecha, codmp, nombre, descripcion, cod_turno, tipo_mov, cantidad, precio_unidad, total_mov FROM $mov_inv LEFT JOIN catalogo_mat_primas USING (codmp) WHERE";
		// Si es para una compañía en específico
		$sql .= $_GET['num_cia'] > 0 ? " num_cia = $_GET[num_cia] AND" : " num_cia < 100 AND";
		// Si es para un producto en específico
		$sql .= $_GET['codmp'] > 0 ? " codmp = $_GET[codmp] AND" : "";
		// Si es controlada o no controlada, si es materia prima o material de empaque
		$sql .= $_GET['controlada'] != "todas" && $_GET['codmp'] < 1 ? ($_GET['controlada'] == "si" ? " controlada = 'TRUE' AND" : " controlada = 'FALSE' AND" . ($_GET['tipo'] != "todas" ? ($_GET['tipo'] == "mp" ? " tipo = 1 AND" : " tipo = 2 AND") : "")) : "";
		// ¿Incluir Gas?
		$sql .= empty($_GET['gas']) ? " codmp NOT IN (90) AND" : "";
		$sql .= " fecha BETWEEN '$fecha1' AND '$fecha2' AND descripcion = 'DIFERENCIA INVENTARIO' ORDER BY num_cia, controlada DESC, tipo, codmp, fecha, tipo_mov";
		$difInv = $db->query($sql);
	}
	else
		$difInv = FALSE;
	
	$nummp = count($saldo_ant);
	$nummov = count($saldo);
	
	$sql = "";
	
	$num_cia = NULL;
	$gran_total_unidades_ant = 0;
	$gran_total_valores_ant = 0;
	$gran_total_unidades_entrada = 0;
	$gran_total_valores_entrada = 0;
	$gran_total_unidades_salida = 0;
	$gran_total_valores_salida = 0;
	$gran_total_unidades = 0;
	$gran_total_valores = 0;
	for ($j = 0; $j < $nummp; $j++) {
		if ($num_cia != $saldo_ant[$j]['num_cia']) {
			if ($num_cia != NULL) {
				$tpl->assign("cia_total.valores_anteriores", number_format($total_valores_anteriores, 2, ".", ","));
				$tpl->assign("cia_total.valores_entrada", number_format($total_valores_entrada, 2, ".", ","));
				$tpl->assign("cia_total.valores_salida", number_format($total_valores_salida, 2, ".", ","));
				$tpl->assign("cia_total.valores", number_format($total_valores, 2, ".", ","));
			}
			
			$num_cia = $saldo_ant[$j]['num_cia'];
			
			$total_valores_anteriores = 0;
			$total_valores_entrada = 0;
			$total_valores_salida = 0;
			$total_valores = 0;
			
			$tpl->newBlock("cia_total");
			$tpl->assign("num_cia", $num_cia);
			$nombre_cia = $db->query("SELECT nombre_corto FROM catalogo_companias WHERE num_cia = $num_cia");
			$tpl->assign("nombre_cia", $nombre_cia[0]['nombre_corto']);
			$tpl->assign("mes", mes_escrito($_GET['mes'], TRUE));
			$tpl->assign("anio", $_GET['anio']);
		}
		
		$codmp = $saldo_ant[$j]['codmp'];
		$nombremp = $saldo_ant[$j]['nombre'];
		
		// Datos del producto
		$tpl->newBlock("mp_total");
		$tpl->assign("codmp", "<font color=\"" . ($saldo_ant[$j]['controlada'] == "TRUE" ? "0000CC" : ($saldo_ant[$j]['tipo'] == 1 ? "993300" : "993399")) . "\">" . $codmp);
		$tpl->assign("nombremp", $nombremp . "</font>");
		
		$tpl->assign("unidades_anteriores", number_format($saldo_ant[$j]['existencia'], 2, ".", ","));
		
		$valores_ant = $saldo_ant[$j]['existencia'] * $saldo_ant[$j]['precio_unidad'];
		$gran_total_unidades_ant += $saldo_ant[$j]['existencia'];
		$gran_total_valores_ant += $saldo_ant[$j]['existencia'] * $saldo_ant[$j]['precio_unidad'];
		
		$tpl->assign("valores_anteriores", number_format($valores_ant, 2, ".", ","));
		$tpl->assign("costo_anterior", number_format($saldo_ant[$j]['precio_unidad'], 4, ".", ","));
		
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
		
		$total_valores_anteriores += $valores;
		
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
		
		
		$total_valores_entrada += $valores_entrada;
		$total_valores_salida += $valores_salida;
		$total_valores += $unidades * $costo_promedio;
		
		$gran_total_unidades_entrada += $unidades_entrada;
		$gran_total_valores_entrada += $valores_entrada;
		$gran_total_unidades_salida += $unidades_salida;
		$gran_total_valores_salida += $valores_salida;
		$gran_total_unidades += $unidades;
		$gran_total_valores += $unidades * $costo_promedio;
		
		// Mostrar totales
		$tpl->assign("unidades_entrada", number_format($unidades_entrada, 2, ".", ","));
		$tpl->assign("valores_entrada", number_format($valores_entrada, 2, ".", ","));
		$tpl->assign("unidades_salida", number_format($unidades_salida, 2, ".", ","));
		$tpl->assign("valores_salida", number_format($valores_salida, 2, ".", ","));
		$tpl->assign("unidades", number_format($unidades, 2, ".", ","));
		$tpl->assign("valores", number_format($unidades * $costo_promedio, 2, ".", ","));
		$tpl->assign("costo_promedio", number_format($costo_promedio, 4, ".", ","));
		/***********************************************************************************************************/
		/* CODIGO PARA CORREGIR UNICAMENTE AVIO, HISTORICOS Y DEMAS                                                */
		/* DESCOMENTAR LOS SCRIPTS PARA CORREGIR                                                                   */
		/***********************************************************************************************************/
		//$sql .= "UPDATE inventario_real SET existencia = " . ($unidades != 0 ? $unidades : "0") . ", precio_unidad = " . ($costo_promedio != 0 ? $costo_promedio : "0") . " WHERE num_cia = $num_cia AND codmp = $codmp;\n";
		//$sql .= "UPDATE inventario_virtual SET existencia = " . ($unidades != 0 ? $unidades : "0") . ", precio_unidad = " . ($costo_promedio != 0 ? $costo_promedio : "0") . " WHERE num_cia = $num_cia AND codmp = $codmp;\n";
		//$sql .= "UPDATE historico_inventario SET existencia = " . ($unidades != 0 ? $unidades : "0") . ", precio_unidad = " . ($costo_promedio != 0 ? $costo_promedio : "0") . " WHERE num_cia = $num_cia AND codmp = $codmp AND fecha = '$fecha2';\n";
		/***********************************************************************************************************/
	}
	if ($num_cia != NULL) {
		$tpl->assign("cia_total.valores_anteriores", number_format($total_valores_anteriores, 2, ".", ","));
		$tpl->assign("cia_total.valores_entrada", number_format($total_valores_entrada, 2, ".", ","));
		$tpl->assign("cia_total.valores_salida", number_format($total_valores_salida, 2, ".", ","));
		$tpl->assign("cia_total.valores", number_format($total_valores, 2, ".", ","));
	}
	
	if ($_GET['codmp'] > 0) {
		$tpl->newBlock("totales");
		$tpl->assign("unidades_anteriores", number_format($gran_total_unidades_ant, 2, ".", ","));
		$tpl->assign("valores_anteriores", number_format($gran_total_valores_ant, 2, ".", ","));
		$tpl->assign("entradas_unidades", number_format($gran_total_unidades_entrada, 2, ".", ","));
		$tpl->assign("entradas_valores", number_format($gran_total_valores_entrada, 2, ".", ","));
		$tpl->assign("salidas_unidades", number_format($gran_total_unidades_salida, 2, ".", ","));
		$tpl->assign("salidas_valores", number_format($gran_total_valores_salida, 2, ".", ","));
		$tpl->assign("unidades", number_format($gran_total_unidades, 2, ".", ","));
		$tpl->assign("valores", number_format($gran_total_valores, 2, ".", ","));
	}
	
	/***********************************************************************************************************/
	/* CODIGO PARA CORREGIR UNICAMENTE AVIO, HISTORICOS Y DEMAS                                                */
	/* DESCOMENTAR LAS FUNCIONES PARA CORREGIR                                                                 */
	/***********************************************************************************************************/
	//$db->comenzar_transaccion();
	//$db->query($sql);
	//$db->terminar_transaccion();
	
	$tpl->printToScreen();
	$db->desconectar();
	die;
}
?>