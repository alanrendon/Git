<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Estado de Resultados</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="./styles/tablas.css" rel="stylesheet" type="text/css">
<link href="./styles/esr.css" rel="stylesheet" type="text/css">
<link href="./styles/impresion.css" rel="stylesheet" type="text/css">
<link href="file:///C|/Documents%20and%20Settings/John%20Talbain/Escritorio/Lecaroz/styles/esr.css" rel="stylesheet" type="text/css">
<script language="javascript" type="text/javascript">
<!--
function view(num_cia, mes, anio, cod) {
	var win = window.open("bal_con_gas.php?num_cia=" + num_cia + "&mes=" + mes + "&anio=" + anio + "&cod=" + cod,"","toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width=500,height=400");
	win.focus();
}
//-->
</script>
</head>

<body>
<!-- START BLOCK : reporte -->
<table width="100%" cellpadding="0" cellspacing="0" align="center">
  <tr class="center">
    <th class="left" scope="col"><font size="+2">{num_cia}</font></th>
    <th scope="col" class="center"><font color="#000099" size="+1">{nombre_cia}<br>
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
    <td colspan="3" class="left">Venta Zapateria </td>
    <td colspan="2">&nbsp;</td>
    <td class="left">{venta_zap} {p_venta_zap}</td>
    <td colspan="2" class="left">&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td colspan="3" class="left">Abono Empleados </td>
    <td colspan="2" class="left">{abono_emp}</td>
    <td class="left">&nbsp;</td>
    <td colspan="2">&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td colspan="3" class="left">Otros</td>
    <td colspan="2" class="left">{otros}</td>
    <td class="left">&nbsp;</td>
    <td colspan="2">&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td colspan="3" class="left">Total de Otros </td>
    <td colspan="2" class="left">&nbsp;</td>
    <td class="left">{total_otros}</td>
    <td colspan="2" class="left">&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td colspan="3" class="left">Menos Errores </td>
    <td colspan="2">&nbsp;</td>
    <td class="left">{errores}</td>
    <td colspan="2" class="left">&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td colspan="3" class="left_total">Ventas Netas </td>
    <td colspan="2">&nbsp;</td>
    <td class="left">&nbsp;</td>
    <td colspan="2" class="left_total">{ventas_netas}</td>
    <td class="left">&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td colspan="10" class="left_total">&nbsp;</td>
  </tr>
  <tr>
    <td colspan="3" class="left">Inventario Anterior </td>
    <td colspan="2" class="left">{inventario_anterior}</td>
    <td>&nbsp;</td>
    <td colspan="2">&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td colspan="3" class="left">+ Compras </td>
    <td colspan="2" class="left">{compras}</td>
    <td>&nbsp;</td>

    <td colspan="2" class="left_total">&nbsp;</td>
    <td class="left_total">&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td colspan="3" class="left">-Desc. Compras </td>
    <td colspan="2" class="left">{desc_compras}</td>
    <td>&nbsp;</td>
    <td colspan="2" class="left_total">&nbsp;</td>
    <td class="left_total">&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td colspan="3" class="left">+ Traspaso Pares </td>
    <td colspan="2" class="left">{traspaso}</td>
    <td>&nbsp;</td>
    <td colspan="2" class="left">&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td colspan="3" class="left">- Devoluciones </td>
    <td colspan="2" class="left">{devoluciones}</td>
    <td>&nbsp;</td>
    <td colspan="2" class="left">&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td colspan="3" class="left">- Inventario Actual </td>
    <td colspan="2" class="left">{inventario_actual}</td>
    <td>&nbsp;</td>
    <td colspan="2" class="left">&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td colspan="3" class="left">= Mat. Prima Utilizada </td>
    <td colspan="2" class="left">&nbsp;</td>
    <td class="left">{mat_prima_utilizada}</td>
    <td colspan="2">&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td colspan="3" class="left">- Desc. Pagos </td>
    <td colspan="2" class="left">&nbsp;</td>
    <td class="left">{desc_pagos}</td>
    <td colspan="2">&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td colspan="3" class="left">- Devoluciones otros meses </td>
    <td colspan="2" class="left">&nbsp;</td>
    <td class="left">{dev_otros_meses}</td>
    <td colspan="2">&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td colspan="3" class="left">- Devoluciones otras tiendas </td>
    <td colspan="2" class="left">&nbsp;</td>
    <td class="left">{dev_otras_tiendas}</td>
    <td colspan="2">&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td colspan="3" class="left">- Devoluciones por otras tiendas </td>
    <td colspan="2" class="left">&nbsp;</td>
    <td class="left">{dev_por_otras_tiendas}</td>
    <td colspan="2">&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td colspan="5" class="left_total">= Costo de Venta </td>
    <td>&nbsp;</td>
    <td colspan="2" class="left_total">{costo_venta} <span style="font-size:8pt; font-weight:bold; color:#CC9900;">{por_cos}</span> </td>
    <td class="left_total">&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td colspan="10" class="left_total">&nbsp;</td>
  </tr>
  <tr>
    <td colspan="3" class="left_total">Utilidad Bruta </td>
    <td colspan="2">&nbsp;</td>
    <td>&nbsp;</td>
    <td colspan="2" class="left_total">{utilidad_bruta} <span style="font-size:8pt; font-weight:bold; color:#CC9900;">{por_uti_bru}</span></td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td colspan="10" class="left">&nbsp;</td>
  </tr>
  <tr>
    <td colspan="3" class="left">- Gastos de Operaci&oacute;n </td>
    <td colspan="2">&nbsp;</td>
    <td class="left">{gastos_operacion}</td>
    <td colspan="2" class="left">&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td colspan="3" class="left">- Gastos Generales </td>
    <td colspan="2">&nbsp;</td>
    <td class="left">{gastos_generales}</td>
    <td colspan="2" class="left">&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td colspan="3" class="left">- Gastos por Caja </td>
    <td colspan="2" class="left">&nbsp;</td>
    <td class="left">{gastos_caja}</td>
    <td colspan="2" class="left">&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td colspan="3" class="left">- Comisiones Bancarias</td>
    <td colspan="2">&nbsp;</td>
    <td class="left">{comisiones}</td>
    <td colspan="2">&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td colspan="3" class="left">- Reservas</td>
    <td colspan="2">&nbsp;</td>
    <td class="left">{reserva_aguinaldos}</td>
    <td colspan="2">&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td colspan="3" class="left">- Pagos Anticipados </td>
    <td colspan="2">&nbsp;</td>
    <td class="left">{pagos_anticipados}</td>
    <td colspan="2">&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td colspan="3" class="left">- Gastos Pagados x Otras Cias. </td>
    <td colspan="2">&nbsp;</td>
    <td class="left">{gastos_otras_cias}</td>
    <td colspan="2">&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td colspan="3" class="left_total">= Total de Gastos </td>
    <td colspan="2">&nbsp;</td>
    <td>&nbsp;</td>
    <td colspan="2" class="left_total">{total_gastos} <span style="font-size:8pt; font-weight:bold; color:#CC9900;">{por_gas}</span></td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td colspan="3" class="left_total">+ Ingrs. Extraordinarios </td>
    <td colspan="2">&nbsp;</td>
    <td>&nbsp;</td>
    <td colspan="2" class="left_total">{ingresos_ext}</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td colspan="10" class="left">&nbsp;</td>
  </tr>
  <tr>
    <td colspan="3" class="left_total">Utilidad del Mes </td>
    <td colspan="2">&nbsp;</td>
    <td>&nbsp;</td>
    <td colspan="2" class="left_total">{utilidad_mes} <span style="font-size:8pt; font-weight:bold; color:#CC9900;">{por_uti}</span> </td>
    <td colspan="2">&nbsp;</td>
  </tr>
  <tr>
    <td colspan="10" class="left">&nbsp;</td>
  </tr>
  <tr>
    <td colspan="5" class="left">&nbsp;</td>
    <td colspan="2" class="left">Enc. Inici&oacute;: </td>
    <td colspan="3" class="left">{inicio}</td>
  </tr>
  <tr>
    <td colspan="5" class="left">&nbsp;</td>
    <td colspan="2" class="left">Enc. Termin&oacute;: </td>
    <td colspan="3" class="left">{termino}</td>
  </tr>
  <tr>
    <td colspan="10" class="left">&nbsp;</td>
  </tr>
  <tr>
    <td class="left">&nbsp;</td>
    <td class="left_total">Sal. Ban.</td>
    <td class="left_total">Sal. Pro. </td>
    <td class="left_total">Par. Ven.</td>
    <td class="left_total">Inv. Final </td>
    <td class="left_total">Efectivo</td>
    <td colspan="3" class="left_total">{titulo_reserva}</td>
    <td class="left_total">{titulo_importe}</td>
  </tr>
  <tr>
    <td class="left">Ene</td>
    <td class="left">{sal1}</td>
    <td class="left">{salpro1}</td>
    <td class="left">{parven1}</td>
    <td class="left">{inv1}</td>
    <td class="left">{efe1}</td>
    <td colspan="3" class="left">{nombre_reserva1}</td>
    <td class="left">{importe_reserva1}</td>
  </tr>
  <tr>
    <td class="left">Feb</td>
    <td class="left">{sal2}</td>
    <td class="left">{salpro2}</td>
    <td class="left">{parven2}</td>
    <td class="left">{inv2}</td>
    <td class="left">{efe2}</td>
    <td colspan="3" class="left">{nombre_reserva2}</td>
    <td class="left">{importe_reserva2}</td>
  </tr>
  <tr>
    <td class="left">Mar</td>
    <td class="left">{sal3}</td>
    <td class="left">{salpro3}</td>
    <td class="left">{parven3}</td>
    <td class="left">{inv3}</td>
    <td class="left">{efe3}</td>
    <td colspan="3" class="left">{nombre_reserva3}</td>
    <td class="left">{importe_reserva3}</td>
  </tr>
  <tr>
    <td class="left">Abr</td>
    <td class="left">{sal4}</td>
    <td class="left">{salpro4}</td>
    <td class="left">{parven4}</td>
    <td class="left">{inv4}</td>
    <td class="left">{efe4}</td>
    <td colspan="3" class="left">{nombre_reserva4}</td>
    <td class="left">{importe_reserva4}</td>
  </tr>
  <tr>
    <td class="left">May</td>
    <td class="left">{sal5}</td>
    <td class="left">{salpro5}</td>
    <td class="left">{parven5}</td>
    <td class="left">{inv5}</td>
    <td class="left">{efe5}</td>
    <td colspan="3" class="left">{nombre_reserva5}</td>
    <td class="left">{importe_reserva5}</td>
  </tr>
  <tr>
    <td class="left">Jun</td>
    <td class="left">{sal6}</td>
    <td class="left">{salpro6}</td>
    <td class="left">{parven6}</td>
    <td class="left">{inv6}</td>
    <td class="left">{efe6}</td>
    <td colspan="3" class="left">{nombre_reserva6}</td>
    <td class="left">{importe_reserva6}</td>
  </tr>
  <tr>
    <td class="left">Jul</td>
    <td class="left">{sal7}</td>
    <td class="left">{salpro7}</td>
    <td class="left">{parven7}</td>
    <td class="left">{inv7}</td>
    <td class="left">{efe7}</td>
    <td colspan="3" class="left">{nombre_reserva7}</td>
    <td class="left">{importe_reserva7}</td>
  </tr>
  <tr>
    <td class="left">Ago</td>
    <td class="left">{sal8}</td>
    <td class="left">{salpro8}</td>
    <td class="left">{parven8}</td>
    <td class="left">{inv8}</td>
    <td class="left">{efe8}</td>
    <td colspan="3" class="left">{nombre_reserva8}</td>
    <td class="left">{importe_reserva8}</td>
  </tr>
  <tr>
    <td class="left">Sep</td>
    <td class="left">{sal9}</td>
    <td class="left">{salpro9}</td>
    <td class="left">{parven9}</td>
    <td class="left">{inv9}</td>
    <td class="left">{efe9}</td>
    <td colspan="3" class="left">{nombre_reserva9}</td>
    <td class="left">{importe_reserva9}</td>
  </tr>
  <tr>
    <td class="left">Oct</td>
    <td class="left">{sal10}</td>
    <td class="left">{salpro10}</td>
    <td class="left">{parven10}</td>
    <td class="left">{inv10}</td>
    <td class="left">{efe10}</td>
    <td colspan="3" class="left">{nombre_reserva10}</td>
    <td class="left">{importe_reserva10}</td>
  </tr>
  <tr>
    <td class="left">Nov</td>
    <td class="left">{sal11}</td>
    <td class="left">{salpro11}</td>
    <td class="left">{parven11}</td>
    <td class="left">{inv11}</td>
    <td class="left">{efe11}</td>
    <td colspan="3" class="left">&nbsp;</td>
    <td class="left">&nbsp;</td>
  </tr>
  <tr>
    <td height="21" class="left">Dic</td>
    <td class="left">{sal12}</td>
    <td class="left">{salpro12}</td>
    <td class="left">{parven12}</td>
    <td class="left">{inv12}</td>
    <td class="left">{efe12}</td>
    <td colspan="3" class="left">&nbsp;</td>
    <td class="left">&nbsp;</td>
  </tr>
  <tr>
    <td class="left_total">Tot</td>
    <td class="left_total">{tot_sal}</td>
    <td class="left_total">{tot_salpro}</td>
    <td class="left_total">{tot_parven}</td>
    <td class="left_total">{tot_inv}</td>
    <td class="left_total">{tot_efe}</td>
    <td colspan="4" class="left">&nbsp;</td>
  </tr>
  <tr>
    <td colspan="3" class="left_total">&nbsp;</td>
    <td colspan="2" class="left_total">&nbsp;</td>
    <td>&nbsp;</td>
    <td colspan="2">&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td colspan="5" class="left_total">Utilidad A&ntilde;o Anterior</td>
    <td>&nbsp;</td>
    <td colspan="2">&nbsp;</td>
    <td class="left_total">{utilidad_anio_ant}</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td colspan="3" class="left">&nbsp;</td>
    <td colspan="2">&nbsp;</td>
    <td>&nbsp;</td>
    <td colspan="2">&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td colspan="5" class="left_total">Datos A&ntilde;o Anterior </td>
    <td>&nbsp;</td>
    <td colspan="2">&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td class="left">{tant_1}</td>
    <td colspan="2" class="left">{ant_1}</td>
    <td class="left">{tant_4}</td>
    <td class="left">{ant_4}</td>
    <td>&nbsp;</td>
    <td class="left">{tant_7}</td>
    <td class="left">{ant_7}</td>
    <td class="left">{tant_10}</td>
    <td class="left">{ant_10}</td>
  </tr>
  <tr>
    <td class="left">{tant_2}</td>
    <td colspan="2" class="left">{ant_2}</td>
    <td class="left">{tant_5}</td>
    <td class="left">{ant_5}</td>
    <td>&nbsp;</td>
    <td class="left">{tant_8}</td>
    <td class="left">{ant_8}</td>
    <td class="left">{tant_11}</td>
    <td class="left">{ant_11}</td>
  </tr>
  <tr>
    <td class="left">{tant_3}</td>
    <td colspan="2" class="left">{ant_3}</td>
    <td class="left">{tant_6}</td>
    <td class="left">{ant_6}</td>
    <td>&nbsp;</td>
    <td class="left">{tant_9}</td>
    <td class="left">{ant_9}</td>
    <td class="left">{tant_12}</td>
    <td class="left">{ant_12}</td>
  </tr>
  <tr>
    <td colspan="3">&nbsp;</td>
    <td colspan="2">&nbsp;</td>
    <td>&nbsp;</td>
    <td colspan="2">&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td colspan="5" class="left_total">Datos A&ntilde;o Actual </td>
    <td>&nbsp;</td>
    <td colspan="2">&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td class="left">{tact_1}</td>
    <td colspan="2" class="left">{act_1}</td>
    <td class="left">{tact_4}</td>
    <td class="left">{act_4}</td>
    <td>&nbsp;</td>
    <td class="left">{tact_7}</td>
    <td class="left">{act_7}</td>
    <td class="left">{tact_10}</td>
    <td class="left">{act_10}</td>
  </tr>
  <tr>
    <td class="left">{tact_2}</td>
    <td colspan="2" class="left">{act_2}</td>
    <td class="left">{tact_5}</td>
    <td class="left">{act_5}</td>
    <td>&nbsp;</td>
    <td class="left">{tact_8}</td>
    <td class="left">{act_8}</td>
    <td class="left">{tact_11}</td>
    <td class="left">{act_11}</td>
  </tr>
  <tr>
    <td class="left">{tact_3}</td>
    <td colspan="2" class="left">{act_3}</td>
    <td class="left">{tact_6}</td>
    <td class="left">{act_6}</td>
    <td>&nbsp;</td>
    <td class="left">{tact_9}</td>
    <td class="left">{act_9}</td>
    <td class="left">{tact_12}</td>
    <td class="left">{act_12}</td>
  </tr>
  <tr>
    <td class="left">&nbsp;</td>
    <td colspan="2" class="left">&nbsp;</td>
    <td class="left">&nbsp;</td>
    <td class="left">&nbsp;</td>
    <td>&nbsp;</td>
    <td class="left">&nbsp;</td>
    <td class="left">&nbsp;</td>
    <td>&nbsp;</td>
    <td class="left">&nbsp;</td>
  </tr>
  <tr>
    <td colspan="5" class="center">Datos A&ntilde;o Actual </td>
    <td class="center">&nbsp;</td>
    <td colspan="4" class="center">Datos A&ntilde;o Anterior </td>
  </tr>
  <tr>
    <td class="left">&nbsp;</td>
    <td class="left_total">Vta. Zap. </td>
    <td class="left_total">Clientes</td>
    <td class="left_total">Prom x Cliente </td>
    <td class="left_total">&nbsp;</td>
    <td class="left_total">&nbsp;</td>
    <td class="left_total">Vta. Zap. </td>
    <td class="left_total">&nbsp;</td>
    <td class="left_total">Clientes</td>
    <td class="left_total">Prom x Cliente </td>
  </tr>
  <tr>
    <td class="left">Ene</td>
    <td class="left">{vta_1}</td>
    <td class="left">{clientes_1}</td>
    <td class="left">{prom_1}</td>
    <td class="left">&nbsp;</td>
    <td class="left">&nbsp;</td>
    <td class="left">{vta_ant_1}</td>
    <td class="left">&nbsp;</td>
    <td class="left">{clientes_ant_1}</td>
    <td class="left">{prom_ant_1}</td>
  </tr>
  <tr>
    <td class="left">Feb</td>
    <td class="left">{vta_2}</td>
    <td class="left">{clientes_2}</td>
    <td class="left">{prom_2}</td>
    <td class="left">&nbsp;</td>
    <td class="left">&nbsp;</td>
    <td class="left">{vta_ant_2}</td>
    <td class="left">&nbsp;</td>
    <td class="left">{clientes_ant_2}</td>
    <td class="left">{prom_ant_2}</td>
  </tr>
  <tr>
    <td class="left">Mar</td>
    <td class="left">{vta_3}</td>
    <td class="left">{clientes_3}</td>
    <td class="left">{prom_3}</td>
    <td class="left">&nbsp;</td>
    <td class="left">&nbsp;</td>
    <td class="left">{vta_ant_3}</td>
    <td class="left">&nbsp;</td>
    <td class="left">{clientes_ant_3}</td>
    <td class="left">{prom_ant_3}</td>
  </tr>
  <tr>
    <td class="left">Abr</td>
    <td class="left">{vta_4}</td>
    <td class="left">{clientes_4}</td>
    <td class="left">{prom_4}</td>
    <td class="left">&nbsp;</td>
    <td class="left">&nbsp;</td>
    <td class="left">{vta_ant_4}</td>
    <td class="left">&nbsp;</td>
    <td class="left">{clientes_ant_4}</td>
    <td class="left">{prom_ant_4}</td>
  </tr>
  <tr>
    <td class="left">May</td>
    <td class="left">{vta_5}</td>
    <td class="left">{clientes_5}</td>
    <td class="left">{prom_5}</td>
    <td class="left">&nbsp;</td>
    <td class="left">&nbsp;</td>
    <td class="left">{vta_ant_5}</td>
    <td class="left">&nbsp;</td>
    <td class="left">{clientes_ant_5}</td>
    <td class="left">{prom_ant_5}</td>
  </tr>
  <tr>
    <td class="left">Jun</td>
    <td class="left">{vta_6}</td>
    <td class="left">{clientes_6}</td>
    <td class="left">{prom_6}</td>
    <td class="left">&nbsp;</td>
    <td class="left">&nbsp;</td>
    <td class="left">{vta_ant_6}</td>
    <td class="left">&nbsp;</td>
    <td class="left">{clientes_ant_6}</td>
    <td class="left">{prom_ant_6}</td>
  </tr>
  <tr>
    <td class="left">Jul</td>
    <td class="left">{vta_7}</td>
    <td class="left">{clientes_7}</td>
    <td class="left">{prom_7}</td>
    <td class="left">&nbsp;</td>
    <td class="left">&nbsp;</td>
    <td class="left">{vta_ant_7}</td>
    <td class="left">&nbsp;</td>
    <td class="left">{clientes_ant_7}</td>
    <td class="left">{prom_ant_7}</td>
  </tr>
  <tr>
    <td class="left">Ago</td>
    <td class="left">{vta_8}</td>
    <td class="left">{clientes_8}</td>
    <td class="left">{prom_8}</td>
    <td class="left">&nbsp;</td>
    <td class="left">&nbsp;</td>
    <td class="left">{vta_ant_8}</td>
    <td class="left">&nbsp;</td>
    <td class="left">{clientes_ant_8}</td>
    <td class="left">{prom_ant_8}</td>
  </tr>
  <tr>
    <td class="left">Sep</td>
    <td class="left">{vta_9}</td>
    <td class="left">{clientes_9}</td>
    <td class="left">{prom_9}</td>
    <td class="left">&nbsp;</td>
    <td class="left">&nbsp;</td>
    <td class="left">{vta_ant_9}</td>
    <td class="left">&nbsp;</td>
    <td class="left">{clientes_ant_9}</td>
    <td class="left">{prom_ant_9}</td>
  </tr>
  <tr>
    <td class="left">Oct</td>
    <td class="left">{vta_10}</td>
    <td class="left">{clientes_10}</td>
    <td class="left">{prom_10}</td>
    <td class="left">&nbsp;</td>
    <td class="left">&nbsp;</td>
    <td class="left">{vta_ant_10}</td>
    <td class="left">&nbsp;</td>
    <td class="left">{clientes_ant_10}</td>
    <td class="left">{prom_ant_10}</td>
  </tr>
  <tr>
    <td class="left">Nov</td>
    <td class="left">{vta_11}</td>
    <td class="left">{clientes_11}</td>
    <td class="left">{prom_11}</td>
    <td class="left">&nbsp;</td>
    <td class="left">&nbsp;</td>
    <td class="left">{vta_ant_11}</td>
    <td class="left">&nbsp;</td>
    <td class="left">{clientes_ant_11}</td>
    <td class="left">{prom_ant_11}</td>
  </tr>
  <tr>
    <td class="left">Dic</td>
    <td class="left">{vta_12}</td>
    <td class="left">{clientes_12}</td>
    <td class="left">{prom_12}</td>
    <td class="left">&nbsp;</td>
    <td class="left">&nbsp;</td>
    <td class="left">{vta_ant_12}</td>
    <td class="left">&nbsp;</td>
    <td class="left">{clientes_ant_12}</td>
    <td class="left">{prom_ant_12}</td>
  </tr>
  <tr>
    <td class="left_total">Tot</td>
    <td class="left_total">{tot_vta}</td>
    <td class="left_total">{tot_clientes}</td>
    <td class="left_total">&nbsp;</td>
    <td class="left_total">&nbsp;</td>
    <td class="left_total">&nbsp;</td>
    <td class="left_total">{tot_vta_ant}</td>
    <td class="left_total">&nbsp;</td>
    <td class="left_total">{tot_clientes_ant}</td>
    <td class="left_total">&nbsp;</td>
  </tr>
</table>
<br style="page-break-after:always;">
<!-- START BLOCK : gastos_extras -->
<!--<br>
<br>
<br>-->
<!--<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="top">-->
  <table width="100%" align="center">
    <tr>
      <td class="print_encabezado">Cia.: {num_cia} </td>
      <td class="print_encabezado" align="center">{nombre_cia}</td>
      <td class="print_encabezado" align="right">Cia.: {num_cia} </td>
    </tr>
    <tr>
      <td width="20%">&nbsp;</td>
      <td width="60%" class="print_encabezado" align="center">Relaci&oacute;n de Gastos Totales <br>
      al d&iacute;a {dia} de {mes} de {anio} </td>
      <td width="20%">&nbsp;</td>
    </tr>
  </table>
 <br>
 <table width="70%" align="center" cellpadding="0" cellspacing="0" class="print">
   <!-- START BLOCK : tipo_gasto -->
   <tr>
     <td colspan="5" class="titulo" scope="col">{tipo_gasto}</td>
   </tr>
   <tr>
     <th colspan="2" class="print" scope="col">C&oacute;digo y Concepto </th>
     <th class="print" scope="col">{title_mes} {title_anio} </th>
     <th class="print" scope="col">{title_mes_anio_ant} {title_anio_anio_ant}</th>
	 <th class="print" scope="col">{title_mes_ant} {title_anio_ant} </th>
   </tr>
   <!-- START BLOCK : fila_gasto -->
   <tr>
     <td class="print"><strong>{codgastos}</strong></td>
     <td class="vprint"><strong>{concepto}</strong></td>
     <td class="rprint"><strong><a style="text-decoration:none; color:#000000;" href="javascript:view({num_cia},{mes},{anio},{codgastos})">{importe}</a></strong></td>
     <td class="rprint"><strong><a style="text-decoration:none; color:#006600;" href="javascript:view({num_cia},{mes},{_anio_ant},{codgastos})">{anio_ant}</a></strong></td>
	 <td class="rprint"><strong><a style="text-decoration:none; color:#0000FF;" href="javascript:view({num_cia},{_mes_ant},{_anio_mes_ant},{codgastos})">{mes_ant}</a></strong></td>
   </tr>
	<!-- END BLOCK : fila_gasto -->
   <tr>
     <th colspan="2" class="print">Sub Total </th>
     <th class="rprint_total">{importe}</th>
     <th class="rprint_total">{anio_ant}</th>
	 <th class="rprint_total">{mes_ant}</th>
   </tr>
	 <!-- END BLOCK : tipo_gasto -->
   <tr>
     <td colspan="2">&nbsp;</td>
     <td>&nbsp;</td>
     <td>&nbsp;</td>
     <td>&nbsp;</td>
   </tr>
   <tr>
     <th colspan="2" class="print">Total</th>
     <th class="rprint_total">{total_importe}</th>
     <th class="rprint_total">{total_anio_ant}</th>
	 <th class="rprint_total">{total_mes_ant}</th>
   </tr>
   <tr>
     <td colspan="2">&nbsp;</td>
     <td>&nbsp;</td>
     <td>&nbsp;</td>
     <td class="rprint_total">&nbsp;</td>
   </tr>
   <!-- START BLOCK : gastos_caja -->
   <tr>
     <td colspan="5" class="titulo">GASTOS POR CAJA </td>
   </tr>
   <tr>
     <th colspan="2" class="print">Concepto</th>
     <th class="print">{title_mes} {title_anio}</th>
     <th class="print">{title_mes_anio_ant} {title_anio_anio_ant}</th>
     <th class="print">{title_mes_ant} {title_anio_ant} </th>
   </tr>
   <!-- START BLOCK : fila_caja -->
   <tr>
     <td colspan="2" class="vprint">{concepto}</td>
     <td class="rprint">{importe}</td>
     <td class="rprint">{anio_ant}</td>
     <td class="rprint">{mes_ant}</td>
   </tr>
   <!-- END BLOCK : fila_caja -->
   <tr>
     <th colspan="2" class="print">Total Gastos por Caja </th>
     <th class="rprint_total">{total_importe}</th>
     <th class="rprint_total">{total_anio_ant}</th>
     <th class="rprint_total">{total_mes_ant}</th>
   </tr>
   <!-- END BLOCK : gastos_caja -->
</table>
 <!--</td>
</tr>
</table>-->
<br style="page-break-after:always;">
<!-- END BLOCK : gastos_extras -->
<!-- Estado de resultados "comparativo" -->
<!--<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="top">-->
<table width="100%" align="center">
  <tr>
    <td class="print_encabezado">Cia.: {num_cia} </td>
    <td class="print_encabezado" align="center">{nombre_cia}</td>
    <td class="print_encabezado" align="right">Cia.: {num_cia} </td>
  </tr>
  <tr>
    <td width="20%">&nbsp;</td>
    <td width="60%" class="print_encabezado" align="center">Estado de Resultados &quot;Comparativo&quot;
de {anio}     </td>
    <td width="20%">&nbsp;</td>
  </tr>
</table>
  <br>
  <table width="70%" align="center" cellpadding="0" cellspacing="0" class="print">
    <tr>
      <th class="print" scope="col">&nbsp;</th>
      <th class="print" scope="col">{mes_act} {anio_act} </th>
	  <th class="print" scope="col">{mes_anio_ant} {anio_anio_ant}</th>
	  <th class="print" scope="col">{mes_ant} {anio_ant}</th>
    </tr>
    <tr>
      <td class="vprint">Venta Zapateria </td>
      <td class="rprint">{venta_zap}</td>
	  <td class="rprint">{venta_zap_aa}</td>
	  <td class="rprint">{vta_zap_ant}</td>
    </tr>
    <tr>
      <td class="vprint">Otros</td>
      <td class="rprint">{otros}</td>
	  <td class="rprint">{otros_aa}</td>
	  <td class="rprint">{otros_ant}</td>
    </tr>
    <tr>
      <td class="vprint">Total Otros </td>
      <td class="rprint">{total_otros}</td>
	  <td class="rprint">{total_otros_aa}</td>
	  <td class="rprint">{total_otros_ant}</td>
    </tr>
    <tr>
      <td class="vprint">Menos Errores </td>
      <td class="rprint">{errores}</td>
	  <td class="rprint">{errores_aa}</td>
	  <td class="rprint">{errores_ant}</td>
    </tr>
    <tr>
      <td class="vprint"><strong><font size="3">Ventas Netas</font> </strong></td>
      <td class="rprint"><strong><font size="3">{ventas_netas}</font></strong></td>
	  <td class="rprint"><strong><font size="3">{ventas_netas_aa}</font></strong></td>
	  <td class="rprint"><strong><font size="3">{ventas_netas_ant}</font></strong></td>
    </tr>
    <tr>
      <td class="vprint">&nbsp;</td>
      <td class="rprint">&nbsp;</td>
      <td class="rprint">&nbsp;</td>
      <td class="rprint">&nbsp;</td>
    </tr>
    <tr>
      <td class="vprint">Inventario Anterior</td>
      <td class="rprint">{inventario_anterior}</td>
	  <td class="rprint">{inventario_anterior_aa}</td>
	  <td class="rprint">{inventario_anterior_ant}</td>
    </tr>
    <tr>
      <td class="vprint">+ Compras </td>
      <td class="rprint">{compras}</td>
	  <td class="rprint">{compras_aa}</td>
	  <td class="rprint">{compras_ant}</td>
    </tr>
    <tr>
      <td class="vprint">- Inventario Actual </td>
      <td class="rprint">{inventario_actual}</td>
	  <td class="rprint">{inventario_actual_aa}</td>
	  <td class="rprint">{inventario_actual_ant}</td>
    </tr>
    <tr>
      <td class="vprint">= Mat. Prima Utilizada </td>
      <td class="rprint">{mat_prima_utilizada}</td>
	  <td class="rprint">{mat_prima_utilizada_aa}</td>
	  <td class="rprint">{mat_prima_utilizada_ant}</td>
    </tr>
    <tr>
      <td class="vprint"><strong><font size="3">= Costo de Producci&oacute;n</font></strong></td>
      <td class="rprint"><strong><font size="3">{costo_produccion}</font></strong></td>
	  <td class="rprint"><strong><font size="3">{costo_produccion_aa}</font></strong></td>
	  <td class="rprint"><strong><font size="3">{costo_produccion_ant}</font></strong></td>
    </tr>
    <tr>
      <td class="vprint">&nbsp;</td>
      <td class="rprint">&nbsp;</td>
      <td class="rprint">&nbsp;</td>
      <td class="rprint">&nbsp;</td>
    </tr>
    <tr>
      <td class="vprint"><strong><font size="3">Utilidad Bruta</font> </strong></td>
      <td class="rprint"><strong><font size="3">{utilidad_bruta}</font></strong></td>
	  <td class="rprint"><strong><font size="3">{utilidad_bruta_aa}</font></strong></td>
	  <td class="rprint"><strong><font size="3">{utilidad_bruta_ant}</font></strong></td>
    </tr>
    <tr>
      <td class="vprint">&nbsp;</td>
      <td class="rprint">&nbsp;</td>
      <td class="rprint">&nbsp;</td>
      <td class="rprint">&nbsp;</td>
    </tr>
    <tr>
      <td class="vprint">- Gastos Generales </td>
      <td class="rprint">{gastos_generales}</td>
	  <td class="rprint">{gastos_generales_aa}</td>
	  <td class="rprint">{gastos_generales_ant}</td>
    </tr>
    <tr>
      <td class="vprint">- Gastos por Caja </td>
      <td class="rprint">{gastos_caja}</td>
	  <td class="rprint">{gastos_caja_aa}</td>
	  <td class="rprint">{gastos_caja_ant}</td>
    </tr>
    <tr>
      <td class="vprint">- Reserva para Aguinaldos</td>
      <td class="rprint">{reserva_aguinaldos}</td>
	  <td class="rprint">{reserva_aguinaldos_aa}</td>
	  <td class="rprint">{reserva_aguinaldos_ant}</td>
    </tr>
    <tr>
      <td class="vprint">- Pagos Anticipados </td>
      <td class="rprint">{pagos_anticipados}</td>
	  <td class="rprint">{pagos_anticipados_aa}</td>
	  <td class="rprint">{pagos_anticipados_ant}</td>
    </tr>
    <tr>
      <td class="vprint">- Gastos Pagados x Otras Cias.</td>
      <td class="rprint">{gastos_otras_cias}</td>
	  <td class="rprint">{gastos_otras_cias_aa}</td>
	  <td class="rprint">{gastos_otras_cias_ant}</td>
    </tr>
    <tr>
      <td class="vprint"><strong><font size="3">= Total de Gastos</font> </strong></td>
      <td class="rprint"><strong><font size="3">{total_gastos}</font></strong></td>
	  <td class="rprint"><strong><font size="3">{total_gastos_aa}</font></strong></td>
	  <td class="rprint"><strong><font size="3">{total_gastos_ant}</font></strong></td>
    </tr>
    <tr>
      <td class="vprint">&nbsp;</td>
      <td class="rprint">&nbsp;</td>
      <td class="rprint">&nbsp;</td>
      <td class="rprint">&nbsp;</td>
    </tr>
    <tr>
      <td class="vprint"><strong><font size="3">+ Ingresos Extraordinarios</font> </strong></td>
      <td class="rprint"><strong><font size="3">{ingresos_ext}</font> </strong></td>
	  <td class="rprint"><strong><font size="3">{ingresos_ext_aa}</font></strong></td>
	  <td class="rprint"><strong><font size="3">{ingresos_ext_ant}</font> </strong></td>
    </tr>
    <tr>
      <td class="vprint">&nbsp;</td>
      <td class="rprint">&nbsp;</td>
      <td class="rprint">&nbsp;</td>
      <td class="rprint">&nbsp;</td>
    </tr>
    <tr>
      <td class="vprint"><strong><font size="3">Utilidad del Mes </font> </strong></td>
      <td class="rprint"><strong><font size="3">{utilidad_mes}</font> </strong></td>
	  <td class="rprint"><strong><font size="3">{utilidad_mes_aa}</font></strong></td>
	  <td class="rprint"><strong><font size="3">{utilidad_mes_ant}</font> </strong></td>
    </tr>
</table>
<!--</td>
</tr>
</table>-->
<br style="page-break-after:always;">
<!--<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="top">-->

<!-- START BLOCK : listado_gastos -->
<!--<br>
<br>
<br>-->
<table width="100%">
  <tr>
    <td class="print_encabezado">Cia.: {num_cia} </td>
    <td class="print_encabezado" align="center">{nombre_cia}</td>
    <td class="print_encabezado" align="right">Cia.: {num_cia} </td>
  </tr>
  <tr>
    <td width="20%">&nbsp;</td>
    <td width="60%" class="print_encabezado" align="center">Listado de Cheques <br>
      al d&iacute;a {dia} de {mes} de {anio} </td>
    <td width="20%">&nbsp;</td>
  </tr>
</table>
  <br>
  <table width="100%" cellpadding="0" cellspacing="0" class="print">
    <tr>
      <th colspan="2" class="print" scope="col">C&oacute;digo y Descripci&oacute;n del Gasto </th>
      <th class="print" scope="col">A nombre de </th>
      <th class="print" scope="col">Concepto</th>
      <th class="print" scope="col">Importe</th>
      <th class="print" scope="col">Cheque</th>
      <th class="print" scope="col">Fecha</th>
    </tr>
    <!-- START BLOCK : gasto -->
	<!-- START BLOCK : fila -->
	<tr>
      <td class="print">{codgastos}</td>
      <td class="vprint">{descripcion}</td>
      <td class="vprint">{a_nombre}</td>
      <td class="vprint">{facturas}{concepto}</td>
      <td class="rprint">{importe}</td>
      <td class="print">{folio}</td>
      <td class="print">{fecha}</td>
	</tr>
	<!-- END BLOCK : fila -->
    <!-- START BLOCK : total -->
	<tr>
      <td colspan="4" class="rprint"><strong>Total Gasto </strong></td>
      <td class="rprint"><strong>{total_gasto}</strong></td>
      <td colspan="2" class="print">&nbsp;</td>
    </tr>
	  <!-- END BLOCK : total -->
    <tr>
      <td colspan="7">&nbsp;</td>
    </tr>
	  <!-- END BLOCK : gasto -->
	  <tr>
      <th colspan="4" class="rprint_total">Totales</th>
      <th class="rprint_total">{total_gastos}</th>
      <th colspan="2" class="rprint_total">&nbsp;</th>
      </tr>
  </table>

<!--</td>
</tr>
</table>-->
<!-- END BLOCK : listado_gastos -->
<!-- START BLOCK : salto -->
<br style="page-break-after:always;">
<!-- END BLOCK : salto -->
<!-- END BLOCK : reporte -->
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