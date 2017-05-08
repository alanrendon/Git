<?php
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

$session = new sessionclass($dsn);
$db = new DBclass($dsn, "autocommit=yes");

$descripcion_error[1] = "No hay resultados";

// Funciones
function turno($cod_turno) {
	switch ($cod_turno) {
		case 1: $string = "<span style=\"color: #009999\">FD</span>"; break;
		case 2: $string = "<span style=\"color: #0000CC\">FN</span>"; break;
		case 3: $string = "<span style=\"color: #CC0000\">BD</span>"; break;
		case 4: $string = "<span style=\"color: #6600CC\">REP</span>"; break;
		case 8: $string = "<span style=\"color: #660000\">PIC</span>"; break;
		case 9: $string = "<span style=\"color: #666666\">GEL</span>"; break;
		case 10: $string = "<span style=\"color: #FF3300\">DESP</span>"; break;
		default: $string = "&nbsp;"; break;
	}
	
	return $string;
}

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

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower("./plantillas/header.tpl");

// Incluir el cuerpo del documento
$tpl->assignInclude("body", "./plantillas/pan/aux_inv_v4.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
if (isset($_SESSION['menu']))
	$tpl->assign("menucnt", "$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (isset($_GET['num_cia'])) {
	$fecha_his = date("d/m/Y", mktime(0, 0, 0, $_GET['mes'], 0, $_GET['anio']));
	$fecha1 = "01/$_GET[mes]/$_GET[anio]";
	$fecha2 = date("d/m/Y", mktime(0, 0, 0, $_GET['mes'] + 1, 0, $_GET['anio']));
	$table = isset($_GET['table']) ? $_GET['table'] : "real";
	$query = "";
	
	$catalogo_mat_primas = mktime(0, 0, 0, $_GET['mes'] + 1, 0, $_GET['anio']) < mktime(0, 0, 0, 3, 1, 2009) ? 'catalogo_mat_primas_old' : 'catalogo_mat_primas';
	
	$sql = "SELECT num_cia, codmp, nombre, existencia, precio_unidad, controlada, tipo FROM historico_inventario LEFT JOIN $catalogo_mat_primas USING (codmp) WHERE num_cia = $_GET[num_cia] AND fecha = '$fecha_his' AND no_exi = 'FALSE'";
	$sql .= $_GET['codmp'] > 0 ? " AND codmp = $_GET[codmp]" : "";
	$sql .= !isset($_GET['gas']) ? " AND codmp NOT IN (90)" : "";
	$sql .= $_GET['ctrl'] > 0 ? ($_GET['ctrl'] == 1 ? " AND controlada = 'TRUE'" : " AND controlada = 'FALSE'") : "";
	$sql .= $_GET['tipo'] > 0 ? " AND tipo = $_GET[tipo]" : "";
	$sql .= " ORDER BY num_cia, controlada DESC, tipo, codmp";
	$inv = $db->query($sql);
	
	if (!$inv) {
		header("location: ./aux_inv_v4.php?codigo_error=1");
		die;
	}
	
	if ($_GET['codmp'] > 0) {
		$sql = "SELECT codmp, fecha, descripcion, cod_turno, tipo_mov, cantidad, precio_unidad, total_mov, num_proveedor AS num_pro, nombre FROM mov_inv_$table LEFT JOIN catalogo_proveedores USING (num_proveedor)";
		$sql .= " WHERE num_cia = $_GET[num_cia] AND fecha BETWEEN '$fecha1' AND '$fecha2' AND codmp = $_GET[codmp] AND descripcion != 'DIFERENCIA INVENTARIO' ORDER BY codmp, fecha, tipo_mov, cantidad DESC";
		$movs = $db->query($sql);
		
		if (isset($_GET['dif'])) {
			$sql = "SELECT codmp, fecha, descripcion, cod_turno, tipo_mov, cantidad, precio_unidad, total_mov FROM mov_inv_$table WHERE num_cia = $_GET[num_cia] AND fecha = '$fecha2'";
			$sql .= " AND codmp = $_GET[codmp] AND descripcion = 'DIFERENCIA INVENTARIO' ORDER BY codmp, fecha, tipo_mov, cantidad DESC";
			$difInv = $db->query($sql);
		}
		else
			$difInv = FALSE;
		
		$tpl->newBlock("desglosado");
		$tpl->assign("num_cia", $_GET['num_cia']);
		$nombre_cia = $db->query("SELECT nombre FROM catalogo_companias WHERE num_cia = $_GET[num_cia]");
		$tpl->assign("nombre_cia", $nombre_cia[0]['nombre']);
		$tpl->assign("mes", mes_escrito($_GET['mes']));
		$tpl->assign("anio", $_GET['anio']);
		$tpl->assign("codmp", $_GET['codmp']);
		$tpl->assign("nombre_mp", $inv[0]['nombre']);
		
		$tpl->assign("unidades_ini", number_format($inv[0]['existencia'], 2, ".", ","));
		$tpl->assign("valores_ini", number_format($inv[0]['existencia'] * $inv[0]['precio_unidad'], 2, ".", ","));
		$tpl->assign("costo_ini", number_format($inv[0]['precio_unidad'], 4, ".", ","));
		
		$unidades = $inv[0]['existencia'] != 0 ? $inv[0]['existencia'] : 0;
		$valores  = $inv[0]['existencia'] != 0 ? $inv[0]['existencia'] * $inv[0]['precio_unidad'] : 0;
		
		$uentrada  = 0;	// Unidades de Entrada
		$ventrada  = 0;	// Valores de Entrada
		$usalida   = 0;	// Unidades de Salida
		$vsalida   = 0;	// Valores de Salida
		$dif       = 0;	// Diferencia de Costos en Entradas
		$costo_prom = $inv[0]['precio_unidad'];	// Costo Promedio
		$costo_ant = $costo_prom;	// Costo anterior a un movimiento
		$unidades_ant = $unidades;	// Unidades anteriores a un movimiento
		
		// [07-Ago-2009] Arrastre de ultimo costo con existencia positiva
		$arrastre_costo = $costo_prom;
		
		$arrastre = FALSE;	// Flag. Indica si debe arrastrarse el costo promedio
		
		// Calcular el costo de la diferencia apartir de las entradas
		$costo_dif = costo_dif($_GET['codmp'], $costo_prom);
		
		// [06-Feb-2009] Arreglo para almacenar consumos por turno
		$consumo_turno = array(
			1  => 0,
			2  => 0,
			3  => 0,
			4  => 0,
			8  => 0,
			9  => 0,
			10 => 0
		);
		
		if ($movs)
			foreach ($movs as $i => $mov) {
				$tpl->newBlock("mov");
				$tpl->assign("fecha", $mov['fecha']);
				$tpl->assign("concepto", $mov['descripcion']);
				$tpl->assign("pro", $mov['nombre']);
				$tpl->assign("turno", turno($mov['cod_turno']));
				
				// Salidas
				if ($mov['tipo_mov'] == "t") {
					$unidades -= $mov['cantidad'];
					
					// Si la existencia actual negativa, calcular costo promedio
					if ($unidades < 0) {
						// Buscar costo de las siguientes facturas que satisfagan la existencia negativa actual si no hay arrastre del costo
						if ($arrastre == TRUE) {
							$proximo_costo = $costo_prom;
							
							// [11-Ago-2009] Calcular costo promedio a partir de la existencia inicial y la entrada
							@$costo_prom = ($inv[0]['existencia'] * $inv[0]['precio_unidad'] + $mov['cantidad'] * $arrastre_costo) / ($inv[0]['existencia'] + $mov['cantidad']);
							$arrastre_costo = $costo_prom;
						}
						else {
							$proximo_costo = buscar_fac($i, $_GET['codmp'], $unidades);
							
							if ($proximo_costo <= 0)
								$proximo_costo = $arrastre_costo;
						}
						
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
						
						$arrastre_costo = $costo_prom;
					}
					
					$unidades_ant = $unidades;
					
					$tpl->assign("costo", number_format($costo, 4, ".", ","));
					$tpl->assign("usalida", number_format($mov['cantidad'], 2, ".", ","));
					$tpl->assign("vsalida", number_format($val_sal, 2, ".", ","));
					
					$tpl->assign("unidades", number_format($unidades, 2, ".", ","));
					$tpl->assign("valores", number_format($unidades >= 0 ? $unidades * $costo_prom : 0, 2, ".", ","));
					$tpl->assign("costo_prom", number_format($unidades >= 0 ? $costo_prom : $arrastre_costo, 4, ".", ","));
					
					// [06-Feb-2009] Acumular costo de consumo por turno
					$consumo_turno[$mov['cod_turno']] += $val_sal;
					
					// Calcular arrastres de salida
					$usalida += $mov['cantidad']; 
					$vsalida  += $val_sal;
				}
				// Entradas
				else if ($mov['tipo_mov'] == "f") {
					@$precio_unidad = $mov['total_mov'] / $mov['cantidad'];	// Costo unitario de la entrada
					$tpl->assign("costo", number_format($precio_unidad, 4, ".", ","));
					$tpl->assign("uentrada", number_format($mov['cantidad'], 2, ".", ","));
					$tpl->assign("ventrada", number_format($mov['cantidad'] * $precio_unidad, 2, ".", ","));
					
					$unidades += $mov['cantidad'];
					
					$valor_ant = $unidades_ant * $costo_prom;
					$costo_ant = $costo_prom;
					
					// Si la existencia anterior y actual son negativas, no calcular costo promedio y poner bandera de arrastre en TRUE
					if ($unidades_ant < 0 && $unidades < 0) {
						$arrastre = TRUE;
						
						// [11-Ago-2009] Calcular costo promedio a partir de la existencia inicial y la entrada
						@$costo_prom = ($inv[0]['existencia'] * $inv[0]['precio_unidad'] + $mov['total_mov']) / ($inv[0]['existencia'] + $mov['cantidad']);
						$arrastre_costo = $costo_prom;
					}
					// Si la existencia anterior es negativa y la actual es positiva, no calcular costo promedio y poner bandera de arrastre en FALSE
					else if ($unidades_ant < 0 && $unidades >= 0)
						$arrastre = FALSE;
					// Calcular costo promedio normalmente
					else {
						@$costo_prom = ($unidades_ant * $costo_ant + $mov['cantidad'] * $precio_unidad) / ($unidades_ant + $mov['cantidad']);
						$arrastre_costo = $costo_prom;
					}
					
					// Actualizar existencia anterior a la existencia actual
					$unidades_ant = $unidades;
					
					$tpl->assign("unidades", number_format($unidades, 2, ".", ","));
					$tpl->assign("valores", $unidades >= 0 ? number_format($unidades * $costo_prom, 2, ".", ",") : '0.00');
					$tpl->assign("costo_prom", number_format($costo_prom, 4, ".", ","));
					// Diferencia de costo inicial y costo final
					$diferencia_costo = $unidades != 0 ? $precio_unidad - /*$costo_prom*/$arrastre_costo : 0;
					$dif += $diferencia_costo;
					$tpl->assign("dif", $diferencia_costo != 0 ? number_format($diferencia_costo, 4, ".", ",") : "&nbsp;");
					
					// Calcular arrastres de entrada
					$uentrada += $mov['cantidad'];
					$ventrada  += $mov['cantidad'] * $precio_unidad;
					
					// [23-Ago-2007] Detalle de proveedor y pago de factura
					if ($mov['num_pro'] > 0) {
						// Obtener número de factura a partir del concepto
						$num_fact = intval(substr($mov['descripcion'], 14));
						
						if ($num_fact > 0) {
							// Buscar detalle de la factura
							$sql = "SELECT fecha_cheque, folio_cheque, fecha_con, fp.cuenta FROM facturas_pagadas AS fp LEFT JOIN estado_cuenta AS ec ON";
							$sql .= " (ec.num_cia = fp.num_cia AND ec.folio = fp.folio_cheque AND ec.cuenta = fp.cuenta) WHERE";
							$sql .= " num_proveedor = $mov[num_pro] AND num_fact = '$num_fact'";
							$det = $db->query($sql);
							
							if ($det)
								$tpl->assign('detalle', "onclick=\"detalle('{$det[0]['fecha_cheque']}',{$det[0]['folio_cheque']},'{$det[0]['fecha_con']}',{$det[0]['cuenta']})\"");
						}
					}
				}
			}
		
		// Diferencia
		if ($difInv) {
			$tpl->newBlock("mov");
			$tpl->assign("fecha", $difInv[0]['fecha']);
			$tpl->assign("concepto", $difInv[0]['descripcion']);
			$tpl->assign("turno", turno($difInv[0]['cod_turno']));
			
			// Diferencia en contra
			if ($difInv[0]['tipo_mov'] == "t") {
				$unidades -= $difInv[0]['cantidad'];
				
				$val_sal = $difInv[0]['cantidad'] * /*$costo_prom*/$arrastre_costo;
				
				$costo = $costo_ant;
				$costo_ant = $costo_prom;
				
				$unidades_ant = $unidades;
				
				$tpl->assign("costo", number_format($costo, 4, ".", ","));
				$tpl->assign("usalida", number_format($difInv[0]['cantidad'], 2, ".", ","));
				$tpl->assign("vsalida", number_format($val_sal, 2, ".", ","));
				
				$tpl->assign("unidades", number_format($unidades, 2, ".", ","));
				$tpl->assign("valores", number_format($unidades * /*$costo_prom*/$arrastre_costo, 2, ".", ","));
				$tpl->assign("costo_prom", number_format(/*$costo_prom*/$arrastre_costo, 4, ".", ","));
				
				$usalida += $difInv[0]['cantidad'];
				$vsalida  += $difInv[0]['cantidad'] * /*$costo_prom*/$arrastre_costo;
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
				
				$tpl->assign("costo", number_format($precio_unidad, 4, ".", ","));
				$tpl->assign("uentrada", number_format($difInv[0]['cantidad'], 2, ".", ","));
				$tpl->assign("ventrada", number_format($difInv[0]['cantidad'] * $precio_unidad, 2, ".", ","));
				
				$tpl->assign("unidades", number_format($unidades, 2, ".", ","));
				$tpl->assign("valores", number_format($unidades * $costo_prom, 2, ".", ","));
				$tpl->assign("costo_prom", number_format($costo_prom, 4, ".", ","));
				// Diferencia de costo inicial y costo final
				$diferencia_costo = $unidades != 0 ? $precio_unidad - $costo_prom : 0;
				$dif += $diferencia_costo;
				$tpl->assign("dif", round($diferencia_costo, 4) != 0 ? number_format($diferencia_costo, 4, ".", ",") : "&nbsp;");
				
				$uentrada += $difInv[0]['cantidad'];
				$ventrada  += $difInv[0]['cantidad'] * $precio_unidad;
			}
		}
		// Totales
		$tpl->assign("desglosado.uentrada", number_format($uentrada, 2, ".", ","));
		$tpl->assign("desglosado.ventrada", number_format($ventrada, 2, ".", ","));
		$tpl->assign("desglosado.usalida", number_format($usalida, 2, ".", ","));
		$tpl->assign("desglosado.vsalida", number_format($vsalida, 2, ".", ","));
		$tpl->assign("desglosado.unidades", number_format($unidades, 2, ".", ","));
		$tpl->assign("desglosado.valores", number_format($unidades >= 0 ? $unidades * ($costo_prom > 0 ? $costo_prom : $arrastre_costo) : 0, 2, ".", ","));
		$tpl->assign("desglosado.costo", number_format($costo_prom > 0 ? $costo_prom : $arrastre_costo, 4, ".", ","));
		$tpl->assign("desglosado.dif", number_format($dif, 4, ".", ","));
		
		// [06-Feb-2009] Mostrar tabla de costos de consumo por turno
		foreach ($consumo_turno as $k => $v)
			if ($v != 0) {
				$tpl->newBlock('consumo_turno');
				switch ($k) {
					case 1:
						$leyenda = 'FRANCES DE DIA';
					break;
					case 2:
						$leyenda = 'FRANCES DE NOCHE';
					break;
					case 3:
						$leyenda = 'BIZCOCHERO';
					break;
					case 4:
						$leyenda = 'REPOSTERO';
					break;
					case 8:
						$leyenda = 'PICONERO';
					break;
					case 9:
						$leyenda = 'GELATINERO';
					break;
					case 10:
						$leyenda = 'DESPACHO';
					break;
					default:
						$leyenda = '-';
				}
				$tpl->assign('turno', $leyenda);
				$tpl->assign('consumo', number_format($v, 2, '.', ','));
			}
		
		$tpl->newBlock(isset($_GET['close']) ? "button_close" : "button_back");
		
		// Opciones extras
		if (isset($_GET['act_real']))
			$query .= "UPDATE inventario_real SET existencia = $unidades, precio_unidad = " . ($costo_prom != 0 ? $costo_prom : $arrastre_costo) . " WHERE num_cia = $_GET[num_cia] AND codmp = $_GET[codmp];\n";
		if (isset($_GET['act_virtual']))
			$query .= "UPDATE inventario_virtual SET existencia = $unidades, precio_unidad = " . ($costo_prom != 0 ? $costo_prom : $arrastre_costo) . " WHERE num_cia = $_GET[num_cia] AND codmp = $_GET[codmp];\n";
		if (isset($_GET['act_his']))
			$query .= "UPDATE historico_inventario SET existencia = $unidades, precio_unidad = " . ($costo_prom != 0 ? $costo_prom : $arrastre_costo) . " WHERE num_cia = $_GET[num_cia] AND codmp = $_GET[codmp] AND fecha = '$fecha2';\n";
		
		// Proceso extra para diferencias
//		if (isset($_GET['id'])) {
//			$query .= "UPDATE inventario_fin_mes SET existencia = $unidades, diferencia = $unidades - inventario, precio_unidad = " . ($costo_prom != 0 ? $costo_prom : $arrastre_costo) . " WHERE id = $_GET[id];\n";
//			$tpl->newBlock("act_dif");
//			$tpl->assign("i", $_GET['i']);
//			$tpl->assign("existencia", $unidades);
//			$tpl->assign("costo", $costo_prom);
//		}
	}
	else {
		$tpl->newBlock("totales");
		$tpl->assign("num_cia", $_GET['num_cia']);
		$nombre_cia = $db->query("SELECT nombre FROM catalogo_companias WHERE num_cia = $_GET[num_cia]");
		$tpl->assign("nombre_cia", $nombre_cia[0]['nombre']);
		$tpl->assign("mes", mes_escrito($_GET['mes']));
		$tpl->assign("anio", $_GET['anio']);
		
		$tvalores_ini = 0;
		$tventrada = 0;
		$tvsalida = 0;
		$tvalores = 0;
		
		foreach ($inv as $reg) {
			$sql = "SELECT codmp, fecha, descripcion, cod_turno, tipo_mov, cantidad, precio_unidad, total_mov FROM mov_inv_$table WHERE num_cia = $_GET[num_cia] AND";
			$sql .= " fecha BETWEEN '$fecha1' AND '$fecha2' AND codmp = $reg[codmp] AND descripcion != 'DIFERENCIA INVENTARIO' ORDER BY codmp, fecha, tipo_mov, cantidad DESC";
			$movs = $db->query($sql);
			
			if (isset($_GET['dif'])) {
				$sql = "SELECT codmp, fecha, descripcion, cod_turno, tipo_mov, cantidad, precio_unidad, total_mov FROM mov_inv_$table WHERE num_cia = $_GET[num_cia] AND fecha = '$fecha2'";
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
			
			// [10-Ago-2009] Arrastre de ultimo costo con existencia positiva
			$arrastre_costo = $costo_prom;
			
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
							if ($arrastre == TRUE) {
								$proximo_costo = $costo_prom;
								
								// [11-Ago-2009] Calcular costo promedio a partir de la existencia inicial y la entrada
								@$costo_prom = ($inv[0]['existencia'] * $inv[0]['precio_unidad'] + $mov['total_mov']) / ($inv[0]['existencia'] + $mov['cantidad']);
								$arrastre_costo = $costo_prom;
							}
							else {
								$proximo_costo = buscar_fac($i, $reg['codmp'], $unidades);
								
								if ($proximo_costo <= 0)
									$proximo_costo = $arrastre_costo;
							}
							
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
							
							$arrastre_costo = $costo_prom;
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
						else {
							@$costo_prom = ($unidades_ant * $costo_ant + $mov['cantidad'] * $precio_unidad) / ($unidades_ant + $mov['cantidad']);
							
							$arrastre_costo = $costo_prom;
						}
						
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
					
					$val_sal = $difInv[0]['cantidad'] * /*$costo_prom*/$arrastre_costo;
					
					$costo = $costo_ant;
					$costo_ant = $costo_prom;
					
					$unidades_ant = $unidades;
					
					$usalida += $difInv[0]['cantidad'];
					$vsalida  += $difInv[0]['cantidad'] * /*$costo_prom*/$arrastre_costo;
				}
				// Diferencia a favor
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
			
			if (round($reg['existencia'], 2) == 0 && round($uentrada, 2) == 0 && round($usalida, 2) == 0 && round($unidades, 2) == 0)
				continue;
			
			$tpl->newBlock("pro");
			$tpl->assign("codmp", $reg['codmp']);
			$tpl->assign("nombre", $reg['nombre']);
			$tpl->assign("color", $reg['controlada'] == "TRUE" ? "0000CC" : ($reg['tipo'] == 1 ? "993300" : "993399"));
			
			$tpl->assign("unidades_ini", number_format($reg['existencia'], 2, ".", ","));
			$tpl->assign("valores_ini", number_format($reg['existencia'] * $reg['precio_unidad'], 2, ".", ","));
			$tpl->assign("costo_ini", number_format($reg['precio_unidad'], 4, ".", ","));
			
			$tpl->assign("num_cia", $_GET['num_cia']);
			$tpl->assign("mes", $_GET['mes']);
			$tpl->assign("anio", $_GET['anio']);
			$tpl->assign("codmp", $reg['codmp']);
			$tpl->assign("uentrada", number_format($uentrada, 2, ".", ","));
			$tpl->assign("ventrada", number_format($ventrada, 2, ".", ","));
			$tpl->assign("usalida", number_format($usalida, 2, ".", ","));
			$tpl->assign("vsalida", number_format($vsalida, 2, ".", ","));
			$tpl->assign("unidades", number_format($unidades, 2, ".", ","));
			$tpl->assign("valores", number_format($unidades * $costo_prom, 2, ".", ","));
			$tpl->assign("costo", number_format($costo_prom, 4, ".", ","));
			
			$tventrada += $ventrada;
			$tvsalida += $vsalida;
			$tvalores += $unidades * $costo_prom;
			
			// Opciones extras
			if (isset($_GET['act_real']))
				$query .= "UPDATE inventario_real SET existencia = $unidades, precio_unidad = " . ($costo_prom != 0 ? $costo_prom : "0.00") . " WHERE num_cia = $_GET[num_cia] AND codmp = $reg[codmp];\n";
			if (isset($_GET['act_virtual']))
				$query .= "UPDATE inventario_virtual SET existencia = $unidades, precio_unidad = " . ($costo_prom != 0 ? $costo_prom : "0.00") . " WHERE num_cia = $_GET[num_cia] AND codmp = $reg[codmp];\n";
			if (isset($_GET['act_his']))
				$query .= "UPDATE historico_inventario SET existencia = $unidades, precio_unidad = " . ($costo_prom != 0 ? $costo_prom : "0.00") . " WHERE num_cia = $_GET[num_cia] AND codmp = $reg[codmp] AND fecha = '$fecha2';\n";
		}
		$tpl->assign("totales.valores_ini", number_format($tvalores_ini, 2, ".", ","));
		$tpl->assign("totales.ventrada", number_format($tventrada, 2, ".", ","));
		$tpl->assign("totales.vsalida", number_format($tvsalida, 2, ".", ","));
		$tpl->assign("totales.valores", number_format($tvalores, 2, ".", ","));
		
	}
	$tpl->printToScreen();
	
	// Actualizar inventarios segun selección
//	if (isset($_GET['act_real']) || isset($_GET['act_virtual']) || isset($_GET['act_his']))
//		$db->query($query);
	
	die;
}

$tpl->newBlock("datos");
$tpl->assign(date("n", mktime(0, 0, 0, date("n"), date("d") > 5 ? 1 : 0, date("Y"))), " selected");
$tpl->assign("anio", date("Y", mktime(0, 0, 0, date("n"), date("d") > 5 ? 1 : 0, date("Y"))));

$cias = $db->query("SELECT num_cia, nombre_corto AS nombre FROM catalogo_companias ORDER BY num_cia");
foreach ($cias as $cia) {
	$tpl->newBlock("cia");
	$tpl->assign("num_cia", $cia['num_cia']);
	$tpl->assign("nombre", $cia['nombre']);
}

$mps = $db->query("SELECT codmp, nombre FROM catalogo_mat_primas ORDER BY codmp");
foreach ($mps as $mp) {
	$tpl->newBlock("mp");
	$tpl->assign("codmp", $mp['codmp']);
	$tpl->assign("nombre", str_replace("\"", "'", $mp['nombre']));
}

// Opciones extras
if (in_array($_SESSION['iduser'], array(1, 4, 5, 18))) {
	$tpl->newBlock("extras");
	
	if ($_SESSION['iduser'] == 1)
		$tpl->newBlock('update');
}

if (isset($_GET['codigo_error'])) {
	$tpl->newBlock("error");
	$tpl->newBlock("message");
	$tpl->assign("message", $descripcion_error[$_GET['codigo_error']]);	
}

$tpl->printToScreen();
?>