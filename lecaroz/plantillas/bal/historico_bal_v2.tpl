<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Hist&oacute;rico de Balances</title>
<link href="../../styles/impresion.css" rel="stylesheet" type="text/css" />
<link href="./styles/impresion.css" rel="stylesheet" type="text/css" />
</head>

<body>
<!-- START BLOCK : hoja_pan -->
<table width="100%">
  <tr>
    <td width="20%" class="print_encabezado">{num_cia}</td>
    <td width="60%" align="center" class="print_encabezado">{nombre}</td>
    <td width="20%" class="rprint_encabezado">{num_cia}</td>
  </tr>
  <tr>
    <td colspan="3" align="center" class="print_encabezado">Hist&oacute;rico de Balances {anio} </td>
  </tr>
</table>
<br>
  <table cellpadding="0" cellspacing="0" class="print">
    <tr>
      <th class="print" scope="col">&nbsp;</th>
      <!-- START BLOCK : titulo_pan -->
	  <th class="print" scope="col">{titulo_pan}</th>
	  <!-- END BLOCK : titulo_pan -->
    </tr>
    <tr>
      <td class="vprint">+ Venta en Puerta </td>
	  <!-- START BLOCK : venta_puerta_pan -->
      <td class="rprint">{venta_puerta_pan}</td>
	  <!-- END BLOCK : venta_puerta_pan -->
    </tr>
    <tr>
      <td class="vprint">+ Bases</td>
      <!-- START BLOCK : bases_pan -->
	  <td class="rprint">{bases_pan}</td>
	  <!-- END BLOCK : bases_pan -->
    </tr>
    <tr>
      <td class="vprint">+ Barredura</td>
	  <!-- START BLOCK : barredura_pan -->
      <td class="rprint">{barredura_pan}</td>
	  <!-- END BLOCK : barredura_pan -->
    </tr>
    <tr>
      <td class="vprint">+ Pastillaje</td>
	  <!-- START BLOCK : pastillaje_pan -->
      <td class="rprint">{pastillaje_pan}</td>
	  <!-- END BLOCK : pastillaje_pan -->
    </tr>
    <tr>
      <td class="vprint">+ Abono Empleados </td>
	  <!-- START BLOCK : abono_emp_pan -->
      <td class="rprint">{abono_emp}</td>
	  <!-- END BLOCK : abono_emp_pan -->
    </tr>
    <tr>
      <td class="vprint">+ Otros</td>
	  <!-- START BLOCK : otros_pan -->
      <td class="rprint">{otros}</td>
	  <!-- END BLOCK : otros_pan -->
    </tr>
    <tr>
      <td class="vprint">= Total Otros </td>
	  <!-- START BLOCK : total_otros_pan -->
      <td class="rprint">{total_otros_pan}</td>
	  <!-- END BLOCK : total_otros_pan -->
    </tr>
    <tr>
      <td class="vprint">+ Abono Reparto</td>
	  <!-- START BLOCK : abono_reparto_pan -->
      <td class="rprint">{abono_reparto_pan}</td>
	  <!-- END BLOCK : abono_reparto_pan -->
    </tr>
    <tr>
      <td class="vprint">- Menos Errores </td>
      <!-- START BLOCK : errores_pan -->
	  <td class="rprint">{errores_pan}</td>
	  <!-- END BLOCK : errores_pan -->
    </tr>
    <tr>
      <td class="vprint" style="font-weight:bold;font-size:10pt;">= Ventas Netas</td>
	  <!-- START BLOCK : ventas_netas_pan -->
      <td class="rprint" style="font-weight:bold;font-size:10pt;">{ventas_netas_pan}</td>
	  <!-- END BLOCK : ventas_netas_pan -->
    </tr>
    <tr>
      <td class="vprint">&nbsp;</td>
      <!-- START BLOCK : blank1_pan -->
	  <td class="rprint">&nbsp;</td>
	  <!-- END BLOCK : blank1_pan -->
    </tr>
    <tr>
      <td class="vprint">+ Inventario Anterior</td>
      <!-- START BLOCK : inv_ant_pan -->
	  <td class="rprint">{inv_ant_pan}</td>
	  <!-- END BLOCK : inv_ant_pan -->
    </tr>
    <tr>
      <td class="vprint">+ Compras </td>
      <!-- START BLOCK : compras_pan -->
	  <td class="rprint">{compras_pan}</td>
	  <!-- END BLOCK : compras_pan -->
    </tr>
    <tr>
      <td class="vprint">+ Mercancias </td>
      <!-- START BLOCK : mercancias_pan -->
	  <td class="rprint">{mercancias_pan}</td>
	  <!-- END BLOCK : mercancias_pan -->
    </tr>
    <tr>
      <td class="vprint">- Inventario Actual </td>
	  <!-- START BLOCK : inv_act_pan -->
      <td class="rprint">{inv_act_pan}</td>
	  <!-- END BLOCK : inv_act_pan -->
    </tr>
    <tr>
      <td class="vprint">= Mat. Prima Utilizada </td>
	  <!-- START BLOCK : mat_prima_utilizada_pan -->
      <td class="rprint">{mat_prima_utilizada_pan}</td>
	  <!-- END BLOCK : mat_prima_utilizada_pan -->
    </tr>
    <tr>
      <td class="vprint">+ Mano de Obra </td>
	  <!-- START BLOCK : mano_obra_pan -->
      <td class="rprint">{mano_obra_pan}</td>
	  <!-- END BLOCK : mano_obra_pan -->
    </tr>
    <tr>
      <td class="vprint">+ Panaderos</td>
	  <!-- START BLOCK : panaderos_pan -->
      <td class="rprint">{panaderos_pan}</td>
	  <!-- END BLOCK : panaderos_pan -->
    </tr>
    <tr>
      <td class="vprint">+ Gastos de Fabricaci&oacute;n </td>
	  <!-- START BLOCK : gastos_fab_pan -->
      <td class="rprint">{gastos_fab_pan}</td>
	  <!-- END BLOCK : gastos_fab_pan -->
    </tr>
    <tr>
      <td class="vprint" style="font-weight:bold;font-size:10pt;">= Costo de Producci&oacute;n</td>
	  <!-- START BLOCK : costo_produccion_pan -->
      <td class="rprint" style="font-weight:bold;font-size:10pt;">{costo_produccion_pan}</td>
	  <!-- END BLOCK : costo_produccion_pan -->
    </tr>
    <tr>
      <td class="vprint">&nbsp;</td>
	  <!-- START BLOCK : blank2_pan -->
      <td class="rprint">&nbsp;</td>
	  <!-- END BLOCK : blank2_pan -->
    </tr>
    <tr>
      <td class="vprint" style="font-weight:bold;font-size:10pt;">Utilidad Bruta</td>
	  <!-- START BLOCK : utilidad_bruta_pan -->
      <td class="rprint" style="font-weight:bold;font-size:10pt;">{utilidad_bruta_pan}</td>
	  <!-- END BLOCK : utilidad_bruta_pan -->
    </tr>
    <tr>
      <td class="vprint">&nbsp;</td>
	  <!-- START BLOCK : blank3_pan -->
      <td class="rprint">&nbsp;</td>
	  <!-- END BLOCK : blank3_pan -->
    </tr>
    <tr>
      <td class="vprint">- Pan Comprado</td>
      <!-- START BLOCK : pan_comprado_pan -->
	  <td class="rprint">{pan_comprado_pan}</td>
	  <!-- END BLOCK : pan_comprado_pan -->
    </tr>
    <tr>
      <td class="vprint">- Gastos Generales </td>
	  <!-- START BLOCK : gastos_generales_pan -->
      <td class="rprint">{gastos_generales_pan}</td>
	  <!-- END BLOCK : gastos_generales_pan -->
    </tr>
    <tr>
      <td class="vprint">- Gastos por Caja </td>
	  <!-- START BLOCK : gastos_caja_pan -->
      <td class="rprint">{gastos_caja_pan}</td>
	  <!-- END BLOCK : gastos_caja_pan -->
    </tr>
    <tr>
      <td class="vprint">- Comisiones Bancarias </td>
      <!-- START BLOCK : comisiones_pan -->
	  <td class="rprint">{comisiones_pan}</td>
	  <!-- END BLOCK : comisiones_pan -->
    </tr>
    <tr>
      <td class="vprint">- Reservas</td>
	  <!-- START BLOCK : reserva_aguinaldos_pan -->
      <td class="rprint">{reserva_aguinaldos_pan}</td>
	  <!-- END BLOCK : reserva_aguinaldos_pan -->
    </tr>
    <tr>
      <td class="vprint">- Pagos Anticipados </td>
      <!-- START BLOCK : pagos_anticipados_pan -->
	  <td class="rprint">{pagos_anticipados_pan}</td>
	  <!-- END BLOCK : pagos_anticipados_pan -->
    </tr>
    <tr>
      <td class="vprint">- Gastos Pagados por Otras </td>
      <!-- START BLOCK : gastos_otras_cias_pan -->
	  <td class="rprint">{gastos_otras_cias_pan}</td>
	  <!-- END BLOCK : gastos_otras_cias_pan -->
    </tr>
    <tr>
      <td class="vprint" style="font-weight:bold;font-size:10pt;">= Total de Gastos</td>
	  <!-- START BLOCK : total_gastos_pan -->
      <td class="rprint" style="font-weight:bold;font-size:10pt;">{total_gastos_pan}</td>
	  <!-- END BLOCK : total_gastos_pan -->
    </tr>
    <tr>
      <td class="vprint">&nbsp;</td>
      <!-- START BLOCK : blank4_pan -->
	  <td class="rprint">&nbsp;</td>
	  <!-- END BLOCK : blank4_pan -->
    </tr>
    <tr>
      <td class="vprint" style="font-weight:bold;font-size:10pt;">+ Ingresos Extraordinarios</td>
	  <!-- START BLOCK : ingresos_ext_pan -->
      <td class="rprint" style="font-weight:bold;font-size:10pt;">{ingresos_ext_pan}</td>
	  <!-- END BLOCK : ingresos_ext_pan -->
    </tr>
    <!--<tr>
      <td class="vprint">&nbsp;</td>
	  <!-- START BLOCK : blank5_pan -->
      <td class="rprint">&nbsp;</td>
	  <!-- END BLOCK : blank5_pan -->
    </tr>-->
    <tr>
      <td class="vprint" style="font-weight:bold;font-size:10pt;">Utilidad del Mes </td>
	  <!-- START BLOCK : utilidad_neta_pan -->
      <td class="rprint" style="font-weight:bold;font-size:10pt;">{utilidad_neta_pan}</td>
	  <!-- END BLOCK : utilidad_neta_pan -->
    </tr>
    <tr>
      <td class="vprint">&nbsp;</td>
      <!-- START BLOCK : blank6_pan -->
	  <td class="rprint">&nbsp;</td>
	  <!-- END BLOCK : blank6_pan -->
    </tr>
    <tr>
      <td class="vprint">M. Prima / Vtas - Pan comprado </td>
	  <!-- START BLOCK : mp_vtas_pan -->
      <td class="rprint">{mp_vtas_pan}</td>
	  <!-- END BLOCK : mp_vtas_pan -->
    </tr>
    <tr>
      <td class="vprint">Utilidad entre Producci&oacute;n </td>
	  <!-- START BLOCK : utilidad_pro_pan -->
      <td class="rprint">{utilidad_pro_pan}</td>
	  <!-- END BLOCK : utilidad_pro_pan -->
    </tr>
    <tr>
      <td class="vprint">Mat. Prima entre Producci&oacute;n</td>
	  <!-- START BLOCK : mp_pro_pan -->
      <td class="rprint">{mp_pro_pan}</td>
	  <!-- END BLOCK : mp_pro_pan -->
    </tr>
    <tr>
      <td class="vprint">Gas / Producci&oacute;n </td>
	  <!-- START BLOCK : gas_pro_pan -->
      <td class="rprint">{gas_pro_pan}</td>
	  <!-- END BLOCK : gas_pro_pan -->
    </tr>
    <tr>
      <td class="vprint">&nbsp;</td>
	  <!-- START BLOCK : blank7_pan -->
      <td class="rprint">&nbsp;</td>
	  <!-- END BLOCK : blank7_pan -->
    </tr>
    <tr>
      <td class="vprint" style="font-weight:bold;font-size:10pt;">Producci&oacute;n Total </td>
      <!-- START BLOCK : produccion_total_pan -->
	  <td class="rprint" style="font-weight:bold;font-size:10pt;">{produccion_total_pan}</td>
	  <!-- END BLOCK : produccion_total_pan -->
    </tr>
    <tr>
      <td class="vprint" style="font-weight:bold;font-size:10pt;">Ganancia</td>
	  <!-- START BLOCK : ganancia_pan -->
      <td class="rprint" style="font-weight:bold;font-size:10pt;">{ganancia_pan}</td>
	  <!-- END BLOCK : ganancia_pan -->
    </tr>
    <tr>
      <td class="vprint" style="font-weight:bold;font-size:10pt;">% de Ganancia </td>
	  <!-- START BLOCK : porc_ganancia_pan -->
      <td class="rprint" style="font-weight:bold;font-size:10pt;">{porc_ganancia_pan}</td>
	  <!-- END BLOCK : porc_ganancia_pan -->
    </tr>
	<tr>
      <td class="vprint" style="font-weight:bold;font-size:10pt;">Faltante de Pan </td>
	  <!-- START BLOCK : faltante_pan_pan -->
      <td class="rprint" style="font-weight:bold;font-size:10pt;">{faltante_pan_pan}</td>
	  <!-- END BLOCK : faltante_pan_pan -->
    </tr>
	<tr>
	  <td class="vprint" style="font-weight:bold;font-size:10pt;">Devoluciones</td>
	  <!-- START BLOCK : devoluciones_pan -->
	  <td class="rprint" style="font-weight:bold;font-size:10pt;">{devoluciones_pan}</td>
	  <!-- END BLOCK : devoluciones_pan -->
    </tr>
    <tr>
      <td class="vprint" style="font-weight:bold;font-size:10pt;">Rezago Inicial </td>
      <!-- START BLOCK : rezago_ini_pan -->
	  <td class="rprint" style="font-weight:bold;font-size:10pt;">{rezago_ini_pan}</td>
	  <!-- END BLOCK : rezago_ini_pan -->
    </tr>
    <tr>
      <td class="vprint" style="font-weight:bold;font-size:10pt;">Rezago Final </td>
	  <!-- START BLOCK : rezago_fin_pan -->
      <td class="rprint" style="font-weight:bold;font-size:10pt;">{rezago_fin_pan}</td>
	  <!-- END BLOCK : rezago_fin_pan -->
    </tr>
    <tr>
      <td class="vprint" style="font-weight:bold;font-size:10pt;">Variaci&oacute;n del Rezago </td>
	  <!-- START BLOCK : var_rezago_pan -->
      <td class="rprint" style="font-weight:bold;font-size:10pt;">{var_rezago_pan}</td>
	  <!-- END BLOCK : var_rezago_pan -->
    </tr>
    <tr>
      <td class="vprint" style="font-weight:bold;font-size:10pt;">Efectivo</td>
	  <!-- START BLOCK : efectivo_pan -->
      <td class="rprint" style="font-weight:bold;font-size:10pt;">{efectivo_pan}</td>
	  <!-- END BLOCK : efectivo_pan -->
    </tr>
    <tr>
      <td class="vprint">&nbsp;</td>
	  <!-- START BLOCK : blank8_pan -->
      <td class="rprint">&nbsp;</td>
	  <!-- END BLOCK : blank8_pan -->
    </tr>
    <tr>
      <td class="vprint">Gastos de Caja (Ingresos) </td>
	  <!-- START BLOCK : ingresos_pan -->
      <td class="rprint">{ingresos_pan}</td>
	  <!-- END BLOCK : ingresos_pan -->
    </tr>
    <tr>
      <td class="vprint">Gastos de Caja (Egresos) </td>
	  <!-- START BLOCK : egresos_pan -->
      <td class="rprint">{egresos_pan}</td>
	  <!-- END BLOCK : egresos_pan -->
    </tr>
    <tr>
      <td class="vprint">Total de Gastos de Caja </td>
	  <!-- START BLOCK : total_gastos_caja_pan -->
      <td class="rprint">{total_gastos_caja_pan}</td>
	  <!-- END BLOCK : total_gastos_caja_pan -->
    </tr>
    <tr>
      <td class="vprint">Dep&oacute;sitos</td>
	  <!-- START BLOCK : depositos_pan -->
      <td class="rprint">{depositos_pan}</td>
	  <!-- END BLOCK : depositos_pan -->
    </tr>
    <tr>
      <td class="vprint">Otros Dep&oacute;sitos </td>
	  <!-- START BLOCK : otros_depositos_pan -->
      <td class="rprint">{otros_depositos_pan}</td>
	  <!-- END BLOCK : otros_depositos_pan -->
    </tr>
    <tr>
      <td class="vprint">General</td>
	  <!-- START BLOCK : general_pan -->
      <td class="rprint">{general_pan}</td>
	  <!-- END BLOCK : general_pan -->
    </tr>
    <tr>
      <td class="vprint">Diferencia</td>
	  <!-- START BLOCK : diferencia_pan -->
      <td class="rprint">{diferencia_pan}</td>
	  <!-- END BLOCK : diferencia_pan -->
    </tr>
    <!--<tr>
      <td class="vprint">&nbsp;</td>
	  <!-- START BLOCK : blank9_pan -->
      <td class="rprint">&nbsp;</td>
	  <!-- END BLOCK : blank9_pan -->
    </tr>-->
    <tr>
      <td class="vprint">Saldo Inicial</td>
	  <!-- START BLOCK : saldo_ini_pan -->
      <td class="rprint">{saldo_ini_pan}</td>
	  <!-- END BLOCK : saldo_ini_pan -->
    </tr>
    <tr>
      <td class="vprint">Saldo Final</td>
	  <!-- START BLOCK : saldo_fin_pan -->
      <td class="rprint">{saldo_fin_pan}</td>
	  <!-- END BLOCK : saldo_fin_pan -->
    </tr>
    <tr>
      <td class="vprint">Saldo Prov. Inicial</td>
	  <!-- START BLOCK : saldo_pro_ini_pan -->
      <td class="rprint">{saldo_pro_ini_pan}</td>
	  <!-- END BLOCK : saldo_pro_ini_pan -->
    </tr>
    <tr>
      <td class="vprint">Saldo Prov. Final</td>
	  <!-- START BLOCK : saldo_pro_fin_pan -->
      <td class="rprint">{saldo_pro_fin_pan}</td>
	  <!-- END BLOCK : saldo_pro_fin_pan -->
    </tr>
    <tr>
      <td class="vprint">No Incluidos</td>
	  <!-- START BLOCK : no_inc_pan -->
      <td class="rprint">{no_inc_pan}</td>
	  <!-- END BLOCK : no_inc_pan -->
    </tr>
    <tr>
      <td class="vprint">Dif. Reservas </td>
	  <!-- START BLOCK : dif_reservas_pan -->
      <td class="rprint">{dif_reservas_pan}</td>
	  <!-- END BLOCK : dif_reservas_pan -->
    </tr>
    <tr>
      <td class="vprint">Pagos anticipados </td>
    <!-- START BLOCK : pagos_anticipados_negativo_pan -->
      <!-- <td class="rprint">{pagos_anticipados_negativo_pan}</td> -->
    <!-- END BLOCK : pagos_anticipados_negativo_pan -->
    <!-- START BLOCK : pagos_anticipados_acumulados_pan -->
      <td class="rprint">{pagos_anticipados_acumulados_pan}</td>
    <!-- END BLOCK : pagos_anticipados_acumulados_pan -->
    </tr>
    <tr>
      <td class="vprint">Dif. Inventario </td>
	  <!-- START BLOCK : dif_inventario_pan -->
      <td class="rprint">{dif_inventario_pan}</td>
	  <!-- END BLOCK : dif_inventario_pan -->
    </tr>
    <tr>
      <td class="vprint">Diferencia</td>
	  <!-- START BLOCK : dif_pan -->
      <td class="rprint">{dif_pan}</td>
	  <!-- END BLOCK : dif_pan -->
    </tr>
  </table>
  {salto}
<!-- END BLOCK : hoja_pan -->
<!-- START BLOCK : hoja_ros -->
<table width="100%">
  <tr>
    <td width="20%" class="print_encabezado">{num_cia}</td>
    <td width="60%" align="center" class="print_encabezado">{nombre}</td>
    <td width="20%" class="rprint_encabezado">{num_cia}</td>
  </tr>
  <tr>
    <td colspan="3" align="center" class="print_encabezado">Hist&oacute;rico de Balances {anio} </td>
  </tr>
</table>
<br>
  <table cellpadding="0" cellspacing="0" class="print">
    <tr>
      <th class="print" scope="col">&nbsp;</th>
      <!-- START BLOCK : titulo_ros -->
	  <th class="print" scope="col">{titulo_ros}</th>
	  <!-- END BLOCK : titulo_ros -->
    </tr>
    <tr>
      <td class="vprint">+ Venta</td>
	  <!-- START BLOCK : venta_ros -->
      <td class="rprint">{venta_ros}</td>
	  <!-- END BLOCK : venta_ros -->
    </tr>
    <tr>
      <td class="vprint">+ Otros</td>
	  <!-- START BLOCK : otros_ros -->
      <td class="rprint">{otros}</td>
	  <!-- END BLOCK : otros_ros -->
    </tr>
    <tr>
      <td class="vprint" style="font-weight:bold;font-size:10pt;">= Ventas Netas</td>
	  <!-- START BLOCK : ventas_netas_ros -->
      <td class="rprint" style="font-weight:bold;font-size:10pt;">{ventas_netas_ros}</td>
	  <!-- END BLOCK : ventas_netas_ros -->
    </tr>
    <tr>
      <td class="vprint">&nbsp;</td>
      <!-- START BLOCK : blank1_ros -->
	  <td class="rprint">&nbsp;</td>
	  <!-- END BLOCK : blank1_ros -->
    </tr>
    <tr>
      <td class="vprint">+ Inventario Anterior</td>
      <!-- START BLOCK : inv_ant_ros -->
	  <td class="rprint">{inv_ant_ros}</td>
	  <!-- END BLOCK : inv_ant_ros -->
    </tr>
    <tr>
      <td class="vprint">+ Compras </td>
      <!-- START BLOCK : compras_ros -->
	  <td class="rprint">{compras_ros}</td>
	  <!-- END BLOCK : compras_ros -->
    </tr>
    <tr>
      <td class="vprint">+ Mercancias </td>
      <!-- START BLOCK : mercancias_ros -->
	  <td class="rprint">{mercancias_ros}</td>
	  <!-- END BLOCK : mercancias_ros -->
    </tr>
    <tr>
      <td class="vprint">- Inventario Actual </td>
	  <!-- START BLOCK : inv_act_ros -->
      <td class="rprint">{inv_act_ros}</td>
	  <!-- END BLOCK : inv_act_ros -->
    </tr>
    <tr>
      <td class="vprint">= Mat. Prima Utilizada </td>
	  <!-- START BLOCK : mat_prima_utilizada_ros -->
      <td class="rprint">{mat_prima_utilizada_ros}</td>
	  <!-- END BLOCK : mat_prima_utilizada_ros -->
    </tr>
    <tr>
      <td class="vprint">+ Gastos de Fabricaci&oacute;n </td>
	  <!-- START BLOCK : gastos_fab_ros -->
      <td class="rprint">{gastos_fab}</td>
	  <!-- END BLOCK : gastos_fab_ros -->
    </tr>
    <tr>
      <td class="vprint" style="font-weight:bold;font-size:10pt;">= Costo de Producci&oacute;n</td>
	  <!-- START BLOCK : costo_produccion_ros -->
      <td class="rprint" style="font-weight:bold;font-size:10pt;">{costo_produccion_ros}</td>
	  <!-- END BLOCK : costo_produccion_ros -->
    </tr>
    <tr>
      <td class="vprint">&nbsp;</td>
	  <!-- START BLOCK : blank2_ros -->
      <td class="rprint">&nbsp;</td>
	  <!-- END BLOCK : blank2_ros -->
    </tr>
    <tr>
      <td class="vprint" style="font-weight:bold;font-size:10pt;">Utilidad Bruta</td>
	  <!-- START BLOCK : utilidad_bruta_ros -->
      <td class="rprint" style="font-weight:bold;font-size:10pt;">{utilidad_bruta_ros}</td>
	  <!-- END BLOCK : utilidad_bruta_ros -->
    </tr>
    <tr>
      <td class="vprint">&nbsp;</td>
	  <!-- START BLOCK : blank3_ros -->
      <td class="rprint">&nbsp;</td>
	  <!-- END BLOCK : blank3_ros -->
    </tr>
    <tr>
      <td class="vprint">- Gastos Generales </td>
	  <!-- START BLOCK : gastos_generales_ros -->
      <td class="rprint">{gastos_generales_ros}</td>
	  <!-- END BLOCK : gastos_generales_ros -->
    </tr>
    <tr>
      <td class="vprint">- Gastos por Caja </td>
	  <!-- START BLOCK : gastos_caja_ros -->
      <td class="rprint">{gastos_caja_ros}</td>
	  <!-- END BLOCK : gastos_caja_ros -->
    </tr>
    <tr>
      <td class="vprint">- Comisiones Bancarias </td>
      <!-- START BLOCK : comisiones_ros -->
	  <td class="rprint">{comisiones_ros}</td>
	  <!-- END BLOCK : comisiones_ros -->
    </tr>
    <tr>
      <td class="vprint">- Reservas</td>
	  <!-- START BLOCK : reserva_aguinaldos_ros -->
      <td class="rprint">{reserva_aguinaldos_ros}</td>
	  <!-- END BLOCK : reserva_aguinaldos_ros -->
    </tr>
    <tr>
      <td class="vprint">- Gastos Pagados por Otras </td>
      <!-- START BLOCK : gastos_otras_cias_ros -->
	  <td class="rprint">{gastos_otras_cias_ros}</td>
	  <!-- END BLOCK : gastos_otras_cias_ros -->
    </tr>
    <tr>
      <td class="vprint" style="font-weight:bold;font-size:10pt;">= Total de Gastos</td>
	  <!-- START BLOCK : total_gastos_ros -->
      <td class="rprint" style="font-weight:bold;font-size:10pt;">{total_gastos_ros}</td>
	  <!-- END BLOCK : total_gastos_ros -->
    </tr>
    <tr>
      <td class="vprint">&nbsp;</td>
      <!-- START BLOCK : blank4_ros -->
	  <td class="rprint">&nbsp;</td>
	  <!-- END BLOCK : blank4_ros -->
    </tr>
    <tr>
      <td class="vprint" style="font-weight:bold;font-size:10pt;">+ Ingresos Extraordinarios</td>
	  <!-- START BLOCK : ingresos_ext_ros -->
      <td class="rprint" style="font-weight:bold;font-size:10pt;">{ingresos_ext_ros}</td>
	  <!-- END BLOCK : ingresos_ext_ros -->
    </tr>
    <tr>
      <td class="vprint">&nbsp;</td>
	  <!-- START BLOCK : blank5_ros -->
      <td class="rprint">&nbsp;</td>
	  <!-- END BLOCK : blank5_ros -->
    </tr>
    <tr>
      <td class="vprint" style="font-weight:bold;font-size:10pt;">I.V.A. / Ventas </td>
    <!-- START BLOCK : iva_ros -->
      <td class="rprint" style="font-weight:bold;font-size:10pt;">{iva_ros}</td>
    <!-- END BLOCK : iva_ros -->
    </tr>
    <tr>
      <td class="vprint" style="font-weight:bold;font-size:10pt;">Utilidad del Mes </td>
	  <!-- START BLOCK : utilidad_neta_ros -->
      <td class="rprint" style="font-weight:bold;font-size:10pt;">{utilidad_neta_ros}</td>
	  <!-- END BLOCK : utilidad_neta_ros -->
    </tr>
    <tr>
      <td class="vprint">&nbsp;</td>
      <!-- START BLOCK : blank6_ros -->
	  <td class="rprint">&nbsp;</td>
	  <!-- END BLOCK : blank6_ros -->
    </tr>
    <tr>
      <td class="vprint">M. Prima / Vtas </td>
	  <!-- START BLOCK : mp_vtas_ros -->
      <td class="rprint">{mp_vtas_ros}</td>
	  <!-- END BLOCK : mp_vtas_ros -->
    </tr>
    <tr>
      <td class="vprint">Efectivo</td>
	  <!-- START BLOCK : efectivo_ros -->
      <td class="rprint">{efectivo_ros}</td>
	  <!-- END BLOCK : efectivo_ros -->
    </tr>
    <tr>
      <td class="vprint">Pollos Vendidos </td>
	  <!-- START BLOCK : pollos_vendidos_ros -->
      <td class="rprint">{pollos_vendidos_ros}</td>
	  <!-- END BLOCK : pollos_vendidos_ros -->
    </tr>
    <tr>
      <td class="vprint">Piernas de Pavo </td>
	  <!-- START BLOCK : p_pavo_ros -->
      <td class="rprint">{p_pavo_ros}</td>
	  <!-- END BLOCK : p_pavo_ros -->
    </tr>
	 <tr>
      <td class="vprint">Pescuezos </td>
	  <!-- START BLOCK : pescuezos_ros -->
      <td class="rprint">{pescuezos_ros}</td>
	   <!-- END BLOCK : pescuezos_ros -->
    </tr>
    <tr>
      <td class="vprint">Precio por kilo </td>
    <!-- START BLOCK : precio_kilo_ros -->
      <td class="rprint">{precio_kilo_ros}</td>
     <!-- END BLOCK : precio_kilo_ros -->
    </tr>
    <tr>
      <td class="vprint">&nbsp;</td>
	  <!-- START BLOCK : blank7_ros -->
      <td class="rprint">&nbsp;</td>
	  <!-- END BLOCK : blank7_ros -->
    </tr>
    <tr>
      <td class="vprint">Gastos de Caja (Ingresos) </td>
	  <!-- START BLOCK : ingresos_ros -->
      <td class="rprint">{ingresos_ros}</td>
	  <!-- END BLOCK : ingresos_ros -->
    </tr>
    <tr>
      <td class="vprint">Gastos de Caja (Egresos) </td>
	  <!-- START BLOCK : egresos_ros -->
      <td class="rprint">{egresos_ros}</td>
	  <!-- END BLOCK : egresos_ros -->
    </tr>
    <tr>
      <td class="vprint">Total de Gastos de Caja </td>
	  <!-- START BLOCK : total_gastos_caja_ros -->
      <td class="rprint">{total_gastos_caja_ros}</td>
	  <!-- END BLOCK : total_gastos_caja_ros -->
    </tr>
    <tr>
      <td class="vprint">Dep&oacute;sitos</td>
	  <!-- START BLOCK : depositos_ros -->
      <td class="rprint">{depositos_ros}</td>
	  <!-- END BLOCK : depositos_ros -->
    </tr>
    <tr>
      <td class="vprint">Otros Dep&oacute;sitos </td>
	  <!-- START BLOCK : otros_depositos_ros -->
      <td class="rprint">{otros_depositos_ros}</td>
	  <!-- END BLOCK : otros_depositos_ros -->
    </tr>
    <tr>
      <td class="vprint">General</td>
	  <!-- START BLOCK : general_ros -->
      <td class="rprint">{general_ros}</td>
	  <!-- END BLOCK : general_ros -->
    </tr>
    <tr>
      <td class="vprint">Diferencia</td>
	  <!-- START BLOCK : diferencia_ros -->
      <td class="rprint">{diferencia_ros}</td>
	  <!-- END BLOCK : diferencia_ros -->
    </tr>
    <tr>
      <td class="vprint">&nbsp;</td>
	  <!-- START BLOCK : blank8_ros -->
      <td class="rprint">&nbsp;</td>
	  <!-- END BLOCK : blank8_ros -->
    </tr>
    <tr>
      <td class="vprint">Saldo Inicial</td>
	  <!-- START BLOCK : saldo_ini_ros -->
      <td class="rprint">{saldo_ini_ros}</td>
	  <!-- END BLOCK : saldo_ini_ros -->
    </tr>
    <tr>
      <td class="vprint">Saldo Final</td>
	  <!-- START BLOCK : saldo_fin_ros -->
      <td class="rprint">{saldo_fin_ros}</td>
	  <!-- END BLOCK : saldo_fin_ros -->
    </tr>
    <tr>
      <td class="vprint">Saldo Prov. Inicial</td>
	  <!-- START BLOCK : saldo_pro_ini_ros -->
      <td class="rprint">{saldo_pro_ini_ros}</td>
	  <!-- END BLOCK : saldo_pro_ini_ros -->
    </tr>
    <tr>
      <td class="vprint">Saldo Prov. Final</td>
	  <!-- START BLOCK : saldo_pro_fin_ros -->
      <td class="rprint">{saldo_pro_fin_ros}</td>
	  <!-- END BLOCK : saldo_pro_fin_ros -->
    </tr>
    <tr>
      <td class="vprint">No Incluidos</td>
	  <!-- START BLOCK : no_inc_ros -->
      <td class="rprint">{no_inc_ros}</td>
	  <!-- END BLOCK : no_inc_ros -->
    </tr>
    <tr>
      <td class="vprint">Dif. Reservas </td>
	  <!-- START BLOCK : dif_reservas_ros -->
      <td class="rprint">{dif_reservas_ros}</td>
	  <!-- END BLOCK : dif_reservas_ros -->
    </tr>
    <tr>
      <td class="vprint">Pagos anticipados </td>
    <!-- START BLOCK : pagos_anticipados_negativo_ros -->
      <td class="rprint">{pagos_anticipados_negativo_ros}</td>
    <!-- END BLOCK : pagos_anticipados_negativo_ros -->
    </tr>
    <tr>
      <td class="vprint">Dif. Inventario </td>
	  <!-- START BLOCK : dif_inventario_ros -->
      <td class="rprint">{dif_inventario_ros}</td>
	  <!-- END BLOCK : dif_inventario_ros -->
    </tr>
    <tr>
      <td class="vprint">Diferencia</td>
	  <!-- START BLOCK : dif_ros -->
      <td class="rprint">{dif_ros}</td>
	  <!-- END BLOCK : dif_ros -->
    </tr>
  </table>
  {salto}
<!-- END BLOCK : hoja_ros -->
</body>
</html>
