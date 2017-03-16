<?php
// LISTADO DE CONSUMOS ACUMULADOS EN VALORES DEL MES
// Tabla 'mov_inv_real ó mov_inv_virtual'
// Menu 'Panaderías->Producción'

define ('IDSCREEN',1241); // ID de pantalla

// --------------------------------- INCLUDES ----------------------------------------------------------------
//include './includes/class.db3.inc.php';
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);

// --------------------------------- Validar acceso de usuario a la pantalla ---------------------------------
$session->validar_pantalla(IDSCREEN);

// --------------------------------- Obtener información de la pantalla --------------------------------------
$session->info_pantalla();

// --------------------------------- Descripcion de errores --------------------------------------------------
$descripcion_error[1] = "No hay resultados";

// --------------------------------- Generar pantalla --------------------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/pan/pan_con_acu.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

// -------------------------------- Buscar datos de compañía -------------------------------------------------
if (!isset($_GET['num_cia'])) {
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
$db = new DBclass($dsn);


// Variables
ereg("([0-9]{1,2})/([0-9]{1,2})/([0-9]{2,4})",$_GET['fecha'],$fecha);
$fecha1 = "1/$fecha[2]/$fecha[3]";
$fecha2 = $_GET['fecha'];
$fecha_historico = date("d/m/Y",mktime(0,0,0,$fecha[2],0,$fecha[3]));

$tabla_inventario = "inventario_real";	// Tabla de donde se tomara el inventario
$tabla_movimientos = "mov_inv_real";	// Tabla de donde se tomaran los movimientos

// Obtener listado de compañías
$sql = "SELECT num_cia,nombre,nombre_corto FROM catalogo_companias WHERE";
if ($_GET['num_cia'] > 0)
	$sql .= " num_cia = $_GET[num_cia]";
else
	$sql .= " num_cia < 100";
$sql .= " ORDER BY num_cia";
$cia = $db->query($sql);

if (!$cia) {
	header("location: ./pan_con_acu.php?codigo_error=1");
	$db->desconectar();
	die;
}

//$inicio = time();
for ($c=0; $c<count($cia); $c++) {
	$num_cia = $cia[$c]['num_cia'];			// Número de Compañía
	
	// Obtener listado de las materias primas
	$sql = "SELECT codmp,nombre,existencia,precio_unidad FROM control_avio LEFT JOIN catalogo_mat_primas USING(codmp) LEFT JOIN inventario_real USING(num_cia,codmp) WHERE num_cia=$num_cia GROUP BY codmp,nombre,num_orden,existencia,precio_unidad ORDER BY num_orden ASC";
	$mp = $db->query($sql);
	
	if ($mp) {
		$tpl->newBlock("listado");
		$tpl->assign("num_cia",$num_cia);
		$tpl->assign("nombre_cia",$cia[$c]['nombre']);
		$tpl->assign("nombre_corto",$cia[$c]['nombre_corto']);
		ereg("([0-9]{1,2})/([0-9]{1,2})/([0-9]{2,4})",$fecha2,$fecha_des);
		$tpl->assign("dia",$fecha_des[1]);
		$tpl->assign("mes",mes_escrito($fecha_des[2]));
		$tpl->assign("anio",$fecha_des[3]);
		
		$consumo = array(1=>0,2=>0,3=>0,4=>0,8=>0,9=>0,10=>0);
		$faltante = array(1=>0,2=>0,3=>0,4=>0,8=>0,9=>0,10=>0);
		$sobrante = array(1=>0,2=>0,3=>0,4=>0,8=>0,9=>0,10=>0);
		$no_controlado = array(1=>0,2=>0,3=>0,4=>0,8=>0,9=>0,10=>0);
		$mer = array(1=>0,2=>0,3=>0,4=>0,8=>0,9=>0,10=>0);
		$total_dif_fal = 0;
		$total_dif_sob = 0;
		
		for ($i=0; $i<count($mp); $i++) {
			$codmp = $mp[$i]['codmp'];
			$consumo_total = 0;
			
			/*****************************************************************************************************/
			// Obtener saldos anteriores de historico_inventario
			$sql  = "SELECT num_cia,codmp,nombre,existencia,precio_unidad FROM historico_inventario JOIN catalogo_mat_primas USING(codmp) WHERE num_cia = $num_cia ";
			$sql .= " AND codmp = $codmp AND fecha = '$fecha_historico'";
			$saldo_anterior = $db->query($sql);
			
			// Obtener saldos de materias primas
			$sql  = "SELECT fecha,codmp,nombre,cod_turno,tipo_mov,cantidad,precio_unidad,total_mov,descripcion FROM mov_inv_real JOIN catalogo_mat_primas USING(codmp) WHERE num_cia = $num_cia ";
			//$sql .= " AND codmp = $codmp AND fecha BETWEEN '$fecha1' AND '$fecha2' AND descripcion != 'DIFERENCIA INVENTARIO' ORDER BY fecha,tipo_mov";
			$sql .= " AND codmp = $codmp AND fecha BETWEEN '$fecha1' AND '$fecha2' ORDER BY fecha,tipo_mov";
			$saldo = $db->query($sql);
			
			if ($saldo) {
				$tpl->newBlock("fila");
				$tpl->assign("codmp",$codmp);
				$tpl->assign("nombre",$mp[$i]['nombre']);
				
				// Calcular arrastre de salidas
				$unidades = $saldo_anterior[0]['existencia'];
				$valores  = $saldo_anterior[0]['existencia'] * $saldo_anterior[0]['precio_unidad'];
				$costo_promedio = round($saldo_anterior[0]['precio_unidad'],3);
				$cantidad_anterior = 0;
				$consumo_turno = array(1=>0,2=>0,3=>0,4=>0,8=>0,9=>0,10=>0);
				$dif_fal = 0;
				$dif_sob = 0;
				for ($j=0; $j<count($saldo); $j++) {
					// Salidas
					if ($saldo[$j]['tipo_mov'] == "t") {
						$unidades -= round($saldo[$j]['cantidad'],2);
						if ($unidades < 0)
							$valores = 0;
						else
							$valores -= round($saldo[$j]['cantidad'] * $costo_promedio,2);
						$cantidad_anterior = $unidades;
						
						if ($saldo[$j]['descripcion'] != "DIFERENCIA INVENTARIO") {
							@$consumo_turno[$saldo[$j]['cod_turno']] += $saldo[$j]['cantidad'] * $costo_promedio;
							@$consumo[$saldo[$j]['cod_turno']] += /*$consumo_turno[$saldo[$j]['cod_turno']]*/$saldo[$j]['cantidad'] * $costo_promedio;
							$consumo_total += $saldo[$j]['cantidad'] * $costo_promedio;
						}
						else
							$dif_fal += $saldo[$j]['cantidad'] * $costo_promedio;
						
						@$tpl->assign($saldo[$j]['cod_turno'],number_format($consumo_turno[$saldo[$j]['cod_turno']],2,".",","));
					}
					// Entradas
					else if ($saldo[$j]['tipo_mov'] == "f") {
						@$precio_unidad = round($saldo[$j]['total_mov'] / $saldo[$j]['cantidad'],3);
						$unidades += $saldo[$j]['cantidad'];
						if (/*$unidades > 0*/round($cantidad_anterior + $saldo[$j]['cantidad'],2) > 0)
							$valores  += /*$saldo[$j]['cantidad'] * $precio_unidad*/round($saldo[$j]['total_mov'],2);
						else
							$valores = 0;
						if (round($cantidad_anterior,2) <= 0)
							$costo_promedio = round($precio_unidad,3);
						else
							@$costo_promedio = round($valores / $unidades,3);
						$cantidad_anterior = $unidades;
						
						if ($saldo[$j]['descripcion'] == "DIFERENCIA INVENTARIO")
							$dif_sob += $saldo[$j]['total_mov'];
					}
				}
				$tpl->assign("precio_unidad",number_format($costo_promedio,2,".",","));
				$tpl->assign("consumo",($consumo_total > 0)?number_format($consumo_total,2,".",","):"&nbsp;");
			}
			
			// Si hay faltantes, calcular el promedio para el turno y sumarlo a faltantes
			if ($dif_fal > 0)
				foreach ($consumo_turno as $key => $value) {
					@$promedio = $value * 100 / $consumo_total;
					@$faltante[$key] += $dif_fal * $promedio / 100;
				}
			// Si hay sobrantes, calcular el promedio para el turno y sumarlo a sobrantes
			if ($dif_sob > 0)
				foreach ($consumo_turno as $key => $value) {
					@$promedio = $value * 100 / $consumo_total;
					@$sobrante[$key] += $dif_sob * $promedio / 100;
				}
		}
		
		$tpl->gotoBlock("listado");
		
		// No controlados
		
		// Produccion y consumo/produccion
		$sql = "SELECT SUM(imp_produccion) FROM produccion WHERE num_cia=$num_cia AND fecha BETWEEN '$fecha1' AND '$fecha2' AND cod_turnos = 1";
		$pro1 = $db->query($sql);
		$sql = "SELECT SUM(imp_produccion) FROM produccion WHERE num_cia=$num_cia AND fecha BETWEEN '$fecha1' AND '$fecha2' AND cod_turnos = 2";
		$pro2 = $db->query($sql);
		$sql = "SELECT SUM(imp_produccion) FROM produccion WHERE num_cia=$num_cia AND fecha BETWEEN '$fecha1' AND '$fecha2' AND cod_turnos = 3";
		$pro3 = $db->query($sql);
		$sql = "SELECT SUM(imp_produccion) FROM produccion WHERE num_cia=$num_cia AND fecha BETWEEN '$fecha1' AND '$fecha2' AND cod_turnos = 4";
		$pro4 = $db->query($sql);
		
		$sql = "SELECT SUM(cantidad*precio_unidad) FROM mov_inv_real JOIN catalogo_mat_primas USING(codmp) WHERE num_cia = $num_cia AND fecha BETWEEN '$fecha1' AND '$fecha2' AND tipo_mov='TRUE' AND controlada='FALSE' AND tipo=1 AND codmp != 90";
		$mp_nc = $db->query($sql);
		$sql = "SELECT SUM(cantidad*precio_unidad) FROM mov_inv_real JOIN catalogo_mat_primas USING(codmp) WHERE num_cia = $num_cia AND fecha BETWEEN '$fecha1' AND '$fecha2' AND tipo_mov='TRUE' AND controlada='FALSE' AND tipo=2 AND codmp != 90";
		$me_nc = $db->query($sql);
		
		$no_controlado[1] = $pro3[0]['sum'] > 0 ? 0.025 * $mp_nc[0]['sum'] : 0.50 * $mp_nc[0]['sum'];
		$no_controlado[2] = $pro3[0]['sum'] > 0 ? 0.025 * $mp_nc[0]['sum'] : 0.50 * $mp_nc[0]['sum'];
		$no_controlado[3] = $pro4[0]['sum'] > 0 ? 0.15 * $mp_nc[0]['sum'] + $me_nc[0]['sum'] * 0.20 : $pro3[0]['sum'] > 0 ? 0.95 * $mp_nc[0]['sum'] + $me_nc[0]['sum'] * 0.90 : 0;
		$no_controlado[4] =  $pro4[0]['sum'] > 0 ? 0.80 * $mp_nc[0]['sum'] + $me_nc[0]['sum'] * 0.70 : 0;
		$no_controlado[8] = 0;
		$no_controlado[9] = 0;
		$no_controlado[10] = $me_nc[0]['sum'] * 0.10;
		
		$total_no_controlado = 0;
		foreach ($no_controlado as $key => $value)
			$tpl->assign($key."_no_control",($value != 0)?number_format($value,2,".",","):"&nbsp;");
		$total_no_controlado = $mp_nc[0]['sum'] + $me_nc[0]['sum'];
		$tpl->assign("total_no_control",($total_no_controlado != 0)?number_format($total_no_controlado,2,".",","):"&nbsp;");
		
		// Diferencias
		// Faltantes
		$total_faltante = 0;
		foreach ($faltante as $key => $value) {
			$tpl->assign($key."_faltante",($value != 0)?number_format($value,2,".",","):"&nbsp;");
			$consumo[$key] += $value;
			$total_faltante += $value;
		}
		$tpl->assign("total_faltante",($total_faltante != 0)?number_format($total_faltante,2,".",","):"&nbsp;");
		// Sobrantes
		$total_sobrante = 0;
		foreach ($sobrante as $key => $value) {
			$tpl->assign($key."_sobrante",($value != 0)?number_format($value,2,".",","):"&nbsp;");
			$consumo[$key] -= $value;
			$total_sobrante += $value;
		}
		$tpl->assign("total_sobrante",($total_sobrante != 0)?number_format($total_sobrante,2,".",","):"&nbsp;");
		
		// Consumo
		$total_consumo = 0;
		foreach ($consumo as $key => $value) {
			$tpl->assign($key."_consumo",number_format($value,2,".",","));
			$total_consumo += $value;
		}
		$tpl->assign("total_consumo",number_format($total_consumo,2,".",","));
		
		// Mercancias
		$sql = "SELECT sum(importe) FROM movimiento_gastos WHERE num_cia = $num_cia AND fecha >= '$fecha1' AND fecha <= '$fecha2' AND codgastos IN (23,9,76)";
		$temp = $db->query($sql);
		// Gastos de caja de codigo 28
		$abarrotes_julild_salida = $db->query("SELECT sum(importe) FROM gastos_caja WHERE num_cia = $num_cia AND fecha BETWEEN '$fecha1' AND '$fecha2' AND cod_gastos = 28 AND tipo_mov = 'FALSE'");
		$abarrotes_julild_entrada = $db->query("SELECT sum(importe) FROM gastos_caja WHERE num_cia = $num_cia AND fecha BETWEEN '$fecha1' AND '$fecha2' AND cod_gastos = 28 AND tipo_mov = 'TRUE'");
		
		$abarrotes_julild = $abarrotes_julild_salida[0]['sum'] - $abarrotes_julild_entrada[0]['sum'];
		
		$mercancias = $temp[0]['sum'] + $abarrotes_julild;
		$tpl->assign("mercancias",($mercancias != 0 || $mercancias != "")?number_format($mercancias,2,".",","):"&nbsp;");
		
		// Produccion y consumo/produccion
		$sql = "SELECT cod_turnos,sum(imp_produccion) FROM produccion WHERE num_cia=$num_cia AND fecha BETWEEN '$fecha1' AND '$fecha2' GROUP BY cod_turnos ORDER BY cod_turnos";
		$produccion = $db->query($sql);
		// PRODUCCION
		if ($produccion) {
			$total_produccion = 0;
			$con_pro = 0;
			for ($i=0; $i<count($produccion); $i++) {
				$tpl->assign($produccion[$i]['cod_turnos']."_produccion",$produccion[$i]['sum'] > 0 ? number_format($produccion[$i]['sum'],2,".",",") : "&nbsp;");
				@$tpl->assign($produccion[$i]['cod_turnos']."_con_pro",round($consumo[$produccion[$i]['cod_turnos']]/$produccion[$i]['sum'],3) > 0 ? number_format($consumo[$produccion[$i]['cod_turnos']]/$produccion[$i]['sum'],3,".",",") : "&nbsp;");
				$total_produccion += $produccion[$i]['sum'];
				//@$con_pro += $consumo[$produccion[$i]['cod_turnos']]/$produccion[$i]['sum'];
			}
			
			$tpl->assign("total_produccion",number_format($total_produccion,2,".",","));
			$tpl->assign("con_pro",number_format(/*$con_pro/$i*/$consumo_total/$total_produccion,3,".",","));
			
			// Desglozar mercancias
			for ($i=0; $i<count($produccion); $i++)
				if ($produccion[$i]['cod_turnos'] != 1 && $produccion[$i]['cod_turnos'] != 2 && $produccion[$i]['cod_turnos'] != 10) {
					@$porcentaje = ($produccion[$i]['sum'] * 100) / ($total_produccion - $produccion[0]['sum'] - $produccion[1]['sum']);
					@$mer[$produccion[$i]['cod_turnos']] = $mercancias * $porcentaje / 100;
					$tpl->assign($produccion[$i]['cod_turnos']."_mercancias",number_format($mer[$produccion[$i]['cod_turnos']],2,".",","));
					
					$consumo[$produccion[$i]['cod_turnos']] += $mer[$produccion[$i]['cod_turnos']];
				}
		}
		// Consumo
		$total_consumo = 0;
		$consumo_total = 0;
		foreach ($consumo as $key => $value) {
			@$consumo[$key] += $no_controlado[$key];
			@$tpl->assign($key."_consumo_total",number_format($consumo[$key],2,".",","));
			@$consumo_total += $consumo[$key];
		}
		// CONSUMO / PRODUCCION
		for ($i=0; $i<count($produccion); $i++) {
			@$tpl->assign($produccion[$i]['cod_turnos']."_con_pro",round($consumo[$produccion[$i]['cod_turnos']]/$produccion[$i]['sum'],3) > 0 ? number_format($consumo[$produccion[$i]['cod_turnos']]/$produccion[$i]['sum'],3,".",",") : "&nbsp;");
			@$con_pro += $consumo[$produccion[$i]['cod_turnos']]/$produccion[$i]['sum'];
		}
		//$consumo_total += $mercancias;
		$tpl->assign("con_pro",round($consumo_total / $total_produccion,3) > 0 ? number_format($consumo_total / $total_produccion,3,".",",") : "&nbsp;");
		$tpl->assign("consumo_total",number_format($consumo_total,2,".",","));
		
		// MP / Producción (Balance)
		$sql = "SELECT mp_pro FROM balances_pan WHERE num_cia=$num_cia AND mes=$fecha[2] AND anio=$fecha[3]";
		$temp = $db->query($sql);
		$mp_pro = ($temp)?$temp[0]['mp_pro']:0;
		$tpl->assign("mp_pro",number_format($mp_pro,3,".",","));
	}
}

//$fin = time();
//echo "Tiempo de ejecución: ".date("i:s",$fin-$inicio);

$tpl->printToScreen();
$db->desconectar();
?>