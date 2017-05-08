<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Hist&oacute;rico de Balances</title>
<link href="../../styles/impresion.css" rel="stylesheet" type="text/css" />
<link href="./styles/impresion.css" rel="stylesheet" type="text/css" />
</head>

<body>
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
      <!-- START BLOCK : titulo -->
	  <th class="print" scope="col">{titulo}</th>
	  <!-- END BLOCK : titulo -->
    </tr>
    <tr>
      <td class="vprint">+ Venta</td>
	  <!-- START BLOCK : venta_puerta -->
      <td class="rprint">{venta_puerta}</td>
	  <!-- END BLOCK : venta_puerta -->
    </tr>
    <tr>
      <td class="vprint">+ Bases</td>
      <!-- START BLOCK : bases -->
	  <td class="rprint">{bases}</td>
	  <!-- END BLOCK : bases -->
    </tr>
    <tr>
      <td class="vprint">+ Otros</td>
	  <!-- START BLOCK : otros -->
      <td class="rprint">{otros}</td>
	  <!-- END BLOCK : otros -->
    </tr>
    <tr>
      <td class="vprint" style="font-weight:bold;font-size:10pt;">= Ventas Netas</td>
	  <!-- START BLOCK : ventas_netas -->
      <td class="rprint" style="font-weight:bold;font-size:10pt;">{ventas_netas}</td>
	  <!-- END BLOCK : ventas_netas -->
    </tr>
    <tr>
      <td class="vprint">&nbsp;</td>
      <!-- START BLOCK : blank1 -->
	  <td class="rprint">&nbsp;</td>
	  <!-- END BLOCK : blank1 -->
    </tr>
    <tr>
      <td class="vprint">+ Inventario Anterior</td>
      <!-- START BLOCK : inv_ant -->
	  <td class="rprint">{inv_ant}</td>
	  <!-- END BLOCK : inv_ant -->
    </tr>
    <tr>
      <td class="vprint">+ Compras </td>
      <!-- START BLOCK : compras -->
	  <td class="rprint">{compras}</td>
	  <!-- END BLOCK : compras -->
    </tr>
    <tr>
      <td class="vprint">+ Mercancias </td>
      <!-- START BLOCK : mercancias -->
	  <td class="rprint">{mercancias}</td>
	  <!-- END BLOCK : mercancias -->
    </tr>
    <tr>
      <td class="vprint">- Inventario Actual </td>
	  <!-- START BLOCK : inv_act -->
      <td class="rprint">{inv_act}</td>
	  <!-- END BLOCK : inv_act -->
    </tr>
    <tr>
      <td class="vprint">= Mat. Prima Utilizada </td>
	  <!-- START BLOCK : mat_prima_utilizada -->
      <td class="rprint">{mat_prima_utilizada}</td>
	  <!-- END BLOCK : mat_prima_utilizada -->
    </tr>
    <tr>
      <td class="vprint">+ Gastos de Fabricaci&oacute;n </td>
	  <!-- START BLOCK : gastos_fab -->
      <td class="rprint">{gastos_fab}</td>
	  <!-- END BLOCK : gastos_fab -->
    </tr>
    <tr>
      <td class="vprint" style="font-weight:bold;font-size:10pt;">= Costo de Producci&oacute;n</td>
	  <!-- START BLOCK : costo_produccion -->
      <td class="rprint" style="font-weight:bold;font-size:10pt;">{costo_produccion}</td>
	  <!-- END BLOCK : costo_produccion -->
    </tr>
    <tr>
      <td class="vprint">&nbsp;</td>
	  <!-- START BLOCK : blank2 -->
      <td class="rprint">&nbsp;</td>
	  <!-- END BLOCK : blank2 -->
    </tr>
    <tr>
      <td class="vprint" style="font-weight:bold;font-size:10pt;">Utilidad Bruta</td>
	  <!-- START BLOCK : utilidad_bruta -->
      <td class="rprint" style="font-weight:bold;font-size:10pt;">{utilidad_bruta}</td>
	  <!-- END BLOCK : utilidad_bruta -->
    </tr>
    <tr>
      <td class="vprint">&nbsp;</td>
	  <!-- START BLOCK : blank3 -->
      <td class="rprint">&nbsp;</td>
	  <!-- END BLOCK : blank3 -->
    </tr>
    <tr>
      <td class="vprint">- Gastos Generales </td>
	  <!-- START BLOCK : gastos_generales -->
      <td class="rprint">{gastos_generales}</td>
	  <!-- END BLOCK : gastos_generales -->
    </tr>
    <tr>
      <td class="vprint">- Gastos por Caja </td>
	  <!-- START BLOCK : gastos_caja -->
      <td class="rprint">{gastos_caja}</td>
	  <!-- END BLOCK : gastos_caja -->
    </tr>
    <tr>
      <td class="vprint">- Comisiones Bancarias </td>
      <!-- START BLOCK : comisiones -->
	  <td class="rprint">{comisiones}</td>
	  <!-- END BLOCK : comisiones -->
    </tr>
    <tr>
      <td class="vprint">- Reservas</td>
	  <!-- START BLOCK : reserva_aguinaldos -->
      <td class="rprint">{reserva_aguinaldos}</td>
	  <!-- END BLOCK : reserva_aguinaldos -->
    </tr>
    <tr>
      <td class="vprint">- Gastos Pagados por Otras </td>
      <!-- START BLOCK : gastos_otras_cias -->
	  <td class="rprint">{gastos_otras_cias}</td>
	  <!-- END BLOCK : gastos_otras_cias -->
    </tr>
    <tr>
      <td class="vprint" style="font-weight:bold;font-size:10pt;">= Total de Gastos</td>
	  <!-- START BLOCK : total_gastos -->
      <td class="rprint" style="font-weight:bold;font-size:10pt;">{total_gastos}</td>
	  <!-- END BLOCK : total_gastos -->
    </tr>
    <tr>
      <td class="vprint">&nbsp;</td>
      <!-- START BLOCK : blank4 -->
	  <td class="rprint">&nbsp;</td>
	  <!-- END BLOCK : blank4 -->
    </tr>
    <tr>
      <td class="vprint" style="font-weight:bold;font-size:10pt;">+ Ingresos Extraordinarios</td>
	  <!-- START BLOCK : ingresos_ext -->
      <td class="rprint" style="font-weight:bold;font-size:10pt;">{ingresos_ext}</td>
	  <!-- END BLOCK : ingresos_ext -->
    </tr>
    <!--<tr>
      <td class="vprint">&nbsp;</td>
	  <!-- START BLOCK : blank5 -->
      <td class="rprint">&nbsp;</td>
	  <!-- END BLOCK : blank5 -->
    </tr>-->
    <tr>
      <td class="vprint" style="font-weight:bold;font-size:10pt;">Utilidad del Mes </td>
	  <!-- START BLOCK : utilidad_neta -->
      <td class="rprint" style="font-weight:bold;font-size:10pt;">{utilidad_neta}</td>
	  <!-- END BLOCK : utilidad_neta -->
    </tr>
    <tr>
      <td class="vprint">&nbsp;</td>
      <!-- START BLOCK : blank6 -->
	  <td class="rprint">&nbsp;</td>
	  <!-- END BLOCK : blank6 -->
    </tr>
    <tr>
      <td class="vprint">M. Prima / Vtas </td>
	  <!-- START BLOCK : mp_vtas -->
      <td class="rprint">{mp_vtas}</td>
	  <!-- END BLOCK : mp_vtas -->
    </tr>
    <tr>
      <td class="vprint">Efectivo</td>
	  <!-- START BLOCK : utilidad_pro -->
      <td class="rprint">{efectivo}</td>
	  <!-- END BLOCK : utilidad_pro -->
    </tr>
    <tr>
      <td class="vprint">Pollos Vendidos </td>
	  <!-- START BLOCK : pollos_vendidos -->
      <td class="rprint">{pollos_vendidos}</td>
	  <!-- END BLOCK : pollos_vendidos -->
    </tr>
    <tr>
      <td class="vprint">Piernas de Pavo </td>
	  <!-- START BLOCK : p_pavo -->
      <td class="rprint">{p_pavo}</td>
	  <!-- END BLOCK : p_pavo -->
    </tr>
	 <tr>
      <td class="vprint">Pescuezos </td>
	  <!-- START BLOCK : pescuezos -->
      <td class="rprint">{pescuezos}</td>
	   <!-- END BLOCK : pescuezos -->
    </tr>
    <tr>
      <td class="vprint">&nbsp;</td>
	  <!-- START BLOCK : blank8 -->
      <td class="rprint">&nbsp;</td>
	  <!-- END BLOCK : blank8 -->
    </tr>
    <tr>
      <td class="vprint">Gastos de Caja (Ingresos) </td>
	  <!-- START BLOCK : ingresos -->
      <td class="rprint">{ingresos}</td>
	  <!-- END BLOCK : ingresos -->
    </tr>
    <tr>
      <td class="vprint">Gastos de Caja (Egresos) </td>
	  <!-- START BLOCK : egresos -->
      <td class="rprint">{egresos}</td>
	  <!-- END BLOCK : egresos -->
    </tr>
    <tr>
      <td class="vprint">Total de Gastos de Caja </td>
	  <!-- START BLOCK : total_gastos_caja -->
      <td class="rprint">{total_gastos_caja}</td>
	  <!-- END BLOCK : total_gastos_caja -->
    </tr>
    <tr>
      <td class="vprint">Dep&oacute;sitos</td>
	  <!-- START BLOCK : depositos -->
      <td class="rprint">{depositos}</td>
	  <!-- END BLOCK : depositos -->
    </tr>
    <tr>
      <td class="vprint">Otros Dep&oacute;sitos </td>
	  <!-- START BLOCK : otros_depositos -->
      <td class="rprint">{otros_depositos}</td>
	  <!-- END BLOCK : otros_depositos -->
    </tr>
    <tr>
      <td class="vprint">General</td>
	  <!-- START BLOCK : general -->
      <td class="rprint">{general}</td>
	  <!-- END BLOCK : general -->
    </tr>
    <tr>
      <td class="vprint">Diferencia</td>
	  <!-- START BLOCK : diferencia -->
      <td class="rprint">{diferencia}</td>
	  <!-- END BLOCK : diferencia -->
    </tr>
    <!--<tr>
      <td class="vprint">&nbsp;</td>
	  <!-- START BLOCK : blank9 -->
      <td class="rprint">&nbsp;</td>
	  <!-- END BLOCK : blank9 -->
    </tr>-->
    <tr>
      <td class="vprint">Saldo Inicial</td>
	  <!-- START BLOCK : saldo_ini -->
      <td class="rprint">{saldo_ini}</td>
	  <!-- END BLOCK : saldo_ini -->
    </tr>
    <tr>
      <td class="vprint">Saldo Final</td>
	  <!-- START BLOCK : saldo_fin -->
      <td class="rprint">{saldo_fin}</td>
	  <!-- END BLOCK : saldo_fin -->
    </tr>
    <tr>
      <td class="vprint">Saldo Prov. Inicial</td>
	  <!-- START BLOCK : saldo_pro_ini -->
      <td class="rprint">{saldo_pro_ini}</td>
	  <!-- END BLOCK : saldo_pro_ini -->
    </tr>
    <tr>
      <td class="vprint">Saldo Prov. Final</td>
	  <!-- START BLOCK : saldo_pro_fin -->
      <td class="rprint">{saldo_pro_fin}</td>
	  <!-- END BLOCK : saldo_pro_fin -->
    </tr>
    <tr>
      <td class="vprint">No Incluidos</td>
	  <!-- START BLOCK : no_inc -->
      <td class="rprint">{no_inc}</td>
	  <!-- END BLOCK : no_inc -->
    </tr>
    <tr>
      <td class="vprint">Dif. Reservas </td>
	  <!-- START BLOCK : dif_reservas -->
      <td class="rprint">{dif_reservas}</td>
	  <!-- END BLOCK : dif_reservas -->
    </tr>
    <tr>
      <td class="vprint">Dif. Inventario </td>
	  <!-- START BLOCK : dif_inventario -->
      <td class="rprint">{dif_inventario}</td>
	  <!-- END BLOCK : dif_inventario -->
    </tr>
    <tr>
      <td class="vprint">Diferencia</td>
	  <!-- START BLOCK : dif -->
      <td class="rprint">{dif}</td>
	  <!-- END BLOCK : dif -->
    </tr>
  </table>
  {salto}
<!-- END BLOCK : hoja_ros -->
</body>
</html>
