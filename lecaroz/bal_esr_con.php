<?php
// PROCESO SECUENCIAL
// Tablas 'compra_directa', 'hoja_dia_rost', 'movimiento_gastos', 'total_companias'
// Menu 'No definido'

define ('IDSCREEN',2); // ID de pantalla

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

if (!isset($_GET['compania']) && !isset($_GET['todas'])) {
	// Hacer un nuevo objeto TemplatePower
	$tpl = new TemplatePower( "./plantillas/header.tpl" );
	
	// Incluir el cuerpo del documento
	$tpl->assignInclude("body","./plantillas/bal/bal_esr_con.tpl");
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


if (isset($_GET['todas'])) {
	$sql = "SELECT num_cia,nombre,nombre_corto FROM catalogo_companias WHERE (num_cia BETWEEN 301 AND 599 OR num_cia IN (702,704)) ORDER BY num_cia ASC";
	//$sql = "SELECT num_cia,nombre,nombre_corto FROM catalogo_companias WHERE num_cia IN (344, 379, 332, 331, 354, 393, 373) ORDER BY num_cia ASC";
	$nombre_cia = ejecutar_script($sql,$dsn);
}
else {
	$sql = "SELECT num_cia,nombre,nombre_corto FROM catalogo_companias WHERE num_cia = $_GET[compania]";
	$nombre_cia = ejecutar_script($sql,$dsn);
}

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/bal/bal_esr_scr.tpl" );
$tpl->prepare();

for ($f=0; $f<count($nombre_cia); $f++) {
//$cia = $_GET['compania'];
$cia = $nombre_cia[$f]['num_cia'];



// -------------------------------------------- TRAZAR REPORTE -------------------------------------------------------------------------
$tpl->newBlock("reporte");

// Totales de gastos
$super_gran_total = 0;

$sql = "SELECT DISTINCT ON (codgastos) * FROM catalogo_gastos JOIN movimiento_gastos USING (codgastos) WHERE num_cia=$cia AND fecha>='$fecha1' AND fecha<='$fecha2' AND codigo_edo_resultados=1 ORDER BY codgastos";
$codigos = ejecutar_script($sql,$dsn);
if ($codigos) {
	$tpl->newBlock("gastos_operacion");
	$gran_total = 0;
	for ($i=0; $i<count($codigos); $i++) {
		$sql = "SELECT sum(importe) FROM catalogo_gastos JOIN movimiento_gastos USING (codgastos) WHERE num_cia=$cia AND codgastos=".$codigos[$i]['codgastos']." AND fecha>='$fecha1' AND fecha<='$fecha2' AND codigo_edo_resultados=1";
		$result = ejecutar_script($sql,$dsn);
		$tpl->newBlock("fila_totales");
		$tpl->assign("cod_total",$codigos[$i]['codgastos']);
		$tpl->assign("nombre_total",$codigos[$i]['descripcion']);
		$tpl->assign("importe_total",number_format($result[0]['sum'],2,".",","));
		$gran_total += $result[0]['sum'];
		$super_gran_total += $result[0]['sum'];
	}
	$tpl->gotoBlock("gastos_operacion");
	$tpl->assign("gran_total_total",number_format($gran_total,2,".",","));
}

// [4-Ago-2008] Quitar de comisiones bancarias el código 78 y sumarlo a los gastos generales
if (mktime(0, 0, 0, $mes, 1, $anio) >= mktime(0, 0, 0, 7, 1, 2008)) {
	$sql = "SELECT round(sum(importe)::numeric - 25000, 2) * 0.02 AS sum FROM estado_cuenta WHERE num_cia = $cia AND fecha_con BETWEEN '$fecha1' AND '$fecha2' AND cod_mov IN (1, 7, 13, 16, 79)";
	$importe_2por_ide = ejecutar_script($sql, $dsn);
}

$sql = "SELECT DISTINCT ON (codgastos) * FROM catalogo_gastos JOIN movimiento_gastos USING (codgastos) WHERE num_cia=$cia AND fecha>='$fecha1' AND fecha<='$fecha2' AND codigo_edo_resultados=2" . (mktime(0, 0, 0, $mes, 1, $anio) >= mktime(0, 0, 0, 2, 1, 2007) ? ' AND codgastos NOT IN (140)' : '')/* . (mktime(0, 0, 0, $mes, 1, $anio) >= mktime(0, 0, 0, 4, 1, 2008) ? ' AND (codgastos NOT IN (141))' : '')*/ . " ORDER BY codgastos";
$codigos = ejecutar_script($sql,$dsn);

$imp_ide_ins = FALSE;

if ($codigos) {
	$tpl->newBlock("gastos_gral");
	$gran_total = 0;
	$ide = false;
	for ($i=0; $i<count($codigos); $i++) {
		$sql = "SELECT sum(importe) FROM catalogo_gastos JOIN movimiento_gastos USING (codgastos) WHERE num_cia=$cia AND codgastos=".$codigos[$i]['codgastos']." AND fecha>='$fecha1' AND fecha<='$fecha2' AND codigo_edo_resultados=2";
		$result = ejecutar_script($sql,$dsn);
		$tpl->newBlock("fila_totales_gral");
		$tpl->assign("cod_total_gral",$codigos[$i]['codgastos']);
		
		// [05-Ago-2008] Sumar comisiones de codigo 78 al gasto 182
		if (mktime(0, 0, 0, $mes, 1, $anio) >= mktime(0, 0, 0, 7, 1, 2008) && $codigos[$i]['codgastos'] == 182 && $importe_2por_ide[0]['sum'] > 0) {
			$codigos[$i]['descripcion'] .= ' / 2% IDE';
			$result[0]['sum'] += $importe_2por_ide[0]['sum'];
			$imp_ide_ins = TRUE;
		}
		
		$tpl->assign("nombre_total_gral",$codigos[$i]['descripcion']);
		$tpl->assign("importe_total_gral",number_format($result[0]['sum'],2,".",","));
		$gran_total += $result[0]['sum'];
		$super_gran_total += $result[0]['sum'];
	}
	if (mktime(0, 0, 0, $mes, 1, $anio) >= mktime(0, 0, 0, 7, 1, 2008) && !$imp_ide_ins && $importe_2por_ide[0]['sum'] > 0) {
		$tpl->newBlock('fila_totales_gral');
		$tpl->assign('cod_total_gral', 182);
		$tpl->assign('nombre_total_gral', 'IMPUESTO EROGACIONES + 2% IDE');
		$tpl->assign('importe_total_gral', number_format($importe_2por_ide[0]['sum'], 2, '.', ','));
		
		$gran_total += $importe_2por_ide[0]['sum'];
		$super_gran_total += $importe_2por_ide[0]['sum'];
	}
	
	$tpl->gotoBlock("gastos_gral");
	$tpl->assign("gran_total_total",number_format($gran_total,2,".",","));
}

$tpl->gotoBlock("listado_x_cia_totales");
$tpl->assign("gran_total",number_format($super_gran_total,2,".",","));

// Gastos de oficina
$sql = "SELECT * FROM gastos_caja WHERE num_cia = $cia AND fecha >= '$fecha1' AND fecha <= '$fecha2' AND clave_balance = 'TRUE' ORDER BY fecha ASC";
$gastos_cia = ejecutar_script($sql,$dsn);
if ($gastos_cia) {
	$tpl->newBlock("listado_one");
	$total_egreso  = 0;
	$total_ingreso = 0;
	
	for ($j=0; $j<count($gastos_cia); $j++) {
		$tpl->newBlock("fila_one");
		$tpl->assign("fecha_one",$gastos_cia[$j]['fecha']);
		$concepto = obtener_registro("catalogo_gastos_caja",array("num_gasto"),array($gastos_cia[$j]['cod_gastos']),"","",$dsn);
		$tpl->assign("concepto_one",$concepto[0]['descripcion']);
		if ($gastos_cia[$j]['clave_balance'] == "t")
			$tpl->assign("afecta_one","AFECTA");
		
		if ($gastos_cia[$j]['tipo_mov'] == "f") {
			$tpl->assign("egreso_one",number_format($gastos_cia[$j]['importe'],2,".",","));
			$tpl->assign("ingreso_one","&nbsp;");
			$total_egreso      += $gastos_cia[$j]['importe'];
		}
		else if ($gastos_cia[$j]['tipo_mov'] == "t") {
			$tpl->assign("egreso_one","&nbsp;");
			$tpl->assign("ingreso_one",number_format($gastos_cia[$j]['importe'],2,".",","));
			$total_ingreso += $gastos_cia[$j]['importe'];
		}
	}
	$tpl->gotoBlock("listado_one");
	$tpl->assign("total_egreso_one",number_format($total_egreso,2,".",","));
	$tpl->assign("total_ingreso_one",number_format($total_ingreso,2,".",","));
	$tpl->assign("total_compania_one",number_format($total_egreso-$total_ingreso,2,".",","));
}

// Auxiliar de inventario
// Obtener saldos anteriores de hitorico_inventario
$fecha_historico = date("d/m/Y",mktime(0,0,0,$mes,0,$anio));

$sql  = "SELECT ";
$sql .= "num_cia,codmp,nombre,existencia,precio_unidad ";
$sql .= "FROM ";
$sql .= "historico_inventario ";
$sql .= "JOIN ";
$sql .= "catalogo_mat_primas ";
$sql .= "USING(codmp) ";
$sql .= "WHERE ";
$sql .= "num_cia = $cia ";
$sql .= "AND ";
$sql .= "fecha = '$fecha_historico' AND codmp != 90";
$sql .= "ORDER BY codmp";
$saldo_anterior = ejecutar_script($sql,$dsn);

// Obtener saldos de materias primas
$sql  = "SELECT ";
$sql .= "fecha,codmp,nombre,tipo_mov,cantidad,precio_unidad ";
$sql .= "FROM ";
$sql .= "mov_inv_real ";
$sql .= "JOIN ";
$sql .= "catalogo_mat_primas ";
$sql .= "USING(codmp) ";
$sql .= "WHERE ";
$sql .= "num_cia = $cia ";
$sql .= "AND ";
$sql .= "fecha >= '$fecha1' ";
$sql .= "AND ";
$sql .= "fecha <= '$fecha2' AND codmp != 90";
$sql .= "ORDER BY num_cia,codmp,fecha,tipo_mov";
$saldo = ejecutar_script($sql,$dsn);

$tpl->newBlock("listado_totales");

$total_valores_anteriores = 0;
$gran_total_valores_entrada = 0;
$gran_total_valores_salida = 0;
$gran_total_valores = 0;

for ($i=0; $i<count($saldo_anterior); $i++) {
	$valores_anteriores = $saldo_anterior[$i]['existencia']*$saldo_anterior[$i]['precio_unidad'];
	
	$unidades = $saldo_anterior[$i]['existencia'];
	$valores  = $saldo_anterior[$i]['existencia'] * $saldo_anterior[$i]['precio_unidad'];
	
	$total_unidades_entrada = 0;
	$total_valores_entrada = 0;
	$total_unidades_salida = 0;
	$total_valores_salida = 0;
	$costo_promedio = $saldo_anterior[$i]['precio_unidad'];
	$cantidad_anterior = 0;
	for ($j=0; $j<count($saldo); $j++) {
		if ($saldo[$j]['codmp'] == $saldo_anterior[$i]['codmp']) {
			// Salidas
			if ($saldo[$j]['tipo_mov'] == "t") {
				$unidades -= $saldo[$j]['cantidad'];
				$valores  -= $saldo[$j]['cantidad'] * $costo_promedio;
				$total_unidades_salida += $saldo[$j]['cantidad'];
				$total_valores_salida  += $saldo[$j]['cantidad'] * $costo_promedio;
				$cantidad_anterior = $unidades;
			}
			// Entradas
			else if ($saldo[$j]['tipo_mov'] == "f") {
				$unidades += $saldo[$j]['cantidad'];
				$valores  += $saldo[$j]['cantidad'] * $saldo[$j]['precio_unidad'];
				if ($cantidad_anterior < 0)
					$costo_promedio = $saldo[$j]['precio_unidad'];
				else
					@$costo_promedio = $valores / $unidades;
				$cantidad_anterior = $unidades;
				$total_unidades_entrada += $saldo[$j]['cantidad'];
				$total_valores_entrada  += $saldo[$j]['cantidad'] * $saldo[$j]['precio_unidad'];
			}
		}
	}
	if ($costo_promedio > 0) {
		$tpl->newBlock("mp_total");
		$tpl->assign("codmp_total",$saldo_anterior[$i]['codmp']);
		$tpl->assign("nombremp_total",$saldo_anterior[$i]['nombre']);
		$tpl->assign("unidades_anteriores_total",number_format($saldo_anterior[$i]['existencia'],2,".",","));
		$tpl->assign("valores_anteriores_total",number_format($valores_anteriores,2,".",","));
		$tpl->assign("costo_anterior_total",number_format($saldo_anterior[$i]['precio_unidad'],2,".",","));
		
		$tpl->assign("total_unidades_entrada_total",number_format($total_unidades_entrada,2,".",","));
		$tpl->assign("total_valores_entrada_total",number_format($total_valores_entrada,2,".",","));
		$tpl->assign("total_unidades_salida_total",number_format($total_unidades_salida,2,".",","));
		$tpl->assign("total_valores_salida_total",number_format($total_valores_salida,2,".",","));
		$tpl->assign("total_unidades_total",number_format($unidades,2,".",","));
		$tpl->assign("total_valores_total",number_format($valores,2,".",","));
		$tpl->assign("ultimo_costo_promedio_total",number_format($costo_promedio,2,".",","));
		
		$total_valores_anteriores += $valores_anteriores;
		$gran_total_valores_salida += $total_valores_salida;
		$gran_total_valores_entrada += $total_valores_entrada;
		$gran_total_valores += $valores;
	}
}
$tpl->gotoBlock("listado_totales");
$tpl->assign("total_valores_anteriores",number_format($total_valores_anteriores,2,".",","));
$tpl->assign("total_valores_entrada_total",number_format($gran_total_valores_entrada,2,".",","));
$tpl->assign("total_valores_salida_total",number_format($gran_total_valores_salida,2,".",","));
$tpl->assign("total_valores_total",number_format($gran_total_valores,2,".",","));

$tpl->gotoBlock("reporte");

// Ventas
/*if ($cia == 702) {
	$ventas = ejecutar_script("SELECT sum(venta_puerta) FROM total_panaderias WHERE num_cia = $cia AND fecha >= '$fecha1' AND fecha <= '$fecha2'",$dsn);
	$ventas_netas = ejecutar_script("SELECT sum(venta_puerta) FROM total_panaderias WHERE num_cia = $cia AND fecha >= '$fecha1' AND fecha <= '$fecha2'",$dsn);
}
else {*/
	$ventas = ejecutar_script("SELECT sum(venta) FROM total_companias WHERE num_cia = $cia AND fecha >= '$fecha1' AND fecha <= '$fecha2'",$dsn);
	// Ventas Netas
	$ventas_netas = ejecutar_script("SELECT sum(venta) FROM total_companias WHERE num_cia = $cia AND fecha >= '$fecha1' AND fecha <= '$fecha2'",$dsn);
	// [12-Feb-2007] Obtener ventas netas del año anterior para comparativo
	$ventas_ant = ejecutar_script("SELECT ventas_netas FROM balances_ros WHERE num_cia = $cia AND mes = $mes AND anio = " . ($anio - 1), $dsn);
/*}*/

// Otros
$otros = $ventas_netas[0]['sum'] - $ventas[0]['sum'];
// Inventario Anterior
if ($cia != 702)
	$inv_ant = ejecutar_script("SELECT sum(precio_unidad*existencia) FROM historico_inventario WHERE num_cia = $cia AND codmp != 90 AND fecha = '$fecha_historico'",$dsn);
else
	$inv_ant = ejecutar_script("SELECT inv_act AS sum FROM balances_ros WHERE num_cia = 702 AND mes = ".($mes==1?12:$mes-1)." AND anio = ".($mes==1?$anio-1:$anio),$dsn);
//$inv_ant[0]['sum'] = 5857.02;
// Compras
//$compras = ejecutar_script("SELECT sum(total_mov) FROM mov_inv_real WHERE num_cia = $cia AND fecha >= '$fecha1' AND fecha <= '$fecha2' AND tipo_mov='FALSE' AND codmp!=90",$dsn);
$otras_mercancias = ejecutar_script("SELECT sum(total) FROM compra_directa WHERE num_cia = $cia AND aplica_gasto = 'FALSE' AND fecha_mov >= '$fecha1' AND fecha_mov <= '$fecha2'",$dsn);
$compras = $gran_total_valores_entrada - $otras_mercancias[0]['sum'];
// Mercancias
$mercancias = ejecutar_script("SELECT sum(importe) FROM movimiento_gastos WHERE num_cia = $cia AND fecha >= '$fecha1' AND fecha <= '$fecha2' AND codgastos = 23",$dsn);
// Inventario Actual
$costentradas = ejecutar_script("SELECT sum(total_mov) FROM mov_inv_real WHERE num_cia = $cia AND fecha >= '$fecha1' AND fecha <= '$fecha2' AND tipo_mov='FALSE'",$dsn);
$costsalidas = ejecutar_script("SELECT sum(total_mov) FROM mov_inv_real WHERE num_cia = $cia AND fecha >= '$fecha1' AND fecha <= '$fecha2' AND tipo_mov='TRUE'",$dsn);
//$inv_act = ejecutar_script("SELECT sum(precio_unidad*existencia) FROM inventario_fin_mes WHERE num_cia = $cia",$dsn);
$tmp = ejecutar_script("SELECT importe FROM inv_act WHERE num_cia = $cia AND mes = $mes AND anio = $anio", $dsn);
$inv_act = $tmp ? $tmp[0]['importe'] : $gran_total_valores;

// Materia Prima Utilizada
$mat_prima_utilizada = $inv_ant[0]['sum'] + $compras/*[0]['sum']*/ + $mercancias[0]['sum'] - /*$inv_act[0]['sum']*//*$gran_total_valores*/$inv_act;

// Gastos de Fabricación
$sql = "SELECT sum (importe) FROM catalogo_gastos JOIN movimiento_gastos USING (codgastos) WHERE num_cia=$cia AND fecha>='$fecha1' AND fecha<='$fecha2' AND codigo_edo_resultados=1 GROUP BY num_cia";
$temp = ejecutar_script($sql,$dsn);
$gastos_fab = $temp[0]['sum'];

// Costos de Elaboración
$costo_elaboracion = $mat_prima_utilizada + $gastos_fab;

// Titulo 1
$titulo1 = $ventas_netas[0]['sum'] - $costo_elaboracion;

// Gastos Generales
$sql = "SELECT sum (importe) FROM catalogo_gastos JOIN movimiento_gastos USING (codgastos) WHERE num_cia=$cia AND fecha>='$fecha1' AND 
fecha<='$fecha2' AND codigo_edo_resultados=2" . (mktime(0, 0, 0, $mes, 1, $anio) >= mktime(0, 0, 0, 2, 1, 2007) ? ' AND codgastos NOT IN (140)' : '')/* . (mktime(0, 0, 0, $mes, 1, $anio) >= mktime(0, 0, 0, 4, 1, 2008) ? ' AND (num_cia IN (110, 148) AND codgastos NOT IN (141))' : '')*/ . " GROUP 
BY num_cia";
$temp = ejecutar_script($sql,$dsn);
$gastos_gral = -1 * $temp[0]['sum'];
// [4-Ago-2008] Quitar de comisiones bancarias el código 78 y sumarlo a los gastos generales
if (mktime(0, 0, 0, $mes, 1, $anio) >= mktime(0, 0, 0, 7, 1, 2008))
	$gastos_gral -= $importe_2por_ide[0]['sum'];

/**** [27-Mar-2007] Comisiones bancarias ****/
$comisiones = 0;
if (mktime(0, 0, 0, $mes, 1, $anio) >= mktime(0, 0, 0, 3, 1, 2007)) {
	$sql = "(SELECT tipo_mov, sum(importe) AS importe FROM estado_cuenta AS ec WHERE num_cia = $cia AND fecha BETWEEN '$fecha1' AND '$fecha2' AND cuenta = 1 AND cod_mov IN (SELECT cod_mov FROM";
	$sql .= " catalogo_mov_bancos WHERE entra_bal = 'TRUE' AND cod_mov NOT IN (78) GROUP BY cod_mov) GROUP BY tipo_mov) UNION (SELECT tipo_mov, sum(importe) AS importe FROM estado_cuenta AS ec WHERE num_cia = $cia";
	$sql .= " AND fecha BETWEEN '$fecha1' AND '$fecha2' AND cuenta = 2 AND cod_mov IN (SELECT cod_mov FROM catalogo_mov_santander WHERE entra_bal = 'TRUE' AND cod_mov NOT IN (78) GROUP BY cod_mov) GROUP BY";
	$sql .= " tipo_mov)";
	$result = ejecutar_script($sql,$dsn);
	
	if ($result)
		foreach ($result as $reg)
			$comisiones += $reg['tipo_mov'] == 'f' ? $reg['importe'] : -$reg['importe'];
}

// Gastos de Caja
$egresos = ejecutar_script("SELECT sum(importe) FROM gastos_caja WHERE num_cia=$cia AND fecha >= '$fecha1' AND fecha <= '$fecha2' AND tipo_mov = 'FALSE' AND clave_balance = 'TRUE'",$dsn);
$ingresos = ejecutar_script("SELECT sum(importe) FROM gastos_caja WHERE num_cia=$cia AND fecha >= '$fecha1' AND fecha <= '$fecha2' AND tipo_mov = 'TRUE' AND clave_balance = 'TRUE'",$dsn);
$gastos_caja = -$egresos[0]['sum'] + $ingresos[0]['sum'];

// Reservas
if ($result = ejecutar_script("SELECT sum(importe) AS importe FROM reservas_cias WHERE num_cia=$cia AND fecha='$fecha1'",$dsn))
	$reservas = -$result[0]['importe'];
else
	$reservas = 0;

// Gastos por otras compañías
//$cia_gasto = ejecutar_script("SELECT sum(monto) FROM gastos_otras_cia WHERE fecha >= '$fecha1' AND fecha <= '$fecha2'",$dsn);
//$cia_recibe = ejecutar_script("SELECT sum(monto) FROM gastos_otras_cia WHERE num_cia_ingreso=$cia AND fecha >= '$fecha1' AND fecha <= '$fecha2' AND tipo_mov='TRUE'",$dsn);
//$gastos_otros = /*$cia_gasto[0]['sum']*/0;

$cia_gasto_egreso = ejecutar_script("SELECT sum(monto) FROM gastos_otras_cia WHERE num_cia_egreso = $cia AND fecha BETWEEN '$fecha1' AND '$fecha2'",$dsn);
$cia_gasto_ingreso = ejecutar_script("SELECT sum(monto) FROM gastos_otras_cia WHERE num_cia_ingreso = $cia AND fecha BETWEEN '$fecha1' AND '$fecha2'",$dsn);
$gastos_otros = $cia_gasto_egreso[0]['sum'] - $cia_gasto_ingreso[0]['sum'];

// Gastos totales
$gastos_totales = $gastos_gral + $gastos_caja + $reservas + $gastos_otros + $comisiones;

// Ingresos extraordinarios
$ingresos_ext = 0;

// Utilidad Neta
$titulo2 = $gastos_totales + $ingresos_ext + $titulo1;
// [12-Feb-2007] Obtener ultilidad neta del año anterior para comparativo
$utilidad_neta_ant = ejecutar_script("SELECT utilidad_neta FROM balances_ros WHERE num_cia = $cia AND mes = $mes AND anio = " . ($anio - 1), $dsn);

// Insertar o actualizar hitorico de utilidades y ventas
if (existe_registro("historico",array("num_cia","mes","anio"),array($cia,$mes,$anio),$dsn))
	ejecutar_script("UPDATE historico SET utilidad=$titulo2,venta=".($ventas_netas[0]['sum'] > 0?$ventas_netas[0]['sum']:0)." WHERE num_cia=$cia AND mes=$mes AND anio=$anio",$dsn);
else
	ejecutar_script("INSERT INTO historico (num_cia,mes,anio,utilidad,venta) VALUES ($cia,$mes,$anio,$titulo2,".($ventas_netas[0]['sum'] > 0?$ventas_netas[0]['sum']:0).")",$dsn);


// Efectivo y porcentaje
if ($cia == 702)
	$efectivo = ejecutar_script("SELECT sum(efectivo) FROM total_panaderias WHERE num_cia=$cia AND fecha >= '$fecha1' AND fecha <= '$fecha2'",$dsn);
else
	$efectivo = ejecutar_script("SELECT sum(efectivo) FROM total_companias WHERE num_cia=$cia AND fecha >= '$fecha1' AND fecha <= '$fecha2'",$dsn);
@$porc_efectivo = $mat_prima_utilizada / $ventas_netas[0]['sum'];

// Pollos vendidos
$pollos = ejecutar_script("SELECT sum(unidades) FROM hoja_diaria_rost WHERE num_cia = $cia AND fecha >= '$fecha1' AND fecha <= '$fecha2' AND (codmp=160 OR codmp=600 OR codmp=700)",$dsn);
$pavo = ejecutar_script("SELECT sum(unidades) FROM hoja_diaria_rost WHERE num_cia = $cia AND fecha >= '$fecha1' AND fecha <= '$fecha2' AND codmp = 352",$dsn);

// Peso promedio de pollos normales
$peso_normal = ejecutar_script("SELECT sum(kilos)/sum(cantidad) AS res FROM fact_rosticeria WHERE num_cia = $cia AND codmp = 160 AND fecha_mov >= '$fecha1' AND fecha_mov <= '$fecha2'",$dsn);
// Peso promedio de pollos chicos
$peso_chico = ejecutar_script("SELECT sum(kilos)/sum(cantidad) AS res FROM fact_rosticeria WHERE num_cia = $cia AND codmp = 600 AND fecha_mov >= '$fecha1' AND fecha_mov <= '$fecha2'",$dsn);
// Peso promedio de pollos grande
$peso_grande = ejecutar_script("SELECT sum(kilos)/sum(cantidad) AS res FROM fact_rosticeria WHERE num_cia = $cia AND codmp = 700 AND fecha_mov >= '$fecha1' AND fecha_mov <= '$fecha2'",$dsn);

// Historico anterior
$historico_actual = ejecutar_script("SELECT * FROM historico WHERE num_cia = $cia AND anio = ".($anio)." AND mes <= $mes ORDER BY mes",$dsn);
$historico_anterior = ejecutar_script("SELECT * FROM historico WHERE num_cia = $cia AND anio = ".($anio-1)." ORDER BY mes",$dsn);

// [10-Ene-2008] Obtener reservas del mes
$aguinaldo = ejecutar_script("SELECT sum(importe) FROM reservas_cias WHERE num_cia = $cia AND fecha = '$fecha1' AND cod_reserva = 1", $dsn);
$imss = ejecutar_script("SELECT sum(importe) FROM reservas_cias WHERE num_cia = $cia AND fecha = '$fecha1' AND cod_reserva = 4", $dsn);

// Insertar datos en la tabla de balances
$bal['venta'] = $ventas[0]['sum'] != 0 ? $ventas[0]['sum'] : "0";
$bal['otros'] = $otros != 0 ? $otros : "0";
$bal['ventas_netas'] = $ventas_netas[0]['sum'];
$bal['inv_ant'] = $inv_ant[0]['sum'] != 0 ? $inv_ant[0]['sum'] : "0";
$bal['compras'] = $compras != 0 ? $compras : "0";
$bal['mercancias'] = $mercancias[0]['sum'] != 0 ? $mercancias[0]['sum'] : "0";
$bal['inv_act'] = $inv_act != 0 ? $inv_act : "0";
$bal['mat_prima_utilizada'] = $mat_prima_utilizada != 0 ? $mat_prima_utilizada : "0";
$bal['gastos_fab'] = $gastos_fab != 0 ? $gastos_fab : "0";
$bal['costo_produccion'] = $costo_elaboracion != 0 ? $costo_elaboracion : "0";
$bal['utilidad_bruta'] = $titulo1 != 0 ? $titulo1 : "0";
$bal['gastos_generales'] = $gastos_gral != 0 ? $gastos_gral : "0";
$bal['gastos_caja'] = $gastos_caja != 0 ? $gastos_caja : "0";
$bal['reserva_aguinaldos'] = $reservas != 0 ? $reservas : "0";
$bal['gastos_otras_cias'] = $gastos_otros != 0 ? $gastos_otros : "0";
$bal['total_gastos'] = $gastos_totales != 0 ? $gastos_totales : "0";
$bal['ingresos_ext'] = $ingresos_ext != 0 ? $ingresos_ext : "0";
$bal['utilidad_neta'] = $titulo2 != 0 ? $titulo2 : "0";
$bal['mp_vtas'] = $porc_efectivo != 0 ? $porc_efectivo : "0";
$bal['efectivo'] = $efectivo[0]['sum'] != 0 ? $efectivo[0]['sum'] : "0";
$bal['pollos_vendidos'] = $pollos[0]['sum'] != 0 ? $pollos[0]['sum'] : "0";
$bal['p_pavo'] = $pavo[0]['sum'] != 0 ? $pavo[0]['sum'] : "0";
$bal['num_cia'] = $cia;
ereg("([0-9]{1,2})/([0-9]{1,2})/([0-9]{2,4})",$fecha2,$temp_fecha);
$bal['mes'] = $temp_fecha[2];
$bal['anio'] = $temp_fecha[3];
$bal['comisiones'] = $comisiones != 0 ? $comisiones : '0';
$bal['peso_normal'] = 0;
$bal['peso_chico'] = 0;
$bal['peso_grande'] = 0;
$bal['fecha'] = date('d/m/Y', mktime(0, 0, 0, $mes, 1, $anio));

if (!existe_registro("balances_ros",array("num_cia","mes","anio"),array($cia,$temp_fecha[2],$temp_fecha[3]),$dsn)) {
	$db = new DBclass($dsn,"balances_ros",$bal);
	$db->generar_script_insert("");
	$db->ejecutar_script();
}
else {
	$db = new DBclass($dsn,"balances_ros",$bal);
	$db->generar_script_update("",array("num_cia","mes","anio"),array($cia,$temp_fecha[2],$temp_fecha[3]));
	$db->ejecutar_script();
}

//$nombre_cia = obtener_registro("catalogo_companias",array("num_cia"),array($cia),"","",$dsn);
$tpl->assign("num_cia",$cia);
$tpl->assign("nombre_cia",$nombre_cia[$f]['nombre']);
$tpl->assign("nombre_corto",$nombre_cia[$f]['nombre_corto']);
$tpl->assign("anio",$anio);
switch ($mes) {
	case 1: $tpl->assign("mes","ENERO");		break;
	case 2: $tpl->assign("mes","FEBRERO");		break;
	case 3: $tpl->assign("mes","MARZO");		break;
	case 4: $tpl->assign("mes","ABRIL");		break;
	case 5: $tpl->assign("mes","MAYO");			break;
	case 6: $tpl->assign("mes","JUNIO");		break;
	case 7: $tpl->assign("mes","JULIO");		break;
	case 8: $tpl->assign("mes","AGOSTO");		break;
	case 9: $tpl->assign("mes","SEPTIEMBRE");	break;
	case 10: $tpl->assign("mes","OCTUBRE");		break;
	case 11: $tpl->assign("mes","NOVIEMBRE");	break;
	case 12: $tpl->assign("mes","DICIEMBRE");	break;
}

if ($ventas[0]['sum'] > 0)
	$tpl->assign("ventas","<font color='#0000FF'>".number_format($ventas[0]['sum'],2,".",",")."</font>");
else
	$tpl->assign("ventas","<font color='#FF0000'>".number_format($ventas[0]['sum'],2,".",",")."</font>");

//$tpl->assign("iva","<font color='#0000FF'>".number_format($iva,2,".",",")."</font>");

//$tpl->assign("venta_total","<font color='#0000FF'>".number_format($ventas[0]['sum'],2,".",",")."</font>");

if ($otros > 0)
	$tpl->assign("otros","<font color='#0000FF'>".number_format($otros,2,".",",")."</font>");
else
	$tpl->assign("otros","<font color='#FF0000'>".number_format($otros,2,".",",")."</font>");
if ($ventas_netas[0]['sum'] > 0)
	$tpl->assign("ventas_netas","<font color='#0000FF'>".number_format($ventas_netas[0]['sum'],2,".",",")."</font>");
else
	$tpl->assign("ventas_netas","<font color='#FF0000'>".number_format($ventas_netas[0]['sum'],2,".",",")."</font>");

// [12-Feb-2007] Mostrar leyenda de comparativo de balance
$prom = isset($ventas_ant) && $ventas_ant[0]['ventas_netas'] != 0 ? abs(($ventas_netas[0]['sum'] * 100 / $ventas_ant[0]['ventas_netas']) - 100) : 0;
$tpl->assign('p_ventas', isset($ventas_ant) ? ' - <span style="font-size:8pt; color:#' . ($ventas_netas[0]['sum'] > $ventas_ant[0]['ventas_netas'] ? '0000CC;">SUBIO ' . number_format($prom, 2) : ($ventas_netas[0]['sum'] < $ventas_ant[0]['ventas_netas'] ? 'CC0000">BAJO ' . number_format($prom, 2) : '&nbsp;')) . '%</span>' : '&nbsp;');

if ($inv_ant[0]['sum'] > 0)
	$tpl->assign("inv_ant","<font color='#0000FF'>".number_format($inv_ant[0]['sum'],2,".",",")."</font>");
else
	$tpl->assign("inv_ant","<font color='#FF0000'>".number_format($inv_ant[0]['sum'],2,".",",")."</font>");

if (/*$compras[0]['sum'] > 0*/$gran_total_valores_entrada > 0)
	$tpl->assign("compras","<font color='#0000FF'>".number_format($compras,2,".",",")."</font>");
else
	$tpl->assign("compras","<font color='#FF0000'>".number_format($compras,2,".",",")."</font>");

if ($mercancias[0]['sum'] > 0)
	$tpl->assign("mercancias","<font color='#0000FF'>".number_format($mercancias[0]['sum'],2,".",",")."</font>");
else
	$tpl->assign("mercancias","<font color='#FF0000'>".number_format($mercancias[0]['sum'],2,".",","));

/*if ($inv_act[0]['sum'] > 0)
	$tpl->assign("inv_act","<font color='#0000FF'>".number_format($inv_act[0]['sum'],2,".",",")."</font>");
else
	$tpl->assign("inv_act","<font color='#FF0000'>".number_format($inv_act[0]['sum'],2,".",",")."</font>");*/

if ($gran_total_valores > 0)
	$tpl->assign("inv_act","<font color='#0000FF'>".number_format(/*$gran_total_valores*/$inv_act,2,".",",")."</font>");
else
	$tpl->assign("inv_act","<font color='#FF0000'>".number_format(/*$gran_total_valores*/$inv_act,2,".",",")."</font>");

if ($mat_prima_utilizada > 0)
	$tpl->assign("mat_pri_utilizada","<font color='#0000FF'>".number_format($mat_prima_utilizada,2,".",",")."</font>");
else
	$tpl->assign("mat_pri_utilizada","<font color='#FF0000'>".number_format($mat_prima_utilizada,2,".",",")."</font>");

if ($gastos_fab > 0)
	$tpl->assign("gastos_fab","<font color='#0000FF'>".number_format($gastos_fab,2,".",",")."</font>");
else
	$tpl->assign("gastos_fab","<font color='#FF0000'>".number_format($gastos_fab,2,".",",")."</font>");

if ($costo_elaboracion > 0)
	$tpl->assign("costo_elaboracion","<font color='#0000FF'>".number_format($costo_elaboracion,2,".",",")."</font>");
else
	$tpl->assign("costo_elaboracion","<font color='#FF0000'>".number_format($costo_elaboracion,2,".",",")."</font>");

if ($titulo1 > 0)
	$tpl->assign("titulo1","<font color='#0000FF'>".number_format($titulo1,2,".",",")."</font>");
else
	$tpl->assign("titulo1","<font color='#FF0000'>".number_format($titulo1,2,".",","));

if ($gastos_gral > 0)
	$tpl->assign("gastos_gral","<font color='#0000FF'>".number_format($gastos_gral,2,".",",")."</font>");
else
	$tpl->assign("gastos_gral","<font color='#FF0000'>".number_format($gastos_gral,2,".",",")."</font>");

// [27-Mar-2007]
$tpl->assign("comisiones", $comisiones != 0 ? "<font color=\"#" . ($comisiones > 0 ? "0000FF" : "FF0000") . "\">" . number_format($comisiones, 2, ".", ",") . "</font>" : "&nbsp;");

if ($gastos_caja > 0)
	$tpl->assign("gastos_caja","<font color='#0000FF'>".number_format($gastos_caja,2,".",",")."</font>");
else
	$tpl->assign("gastos_caja","<font color='#FF0000'>".number_format($gastos_caja,2,".",",")."</font>");

if ($reservas > 0)
	$tpl->assign("reservas","<font color='#0000FF'>".number_format($reservas,2,".",".")."</font>");
else
	$tpl->assign("reservas","<font color='#FF0000'>".number_format($reservas,2,".",".")."</font>");

if ($gastos_otros > 0)
	$tpl->assign("gastos_otras_cias","<font color='#0000FF'>".number_format($gastos_otros,2,".",",")."</font>");
else
	$tpl->assign("gastos_otras_cias","<font color='#FF0000'>".number_format($gastos_otros,2,".",",")."</font>");

if ($gastos_totales > 0)
	$tpl->assign("total_gastos","<font color='#0000FF'>".number_format($gastos_totales,2,".",",")."</font>");
else
	$tpl->assign("total_gastos","<font color='#FF0000'>".number_format($gastos_totales,2,".",",")."</font>");

if ($ingresos_ext > 0)
	$tpl->assign("ingresos_ext","<font color='#0000FF'>".number_format($ingresos_ext,2,".",",")."</font>");
else
	$tpl->assign("ingresos_ext","<font color='#FF0000'>".number_format($ingresos_ext,2,".",",")."</font>");

if ($titulo2 > 0)
	$tpl->assign("titulo2","<font color='#0000FF'>".number_format($titulo2,2,".",",")."</font>");
else
	$tpl->assign("titulo2","<font color='#FF0000'>".number_format($titulo2,2,".",",")."</font>");

// [12-Feb-2006] Mostrar leyenda de comparativo de balance
$prom = $utilidad_neta_ant && $utilidad_neta_ant[0]['utilidad_neta'] != 0 ? abs(($titulo2 * 100 / $utilidad_neta_ant[0]['utilidad_neta']) - 100) : 0;
$tpl->assign('p_utilidad', $utilidad_neta_ant ? ' - <span style="font-size:8pt; color:#' . ($titulo2 > $utilidad_neta_ant[0]['utilidad_neta'] ? '0000CC;">SUBIO ' . number_format($prom, 2) : ($titulo2 < $utilidad_neta_ant[0]['utilidad_neta'] ? 'CC0000">BAJO ' . number_format($prom, 2) : '&nbsp;')) . '%</span>' : '&nbsp;');

if ($efectivo[0]['sum'] > 0)
	$tpl->assign("efectivo","<font color='#0000FF'>".number_format($efectivo[0]['sum'],2,".",",")."</font>");
else
	$tpl->assign("efectivo","<font color='#FF0000'>".number_format($efectivo[0]['sum'],2,".",",")."</font>");

if ($porc_efectivo > 0)
	$tpl->assign("porc_efectivo","<font color='#0000FF'>".number_format($porc_efectivo,2,".",",")."</font>");
else
	$tpl->assign("porc_efectivo","<font color='#FF0000'>".number_format($porc_efectivo,2,".",",")."</font>");

if ($pollos[0]['sum'] > 0)
	$tpl->assign("pollos_vendidos","<font color='#0000FF'>".number_format($pollos[0]['sum'],0,"",",")."</font>");
else
	$tpl->assign("pollos_vendidos","<font color='#FF0000'>".number_format($pollos[0]['sum'],0,"",",")."</font>");

if ($pavo[0]['sum'] > 0)
	$tpl->assign("p_pavo","P.Pavo: <font color='#0000FF'>".number_format($pavo[0]['sum'],2,".",",")."</font>");

if ($peso_normal[0]['res'] > 0)
	$tpl->assign("peso_normal","Normales: <font color='#0000FF'>".number_format($peso_normal[0]['res'],3,".",",")."</font> Kgs");

if ($peso_chico[0]['res'] > 0)
	$tpl->assign("peso_chico","Chicos: <font color='#0000FF'>".number_format($peso_chico[0]['res'],3,".",",")."</font> Kgs");

if ($peso_grande[0]['res'] > 0)
	$tpl->assign("peso_grande","Grandes: <font color='#0000FF'>".number_format($peso_grande[0]['res'],3,".",",")."</font> Kgs");

$tpl->assign('aguinaldos', $aguinaldo[0]['sum'] > 0 ? number_format($aguinaldo[0]['sum'], 2, '.', ',') : '&nbsp;');
$tpl->assign('imss', $imss[0]['sum'] > 0 ? number_format($imss[0]['sum'], 2, '.', ',') : '&nbsp;');
$total_res = $aguinaldo[0]['sum'] + $imss[0]['sum'];
$tpl->assign('total_res', $total_res > 0 ? number_format($total_res, 2, '.', ',') : '&nbsp;');

// Estadisticas
$pollos_ene = ejecutar_script("SELECT sum(unidades) FROM hoja_diaria_rost WHERE num_cia = $cia AND fecha >= '01/01/$anio' AND fecha <= '31/01/$anio' AND (codmp=160 OR codmp=600 OR codmp=700)",$dsn);
$pollos_feb = ejecutar_script("SELECT sum(unidades) FROM hoja_diaria_rost WHERE num_cia = $cia AND fecha >= '01/02/$anio' AND fecha <= '".(($anio%4 == 0)?29:28)."/02/$anio' AND (codmp=160 OR codmp=600 OR codmp=700)",$dsn);
$pollos_mar = ejecutar_script("SELECT sum(unidades) FROM hoja_diaria_rost WHERE num_cia = $cia AND fecha >= '01/03/$anio' AND fecha <= '31/03/$anio' AND (codmp=160 OR codmp=600 OR codmp=700)",$dsn);
$pollos_abr = ejecutar_script("SELECT sum(unidades) FROM hoja_diaria_rost WHERE num_cia = $cia AND fecha >= '01/04/$anio' AND fecha <= '30/04/$anio' AND (codmp=160 OR codmp=600 OR codmp=700)",$dsn);
$pollos_may = ejecutar_script("SELECT sum(unidades) FROM hoja_diaria_rost WHERE num_cia = $cia AND fecha >= '01/05/$anio' AND fecha <= '31/05/$anio' AND (codmp=160 OR codmp=600 OR codmp=700)",$dsn);
$pollos_jun = ejecutar_script("SELECT sum(unidades) FROM hoja_diaria_rost WHERE num_cia = $cia AND fecha >= '01/06/$anio' AND fecha <= '30/06/$anio' AND (codmp=160 OR codmp=600 OR codmp=700)",$dsn);
$pollos_jul = ejecutar_script("SELECT sum(unidades) FROM hoja_diaria_rost WHERE num_cia = $cia AND fecha >= '01/07/$anio' AND fecha <= '31/07/$anio' AND (codmp=160 OR codmp=600 OR codmp=700)",$dsn);
$pollos_ago = ejecutar_script("SELECT sum(unidades) FROM hoja_diaria_rost WHERE num_cia = $cia AND fecha >= '01/08/$anio' AND fecha <= '31/08/$anio' AND (codmp=160 OR codmp=600 OR codmp=700)",$dsn);
$pollos_sep = ejecutar_script("SELECT sum(unidades) FROM hoja_diaria_rost WHERE num_cia = $cia AND fecha >= '01/09/$anio' AND fecha <= '30/09/$anio' AND (codmp=160 OR codmp=600 OR codmp=700)",$dsn);
$pollos_oct = ejecutar_script("SELECT sum(unidades) FROM hoja_diaria_rost WHERE num_cia = $cia AND fecha >= '01/10/$anio' AND fecha <= '31/10/$anio' AND (codmp=160 OR codmp=600 OR codmp=700)",$dsn);
$pollos_nov = ejecutar_script("SELECT sum(unidades) FROM hoja_diaria_rost WHERE num_cia = $cia AND fecha >= '01/11/$anio' AND fecha <= '30/11/$anio' AND (codmp=160 OR codmp=600 OR codmp=700)",$dsn);
$pollos_dic = ejecutar_script("SELECT sum(unidades) FROM hoja_diaria_rost WHERE num_cia = $cia AND fecha >= '01/12/$anio' AND fecha <= '31/12/$anio' AND (codmp=160 OR codmp=600 OR codmp=700)",$dsn);

if ($mes >= 1)
	$tpl->assign("pollos_ene",($pollos_ene[0]['sum'] != 0)?number_format($pollos_ene[0]['sum'],0,"",","):"&nbsp;");
if ($mes >= 2)
	$tpl->assign("pollos_feb",($pollos_feb[0]['sum'] != 0)?number_format($pollos_feb[0]['sum'],0,"",","):"&nbsp;");
if ($mes >= 3)
	$tpl->assign("pollos_mar",($pollos_mar[0]['sum'] != 0)?number_format($pollos_mar[0]['sum'],0,"",","):"&nbsp;");
if ($mes >= 4)
	$tpl->assign("pollos_abr",($pollos_abr[0]['sum'] != 0)?number_format($pollos_abr[0]['sum'],0,"",","):"&nbsp;");
if ($mes >= 5)
	$tpl->assign("pollos_may",($pollos_may[0]['sum'] != 0)?number_format($pollos_may[0]['sum'],0,"",","):"&nbsp;");
if ($mes >= 6)
	$tpl->assign("pollos_jun",($pollos_jun[0]['sum'] != 0)?number_format($pollos_jun[0]['sum'],0,"",","):"&nbsp;");
if ($mes >= 7)
	$tpl->assign("pollos_jul",($pollos_jul[0]['sum'] != 0)?number_format($pollos_jul[0]['sum'],0,"",","):"&nbsp;");
if ($mes >= 8)
	$tpl->assign("pollos_ago",($pollos_ago[0]['sum'] != 0)?number_format($pollos_ago[0]['sum'],0,"",","):"&nbsp;");
if ($mes >= 9)
	$tpl->assign("pollos_sep",($pollos_sep[0]['sum'] != 0)?number_format($pollos_sep[0]['sum'],0,"",","):"&nbsp;");
if ($mes >= 10)
	$tpl->assign("pollos_oct",($pollos_oct[0]['sum'] != 0)?number_format($pollos_oct[0]['sum'],0,"",","):"&nbsp;");
if ($mes >= 11)
	$tpl->assign("pollos_nov",($pollos_nov[0]['sum'] != 0)?number_format($pollos_nov[0]['sum'],0,"",","):"&nbsp;");
if ($mes >= 12)
	$tpl->assign("pollos_dic",($pollos_dic[0]['sum'] != 0)?number_format($pollos_dic[0]['sum'],0,"",","):"&nbsp;");
$tpl->assign("total_pollos",number_format($pollos_ene[0]['sum']+$pollos_feb[0]['sum']+$pollos_mar[0]['sum']+$pollos_abr[0]['sum']+$pollos_may[0]['sum']+$pollos_jun[0]['sum']+$pollos_jul[0]['sum']+$pollos_ago[0]['sum']+$pollos_sep[0]['sum']+$pollos_oct[0]['sum']+$pollos_nov[0]['sum']+$pollos_dic[0]['sum'],0,"",","));

// [06-Feb-2009] Guardar piezas para comparativo
switch ($mes) {
	case 1:
		$piezas_act = $pollos_ene[0]['sum'];
	break;
	case 2:
		$piezas_act = $pollos_feb[0]['sum'];
	break;
	case 3:
		$piezas_act = $pollos_mar[0]['sum'];
	break;
	case 4:
		$piezas_act = $pollos_abr[0]['sum'];
	break;
	case 5:
		$piezas_act = $pollos_may[0]['sum'];
	break;
	case 6:
		$piezas_act = $pollos_jun[0]['sum'];
	break;
	case 7:
		$piezas_act = $pollos_jul[0]['sum'];
	break;
	case 8:
		$piezas_act = $pollos_ago[0]['sum'];
	break;
	case 9:
		$piezas_act = $pollos_sep[0]['sum'];
	break;
	case 10:
		$piezas_act = $pollos_oct[0]['sum'];
	break;
	case 11:
		$piezas_act = $pollos_nov[0]['sum'];
	break;
	case 12:
		$piezas_act = $pollos_dic[0]['sum'];
	break;
	default:
		$piezas_act = 0;
}

/******************************************************************************************************************/
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
	$tpl->assign("tant_".$historico_anterior[$h]['mes'],$historico_anterior[$h]['utilidad'] != 0 ? mes_escrito($historico_anterior[$h]['mes']) : "");
	$tpl->assign("ant_".$historico_anterior[$h]['mes'],$historico_anterior[$h]['utilidad'] != 0 ? "<font color='#".($historico_anterior[$h]['utilidad'] > 0 ? "0000FF" : "FF0000")."'>".number_format($historico_anterior[$h]['utilidad'],2,".",",")."</font>" : "");
}

$vta = 0;
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
	$tpl->assign("tact_".$historico_actual[$h]['mes'],$historico_actual[$h]['utilidad'] != 0 ? mes_escrito($historico_actual[$h]['mes']) : "");
	$tpl->assign("act_".$historico_actual[$h]['mes'],$historico_actual[$h]['utilidad'] != 0 ? "<font color='#".($historico_actual[$h]['utilidad'] > 0 ? "0000FF" : "FF0000")."'>".number_format($historico_actual[$h]['utilidad'],2,".",",")."</font>" : "");
	$tpl->assign("ven_".$historico_actual[$h]['mes'],number_format($historico_actual[$h]['venta'],2,".",","));
	
	$vta += $historico_actual[$h]['venta'];
}
$tpl->assign("total_ventas",($vta > 0)?number_format($vta,2,".",","):"");
/******************************************************************************************************************/

/*****************************[12-Feb-2007] Estadisticas año anterior *********************************************/
$tpl->assign('anio_act', $anio);
$tpl->assign('anio_ant', $anio - 1);
$his_ant = ejecutar_script("SELECT mes, venta FROM historico WHERE num_cia = $cia AND anio = " . ($anio - 1) . " ORDER BY mes", $dsn);
$total = 0;
if ($his_ant)
	foreach ($his_ant as $reg) {
		$tpl->assign('ven_ant_' . $reg['mes'], $reg['venta'] != 0 ? number_format($reg['venta'], 2, '.', ',') : '&nbsp;');
		$total += $reg['venta'];
	}
$tpl->assign('total_ventas_ant', number_format($total, 2, '.', ','));

$fecha1_ant = '01/01/' . ($anio - 1);
$fecha2_ant = '31/12/' . ($anio - 1);
$sql = "SELECT extract(month from fecha) as mes, sum(unidades) AS unidades FROM hoja_diaria_rost WHERE num_cia = $cia AND fecha BETWEEN '$fecha1_ant' AND '$fecha2_ant'";
$sql .= " AND codmp IN (160, 600, 700) GROUP BY mes ORDER BY mes";
$pollos_ant = ejecutar_script($sql, $dsn);

$total = 0;
$piezas_ant = 0;
if ($pollos_ant)
	foreach ($pollos_ant as $reg) {
		$tpl->assign('pollo_' . $reg['mes'], $reg['unidades'] != 0 ? number_format($reg['unidades'], 2, '.', ',') : '&nbsp;');
		$total += $reg['unidades'];
		
		// [06-Feb-2009] Guardar piezas para comparativo
		if ($reg['mes'] == $mes)
			$piezas_ant = $reg['unidades'];
	}
$tpl->assign('total_pollos_ant', number_format($total, 2, '.', ','));

// [06-Feb-2009] Comparativo de piezas
$piezas_por = $piezas_ant > 0 ? abs($piezas_act * 100 / $piezas_ant - 100) : 0;
$tpl->assign('por_piezas', number_format($piezas_por, 2, '.', ','));
$tpl->assign('piezas_var', '<span style="color:#' . ($piezas_act > $piezas_ant ? '00C' : 'C00') . ';">' . ($piezas_act > $piezas_ant ? 'SUBIO' : 'BAJO') . '</span>');

// Si son todas las compañías, insertar un salto de pagina al final de cada una
if (isset($_GET['todas'])) {
	$tpl->newBlock("salto_pagina");
	
	/*if ($_GET['fin'] == 145) {
		$tpl->newBlock("siguiente");
		$tpl->assign("ini","146");
		$tpl->assign("fin","200");
		$tpl->assign("mes",$_GET['mes']);
	}
	else if ($_GET['ini'] == 146) {
		$tpl->newBlock("cerrar");
	}*/
}

unset($cia);
unset($ventas);
unset($ventas_netas);
unset($otros);
unset($inv_ant);
unset($compras);
unset($mercancias);
unset($costentradas);
unset($costsalidas);
unset($inv_act);
unset($mat_prima_utilizada);
unset($temp);
unset($gastos_fab);
unset($costo_elaboracion);
unset($titulo1);
unset($gastos_gral);
unset($egresos);
unset($ingresos);
unset($gastos_caja);
unset($reservas);
unset($cia_presta);
unset($cia_recibe);
unset($gastos_otros);
unset($gastos_totales);
unset($ingresos_ext);
unset($titulo2);
unset($efectivo);
unset($porc_efectivo);
unset($pollos);
unset($pavo);
unset($peso_normal);
unset($peso_chico);
unset($peso_grande);
unset($historico);
unset($codigos);
unset($gastos_cia);
unset($concepto);
unset($saldo_anterior);
unset($saldo);
}
$tpl->printToScreen();

?>
