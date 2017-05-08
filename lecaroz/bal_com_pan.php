<?php
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';
include './includes/class.TemplatePower.inc.php';

$db = new DBclass($dsn, "autocommit=yes");
$session = new sessionclass($dsn);

if (isset($_GET['mes'])) {
	// Obtener datos de balance
	if ($_GET['tipo'] == 1) {
		$sql = "SELECT * FROM balances_pan WHERE num_cia <= 300 AND mes = $_GET[mes] AND anio = $_GET[anio] AND produccion_total BETWEEN $_GET[produccion] * 0.80 AND $_GET[produccion] * 1.20 ORDER BY produccion_total DESC LIMIT 7";
		$hoja = 1;
	}
	else if ($_GET['tipo'] == 3) {
		$sql = "SELECT * FROM balances_pan WHERE num_cia <= 300 AND mes = $_GET[mes] AND anio = $_GET[anio] AND ventas_netas BETWEEN $_GET[ventas] * 0.80 AND $_GET[ventas] * 1.20 ORDER BY ventas_netas DESC LIMIT 7";
		$hoja = 1;
	}
	else if ($_GET['tipo'] == 4) {
		$sql = "SELECT * FROM balances_ros WHERE num_cia BETWEEN 301 AND 599 AND mes = $_GET[mes] AND anio = $_GET[anio] AND ventas_netas BETWEEN $_GET[ventas_ros] * 0.80 AND $_GET[ventas_ros] * 1.20 ORDER BY ventas_netas DESC LIMIT 7";
		$hoja = 2;
	}
	else {
		$num_cia = array();
		$ros_cia = array();
		foreach ($_GET['num_cia'] as $cia)
			if ($cia > 0 && $cia <= 300)
				$num_cia[] = $cia;
			else if ($cia > 300 && $cia < 600)
				$ros_cia[] = $cia;
		
		if (count($num_cia) > count($ros_cia)) {
			$sql = "SELECT * FROM balances_pan WHERE num_cia IN (";
			for ($i = 0; $i < count($num_cia); $i++)
				$sql .= $num_cia[$i] . ($i < count($num_cia) - 1 ? ", " : ")");
			$sql .= " AND mes = $_GET[mes] AND anio = $_GET[anio] ORDER BY produccion_total DESC";
			$hoja = 1;
		}
		else {
			$sql = "SELECT * FROM balances_ros WHERE num_cia IN (";
			for ($i = 0; $i < count($ros_cia); $i++)
				$sql .= $ros_cia[$i] . ($i < count($ros_cia) - 1 ? ", " : ")");
			$sql .= " AND mes = $_GET[mes] AND anio = $_GET[anio] ORDER BY ventas_netas DESC";
			$hoja = 2;
		}
	}
	$result = $db->query($sql);
	
	// Hacer un nuevo objeto TemplatePower
	$tpl = new TemplatePower( "./plantillas/bal/comparativo_bal.tpl" );
	$tpl->prepare();
	
	if (!$result) {
		$tpl->newBlock("cerrar");
		$tpl->printToScreen();
		die;
	}
	
	$tpl->newBlock($hoja == 1 ? "hoja_pan" : "hoja_ros");
	$tpl->assign("mes", mes_escrito($_GET['mes']));
	$tpl->assign("anio", $_GET['anio']);
	
	foreach ($result as $datos) {
		if ($hoja == 1) {
			$tpl->newBlock("col_pan");
			
			$nombre_cia = $db->query("SELECT nombre_corto FROM catalogo_companias WHERE num_cia = $datos[num_cia]");
			$tpl->assign("nombre_cia", $nombre_cia[0]['nombre_corto']);
			
			$tpl->assign("venta_puerta",(round($datos['venta_puerta'],2) != 0) ? "<font color=\"#".($datos['venta_puerta'] > 0 ? "0000FF" : "FF0000")."\">".number_format($datos['venta_puerta'],2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("bases",(round($datos['bases'],2) != 0) ? "<font color=\"#".($datos['bases'] > 0 ? "0000FF" : "FF0000")."\">".number_format($datos['bases'],2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("barredura",(round($datos['barredura'],2) != 0) ? "<font color=\"#".($datos['barredura'] > 0 ? "0000FF" : "FF0000")."\">".number_format($datos['barredura'],2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("pastillaje",(round($datos['pastillaje'],2) != 0) ? "<font color=\"#".($datos['pastillaje'] > 0 ? "0000FF" : "FF0000")."\">".number_format($datos['pastillaje'],2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("abono_emp",(round($datos['abono_emp'],2) != 0) ? "<font color=\"#".($datos['abono_emp'] > 0 ? "0000FF" : "FF0000")."\">".number_format($datos['abono_emp'],2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("otros",(round($datos['otros'],2) != 0) ? "<font color=\"#".($datos['otros'] > 0 ? "0000FF" : "FF0000")."\">".number_format($datos['otros'],2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("total_otros",(round($datos['total_otros'],2) != 0) ? "<font color=\"#".($datos['total_otros'] > 0 ? "0000FF" : "FF0000")."\">".number_format($datos['total_otros'],2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("abono_reparto",(round($datos['abono_reparto'],2) != 0) ? "<font color=\"#".($datos['abono_reparto'] > 0 ? "0000FF" : "FF0000")."\">".number_format($datos['abono_reparto'],2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("errores",(round($datos['errores'],2) != 0) ? "<font color=\"#".($datos['errores'] > 0 ? "0000FF" : "FF0000")."\">".number_format($datos['errores'],2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("ventas_netas",(round($datos['ventas_netas'],2) != 0) ? "<font color=\"#".($datos['ventas_netas'] > 0 ? "0000FF" : "FF0000")."\">".number_format($datos['ventas_netas'],2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("inventario_anterior",(round($datos['inv_ant'],2) != 0) ? "<font color=\"#".($datos['inv_ant'] > 0 ? "0000FF" : "FF0000")."\">".number_format($datos['inv_ant'],2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("compras",(round($datos['compras'],2) != 0) ? "<font color=\"#".($datos['compras'] > 0 ? "0000FF" : "FF0000")."\">".number_format($datos['compras'],2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("mercancias",(round($datos['mercancias'],2) != 0) ? "<font color=\"#".($datos['mercancias'] > 0 ? "0000FF" : "FF0000")."\">".number_format($datos['mercancias'],2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("inventario_actual",(round($datos['inv_act'],2) != 0) ? "<font color=\"#".($datos['inv_act'] > 0 ? "0000FF" : "FF0000")."\">".number_format($datos['inv_act'],2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("mat_prima_utilizada",(round($datos['mat_prima_utilizada'],2) != 0) ? "<font color=\"#".($datos['mat_prima_utilizada'] > 0 ? "0000FF" : "FF0000")."\">".number_format($datos['mat_prima_utilizada'],2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("mano_obra",(round($datos['mano_obra'],2) != 0) ? "<font color=\"#".($datos['mano_obra'] > 0 ? "0000FF" : "FF0000")."\">".number_format($datos['mano_obra'],2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("panaderos",(round($datos['panaderos'],2) != 0) ? "<font color=\"#".($datos['panaderos'] > 0 ? "0000FF" : "FF0000")."\">".number_format($datos['panaderos'],2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("gastos_fabricacion",(round($datos['gastos_fab'],2) != 0) ? "<font color=\"#".($datos['gastos_fab'] > 0 ? "0000FF" : "FF0000")."\">".number_format($datos['gastos_fab'],2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("costo_produccion",(round($datos['costo_produccion'],2) != 0) ? "<font color=\"#".($datos['costo_produccion'] > 0 ? "0000FF" : "FF0000")."\">".number_format($datos['costo_produccion'],2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("utilidad_bruta",(round($datos['utilidad_bruta'],2) != 0) ? "<font color=\"#".($datos['utilidad_bruta'] > 0 ? "0000FF" : "FF0000")."\">".number_format($datos['utilidad_bruta'],2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("pan_comprado",(round($datos['pan_comprado'],2) != 0) ? "<font color=\"#".($datos['pan_comprado'] > 0 ? "0000FF" : "FF0000")."\">".number_format($datos['pan_comprado'],2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("gastos_generales",(round($datos['gastos_generales'],2) != 0) ? "<font color=\"#".($datos['gastos_generales'] > 0 ? "0000FF" : "FF0000")."\">".number_format($datos['gastos_generales'],2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("gastos_caja",(round($datos['gastos_caja'],2) != 0) ? "<font color=\"#".($datos['gastos_caja'] > 0 ? "0000FF" : "FF0000")."\">".number_format($datos['gastos_caja'],2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("reserva_aguinaldos",(round($datos['reserva_aguinaldos'],2) != 0) ? "<font color=\"#".($datos['reserva_aguinaldos'] > 0 ? "0000FF" : "FF0000")."\">".number_format($datos['reserva_aguinaldos'],2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("gastos_otras_cias",(round($datos['gastos_otras_cias'],2) != 0) ? "<font color=\"#".($datos['gastos_otras_cias'] > 0 ? "0000FF" : "FF0000")."\">".number_format($datos['gastos_otras_cias'],2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("total_gastos",(round($datos['total_gastos'],2) != 0) ? "<font color=\"#".($datos['total_gastos'] > 0 ? "0000FF" : "FF0000")."\">".number_format($datos['total_gastos'],2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("ingresos_ext",(round($datos['ingresos_ext'],2) != 0) ? "<font color=\"#".($datos['ingresos_ext'] > 0 ? "0000FF" : "FF0000")."\">".number_format($datos['ingresos_ext'],2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("utilidad_mes",(round($datos['utilidad_neta'],2) != 0) ? "<font color=\"#".($datos['utilidad_neta'] > 0 ? "0000FF" : "FF0000")."\">".number_format($datos['utilidad_neta'],2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("mp_vtas",(round($datos['mp_vtas'],3) != 0) ? "<font color=\"#".($datos['mp_vtas'] > 0 ? "0000FF" : "FF0000")."\">".number_format($datos['mp_vtas'],3,".",",")."</font>" : "&nbsp;");
			$tpl->assign("utilidad_produccion",(round($datos['utilidad_pro'],2) != 0) ? "<font color=\"#".($datos['utilidad_pro'] > 0 ? "0000FF" : "FF0000")."\">".number_format($datos['utilidad_pro'],3,".",",")."</font>" : "&nbsp;");
			$tpl->assign("mp_produccion",(round($datos['mp_pro'],3) != 0) ? "<font color=\"#".($datos['mp_pro'] > 0 ? "0000FF" : "FF0000")."\">".number_format($datos['mp_pro'],3,".",",")."</font>" : "&nbsp;");
			$tpl->assign("produccion_total",(round($datos['produccion_total'],2) != 0) ? "<font color=\"#".($datos['produccion_total'] > 0 ? "0000FF" : "FF0000")."\">".number_format($datos['produccion_total'],2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("faltante_pan",(round($datos['faltante_pan'],2) != 0) ? "<font color=\"#".($datos['faltante_pan'] > 0 ? "0000FF" : "FF0000")."\">".number_format($datos['faltante_pan'],2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("rezago_inicial",(round($datos['rezago_ini'],2) != 0) ? "<font color=\"#".($datos['rezago_ini'] > 0 ? "0000FF" : "FF0000")."\">".number_format($datos['rezago_ini'],2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("rezago_final",(round($datos['rezago_fin'],2) != 0) ? "<font color=\"#".($datos['rezago_fin'] > 0 ? "0000FF" : "FF0000")."\">".number_format($datos['rezago_fin'],2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("subio_rezago",(round($datos['var_rezago'],2) != 0) ? "<font color=\"#".($datos['var_rezago'] > 0 ? "0000FF" : "FF0000")."\">".number_format($datos['var_rezago'],2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("efectivo",(round($datos['efectivo'],2) != 0) ? "<font color=\"#".($datos['efectivo'] > 0 ? "0000FF" : "FF0000")."\">".number_format($datos['efectivo'],2,".",",")."</font>" : "&nbsp;");
			
			$fecha1 = "1/$_GET[mes]/$_GET[anio]";
			$fecha2 = date("d/m/Y",mktime(0,0,0,$_GET['mes']+1,0,$_GET['anio']));
			// Obtener Gastos de caja (ingresos y egresos) del mes
			$sql = "SELECT SUM(importe) FROM gastos_caja WHERE num_cia = $datos[num_cia] AND fecha BETWEEN '$fecha1' AND '$fecha2' AND tipo_mov = 'FALSE'";
			$ingresos = $db->query($sql);
			$sql = "SELECT SUM(importe) FROM gastos_caja WHERE num_cia = $datos[num_cia] AND fecha BETWEEN '$fecha1' AND '$fecha2' AND tipo_mov = 'TRUE'";
			$egresos = $db->query($sql);
			$total_gastos_caja = $egresos[0]['sum'] - $ingresos[0]['sum'];
			$tpl->assign("ingresos",$ingresos[0]['sum'] > 0 ? number_format($ingresos[0]['sum'],2,".",",") : "&nbsp;");
			$tpl->assign("egresos",$egresos[0]['sum'] > 0 ? number_format($egresos[0]['sum'],2,".",",") : "&nbsp;");
			$tpl->assign("gastos_caja_total",(round($total_gastos_caja,2) != 0) ? "<font color=\"#".($total_gastos_caja > 0 ? "0000FF" : "FF0000")."\">".number_format($total_gastos_caja,2,".",",")."</font>" : "&nbsp;");
			// Obtener depositos
			$sql = "SELECT SUM(importe) FROM estado_cuenta WHERE num_cia = $datos[num_cia] AND fecha BETWEEN '$fecha1' AND '$fecha2' AND cod_mov = 1";
			$depositos = $db->query($sql);
			$tpl->assign("depositos",(round($depositos[0]['sum'],2) != 0) ? "<font color=\"#".($depositos[0]['sum'] > 0 ? "0000FF" : "FF0000")."\">".number_format($depositos[0]['sum'],2,".",",")."</font>" : "&nbsp;");
			// Otros depositos
			$sql = "SELECT SUM(importe) FROM otros_depositos WHERE num_cia = $datos[num_cia] AND fecha BETWEEN '$fecha1' AND '$fecha2'";
			$otros_dep = $db->query($sql);
			$tpl->assign("otros_depositos",(round($otros_dep[0]['sum'],2) != 0) ? "<font color=\"#".($otros_dep[0]['sum'] > 0 ? "0000FF" : "FF0000")."\">".number_format($otros_dep[0]['sum'],2,".",",")."</font>" : "&nbsp;");
			// General
			$general = $otros_dep[0]['sum'] + $total_gastos_caja;
			$tpl->assign("general",(round($general,2) != 0) ? "<font color=\"#".($general > 0 ? "0000FF" : "FF0000")."\">".number_format($general,2,".",",")."</font>" : "&nbsp;");
			// Diferencia
			$diferencia = $general - $datos['utilidad_neta'];
			$tpl->assign("diferencia",(round($diferencia,2) != 0) ? "<font color=\"#".($diferencia > 0 ? "0000FF" : "FF0000")."\">".number_format($diferencia,2,".",",")."</font>" : "&nbsp;");
		}
		else {
			$tpl->newBlock("col_ros");
			
			$nombre_cia = $db->query("SELECT nombre_corto FROM catalogo_companias WHERE num_cia = $datos[num_cia]");
			$tpl->assign("nombre_cia", $nombre_cia[0]['nombre_corto']);
			
			$tpl->assign("venta",(round($datos['venta'],2) != 0) ? "<font color=\"#".($datos['venta'] > 0 ? "0000FF" : "FF0000")."\">".number_format($datos['venta'],2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("otros",(round($datos['otros'],2) != 0) ? "<font color=\"#".($datos['otros'] > 0 ? "0000FF" : "FF0000")."\">".number_format($datos['otros'],2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("ventas_netas",(round($datos['ventas_netas'],2) != 0) ? "<font color=\"#".($datos['ventas_netas'] > 0 ? "0000FF" : "FF0000")."\">".number_format($datos['ventas_netas'],2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("inventario_anterior",(round($datos['inv_ant'],2) != 0) ? "<font color=\"#".($datos['inv_ant'] > 0 ? "0000FF" : "FF0000")."\">".number_format($datos['inv_ant'],2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("compras",(round($datos['compras'],2) != 0) ? "<font color=\"#".($datos['compras'] > 0 ? "0000FF" : "FF0000")."\">".number_format($datos['compras'],2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("mercancias",(round($datos['mercancias'],2) != 0) ? "<font color=\"#".($datos['mercancias'] > 0 ? "0000FF" : "FF0000")."\">".number_format($datos['mercancias'],2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("inventario_actual",(round($datos['inv_act'],2) != 0) ? "<font color=\"#".($datos['inv_act'] > 0 ? "0000FF" : "FF0000")."\">".number_format($datos['inv_act'],2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("mat_prima_utilizada",(round($datos['mat_prima_utilizada'],2) != 0) ? "<font color=\"#".($datos['mat_prima_utilizada'] > 0 ? "0000FF" : "FF0000")."\">".number_format($datos['mat_prima_utilizada'],2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("gastos_fabricacion",(round($datos['gastos_fab'],2) != 0) ? "<font color=\"#".($datos['gastos_fab'] > 0 ? "0000FF" : "FF0000")."\">".number_format($datos['gastos_fab'],2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("costo_produccion",(round($datos['costo_produccion'],2) != 0) ? "<font color=\"#".($datos['costo_produccion'] > 0 ? "0000FF" : "FF0000")."\">".number_format($datos['costo_produccion'],2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("utilidad_bruta",(round($datos['utilidad_bruta'],2) != 0) ? "<font color=\"#".($datos['utilidad_bruta'] > 0 ? "0000FF" : "FF0000")."\">".number_format($datos['utilidad_bruta'],2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("gastos_generales",(round($datos['gastos_generales'],2) != 0) ? "<font color=\"#".($datos['gastos_generales'] > 0 ? "0000FF" : "FF0000")."\">".number_format($datos['gastos_generales'],2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("gastos_caja",(round($datos['gastos_caja'],2) != 0) ? "<font color=\"#".($datos['gastos_caja'] > 0 ? "0000FF" : "FF0000")."\">".number_format($datos['gastos_caja'],2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("reserva_aguinaldos",(round($datos['reserva_aguinaldos'],2) != 0) ? "<font color=\"#".($datos['reserva_aguinaldos'] > 0 ? "0000FF" : "FF0000")."\">".number_format($datos['reserva_aguinaldos'],2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("gastos_otras_cias",(round($datos['gastos_otras_cias'],2) != 0) ? "<font color=\"#".($datos['gastos_otras_cias'] > 0 ? "0000FF" : "FF0000")."\">".number_format($datos['gastos_otras_cias'],2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("total_gastos",(round($datos['total_gastos'],2) != 0) ? "<font color=\"#".($datos['total_gastos'] > 0 ? "0000FF" : "FF0000")."\">".number_format($datos['total_gastos'],2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("ingresos_ext",(round($datos['ingresos_ext'],2) != 0) ? "<font color=\"#".($datos['ingresos_ext'] > 0 ? "0000FF" : "FF0000")."\">".number_format($datos['ingresos_ext'],2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("utilidad_mes",(round($datos['utilidad_neta'],2) != 0) ? "<font color=\"#".($datos['utilidad_neta'] > 0 ? "0000FF" : "FF0000")."\">".number_format($datos['utilidad_neta'],2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("mp_vtas",(round($datos['mp_vtas'],3) != 0) ? "<font color=\"#".($datos['mp_vtas'] > 0 ? "0000FF" : "FF0000")."\">".number_format($datos['mp_vtas'],3,".",",")."</font>" : "&nbsp;");
			$tpl->assign("efectivo",(round($datos['efectivo'],2) != 0) ? "<font color=\"#".($datos['efectivo'] > 0 ? "0000FF" : "FF0000")."\">".number_format($datos['efectivo'],2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("pollos_vendidos",(round($datos['pollos_vendidos'],2) != 0) ? "<font color=\"#".($datos['pollos_vendidos'] > 0 ? "0000FF" : "FF0000")."\">".number_format($datos['pollos_vendidos'],2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("p_pavo",(round($datos['p_pavo'],2) != 0) ? "<font color=\"#".($datos['p_pavo'] > 0 ? "0000FF" : "FF0000")."\">".number_format($datos['p_pavo'],2,".",",")."</font>" : "&nbsp;");
		}
	}
	
	$tpl->printToScreen();
	die;
}

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/bal/bal_com_pan.tpl");
$tpl->prepare();

//Seleccionar el script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

$tpl->assign(date("n", mktime(0, 0, 0, date("m"), 0, date("Y"))), "selected");
$tpl->assign("anio", date("Y", mktime(0, 0, 0, date("m"), 0, date("Y"))));

$tpl->printToScreen();
?>