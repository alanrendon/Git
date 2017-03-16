<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Estado de Resultados</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="./styles/tablas.css" rel="stylesheet" type="text/css">
<link href="./styles/esr.css" rel="stylesheet" type="text/css">
<link href="./styles/impresion.css" rel="stylesheet" type="text/css">
</head>

<body>
<!-- START BLOCK : reporte -->
<table width="100%" cellpadding="0" cellspacing="0" align="center">
  <tr class="center">
    <th class="left" scope="col"><font size="+2">{num_cia}</font></th>
    <th scope="col" class="center"><font color="#000099">{nombre_cia}<br>
      ({nombre_corto})</font></th>
    <th class="right" scope="col"><font size="+2">{num_cia}</font></th>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td class="left"><div align="center">Estado de Resultados del mes de {mes} del {anio}</div></td>
    <td>&nbsp;</td>
  </tr>
</table><br>
<table width="100%" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td colspan="2" class="left">Venta</td>
    <td colspan="2">&nbsp;</td>
    <td class="left">{ventas}</td>
    <td colspan="2" class="left">&nbsp;</td>
    <td>&nbsp;</td>
    <td colspan="2">&nbsp;</td>
  </tr>
  <tr>
    <td colspan="2" class="left">Otros</td>
    <td colspan="2">&nbsp;</td>
    <td class="left">{otros}</td>
    <td colspan="2" class="left">&nbsp;</td>
    <td>&nbsp;</td>
    <td colspan="2">&nbsp;</td>
  </tr>
  <tr>
    <td colspan="2" class="left_total"><strong>Ventas Netas</strong></td>
    <td colspan="2">&nbsp;</td>
    <td>&nbsp;</td>
    <td colspan="2" class="left_total"><strong>{ventas_netas}{p_ventas}</strong></td>
    <td class="left_total">&nbsp;</td>
    <td colspan="2">&nbsp;</td>
  </tr>
  <tr>
    <td colspan="2">&nbsp;</td>
    <td colspan="2">&nbsp;</td>
    <td>&nbsp;</td>
    <td colspan="2">&nbsp;</td>
    <td>&nbsp;</td>
    <td colspan="2">&nbsp;</td>
  </tr>
  <tr>
    <td colspan="2" class="left">Inventario Anterior</td>
    <td colspan="2" class="left">{inv_ant}</td>
    <td class="left">&nbsp;</td>
    <td colspan="2">&nbsp;</td>
    <td>&nbsp;</td>
    <td colspan="2">&nbsp;</td>
  </tr>
  <tr>
    <td colspan="2" class="left">+ Compras</td>
    <td colspan="2" class="left">{compras}</td>
    <td class="left">&nbsp;</td>
    <td colspan="2">&nbsp;</td>
    <td>&nbsp;</td>
    <td colspan="2">&nbsp;</td>
  </tr>
  <tr>
    <td colspan="2" class="left">+ Mercancias</td>
    <td colspan="2" class="left">{mercancias}</td>
    <td class="left">&nbsp;</td>
    <td colspan="2">&nbsp;</td>
    <td>&nbsp;</td>
    <td colspan="2">&nbsp;</td>
  </tr>
  <tr>
    <td colspan="2" class="left">- Inventario Actual</td>
    <td colspan="2" class="left">{inv_act}</td>
    <td class="left">&nbsp;</td>
    <td colspan="2">&nbsp;</td>
    <td>&nbsp;</td>
    <td colspan="2">&nbsp;</td>
  </tr>
  <tr>
    <td colspan="2" class="left">= Mat. Prima Utilizada </td>
    <td colspan="2">&nbsp;</td>
    <td class="left">{mat_pri_utilizada}</td>
    <td colspan="2" class="left">&nbsp;</td>
    <td>&nbsp;</td>
    <td colspan="2">&nbsp;</td>
  </tr>
  <tr>
    <td colspan="2" class="left">+ Gastos de Fabricaci&oacute;n </td>
    <td colspan="2">&nbsp;</td>
    <td class="left">{gastos_fab}</td>
    <td colspan="2" class="left">&nbsp;</td>
    <td>&nbsp;</td>
    <td colspan="2">&nbsp;</td>
  </tr>
  <tr>
    <td colspan="2" class="left_total">= Costo de Elaboraci&oacute;n </td>
    <td colspan="2">&nbsp;</td>
    <td>&nbsp;</td>
    <td colspan="2" class="left_total"><strong>{costo_elaboracion}</strong></td>
    <td class="left_total">&nbsp;</td>
    <td colspan="2">&nbsp;</td>
  </tr>
  <tr>
    <td colspan="2">&nbsp;</td>
    <td colspan="2">&nbsp;</td>
    <td>&nbsp;</td>
    <td colspan="2">&nbsp;</td>
    <td>&nbsp;</td>
    <td colspan="2">&nbsp;</td>
  </tr>
  <tr>
    <td colspan="2" class="left_total"><strong>Utilidad Bruta </strong></td>
    <td colspan="2">&nbsp;</td>
    <td>&nbsp;</td>
    <td colspan="2" class="left_total"><strong>{titulo1}</strong></td>
    <td class="left_total">&nbsp;</td>
    <td colspan="2">&nbsp;</td>
  </tr>
  <tr>
    <td colspan="2" class="left">- Gastos Generales</td>
    <td colspan="2" class="left">{gastos_gral}</td>
    <td>&nbsp;</td>
    <td colspan="2" class="left">&nbsp;</td>
    <td>&nbsp;</td>
    <td colspan="2">&nbsp;</td>
  </tr>
  <tr>
    <td colspan="2" class="left">- Gastos de Caja</td>
    <td colspan="2" class="left">{gastos_caja}</td>
    <td>&nbsp;</td>
    <td colspan="2" class="left_total"><strong>Reserva</strong></td>
    <td class="left_total"><strong>Importe</strong></td>
    <td colspan="2">&nbsp;</td>
  </tr>
  <tr>
    <td colspan="2" class="left">- Comisiones Bancarias </td>
    <td colspan="2" class="left">{comisiones}</td>
    <td>&nbsp;</td>
    <td colspan="2" class="left">AGUINALDOS</td>
    <td class="left">{aguinaldos}</td>
    <td colspan="2">&nbsp;</td>
  </tr>
  <tr>
    <td colspan="2" class="left">- Reserva para Aguinaldos</td>
    <td colspan="2" class="left">{reservas}</td>
    <td>&nbsp;</td>
    <td colspan="2" class="left">I.M.S.S.-INFONAVIT</td>
    <td class="left">{imss}</td>
    <td colspan="2">&nbsp;</td>
  </tr>
  <tr>
    <td colspan="2" class="left">- Gtos. Pagados Por Otras Cias.</td>
    <td colspan="2" class="left">{gastos_otras_cias}</td>
    <td>&nbsp;</td>
    <td colspan="2" class="left_total"><strong>Total</strong></td>
    <td class="left_total">{total_res}</td>
    <td colspan="2">&nbsp;</td>
  </tr>
  <tr>
    <td colspan="2" class="left">= Total de Gastos</td>
    <td colspan="2">&nbsp;</td>
    <td class="left"><strong>{total_gastos}</strong></td>
    <td colspan="2">&nbsp;</td>
    <td class="left">&nbsp;</td>
    <td colspan="2">&nbsp;</td>
  </tr>
  <tr>
    <td colspan="2" class="left">+ Ingresos Extraordinarios </td>
    <td colspan="2">&nbsp;</td>
    <td class="left">{ingresos_ext}</td>
    <td colspan="2">&nbsp;</td>
    <td class="left">&nbsp;</td>
    <td colspan="2">&nbsp;</td>
  </tr>
  <tr>
    <td colspan="2" class="left_total"><strong>Utilidad del Mes </strong></td>
    <td colspan="2">&nbsp;</td>
    <td>&nbsp;</td>
    <td colspan="2" class="left_total"><strong>{titulo2}{p_utilidad}</strong></td>
    <td class="left_total">&nbsp;</td>
    <td colspan="2">&nbsp;</td>
  </tr>
  <tr>
    <td colspan="2" class="left_total">&nbsp;</td>
    <td colspan="2">&nbsp;</td>
    <td>&nbsp;</td>
    <td colspan="2" class="left_total" style="font-size:14pt;">Piezas: {por_piezas}% {piezas_var}</td>
    <td class="left_total">&nbsp;</td>
    <td colspan="2">&nbsp;</td>
  </tr>
  <tr>
    <td colspan="2">&nbsp;</td>
    <td colspan="2">&nbsp;</td>
    <td>&nbsp;</td>
    <td colspan="2">&nbsp;</td>
    <td>&nbsp;</td>
    <td colspan="2">&nbsp;</td>
  </tr>
  <tr>
    <td colspan="2" class="left">Efectivo: {efectivo} </td>
    <td colspan="2" class="left">MP/Ventas% {porc_efectivo}</td>
    <td>&nbsp;</td>
    <td colspan="2">&nbsp;</td>
    <td>&nbsp;</td>
    <td colspan="2">&nbsp;</td>
  </tr>
  <tr>
    <td colspan="2">&nbsp;</td>
    <td colspan="2">&nbsp;</td>
    <td>&nbsp;</td>
    <td colspan="2">&nbsp;</td>
    <td>&nbsp;</td>
    <td colspan="2">&nbsp;</td>
  </tr>
  <tr>
    <td colspan="2" class="left">Pollos Vendidos: {pollos_vendidos}</td>
    <td colspan="2">&nbsp;</td>
    <td class="left">{p_pavo}</td>
    <td colspan="2">&nbsp;</td>
    <td>&nbsp;</td>
    <td colspan="2">&nbsp;</td>
  </tr>
  <tr>
    <td colspan="2" class="left">Peso Promedio:</td>
    <td colspan="2" class="left">{peso_normal}</td>
    <td class="left">{peso_chico}</td>
    <td colspan="2" class="left">{peso_grande}</td>
    <td>&nbsp;</td>
    <td colspan="2">&nbsp;</td>
  </tr>
  <tr>
    <td colspan="2">&nbsp;</td>
    <td colspan="2">&nbsp;</td>
    <td>&nbsp;</td>
    <td colspan="2">&nbsp;</td>
    <td>&nbsp;</td>
    <td colspan="2">&nbsp;</td>
  </tr>
  <tr>
    <td colspan="2" class="left_total"><strong>A&ntilde;o Anterior </strong></td>
    <td colspan="2">&nbsp;</td>
    <td>&nbsp;</td>
    <td colspan="2">&nbsp;</td>
    <td>&nbsp;</td>
    <td colspan="2">&nbsp;</td>
  </tr>
  <tr>
    <td class="left">{tant_1}</td>
    <td class="left">
      {ant_1}    </td>
    <td class="left">{tant_4}</td>
    <td class="left">
      {ant_4}    </td>
    <td>&nbsp;</td>
    <td class="left">{tant_7} </td>
    <td class="left">
      {ant_7}    </td>
    <td>&nbsp;</td>
    <td class="left">{tant_10}</td>
    <td class="left">
      {ant_10}    </td>
  </tr>
  <tr>
    <td class="left">{tant_2} </td>
    <td class="left">
      {ant_2}    </td>
    <td class="left">{tant_5}</td>
    <td class="left">
      {ant_5}    </td>
    <td>&nbsp;</td>
    <td class="left">{tant_8}</td>
    <td class="left">
      {ant_8}    </td>
    <td>&nbsp;</td>
    <td class="left">{tant_11}</td>
    <td class="left">
      {ant_11}    </td>
  </tr>
  <tr>
    <td class="left">{tant_3} </td>
    <td class="left">
      {ant_3}    </td>
    <td class="left">{tant_6}</td>
    <td class="left">
      {ant_6}    </td>
    <td>&nbsp;</td>
    <td class="left">{tant_9}</td>
    <td class="left">
      {ant_9}    </td>
    <td>&nbsp;</td>
    <td class="left">{tant_12}</td>
    <td class="left">
      {ant_12}    </td>
  </tr>
  <tr>
    <td colspan="2">&nbsp;</td>
    <td colspan="2">&nbsp;</td>
    <td>&nbsp;</td>
    <td colspan="2">&nbsp;</td>
    <td>&nbsp;</td>
    <td colspan="2">&nbsp;</td>
  </tr>
  <tr>
    <td colspan="2" class="left_total"><strong>A&ntilde;o Actual </strong></td>
    <td colspan="2">&nbsp;</td>
    <td>&nbsp;</td>
    <td colspan="2">&nbsp;</td>
    <td>&nbsp;</td>
    <td colspan="2">&nbsp;</td>
  </tr>
  <tr>
    <td class="left">{tact_1}</td>
    <td class="left">
      {act_1}    </td>
    <td class="left"> {tact_4} </td>
    <td class="left">
      {act_4}    </td>
    <td>&nbsp;</td>
    <td class="left"> {tact_7} </td>
    <td class="left">
      {act_7}    </td>
    <td>&nbsp;</td>
    <td class="left"> {tact_10} </td>
    <td class="left">
      {act_10}    </td>
  </tr>
  <tr>
    <td class="left">{tact_2} </td>
    <td class="left">
      {act_2}    </td>
    <td class="left">{tact_5} </td>
    <td class="left">
      {act_5}    </td>
    <td>&nbsp;</td>
    <td class="left"> {tact_8} </td>
    <td class="left"> 
     {act_8}    </td>
    <td>&nbsp;</td>
    <td class="left"> {tact_11} </td>
    <td class="left">
      {act_11}    </td>
  </tr>
  <tr>
    <td class="left"> {tact_3} </td>
    <td class="left">
      {act_3}    </td>
    <td class="left"> {tact_6} </td>
    <td class="left">
      {act_6}    </td>
    <td>&nbsp;</td>
    <td class="left"> {tact_9} </td>
    <td class="left">
      {act_9}    </td>
    <td>&nbsp;</td>
    <td class="left"> {tact_12} </td>
    <td class="left">
      {act_12}    </td>
  </tr>
  <tr>
    <td colspan="2">&nbsp;</td>
    <td colspan="2">&nbsp;</td>
    <td>&nbsp;</td>
    <td colspan="2">&nbsp;</td>
    <td>&nbsp;</td>
    <td colspan="2">&nbsp;</td>
  </tr>
  <tr>
    <td colspan="2" class="left_total"><strong>Estadistica de Ventas </strong></td>
    <td colspan="2">&nbsp;</td>
    <td>&nbsp;</td>
    <td colspan="2">&nbsp;</td>
    <td>&nbsp;</td>
    <td colspan="2">&nbsp;</td>
  </tr>
  <tr>
    <td colspan="5" class="center" style="font-weight:bold; font-size:10pt;">A&ntilde;o {anio_act}</td>
    <td colspan="5" class="center" style="font-weight:bold; font-size:10pt;">A&ntilde;o {anio_ant}</td>
  </tr>
  <tr>
    <td class="left_total">&nbsp;</td>
    <td class="left_total"><strong>Ventas Netas </strong></td>
    <td class="left_total"><strong>Pollos</strong></td>
    <td class="left_total"><strong>{P.Pavo}</strong></td>
    <td class="left_total"><strong>{Pescuesos}</strong></td>
    <td class="left_total">Ventas Netas </td>
    <td class="left_total">Pollos</td>
    <td class="left_total">{P.Pavo_ant}</td>
    <td colspan="2" class="left_total">{pescuesos_ant}</td>
  </tr>
  <tr>
    <td class="left">Enero</td>
    <td class="left">{ven_1}</td>
    <td class="left">{pollos_ene}</td>
    <td class="left">{pavo_ene}</td>
    <td class="left">{pes_ene}</td>
    <td class="left">{ven_ant_1}</td>
    <td class="left">{pollo_1}</td>
    <td class="left">{pavo_1}</td>
    <td colspan="2" class="left">{pes_1}</td>
  </tr>
  <tr>
    <td class="left">Febrero</td>
    <td class="left">{ven_2}</td>
    <td class="left">{pollos_feb}</td>
    <td class="left">{pavo_feb}</td>
    <td class="left">{pes_feb}</td>
    <td class="left">{ven_ant_2}</td>
    <td class="left">{pollo_2}</td>
    <td class="left">{pavo_2}</td>
    <td colspan="2" class="left">{pes_2}</td>
  </tr>
  <tr>
    <td class="left">Marzo</td>
    <td class="left">{ven_3}</td>
    <td class="left">{pollos_mar}</td>
    <td class="left">{pavo_mar}</td>
    <td class="left">{pes_mar}</td>
    <td class="left">{ven_ant_3}</td>
    <td class="left">{pollo_3}</td>
    <td class="left">{pavo_3}</td>
    <td colspan="2" class="left">{pes_3}</td>
  </tr>
  <tr>
    <td class="left">Abril</td>
    <td class="left">{ven_4}</td>
    <td class="left">{pollos_abr}</td>
    <td class="left">{pavo_abr}</td>
    <td class="left">{pes_abr}</td>
    <td class="left">{ven_ant_4}</td>
    <td class="left">{pollo_4}</td>
    <td class="left">{pavo_4}</td>
    <td colspan="2" class="left">{pes_4}</td>
  </tr>
  <tr>
    <td class="left">Mayo</td>
    <td class="left">{ven_5}</td>
    <td class="left">{pollos_may}</td>
    <td class="left">{pavo_may}</td>
    <td class="left">{pes_may}</td>
    <td class="left">{ven_ant_5}</td>
    <td class="left">{pollo_5}</td>
    <td class="left">{pavo_5}</td>
    <td colspan="2" class="left">{pes_5}</td>
  </tr>
  <tr>
    <td class="left">Junio</td>
    <td class="left">{ven_6}</td>
    <td class="left">{pollos_jun}</td>
    <td class="left">{pavo_jun}</td>
    <td class="left">{pes_jun}</td>
    <td class="left">{ven_ant_6}</td>
    <td class="left">{pollo_6}</td>
    <td class="left">{pavo_6}</td>
    <td colspan="2" class="left">{pes_6}</td>
  </tr>
  <tr>
    <td class="left">Julio</td>
    <td class="left">{ven_7}</td>
    <td class="left">{pollos_jul}</td>
    <td class="left">{pavo_jul}</td>
    <td class="left">{pes_jul}</td>
    <td class="left">{ven_ant_7}</td>
    <td class="left">{pollo_7}</td>
    <td class="left">{pavo_7}</td>
    <td colspan="2" class="left">{pes_7}</td>
  </tr>
  <tr>
    <td class="left">Agosto</td>
    <td class="left">{ven_8}</td>
    <td class="left">{pollos_ago}</td>
    <td class="left">{pavo_ago}</td>
    <td class="left">{pes_ago}</td>
    <td class="left">{ven_ant_8}</td>
    <td class="left">{pollo_8}</td>
    <td class="left">{pavo_8}</td>
    <td colspan="2" class="left">{pes_8}</td>
  </tr>
  <tr>
    <td class="left">Septiembre</td>
    <td class="left">{ven_9}</td>
    <td class="left">{pollos_sep}</td>
    <td class="left">{pavo_sep}</td>
    <td class="left">{pes_sep}</td>
    <td class="left">{ven_ant_9}</td>
    <td class="left">{pollo_9}</td>
    <td class="left">{pavo_9}</td>
    <td colspan="2" class="left">{pes_9}</td>
  </tr>
  <tr>
    <td class="left">Octubre</td>
    <td class="left">{ven_10}</td>
    <td class="left">{pollos_oct}</td>
    <td class="left">{pavo_oct}</td>
    <td class="left">{pes_oct}</td>
    <td class="left">{ven_ant_10}</td>
    <td class="left">{pollo_10}</td>
    <td class="left">{pavo_10}</td>
    <td colspan="2" class="left">{pes_10}</td>
  </tr>
  <tr>
    <td class="left">Noviembre</td>
    <td class="left">{ven_11}</td>
    <td class="left">{pollos_nov}</td>
    <td class="left">{pavo_nov}</td>
    <td class="left">{pes_nov}</td>
    <td class="left">{ven_ant_11}</td>
    <td class="left">{pollo_11}</td>
    <td class="left">{pavo_11}</td>
    <td colspan="2" class="left">{pes_11}</td>
  </tr>
  <tr>
    <td class="left">Diciembre</td>
    <td class="left">{ven_12}</td>
    <td class="left">{pollos_dic}</td>
    <td class="left">{pavo_dic}</td>
    <td class="left">{pes_dic}</td>
    <td class="left">{ven_ant_12}</td>
    <td class="left">{pollo_12}</td>
    <td class="left">{pavo_12}</td>
    <td colspan="2" class="left">{pes_12}</td>
  </tr>
  <tr>
    <td class="left_total"><strong>Total</strong></td>
    <td class="left_total"><strong>{total_ventas}</strong></td>
    <td class="left_total"><strong>{total_pollos}</strong></td>
    <td class="left_total"><strong>{total_pavo}</strong></td>
    <td class="left_total"><strong>{total_pes}</strong></td>
    <td class="left_total"><strong>{total_ventas_ant}</strong></td>
    <td class="left_total"><strong>{total_pollos_ant}</strong></td>
    <td class="left_total"><strong>{total_pavo_ant}</strong></td>
    <td colspan="2" class="left_total"><strong>{total_pes_ant}</strong></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td class="left_total"><strong>{porc_ventas}</strong></td>
    <td class="left_total"><strong>{porc_pollos}</strong></td>
    <td class="left_total"><strong>{porc_pavo}</strong></td>
    <td class="left_total"><strong>{porc_pes}</strong></td>
    <td class="left_total"><strong>{porc_ventas_ant}</strong></td>
    <td class="left_total"><strong>{porc_pollos_ant}</strong></td>
    <td class="left_total"><strong>{porc_pavo_ant}</strong></td>
    <td colspan="2" class="left_total"><strong>{porc_pes_ant}</strong></td>
  </tr>
</table>
<br style="page-break-after:always;">
<table width="100%" cellpadding="0" cellspacing="0" align="center">
  <tr class="center">
    <th class="left" scope="col"><font size="+2">{num_cia}</font></th>
    <th class="right" scope="col"><font size="+2">{num_cia}</font></th>
  </tr>
</table>
<!-- START BLOCK : gastos_operacion -->
<center><span class="titulo">Gastos de Operaci&oacute;n</span></center>
<table class="print" width="70%" align="center">
  <tr>
    <th class="print" scope="col" width="20%">C&oacute;digo</th>
    <th class="print" scope="col" width="40%">Descripci&oacute;n de Gasto </th>
    <th class="print" scope="col">Importe</th>
  </tr>
  <!-- START BLOCK : fila_totales -->
  <tr class="print">
    <td class="vprint"><b>{cod_total}</b></td>
    <td class="vprint"><b>{nombre_total}</b></td>
    <td class="rprint"><b>{importe_total}</b></td>
  </tr>
  <!-- END BLOCK : fila_totales -->
   <tr class="print_total">
  	<th class="rprint" colspan="2">Totales</th>
	<th class="print_total">{gran_total_total}</th>
  </tr>
</table>

<!-- END BLOCK : gastos_operacion -->
<!-- START BLOCK : gastos_gral -->
<center><span class="titulo">Gastos Generales
</span></center>
<table class="print" width="70%" align="center">
  <tr class="print">
    <th class="print" scope="col" width="20%">C&oacute;digo</th>
    <th class="print" scope="col" width="40%">Descripci&oacute;n de gasto </th>
    <th class="print" scope="col">Importe</th>
  </tr>
  <!-- START BLOCK : fila_totales_gral -->
  <tr>
    <td class="vprint"><b>{cod_total_gral}</b></td>
    <td class="vprint"><b>{nombre_total_gral}</b></td>
    <td class="rprint"><b>{importe_total_gral}</b></td>
  </tr>
  <!-- END BLOCK : fila_totales_gral -->
   <tr>
  	<th class="rprint" colspan="2">Totales</th>
	<th class="print_total">{gran_total_total}</th>
  </tr>
</table>
<br>
<table class="print" width="70%" align="center">
   <tr>
  	<th class="print" colspan="2">Gran Total</th>
	<th class="print_total">{gran_total}</th>
  </tr>
</table>

<!-- END BLOCK : gastos_gral -->
<br>
<!-- START BLOCK : listado_one -->
<center><span class="titulo">Gastos de Oficina </span></center>
<table class="print" width="70%" align="center">
    <tr class="print">
    <th class="print" scope="col">Concepto</th>
    <th class="print" scope="col">Egreso</th>
    <th class="print" scope="col">Ingreso</th>
	<th class="print" scope="col">Balance</th>
    <th class="print" scope="col">Fecha</th>
  </tr>
  <!-- START BLOCK : fila_one -->
  <tr class="print">
    <td class="vprint">{concepto_one}</td>
    <td class="print">{egreso_one}</td>
    <td class="print">{ingreso_one}</td>
    <td class="print">{afecta_one}</td>
    <td class="print">{fecha_one}</td>
  </tr>
  <!-- END BLOCK : fila_one -->
  <tr>
    <th class="print">Total de Gastos </th>
    <th class="print_total">{total_egreso_one}</th>
    <th class="print_total">{total_ingreso_one}</th>
	<th class="print">Total de la Compa&ntilde;&iacute;a </th>
    <th class="print_total" >{total_compania_one}</th>
  </tr>
</table>
<!-- END BLOCK : listado_one -->
<!--<br>-->
<!-- START BLOCK : listado_totales -->
<!--<table class="print" align="center" width="100%">
  <tr>
    <th class="print">&nbsp;</th>
	<th colspan="3" class="print" scope="col">Existencia Inicial </th>
    <th colspan="2" class="print" scope="col">Entradas</th>
    <th colspan="2" class="print" scope="col">Salidas</th>
    <th colspan="3" class="print" scope="col">Existencia Actual </th>
  </tr>
  <tr>
    <th class="print" scope="col">Materia Prima </th>
    <th class="print" scope="col">Unidades</th>
    <th class="print" scope="col">Valores</th>
    <th class="print" scope="col">Costo<br> 
    Promedio </th>
    <th class="print" scope="col">Unidades</th>
    <th class="print" scope="col">Valores</th>
    <th class="print" scope="col">Unidades</th>
    <th class="print" scope="col">Valores</th>
    <th class="print" scope="col">Unidades</th>
    <th class="print" scope="col">Valores</th>
    <th class="print" scope="col">Costo<br> 
    Promedio </th>
  </tr>-->
  <!-- START BLOCK : mp_total -->
  <!--<tr>
    <td class="vprint">{codmp_total} {nombremp_total}</td>
    <td class="rprint">{unidades_anteriores_total}</td>
    <td class="rprint">{valores_anteriores_total}</td>
    <td class="rprint">{costo_anterior_total}</td>
    <td class="rprint">{total_unidades_entrada_total}</td>
    <td class="rprint">{total_valores_entrada_total}</td>
    <td class="rprint">{total_unidades_salida_total}</td>
    <td class="rprint">{total_valores_salida_total}</td>
    <td class="rprint">{total_unidades_total}</td>
    <td class="rprint">{total_valores_total}</td>
    <td class="rprint">{ultimo_costo_promedio_total}</td>
  </tr>-->
  <!-- END BLOCK : mp_total -->
  <!--<tr>
    <th class="rprint">Total General </th>
    <th class="rprint">&nbsp;</th>
    <th class="print_total">{total_valores_anteriores}</th>
    <th class="rprint">&nbsp;</th>
    <th class="rprint">&nbsp;</th>
    <th class="print_total">{total_valores_entrada_total}</th>
    <th class="rprint">&nbsp;</th>
    <th class="print_total">{total_valores_salida_total}</th>
    <th class="rprint">&nbsp;</th>
    <th class="print_total">{total_valores_total}</th>
    <th class="rprint">&nbsp;</th>
  </tr>
  <caption class="caption" align="bottom">Al saldo total de Compras se restan las Compras Directas</caption>
</table>-->
<!-- END BLOCK : listado_totales -->
<!-- START BLOCK : salto_pagina -->
<br style="page-break-after:always;">
<!-- END BLOCK : salto_pagina -->
<!-- END BLOCK : reporte -->
<!-- START BLOCK : siguiente -->
<script language="javascript" type="text/javascript">
	function siguiente() {
		//window.print();
		//window.location = "./bal_esr_con.php?ini={ini}&fin={fin}&todas=TRUE&mes={mes}";
	}
	
	window.onload = siguiente();
</script>
<!-- END BLOCK : siguiente -->
<!-- START BLOCK : cerrar -->
<script language="javascript" type="text/javascript">
	function cerrar() {
		//window.print();
		//self.close();
	}
	
	window.onload = cerrar();
</script>
<!-- END BLOCK : cerrar -->
</body>
</html>