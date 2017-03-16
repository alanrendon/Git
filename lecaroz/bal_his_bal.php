<?php
// HISTORICO DE BALANCES
// Tabla 'catalogo_trabajadores'
// Menu Proveedores y facturas -> Trabajadores

//define ('IDSCREEN',3311); //ID de pantalla


// --------------------------------- INCLUDES ----------------------------------------------------------------
include 'DB.php';
include './includes/class.db3.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';
include './includes/class.TemplatePower.inc.php';

// --------------------------------- Descripcion de errores --------------------------------------------------
$descripcion_error[1] = "No hay resultados";

// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);

// --------------------------------- Validar acceso de usuario a la pantalla ---------------------------------
//$session->validar_pantalla(IDSCREEN);

// --------------------------------- Obtener información de la pantalla --------------------------------------
//$session->info_pantalla();

// --------------------------------- Funciones ---------------------------------------------------------------

// --------------------------------- Generar pantalla --------------------------------------------------------
if (isset($_GET['anio'])) {
	$numcols_x_hoja = 7;
	
	$tpl = new TemplatePower( "./plantillas/bal/historico_bal.tpl" );
	$tpl->prepare();
	
	$cias = array();
	foreach ($_GET['num_cia'] as $cia)
		if ($cia > 0)
			$cias[] = $cia;
	
	// Obtener datos de balance
	if (count($cias) > 0)
		$sql = "SELECT * FROM ".($cias[0] <= 300 || $cias[0] == 703 ? "balances_pan" : "balances_ros")." LEFT JOIN catalogo_companias USING (num_cia) WHERE anio = $_GET[anio] AND num_cia IN (" . implode(', ', $cias) . ")" . ($_GET['idadmin'] > 0 ? " AND idadministrador = $_GET[idadmin]" : '') . " ORDER BY num_cia, mes";
	else
		$sql = "SELECT * FROM ".($_GET['rango'] == "pan" ? "balances_pan" : "balances_ros")." LEFT JOIN catalogo_companias USING (num_cia) WHERE anio = $_GET[anio]" . ($_GET['idadmin'] > 0 ? " AND idadministrador = $_GET[idadmin]" : '') . " ORDER BY num_cia, mes";
	$result = ejecutar_script($sql,$dsn);
	
	if (!$result) {
		$tpl->newBlock("cerrar");
		$tpl->printToScreen();
		die;
	}
	
	$num_cia = NULL;
	for ($i=0; $i<count($result); $i++) {
		if ($num_cia != $result[$i]['num_cia']) {
			if ($num_cia != NULL) {
				if ($numcols == $numcols_x_hoja) {
					$tpl->newBlock($num_cia <= 300 || $num_cia == 703 ? "hoja_pan" : "hoja_ros");
					$tpl->assign("num_cia",$num_cia);
					$tpl->assign("nombre_cia",$nombre[0]['nombre']);
					$tpl->assign("anio",$_GET['anio']);
					
					$numcols = 0;
				}
				
				// COLUMNA DE TOTALES
				$tpl->newBlock($num_cia <= 300 || $num_cia == 703 ? "col_pan" : "col_ros");
				$tpl->assign("mes","Total");
				// PANADERIAS
				if ($num_cia <= 300 || $num_cia == 703) {
					$tpl->assign("venta_puerta",(round($venta_puerta,2) != 0) ? "<font color=\"#".($venta_puerta > 0 ? "0000FF" : "FF0000")."\">".number_format($venta_puerta,2,".",",")."</font>" : "&nbsp;");
					$tpl->assign("bases",(round($bases,2) != 0) ? "<font color=\"#".($bases > 0 ? "0000FF" : "FF0000")."\">".number_format($bases,2,".",",")."</font>" : "&nbsp;");
					$tpl->assign("barredura",(round($barredura,2) != 0) ? "<font color=\"#".($barredura > 0 ? "0000FF" : "FF0000")."\">".number_format($barredura,2,".",",")."</font>" : "&nbsp;");
					$tpl->assign("pastillaje",(round($pastillaje,2) != 0) ? "<font color=\"#".($pastillaje > 0 ? "0000FF" : "FF0000")."\">".number_format($pastillaje,2,".",",")."</font>" : "&nbsp;");
					$tpl->assign("abono_emp",(round($abono_emp,2) != 0) ? "<font color=\"#".($abono_emp > 0 ? "0000FF" : "FF0000")."\">".number_format($abono_emp,2,".",",")."</font>" : "&nbsp;");
					$tpl->assign("otros",(round($otros,2) != 0) ? "<font color=\"#".($otros > 0 ? "0000FF" : "FF0000")."\">".number_format($otros,2,".",",")."</font>" : "&nbsp;");
					$tpl->assign("total_otros",(round($total_otros,2) != 0) ? "<font color=\"#".($total_otros > 0 ? "0000FF" : "FF0000")."\">".number_format($total_otros,2,".",",")."</font>" : "&nbsp;");
					$tpl->assign("abono_reparto",(round($abono_reparto,2) != 0) ? "<font color=\"#".($abono_reparto > 0 ? "0000FF" : "FF0000")."\">".number_format($abono_reparto,2,".",",")."</font>" : "&nbsp;");
					$tpl->assign("errores",(round($errores,2) != 0) ? "<font color=\"#".($errores > 0 ? "0000FF" : "FF0000")."\">".number_format($errores,2,".",",")."</font>" : "&nbsp;");
					$tpl->assign("ventas_netas",(round($ventas_netas,2) != 0) ? "<font color=\"#".($ventas_netas > 0 ? "0000FF" : "FF0000")."\">".number_format($ventas_netas,2,".",",")."</font>" : "&nbsp;");
					$tpl->assign("inventario_anterior",(round($inv_ant,2) != 0) ? "<font color=\"#".($inv_ant > 0 ? "0000FF" : "FF0000")."\">".number_format($inv_ant,2,".",",")."</font>" : "&nbsp;");
					$tpl->assign("compras",(round($compras,2) != 0) ? "<font color=\"#".($compras > 0 ? "0000FF" : "FF0000")."\">".number_format($compras,2,".",",")."</font>" : "&nbsp;");
					$tpl->assign("mercancias",(round($mercancias,2) != 0) ? "<font color=\"#".($mercancias > 0 ? "0000FF" : "FF0000")."\">".number_format($mercancias,2,".",",")."</font>" : "&nbsp;");
					$tpl->assign("inventario_actual",(round($inv_act,2) != 0) ? "<font color=\"#".($inv_act > 0 ? "0000FF" : "FF0000")."\">".number_format($inv_act,2,".",",")."</font>" : "&nbsp;");
					$tpl->assign("mat_prima_utilizada",(round($mat_prima_utilizada,2) != 0) ? "<font color=\"#".($mat_prima_utilizada > 0 ? "0000FF" : "FF0000")."\">".number_format($mat_prima_utilizada,2,".",",")."</font>" : "&nbsp;");
					$tpl->assign("mano_obra",(round($mano_obra,2) != 0) ? "<font color=\"#".($mano_obra > 0 ? "0000FF" : "FF0000")."\">".number_format($mano_obra,2,".",",")."</font>" : "&nbsp;");
					$tpl->assign("panaderos",(round($panaderos,2) != 0) ? "<font color=\"#".($panaderos > 0 ? "0000FF" : "FF0000")."\">".number_format($panaderos,2,".",",")."</font>" : "&nbsp;");
					$tpl->assign("gastos_fabricacion",(round($gastos_fab,2) != 0) ? "<font color=\"#".($gastos_fab > 0 ? "0000FF" : "FF0000")."\">".number_format($gastos_fab,2,".",",")."</font>" : "&nbsp;");
					$tpl->assign("costo_produccion",(round($costo_produccion,2) != 0) ? "<font color=\"#".($costo_produccion > 0 ? "0000FF" : "FF0000")."\">".number_format($costo_produccion,2,".",",")."</font>" : "&nbsp;");
					$tpl->assign("utilidad_bruta",(round($utilidad_bruta,2) != 0) ? "<font color=\"#".($utilidad_bruta > 0 ? "0000FF" : "FF0000")."\">".number_format($utilidad_bruta,2,".",",")."</font>" : "&nbsp;");
					$tpl->assign("pan_comprado",(round($pan_comprado,2) != 0) ? "<font color=\"#".($pan_comprado > 0 ? "0000FF" : "FF0000")."\">".number_format($pan_comprado,2,".",",")."</font>" : "&nbsp;");
					$tpl->assign("gastos_generales",(round($gastos_generales,2) != 0) ? "<font color=\"#".($gastos_generales > 0 ? "0000FF" : "FF0000")."\">".number_format($gastos_generales,2,".",",")."</font>" : "&nbsp;");
					$tpl->assign("gastos_caja",(round($gastos_caja,2) != 0) ? "<font color=\"#".($gastos_caja > 0 ? "0000FF" : "FF0000")."\">".number_format($gastos_caja,2,".",",")."</font>" : "&nbsp;");
					$tpl->assign("reserva_aguinaldos",(round($reserva_aguinaldos,2) != 0) ? "<font color=\"#".($reserva_aguinaldos > 0 ? "0000FF" : "FF0000")."\">".number_format($reserva_aguinaldos,2,".",",")."</font>" : "&nbsp;");
					$tpl->assign("gastos_otras_cias",(round($gastos_otras_cias,2) != 0) ? "<font color=\"#".($gastos_otras_cias > 0 ? "0000FF" : "FF0000")."\">".number_format($gastos_otras_cias,2,".",",")."</font>" : "&nbsp;");
					$tpl->assign("total_gastos",(round($total_gastos,2) != 0) ? "<font color=\"#".($total_gastos > 0 ? "0000FF" : "FF0000")."\">".number_format($total_gastos,2,".",",")."</font>" : "&nbsp;");
					$tpl->assign("ingresos_ext",(round($ingresos_ext,2) != 0) ? "<font color=\"#".($ingresos_ext > 0 ? "0000FF" : "FF0000")."\">".number_format($ingresos_ext,2,".",",")."</font>" : "&nbsp;");
					$tpl->assign("utilidad_mes",(round($utilidad_neta,2) != 0) ? "<font color=\"#".($utilidad_neta > 0 ? "0000FF" : "FF0000")."\">".number_format($utilidad_neta,2,".",",")."</font>" : "&nbsp;");
					$tpl->assign("mp_vtas",(round($mp_vtas,3) != 0) ? "<font color=\"#".($mp_vtas > 0 ? "0000FF" : "FF0000")."\">".number_format($mp_vtas,3,".",",")."</font>" : "&nbsp;");
					$tpl->assign("utilidad_produccion",(round($utilidad_pro,2) != 0) ? "<font color=\"#".($utilidad_pro > 0 ? "0000FF" : "FF0000")."\">".number_format($utilidad_pro,3,".",",")."</font>" : "&nbsp;");
					$tpl->assign("mp_produccion",(round($mp_pro,3) != 0) ? "<font color=\"#".($mp_pro > 0 ? "0000FF" : "FF0000")."\">".number_format($mp_pro,3,".",",")."</font>" : "&nbsp;");
					$tpl->assign("produccion_total",(round($produccion_total,2) != 0) ? "<font color=\"#".($produccion_total > 0 ? "0000FF" : "FF0000")."\">".number_format($produccion_total,2,".",",")."</font>" : "&nbsp;");
					$tpl->assign("faltante_pan",(round($faltante_pan,2) != 0) ? "<font color=\"#".($faltante_pan > 0 ? "0000FF" : "FF0000")."\">".number_format($faltante_pan,2,".",",")."</font>" : "&nbsp;");
					$tpl->assign("rezago_inicial",(round($rezago_ini,2) != 0) ? "<font color=\"#".($rezago_ini > 0 ? "0000FF" : "FF0000")."\">".number_format($rezago_ini,2,".",",")."</font>" : "&nbsp;");
					$tpl->assign("rezago_final",(round($rezago_fin,2) != 0) ? "<font color=\"#".($rezago_fin > 0 ? "0000FF" : "FF0000")."\">".number_format($rezago_fin,2,".",",")."</font>" : "&nbsp;");
					$tpl->assign("subio_rezago",(round($var_rezago,2) != 0) ? "<font color=\"#".($var_rezago > 0 ? "0000FF" : "FF0000")."\">".number_format($var_rezago,2,".",",")."</font>" : "&nbsp;");
					$tpl->assign("efectivo",(round($efectivo,2) != 0) ? "<font color=\"#".($efectivo > 0 ? "0000FF" : "FF0000")."\">".number_format($efectivo,2,".",",")."</font>" : "&nbsp;");
					
					$tpl->assign("ingresos",(round($ing,2) != 0) ? "<font color=\"#".($ing > 0 ? "0000FF" : "FF0000")."\">".number_format($ing,2,".",",")."</font>" : "&nbsp;");
					$tpl->assign("egresos",(round($egr,2) != 0) ? "<font color=\"#".($egr > 0 ? "0000FF" : "FF0000")."\">".number_format($egr,2,".",",")."</font>" : "&nbsp;");
					$tpl->assign("gastos_caja_total",(round($total_caja,2) != 0) ? "<font color=\"#".($total_caja > 0 ? "0000FF" : "FF0000")."\">".number_format($total_caja,2,".",",")."</font>" : "&nbsp;");
					$tpl->assign("depositos",(round($dep,2) != 0) ? "<font color=\"#".($dep > 0 ? "0000FF" : "FF0000")."\">".number_format($dep,2,".",",")."</font>" : "&nbsp;");
					$tpl->assign("otros_depositos",(round($odep,2) != 0) ? "<font color=\"#".($odep > 0 ? "0000FF" : "FF0000")."\">".number_format($odep,2,".",",")."</font>" : "&nbsp;");
					$tpl->assign("general",(round($gen,2) != 0) ? "<font color=\"#".($gen > 0 ? "0000FF" : "FF0000")."\">".number_format($gen,2,".",",")."</font>" : "&nbsp;");
					$tpl->assign("diferencia",(round($dif,2) != 0) ? "<font color=\"#".($dif > 0 ? "0000FF" : "FF0000")."\">".number_format($dif,2,".",",")."</font>" : "&nbsp;");
					
					$tpl->assign('saldo_ini', $sal_ini != 0 ? number_format($sal_ini, 2, '.', ',') : '&nbsp;');
					$tpl->assign('saldo_fin', $sal_fin != 0 ? number_format($sal_fin, 2, '.', ',') : '&nbsp;');
					$tpl->assign('saldo_pro_ini', $sal_pro_ini != 0 ? number_format($sal_pro_ini, 2, '.', ',') : '&nbsp;');
					$tpl->assign('saldo_pro_fin', $sal_pro_fin != 0 ? number_format($sal_pro_fin, 2, '.', ',') : '&nbsp;');
					$tpl->assign('no_inc', $gas_no_inc != 0 ? number_format($gas_no_inc, 2, '.', ',') : '&nbsp;');
					$tpl->assign('dif', $total_otra_dif != 0 ? number_format($total_otra_dif, 2, '.', ',') : '&nbsp;');
					
				}
				// ROSTICERIAS
				else {
					$tpl->assign("venta",(round($venta,2) != 0) ? "<font color=\"#".($venta > 0 ? "0000FF" : "FF0000")."\">".number_format($venta,2,".",",")."</font>" : "&nbsp;");
					$tpl->assign("otros",(round($otros,2) != 0) ? "<font color=\"#".($otros > 0 ? "0000FF" : "FF0000")."\">".number_format($otros,2,".",",")."</font>" : "&nbsp;");
					$tpl->assign("ventas_netas",(round($ventas_netas,2) != 0) ? "<font color=\"#".($ventas_netas > 0 ? "0000FF" : "FF0000")."\">".number_format($ventas_netas,2,".",",")."</font>" : "&nbsp;");
					$tpl->assign("inventario_anterior",(round($inv_ant,2) != 0) ? "<font color=\"#".($inv_ant > 0 ? "0000FF" : "FF0000")."\">".number_format($inv_ant,2,".",",")."</font>" : "&nbsp;");
					$tpl->assign("compras",(round($compras,2) != 0) ? "<font color=\"#".($compras > 0 ? "0000FF" : "FF0000")."\">".number_format($compras,2,".",",")."</font>" : "&nbsp;");
					$tpl->assign("mercancias",(round($mercancias,2) != 0) ? "<font color=\"#".($mercancias > 0 ? "0000FF" : "FF0000")."\">".number_format($mercancias,2,".",",")."</font>" : "&nbsp;");
					$tpl->assign("inventario_actual",(round($inv_act,2) != 0) ? "<font color=\"#".($inv_act > 0 ? "0000FF" : "FF0000")."\">".number_format($inv_act,2,".",",")."</font>" : "&nbsp;");
					$tpl->assign("mat_prima_utilizada",(round($mat_prima_utilizada,2) != 0) ? "<font color=\"#".($mat_prima_utilizada > 0 ? "0000FF" : "FF0000")."\">".number_format($mat_prima_utilizada,2,".",",")."</font>" : "&nbsp;");
					$tpl->assign("gastos_fabricacion",(round($gastos_fab,2) != 0) ? "<font color=\"#".($gastos_fab > 0 ? "0000FF" : "FF0000")."\">".number_format($gastos_fab,2,".",",")."</font>" : "&nbsp;");
					$tpl->assign("costo_produccion",(round($costo_produccion,2) != 0) ? "<font color=\"#".($costo_produccion > 0 ? "0000FF" : "FF0000")."\">".number_format($costo_produccion,2,".",",")."</font>" : "&nbsp;");
					$tpl->assign("utilidad_bruta",(round($utilidad_bruta,2) != 0) ? "<font color=\"#".($utilidad_bruta > 0 ? "0000FF" : "FF0000")."\">".number_format($utilidad_bruta,2,".",",")."</font>" : "&nbsp;");
					$tpl->assign("gastos_generales",(round($gastos_generales,2) != 0) ? "<font color=\"#".($gastos_generales > 0 ? "0000FF" : "FF0000")."\">".number_format($gastos_generales,2,".",",")."</font>" : "&nbsp;");
					$tpl->assign("gastos_caja",(round($gastos_caja,2) != 0) ? "<font color=\"#".($gastos_caja > 0 ? "0000FF" : "FF0000")."\">".number_format($gastos_caja,2,".",",")."</font>" : "&nbsp;");
					$tpl->assign("reserva_aguinaldos",(round($reserva_aguinaldos,2) != 0) ? "<font color=\"#".($reserva_aguinaldos > 0 ? "0000FF" : "FF0000")."\">".number_format($reserva_aguinaldos,2,".",",")."</font>" : "&nbsp;");
					$tpl->assign("gastos_otras_cias",(round($gastos_otras_cias,2) != 0) ? "<font color=\"#".($gastos_otras_cias > 0 ? "0000FF" : "FF0000")."\">".number_format($gastos_otras_cias,2,".",",")."</font>" : "&nbsp;");
					$tpl->assign("total_gastos",(round($total_gastos,2) != 0) ? "<font color=\"#".($total_gastos > 0 ? "0000FF" : "FF0000")."\">".number_format($total_gastos,2,".",",")."</font>" : "&nbsp;");
					$tpl->assign("ingresos_ext",(round($ingresos_ext,2) != 0) ? "<font color=\"#".($ingresos_ext > 0 ? "0000FF" : "FF0000")."\">".number_format($ingresos_ext,2,".",",")."</font>" : "&nbsp;");
					$tpl->assign("utilidad_mes",(round($utilidad_neta,2) != 0) ? "<font color=\"#".($utilidad_neta > 0 ? "0000FF" : "FF0000")."\">".number_format($utilidad_neta,2,".",",")."</font>" : "&nbsp;");
					$tpl->assign("mp_vtas",(round($mp_vtas,2) != 0) ? "<font color=\"#".($mp_vtas > 0 ? "0000FF" : "FF0000")."\">".number_format($mp_vtas,2,".",",")."</font>" : "&nbsp;");
					$tpl->assign("efectivo",(round($efectivo,2) != 0) ? "<font color=\"#".($efectivo > 0 ? "0000FF" : "FF0000")."\">".number_format($efectivo,2,".",",")."</font>" : "&nbsp;");
					$tpl->assign("pollos_vendidos",(round($pollos_vendidos,2) != 0) ? "<font color=\"#".($pollos_vendidos > 0 ? "0000FF" : "FF0000")."\">".number_format($pollos_vendidos,2,".",",")."</font>" : "&nbsp;");
					$tpl->assign("p_pavo",(round($p_pavo,2) != 0) ? "<font color=\"#".($p_pavo > 0 ? "0000FF" : "FF0000")."\">".number_format($p_pavo,2,".",",")."</font>" : "&nbsp;");
					
					$tpl->assign("ingresos",(round($ing,2) != 0) ? "<font color=\"#".($ing > 0 ? "0000FF" : "FF0000")."\">".number_format($ing,2,".",",")."</font>" : "&nbsp;");
					$tpl->assign("egresos",(round($egr,2) != 0) ? "<font color=\"#".($egr > 0 ? "0000FF" : "FF0000")."\">".number_format($egr,2,".",",")."</font>" : "&nbsp;");
					$tpl->assign("gastos_caja_total",(round($total_caja,2) != 0) ? "<font color=\"#".($total_caja > 0 ? "0000FF" : "FF0000")."\">".number_format($total_caja,2,".",",")."</font>" : "&nbsp;");
					$tpl->assign("depositos",(round($dep,2) != 0) ? "<font color=\"#".($dep > 0 ? "0000FF" : "FF0000")."\">".number_format($dep,2,".",",")."</font>" : "&nbsp;");
					$tpl->assign("otros_depositos",(round($odep,2) != 0) ? "<font color=\"#".($odep > 0 ? "0000FF" : "FF0000")."\">".number_format($odep,2,".",",")."</font>" : "&nbsp;");
					$tpl->assign("general",(round($gen,2) != 0) ? "<font color=\"#".($gen > 0 ? "0000FF" : "FF0000")."\">".number_format($gen,2,".",",")."</font>" : "&nbsp;");
					$tpl->assign("diferencia",(round($dif,2) != 0) ? "<font color=\"#".($dif > 0 ? "0000FF" : "FF0000")."\">".number_format($dif,2,".",",")."</font>" : "&nbsp;");
					
					$tpl->assign('saldo_ini', $sal_ini != 0 ? number_format($sal_ini, 2, '.', ',') : '&nbsp;');
					$tpl->assign('saldo_fin', $sal_fin != 0 ? number_format($sal_fin, 2, '.', ',') : '&nbsp;');
					$tpl->assign('saldo_pro_ini', $sal_pro_ini != 0 ? number_format($sal_pro_ini, 2, '.', ',') : '&nbsp;');
					$tpl->assign('saldo_pro_fin', $sal_pro_fin != 0 ? number_format($sal_pro_fin, 2, '.', ',') : '&nbsp;');
					$tpl->assign('no_inc', $gas_no_inc != 0 ? number_format($gas_no_inc, 2, '.', ',') : '&nbsp;');
					$tpl->assign('dif', $total_otra_dif != 0 ? number_format($total_otra_dif, 2, '.', ',') : '&nbsp;');
				}
				$numcols++;
				
				if ($numcols == $numcols_x_hoja) {
					$tpl->newBlock($num_cia <= 300 || $num_cia == 703 ? "hoja_pan" : "hoja_ros");
					$tpl->assign("num_cia",$num_cia);
					$tpl->assign("nombre_cia",$nombre[0]['nombre']);
					$tpl->assign("anio",$_GET['anio']);
					
					$numcols = 0;
				}
				
				// COLUMNA DE PROMEDIOS
				$tpl->newBlock($num_cia <= 300 || $num_cia == 703 ? "col_pan" : "col_ros");
				$tpl->assign("mes","Promedio");
				// PANADERIAS
				if ($num_cia <= 300 || $num_cia == 703) {
					$tpl->assign("venta_puerta",(round($venta_puerta/$num_meses,2) != 0) ? "<font color=\"#".($venta_puerta/$num_meses > 0 ? "0000FF" : "FF0000")."\">".number_format($venta_puerta/$num_meses,2,".",",")."</font>" : "&nbsp;");
					$tpl->assign("bases",(round($bases/$num_meses,2) != 0) ? "<font color=\"#".($bases/$num_meses > 0 ? "0000FF" : "FF0000")."\">".number_format($bases/$num_meses,2,".",",")."</font>" : "&nbsp;");
					$tpl->assign("barredura",(round($barredura/$num_meses,2) != 0) ? "<font color=\"#".($barredura/$num_meses > 0 ? "0000FF" : "FF0000")."\">".number_format($barredura/$num_meses,2,".",",")."</font>" : "&nbsp;");
					$tpl->assign("pastillaje",(round($pastillaje/$num_meses,2) != 0) ? "<font color=\"#".($pastillaje/$num_meses > 0 ? "0000FF" : "FF0000")."\">".number_format($pastillaje/$num_meses,2,".",",")."</font>" : "&nbsp;");
					$tpl->assign("abono_emp",(round($abono_emp/$num_meses,2) != 0) ? "<font color=\"#".($abono_emp/$num_meses > 0 ? "0000FF" : "FF0000")."\">".number_format($abono_emp/$num_meses,2,".",",")."</font>" : "&nbsp;");
					$tpl->assign("otros",(round($otros/$num_meses,2) != 0) ? "<font color=\"#".($otros/$num_meses > 0 ? "0000FF" : "FF0000")."\">".number_format($otros/$num_meses,2,".",",")."</font>" : "&nbsp;");
					$tpl->assign("total_otros",(round($total_otros/$num_meses,2) != 0) ? "<font color=\"#".($total_otros/$num_meses > 0 ? "0000FF" : "FF0000")."\">".number_format($total_otros/$num_meses,2,".",",")."</font>" : "&nbsp;");
					$tpl->assign("abono_reparto",(round($abono_reparto/$num_meses,2) != 0) ? "<font color=\"#".($abono_reparto/$num_meses > 0 ? "0000FF" : "FF0000")."\">".number_format($abono_reparto/$num_meses,2,".",",")."</font>" : "&nbsp;");
					$tpl->assign("errores",(round($errores/$num_meses,2) != 0) ? "<font color=\"#".($errores/$num_meses > 0 ? "0000FF" : "FF0000")."\">".number_format($errores/$num_meses,2,".",",")."</font>" : "&nbsp;");
					$tpl->assign("ventas_netas",(round($ventas_netas/$num_meses,2) != 0) ? "<font color=\"#".($ventas_netas/$num_meses > 0 ? "0000FF" : "FF0000")."\">".number_format($ventas_netas/$num_meses,2,".",",")."</font>" : "&nbsp;");
					$tpl->assign("inventario_anterior",(round($inv_ant/$num_meses,2) != 0) ? "<font color=\"#".($inv_ant/$num_meses > 0 ? "0000FF" : "FF0000")."\">".number_format($inv_ant/$num_meses,2,".",",")."</font>" : "&nbsp;");
					$tpl->assign("compras",(round($compras/$num_meses,2) != 0) ? "<font color=\"#".($compras/$num_meses > 0 ? "0000FF" : "FF0000")."\">".number_format($compras/$num_meses,2,".",",")."</font>" : "&nbsp;");
					$tpl->assign("mercancias",(round($mercancias/$num_meses,2) != 0) ? "<font color=\"#".($mercancias/$num_meses > 0 ? "0000FF" : "FF0000")."\">".number_format($mercancias/$num_meses,2,".",",")."</font>" : "&nbsp;");
					$tpl->assign("inventario_actual",(round($inv_act/$num_meses,2) != 0) ? "<font color=\"#".($inv_act/$num_meses > 0 ? "0000FF" : "FF0000")."\">".number_format($inv_act/$num_meses,2,".",",")."</font>" : "&nbsp;");
					$tpl->assign("mat_prima_utilizada",(round($mat_prima_utilizada/$num_meses,2) != 0) ? "<font color=\"#".($mat_prima_utilizada/$num_meses > 0 ? "0000FF" : "FF0000")."\">".number_format($mat_prima_utilizada/$num_meses,2,".",",")."</font>" : "&nbsp;");
					$tpl->assign("mano_obra",(round($mano_obra/$num_meses,2) != 0) ? "<font color=\"#".($mano_obra/$num_meses > 0 ? "0000FF" : "FF0000")."\">".number_format($mano_obra/$num_meses,2,".",",")."</font>" : "&nbsp;");
					$tpl->assign("panaderos",(round($panaderos/$num_meses,2) != 0) ? "<font color=\"#".($panaderos/$num_meses > 0 ? "0000FF" : "FF0000")."\">".number_format($panaderos/$num_meses,2,".",",")."</font>" : "&nbsp;");
					$tpl->assign("gastos_fabricacion",(round($gastos_fab/$num_meses,2) != 0) ? "<font color=\"#".($gastos_fab/$num_meses > 0 ? "0000FF" : "FF0000")."\">".number_format($gastos_fab/$num_meses,2,".",",")."</font>" : "&nbsp;");
					$tpl->assign("costo_produccion",(round($costo_produccion/$num_meses,2) != 0) ? "<font color=\"#".($costo_produccion/$num_meses > 0 ? "0000FF" : "FF0000")."\">".number_format($costo_produccion/$num_meses,2,".",",")."</font>" : "&nbsp;");
					$tpl->assign("utilidad_bruta",(round($utilidad_bruta/$num_meses,2) != 0) ? "<font color=\"#".($utilidad_bruta/$num_meses > 0 ? "0000FF" : "FF0000")."\">".number_format($utilidad_bruta/$num_meses,2,".",",")."</font>" : "&nbsp;");
					$tpl->assign("pan_comprado",(round($pan_comprado/$num_meses,2) != 0) ? "<font color=\"#".($pan_comprado/$num_meses > 0 ? "0000FF" : "FF0000")."\">".number_format($pan_comprado/$num_meses,2,".",",")."</font>" : "&nbsp;");
					$tpl->assign("gastos_generales",(round($gastos_generales/$num_meses,2) != 0) ? "<font color=\"#".($gastos_generales/$num_meses > 0 ? "0000FF" : "FF0000")."\">".number_format($gastos_generales/$num_meses,2,".",",")."</font>" : "&nbsp;");
					$tpl->assign("gastos_caja",(round($gastos_caja/$num_meses,2) != 0) ? "<font color=\"#".($gastos_caja/$num_meses > 0 ? "0000FF" : "FF0000")."\">".number_format($gastos_caja/$num_meses,2,".",",")."</font>" : "&nbsp;");
					$tpl->assign("reserva_aguinaldos",(round($reserva_aguinaldos/$num_meses,2) != 0) ? "<font color=\"#".($reserva_aguinaldos/$num_meses > 0 ? "0000FF" : "FF0000")."\">".number_format($reserva_aguinaldos/$num_meses,2,".",",")."</font>" : "&nbsp;");
					$tpl->assign("gastos_otras_cias",(round($gastos_otras_cias/$num_meses,2) != 0) ? "<font color=\"#".($gastos_otras_cias/$num_meses > 0 ? "0000FF" : "FF0000")."\">".number_format($gastos_otras_cias/$num_meses,2,".",",")."</font>" : "&nbsp;");
					$tpl->assign("total_gastos",(round($total_gastos/$num_meses,2) != 0) ? "<font color=\"#".($total_gastos/$num_meses > 0 ? "0000FF" : "FF0000")."\">".number_format($total_gastos/$num_meses,2,".",",")."</font>" : "&nbsp;");
					$tpl->assign("ingresos_ext",(round($ingresos_ext/$num_meses,2) != 0) ? "<font color=\"#".($ingresos_ext/$num_meses > 0 ? "0000FF" : "FF0000")."\">".number_format($ingresos_ext/$num_meses,2,".",",")."</font>" : "&nbsp;");
					$tpl->assign("utilidad_mes",(round($utilidad_neta/$num_meses,2) != 0) ? "<font color=\"#".($utilidad_neta/$num_meses > 0 ? "0000FF" : "FF0000")."\">".number_format($utilidad_neta/$num_meses,2,".",",")."</font>" : "&nbsp;");
					$tpl->assign("mp_vtas",(round($mp_vtas/$num_meses,3) != 0) ? "<font color=\"#".($mp_vtas/$num_meses > 0 ? "0000FF" : "FF0000")."\">".number_format($mp_vtas/$num_meses,3,".",",")."</font>" : "&nbsp;");
					$tpl->assign("utilidad_produccion",(round($utilidad_pro/$num_meses,2) != 0) ? "<font color=\"#".($utilidad_pro/$num_meses > 0 ? "0000FF" : "FF0000")."\">".number_format($utilidad_pro/$num_meses,3,".",",")."</font>" : "&nbsp;");
					$tpl->assign("mp_produccion",(round($mp_pro/$num_meses,3) != 0) ? "<font color=\"#".($mp_pro/$num_meses > 0 ? "0000FF" : "FF0000")."\">".number_format($mp_pro/$num_meses,3,".",",")."</font>" : "&nbsp;");
					$tpl->assign("produccion_total",(round($produccion_total/$num_meses,2) != 0) ? "<font color=\"#".($produccion_total/$num_meses > 0 ? "0000FF" : "FF0000")."\">".number_format($produccion_total/$num_meses,2,".",",")."</font>" : "&nbsp;");
					$tpl->assign("faltante_pan",(round($faltante_pan/$num_meses,2) != 0) ? "<font color=\"#".($faltante_pan/$num_meses > 0 ? "0000FF" : "FF0000")."\">".number_format($faltante_pan/$num_meses,2,".",",")."</font>" : "&nbsp;");
					$tpl->assign("rezago_inicial",(round($rezago_ini/$num_meses,2) != 0) ? "<font color=\"#".($rezago_ini/$num_meses > 0 ? "0000FF" : "FF0000")."\">".number_format($rezago_ini/$num_meses,2,".",",")."</font>" : "&nbsp;");
					$tpl->assign("rezago_final",(round($rezago_fin/$num_meses,2) != 0) ? "<font color=\"#".($rezago_fin/$num_meses > 0 ? "0000FF" : "FF0000")."\">".number_format($rezago_fin/$num_meses,2,".",",")."</font>" : "&nbsp;");
					$tpl->assign("subio_rezago",(round($var_rezago/$num_meses,2) != 0) ? "<font color=\"#".($var_rezago/$num_meses > 0 ? "0000FF" : "FF0000")."\">".number_format($var_rezago/$num_meses,2,".",",")."</font>" : "&nbsp;");
					$tpl->assign("efectivo",(round($efectivo/$num_meses,2) != 0) ? "<font color=\"#".($efectivo/$num_meses > 0 ? "0000FF" : "FF0000")."\">".number_format($efectivo/$num_meses,2,".",",")."</font>" : "&nbsp;");
					
					$tpl->assign("ingresos",(round($ing/$num_meses,2) != 0) ? "<font color=\"#".($ing/$num_meses > 0 ? "0000FF" : "FF0000")."\">".number_format($ing/$num_meses,2,".",",")."</font>" : "&nbsp;");
					$tpl->assign("egresos",(round($egr/$num_meses,2) != 0) ? "<font color=\"#".($egr/$num_meses > 0 ? "0000FF" : "FF0000")."\">".number_format($egr/$num_meses,2,".",",")."</font>" : "&nbsp;");
					$tpl->assign("gastos_caja_total",(round($total_caja/$num_meses,2) != 0) ? "<font color=\"#".($total_caja/$num_meses > 0 ? "0000FF" : "FF0000")."\">".number_format($total_caja/$num_meses,2,".",",")."</font>" : "&nbsp;");
					$tpl->assign("depositos",(round($dep/$num_meses,2) != 0) ? "<font color=\"#".($dep/$num_meses > 0 ? "0000FF" : "FF0000")."\">".number_format($dep/$num_meses,2,".",",")."</font>" : "&nbsp;");
					$tpl->assign("otros_depositos",(round($odep/$num_meses,2) != 0) ? "<font color=\"#".($odep/$num_meses > 0 ? "0000FF" : "FF0000")."\">".number_format($odep/$num_meses,2,".",",")."</font>" : "&nbsp;");
					$tpl->assign("general",(round($gen/$num_meses,2) != 0) ? "<font color=\"#".($gen/$num_meses > 0 ? "0000FF" : "FF0000")."\">".number_format($gen/$num_meses,2,".",",")."</font>" : "&nbsp;");
					$tpl->assign("diferencia",(round($dif/$num_meses,2) != 0) ? "<font color=\"#".($dif/$num_meses > 0 ? "0000FF" : "FF0000")."\">".number_format($dif/$num_meses,2,".",",")."</font>" : "&nbsp;");
					
					$tpl->assign('saldo_ini', '&nbsp;');
					$tpl->assign('saldo_fin', '&nbsp;');
					$tpl->assign('saldo_pro_ini', '&nbsp;');
					$tpl->assign('saldo_pro_fin', '&nbsp;');
					$tpl->assign('no_inc', '&nbsp;');
					$tpl->assign('dif', '&nbsp;');
				}
				// ROSTICERIAS
				else {
					$tpl->assign("venta",(round($venta/$num_meses,2) != 0) ? "<font color=\"#".($venta/$num_meses > 0 ? "0000FF" : "FF0000")."\">".number_format($venta/$num_meses,2,".",",")."</font>" : "&nbsp;");
					$tpl->assign("otros",(round($otros/$num_meses,2) != 0) ? "<font color=\"#".($otros/$num_meses > 0 ? "0000FF" : "FF0000")."\">".number_format($otros/$num_meses,2,".",",")."</font>" : "&nbsp;");
					$tpl->assign("ventas_netas",(round($ventas_netas/$num_meses,2) != 0) ? "<font color=\"#".($ventas_netas/$num_meses > 0 ? "0000FF" : "FF0000")."\">".number_format($ventas_netas/$num_meses,2,".",",")."</font>" : "&nbsp;");
					$tpl->assign("inventario_anterior",(round($inv_ant/$num_meses,2) != 0) ? "<font color=\"#".($inv_ant/$num_meses > 0 ? "0000FF" : "FF0000")."\">".number_format($inv_ant/$num_meses,2,".",",")."</font>" : "&nbsp;");
					$tpl->assign("compras",(round($compras/$num_meses,2) != 0) ? "<font color=\"#".($compras/$num_meses > 0 ? "0000FF" : "FF0000")."\">".number_format($compras/$num_meses,2,".",",")."</font>" : "&nbsp;");
					$tpl->assign("mercancias",(round($mercancias/$num_meses,2) != 0) ? "<font color=\"#".($mercancias/$num_meses > 0 ? "0000FF" : "FF0000")."\">".number_format($mercancias/$num_meses,2,".",",")."</font>" : "&nbsp;");
					$tpl->assign("inventario_actual",(round($inv_act/$num_meses,2) != 0) ? "<font color=\"#".($inv_act/$num_meses > 0 ? "0000FF" : "FF0000")."\">".number_format($inv_act/$num_meses,2,".",",")."</font>" : "&nbsp;");
					$tpl->assign("mat_prima_utilizada",(round($mat_prima_utilizada/$num_meses,2) != 0) ? "<font color=\"#".($mat_prima_utilizada/$num_meses > 0 ? "0000FF" : "FF0000")."\">".number_format($mat_prima_utilizada/$num_meses,2,".",",")."</font>" : "&nbsp;");
					$tpl->assign("gastos_fabricacion",(round($gastos_fab/$num_meses,2) != 0) ? "<font color=\"#".($gastos_fab/$num_meses > 0 ? "0000FF" : "FF0000")."\">".number_format($gastos_fab/$num_meses,2,".",",")."</font>" : "&nbsp;");
					$tpl->assign("costo_produccion",(round($costo_produccion/$num_meses,2) != 0) ? "<font color=\"#".($costo_produccion/$num_meses > 0 ? "0000FF" : "FF0000")."\">".number_format($costo_produccion/$num_meses,2,".",",")."</font>" : "&nbsp;");
					$tpl->assign("utilidad_bruta",(round($utilidad_bruta/$num_meses,2) != 0) ? "<font color=\"#".($utilidad_bruta/$num_meses > 0 ? "0000FF" : "FF0000")."\">".number_format($utilidad_bruta/$num_meses,2,".",",")."</font>" : "&nbsp;");
					$tpl->assign("gastos_generales",(round($gastos_generales/$num_meses,2) != 0) ? "<font color=\"#".($gastos_generales/$num_meses > 0 ? "0000FF" : "FF0000")."\">".number_format($gastos_generales/$num_meses,2,".",",")."</font>" : "&nbsp;");
					$tpl->assign("gastos_caja",(round($gastos_caja/$num_meses,2) != 0) ? "<font color=\"#".($gastos_caja/$num_meses > 0 ? "0000FF" : "FF0000")."\">".number_format($gastos_caja/$num_meses,2,".",",")."</font>" : "&nbsp;");
					$tpl->assign("reserva_aguinaldos",(round($reserva_aguinaldos/$num_meses,2) != 0) ? "<font color=\"#".($reserva_aguinaldos/$num_meses > 0 ? "0000FF" : "FF0000")."\">".number_format($reserva_aguinaldos/$num_meses,2,".",",")."</font>" : "&nbsp;");
					$tpl->assign("gastos_otras_cias",(round($gastos_otras_cias/$num_meses,2) != 0) ? "<font color=\"#".($gastos_otras_cias/$num_meses > 0 ? "0000FF" : "FF0000")."\">".number_format($gastos_otras_cias/$num_meses,2,".",",")."</font>" : "&nbsp;");
					$tpl->assign("total_gastos",(round($total_gastos/$num_meses,2) != 0) ? "<font color=\"#".($total_gastos/$num_meses > 0 ? "0000FF" : "FF0000")."\">".number_format($total_gastos/$num_meses,2,".",",")."</font>" : "&nbsp;");
					$tpl->assign("ingresos_ext",(round($ingresos_ext/$num_meses,2) != 0) ? "<font color=\"#".($ingresos_ext/$num_meses > 0 ? "0000FF" : "FF0000")."\">".number_format($ingresos_ext/$num_meses,2,".",",")."</font>" : "&nbsp;");
					$tpl->assign("utilidad_mes",(round($utilidad_neta/$num_meses,2) != 0) ? "<font color=\"#".($utilidad_neta/$num_meses > 0 ? "0000FF" : "FF0000")."\">".number_format($utilidad_neta/$num_meses,2,".",",")."</font>" : "&nbsp;");
					$tpl->assign("mp_vtas",(round($mp_vtas/$num_meses,2) != 0) ? "<font color=\"#".($mp_vtas/$num_meses > 0 ? "0000FF" : "FF0000")."\">".number_format($mp_vtas/$num_meses,2,".",",")."</font>" : "&nbsp;");
					$tpl->assign("efectivo",(round($efectivo/$num_meses,2) != 0) ? "<font color=\"#".($efectivo/$num_meses > 0 ? "0000FF" : "FF0000")."\">".number_format($efectivo/$num_meses,2,".",",")."</font>" : "&nbsp;");
					$tpl->assign("pollos_vendidos",(round($pollos_vendidos/$num_meses,2) != 0) ? "<font color=\"#".($pollos_vendidos/$num_meses > 0 ? "0000FF" : "FF0000")."\">".number_format($pollos_vendidos/$num_meses,2,".",",")."</font>" : "&nbsp;");
					$tpl->assign("p_pavo",(round($p_pavo/$num_meses,2) != 0) ? "<font color=\"#".($p_pavo/$num_meses > 0 ? "0000FF" : "FF0000")."\">".number_format($p_pavo/$num_meses,2,".",",")."</font>" : "&nbsp;");
					
					$tpl->assign("ingresos",(round($ing/$num_meses,2) != 0) ? "<font color=\"#".($ing/$num_meses > 0 ? "0000FF" : "FF0000")."\">".number_format($ing/$num_meses,2,".",",")."</font>" : "&nbsp;");
					$tpl->assign("egresos",(round($egr/$num_meses,2) != 0) ? "<font color=\"#".($egr/$num_meses > 0 ? "0000FF" : "FF0000")."\">".number_format($egr/$num_meses,2,".",",")."</font>" : "&nbsp;");
					$tpl->assign("gastos_caja_total",(round($total_caja/$num_meses,2) != 0) ? "<font color=\"#".($total_caja/$num_meses > 0 ? "0000FF" : "FF0000")."\">".number_format($total_caja/$num_meses,2,".",",")."</font>" : "&nbsp;");
					$tpl->assign("depositos",(round($dep/$num_meses,2) != 0) ? "<font color=\"#".($dep/$num_meses > 0 ? "0000FF" : "FF0000")."\">".number_format($dep/$num_meses,2,".",",")."</font>" : "&nbsp;");
					$tpl->assign("otros_depositos",(round($odep/$num_meses,2) != 0) ? "<font color=\"#".($odep/$num_meses > 0 ? "0000FF" : "FF0000")."\">".number_format($odep/$num_meses,2,".",",")."</font>" : "&nbsp;");
					$tpl->assign("general",(round($gen/$num_meses,2) != 0) ? "<font color=\"#".($gen/$num_meses > 0 ? "0000FF" : "FF0000")."\">".number_format($gen/$num_meses,2,".",",")."</font>" : "&nbsp;");
					$tpl->assign("diferencia",(round($dif/$num_meses,2) != 0) ? "<font color=\"#".($dif/$num_meses > 0 ? "0000FF" : "FF0000")."\">".number_format($dif/$num_meses,2,".",",")."</font>" : "&nbsp;");
					
					$tpl->assign('saldo_ini', '&nbsp;');
					$tpl->assign('saldo_fin', '&nbsp;');
					$tpl->assign('saldo_pro_ini', '&nbsp;');
					$tpl->assign('saldo_pro_fin', '&nbsp;');
					$tpl->assign('no_inc', '&nbsp;');
					$tpl->assign('dif', '&nbsp;');
				}
			}
			
			$num_cia = $result[$i]['num_cia'];
			
			$tpl->newBlock($num_cia <= 300 || $num_cia == 703 ? "hoja_pan" : "hoja_ros");
			$tpl->assign("num_cia",$num_cia);
			$nombre = ejecutar_script("SELECT nombre FROM catalogo_companias WHERE num_cia = $num_cia",$dsn);
			$tpl->assign("nombre_cia",$nombre[0]['nombre']);
			$tpl->assign("anio",$_GET['anio']);
			
			$num_meses = 0;
			
			if ($num_cia <= 300 || $num_cia == 703) {
				$venta_puerta = 0;
				$bases = 0;
				$barredura = 0;
				$pastillaje = 0;
				$abono_emp = 0;
				$otros = 0;
				$total_otros = 0;
				$abono_reparto = 0;
				$errores = 0;
				$ventas_netas = 0;
				$inv_ant = 0;
				$compras = 0;
				$mercancias = 0;
				$inv_act = 0;
				$mat_prima_utilizada = 0;
				$mano_obra = 0;
				$panaderos = 0;
				$gastos_fab = 0;
				$costo_produccion = 0;
				$utilidad_bruta = 0;
				$pan_comprado = 0;
				$gastos_generales = 0;
				$gastos_caja = 0;
				$reserva_aguinaldos = 0;
				$gastos_otras_cias = 0;
				$total_gastos = 0;
				$ingresos_ext = 0;
				$utilidad_neta = 0;
				$mp_vtas = 0;
				$utilidad_pro = 0;
				$mp_pro = 0;
				$produccion_total = 0;
				$faltante_pan = 0;
				$rezago_ini = 0;
				$rezago_fin = 0;
				$var_rezago = 0;
				$efectivo = 0;
				
				$ing = 0;
				$egr = 0;
				$total_caja = 0;
				$dep = 0;
				$odep = 0;
				$gen = 0;
				$dif = 0;
				
				$sal_ini = NULL;
				$sal_fin = 0;
				$sal_pro_ini = NULL;
				$sal_pro_fin = 0;
				$gas_no_inc = 0;
				$total_otra_dif = 0;
			}
			else {
				$venta = 0;
				$otros = 0;
				$total_otros = 0;
				$ventas_netas = 0;
				$inv_ant = 0;
				$compras = 0;
				$mercancias = 0;
				$inv_act = 0;
				$mat_prima_utilizada = 0;
				$gastos_fab = 0;
				$costo_produccion = 0;
				$utilidad_bruta = 0;
				$gastos_generales = 0;
				$gastos_caja = 0;
				$reserva_aguinaldos = 0;
				$gastos_otras_cias = 0;
				$total_gastos = 0;
				$ingresos_ext = 0;
				$utilidad_neta = 0;
				$mp_vtas = 0;
				$efectivo = 0;
				$pollos_vendidos = 0;
				$p_pavo = 0;
				
				$ing = 0;
				$egr = 0;
				$total_caja = 0;
				$dep = 0;
				$odep = 0;
				$gen = 0;
				$dif = 0;
				
				$sal_ini = NULL;
				$sal_fin = 0;
				$sal_pro_ini = NULL;
				$sal_pro_fin = 0;
				$gas_no_inc = 0;
				$total_otra_dif = 0;
			}
			
			$numcols = 0;
		}
		if ($numcols == $numcols_x_hoja) {
			$tpl->newBlock($num_cia <= 300 || $num_cia == 703 ? "hoja_pan" : "hoja_ros");
			$tpl->assign("num_cia",$num_cia);
			$tpl->assign("nombre_cia",$nombre[0]['nombre']);
			$tpl->assign("anio",$_GET['anio']);
			
			$numcols = 0;
		}
		$tpl->newBlock($num_cia <= 300 || $num_cia == 703 ? "col_pan" : "col_ros");
		$tpl->assign("mes",mes_escrito($result[$i]['mes']));
		// PANADERIAS
		if ($num_cia <= 300 ||$num_cia == 703) {
			$tpl->assign("venta_puerta",(round($result[$i]['venta_puerta'],2) != 0) ? "<font color=\"#".($result[$i]['venta_puerta'] > 0 ? "0000FF" : "FF0000")."\">".number_format($result[$i]['venta_puerta'],2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("bases",(round($result[$i]['bases'],2) != 0) ? "<font color=\"#".($result[$i]['bases'] > 0 ? "0000FF" : "FF0000")."\">".number_format($result[$i]['bases'],2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("barredura",(round($result[$i]['barredura'],2) != 0) ? "<font color=\"#".($result[$i]['barredura'] > 0 ? "0000FF" : "FF0000")."\">".number_format($result[$i]['barredura'],2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("pastillaje",(round($result[$i]['pastillaje'],2) != 0) ? "<font color=\"#".($result[$i]['pastillaje'] > 0 ? "0000FF" : "FF0000")."\">".number_format($result[$i]['pastillaje'],2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("abono_emp",(round($result[$i]['abono_emp'],2) != 0) ? "<font color=\"#".($result[$i]['abono_emp'] > 0 ? "0000FF" : "FF0000")."\">".number_format($result[$i]['abono_emp'],2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("otros",(round($result[$i]['otros'],2) != 0) ? "<font color=\"#".($result[$i]['otros'] > 0 ? "0000FF" : "FF0000")."\">".number_format($result[$i]['otros'],2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("total_otros",(round($result[$i]['total_otros'],2) != 0) ? "<font color=\"#".($result[$i]['total_otros'] > 0 ? "0000FF" : "FF0000")."\">".number_format($result[$i]['total_otros'],2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("abono_reparto",(round($result[$i]['abono_reparto'],2) != 0) ? "<font color=\"#".($result[$i]['abono_reparto'] > 0 ? "0000FF" : "FF0000")."\">".number_format($result[$i]['abono_reparto'],2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("errores",(round($result[$i]['errores'],2) != 0) ? "<font color=\"#".($result[$i]['errores'] > 0 ? "0000FF" : "FF0000")."\">".number_format($result[$i]['errores'],2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("ventas_netas",(round($result[$i]['ventas_netas'],2) != 0) ? "<font color=\"#".($result[$i]['ventas_netas'] > 0 ? "0000FF" : "FF0000")."\">".number_format($result[$i]['ventas_netas'],2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("inventario_anterior",(round($result[$i]['inv_ant'],2) != 0) ? "<font color=\"#".($result[$i]['inv_ant'] > 0 ? "0000FF" : "FF0000")."\">".number_format($result[$i]['inv_ant'],2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("compras",(round($result[$i]['compras'],2) != 0) ? "<font color=\"#".($result[$i]['compras'] > 0 ? "0000FF" : "FF0000")."\">".number_format($result[$i]['compras'],2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("mercancias",(round($result[$i]['mercancias'],2) != 0) ? "<font color=\"#".($result[$i]['mercancias'] > 0 ? "0000FF" : "FF0000")."\">".number_format($result[$i]['mercancias'],2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("inventario_actual",(round($result[$i]['inv_act'],2) != 0) ? "<font color=\"#".($result[$i]['inv_act'] > 0 ? "0000FF" : "FF0000")."\">".number_format($result[$i]['inv_act'],2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("mat_prima_utilizada",(round($result[$i]['mat_prima_utilizada'],2) != 0) ? "<font color=\"#".($result[$i]['mat_prima_utilizada'] > 0 ? "0000FF" : "FF0000")."\">".number_format($result[$i]['mat_prima_utilizada'],2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("mano_obra",(round($result[$i]['mano_obra'],2) != 0) ? "<font color=\"#".($result[$i]['mano_obra'] > 0 ? "0000FF" : "FF0000")."\">".number_format($result[$i]['mano_obra'],2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("panaderos",(round($result[$i]['panaderos'],2) != 0) ? "<font color=\"#".($result[$i]['panaderos'] > 0 ? "0000FF" : "FF0000")."\">".number_format($result[$i]['panaderos'],2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("gastos_fabricacion",(round($result[$i]['gastos_fab'],2) != 0) ? "<font color=\"#".($result[$i]['gastos_fab'] > 0 ? "0000FF" : "FF0000")."\">".number_format($result[$i]['gastos_fab'],2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("costo_produccion",(round($result[$i]['costo_produccion'],2) != 0) ? "<font color=\"#".($result[$i]['costo_produccion'] > 0 ? "0000FF" : "FF0000")."\">".number_format($result[$i]['costo_produccion'],2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("utilidad_bruta",(round($result[$i]['utilidad_bruta'],2) != 0) ? "<font color=\"#".($result[$i]['utilidad_bruta'] > 0 ? "0000FF" : "FF0000")."\">".number_format($result[$i]['utilidad_bruta'],2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("pan_comprado",(round($result[$i]['pan_comprado'],2) != 0) ? "<font color=\"#".($result[$i]['pan_comprado'] > 0 ? "0000FF" : "FF0000")."\">".number_format($result[$i]['pan_comprado'],2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("gastos_generales",(round($result[$i]['gastos_generales'],2) != 0) ? "<font color=\"#".($result[$i]['gastos_generales'] > 0 ? "0000FF" : "FF0000")."\">".number_format($result[$i]['gastos_generales'],2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("gastos_caja",(round($result[$i]['gastos_caja'],2) != 0) ? "<font color=\"#".($result[$i]['gastos_caja'] > 0 ? "0000FF" : "FF0000")."\">".number_format($result[$i]['gastos_caja'],2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("reserva_aguinaldos",(round($result[$i]['reserva_aguinaldos'],2) != 0) ? "<font color=\"#".($result[$i]['reserva_aguinaldos'] > 0 ? "0000FF" : "FF0000")."\">".number_format($result[$i]['reserva_aguinaldos'],2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("gastos_otras_cias",(round($result[$i]['gastos_otras_cias'],2) != 0) ? "<font color=\"#".($result[$i]['gastos_otras_cias'] > 0 ? "0000FF" : "FF0000")."\">".number_format($result[$i]['gastos_otras_cias'],2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("total_gastos",(round($result[$i]['total_gastos'],2) != 0) ? "<font color=\"#".($result[$i]['total_gastos'] > 0 ? "0000FF" : "FF0000")."\">".number_format($result[$i]['total_gastos'],2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("ingresos_ext",(round($result[$i]['ingresos_ext'],2) != 0) ? "<font color=\"#".($result[$i]['ingresos_ext'] > 0 ? "0000FF" : "FF0000")."\">".number_format($result[$i]['ingresos_ext'],2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("utilidad_mes",(round($result[$i]['utilidad_neta'],2) != 0) ? "<font color=\"#".($result[$i]['utilidad_neta'] > 0 ? "0000FF" : "FF0000")."\">".number_format($result[$i]['utilidad_neta'],2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("mp_vtas",(round($result[$i]['mp_vtas'],3) != 0) ? "<font color=\"#".($result[$i]['mp_vtas'] > 0 ? "0000FF" : "FF0000")."\">".number_format($result[$i]['mp_vtas'],3,".",",")."</font>" : "&nbsp;");
			$tpl->assign("utilidad_produccion",(round($result[$i]['utilidad_pro'],2) != 0) ? "<font color=\"#".($result[$i]['utilidad_pro'] > 0 ? "0000FF" : "FF0000")."\">".number_format($result[$i]['utilidad_pro'],3,".",",")."</font>" : "&nbsp;");
			$tpl->assign("mp_produccion",(round($result[$i]['mp_pro'],3) != 0) ? "<font color=\"#".($result[$i]['mp_pro'] > 0 ? "0000FF" : "FF0000")."\">".number_format($result[$i]['mp_pro'],3,".",",")."</font>" : "&nbsp;");
			$tpl->assign("produccion_total",(round($result[$i]['produccion_total'],2) != 0) ? "<font color=\"#".($result[$i]['produccion_total'] > 0 ? "0000FF" : "FF0000")."\">".number_format($result[$i]['produccion_total'],2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("faltante_pan",(round($result[$i]['faltante_pan'],2) != 0) ? "<font color=\"#".($result[$i]['faltante_pan'] > 0 ? "0000FF" : "FF0000")."\">".number_format($result[$i]['faltante_pan'],2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("rezago_inicial",(round($result[$i]['rezago_ini'],2) != 0) ? "<font color=\"#".($result[$i]['rezago_ini'] > 0 ? "0000FF" : "FF0000")."\">".number_format($result[$i]['rezago_ini'],2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("rezago_final",(round($result[$i]['rezago_fin'],2) != 0) ? "<font color=\"#".($result[$i]['rezago_fin'] > 0 ? "0000FF" : "FF0000")."\">".number_format($result[$i]['rezago_fin'],2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("subio_rezago",(round($result[$i]['var_rezago'],2) != 0) ? "<font color=\"#".($result[$i]['var_rezago'] > 0 ? "0000FF" : "FF0000")."\">".number_format($result[$i]['var_rezago'],2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("efectivo",(round($result[$i]['efectivo'],2) != 0) ? "<font color=\"#".($result[$i]['efectivo'] > 0 ? "0000FF" : "FF0000")."\">".number_format($result[$i]['efectivo'],2,".",",")."</font>" : "&nbsp;");
			
			$fecha1 = "1/{$result[$i]['mes']}/$_GET[anio]";
			$fecha2 = date("d/m/Y",mktime(0,0,0,$result[$i]['mes']+1,0,$_GET['anio']));
			// Obtener Gastos de caja (ingresos y egresos) del mes
			$sql = "SELECT SUM(importe) FROM gastos_caja WHERE num_cia = $num_cia AND fecha BETWEEN '$fecha1' AND '$fecha2' AND tipo_mov = 'FALSE'";
			$ingresos = ejecutar_script($sql,$dsn);
			$sql = "SELECT SUM(importe) FROM gastos_caja WHERE num_cia = $num_cia AND fecha BETWEEN '$fecha1' AND '$fecha2' AND tipo_mov = 'TRUE'";
			$egresos = ejecutar_script($sql,$dsn);
			$total_gastos_caja = $egresos[0]['sum'] - $ingresos[0]['sum'];
			$tpl->assign("ingresos",$ingresos[0]['sum'] > 0 ? number_format($ingresos[0]['sum'],2,".",",") : "&nbsp;");
			$tpl->assign("egresos",$egresos[0]['sum'] > 0 ? number_format($egresos[0]['sum'],2,".",",") : "&nbsp;");
			$tpl->assign("gastos_caja_total",(round($total_gastos_caja,2) != 0) ? "<font color=\"#".($total_gastos_caja > 0 ? "0000FF" : "FF0000")."\">".number_format($total_gastos_caja,2,".",",")."</font>" : "&nbsp;");
			// Obtener depositos
			$sql = "SELECT SUM(importe) FROM estado_cuenta WHERE num_cia = $num_cia AND fecha BETWEEN '$fecha1' AND '$fecha2' AND cod_mov = 1";
			$depositos = ejecutar_script($sql,$dsn);
			$tpl->assign("depositos",(round($depositos[0]['sum'],2) != 0) ? "<font color=\"#".($depositos[0]['sum'] > 0 ? "0000FF" : "FF0000")."\">".number_format($depositos[0]['sum'],2,".",",")."</font>" : "&nbsp;");
			// Otros depositos
			$sql = "SELECT SUM(importe) FROM otros_depositos WHERE num_cia = $num_cia AND fecha BETWEEN '$fecha1' AND '$fecha2'";
			$otros_dep = ejecutar_script($sql,$dsn);
			$tpl->assign("otros_depositos",(round($otros_dep[0]['sum'],2) != 0) ? "<font color=\"#".($otros_dep[0]['sum'] > 0 ? "0000FF" : "FF0000")."\">".number_format($otros_dep[0]['sum'],2,".",",")."</font>" : "&nbsp;");
			// General
			$general = $otros_dep[0]['sum'] + $total_gastos_caja;
			$tpl->assign("general",(round($general,2) != 0) ? "<font color=\"#".($general > 0 ? "0000FF" : "FF0000")."\">".number_format($general,2,".",",")."</font>" : "&nbsp;");
			// Diferencia
			$diferencia = $general - $result[$i]['utilidad_neta'];
			$tpl->assign("diferencia",(round($diferencia,2) != 0) ? "<font color=\"#".($diferencia > 0 ? "0000FF" : "FF0000")."\">".number_format($diferencia,2,".",",")."</font>" : "&nbsp;");
			
			$ing += $ingresos[0]['sum'];
			$egr += $egresos[0]['sum'];
			$total_caja += $total_gastos_caja;
			$dep += $depositos[0]['sum'];
			$odep += $otros_dep[0]['sum'];
			$gen += $general;
			$dif += $diferencia;
			
			// [11-Feb-2009] Obtener saldo en bancos inicial del mes
			$sql = "SELECT sum(saldo_libros) AS saldo FROM his_sal_ban WHERE num_cia = $num_cia AND fecha < '$fecha1' GROUP BY fecha ORDER BY fecha DESC LIMIT 1";
			$tmp = ejecutar_script($sql,$dsn);
			$saldo_ini = $tmp ? round($tmp[0]['saldo'], 2) : 0;
			
			// [11-Feb-2009] Obtener saldo en bancos final del mes
			$sql = "SELECT sum(saldo_libros) AS saldo FROM his_sal_ban WHERE num_cia = $num_cia AND fecha <= '$fecha2' GROUP BY fecha ORDER BY fecha DESC LIMIT 1";
			$tmp = ejecutar_script($sql,$dsn);
			$saldo_fin = $tmp ? round($tmp[0]['saldo'], 2) : 0;
			
			// [11-Feb-2009] Obtener saldo a proveedores inicial del mes
			$sql = "SELECT saldo FROM his_sal_pro WHERE num_cia = $num_cia AND fecha < '$fecha1' ORDER BY fecha DESC LIMIT 1";
			$tmp = ejecutar_script($sql,$dsn);
			$saldo_pro_ini = $tmp ? round($tmp[0]['saldo'], 2) : 0;
			
			// [11-Feb-2009] Obtener saldo a proveedores final del mes
			$sql = "SELECT saldo FROM his_sal_pro WHERE num_cia = $num_cia AND fecha <= '$fecha2' ORDER BY fecha DESC LIMIT 1";
			$tmp = ejecutar_script($sql,$dsn);
			$saldo_pro_fin = $tmp ? round($tmp[0]['saldo'], 2) : 0;
			
			$tpl->assign('saldo_ini', $saldo_ini != 0 ? number_format($saldo_ini, 2, '.', ',') : '&nbsp;');
			$tpl->assign('saldo_fin', $saldo_fin != 0 ? number_format($saldo_fin, 2, '.', ',') : '&nbsp;');
			$tpl->assign('saldo_pro_ini', $saldo_pro_ini != 0 ? number_format($saldo_pro_ini, 2, '.', ',') : '&nbsp;');
			$tpl->assign('saldo_pro_fin', $saldo_pro_fin != 0 ? number_format($saldo_pro_fin, 2, '.', ',') : '&nbsp;');
			
			if ($sal_ini === NULL)
				$sal_ini = $saldo_ini;
			
			$sal_fin = $saldo_fin;
			
			if ($sal_pro_ini === NULL)
				$sal_pro_ini = $saldo_pro_ini;
			
			$sal_pro_fin = $saldo_pro_fin;
			
			// [12-Feb-2009] Obtener gastos no incluidos
			$sql = "SELECT sum(importe) AS importe FROM movimiento_gastos g LEFT JOIN catalogo_gastos cg USING (codgastos) WHERE num_cia = $num_cia AND fecha BETWEEN '$fecha1' AND '$fecha2' AND codigo_edo_resultados = 0 AND codgastos NOT IN (33, 134)";
			$tmp = ejecutar_script($sql, $dsn);
			$g = $tmp ? $tmp[0]['importe'] : 0;
			
			// [12-Feb-2009] Obtener gastos de caja no incluidos
			$sql = "SELECT sum(CASE WHEN tipo_mov = 'FALSE' THEN importe ELSE -importe END) AS importe FROM gastos_caja WHERE num_cia = $num_cia AND fecha BETWEEN '$fecha1' AND '$fecha2' AND clave_balance = 'FALSE'";
			$tmp = ejecutar_script($sql, $dsn);
			$gc = $tmp ? $tmp[0]['importe'] : 0;
			
			// [12-Feb-2009] No incluidos
			$no_inc = $g + $gc;
			$tpl->assign('no_inc', $no_inc != 0 ? number_format($no_inc, 2, '.', ',') : '&nbsp;');
			
			$gas_no_inc += $no_inc;
			
			// [17-Jun-2009] Dif = Diferencia - Saldo Inicial + Saldo Final - Saldo Pro. Inicial + Saldo Pro. Final + No Incluidos
			$otra_dif = $diferencia - $saldo_ini + $saldo_fin - $saldo_pro_ini + $saldo_pro_fin + $no_inc;
			$tpl->assign('dif', number_format($otra_dif, 2, '.', ','));
			$total_otra_dif += $otra_dif;
			
			$num_meses++;
			
			$venta_puerta += $result[$i]['venta_puerta'];
			$bases += $result[$i]['bases'];
			$barredura += $result[$i]['barredura'];
			$pastillaje += $result[$i]['pastillaje'];
			$abono_emp += $result[$i]['abono_emp'];
			$otros += $result[$i]['otros'];
			$total_otros += $result[$i]['total_otros'];
			$abono_reparto += $result[$i]['abono_reparto'];
			$errores += $result[$i]['errores'];
			$ventas_netas += $result[$i]['ventas_netas'];
			$inv_ant += $result[$i]['inv_ant'];
			$compras += $result[$i]['compras'];
			$mercancias += $result[$i]['mercancias'];
			$inv_act += $result[$i]['inv_act'];
			$mat_prima_utilizada += $result[$i]['mat_prima_utilizada'];
			$mano_obra += $result[$i]['mano_obra'];
			$panaderos += $result[$i]['panaderos'];
			$gastos_fab += $result[$i]['gastos_fab'];
			$costo_produccion += $result[$i]['costo_produccion'];
			$utilidad_bruta += $result[$i]['utilidad_bruta'];
			$pan_comprado += $result[$i]['pan_comprado'];
			$gastos_generales += $result[$i]['gastos_generales'];
			$gastos_caja += $result[$i]['gastos_caja'];
			$reserva_aguinaldos += $result[$i]['reserva_aguinaldos'];
			$gastos_otras_cias += $result[$i]['gastos_otras_cias'];
			$total_gastos += $result[$i]['total_gastos'];
			$ingresos_ext += $result[$i]['ingresos_ext'];
			$utilidad_neta += $result[$i]['utilidad_neta'];
			$mp_vtas += $result[$i]['mp_vtas'];
			$utilidad_pro += $result[$i]['utilidad_pro'];
			$mp_pro += $result[$i]['mp_pro'];
			$produccion_total += $result[$i]['produccion_total'];
			$faltante_pan += $result[$i]['faltante_pan'];
			$rezago_ini += $result[$i]['rezago_ini'];
			$rezago_fin += $result[$i]['rezago_fin'];
			$var_rezago += $result[$i]['var_rezago'];
			$efectivo += $result[$i]['efectivo'];
		}
		// ROSTICERIAS
		else {
			$tpl->assign("venta",(round($result[$i]['venta'],2) != 0) ? "<font color=\"#".($result[$i]['venta'] > 0 ? "0000FF" : "FF0000")."\">".number_format($result[$i]['venta'],2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("otros",(round($result[$i]['otros'],2) != 0) ? "<font color=\"#".($result[$i]['otros'] > 0 ? "0000FF" : "FF0000")."\">".number_format($result[$i]['otros'],2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("ventas_netas",(round($result[$i]['ventas_netas'],2) != 0) ? "<font color=\"#".($result[$i]['ventas_netas'] > 0 ? "0000FF" : "FF0000")."\">".number_format($result[$i]['ventas_netas'],2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("inventario_anterior",(round($result[$i]['inv_ant'],2) != 0) ? "<font color=\"#".($result[$i]['inv_ant'] > 0 ? "0000FF" : "FF0000")."\">".number_format($result[$i]['inv_ant'],2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("compras",(round($result[$i]['compras'],2) != 0) ? "<font color=\"#".($result[$i]['compras'] > 0 ? "0000FF" : "FF0000")."\">".number_format($result[$i]['compras'],2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("mercancias",(round($result[$i]['mercancias'],2) != 0) ? "<font color=\"#".($result[$i]['mercancias'] > 0 ? "0000FF" : "FF0000")."\">".number_format($result[$i]['mercancias'],2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("inventario_actual",(round($result[$i]['inv_act'],2) != 0) ? "<font color=\"#".($result[$i]['inv_act'] > 0 ? "0000FF" : "FF0000")."\">".number_format($result[$i]['inv_act'],2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("mat_prima_utilizada",(round($result[$i]['mat_prima_utilizada'],2) != 0) ? "<font color=\"#".($result[$i]['mat_prima_utilizada'] > 0 ? "0000FF" : "FF0000")."\">".number_format($result[$i]['mat_prima_utilizada'],2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("gastos_fabricacion",(round($result[$i]['gastos_fab'],2) != 0) ? "<font color=\"#".($result[$i]['gastos_fab'] > 0 ? "0000FF" : "FF0000")."\">".number_format($result[$i]['gastos_fab'],2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("costo_produccion",(round($result[$i]['costo_produccion'],2) != 0) ? "<font color=\"#".($result[$i]['costo_produccion'] > 0 ? "0000FF" : "FF0000")."\">".number_format($result[$i]['costo_produccion'],2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("utilidad_bruta",(round($result[$i]['utilidad_bruta'],2) != 0) ? "<font color=\"#".($result[$i]['utilidad_bruta'] > 0 ? "0000FF" : "FF0000")."\">".number_format($result[$i]['utilidad_bruta'],2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("gastos_generales",(round($result[$i]['gastos_generales'],2) != 0) ? "<font color=\"#".($result[$i]['gastos_generales'] > 0 ? "0000FF" : "FF0000")."\">".number_format($result[$i]['gastos_generales'],2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("gastos_caja",(round($result[$i]['gastos_caja'],2) != 0) ? "<font color=\"#".($result[$i]['gastos_caja'] > 0 ? "0000FF" : "FF0000")."\">".number_format($result[$i]['gastos_caja'],2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("reserva_aguinaldos",(round($result[$i]['reserva_aguinaldos'],2) != 0) ? "<font color=\"#".($result[$i]['reserva_aguinaldos'] > 0 ? "0000FF" : "FF0000")."\">".number_format($result[$i]['reserva_aguinaldos'],2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("gastos_otras_cias",(round($result[$i]['gastos_otras_cias'],2) != 0) ? "<font color=\"#".($result[$i]['gastos_otras_cias'] > 0 ? "0000FF" : "FF0000")."\">".number_format($result[$i]['gastos_otras_cias'],2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("total_gastos",(round($result[$i]['total_gastos'],2) != 0) ? "<font color=\"#".($result[$i]['total_gastos'] > 0 ? "0000FF" : "FF0000")."\">".number_format($result[$i]['total_gastos'],2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("ingresos_ext",(round($result[$i]['ingresos_ext'],2) != 0) ? "<font color=\"#".($result[$i]['ingresos_ext'] > 0 ? "0000FF" : "FF0000")."\">".number_format($result[$i]['ingresos_ext'],2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("utilidad_mes",(round($result[$i]['utilidad_neta'],2) != 0) ? "<font color=\"#".($result[$i]['utilidad_neta'] > 0 ? "0000FF" : "FF0000")."\">".number_format($result[$i]['utilidad_neta'],2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("mp_vtas",(round($result[$i]['mp_vtas'],2) != 0) ? "<font color=\"#".($result[$i]['mp_vtas'] > 0 ? "0000FF" : "FF0000")."\">".number_format($result[$i]['mp_vtas'],2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("efectivo",(round($result[$i]['efectivo'],2) != 0) ? "<font color=\"#".($result[$i]['efectivo'] > 0 ? "0000FF" : "FF0000")."\">".number_format($result[$i]['efectivo'],2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("pollos_vendidos",(round($result[$i]['pollos_vendidos'],2) != 0) ? "<font color=\"#".($result[$i]['pollos_vendidos'] > 0 ? "0000FF" : "FF0000")."\">".number_format($result[$i]['pollos_vendidos'],2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("p_pavo",(round($result[$i]['p_pavo'],2) != 0) ? "<font color=\"#".($result[$i]['p_pavo'] > 0 ? "0000FF" : "FF0000")."\">".number_format($result[$i]['p_pavo'],2,".",",")."</font>" : "&nbsp;");
			
			$fecha1 = "1/{$result[$i]['mes']}/$_GET[anio]";
			$fecha2 = date("d/m/Y",mktime(0,0,0,$result[$i]['mes']+1,0,$_GET['anio']));
			// Obtener Gastos de caja (ingresos y egresos) del mes
			$sql = "SELECT SUM(importe) FROM gastos_caja WHERE num_cia = $num_cia AND fecha BETWEEN '$fecha1' AND '$fecha2' AND tipo_mov = 'FALSE'";
			$ingresos = ejecutar_script($sql,$dsn);
			$sql = "SELECT SUM(importe) FROM gastos_caja WHERE num_cia = $num_cia AND fecha BETWEEN '$fecha1' AND '$fecha2' AND tipo_mov = 'TRUE'";
			$egresos = ejecutar_script($sql,$dsn);
			$total_gastos_caja = $egresos[0]['sum'] - $ingresos[0]['sum'];
			$tpl->assign("ingresos",$ingresos[0]['sum'] > 0 ? number_format($ingresos[0]['sum'],2,".",",") : "&nbsp;");
			$tpl->assign("egresos",$egresos[0]['sum'] > 0 ? number_format($egresos[0]['sum'],2,".",",") : "&nbsp;");
			$tpl->assign("gastos_caja_total",(round($total_gastos_caja,2) != 0) ? "<font color=\"#".($total_gastos_caja > 0 ? "0000FF" : "FF0000")."\">".number_format($total_gastos_caja,2,".",",")."</font>" : "&nbsp;");
			// Obtener depositos
			$sql = "SELECT SUM(importe) FROM estado_cuenta WHERE num_cia = $num_cia AND fecha BETWEEN '$fecha1' AND '$fecha2' AND cod_mov = 1";
			$depositos = ejecutar_script($sql,$dsn);
			$tpl->assign("depositos",(round($depositos[0]['sum'],2) != 0) ? "<font color=\"#".($depositos[0]['sum'] > 0 ? "0000FF" : "FF0000")."\">".number_format($depositos[0]['sum'],2,".",",")."</font>" : "&nbsp;");
			// Otros depositos
			$sql = "SELECT SUM(importe) FROM otros_depositos WHERE num_cia = $num_cia AND fecha BETWEEN '$fecha1' AND '$fecha2'";
			$otros_dep = ejecutar_script($sql,$dsn);
			$tpl->assign("otros_depositos",(round($otros_dep[0]['sum'],2) != 0) ? "<font color=\"#".($otros_dep[0]['sum'] > 0 ? "0000FF" : "FF0000")."\">".number_format($otros_dep[0]['sum'],2,".",",")."</font>" : "&nbsp;");
			// General
			$general = $otros_dep[0]['sum'] + $total_gastos_caja;
			$tpl->assign("general",(round($general,2) != 0) ? "<font color=\"#".($general > 0 ? "0000FF" : "FF0000")."\">".number_format($general,2,".",",")."</font>" : "&nbsp;");
			// Diferencia
			$diferencia = $general - $result[$i]['utilidad_neta'];
			$tpl->assign("diferencia",(round($diferencia,2) != 0) ? "<font color=\"#".($diferencia > 0 ? "0000FF" : "FF0000")."\">".number_format($diferencia,2,".",",")."</font>" : "&nbsp;");
			
			$ing += $ingresos[0]['sum'];
			$egr += $egresos[0]['sum'];
			$total_caja += $total_gastos_caja;
			$dep += $depositos[0]['sum'];
			$odep += $otros_dep[0]['sum'];
			$gen += $general;
			$dif += $diferencia;
			
			// [11-Feb-2009] Obtener saldo en bancos inicial del mes
			$sql = "SELECT sum(saldo_libros) AS saldo FROM his_sal_ban WHERE num_cia = $num_cia AND fecha < '$fecha1' GROUP BY fecha ORDER BY fecha DESC LIMIT 1";
			$tmp = ejecutar_script($sql,$dsn);
			$saldo_ini = $tmp ? round($tmp[0]['saldo'], 2) : 0;
			
			// [11-Feb-2009] Obtener saldo en bancos final del mes
			$sql = "SELECT sum(saldo_libros) AS saldo FROM his_sal_ban WHERE num_cia = $num_cia AND fecha <= '$fecha2' GROUP BY fecha ORDER BY fecha DESC LIMIT 1";
			$tmp = ejecutar_script($sql,$dsn);
			$saldo_fin = $tmp ? round($tmp[0]['saldo'], 2) : 0;
			
			// [11-Feb-2009] Obtener saldo a proveedores inicial del mes
			$sql = "SELECT saldo FROM his_sal_pro WHERE num_cia = $num_cia AND fecha < '$fecha1' ORDER BY fecha DESC LIMIT 1";
			$tmp = ejecutar_script($sql,$dsn);
			$saldo_pro_ini = $tmp ? round($tmp[0]['saldo'], 2) : 0;
			
			// [11-Feb-2009] Obtener saldo a proveedores final del mes
			$sql = "SELECT saldo FROM his_sal_pro WHERE num_cia = $num_cia AND fecha <= '$fecha2' ORDER BY fecha DESC LIMIT 1";
			$tmp = ejecutar_script($sql,$dsn);
			$saldo_pro_fin = $tmp ? round($tmp[0]['saldo'], 2) : 0;
			
			$tpl->assign('saldo_ini', $saldo_ini != 0 ? number_format($saldo_ini, 2, '.', ',') : '&nbsp;');
			$tpl->assign('saldo_fin', $saldo_fin != 0 ? number_format($saldo_fin, 2, '.', ',') : '&nbsp;');
			$tpl->assign('saldo_pro_ini', $saldo_pro_ini != 0 ? number_format($saldo_pro_ini, 2, '.', ',') : '&nbsp;');
			$tpl->assign('saldo_pro_fin', $saldo_pro_fin != 0 ? number_format($saldo_pro_fin, 2, '.', ',') : '&nbsp;');
			
			if ($sal_ini == NULL)
				$sal_ini = $saldo_ini;
			
			$sal_fin = $saldo_fin;
			
			if ($sal_pro_ini == NULL)
				$sal_pro_ini = $saldo_ini;
			
			$sal_pro_fin = $saldo_pro_fin;
			
			// [12-Feb-2009] Obtener gastos no incluidos
			$sql = "SELECT sum(importe) AS importe FROM movimiento_gastos g LEFT JOIN catalogo_gastos cg USING (codgastos) WHERE num_cia = $num_cia AND fecha BETWEEN '$fecha1' AND '$fecha2' AND codigo_edo_resultados = 0 AND codgastos NOT IN (33, 134)";
			$tmp = ejecutar_script($sql, $dsn);
			$g = $tmp ? $tmp[0]['importe'] : 0;
			
			// [12-Feb-2009] Obtener gastos de caja no incluidos
			$sql = "SELECT sum(CASE WHEN tipo_mov = 'FALSE' THEN importe ELSE -importe END) AS importe FROM gastos_caja WHERE num_cia = $num_cia AND fecha BETWEEN '$fecha1' AND '$fecha2' AND clave_balance = 'FALSE'";
			$tmp = ejecutar_script($sql, $dsn);
			$gc = $tmp ? $tmp[0]['importe'] : 0;
			
			// [12-Feb-2009] No incluidos
			$no_inc = $g + $gc;
			$tpl->assign('no_inc', $no_inc != 0 ? number_format($no_inc, 2, '.', ',') : '&nbsp;');
			
			$gas_no_inc += $no_inc;
			
			// [17-Jun-2009] Dif = Diferencia - Saldo Inicial + Saldo Final - Saldo Pro. Inicial + Saldo Pro. Final + No Incluidos
			$otra_dif = $diferencia - $saldo_ini + $saldo_fin - $saldo_pro_ini + $saldo_pro_fin + $no_inc;
			$tpl->assign('dif', number_format($otra_dif, 2, '.', ','));
			$total_otra_dif += $otra_dif;
			
			$num_meses++;
			
			$venta += $result[$i]['venta'];
			$otros += $result[$i]['otros'];
			$ventas_netas += $result[$i]['ventas_netas'];
			$inv_ant += $result[$i]['inv_ant'];
			$compras += $result[$i]['compras'];
			$mercancias += $result[$i]['mercancias'];
			$inv_act += $result[$i]['inv_act'];
			$mat_prima_utilizada += $result[$i]['mat_prima_utilizada'];
			$gastos_fab += $result[$i]['gastos_fab'];
			$costo_produccion += $result[$i]['costo_produccion'];
			$utilidad_bruta += $result[$i]['utilidad_bruta'];
			$gastos_generales += $result[$i]['gastos_generales'];
			$gastos_caja += $result[$i]['gastos_caja'];
			$reserva_aguinaldos += $result[$i]['reserva_aguinaldos'];
			$gastos_otras_cias += $result[$i]['gastos_otras_cias'];
			$total_gastos += $result[$i]['total_gastos'];
			$ingresos_ext += $result[$i]['ingresos_ext'];
			$utilidad_neta += $result[$i]['utilidad_neta'];
			$mp_vtas += $result[$i]['mp_vtas'];
			$efectivo += $result[$i]['efectivo'];
			$pollos_vendidos += $result[$i]['pollos_vendidos'];
			$p_pavo += $result[$i]['p_pavo'];
		}
		$numcols++;
	}
	if ($num_cia != NULL) {
		if ($numcols == $numcols_x_hoja) {
			$tpl->newBlock($num_cia <= 300 || $num_cia == 703 ? "hoja_pan" : "hoja_ros");
			$tpl->assign("num_cia",$num_cia);
			$tpl->assign("nombre_cia",$nombre[0]['nombre']);
			$tpl->assign("anio",$_GET['anio']);
			
			$numcols = 0;
		}
		
		// COLUMNA DE TOTALES
		$tpl->newBlock($num_cia <= 300 || $num_cia == 703 ? "col_pan" : "col_ros");
		$tpl->assign("mes","Total");
		if ($num_cia <= 300 ||$num_cia == 703) {
			$tpl->assign("venta_puerta",(round($venta_puerta,2) != 0) ? "<font color=\"#".($venta_puerta > 0 ? "0000FF" : "FF0000")."\">".number_format($venta_puerta,2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("bases",(round($bases,2) != 0) ? "<font color=\"#".($bases > 0 ? "0000FF" : "FF0000")."\">".number_format($bases,2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("barredura",(round($barredura,2) != 0) ? "<font color=\"#".($barredura > 0 ? "0000FF" : "FF0000")."\">".number_format($barredura,2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("pastillaje",(round($pastillaje,2) != 0) ? "<font color=\"#".($pastillaje > 0 ? "0000FF" : "FF0000")."\">".number_format($pastillaje,2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("abono_emp",(round($abono_emp,2) != 0) ? "<font color=\"#".($abono_emp > 0 ? "0000FF" : "FF0000")."\">".number_format($abono_emp,2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("otros",(round($otros,2) != 0) ? "<font color=\"#".($otros > 0 ? "0000FF" : "FF0000")."\">".number_format($otros,2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("total_otros",(round($total_otros,2) != 0) ? "<font color=\"#".($total_otros > 0 ? "0000FF" : "FF0000")."\">".number_format($total_otros,2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("abono_reparto",(round($abono_reparto,2) != 0) ? "<font color=\"#".($abono_reparto > 0 ? "0000FF" : "FF0000")."\">".number_format($abono_reparto,2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("errores",(round($errores,2) != 0) ? "<font color=\"#".($errores > 0 ? "0000FF" : "FF0000")."\">".number_format($errores,2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("ventas_netas",(round($ventas_netas,2) != 0) ? "<font color=\"#".($ventas_netas > 0 ? "0000FF" : "FF0000")."\">".number_format($ventas_netas,2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("inventario_anterior",(round($inv_ant,2) != 0) ? "<font color=\"#".($inv_ant > 0 ? "0000FF" : "FF0000")."\">".number_format($inv_ant,2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("compras",(round($compras,2) != 0) ? "<font color=\"#".($compras > 0 ? "0000FF" : "FF0000")."\">".number_format($compras,2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("mercancias",(round($mercancias,2) != 0) ? "<font color=\"#".($mercancias > 0 ? "0000FF" : "FF0000")."\">".number_format($mercancias,2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("inventario_actual",(round($inv_act,2) != 0) ? "<font color=\"#".($inv_act > 0 ? "0000FF" : "FF0000")."\">".number_format($inv_act,2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("mat_prima_utilizada",(round($mat_prima_utilizada,2) != 0) ? "<font color=\"#".($mat_prima_utilizada > 0 ? "0000FF" : "FF0000")."\">".number_format($mat_prima_utilizada,2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("mano_obra",(round($mano_obra,2) != 0) ? "<font color=\"#".($mano_obra > 0 ? "0000FF" : "FF0000")."\">".number_format($mano_obra,2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("panaderos",(round($panaderos,2) != 0) ? "<font color=\"#".($panaderos > 0 ? "0000FF" : "FF0000")."\">".number_format($panaderos,2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("gastos_fabricacion",(round($gastos_fab,2) != 0) ? "<font color=\"#".($gastos_fab > 0 ? "0000FF" : "FF0000")."\">".number_format($gastos_fab,2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("costo_produccion",(round($costo_produccion,2) != 0) ? "<font color=\"#".($costo_produccion > 0 ? "0000FF" : "FF0000")."\">".number_format($costo_produccion,2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("utilidad_bruta",(round($utilidad_bruta,2) != 0) ? "<font color=\"#".($utilidad_bruta > 0 ? "0000FF" : "FF0000")."\">".number_format($utilidad_bruta,2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("pan_comprado",(round($pan_comprado,2) != 0) ? "<font color=\"#".($pan_comprado > 0 ? "0000FF" : "FF0000")."\">".number_format($pan_comprado,2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("gastos_generales",(round($gastos_generales,2) != 0) ? "<font color=\"#".($gastos_generales > 0 ? "0000FF" : "FF0000")."\">".number_format($gastos_generales,2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("gastos_caja",(round($gastos_caja,2) != 0) ? "<font color=\"#".($gastos_caja > 0 ? "0000FF" : "FF0000")."\">".number_format($gastos_caja,2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("reserva_aguinaldos",(round($reserva_aguinaldos,2) != 0) ? "<font color=\"#".($reserva_aguinaldos > 0 ? "0000FF" : "FF0000")."\">".number_format($reserva_aguinaldos,2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("gastos_otras_cias",(round($gastos_otras_cias,2) != 0) ? "<font color=\"#".($gastos_otras_cias > 0 ? "0000FF" : "FF0000")."\">".number_format($gastos_otras_cias,2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("total_gastos",(round($total_gastos,2) != 0) ? "<font color=\"#".($total_gastos > 0 ? "0000FF" : "FF0000")."\">".number_format($total_gastos,2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("ingresos_ext",(round($ingresos_ext,2) != 0) ? "<font color=\"#".($ingresos_ext > 0 ? "0000FF" : "FF0000")."\">".number_format($ingresos_ext,2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("utilidad_mes",(round($utilidad_neta,2) != 0) ? "<font color=\"#".($utilidad_neta > 0 ? "0000FF" : "FF0000")."\">".number_format($utilidad_neta,2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("mp_vtas",(round($mp_vtas,3) != 0) ? "<font color=\"#".($mp_vtas > 0 ? "0000FF" : "FF0000")."\">".number_format($mp_vtas,3,".",",")."</font>" : "&nbsp;");
			$tpl->assign("utilidad_produccion",(round($utilidad_pro,2) != 0) ? "<font color=\"#".($utilidad_pro > 0 ? "0000FF" : "FF0000")."\">".number_format($utilidad_pro,3,".",",")."</font>" : "&nbsp;");
			$tpl->assign("mp_produccion",(round($mp_pro,3) != 0) ? "<font color=\"#".($mp_pro > 0 ? "0000FF" : "FF0000")."\">".number_format($mp_pro,3,".",",")."</font>" : "&nbsp;");
			$tpl->assign("produccion_total",(round($produccion_total,2) != 0) ? "<font color=\"#".($produccion_total > 0 ? "0000FF" : "FF0000")."\">".number_format($produccion_total,2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("faltante_pan",(round($faltante_pan,2) != 0) ? "<font color=\"#".($faltante_pan > 0 ? "0000FF" : "FF0000")."\">".number_format($faltante_pan,2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("rezago_inicial",(round($rezago_ini,2) != 0) ? "<font color=\"#".($rezago_ini > 0 ? "0000FF" : "FF0000")."\">".number_format($rezago_ini,2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("rezago_final",(round($rezago_fin,2) != 0) ? "<font color=\"#".($rezago_fin > 0 ? "0000FF" : "FF0000")."\">".number_format($rezago_fin,2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("subio_rezago",(round($var_rezago,2) != 0) ? "<font color=\"#".($var_rezago > 0 ? "0000FF" : "FF0000")."\">".number_format($var_rezago,2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("efectivo",(round($efectivo,2) != 0) ? "<font color=\"#".($efectivo > 0 ? "0000FF" : "FF0000")."\">".number_format($efectivo,2,".",",")."</font>" : "&nbsp;");
			
			$tpl->assign("ingresos",(round($ing,2) != 0) ? "<font color=\"#".($ing > 0 ? "0000FF" : "FF0000")."\">".number_format($ing,2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("egresos",(round($egr,2) != 0) ? "<font color=\"#".($egr > 0 ? "0000FF" : "FF0000")."\">".number_format($egr,2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("gastos_caja_total",(round($total_caja,2) != 0) ? "<font color=\"#".($total_caja > 0 ? "0000FF" : "FF0000")."\">".number_format($total_caja,2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("depositos",(round($dep,2) != 0) ? "<font color=\"#".($dep > 0 ? "0000FF" : "FF0000")."\">".number_format($dep,2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("otros_depositos",(round($odep,2) != 0) ? "<font color=\"#".($odep > 0 ? "0000FF" : "FF0000")."\">".number_format($odep,2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("general",(round($gen,2) != 0) ? "<font color=\"#".($gen > 0 ? "0000FF" : "FF0000")."\">".number_format($gen,2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("diferencia",(round($dif,2) != 0) ? "<font color=\"#".($dif > 0 ? "0000FF" : "FF0000")."\">".number_format($dif,2,".",",")."</font>" : "&nbsp;");
			
			$tpl->assign('saldo_ini', $sal_ini != 0 ? number_format($sal_ini, 2, '.', ',') : '&nbsp;');
			$tpl->assign('saldo_fin', $sal_fin != 0 ? number_format($sal_fin, 2, '.', ',') : '&nbsp;');
			$tpl->assign('saldo_pro_ini', $sal_pro_ini != 0 ? number_format($sal_pro_ini, 2, '.', ',') : '&nbsp;');
			$tpl->assign('saldo_pro_fin', $sal_pro_fin != 0 ? number_format($sal_pro_fin, 2, '.', ',') : '&nbsp;');
			$tpl->assign('no_inc', $gas_no_inc != 0 ? number_format($gas_no_inc, 2, '.', ',') : '&nbsp;');
			$tpl->assign('dif', $total_otra_dif != 0 ? number_format($total_otra_dif, 2, '.', ',') : '&nbsp;');
		}
		else {
			$tpl->assign("venta",(round($venta,2) != 0) ? "<font color=\"#".($venta > 0 ? "0000FF" : "FF0000")."\">".number_format($venta,2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("otros",(round($otros,2) != 0) ? "<font color=\"#".($otros > 0 ? "0000FF" : "FF0000")."\">".number_format($otros,2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("ventas_netas",(round($ventas_netas,2) != 0) ? "<font color=\"#".($ventas_netas > 0 ? "0000FF" : "FF0000")."\">".number_format($ventas_netas,2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("inventario_anterior",(round($inv_ant,2) != 0) ? "<font color=\"#".($inv_ant > 0 ? "0000FF" : "FF0000")."\">".number_format($inv_ant,2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("compras",(round($compras,2) != 0) ? "<font color=\"#".($compras > 0 ? "0000FF" : "FF0000")."\">".number_format($compras,2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("mercancias",(round($mercancias,2) != 0) ? "<font color=\"#".($mercancias > 0 ? "0000FF" : "FF0000")."\">".number_format($mercancias,2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("inventario_actual",(round($inv_act,2) != 0) ? "<font color=\"#".($inv_act > 0 ? "0000FF" : "FF0000")."\">".number_format($inv_act,2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("mat_prima_utilizada",(round($mat_prima_utilizada,2) != 0) ? "<font color=\"#".($mat_prima_utilizada > 0 ? "0000FF" : "FF0000")."\">".number_format($mat_prima_utilizada,2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("gastos_fabricacion",(round($gastos_fab,2) != 0) ? "<font color=\"#".($gastos_fab > 0 ? "0000FF" : "FF0000")."\">".number_format($gastos_fab,2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("costo_produccion",(round($costo_produccion,2) != 0) ? "<font color=\"#".($costo_produccion > 0 ? "0000FF" : "FF0000")."\">".number_format($costo_produccion,2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("utilidad_bruta",(round($utilidad_bruta,2) != 0) ? "<font color=\"#".($utilidad_bruta > 0 ? "0000FF" : "FF0000")."\">".number_format($utilidad_bruta,2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("gastos_generales",(round($gastos_generales,2) != 0) ? "<font color=\"#".($gastos_generales > 0 ? "0000FF" : "FF0000")."\">".number_format($gastos_generales,2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("gastos_caja",(round($gastos_caja,2) != 0) ? "<font color=\"#".($gastos_caja > 0 ? "0000FF" : "FF0000")."\">".number_format($gastos_caja,2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("reserva_aguinaldos",(round($reserva_aguinaldos,2) != 0) ? "<font color=\"#".($reserva_aguinaldos > 0 ? "0000FF" : "FF0000")."\">".number_format($reserva_aguinaldos,2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("gastos_otras_cias",(round($gastos_otras_cias,2) != 0) ? "<font color=\"#".($gastos_otras_cias > 0 ? "0000FF" : "FF0000")."\">".number_format($gastos_otras_cias,2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("total_gastos",(round($total_gastos,2) != 0) ? "<font color=\"#".($total_gastos > 0 ? "0000FF" : "FF0000")."\">".number_format($total_gastos,2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("ingresos_ext",(round($ingresos_ext,2) != 0) ? "<font color=\"#".($ingresos_ext > 0 ? "0000FF" : "FF0000")."\">".number_format($ingresos_ext,2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("utilidad_mes",(round($utilidad_neta,2) != 0) ? "<font color=\"#".($utilidad_neta > 0 ? "0000FF" : "FF0000")."\">".number_format($utilidad_neta,2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("mp_vtas",(round($mp_vtas,2) != 0) ? "<font color=\"#".($mp_vtas > 0 ? "0000FF" : "FF0000")."\">".number_format($mp_vtas,2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("efectivo",(round($efectivo,2) != 0) ? "<font color=\"#".($efectivo > 0 ? "0000FF" : "FF0000")."\">".number_format($efectivo,2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("pollos_vendidos",(round($pollos_vendidos,2) != 0) ? "<font color=\"#".($pollos_vendidos > 0 ? "0000FF" : "FF0000")."\">".number_format($pollos_vendidos,2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("p_pavo",(round($p_pavo,2) != 0) ? "<font color=\"#".($p_pavo > 0 ? "0000FF" : "FF0000")."\">".number_format($p_pavo,2,".",",")."</font>" : "&nbsp;");
			
			$tpl->assign("ingresos",(round($ing,2) != 0) ? "<font color=\"#".($ing > 0 ? "0000FF" : "FF0000")."\">".number_format($ing,2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("egresos",(round($egr,2) != 0) ? "<font color=\"#".($egr > 0 ? "0000FF" : "FF0000")."\">".number_format($egr,2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("gastos_caja_total",(round($total_caja,2) != 0) ? "<font color=\"#".($total_caja > 0 ? "0000FF" : "FF0000")."\">".number_format($total_caja,2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("depositos",(round($dep,2) != 0) ? "<font color=\"#".($dep > 0 ? "0000FF" : "FF0000")."\">".number_format($dep,2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("otros_depositos",(round($odep,2) != 0) ? "<font color=\"#".($odep > 0 ? "0000FF" : "FF0000")."\">".number_format($odep,2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("general",(round($gen,2) != 0) ? "<font color=\"#".($gen > 0 ? "0000FF" : "FF0000")."\">".number_format($gen,2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("diferencia",(round($dif,2) != 0) ? "<font color=\"#".($dif > 0 ? "0000FF" : "FF0000")."\">".number_format($dif,2,".",",")."</font>" : "&nbsp;");
			
			$tpl->assign('saldo_ini', $sal_ini != 0 ? number_format($sal_ini, 2, '.', ',') : '&nbsp;');
			$tpl->assign('saldo_fin', $sal_fin != 0 ? number_format($sal_fin, 2, '.', ',') : '&nbsp;');
			$tpl->assign('saldo_pro_ini', $sal_pro_ini != 0 ? number_format($sal_pro_ini, 2, '.', ',') : '&nbsp;');
			$tpl->assign('saldo_pro_fin', $sal_pro_fin != 0 ? number_format($sal_pro_fin, 2, '.', ',') : '&nbsp;');
			$tpl->assign('no_inc', $gas_no_inc != 0 ? number_format($gas_no_inc, 2, '.', ',') : '&nbsp;');
			$tpl->assign('dif', $total_otra_dif != 0 ? number_format($total_otra_dif, 2, '.', ',') : '&nbsp;');
		}
		$numcols++;
		
		if ($numcols == $numcols_x_hoja) {
			$tpl->newBlock($num_cia <= 300 || $num_cia == 703 ? "hoja_pan" : "hoja_ros");
			$tpl->assign("num_cia",$num_cia);
			$tpl->assign("nombre_cia",$nombre[0]['nombre']);
			$tpl->assign("anio",$_GET['anio']);
			
			$numcols = 0;
		}
		
		// COLUMNA DE PROMEDIOS
		$tpl->newBlock($num_cia <= 300 || $num_cia == 703 ? "col_pan" : "col_ros");
		$tpl->assign("mes","Promedio");
		if ($num_cia < 100 || $num_cia == 703) {
			$tpl->assign("venta_puerta",(round($venta_puerta/$num_meses,2) != 0) ? "<font color=\"#".($venta_puerta/$num_meses > 0 ? "0000FF" : "FF0000")."\">".number_format($venta_puerta/$num_meses,2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("bases",(round($bases/$num_meses,2) != 0) ? "<font color=\"#".($bases/$num_meses > 0 ? "0000FF" : "FF0000")."\">".number_format($bases/$num_meses,2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("barredura",(round($barredura/$num_meses,2) != 0) ? "<font color=\"#".($barredura/$num_meses > 0 ? "0000FF" : "FF0000")."\">".number_format($barredura/$num_meses,2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("pastillaje",(round($pastillaje/$num_meses,2) != 0) ? "<font color=\"#".($pastillaje/$num_meses > 0 ? "0000FF" : "FF0000")."\">".number_format($pastillaje/$num_meses,2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("abono_emp",(round($abono_emp/$num_meses,2) != 0) ? "<font color=\"#".($abono_emp/$num_meses > 0 ? "0000FF" : "FF0000")."\">".number_format($abono_emp/$num_meses,2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("otros",(round($otros/$num_meses,2) != 0) ? "<font color=\"#".($otros/$num_meses > 0 ? "0000FF" : "FF0000")."\">".number_format($otros/$num_meses,2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("total_otros",(round($total_otros/$num_meses,2) != 0) ? "<font color=\"#".($total_otros/$num_meses > 0 ? "0000FF" : "FF0000")."\">".number_format($total_otros/$num_meses,2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("abono_reparto",(round($abono_reparto/$num_meses,2) != 0) ? "<font color=\"#".($abono_reparto/$num_meses > 0 ? "0000FF" : "FF0000")."\">".number_format($abono_reparto/$num_meses,2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("errores",(round($errores/$num_meses,2) != 0) ? "<font color=\"#".($errores/$num_meses > 0 ? "0000FF" : "FF0000")."\">".number_format($errores/$num_meses,2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("ventas_netas",(round($ventas_netas/$num_meses,2) != 0) ? "<font color=\"#".($ventas_netas/$num_meses > 0 ? "0000FF" : "FF0000")."\">".number_format($ventas_netas/$num_meses,2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("inventario_anterior",(round($inv_ant/$num_meses,2) != 0) ? "<font color=\"#".($inv_ant/$num_meses > 0 ? "0000FF" : "FF0000")."\">".number_format($inv_ant/$num_meses,2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("compras",(round($compras/$num_meses,2) != 0) ? "<font color=\"#".($compras/$num_meses > 0 ? "0000FF" : "FF0000")."\">".number_format($compras/$num_meses,2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("mercancias",(round($mercancias/$num_meses,2) != 0) ? "<font color=\"#".($mercancias/$num_meses > 0 ? "0000FF" : "FF0000")."\">".number_format($mercancias/$num_meses,2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("inventario_actual",(round($inv_act/$num_meses,2) != 0) ? "<font color=\"#".($inv_act/$num_meses > 0 ? "0000FF" : "FF0000")."\">".number_format($inv_act/$num_meses,2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("mat_prima_utilizada",(round($mat_prima_utilizada/$num_meses,2) != 0) ? "<font color=\"#".($mat_prima_utilizada/$num_meses > 0 ? "0000FF" : "FF0000")."\">".number_format($mat_prima_utilizada/$num_meses,2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("mano_obra",(round($mano_obra/$num_meses,2) != 0) ? "<font color=\"#".($mano_obra/$num_meses > 0 ? "0000FF" : "FF0000")."\">".number_format($mano_obra/$num_meses,2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("panaderos",(round($panaderos/$num_meses,2) != 0) ? "<font color=\"#".($panaderos/$num_meses > 0 ? "0000FF" : "FF0000")."\">".number_format($panaderos/$num_meses,2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("gastos_fabricacion",(round($gastos_fab/$num_meses,2) != 0) ? "<font color=\"#".($gastos_fab/$num_meses > 0 ? "0000FF" : "FF0000")."\">".number_format($gastos_fab/$num_meses,2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("costo_produccion",(round($costo_produccion/$num_meses,2) != 0) ? "<font color=\"#".($costo_produccion/$num_meses > 0 ? "0000FF" : "FF0000")."\">".number_format($costo_produccion/$num_meses,2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("utilidad_bruta",(round($utilidad_bruta/$num_meses,2) != 0) ? "<font color=\"#".($utilidad_bruta/$num_meses > 0 ? "0000FF" : "FF0000")."\">".number_format($utilidad_bruta/$num_meses,2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("pan_comprado",(round($pan_comprado/$num_meses,2) != 0) ? "<font color=\"#".($pan_comprado/$num_meses > 0 ? "0000FF" : "FF0000")."\">".number_format($pan_comprado/$num_meses,2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("gastos_generales",(round($gastos_generales/$num_meses,2) != 0) ? "<font color=\"#".($gastos_generales/$num_meses > 0 ? "0000FF" : "FF0000")."\">".number_format($gastos_generales/$num_meses,2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("gastos_caja",(round($gastos_caja/$num_meses,2) != 0) ? "<font color=\"#".($gastos_caja/$num_meses > 0 ? "0000FF" : "FF0000")."\">".number_format($gastos_caja/$num_meses,2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("reserva_aguinaldos",(round($reserva_aguinaldos/$num_meses,2) != 0) ? "<font color=\"#".($reserva_aguinaldos/$num_meses > 0 ? "0000FF" : "FF0000")."\">".number_format($reserva_aguinaldos/$num_meses,2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("gastos_otras_cias",(round($gastos_otras_cias/$num_meses,2) != 0) ? "<font color=\"#".($gastos_otras_cias/$num_meses > 0 ? "0000FF" : "FF0000")."\">".number_format($gastos_otras_cias/$num_meses,2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("total_gastos",(round($total_gastos/$num_meses,2) != 0) ? "<font color=\"#".($total_gastos/$num_meses > 0 ? "0000FF" : "FF0000")."\">".number_format($total_gastos/$num_meses,2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("ingresos_ext",(round($ingresos_ext/$num_meses,2) != 0) ? "<font color=\"#".($ingresos_ext/$num_meses > 0 ? "0000FF" : "FF0000")."\">".number_format($ingresos_ext/$num_meses,2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("utilidad_mes",(round($utilidad_neta/$num_meses,2) != 0) ? "<font color=\"#".($utilidad_neta/$num_meses > 0 ? "0000FF" : "FF0000")."\">".number_format($utilidad_neta/$num_meses,2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("mp_vtas",(round($mp_vtas/$num_meses,3) != 0) ? "<font color=\"#".($mp_vtas/$num_meses > 0 ? "0000FF" : "FF0000")."\">".number_format($mp_vtas/$num_meses,3,".",",")."</font>" : "&nbsp;");
			$tpl->assign("utilidad_produccion",(round($utilidad_pro/$num_meses,2) != 0) ? "<font color=\"#".($utilidad_pro/$num_meses > 0 ? "0000FF" : "FF0000")."\">".number_format($utilidad_pro/$num_meses,3,".",",")."</font>" : "&nbsp;");
			$tpl->assign("mp_produccion",(round($mp_pro/$num_meses,3) != 0) ? "<font color=\"#".($mp_pro/$num_meses > 0 ? "0000FF" : "FF0000")."\">".number_format($mp_pro/$num_meses,3,".",",")."</font>" : "&nbsp;");
			$tpl->assign("produccion_total",(round($produccion_total/$num_meses,2) != 0) ? "<font color=\"#".($produccion_total/$num_meses > 0 ? "0000FF" : "FF0000")."\">".number_format($produccion_total/$num_meses,2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("faltante_pan",(round($faltante_pan/$num_meses,2) != 0) ? "<font color=\"#".($faltante_pan/$num_meses > 0 ? "0000FF" : "FF0000")."\">".number_format($faltante_pan/$num_meses,2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("rezago_inicial",(round($rezago_ini/$num_meses,2) != 0) ? "<font color=\"#".($rezago_ini/$num_meses > 0 ? "0000FF" : "FF0000")."\">".number_format($rezago_ini/$num_meses,2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("rezago_final",(round($rezago_fin/$num_meses,2) != 0) ? "<font color=\"#".($rezago_fin/$num_meses > 0 ? "0000FF" : "FF0000")."\">".number_format($rezago_fin/$num_meses,2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("subio_rezago",(round($var_rezago/$num_meses,2) != 0) ? "<font color=\"#".($var_rezago/$num_meses > 0 ? "0000FF" : "FF0000")."\">".number_format($var_rezago/$num_meses,2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("efectivo",(round($efectivo/$num_meses,2) != 0) ? "<font color=\"#".($efectivo/$num_meses > 0 ? "0000FF" : "FF0000")."\">".number_format($efectivo/$num_meses,2,".",",")."</font>" : "&nbsp;");
			
			$tpl->assign("ingresos",(round($ing/$num_meses,2) != 0) ? "<font color=\"#".($ing/$num_meses > 0 ? "0000FF" : "FF0000")."\">".number_format($ing/$num_meses,2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("egresos",(round($egr/$num_meses,2) != 0) ? "<font color=\"#".($egr/$num_meses > 0 ? "0000FF" : "FF0000")."\">".number_format($egr/$num_meses,2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("gastos_caja_total",(round($total_caja/$num_meses,2) != 0) ? "<font color=\"#".($total_caja/$num_meses > 0 ? "0000FF" : "FF0000")."\">".number_format($total_caja/$num_meses,2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("depositos",(round($dep/$num_meses,2) != 0) ? "<font color=\"#".($dep/$num_meses > 0 ? "0000FF" : "FF0000")."\">".number_format($dep/$num_meses,2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("otros_depositos",(round($odep/$num_meses,2) != 0) ? "<font color=\"#".($odep/$num_meses > 0 ? "0000FF" : "FF0000")."\">".number_format($odep/$num_meses,2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("general",(round($gen/$num_meses,2) != 0) ? "<font color=\"#".($gen/$num_meses > 0 ? "0000FF" : "FF0000")."\">".number_format($gen/$num_meses,2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("diferencia",(round($dif/$num_meses,2) != 0) ? "<font color=\"#".($dif/$num_meses > 0 ? "0000FF" : "FF0000")."\">".number_format($dif/$num_meses,2,".",",")."</font>" : "&nbsp;");
			
			$tpl->assign('saldo_ini', '&nbsp;');
			$tpl->assign('saldo_fin', '&nbsp;');
			$tpl->assign('saldo_pro_ini', '&nbsp;');
			$tpl->assign('saldo_pro_fin', '&nbsp;');
			$tpl->assign('no_inc', '&nbsp;');
			$tpl->assign('dif', '&nbsp;');
		}
		else {
			$tpl->assign("venta",(round($venta/$num_meses,2) != 0) ? "<font color=\"#".($venta/$num_meses > 0 ? "0000FF" : "FF0000")."\">".number_format($venta/$num_meses,2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("otros",(round($otros/$num_meses,2) != 0) ? "<font color=\"#".($otros/$num_meses > 0 ? "0000FF" : "FF0000")."\">".number_format($otros/$num_meses,2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("ventas_netas",(round($ventas_netas/$num_meses,2) != 0) ? "<font color=\"#".($ventas_netas/$num_meses > 0 ? "0000FF" : "FF0000")."\">".number_format($ventas_netas/$num_meses,2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("inventario_anterior",(round($inv_ant/$num_meses,2) != 0) ? "<font color=\"#".($inv_ant/$num_meses > 0 ? "0000FF" : "FF0000")."\">".number_format($inv_ant/$num_meses,2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("compras",(round($compras/$num_meses,2) != 0) ? "<font color=\"#".($compras/$num_meses > 0 ? "0000FF" : "FF0000")."\">".number_format($compras/$num_meses,2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("mercancias",(round($mercancias/$num_meses,2) != 0) ? "<font color=\"#".($mercancias/$num_meses > 0 ? "0000FF" : "FF0000")."\">".number_format($mercancias/$num_meses,2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("inventario_actual",(round($inv_act/$num_meses,2) != 0) ? "<font color=\"#".($inv_act/$num_meses > 0 ? "0000FF" : "FF0000")."\">".number_format($inv_act/$num_meses,2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("mat_prima_utilizada",(round($mat_prima_utilizada/$num_meses,2) != 0) ? "<font color=\"#".($mat_prima_utilizada/$num_meses > 0 ? "0000FF" : "FF0000")."\">".number_format($mat_prima_utilizada/$num_meses,2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("gastos_fabricacion",(round($gastos_fab/$num_meses,2) != 0) ? "<font color=\"#".($gastos_fab/$num_meses > 0 ? "0000FF" : "FF0000")."\">".number_format($gastos_fab/$num_meses,2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("costo_produccion",(round($costo_produccion/$num_meses,2) != 0) ? "<font color=\"#".($costo_produccion/$num_meses > 0 ? "0000FF" : "FF0000")."\">".number_format($costo_produccion/$num_meses,2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("utilidad_bruta",(round($utilidad_bruta/$num_meses,2) != 0) ? "<font color=\"#".($utilidad_bruta/$num_meses > 0 ? "0000FF" : "FF0000")."\">".number_format($utilidad_bruta/$num_meses,2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("gastos_generales",(round($gastos_generales/$num_meses,2) != 0) ? "<font color=\"#".($gastos_generales/$num_meses > 0 ? "0000FF" : "FF0000")."\">".number_format($gastos_generales/$num_meses,2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("gastos_caja",(round($gastos_caja/$num_meses,2) != 0) ? "<font color=\"#".($gastos_caja/$num_meses > 0 ? "0000FF" : "FF0000")."\">".number_format($gastos_caja/$num_meses,2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("reserva_aguinaldos",(round($reserva_aguinaldos/$num_meses,2) != 0) ? "<font color=\"#".($reserva_aguinaldos/$num_meses > 0 ? "0000FF" : "FF0000")."\">".number_format($reserva_aguinaldos/$num_meses,2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("gastos_otras_cias",(round($gastos_otras_cias/$num_meses,2) != 0) ? "<font color=\"#".($gastos_otras_cias/$num_meses > 0 ? "0000FF" : "FF0000")."\">".number_format($gastos_otras_cias/$num_meses,2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("total_gastos",(round($total_gastos/$num_meses,2) != 0) ? "<font color=\"#".($total_gastos/$num_meses > 0 ? "0000FF" : "FF0000")."\">".number_format($total_gastos/$num_meses,2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("ingresos_ext",(round($ingresos_ext/$num_meses,2) != 0) ? "<font color=\"#".($ingresos_ext/$num_meses > 0 ? "0000FF" : "FF0000")."\">".number_format($ingresos_ext/$num_meses,2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("utilidad_mes",(round($utilidad_neta/$num_meses,2) != 0) ? "<font color=\"#".($utilidad_neta/$num_meses > 0 ? "0000FF" : "FF0000")."\">".number_format($utilidad_neta/$num_meses,2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("mp_vtas",(round($mp_vtas/$num_meses,2) != 0) ? "<font color=\"#".($mp_vtas/$num_meses > 0 ? "0000FF" : "FF0000")."\">".number_format($mp_vtas/$num_meses,2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("efectivo",(round($efectivo/$num_meses,2) != 0) ? "<font color=\"#".($efectivo/$num_meses > 0 ? "0000FF" : "FF0000")."\">".number_format($efectivo/$num_meses,2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("pollos_vendidos",(round($pollos_vendidos/$num_meses,2) != 0) ? "<font color=\"#".($pollos_vendidos/$num_meses > 0 ? "0000FF" : "FF0000")."\">".number_format($pollos_vendidos/$num_meses,2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("p_pavo",(round($p_pavo/$num_meses,2) != 0) ? "<font color=\"#".($p_pavo/$num_meses > 0 ? "0000FF" : "FF0000")."\">".number_format($p_pavo/$num_meses,2,".",",")."</font>" : "&nbsp;");
			
			$tpl->assign("ingresos",(round($ing/$num_meses,2) != 0) ? "<font color=\"#".($ing/$num_meses > 0 ? "0000FF" : "FF0000")."\">".number_format($ing/$num_meses,2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("egresos",(round($egr/$num_meses,2) != 0) ? "<font color=\"#".($egr/$num_meses > 0 ? "0000FF" : "FF0000")."\">".number_format($egr/$num_meses,2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("gastos_caja_total",(round($total_caja/$num_meses,2) != 0) ? "<font color=\"#".($total_caja/$num_meses > 0 ? "0000FF" : "FF0000")."\">".number_format($total_caja/$num_meses,2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("depositos",(round($dep/$num_meses,2) != 0) ? "<font color=\"#".($dep/$num_meses > 0 ? "0000FF" : "FF0000")."\">".number_format($dep/$num_meses,2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("otros_depositos",(round($odep/$num_meses,2) != 0) ? "<font color=\"#".($odep/$num_meses > 0 ? "0000FF" : "FF0000")."\">".number_format($odep/$num_meses,2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("general",(round($gen/$num_meses,2) != 0) ? "<font color=\"#".($gen/$num_meses > 0 ? "0000FF" : "FF0000")."\">".number_format($gen/$num_meses,2,".",",")."</font>" : "&nbsp;");
			$tpl->assign("diferencia",(round($dif/$num_meses,2) != 0) ? "<font color=\"#".($dif/$num_meses > 0 ? "0000FF" : "FF0000")."\">".number_format($dif/$num_meses,2,".",",")."</font>" : "&nbsp;");
			
			$tpl->assign('saldo_ini', '&nbsp;');
			$tpl->assign('saldo_fin', '&nbsp;');
			$tpl->assign('saldo_pro_ini', '&nbsp;');
			$tpl->assign('saldo_pro_fin', '&nbsp;');
			$tpl->assign('no_inc', '&nbsp;');
			$tpl->assign('dif', '&nbsp;');
		}
	}
	
	$tpl->printToScreen();
	die;
}

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/bal/bal_his_bal.tpl");
$tpl->prepare();

//Seleccionar el script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

// DATOS
$tpl->assign("anio");

$admins = ejecutar_script("SELECT idadministrador AS id, nombre_administrador AS admin FROM catalogo_administradores ORDER BY admin", $dsn);
foreach ($admins as $a) {
	$tpl->newBlock('admin');
	$tpl->assign('id', $a['id']);
	$tpl->assign('admin', $a['admin']);
}

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

// Imprimir el resultado
$tpl->printToScreen();
?>