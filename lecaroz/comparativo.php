<?php
// AUXILIAR DE MATERIAS PRIMAS
// Tablas 'historico_inventario', 'mov_inv_real'
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

// --------------------------------- Descripcion de errores --------------------------------------------------
$descripcion_error[1] = "La compañía no existe en la Base de Datos";

// --------------------------------- Generar pantalla --------------------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/pan/comparativo.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
if (isset($_SESSION['menu']))
	$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (!isset($_GET['listado'])) {
	$tpl->newBlock("datos");
	
	$tpl->assign(date("n",mktime(0,0,0,date("m"),1,date("Y"))),"selected");
	$tpl->assign("anio",date("Y"));

	$tpl->printToScreen();
	die;
}

// Eliminar registro de mov_inv_real
if (isset($_GET['eliminar'])) {
//	ejecutar_script("DELETE FROM mov_inv_real WHERE id=$_GET[eliminar]",$dsn);
}

// Construir fecha inicial y fecha final
$fecha_historico = date("d/m/Y",mktime(0,0,0,$_GET['mes'],0,$_GET['anio']));
$fecha1 = date("d/m/Y",mktime(0,0,0,$_GET['mes'],1,$_GET['anio']));
$fecha2 = date("d/m/Y",mktime(0,0,0,$_GET['mes']+1,0,$_GET['anio']));

if ($_GET['listado'] == "cia") {
	if ($_GET['tipo'] == "mp") {
		// Obtener saldos anteriores de hitorico_inventario
		$sql  = "SELECT ";
		$sql .= "num_cia,codmp,nombre,existencia,precio_unidad ";
		$sql .= "FROM ";
		$sql .= "historico_inventario ";
		$sql .= "JOIN ";
		$sql .= "catalogo_mat_primas ";
		$sql .= "USING(codmp) ";
		$sql .= "WHERE ";
		$sql .= "num_cia = $_GET[num_cia] ";
		$sql .= "AND ";
		$sql .= "fecha = '$fecha_historico' ";
		$sql .= "AND ";
		$sql .= "codmp = $_GET[codmp]";
		$saldo_anterior = ejecutar_script($sql,$dsn);
		
		// Obtener saldos de materias primas
		$sql  = "SELECT ";
		$sql .= "id,fecha,codmp,nombre,descripcion,tipo_mov,cantidad,precio_unidad,total_mov ";
		$sql .= "FROM ";
		$sql .= "mov_inv_real ";
		$sql .= "JOIN ";
		$sql .= "catalogo_mat_primas ";
		$sql .= "USING(codmp) ";
		$sql .= "WHERE ";
		$sql .= "num_cia = $_GET[num_cia] ";
		$sql .= "AND ";
		$sql .= "fecha >= '$fecha1' ";
		$sql .= "AND ";
		$sql .= "fecha <= '$fecha2' ";
		$sql .= "AND ";
		$sql .= "codmp =  $_GET[codmp]";
		$sql .= "ORDER BY num_cia,codmp,fecha,tipo_mov";
		$saldo = ejecutar_script($sql,$dsn);
		
		$tpl->newBlock("listado");
		
		$tpl->assign("num_cia",$_GET['num_cia']);
		$cia = obtener_registro("catalogo_companias",array("num_cia"),array($_GET['num_cia']),"","",$dsn);
		$tpl->assign("nombre_cia",$cia[0]['nombre']);
		
		switch ($_GET['mes']) {
			case 1 : $mes = "Enero";      break;
			case 2 : $mes = "Febrero";    break;
			case 3 : $mes = "Marzo";      break;
			case 4 : $mes = "Abril";      break;
			case 5 : $mes = "Mayo";       break;
			case 6 : $mes = "Junio";      break;
			case 7 : $mes = "Julio";      break;
			case 8 : $mes = "Agosto";     break;
			case 9 : $mes = "Septiembre"; break;
			case 10: $mes = "Octubre";    break;
			case 11: $mes = "Noviembre";  break;
			case 12: $mes = "Diciembre";  break;
		}
		$tpl->assign("mes",$mes);
		$tpl->assign("anio",date("Y"));
		
		$tpl->newBlock("mp");
		$tpl->assign("codmp",$saldo_anterior[0]['codmp']);
		$tpl->assign("nombremp",$saldo_anterior[0]['nombre']);
		$tpl->assign("unidades_anteriores",number_format($saldo_anterior[0]['existencia'],2,".",","));
		$valores_anteriores = $saldo_anterior[0]['existencia']*$saldo_anterior[0]['precio_unidad'];
		$tpl->assign("valores_anteriores",number_format($valores_anteriores,2,".",","));
		$tpl->assign("costo_anterior",number_format($saldo_anterior[0]['precio_unidad'],2,".",","));
		
		$unidades = $saldo_anterior[0]['existencia'];
		$valores  = $saldo_anterior[0]['existencia'] * $saldo_anterior[0]['precio_unidad'];
		
		$total_unidades_entrada = 0;
		$total_valores_entrada  = 0;
		$total_unidades_salida  = 0;
		$total_valores_salida   = 0;
		$total_diferencia       = 0;
		$costo_promedio         = $saldo_anterior[0]['precio_unidad'];
		for ($j=0; $j<count($saldo); $j++) {
			if ($saldo[$j]['codmp'] == $saldo_anterior[0]['codmp']) {
				$tpl->newBlock("fila");
				$tpl->assign("fecha",$saldo[$j]['fecha']);
				$tpl->assign("concepto",$saldo[$j]['descripcion']);
				
				// Salidas
				if ($saldo[$j]['tipo_mov'] == "t") {
					$tpl->assign("costo_unitario",number_format($costo_promedio,2,".",","));
					$tpl->assign("unidades_salida",number_format($saldo[$j]['cantidad'],2,".",","));
					$tpl->assign("valores_salida",number_format($saldo[$j]['cantidad']*$costo_promedio,2,".",","));
					$tpl->assign("unidades_entrada","&nbsp;");
					$tpl->assign("valores_entrada","&nbsp;");
					$unidades -= $saldo[$j]['cantidad'];
					$valores  -= $saldo[$j]['cantidad'] * $costo_promedio;
					$tpl->assign("unidades_existencia",number_format($unidades,2,".",","));
					$tpl->assign("valores_existencia",number_format($valores,2,".",","));
					$tpl->assign("costo_promedio",number_format($costo_promedio,2,".",","));
					$tpl->assign("diferencia_costo","&nbsp;");
					
					$total_unidades_salida += $saldo[$j]['cantidad'];
					$total_valores_salida  += $saldo[$j]['cantidad'] * $costo_promedio;
				}
				// Entradas
				else if ($saldo[$j]['tipo_mov'] == "f") {
					@$precio_unidad = $saldo[$j]['total_mov'] / $saldo[$j]['cantidad'];
					$tpl->assign("costo_unitario",number_format(/*$saldo[$j]['precio_unidad']*/$precio_unidad,2,".",","));
					$tpl->assign("unidades_entrada",number_format($saldo[$j]['cantidad'],2,".",","));
					$tpl->assign("valores_entrada",number_format($saldo[$j]['cantidad']*/*$saldo[$j]['precio_unidad']*/$precio_unidad,2,".",","));
					$tpl->assign("unidades_salida","&nbsp;");
					$tpl->assign("valores_salida","&nbsp;");
					$unidades += $saldo[$j]['cantidad'];
					$valores  += $saldo[$j]['cantidad'] * /*$saldo[$j]['precio_unidad']*/$precio_unidad;
					$costo_promedio = $valores / $unidades;
					//$costo_promedio = ($saldo[$j]['cantidad']*$saldo[$j]['precio_unidad']) / $saldo[$j]['cantidad'];
					$tpl->assign("unidades_existencia",number_format($unidades,2,".",","));
					$tpl->assign("valores_existencia",number_format($valores,2,".",","));
					$tpl->assign("costo_promedio",number_format($costo_promedio,2,".",","));
					// Diferencia de costo inicial y costo final
					$diferencia_costo = /*$saldo[$j]['precio_unidad']*/$precio_unidad - $costo_promedio;
					$total_diferencia += $diferencia_costo;
					$tpl->assign("diferencia_costo",number_format($diferencia_costo,2,".",","));
					
					$total_unidades_entrada += $saldo[$j]['cantidad'];
					$total_valores_entrada  += $saldo[$j]['cantidad'] * /*$saldo[$j]['precio_unidad']*/$precio_unidad;
				}
			}
		}
		$tpl->gotoBlock("mp");
		$tpl->assign("total_unidades_entrada",number_format($total_unidades_entrada,2,".",","));
		$tpl->assign("total_valores_entrada",number_format($total_valores_entrada,2,".",","));
		$tpl->assign("total_unidades_salida",number_format($total_unidades_salida,2,".",","));
		$tpl->assign("total_valores_salida",number_format($total_valores_salida,2,".",","));
		$tpl->assign("total_unidades",number_format($unidades,2,".",","));
		$tpl->assign("total_valores",number_format($valores,2,".",","));
		$tpl->assign("ultimo_costo_promedio",number_format($costo_promedio,2,".",","));
		$tpl->assign("total_diferencia",number_format($total_diferencia,2,".",","));
		
		$tpl->printToScreen();
	}
	else if ($_GET['tipo'] == "desglozado") {
		// Obtener saldos anteriores de hitorico_inventario
		$sql  = "SELECT ";
		$sql .= "num_cia,codmp,nombre,existencia,precio_unidad ";
		$sql .= "FROM ";
		$sql .= "historico_inventario ";
		$sql .= "JOIN ";
		$sql .= "catalogo_mat_primas ";
		$sql .= "USING(codmp) ";
		$sql .= "WHERE ";
		$sql .= "num_cia = $_GET[num_cia] ";
		$sql .= "AND ";
		$sql .= "fecha = '$fecha_historico' ";
		$sql .= "ORDER BY codmp";
		$saldo_anterior = ejecutar_script($sql,$dsn);
		
		// Obtener saldos de materias primas
		$sql  = "SELECT ";
		$sql .= "id,fecha,codmp,nombre,descripcion,tipo_mov,cantidad,precio_unidad,total_mov ";
		$sql .= "FROM ";
		$sql .= "mov_inv_real ";
		$sql .= "JOIN ";
		$sql .= "catalogo_mat_primas ";
		$sql .= "USING(codmp) ";
		$sql .= "WHERE ";
		$sql .= "num_cia = $_GET[num_cia] ";
		$sql .= "AND ";
		$sql .= "fecha >= '$fecha1' ";
		$sql .= "AND ";
		$sql .= "fecha <= '$fecha2' ";
		$sql .= "ORDER BY num_cia,codmp,fecha,tipo_mov";
		$saldo = ejecutar_script($sql,$dsn);
		
		$tpl->newBlock("listado");
		
		$tpl->assign("num_cia",$_GET['num_cia']);
		$cia = obtener_registro("catalogo_companias",array("num_cia"),array($_GET['num_cia']),"","",$dsn);
		$tpl->assign("nombre_cia",$cia[0]['nombre']);
		
		switch ($_GET['mes']) {
			case 1 : $mes = "Enero";      break;
			case 2 : $mes = "Febrero";    break;
			case 3 : $mes = "Marzo";      break;
			case 4 : $mes = "Abril";      break;
			case 5 : $mes = "Mayo";       break;
			case 6 : $mes = "Junio";      break;
			case 7 : $mes = "Julio";      break;
			case 8 : $mes = "Agosto";     break;
			case 9 : $mes = "Septiembre"; break;
			case 10: $mes = "Octubre";    break;
			case 11: $mes = "Noviembre";  break;
			case 12: $mes = "Diciembre";  break;
		}
		$tpl->assign("mes",$mes);
		$tpl->assign("anio",date("Y"));
		
		$total_valores_anteriores = 0;
		$gran_total_valores_entrada = 0;
		$gran_total_valores_salida = 0;
		$gran_total_valores = 0;
		
		for ($i=0; $i<count($saldo_anterior); $i++) {
			$tpl->newBlock("mp");
			$tpl->assign("codmp",$saldo_anterior[$i]['codmp']);
			$tpl->assign("nombremp",$saldo_anterior[$i]['nombre']);
			$tpl->assign("unidades_anteriores",number_format($saldo_anterior[$i]['existencia'],2,".",","));
			$valores_anteriores = $saldo_anterior[$i]['existencia']*$saldo_anterior[$i]['precio_unidad'];
			$tpl->assign("valores_anteriores",number_format($valores_anteriores,2,".",","));
			$tpl->assign("costo_anterior",number_format($saldo_anterior[$i]['precio_unidad'],2,".",","));
			
			$unidades = $saldo_anterior[$i]['existencia'];
			$valores  = $saldo_anterior[$i]['existencia'] * $saldo_anterior[$i]['precio_unidad'];
			
			$total_unidades_entrada = 0;
			$total_valores_entrada  = 0;
			$total_unidades_salida  = 0;
			$total_valores_salida   = 0;
			$total_diferencia       = 0;
			$costo_promedio         = $saldo_anterior[$i]['precio_unidad'];
			for ($j=0; $j<count($saldo); $j++) {
				if ($saldo[$j]['codmp'] == $saldo_anterior[$i]['codmp']) {
					$tpl->newBlock("fila");
					$tpl->assign("fecha",$saldo[$j]['fecha']);
					$tpl->assign("concepto",$saldo[$j]['descripcion']);
					
					// Salidas
					if ($saldo[$j]['tipo_mov'] == "t") {
						$tpl->assign("costo_unitario",number_format($costo_promedio,2,".",","));
						$tpl->assign("unidades_salida",number_format($saldo[$j]['cantidad'],2,".",","));
						$tpl->assign("valores_salida",number_format($saldo[$j]['cantidad']*$costo_promedio,2,".",","));
						$tpl->assign("unidades_entrada","&nbsp;");
						$tpl->assign("valores_entrada","&nbsp;");
						$unidades -= $saldo[$j]['cantidad'];
						$valores  -= $saldo[$j]['cantidad'] * $costo_promedio;
						$tpl->assign("unidades_existencia",number_format($unidades,2,".",","));
						$tpl->assign("valores_existencia",number_format($valores,2,".",","));
						$tpl->assign("costo_promedio",number_format($costo_promedio,2,".",","));
						$total_unidades_salida += $saldo[$j]['cantidad'];
						$total_valores_salida  += $saldo[$j]['cantidad'] * $costo_promedio;
						$tpl->assign("diferencia_costo","&nbsp;");
						
						$total_unidades_salida += $saldo[$j]['cantidad'];
						$total_valores_salida  += $saldo[$j]['cantidad'] * $costo_promedio;
					}
					// Entradas
					else if ($saldo[$j]['tipo_mov'] == "f") {
						@$precio_unidad = $saldo[$j]['total_mov'] / $saldo[$j]['cantidad'];
						$tpl->assign("costo_unitario",number_format(/*$saldo[$j]['precio_unidad']*/$precio_unidad,2,".",","));
						$tpl->assign("unidades_entrada",number_format($saldo[$j]['cantidad'],2,".",","));
						$tpl->assign("valores_entrada",number_format(/*$saldo[$j]['cantidad']*$saldo[$j]['precio_unidad']*/$saldo[$j]['total_mov'],2,".",","));
						$tpl->assign("unidades_salida","&nbsp;");
						$tpl->assign("valores_salida","&nbsp;");
						$unidades += $saldo[$j]['cantidad'];
						$valores  += /*$saldo[$j]['cantidad'] * $saldo[$j]['precio_unidad']*/$saldo[$j]['total_mov'];
						@$costo_promedio = $valores / $unidades;
						$tpl->assign("unidades_existencia",number_format($unidades,2,".",","));
						$tpl->assign("valores_existencia",number_format($valores,2,".",","));
						$tpl->assign("costo_promedio",number_format($costo_promedio,2,".",","));
						$total_unidades_entrada += $saldo[$j]['cantidad'];
						$total_valores_entrada  += /*$saldo[$j]['cantidad'] * $saldo[$j]['precio_unidad']*/$saldo[$j]['total_mov'];
						// Diferencia de costo inicial y costo final
						$diferencia_costo = /*$saldo[$j]['precio_unidad']*/$precio_unidad - $costo_promedio;
						$total_diferencia += $diferencia_costo;
						$tpl->assign("diferencia_costo",number_format($diferencia_costo,2,".",","));
						
						$total_unidades_salida += $saldo[$j]['cantidad'];
						$total_valores_salida  += $saldo[$j]['cantidad'] * $costo_promedio;
					}
				}
			}
			$tpl->gotoBlock("mp");
			$tpl->assign("total_unidades_entrada",number_format($total_unidades_entrada,2,".",","));
			$tpl->assign("total_valores_entrada",number_format($total_valores_entrada,2,".",","));
			$tpl->assign("total_unidades_salida",number_format($total_unidades_salida,2,".",","));
			$tpl->assign("total_valores_salida",number_format($total_valores_salida,2,".",","));
			$tpl->assign("total_unidades",number_format($unidades,2,".",","));
			$tpl->assign("total_valores",number_format($valores,2,".",","));
			$tpl->assign("ultimo_costo_promedio",number_format($costo_promedio,2,".",","));
			$tpl->assign("total_diferencia",number_format($total_diferencia,2,".",","));
		}
		$tpl->printToScreen();
	}
	else if ($_GET['tipo'] == "totales") {
		// Obtener saldos anteriores de hitorico_inventario
		$sql  = "SELECT ";
		$sql .= "num_cia,codmp,nombre,existencia,precio_unidad ";
		$sql .= "FROM ";
		$sql .= "historico_inventario ";
		$sql .= "JOIN ";
		$sql .= "catalogo_mat_primas ";
		$sql .= "USING(codmp) ";
		$sql .= "WHERE ";
		$sql .= "num_cia = $_GET[num_cia] ";
		$sql .= "AND ";
		$sql .= "fecha = '$fecha_historico' ";
		$sql .= "ORDER BY codmp";
		$saldo_anterior = ejecutar_script($sql,$dsn);
		
		// Obtener saldos de materias primas
		$sql  = "SELECT ";
		$sql .= "fecha,codmp,nombre,tipo_mov,cantidad,precio_unidad,total_mov ";
		$sql .= "FROM ";
		$sql .= "mov_inv_real ";
		$sql .= "JOIN ";
		$sql .= "catalogo_mat_primas ";
		$sql .= "USING(codmp) ";
		$sql .= "WHERE ";
		$sql .= "num_cia = $_GET[num_cia] ";
		$sql .= "AND ";
		$sql .= "fecha >= '$fecha1' ";
		$sql .= "AND ";
		$sql .= "fecha <= '$fecha2' ";
		$sql .= "ORDER BY num_cia,codmp,fecha,tipo_mov";
		$saldo = ejecutar_script($sql,$dsn);
		
		$tpl->newBlock("listado_totales");
		
		$tpl->assign("num_cia",$_GET['num_cia']);
		$cia = obtener_registro("catalogo_companias",array("num_cia"),array($_GET['num_cia']),"","",$dsn);
		$tpl->assign("nombre_cia",$cia[0]['nombre']);
		
		switch ($_GET['mes']) {
			case 1 : $mes = "Enero";      break;
			case 2 : $mes = "Febrero";    break;
			case 3 : $mes = "Marzo";      break;
			case 4 : $mes = "Abril";      break;
			case 5 : $mes = "Mayo";       break;
			case 6 : $mes = "Junio";      break;
			case 7 : $mes = "Julio";      break;
			case 8 : $mes = "Agosto";     break;
			case 9 : $mes = "Septiembre"; break;
			case 10: $mes = "Octubre";    break;
			case 11: $mes = "Noviembre";  break;
			case 12: $mes = "Diciembre";  break;
		}
		$tpl->assign("mes",$mes);
		$tpl->assign("anio",date("Y"));
		
		$total_valores_anteriores = 0;
		$gran_total_valores_entrada = 0;
		$gran_total_valores_salida = 0;
		$gran_total_valores = 0;
		
		for ($i=0; $i<count($saldo_anterior); $i++) {
			$tpl->newBlock("mp_total");
			$tpl->assign("codmp_total",$saldo_anterior[$i]['codmp']);
			$tpl->assign("nombremp_total",$saldo_anterior[$i]['nombre']);
			$tpl->assign("unidades_anteriores_total",number_format($saldo_anterior[$i]['existencia'],2,".",","));
			$valores_anteriores = $saldo_anterior[$i]['existencia']*$saldo_anterior[$i]['precio_unidad'];
			$tpl->assign("valores_anteriores_total",number_format($valores_anteriores,2,".",","));
			$tpl->assign("costo_anterior_total",number_format($saldo_anterior[$i]['precio_unidad'],2,".",","));
			
			$unidades = $saldo_anterior[$i]['existencia'];
			$valores  = $saldo_anterior[$i]['existencia'] * $saldo_anterior[$i]['precio_unidad'];
			
			$total_unidades_entrada = 0;
			$total_valores_entrada = 0;
			$total_unidades_salida = 0;
			$total_valores_salida = 0;
			$costo_promedio = $saldo_anterior[$i]['precio_unidad'];
			for ($j=0; $j<count($saldo); $j++) {
				if ($saldo[$j]['codmp'] == $saldo_anterior[$i]['codmp']) {
					// Salidas
					if ($saldo[$j]['tipo_mov'] == "t") {
						$unidades -= $saldo[$j]['cantidad'];
						$valores  -= $saldo[$j]['cantidad'] * $costo_promedio;
						$total_unidades_salida += $saldo[$j]['cantidad'];
						$total_valores_salida  += $saldo[$j]['cantidad'] * $costo_promedio;
					}
					// Entradas
					else if ($saldo[$j]['tipo_mov'] == "f") {
						$precio_unidad = $saldo[$j]['total_mov'] / $saldo[$j]['cantidad'];
						$unidades += $saldo[$j]['cantidad'];
						$valores  += $saldo[$j]['cantidad'] * /*$saldo[$j]['precio_unidad']*/$precio_unidad;
						if ($unidades > 0)
							$costo_promedio = $valores / $unidades;
						else
							$costo_promedio = 0;
						$total_unidades_entrada += $saldo[$j]['cantidad'];
						$total_valores_entrada  += $saldo[$j]['cantidad'] * /*$saldo[$j]['precio_unidad']*/$precio_unidad;
					}
				}
			}
			$tpl->gotoBlock("mp_total");
			$tpl->assign("total_unidades_entrada_total",number_format($total_unidades_entrada,2,".",","));
			$tpl->assign("total_valores_entrada_total",number_format($total_valores_entrada,2,".",","));
			$tpl->assign("total_unidades_salida_total",number_format($total_unidades_salida,2,".",","));
			$tpl->assign("total_valores_salida_total",number_format($total_valores_salida,2,".",","));
			$tpl->assign("total_unidades_total",number_format($unidades,2,".",","));
			$tpl->assign("total_valores_total",number_format($valores,2,".",","));
			$tpl->assign("ultimo_costo_promedio_total",number_format($costo_promedio,2,".",","));
			
			//ejecutar_script("UPDATE inventario_fin_mes SET precio_unidad=$costo_promedio WHERE num_cia=$_GET[num_cia] AND codmp=".$saldo_anterior[$i]['codmp'],$dsn);
			//ejecutar_script("UPDATE inventario_fin_mes SET existencia=$unidades WHERE num_cia=$_GET[num_cia] AND codmp=".$saldo_anterior[$i]['codmp'],$dsn);
			//$sql = "INSERT INTO inventario_fin_mes (num_cia,codmp,existencia,fecha,precio_unidad) VALUES (".$_GET['num_cia'].",".$saldo_anterior[$i]['codmp'].",".$unidades.",'".$fecha2."',".$costo_promedio.")";
			//ejecutar_script($sql,$dsn);
//			$sql = "UPDATE inventario_real SET existencia=$unidades,precio_unidad=$costo_promedio WHERE num_cia=".$_GET['num_cia']." AND codmp=".$saldo_anterior[$i]['codmp'];
//			ejecutar_script($sql,$dsn);
			
			$total_valores_anteriores += $valores_anteriores;
			$gran_total_valores_salida += $total_valores_salida;
			$gran_total_valores_entrada += $total_valores_entrada;
			$gran_total_valores += $valores;
		}
		$tpl->gotoBlock("listado_totales");
		$tpl->assign("total_valores_anteriores",number_format($total_valores_anteriores,2,".",","));
		$tpl->assign("total_valores_entrada_total",number_format($gran_total_valores_entrada,2,".",","));
		$tpl->assign("total_valores_salida_total",number_format($gran_total_valores_salida,2,".",","));
		$tpl->assign("total_valores_total",number_format($gran_total_valores,2,".",","));
		$tpl->printToScreen();
	}
}
if ($_GET['listado'] == "todas") {
	/*if ($_GET['tipo'] == "mp") {
		// Obtener saldos anteriores de hitorico_inventario
		$sql  = "SELECT ";
		$sql .= "num_cia,codmp,nombre,existencia,precio_unidad ";
		$sql .= "FROM ";
		$sql .= "historico_inventario ";
		$sql .= "JOIN ";
		$sql .= "catalogo_mat_primas ";
		$sql .= "USING(codmp) ";
		$sql .= "WHERE ";
		$sql .= "num_cia = $_GET[num_cia] ";
		$sql .= "AND ";
		$sql .= "fecha_entrada = '$fecha_historico' ";
		$sql .= "AND ";
		$sql .= "codmp = $_GET[codmp]";
		$saldo_anterior = ejecutar_script($sql,$dsn);
		
		// Obtener saldos de materias primas
		$sql  = "SELECT ";
		$sql .= "id,fecha,codmp,nombre,descripcion,tipo_mov,cantidad,precio_unidad ";
		$sql .= "FROM ";
		$sql .= "mov_inv_real ";
		$sql .= "JOIN ";
		$sql .= "catalogo_mat_primas ";
		$sql .= "USING(codmp) ";
		$sql .= "WHERE ";
		$sql .= "num_cia = $_GET[num_cia] ";
		$sql .= "AND ";
		$sql .= "fecha >= '$fecha1' ";
		$sql .= "AND ";
		$sql .= "fecha <= '$fecha2' ";
		$sql .= "AND ";
		$sql .= "codmp =  $_GET[codmp]";
		$sql .= "ORDER BY num_cia,codmp,fecha,tipo_mov";
		$saldo = ejecutar_script($sql,$dsn);
		
		$tpl->newBlock("listado");
		
		$tpl->assign("num_cia",$_GET['num_cia']);
		$cia = obtener_registro("catalogo_companias",array("num_cia"),array($_GET['num_cia']),"","",$dsn);
		$tpl->assign("nombre_cia",$cia[0]['nombre']);
		
		switch ($_GET['mes']) {
			case 1 : $mes = "Enero";      break;
			case 2 : $mes = "Febrero";    break;
			case 3 : $mes = "Marzo";      break;
			case 4 : $mes = "Abril";      break;
			case 5 : $mes = "Mayo";       break;
			case 6 : $mes = "Junio";      break;
			case 7 : $mes = "Julio";      break;
			case 8 : $mes = "Agosto";     break;
			case 9 : $mes = "Septiembre"; break;
			case 10: $mes = "Octubre";    break;
			case 11: $mes = "Noviembre";  break;
			case 12: $mes = "Diciembre";  break;
		}
		$tpl->assign("mes",$mes);
		$tpl->assign("anio",date("Y"));
		
		$tpl->newBlock("mp");
		$tpl->assign("codmp",$saldo_anterior[0]['codmp']);
		$tpl->assign("nombremp",$saldo_anterior[0]['nombre']);
		$tpl->assign("unidades_anteriores",number_format($saldo_anterior[0]['existencia'],2,".",","));
		$valores_anteriores = $saldo_anterior[0]['existencia']*$saldo_anterior[0]['precio_unidad'];
		$tpl->assign("valores_anteriores",number_format($valores_anteriores,2,".",","));
		$tpl->assign("costo_anterior",number_format($saldo_anterior[0]['precio_unidad'],2,".",","));
		
		$unidades = $saldo_anterior[0]['existencia'];
		$valores  = $saldo_anterior[0]['existencia'] * $saldo_anterior[0]['precio_unidad'];
		
		$total_unidades_entrada = 0;
		$total_valores_entrada  = 0;
		$total_unidades_salida  = 0;
		$total_valores_salida   = 0;
		$total_diferencia       = 0;
		$costo_promedio         = $saldo_anterior[0]['precio_unidad'];
		for ($j=0; $j<count($saldo); $j++) {
			if ($saldo[$j]['codmp'] == $saldo_anterior[0]['codmp']) {
				$tpl->newBlock("fila");
				$tpl->assign("fecha",$saldo[$j]['fecha']);
				$tpl->assign("concepto",$saldo[$j]['descripcion']);
				
				// Salidas
				if ($saldo[$j]['tipo_mov'] == "t") {
					$tpl->assign("costo_unitario",number_format($costo_promedio,2,".",","));
					$tpl->assign("unidades_salida",number_format($saldo[$j]['cantidad'],2,".",","));
					$tpl->assign("valores_salida",number_format($saldo[$j]['cantidad']*$costo_promedio,2,".",","));
					$tpl->assign("unidades_entrada","&nbsp;");
					$tpl->assign("valores_entrada","&nbsp;");
					$unidades -= $saldo[$j]['cantidad'];
					$valores  -= $saldo[$j]['cantidad'] * $costo_promedio;
					$tpl->assign("unidades_existencia",number_format($unidades,2,".",","));
					$tpl->assign("valores_existencia",number_format($valores,2,".",","));
					$tpl->assign("costo_promedio",number_format($costo_promedio,2,".",","));
					$tpl->assign("diferencia_costo","&nbsp;");
					
					$total_unidades_salida += $saldo[$j]['cantidad'];
					$total_valores_salida  += $saldo[$j]['cantidad'] * $costo_promedio;
				}
				// Entradas
				else if ($saldo[$j]['tipo_mov'] == "f") {
					$tpl->assign("costo_unitario",number_format($saldo[$j]['precio_unidad'],2,".",","));
					$tpl->assign("unidades_entrada",number_format($saldo[$j]['cantidad'],2,".",","));
					$tpl->assign("valores_entrada",number_format($saldo[$j]['cantidad']*$saldo[$j]['precio_unidad'],2,".",","));
					$tpl->assign("unidades_salida","&nbsp;");
					$tpl->assign("valores_salida","&nbsp;");
					$unidades += $saldo[$j]['cantidad'];
					$valores  += $saldo[$j]['cantidad'] * $saldo[$j]['precio_unidad'];
					$costo_promedio = $valores / $unidades;
					$tpl->assign("unidades_existencia",number_format($unidades,2,".",","));
					$tpl->assign("valores_existencia",number_format($valores,2,".",","));
					$tpl->assign("costo_promedio",number_format($costo_promedio,2,".",","));
					// Diferencia de costo inicial y costo final
					$diferencia_costo = $saldo[$j]['precio_unidad'] - $costo_promedio;
					$total_diferencia += $diferencia_costo;
					$tpl->assign("diferencia_costo",number_format($diferencia_costo,2,".",","));
					
					$total_unidades_entrada += $saldo[$j]['cantidad'];
					$total_valores_entrada  += $saldo[$j]['cantidad'] * $saldo[$j]['precio_unidad'];
				}
			}
		}
		$tpl->gotoBlock("mp");
		$tpl->assign("total_unidades_entrada",number_format($total_unidades_entrada,2,".",","));
		$tpl->assign("total_valores_entrada",number_format($total_valores_entrada,2,".",","));
		$tpl->assign("total_unidades_salida",number_format($total_unidades_salida,2,".",","));
		$tpl->assign("total_valores_salida",number_format($total_valores_salida,2,".",","));
		$tpl->assign("total_unidades",number_format($unidades,2,".",","));
		$tpl->assign("total_valores",number_format($valores,2,".",","));
		$tpl->assign("ultimo_costo_promedio",number_format($costo_promedio,2,".",","));
		$tpl->assign("total_diferencia",number_format($total_diferencia,2,".",","));
		
		$tpl->printToScreen();
	}
	else*/ if ($_GET['tipo'] == "desglozado") {
		$cia = ejecutar_script("SELECT num_cia,nombre FROM catalogo_companias WHERE num_cia > 100 AND num_cia < 200",$dsn);
		
		for ($c=0; $c<count($cia); $c++) {
			// Obtener saldos anteriores de hitorico_inventario
			$sql  = "SELECT ";
			$sql .= "num_cia,codmp,nombre,existencia,precio_unidad ";
			$sql .= "FROM ";
			$sql .= "historico_inventario ";
			$sql .= "JOIN ";
			$sql .= "catalogo_mat_primas ";
			$sql .= "USING(codmp) ";
			$sql .= "WHERE ";
			$sql .= "num_cia = ".$cia[$c]['num_cia'];
			$sql .= " AND ";
			$sql .= "fecha = '$fecha_historico' ";
			$sql .= "ORDER BY codmp";
			$saldo_anterior = ejecutar_script($sql,$dsn);
			
			// Obtener saldos de materias primas
			$sql  = "SELECT ";
			$sql .= "id,fecha,codmp,nombre,descripcion,tipo_mov,cantidad,precio_unidad,total_mov ";
			$sql .= "FROM ";
			$sql .= "mov_inv_real ";
			$sql .= "JOIN ";
			$sql .= "catalogo_mat_primas ";
			$sql .= "USING(codmp) ";
			$sql .= "WHERE ";
			$sql .= "num_cia = ".$cia[$c]['num_cia'];
			$sql .= " AND ";
			$sql .= "fecha >= '$fecha1' ";
			$sql .= "AND ";
			$sql .= "fecha <= '$fecha2' ";
			$sql .= "ORDER BY num_cia,codmp,fecha,tipo_mov";
			$saldo = ejecutar_script($sql,$dsn);
			
			$tpl->newBlock("listado");
			
			$tpl->assign("num_cia",$cia[$c]['num_cia']);
			$tpl->assign("nombre_cia",$cia[$c]['nombre_corto']);
			
			switch ($_GET['mes']) {
				case 1 : $mes = "Enero";      break;
				case 2 : $mes = "Febrero";    break;
				case 3 : $mes = "Marzo";      break;
				case 4 : $mes = "Abril";      break;
				case 5 : $mes = "Mayo";       break;
				case 6 : $mes = "Junio";      break;
				case 7 : $mes = "Julio";      break;
				case 8 : $mes = "Agosto";     break;
				case 9 : $mes = "Septiembre"; break;
				case 10: $mes = "Octubre";    break;
				case 11: $mes = "Noviembre";  break;
				case 12: $mes = "Diciembre";  break;
			}
			$tpl->assign("mes",$mes);
			$tpl->assign("anio",date("Y"));
			
			$total_valores_anteriores = 0;
			$gran_total_valores_entrada = 0;
			$gran_total_valores_salida = 0;
			$gran_total_valores = 0;
			
			for ($i=0; $i<count($saldo_anterior); $i++) {
				$tpl->newBlock("mp");
				$tpl->assign("codmp",$saldo_anterior[$i]['codmp']);
				$tpl->assign("nombremp",$saldo_anterior[$i]['nombre']);
				$tpl->assign("unidades_anteriores",number_format($saldo_anterior[$i]['existencia'],2,".",","));
				$valores_anteriores = $saldo_anterior[$i]['existencia']*$saldo_anterior[$i]['precio_unidad'];
				$tpl->assign("valores_anteriores",number_format($valores_anteriores,2,".",","));
				$tpl->assign("costo_anterior",number_format($saldo_anterior[$i]['precio_unidad'],2,".",","));
				
				$unidades = $saldo_anterior[$i]['existencia'];
				$valores  = $saldo_anterior[$i]['existencia'] * $saldo_anterior[$i]['precio_unidad'];
				
				$total_unidades_entrada = 0;
				$total_valores_entrada  = 0;
				$total_unidades_salida  = 0;
				$total_valores_salida   = 0;
				$total_diferencia       = 0;
				$costo_promedio         = $saldo_anterior[$i]['precio_unidad'];
				for ($j=0; $j<count($saldo); $j++) {
					if ($saldo[$j]['codmp'] == $saldo_anterior[$i]['codmp']) {
						$tpl->newBlock("fila");
						$tpl->assign("fecha",$saldo[$j]['fecha']);
						$tpl->assign("concepto",$saldo[$j]['descripcion']);
						
						// Salidas
						if ($saldo[$j]['tipo_mov'] == "t") {
							$tpl->assign("costo_unitario",number_format($costo_promedio,2,".",","));
							$tpl->assign("unidades_salida",number_format($saldo[$j]['cantidad'],2,".",","));
							$tpl->assign("valores_salida",number_format($saldo[$j]['cantidad']*$costo_promedio,2,".",","));
							$tpl->assign("unidades_entrada","&nbsp;");
							$tpl->assign("valores_entrada","&nbsp;");
							$unidades -= $saldo[$j]['cantidad'];
							$valores  -= $saldo[$j]['cantidad'] * $costo_promedio;
							$tpl->assign("unidades_existencia",number_format($unidades,2,".",","));
							$tpl->assign("valores_existencia",number_format($valores,2,".",","));
							$tpl->assign("costo_promedio",number_format($costo_promedio,2,".",","));
							$total_unidades_salida += $saldo[$j]['cantidad'];
							$total_valores_salida  += $saldo[$j]['cantidad'] * $costo_promedio;
							$tpl->assign("diferencia_costo","&nbsp;");
							
							$total_unidades_salida += $saldo[$j]['cantidad'];
							$total_valores_salida  += $saldo[$j]['cantidad'] * $costo_promedio;
						}
						// Entradas
						else if ($saldo[$j]['tipo_mov'] == "f") {
							@$precio_unidad = $saldo[$j]['total_mov'] / $saldo[$j]['cantidad'];
							$tpl->assign("costo_unitario",number_format(/*$saldo[$j]['precio_unidad']*/$precio_unidad,2,".",","));
							$tpl->assign("unidades_entrada",number_format($saldo[$j]['cantidad'],2,".",","));
							$tpl->assign("valores_entrada",number_format($saldo[$j]['cantidad']*/*$saldo[$j]['precio_unidad']*/$precio_unidad,2,".",","));
							$tpl->assign("unidades_salida","&nbsp;");
							$tpl->assign("valores_salida","&nbsp;");
							$unidades += $saldo[$j]['cantidad'];
							$valores  += $saldo[$j]['cantidad'] * /*$saldo[$j]['precio_unidad']*/$precio_unidad;
							@$costo_promedio = $valores / $unidades;
							$tpl->assign("unidades_existencia",number_format($unidades,2,".",","));
							$tpl->assign("valores_existencia",number_format($valores,2,".",","));
							$tpl->assign("costo_promedio",number_format($costo_promedio,2,".",","));
							$total_unidades_entrada += $saldo[$j]['cantidad'];
							$total_valores_entrada  += $saldo[$j]['cantidad'] * /*$saldo[$j]['precio_unidad']*/$precio_unidad;
							// Diferencia de costo inicial y costo final
							$diferencia_costo = /*$saldo[$j]['precio_unidad']*/$precio_unidad - $costo_promedio;
							$total_diferencia += $diferencia_costo;
							$tpl->assign("diferencia_costo",number_format($diferencia_costo,2,".",","));
							
							$total_unidades_salida += $saldo[$j]['cantidad'];
							$total_valores_salida  += $saldo[$j]['cantidad'] * $costo_promedio;
						}
					}
				}
				$tpl->gotoBlock("mp");
				$tpl->assign("total_unidades_entrada",number_format($total_unidades_entrada,2,".",","));
				$tpl->assign("total_valores_entrada",number_format($total_valores_entrada,2,".",","));
				$tpl->assign("total_unidades_salida",number_format($total_unidades_salida,2,".",","));
				$tpl->assign("total_valores_salida",number_format($total_valores_salida,2,".",","));
				$tpl->assign("total_unidades",number_format($unidades,2,".",","));
				$tpl->assign("total_valores",number_format($valores,2,".",","));
				$tpl->assign("ultimo_costo_promedio",number_format($costo_promedio,2,".",","));
				$tpl->assign("total_diferencia",number_format($total_diferencia,2,".",","));
			}
		}
		$tpl->printToScreen();
	}
	else if ($_GET['tipo'] == "totales") {
		//$cia = ejecutar_script("SELECT num_cia,nombre_corto FROM catalogo_companias WHERE num_cia > 100 AND num_cia < 200",$dsn);
		$cia = ejecutar_script("SELECT num_cia,nombre_corto FROM catalogo_companias WHERE num_cia < 100 order by num_cia",$dsn);
		
		for ($c=0; $c<count($cia); $c++) {
			// Obtener saldos anteriores de hitorico_inventario
			$sql  = "SELECT ";
			$sql .= "num_cia,codmp,nombre,existencia,precio_unidad ";
			$sql .= "FROM ";
			$sql .= "historico_inventario ";
			$sql .= "JOIN ";
			$sql .= "catalogo_mat_primas ";
			$sql .= "USING(codmp) ";
			$sql .= "WHERE ";
			$sql .= "num_cia = ".$cia[$c]['num_cia'];
			$sql .= " AND ";
			$sql .= "fecha = '$fecha_historico' ";
			$sql .= "ORDER BY codmp";
			$saldo_anterior = ejecutar_script($sql,$dsn);
			
			// Obtener saldos de materias primas
			$sql  = "SELECT ";
			$sql .= "fecha,codmp,nombre,tipo_mov,cantidad,precio_unidad,total_mov ";
			$sql .= "FROM ";
			$sql .= "mov_inv_real ";
			$sql .= "JOIN ";
			$sql .= "catalogo_mat_primas ";
			$sql .= "USING(codmp) ";
			$sql .= "WHERE ";
			$sql .= "num_cia = ".$cia[$c]['num_cia'];
			$sql .= " AND ";
			$sql .= "fecha >= '$fecha1' ";
			$sql .= "AND ";
			$sql .= "fecha <= '$fecha2' ";
			$sql .= "ORDER BY num_cia,codmp,fecha,tipo_mov";
			$saldo = ejecutar_script($sql,$dsn);

			$facturas=ejecutar_script("select sum(costo_unitario) from entrada_mp where num_cia=".$cia[$c]['num_cia']." and fecha between '$fecha1' and '$fecha2'",$dsn);			
			$tpl->newBlock("listado_totales");
			
			$tpl->assign("num_cia",$cia[$c]['num_cia']);
			$tpl->assign("nombre_cia",$cia[$c]['nombre_corto']);
			
			switch ($_GET['mes']) {
				case 1 : $mes = "Enero";      break;
				case 2 : $mes = "Febrero";    break;
				case 3 : $mes = "Marzo";      break;
				case 4 : $mes = "Abril";      break;
				case 5 : $mes = "Mayo";       break;
				case 6 : $mes = "Junio";      break;
				case 7 : $mes = "Julio";      break;
				case 8 : $mes = "Agosto";     break;
				case 9 : $mes = "Septiembre"; break;
				case 10: $mes = "Octubre";    break;
				case 11: $mes = "Noviembre";  break;
				case 12: $mes = "Diciembre";  break;
			}
			$tpl->assign("mes",$mes);
			$tpl->assign("anio",date("Y"));
			
			$total_valores_anteriores = 0;
			$gran_total_valores_entrada = 0;
			$gran_total_valores_salida = 0;
			$gran_total_valores = 0;
			
			for ($i=0; $i<count($saldo_anterior); $i++) {
				if($saldo_anterior[$i]['codmp']==90) continue;
				$tpl->newBlock("mp_total");
				$tpl->assign("codmp_total",$saldo_anterior[$i]['codmp']);
				$tpl->assign("nombremp_total",$saldo_anterior[$i]['nombre']);
				$tpl->assign("unidades_anteriores_total",number_format($saldo_anterior[$i]['existencia'],2,".",","));
				$valores_anteriores = $saldo_anterior[$i]['existencia']*$saldo_anterior[$i]['precio_unidad'];
				$tpl->assign("valores_anteriores_total",number_format($valores_anteriores,2,".",","));
				$tpl->assign("costo_anterior_total",number_format($saldo_anterior[$i]['precio_unidad'],2,".",","));
				
				$unidades = $saldo_anterior[$i]['existencia'];
				$valores  = $saldo_anterior[$i]['existencia'] * $saldo_anterior[$i]['precio_unidad'];
				
				$total_unidades_entrada = 0;
				$total_valores_entrada = 0;
				$total_unidades_salida = 0;
				$total_valores_salida = 0;
				$costo_promedio = $saldo_anterior[$i]['precio_unidad'];
				for ($j=0; $j<count($saldo); $j++) {
					if ($saldo[$j]['codmp'] == $saldo_anterior[$i]['codmp']) {
						// Salidas
						if ($saldo[$j]['tipo_mov'] == "t") {
							$unidades -= $saldo[$j]['cantidad'];
							$valores  -= $saldo[$j]['cantidad'] * $costo_promedio;
							$total_unidades_salida += $saldo[$j]['cantidad'];
							$total_valores_salida  += $saldo[$j]['cantidad'] * $costo_promedio;
						}
						// Entradas
						else if ($saldo[$j]['tipo_mov'] == "f") {
							@$precio_unidad = $saldo[$j]['total_mov'] / $saldo[$j]['cantidad'];
							$unidades += $saldo[$j]['cantidad'];
							$valores  += $saldo[$j]['cantidad'] * /*$saldo[$j]['precio_unidad']*/$precio_unidad;
							if ($unidades > 0)
								$costo_promedio = $valores / $unidades;
							//else
								//$costo_promedio = 0;
							$total_unidades_entrada += $saldo[$j]['cantidad'];
							$total_valores_entrada  += $saldo[$j]['cantidad'] * /*$saldo[$j]['precio_unidad']*/$precio_unidad;
						}
					}
				}
				$tpl->gotoBlock("mp_total");
				$tpl->assign("total_unidades_entrada_total",number_format($total_unidades_entrada,2,".",","));
				$tpl->assign("total_valores_entrada_total",number_format($total_valores_entrada,2,".",","));
				$tpl->assign("total_unidades_salida_total",number_format($total_unidades_salida,2,".",","));
				$tpl->assign("total_valores_salida_total",number_format($total_valores_salida,2,".",","));
				$tpl->assign("total_unidades_total",number_format($unidades,2,".",","));
				$tpl->assign("total_valores_total",number_format($valores,2,".",","));
				$tpl->assign("ultimo_costo_promedio_total",number_format($costo_promedio,2,".",","));
				
				//ejecutar_script("INSERT INTO historico_inventario (num_cia,codmp,fecha,existencia,precio_unidad) VALUES (".$cia[$c]['num_cia'].",".$saldo_anterior[$i]['codmp'].",'31/08/2004',$unidades,$costo_promedio)",$dsn);
				//ejecutar_script("UPDATE historico_inventario SET precio_unidad=$costo_promedio,existencia=$unidades,fecha='31/08/2004' WHERE num_cia=".$cia[$c]['num_cia']." AND codmp=".$saldo_anterior[$i]['codmp'],$dsn);
				//ejecutar_script("UPDATE inventario_fin_mes SET existencia=$unidades WHERE num_cia=".$cia[$c]['num_cia']." AND codmp=".$saldo_anterior[$i]['codmp'],$dsn);
				//$sql = "INSERT INTO inventario_fin_mes (num_cia,codmp,existencia,fecha,precio_unidad) VALUES (".$cia[$c]['num_cia'].",".$saldo_anterior[$i]['codmp'].",".$unidades.",'".$fecha2."',".$costo_promedio.")";
				//ejecutar_script($sql,$dsn);
///				$sql = "UPDATE inventario_real SET existencia=$unidades,precio_unidad=$costo_promedio WHERE num_cia=".$cia[$c]['num_cia']." AND codmp=".$saldo_anterior[$i]['codmp'];
//				ejecutar_script($sql,$dsn);
				
				$total_valores_anteriores += $valores_anteriores;
				$gran_total_valores_salida += $total_valores_salida;
				$gran_total_valores_entrada += $total_valores_entrada;
				$gran_total_valores += $valores;
			}
			$tpl->gotoBlock("listado_totales");
			$tpl->assign("facturas",number_format($facturas[0]['sum'],2,'.',','));
			$tpl->assign("total_valores_anteriores",number_format($total_valores_anteriores,2,".",","));
			$tpl->assign("total_valores_entrada_total",number_format($gran_total_valores_entrada,2,".",","));
			$tpl->assign("total_valores_salida_total",number_format($gran_total_valores_salida,2,".",","));
			$tpl->assign("total_valores_total",number_format($gran_total_valores,2,".",","));
		}
		$tpl->printToScreen();
	}
}

?>