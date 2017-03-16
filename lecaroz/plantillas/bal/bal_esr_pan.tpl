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
    <td colspan="2" class="left">Venta en puerta </td>
    <td colspan="2">&nbsp;</td>
    <td class="left">&nbsp;</td>
    <td colspan="2" class="left">{venta_puerta}</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td colspan="2" class="left">Bases</td>
    <td colspan="2" class="left">{bases}</td>
    <td class="left">&nbsp;</td>
    <td colspan="2" class="left">&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td colspan="2" class="left">Barredura</td>
    <td colspan="2" class="left">{barredura}</td>
    <td>&nbsp;</td>
    <td colspan="2" class="left_total">&nbsp;</td>
    <td class="left_total">&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td colspan="2" class="left">Pastillaje</td>
    <td colspan="2" class="left">{pastillaje}</td>
    <td>&nbsp;</td>
    <td colspan="2">&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td colspan="2" class="left">Abono Empleados </td>
    <td colspan="2" class="left">{abono_emp}</td>
    <td class="left">&nbsp;</td>
    <td colspan="2">&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td colspan="2" class="left">Otros</td>
    <td colspan="2" class="left">{otros}</td>
    <td class="left">&nbsp;</td>
    <td colspan="2">&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td colspan="2" class="left">Total de Otros </td>
    <td colspan="2" class="left">&nbsp;</td>
    <td class="left">&nbsp;</td>
    <td colspan="2" class="left">{total_otros}</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td colspan="2" class="left">Abono Reparto </td>
    <td colspan="2" class="left">&nbsp;</td>
    <td class="left">&nbsp;</td>
    <td colspan="2" class="left">{abono_reparto}</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td colspan="2" class="left">Menos Errores </td>
    <td colspan="2">&nbsp;</td>
    <td class="left">&nbsp;</td>
    <td colspan="2" class="left">{errores}</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td colspan="2" class="left_total">Ventas Netas </td>
    <td colspan="2">&nbsp;</td>
    <td class="left">&nbsp;</td>
    <td colspan="2" class="left">&nbsp;</td>
    <td class="left_total">{ventas_netas}</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td colspan="2" class="left_total">&nbsp;</td>
    <td colspan="2">&nbsp;</td>
    <td>&nbsp;</td>
    <td colspan="2" class="left_total">&nbsp;</td>
    <td class="left_total">&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td colspan="2" class="left">Inventario Anterior </td>
    <td colspan="2" class="left">{inventario_anterior}</td>
    <td>&nbsp;</td>
    <td colspan="2">&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td colspan="2" class="left">+ Compras </td>
    <td colspan="2" class="left">{compras}</td>
    <td>&nbsp;</td>
    <td colspan="2" class="left_total">&nbsp;</td>
    <td class="left_total">&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td colspan="2" class="left">+ Mercancias, Leche, Vino </td>
    <td colspan="2" class="left">{mercancias}</td>
    <td>&nbsp;</td>
    <td colspan="2" class="left">&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td colspan="2" class="left">- Inventario Actual </td>
    <td colspan="2" class="left">{inventario_actual}</td>
    <td>&nbsp;</td>
    <td colspan="2" class="left">&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td colspan="2" class="left">= Mat. Prima Utilizada </td>
    <td colspan="2" class="left">&nbsp;</td>
    <td>&nbsp;</td>
    <td colspan="2" class="left">{mat_prima_utilizada}</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td colspan="2" class="left">+ Mano de Obra </td>
    <td colspan="2" class="left">&nbsp;</td>
    <td>&nbsp;</td>
    <td colspan="2" class="left">{mano_obra}</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td colspan="2" class="left">+Panaderos</td>
    <td colspan="2">&nbsp;</td>
    <td class="left">&nbsp;</td>
    <td colspan="2" class="left">{panaderos}</td>
    <td class="left">&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td colspan="2" class="left">+ Gastos de Fabricaci&oacute;n </td>
    <td colspan="2">&nbsp;</td>
    <td class="left">&nbsp;</td>
    <td colspan="2" class="left">{gastos_fabricacion}</td>
    <td class="left">&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td colspan="4" class="left_total">= Costo de Producci&oacute;n </td>
    <td>&nbsp;</td>
    <td colspan="2" class="left_total">&nbsp;</td>
    <td class="left_total">{costo_produccion}</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td colspan="4" class="left_total">&nbsp;</td>
    <td>&nbsp;</td>
    <td colspan="2" class="left_total">&nbsp;</td>
    <td class="left_total">&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td colspan="2" class="left_total">Utilidad Bruta </td>
    <td colspan="2">&nbsp;</td>
    <td>&nbsp;</td>
    <td colspan="2">&nbsp;</td>
    <td class="left_total">{utilidad_bruta}</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td colspan="2" class="left">&nbsp;</td>
    <td colspan="2" class="left">&nbsp;</td>
    <td>&nbsp;</td>
    <td colspan="2">&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td colspan="2" class="left">- Pan Comprado </td>
    <td colspan="2">&nbsp;</td>
    <td>&nbsp;</td>
    <td colspan="2" class="left">{pan_comprado}</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td colspan="2" class="left">- Gastos Generales </td>
    <td colspan="2">&nbsp;</td>
    <td class="left">&nbsp;</td>
    <td colspan="2" class="left">{gastos_generales}</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td colspan="2" class="left">- Gastos por Caja </td>
    <td colspan="2" class="left">&nbsp;</td>
    <td class="left">&nbsp;</td>
    <td colspan="2" class="left">{gastos_caja}</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td colspan="2" class="left">- Reservas</td>
    <td colspan="2">&nbsp;</td>
    <td>&nbsp;</td>
    <td colspan="2" class="left">{reserva_aguinaldos}</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td colspan="2" class="left">- Gastos Pagados x Otras Cias. </td>
    <td colspan="2">&nbsp;</td>
    <td>&nbsp;</td>
    <td colspan="2" class="left">{gastos_otras_cias}</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td colspan="2" class="left_total">= Total de Gastos </td>
    <td colspan="2">&nbsp;</td>
    <td>&nbsp;</td>
    <td colspan="2">&nbsp;</td>
    <td class="left_total">{total_gastos}</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td colspan="2" class="left_total">+ Ingrs. Extraordinarios </td>
    <td colspan="2">&nbsp;</td>
    <td>&nbsp;</td>
    <td colspan="2">&nbsp;</td>
    <td class="left_total">{ingresos_ext}</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td colspan="2" class="left">&nbsp;</td>
    <td colspan="2">&nbsp;</td>
    <td>&nbsp;</td>
    <td colspan="2">&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td colspan="2" class="left_total">Utilidad del Mes </td>
    <td colspan="2">&nbsp;</td>
    <td>&nbsp;</td>
    <td colspan="2">&nbsp;</td>
    <td colspan="2" class="left_total">{utilidad_mes}</td>
  </tr>
  <tr>
    <td colspan="2" class="left">&nbsp;</td>
    <td colspan="2">&nbsp;</td>
    <td>&nbsp;</td>
    <td colspan="2">&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td colspan="2" class="left">M. Prima / Vtas - Pan-comp</td>
    <td class="left">{mp_vtas}</td>
    <td class="left">F.D.: {con_pro_1} </td>
    <td colspan="2" class="left">Enc. que Inici&oacute;: </td>
    <td colspan="3" class="left">{inicio}</td>
  </tr>
  <tr>
    <td colspan="2" class="left">Utilidad / Producci&oacute;n </td>
    <td class="left">{utilidad_produccion}</td>
    <td class="left">F.N.: {con_pro_2} </td>
    <td colspan="2" class="left">Enc. que Termin&oacute;: </td>
    <td colspan="3" class="left">{termino}</td>
  </tr>
  <tr>
    <td colspan="2" class="left">Mat. Prima / Producci&oacute;n </td>
    <td class="left">{mp_produccion}</td>
    <td class="left">B.D.: {con_pro_3} </td>
    <td class="left">&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td colspan="2" class="left">Gas / Producci&oacute;n&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; {dif_mes_gas}</td>
    <td class="left">{gas_produccion}</td>
    <td class="left">REP.: {con_pro_4} </td>
    <td colspan="2" class="left_total">{titulo_reserva}</td>
    <td class="left_total">{titulo_importe}</td>
    <td class="left_total">{titulo_pagado}</td>
    <td class="left_total">{titulo_diferencia}</td>
  </tr>
  <tr>
    <td colspan="2" class="left">&nbsp;</td>
    <td colspan="2">&nbsp;</td>
    <td colspan="2" class="left">{nombre_reserva1}</td>
    <td class="left">{importe_reserva1}</td>
    <td class="left">{pagado1}</td>
    <td class="left">{diferencia1}</td>
  </tr>
  <tr>
    <td colspan="2" class="left_total">Producci&oacute;n Total </td>
    <td colspan="2" class="left_total">{produccion_total}</td>
    <td colspan="2" class="left">{nombre_reserva2}</td>
    <td class="left">{importe_reserva2}</td>
    <td class="left">{pagado2}</td>
    <td class="left">{diferencia2}</td>
  </tr>
  <tr>
    <td colspan="2" class="left_total">Ganancia</td>
    <td colspan="2" class="left_total">{ganancia}</td>
    <td colspan="2" class="left">{nombre_reserva3}</td>
    <td class="left">{importe_reserva3}</td>
    <td class="left">{pagado3}</td>
    <td class="left">{diferencia3}</td>
  </tr>
  <tr>
    <td colspan="2" class="left_total">% de Ganancia </td>
    <td colspan="2" class="left_total">{porc_ganancia}</td>
    <td colspan="2" class="left">{nombre_reserva4}</td>
    <td class="left">{importe_reserva4}</td>
    <td class="left">{pagado4}</td>
    <td class="left">{diferencia4}</td>
  </tr>
  <tr>
    <td colspan="2" class="left_total">Faltante de Pan </td>
    <td colspan="2" class="left_total">{faltante_pan}</td>
    <td colspan="2" class="left">{nombre_reserva5}</td>
    <td class="left">{importe_reserva5}</td>
    <td class="left">{pagado5}</td>
    <td class="left">{diferencia5}</td>
  </tr>
  <tr>
    <td colspan="2" class="left_total">Devoluciones</td>
    <td colspan="2" class="left_total">{devoluciones}</td>
    <td colspan="2" class="left">{nombre_reserva6}</td>
    <td class="left">{importe_reserva6}</td>
    <td class="left">{pagado6}</td>
    <td class="left">{diferencia6}</td>
  </tr>
  <tr>
    <td colspan="2" class="left_total">Rezago Inicial </td>
    <td colspan="2" class="left_total">{rezago_inicial}</td>
    <td colspan="2" class="left">{nombre_reserva7}</td>
    <td class="left">{importe_reserva7}</td>
    <td class="left">{pagado7}</td>
    <td class="left">{diferencia7}</td>
  </tr>
  <tr>
    <td colspan="2" class="left_total">Rezago Final </td>
    <td colspan="2" class="left_total">{rezago_final}</td>
    <td colspan="2" class="left">{nombre_reserva8}</td>
    <td class="left">{importe_reserva8}</td>
    <td class="left">{pagado8}</td>
    <td class="left">{diferencia8}</td>
  </tr>
  <tr>
    <td colspan="2" class="left_total">{estado} el Rezago </td>
    <td colspan="2" class="left_total">{subio_rezago}</td>
    <td colspan="2" class="left">{nombre_reserva9}</td>
    <td class="left">{importe_reserva9}</td>
    <td class="left">{pagado9}</td>
    <td class="left">{diferencia9}</td>
  </tr>
  <tr>
    <td colspan="2" class="left_total">Efectivo</td>
    <td colspan="2" class="left_total">{efectivo}</td>
    <td colspan="2" class="left">{nombre_reserva10}</td>
    <td class="left">{importe_reserva10}</td>
    <td class="left">{pagado10}</td>
    <td class="left">{diferencia10}</td>
  </tr>
  <tr>
    <td colspan="2" class="left_total">&nbsp;</td>
    <td colspan="2" class="left_total">&nbsp;</td>
    <td>&nbsp;</td>
    <td colspan="2">&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td colspan="4" class="left_total">Utilidad A&ntilde;o Anterior</td>
    <td>&nbsp;</td>
    <td colspan="2">&nbsp;</td>
    <td class="left_total">{utilidad_anio_ant}</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td colspan="2" class="left">&nbsp;</td>
    <td colspan="2">&nbsp;</td>
    <td>&nbsp;</td>
    <td colspan="2">&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td colspan="4" class="left_total">Datos A&ntilde;o Anterior </td>
    <td>&nbsp;</td>
    <td colspan="2">&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td class="left">{tant_1}</td>
    <td class="left">{ant_1}</td>
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
    <td class="left">{ant_2}</td>
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
    <td class="left">{ant_3}</td>
    <td class="left">{tant_6}</td>
    <td class="left">{ant_6}</td>
    <td>&nbsp;</td>
    <td class="left">{tant_9}</td>
    <td class="left">{ant_9}</td>
    <td class="left">{tant_12}</td>
    <td class="left">{ant_12}</td>
  </tr>
  <tr>
    <td colspan="2">&nbsp;</td>
    <td colspan="2">&nbsp;</td>
    <td>&nbsp;</td>
    <td colspan="2">&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td colspan="4" class="left_total">Datos A&ntilde;o Actual </td>
    <td>&nbsp;</td>
    <td colspan="2">&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td class="left">{tact_1}</td>
    <td class="left">{act_1}</td>
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
    <td class="left">{act_2}</td>
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
    <td class="left">{act_3}</td>
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
    <td class="left">&nbsp;</td>
    <td class="left">&nbsp;</td>
    <td class="left">&nbsp;</td>
    <td>&nbsp;</td>
    <td class="left">&nbsp;</td>
    <td class="left">&nbsp;</td>
    <td>&nbsp;</td>
    <td class="left">&nbsp;</td>
  </tr>
  <tr>
    <td class="left">&nbsp;</td>
    <td colspan="4" class="center">Datos A&ntilde;o Actual </td>
    <td colspan="4" class="center">Datos A&ntilde;o Anterior </td>
  </tr>
  <tr>
    <td class="left">&nbsp;</td>
    <td class="left_total">Vta. Pta. </td>
    <td class="left_total">Abono R. </td>
    <td class="left_total">Clientes</td>
    <td class="left_total">Prom</td>
    <td class="left_total">Vta. Pta. </td>
    <td class="left_total">Abono R. </td>
    <td class="left_total">Clientes</td>
    <td class="left_total">Prom</td>
  </tr>
  <tr>
    <td class="left">Ene</td>
    <td class="left">{vta_1}</td>
    <td class="left">{abono_1}</td>
    <td class="left">{clientes_1}</td>
    <td class="left">{prom_1}</td>
    <td class="left">{vta_ant_1}</td>
    <td class="left">{abono_ant_1}</td>
    <td class="left">{clientes_ant_1}</td>
    <td class="left">{prom_ant_1}</td>
  </tr>
  <tr>
    <td class="left">Feb</td>
    <td class="left">{vta_2}</td>
    <td class="left">{abono_2}</td>
    <td class="left">{clientes_2}</td>
    <td class="left">{prom_2}</td>
    <td class="left">{vta_ant_2}</td>
    <td class="left">{abono_ant_2}</td>
    <td class="left">{clientes_ant_2}</td>
    <td class="left">{prom_ant_2}</td>
  </tr>
  <tr>
    <td class="left">Marzo</td>
    <td class="left">{vta_3}</td>
    <td class="left">{abono_3}</td>
    <td class="left">{clientes_3}</td>
    <td class="left">{prom_3}</td>
    <td class="left">{vta_ant_3}</td>
    <td class="left">{abono_ant_3}</td>
    <td class="left">{clientes_ant_3}</td>
    <td class="left">{prom_ant_3}</td>
  </tr>
  <tr>
    <td class="left">Abr</td>
    <td class="left">{vta_4}</td>
    <td class="left">{abono_4}</td>
    <td class="left">{clientes_4}</td>
    <td class="left">{prom_4}</td>
    <td class="left">{vta_ant_4}</td>
    <td class="left">{abono_ant_4}</td>
    <td class="left">{clientes_ant_4}</td>
    <td class="left">{prom_ant_4}</td>
  </tr>
  <tr>
    <td class="left">May</td>
    <td class="left">{vta_5}</td>
    <td class="left">{abono_5}</td>
    <td class="left">{clientes_5}</td>
    <td class="left">{prom_5}</td>
    <td class="left">{vta_ant_5}</td>
    <td class="left">{abono_ant_5}</td>
    <td class="left">{clientes_ant_5}</td>
    <td class="left">{prom_ant_5}</td>
  </tr>
  <tr>
    <td class="left">Jun</td>
    <td class="left">{vta_6}</td>
    <td class="left">{abono_6}</td>
    <td class="left">{clientes_6}</td>
    <td class="left">{prom_6}</td>
    <td class="left">{vta_ant_6}</td>
    <td class="left">{abono_ant_6}</td>
    <td class="left">{clientes_ant_6}</td>
    <td class="left">{prom_ant_6}</td>
  </tr>
  <tr>
    <td class="left">Jul</td>
    <td class="left">{vta_7}</td>
    <td class="left">{abono_7}</td>
    <td class="left">{clientes_7}</td>
    <td class="left">{prom_7}</td>
    <td class="left">{vta_ant_7}</td>
    <td class="left">{abono_ant_7}</td>
    <td class="left">{clientes_ant_7}</td>
    <td class="left">{prom_ant_7}</td>
  </tr>
  <tr>
    <td class="left">Ago</td>
    <td class="left">{vta_8}</td>
    <td class="left">{abono_8}</td>
    <td class="left">{clientes_8}</td>
    <td class="left">{prom_8}</td>
    <td class="left">{vta_ant_8}</td>
    <td class="left">{abono_ant_8}</td>
    <td class="left">{clientes_ant_8}</td>
    <td class="left">{prom_ant_8}</td>
  </tr>
  <tr>
    <td class="left">Sep</td>
    <td class="left">{vta_9}</td>
    <td class="left">{abono_9}</td>
    <td class="left">{clientes_9}</td>
    <td class="left">{prom_9}</td>
    <td class="left">{vta_ant_9}</td>
    <td class="left">{abono_ant_9}</td>
    <td class="left">{clientes_ant_9}</td>
    <td class="left">{prom_ant_9}</td>
  </tr>
  <tr>
    <td class="left">Oct</td>
    <td class="left">{vta_10}</td>
    <td class="left">{abono_10}</td>
    <td class="left">{clientes_10}</td>
    <td class="left">{prom_10}</td>
    <td class="left">{vta_ant_10}</td>
    <td class="left">{abono_ant_10}</td>
    <td class="left">{clientes_ant_10}</td>
    <td class="left">{prom_ant_10}</td>
  </tr>
  <tr>
    <td class="left">Nov</td>
    <td class="left">{vta_11}</td>
    <td class="left">{abono_11}</td>
    <td class="left">{clientes_11}</td>
    <td class="left">{prom_11}</td>
    <td class="left">{vta_ant_11}</td>
    <td class="left">{abono_ant_11}</td>
    <td class="left">{clientes_ant_11}</td>
    <td class="left">{prom_ant_11}</td>
  </tr>
  <tr>
    <td class="left">Dic</td>
    <td class="left">{vta_12}</td>
    <td class="left">{abono_12}</td>
    <td class="left">{clientes_12}</td>
    <td class="left">{prom_12}</td>
    <td class="left">{vta_ant_12}</td>
    <td class="left">{abono_ant_12}</td>
    <td class="left">{clientes_ant_12}</td>
    <td class="left">{prom_ant_12}</td>
  </tr>
  <tr>
    <td class="left_total">Tot</td>
    <td class="left_total">{tot_vta}</td>
    <td class="left_total">{tot_abono}</td>
    <td class="left_total">{tot_clientes}</td>
    <td class="left_total">&nbsp;</td>
    <td class="left_total">{tot_vta_ant}</td>
    <td class="left_total">{tot_abono_ant}</td>
    <td class="left_total">{tot_clientes_ant}</td>
    <td class="left_total">&nbsp;</td>
  </tr>
</table>
<br style="page-break-after:always;">
<!-- START BLOCK : gastos_extras -->
<br>
<br>
<br>
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="top">
  <table width="100%">
    <tr>
      <td class="print_encabezado">Cia.: {num_cia} </td>
      <td class="print_encabezado" align="center">{nombre_cia}</td>
      <td class="print_encabezado" align="right">Cia.: {num_cia} </td>
    </tr>
    <tr>
      <td width="20%">&nbsp;</td>
      <td width="60%" class="print_encabezado" align="center">Relaci&oacute;n de Gastos Extras <br>
      al d&iacute;a {dia} de {mes} de {anio} </td>
      <td width="20%">&nbsp;</td>
    </tr>
  </table>
 <br>
 <table width="70%" cellpadding="0" cellspacing="0" class="print">
   <!-- START BLOCK : tipo_gasto -->
   <tr>
     <td colspan="5" class="titulo" scope="col">{tipo_gasto}</td>
     </tr>
   <tr>
     <th colspan="2" class="print" scope="col">C&oacute;digo y Concepto </th>
     <th class="print" scope="col">Importe</th>
     <th class="print" scope="col">Mes Anterior </th>
     <th class="print" scope="col">% Variaci&oacute;n </th>
     </tr>
   <!-- START BLOCK : fila_gasto -->
   <tr>
     <td class="print"><strong>{codgastos}</strong></td>
     <td class="vprint"><strong>{concepto}</strong></td>
     <td class="rprint"><strong>{importe}</strong></td>
     <td class="rprint"><strong><font color="#0000FF">{mes_ant}</font></strong></td>
     <td class="rprint"><strong>{variacion}</strong></td>
     </tr>
	<!-- END BLOCK : fila_gasto -->
   <tr>
     <th colspan="2" class="print">Sub Total </th>
     <th class="rprint_total">{importe}</th>
     <th class="rprint_total">{mes_ant}</th>
     <th class="rprint_total">{variacion}</th>
     </tr>
   <tr>
     <td colspan="2">&nbsp;</td>
     <td>&nbsp;</td>
     <td>&nbsp;</td>
     <td>&nbsp;</td>
   </tr>
	 <!-- END BLOCK : tipo_gasto -->
   <tr>
     <th colspan="2" class="print">Total</th>
     <th class="rprint_total">{total_importe}</th>
     <th class="rprint_total">{total_mes_ant}</th>
     <th class="rprint_total">{prom_variacion}</th>
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
   <!-- START BLOCK : fila_caja -->
   <tr>
     <td colspan="2" class="vprint">{concepto}</td>
     <td class="rprint">{importe}</td>
     <td>&nbsp;</td>
     <td class="rprint_total">&nbsp;</td>
   </tr>
   <!-- END BLOCK : fila_caja -->
   <tr>
     <th colspan="2" class="print">Total Gastos por Caja </th>
     <th class="rprint_total">{importe}</th>
     <td>&nbsp;</td>
     <td class="rprint_total">&nbsp;</td>
   </tr>
   <!-- END BLOCK : gastos_caja -->
 </table>
 <br>
 </td>
</tr>
</table>
<br style="page-break-after:always;">
<!-- END BLOCK : gastos_extras -->

<!-- Estado de resultados "comparativo" -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="top">
<table width="100%">
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
  <table width="70%" cellpadding="0" cellspacing="0" class="print">
    <tr>
      <th class="print" scope="col">&nbsp;</th>
      <th class="print" scope="col">{mes_ant}</th>
      <th class="print" scope="col">{mes_act}</th>
      <th class="print" scope="col">Diferencia</th>
    </tr>
    <tr>
      <td class="vprint">Venta en Puerta </td>
      <td class="rprint">{vta_pta_ant}</td>
      <td class="rprint">{venta_puerta}</td>
      <td class="rprint">{venta_puerta_dif}</td>
    </tr>
    <tr>
      <td class="vprint">Bases</td>
      <td class="rprint">{bases_ant}</td>
      <td class="rprint">{bases}</td>
      <td class="rprint">{bases_dif}</td>
    </tr>
    <tr>
      <td class="vprint">Barredura</td>
      <td class="rprint">{barredura_ant}</td>
      <td class="rprint">{barredura}</td>
      <td class="rprint">{barredura_dif}</td>
    </tr>
    <tr>
      <td class="vprint">Pastillaje</td>
      <td class="rprint">{pastillaje_ant}</td>
      <td class="rprint">{pastillaje}</td>
      <td class="rprint">{pastillaje_dif}</td>
    </tr>
    <tr>
      <td class="vprint">Abono Empleados </td>
      <td class="rprint">{abono_emp_ant}</td>
      <td class="rprint">{abono_emp}</td>
      <td class="rprint">{abono_emp_dif}</td>
    </tr>
    <tr>
      <td class="vprint">Otros</td>
      <td class="rprint">{otros_ant}</td>
      <td class="rprint">{otros}</td>
      <td class="rprint">{otros_dif}</td>
    </tr>
    <tr>
      <td class="vprint">Total Otros </td>
      <td class="rprint">{total_otros_ant}</td>
      <td class="rprint">{total_otros}</td>
      <td class="rprint">{total_otros_dif}</td>
    </tr>
    <tr>
      <td class="vprint">Abono Reparto</td>
      <td class="rprint">{abono_reparto_ant}</td>
      <td class="rprint">{abono_reparto}</td>
      <td class="rprint">{abono_reparto_dif}</td>
    </tr>
    <tr>
      <td class="vprint">Menos Errores </td>
      <td class="rprint">{errores_ant}</td>
      <td class="rprint">{errores}</td>
      <td class="rprint">{errores_dif}</td>
    </tr>
    <tr>
      <td class="vprint"><strong><font size="3">Ventas Netas</font> </strong></td>
      <td class="rprint"><strong><font size="3">{ventas_netas_ant}</font></strong></td>
      <td class="rprint"><strong><font size="3">{ventas_netas}</font></strong></td>
      <td class="rprint"><strong><font size="3">{ventas_netas_dif}</font></strong></td>
    </tr>
    <tr>
      <td class="vprint">&nbsp;</td>
      <td class="rprint">&nbsp;</td>
      <td class="rprint">&nbsp;</td>
      <td class="rprint">&nbsp;</td>
    </tr>
    <tr>
      <td class="vprint">Inventario Anterior</td>
      <td class="rprint">{inventario_anterior_ant}</td>
      <td class="rprint">{inventario_anterior}</td>
      <td class="rprint">{inventario_anterior_dif}</td>
    </tr>
    <tr>
      <td class="vprint">+ Compras </td>
      <td class="rprint">{compras_ant}</td>
      <td class="rprint">{compras}</td>
      <td class="rprint">{compras_dif}</td>
    </tr>
    <tr>
      <td class="vprint">+ Mercancias </td>
      <td class="rprint">{mercancias_ant}</td>
      <td class="rprint">{mercancias}</td>
      <td class="rprint">{mercancias_dif}</td>
    </tr>
    <tr>
      <td class="vprint">- Inventario Actual </td>
      <td class="rprint">{inventario_actual_ant}</td>
      <td class="rprint">{inventario_actual}</td>
      <td class="rprint">{inventario_actual_dif}</td>
    </tr>
    <tr>
      <td class="vprint">= Mat. Prima Utilizada </td>
      <td class="rprint">{mat_prima_utilizada_ant}</td>
      <td class="rprint">{mat_prima_utilizada}</td>
      <td class="rprint">{mat_prima_utilizada_dif}</td>
    </tr>
    <tr>
      <td class="vprint">+ Mano de Obra </td>
      <td class="rprint">{mano_obra_ant}</td>
      <td class="rprint">{mano_obra}</td>
      <td class="rprint">{mano_obra_dif}</td>
    </tr>
    <tr>
      <td class="vprint">+ Panaderos</td>
      <td class="rprint">{panaderos_ant}</td>
      <td class="rprint">{panaderos}</td>
      <td class="rprint">{panaderos_dif}</td>
    </tr>
    <tr>
      <td class="vprint">+ Gastos de Fabricaci&oacute;n </td>
      <td class="rprint">{gastos_fabricacion_ant}</td>
      <td class="rprint">{gastos_fabricacion}</td>
      <td class="rprint">{gastos_fabricacion_dif}</td>
    </tr>
    <tr>
      <td class="vprint"><strong><font size="3">= Costo de Producci&oacute;n</font></strong></td>
      <td class="rprint"><strong><font size="3">{costo_produccion_ant}</font></strong></td>
      <td class="rprint"><strong><font size="3">{costo_produccion}</font></strong></td>
      <td class="rprint"><strong><font size="3">{costo_produccion_dif}</font></strong></td>
    </tr>
    <tr>
      <td class="vprint">&nbsp;</td>
      <td class="rprint">&nbsp;</td>
      <td class="rprint">&nbsp;</td>
      <td class="rprint">&nbsp;</td>
    </tr>
    <tr>
      <td class="vprint"><strong><font size="3">Utilidad Bruta</font> </strong></td>
      <td class="rprint"><strong><font size="3">{utilidad_bruta_ant}</font></strong></td>
      <td class="rprint"><strong><font size="3">{utilidad_bruta}</font></strong></td>
      <td class="rprint"><strong><font size="3">{utilidad_bruta_dif}</font></strong></td>
    </tr>
    <tr>
      <td class="vprint">&nbsp;</td>
      <td class="rprint">&nbsp;</td>
      <td class="rprint">&nbsp;</td>
      <td class="rprint">&nbsp;</td>
    </tr>
    <tr>
      <td class="vprint">- Pan Comprado</td>
      <td class="rprint">{pan_comprado_ant}</td>
      <td class="rprint">{pan_comprado}</td>
      <td class="rprint">{pan_comprado_dif}</td>
    </tr>
    <tr>
      <td class="vprint">- Gastos Generales </td>
      <td class="rprint">{gastos_generales_ant}</td>
      <td class="rprint">{gastos_generales}</td>
      <td class="rprint">{gastos_generales_dif}</td>
    </tr>
    <tr>
      <td class="vprint">- Gastos por Caja </td>
      <td class="rprint">{gastos_caja_ant}</td>
      <td class="rprint">{gastos_caja}</td>
      <td class="rprint">{gastos_caja_dif}</td>
    </tr>
    <tr>
      <td class="vprint">- Reserva para Aguinaldos</td>
      <td class="rprint">{reserva_aguinaldos_ant}</td>
      <td class="rprint">{reserva_aguinaldos}</td>
      <td class="rprint">{reserva_aguinaldos_dif}</td>
    </tr>
    <tr>
      <td class="vprint">- Gastos Pagados por Otras </td>
      <td class="rprint">{gastos_otras_cias_ant}</td>
      <td class="rprint">{gastos_otras_cias}</td>
      <td class="rprint">{gastos_otras_cias_dif}</td>
    </tr>
    <tr>
      <td class="vprint"><strong><font size="3">= Total de Gastos</font> </strong></td>
      <td class="rprint"><strong><font size="3">{total_gastos_ant}</font></strong></td>
      <td class="rprint"><strong><font size="3">{total_gastos}</font></strong></td>
      <td class="rprint"><strong><font size="3">{total_gastos_dif}</font></strong></td>
    </tr>
    <tr>
      <td class="vprint">&nbsp;</td>
      <td class="rprint">&nbsp;</td>
      <td class="rprint">&nbsp;</td>
      <td class="rprint">&nbsp;</td>
    </tr>
    <tr>
      <td class="vprint"><strong><font size="3">+ Ingresos Extraordinarios</font> </strong></td>
      <td class="rprint"><strong><font size="3">{ingresos_ext_ant}</font> </strong></td>
      <td class="rprint"><strong><font size="3">{ingresos_ext}</font> </strong></td>
      <td class="rprint"><strong><font size="3">{ingresos_ext_dif}</font></strong></td>
    </tr>
    <tr>
      <td class="vprint">&nbsp;</td>
      <td class="rprint">&nbsp;</td>
      <td class="rprint">&nbsp;</td>
      <td class="rprint">&nbsp;</td>
    </tr>
    <tr>
      <td class="vprint"><strong><font size="3">Utilidad del Mes </font> </strong></td>
      <td class="rprint"><strong><font size="3">{utilidad_mes_ant}</font> </strong></td>
      <td class="rprint"><strong><font size="3">{utilidad_mes}</font> </strong></td>
      <td class="rprint"><strong><font size="3">{utilidad_mes_dif}</font></strong></td>
    </tr>
    <tr>
      <td class="vprint">&nbsp;</td>
      <td class="rprint">&nbsp;</td>
      <td class="rprint">&nbsp;</td>
      <td class="rprint">&nbsp;</td>
    </tr>
    <tr>
      <td class="vprint">M. Prima / Vtas - Pan comprado </td>
      <td class="rprint">{mp_vtas_ant}</td>
      <td class="rprint">{mp_vtas}</td>
      <td class="rprint">{mp_vtas_dif}</td>
    </tr>
    <tr>
      <td class="vprint">Utilidad entre Producci&oacute;n </td>
      <td class="rprint">{utilidad_produccion_ant}</td>
      <td class="rprint">{utilidad_produccion}</td>
      <td class="rprint">{utilidad_produccion_dif}</td>
    </tr>
    <tr>
      <td class="vprint">Mat. Prima entre Producci&oacute;n</td>
      <td class="rprint">{mp_produccion_ant}</td>
      <td class="rprint">{mp_produccion}</td>
      <td class="rprint">{mp_produccion_dif}</td>
    </tr>
    <tr>
      <td class="vprint">&nbsp;</td>
      <td class="rprint">&nbsp;</td>
      <td class="rprint">&nbsp;</td>
      <td class="rprint">&nbsp;</td>
    </tr>
    <tr>
      <td class="vprint"><strong><font size="3">Producci&oacute;n Total</font></strong> </td>
      <td class="rprint"><strong><font size="3">{produccion_total_ant}</font></strong></td>
      <td class="rprint"><strong><font size="3">{produccion_total}</font></strong></td>
      <td class="rprint"><strong><font size="3">{produccion_total_dif}</font></strong></td>
    </tr>
    <tr>
      <td class="vprint"><strong><font size="3">Faltante de Pan</font></strong> </td>
      <td class="rprint"><strong><font size="3">{faltante_pan_ant}</font></strong></td>
      <td class="rprint"><strong><font size="3">{faltante_pan}</font></strong></td>
      <td class="rprint"><strong><font size="3">{faltante_pan_dif}</font></strong></td>
    </tr>
    <tr>
      <td class="vprint"><strong><font size="3">Rezago Inicial</font></strong> </td>
      <td class="rprint"><strong><font size="3">{rezago_inicial_ant}</font></strong></td>
      <td class="rprint"><strong><font size="3">{rezago_inicial}</font></strong></td>
      <td class="rprint"><strong><font size="3">{rezago_inicial_dif}</font></strong></td>
    </tr>
    <tr>
      <td class="vprint"><strong><font size="3">Rezago Final </font></strong></td>
      <td class="rprint"><strong><font size="3">{rezago_final_ant}</font></strong></td>
      <td class="rprint"><strong><font size="3">{rezago_final}</font></strong></td>
      <td class="rprint"><strong><font size="3">{rezago_final_dif}</font></strong></td>
    </tr>
    <tr>
      <td class="vprint"><strong><font size="3">Variaci&oacute;n del Rezago</font></strong> </td>
      <td class="rprint"><strong><font size="3">{var_rezago_ant}</font></strong></td>
      <td class="rprint"><strong><font size="3">{subio_rezago}</font></strong></td>
      <td class="rprint"><strong><font size="3">{subio_rezago_dif}</font></strong></td>
    </tr>
    <tr>
      <td class="vprint"><strong><font size="3">Efectivo</font></strong></td>
      <td class="rprint"><strong><font size="3">{efectivo_ant}</font></strong></td>
      <td class="rprint"><strong><font size="3"> {efectivo}</font></strong> </td>
      <td class="rprint"><strong><font size="3">{efectivo_dif}</font></strong></td>
    </tr>
  </table>
</td>
</tr>
</table>
<br style="page-break-after:always;">
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="top">

<!-- START BLOCK : listado_gastos -->
<br>
<br>
<br>
<table width="100%">
  <tr>
    <td class="print_encabezado">Cia.: {num_cia} </td>
    <td class="print_encabezado" align="center">{nombre_cia}</td>
    <td class="print_encabezado" align="right">Cia.: {num_cia} </td>
  </tr>
  <tr>
    <td width="20%">&nbsp;</td>
    <td width="60%" class="print_encabezado" align="center">Listado de Gastos Pagados en Oficinas <br>
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

</td>
</tr>
</table>
<br style="page-break-after:always;">
<!-- END BLOCK : listado_gastos -->
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