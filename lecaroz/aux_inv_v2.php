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

// --------------------------------- Descripcion de errores --------------------------------------------------
$descripcion_error[1] = "No hay resultados";

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower("./plantillas/header.tpl");

// Incluir el cuerpo del documento
$tpl->assignInclude("body", "./plantillas/pan/aux_inv_v2.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
if (isset($_SESSION['menu']))
	$tpl->assign("menucnt", "$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (!isset($_GET['listado'])) {
	$tpl->newBlock("datos");
	
	$tpl->assign(date("n", mktime(0, 0, 0, date("m"), 1, date("Y"))), "selected");
	$tpl->assign("anio", date("Y"));

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
	$sql = "SELECT num_cia, codmp, nombre, existencia, precio_unidad FROM historico_inventario JOIN catalogo_mat_primas USING (codmp) WHERE";
	// Si es para una compañía en específico
	$sql .= $_GET['num_cia'] > 0 ? " num_cia = $_GET[num_cia] AND" : "";
	// Si es para un producto en específico
	$sql .= $_GET['codmp'] > 0 ? " codmp = $_GET[codmp] AND" : "";
	// Si es controlada o no controlada, si es materia prima o material de empaque
	$sql .= $_GET['controlada'] != "todas" && $_GET['codmp'] < 1 ? ($_GET['controlada'] == "si" ? " controlada = 'TRUE' AND" : " controlada = 'FALSE' AND" . ($_GET['tipo'] != "todas" ? ($_GET['tipo'] == "mp" ? " tipo = 1 AND" : " tipo = 2 AND") : "")) : "";
	$sql .= " fecha = '$fecha_historico' ORDER BY num_cia, controlada DESC, tipo, codmp";
	$saldo_ant = $db->query($sql);
	
	// Saldo actual
	$sql = "SELECT num_cia, fecha, codmp, nombre, descripcion, cod_turno, tipo_mov, cantidad, precio_unidad, total_mov FROM mov_inv_real JOIN catalogo_mat_primas USING (codmp) WHERE";
	// Si es para una compañía en específico
	$sql .= $_GET['num_cia'] > 0 ? " num_cia = $_GET[num_cia] AND" : "";
	// Si es para un producto en específico
	$sql .= $_GET['codmp'] > 0 ? " codmp = $_GET[codmp] AND" : "";
	// Si es controlada o no controlada, si es materia prima o material de empaque
	$sql .= $_GET['controlada'] != "todas" && $_GET['codmp'] < 1 ? ($_GET['controlada'] == "si" ? " controlada = 'TRUE' AND" : " controlada = 'FALSE' AND" . ($_GET['tipo'] != "todas" ? ($_GET['tipo'] == "mp" ? " tipo = 1 AND" : " tipo = 2 AND") : "")) : "";
	$sql .= " fecha BETWEEN '$fecha1' AND '$fecha2' ORDER BY num_cia, controlada DESC, tipo, codmp, fecha, tipo_mov";
	$saldo = $db->query($sql);
	
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
		$tpl->assign("costo_anterior", number_format($saldo_ant[$j]['precio_unidad'], 3, ".", ","));
		
		$unidades = $saldo_ant[$j]['existencia'];
		$valores  = $saldo_ant[$j]['existencia'] * $saldo_ant[$j]['precio_unidad'];
		
		$unidades_entrada = 0;
		$valores_entrada  = 0;
		$unidades_salida  = 0;
		$valores_salida   = 0;
		$diferencia       = 0;
		$costo_promedio   = round($saldo_ant[$j]['precio_unidad'], 3);
		$cantidad_anterior = 0;
		
		for ($k = 0; $k < $nummov; $k++) {
			if ($saldo[$k]['num_cia'] == $num_cia && $saldo[$k]['codmp'] == $codmp) {
				$tpl->newBlock("fila_des");
				$tpl->assign("fecha", $saldo[$k]['fecha']);
				$tpl->assign("concepto", $saldo[$k]['descripcion']);
				$tpl->assign("turno", turno($saldo[$k]['cod_turno']));
				
				// Salidas
				if ($saldo[$k]['tipo_mov'] == "t") {
					$tpl->assign("costo_unitario", number_format($costo_promedio, 3, ".", ","));
					$tpl->assign("unidades_salida", number_format($saldo[$k]['cantidad'], 2, ".", ","));
					$tpl->assign("valores_salida", number_format($saldo[$k]['cantidad'] * $costo_promedio, 2, ".", ","));
					$tpl->assign("unidades_entrada", "&nbsp;");
					$tpl->assign("valores_entrada", "&nbsp;");
					
					$unidades -= round($saldo[$k]['cantidad'], 2);
					if ($unidades < 0)
						$valores = 0;
					else
						$valores -= round($saldo[$k]['cantidad'] * $costo_promedio,2);
					
					$tpl->assign("unidades", number_format($unidades, 2, ".", ","));
					$tpl->assign("valores", number_format($valores, 2, ".", ","));
					$tpl->assign("costo_promedio", number_format($costo_promedio, 3, ".", ","));
					$tpl->assign("diferencia_costo", "&nbsp;");
					$cantidad_anterior = $unidades;
					
					$unidades_salida += $saldo[$k]['cantidad'];
					$valores_salida  += $saldo[$k]['cantidad'] * $costo_promedio;
				}
				// Entradas
				else if ($saldo[$k]['tipo_mov'] == "f") {
					@$precio_unidad = round($saldo[$k]['total_mov'] / $saldo[$k]['cantidad'], 3);
					$tpl->assign("costo_unitario", number_format($precio_unidad, 3, ".", ","));
					$tpl->assign("unidades_entrada", number_format($saldo[$k]['cantidad'], 2, ".", ","));
					$tpl->assign("valores_entrada", number_format($saldo[$k]['cantidad'] * $precio_unidad, 2, ".", ","));
					$tpl->assign("unidades_salida", "&nbsp;");
					$tpl->assign("valores_salida", "&nbsp;");
					$unidades += $saldo[$k]['cantidad'];
					
					// Calcular valores
					if (round($cantidad_anterior + $saldo[$k]['cantidad'], 2) > 0)
						$valores += round($saldo[$k]['cantidad'] * $precio_unidad, 2);
					else
						$valores = 0;
					
					// Calcular costo promedio
					if (round($cantidad_anterior, 2) <= 0)
						$costo_promedio = round($precio_unidad, 3);
					else
						$costo_promedio = round($valores / $unidades, 3);
					
					$cantidad_anterior = $unidades;
					$tpl->assign("unidades", number_format($unidades, 2, ".", ","));
					$tpl->assign("valores", number_format($valores, 2, ".", ","));
					$tpl->assign("costo_promedio", number_format($costo_promedio, 3, ".", ","));
					// Diferencia de costo inicial y costo final
					$diferencia_costo = round($precio_unidad - $costo_promedio, 3);
					$diferencia += $diferencia_costo;
					$tpl->assign("diferencia_costo", $diferencia_costo != 0 ? number_format($diferencia_costo, 3, ".", ",") : "&nbsp;");
					
					$unidades_entrada += $saldo[$k]['cantidad'];
					$valores_entrada  += $saldo[$k]['cantidad'] * $precio_unidad;
				}
			}
		}
		
		// Mostrar totales
		$tpl->assign("mp_des.unidades_entrada", number_format($unidades_entrada, 2, ".", ","));
		$tpl->assign("mp_des.valores_entrada", number_format($valores_entrada, 2, ".", ","));
		$tpl->assign("mp_des.unidades_salida", number_format($unidades_salida, 2, ".", ","));
		$tpl->assign("mp_des.valores_salida", number_format($valores_salida, 2, ".", ","));
		$tpl->assign("mp_des.unidades", number_format($unidades, 2, ".", ","));
		$tpl->assign("mp_des.valores", number_format($valores, 2, ".", ","));
		$tpl->assign("mp_des.costo_promedio", number_format($costo_promedio, 3, ".", ","));
		$tpl->assign("mp_des.total_diferencia", number_format($diferencia, 3, ".", ","));
		
		/***********************************************************************************************************/
		/* CODIGO PARA CORREGIR UNICAMENTE AVIO, HISTORICOS Y DEMAS                                                */
		/* DESCOMENTAR LOS SCRIPTS PARA CORREGIR                                                                   */
		/***********************************************************************************************************/
		//$db->comenzar_transaccion();
		//$sql = "UPDATE inventario_real SET existencia = $unidades, precio_unidad = $costo_promedio WHERE num_cia = $num_cia AND codmp = $codmp";
		//$db->query($sql);
		//$sql = "UPDATE inventario_virtual SET existencia = $unidades, precio_unidad = $costo_promedio WHERE num_cia = $num_cia AND codmp = $codmp";
		//$db->query($sql);
		//$db->terminar_transaccion();
		/***********************************************************************************************************/
	}
	$tpl->printToScreen();
	$db->desconectar();
	die;
	
}
else if ($_GET['listado'] == "totales") {
	$tpl->newBlock("listado_totales");
	// Saldos anteriores
	$sql = "SELECT num_cia, codmp, nombre, existencia, precio_unidad, controlada, tipo FROM historico_inventario JOIN catalogo_mat_primas USING (codmp) WHERE";
	// Si es para una compañía en específico
	$sql .= $_GET['num_cia'] > 0 ? " num_cia = $_GET[num_cia] AND" : "";
	// Si es para un producto en específico
	$sql .= $_GET['codmp'] > 0 ? " codmp = $_GET[codmp] AND" : "";
	// Si es controlada o no controlada, si es materia prima o material de empaque
	$sql .= $_GET['controlada'] != "todas" && $_GET['codmp'] < 1 ? ($_GET['controlada'] == "si" ? " controlada = 'TRUE' AND" : " controlada = 'FALSE' AND" . ($_GET['tipo'] != "todas" ? ($_GET['tipo'] == "mp" ? " tipo = 1 AND" : " tipo = 2 AND") : "")) : "";
	$sql .= " fecha = '$fecha_historico' ORDER BY num_cia, controlada DESC, tipo, codmp";
	$saldo_ant = $db->query($sql);
	
	// Saldo actual
	$sql = "SELECT num_cia, fecha, codmp, nombre, descripcion, cod_turno, tipo_mov, cantidad, precio_unidad, total_mov FROM mov_inv_real JOIN catalogo_mat_primas USING (codmp) WHERE";
	// Si es para una compañía en específico
	$sql .= $_GET['num_cia'] > 0 ? " num_cia = $_GET[num_cia] AND" : "";
	// Si es para un producto en específico
	$sql .= $_GET['codmp'] > 0 ? " codmp = $_GET[codmp] AND" : "";
	// Si es controlada o no controlada, si es materia prima o material de empaque
	$sql .= $_GET['controlada'] != "todas" && $_GET['codmp'] < 1 ? ($_GET['controlada'] == "si" ? " controlada = 'TRUE' AND" : " controlada = 'FALSE' AND" . ($_GET['tipo'] != "todas" ? ($_GET['tipo'] == "mp" ? " tipo = 1 AND" : " tipo = 2 AND") : "")) : "";
	$sql .= " fecha BETWEEN '$fecha1' AND '$fecha2' ORDER BY num_cia, controlada DESC, tipo, codmp, fecha, tipo_mov";
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
			$num_cia = $saldo_ant[$j]['num_cia'];
			
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
		$tpl->assign("valores_anteriores", number_format($valores_ant, 2, ".", ","));
		$tpl->assign("costo_anterior", number_format($saldo_ant[$j]['precio_unidad'], 3, ".", ","));
		
		$unidades = $saldo_ant[$j]['existencia'];
		$valores  = $saldo_ant[$j]['existencia'] * $saldo_ant[$j]['precio_unidad'];
		
		$unidades_entrada = 0;
		$valores_entrada  = 0;
		$unidades_salida  = 0;
		$valores_salida   = 0;
		$diferencia       = 0;
		$costo_promedio   = round($saldo_ant[$j]['precio_unidad'], 3);
		$cantidad_anterior = 0;
		
		$total_valores_anteriores += $valores;
		
		for ($k = 0; $k < $nummov; $k++) {
			if ($saldo[$k]['num_cia'] == $saldo_ant[$j]['num_cia'] && $saldo[$k]['codmp'] == $saldo_ant[$j]['codmp']) {
				// Salidas
				if ($saldo[$k]['tipo_mov'] == "t") {
					$unidades -= round($saldo[$k]['cantidad'], 2);
					if ($unidades < 0)
						$valores = 0;
					else
						$valores  -= round($saldo[$k]['cantidad'] * $costo_promedio,2);
					$cantidad_anterior = $unidades;
					
					$unidades_salida += $saldo[$k]['cantidad'];
					$valores_salida  += $saldo[$k]['cantidad'] * $costo_promedio;
				}
				// Entradas
				else if ($saldo[$k]['tipo_mov'] == "f") {
					@$precio_unidad = round($saldo[$k]['total_mov'] / $saldo[$k]['cantidad'], 4);
					$unidades += $saldo[$k]['cantidad'];
					// Calcular valores
					if (round($cantidad_anterior + $saldo[$k]['cantidad'], 2) > 0)
						$valores  += round($saldo[$k]['cantidad'] * $precio_unidad, 2);
					else
						$valores = 0;
					// Calcular costo promedio
					if (round($cantidad_anterior, 2) <= 0)
						$costo_promedio = round($precio_unidad, 4);
					else
						$costo_promedio = round($valores / $unidades, 4);
					$cantidad_anterior = $unidades;
					// Diferencia de costo inicial y costo final
					$diferencia_costo = round($precio_unidad - $costo_promedio, 4);
					$diferencia += $diferencia_costo;
					
					$unidades_entrada += $saldo[$k]['cantidad'];
					$valores_entrada  += $saldo[$k]['cantidad'] * $precio_unidad;
				}
			}
		}
		$total_valores_entrada += $valores_entrada;
		$total_valores_salida += $valores_salida;
		$total_valores += $valores;
		
		// Mostrar totales
		$tpl->assign("unidades_entrada", number_format($unidades_entrada, 2, ".", ","));
		$tpl->assign("valores_entrada", number_format($valores_entrada, 2, ".", ","));
		$tpl->assign("unidades_salida", number_format($unidades_salida, 2, ".", ","));
		$tpl->assign("valores_salida", number_format($valores_salida, 2, ".", ","));
		$tpl->assign("unidades", number_format($unidades, 2, ".", ","));
		$tpl->assign("valores", number_format($valores, 2, ".", ","));
		$tpl->assign("costo_promedio", number_format($costo_promedio, 3, ".", ","));
		/***********************************************************************************************************/
		/* CODIGO PARA CORREGIR UNICAMENTE AVIO, HISTORICOS Y DEMAS                                                */
		/* DESCOMENTAR LOS SCRIPTS PARA CORREGIR                                                                   */
		/***********************************************************************************************************/
		//$db->comenzar_transaccion();
		//$sql = "UPDATE inventario_real SET existencia = $unidades, precio_unidad = $costo_promedio WHERE num_cia = $num_cia AND codmp = $codmp";
		//$sql = "UPDATE inventario_virtual SET existencia = $unidades, precio_unidad = $costo_promedio WHERE num_cia = $num_cia AND codmp = $codmp";
		//$sql = "UPDATE historico_inventario SET precio_unidad = $costo_promedio WHERE num_cia = $num_cia AND codmp = $codmp AND fecha = '$fecha2'";
		//$db->query($sql);
		//$db->terminar_transaccion();
		/***********************************************************************************************************/
	}
	$tpl->assign("cia_total.valores_anteriores", number_format($total_valores_anteriores, 2, ".", ","));
	$tpl->assign("cia_total.valores_entrada", number_format($total_valores_entrada, 2, ".", ","));
	$tpl->assign("cia_total.valores_salida", number_format($total_valores_salida, 2, ".", ","));
	$tpl->assign("cia_total.valores", number_format($total_valores, 2, ".", ","));
	
	$tpl->printToScreen();
	$db->desconectar();
	die;
}
?>