<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Prueba de Pan</title>
<link href="/lecaroz/styles/screen.css" rel="stylesheet" type="text/css" media="screen" />
<link href="/lecaroz/styles/print.css" rel="stylesheet" type="text/css" media="print" />
<link href="/lecaroz/styles/font-style.css" rel="stylesheet" type="text/css" />

<script type="text/javascript" src="jscripts/mootools/mootools-1.2-core.js"></script>
<script type="text/javascript" src="jscripts/mootools/mootools-1.2-more.js"></script>
<script type="text/javascript" src="jscripts/bal/ComparativoBalancesPanaderiasReporte.js"></script>

<body>
<!-- START BLOCK : reporte1 -->
<div class="Reporte1">
	<div class="bold" align="center" style="margin-bottom:5px;">Oficinas Administrativas Mollendo</div>
	<div align="center" class="bold" style="margin-bottom:20px;">Comparativo entre Compañías<br />
	del mes de {mes}
	de {anio}</div>
	<div class="Datos">
		<table width="98%" class="print">
			<tr>
				<th width="16%" class="print" scope="col">&nbsp;</th>
				<th width="14%" class="print" scope="col">{nombre_cia_0}</th>
				<th width="14%" class="print" scope="col">{nombre_cia_1}</th>
				<th width="14%" class="print" scope="col">{nombre_cia_2}</th>
				<th width="14%" class="print" scope="col">{nombre_cia_3}</th>
				<th width="14%" class="print" scope="col">{nombre_cia_4}</th>
				<th width="14%" class="print" scope="col">{nombre_cia_5}</th>
			</tr>
			<tr>
				<td class="print">+ Venta en puerta</td>
				<td align="right" class="print blue">{venta_puerta_0}</td>
				<td align="right" class="print blue">{venta_puerta_1}</td>
				<td align="right" class="print blue">{venta_puerta_2}</td>
				<td align="right" class="print blue">{venta_puerta_3}</td>
				<td align="right" class="print blue">{venta_puerta_4}</td>
				<td align="right" class="print blue">{venta_puerta_5}</td>
			</tr>
			<tr>
				<td class="print">+ Pastel vitrina</td>
				<td align="right" class="print blue">{pastel_vitrina_0}</td>
				<td align="right" class="print blue">{pastel_vitrina_1}</td>
				<td align="right" class="print blue">{pastel_vitrina_2}</td>
				<td align="right" class="print blue">{pastel_vitrina_3}</td>
				<td align="right" class="print blue">{pastel_vitrina_4}</td>
				<td align="right" class="print blue">{pastel_vitrina_5}</td>
			</tr>
			<tr>
				<td class="print">+ Pastel pedido</td>
				<td align="right" class="print blue">{pastel_pedido_0}</td>
				<td align="right" class="print blue">{pastel_pedido_1}</td>
				<td align="right" class="print blue">{pastel_pedido_2}</td>
				<td align="right" class="print blue">{pastel_pedido_3}</td>
				<td align="right" class="print blue">{pastel_pedido_4}</td>
				<td align="right" class="print blue">{pastel_pedido_5}</td>
			</tr>
			<tr>
				<td class="print">+ Pan pedido</td>
				<td align="right" class="print blue">{pan_pedido_0}</td>
				<td align="right" class="print blue">{pan_pedido_1}</td>
				<td align="right" class="print blue">{pan_pedido_2}</td>
				<td align="right" class="print blue">{pan_pedido_3}</td>
				<td align="right" class="print blue">{pan_pedido_4}</td>
				<td align="right" class="print blue">{pan_pedido_5}</td>
			</tr>
			<tr>
				<td class="print">= Venta en puerta total</td>
				<td align="right" class="print blue bold">{venta_puerta_total_0}{por_venta_puerta_0}</td>
				<td align="right" class="print blue bold">{venta_puerta_total_1}{por_venta_puerta_1}</td>
				<td align="right" class="print blue bold">{venta_puerta_total_2}{por_venta_puerta_2}</td>
				<td align="right" class="print blue bold">{venta_puerta_total_3}{por_venta_puerta_3}</td>
				<td align="right" class="print blue bold">{venta_puerta_total_4}{por_venta_puerta_4}</td>
				<td align="right" class="print blue bold">{venta_puerta_total_5}{por_venta_puerta_5}</td>
			</tr>
			<tr>
				<td class="print">+ Bases</td>
				<td align="right" class="print blue">{bases_0}</td>
				<td align="right" class="print blue">{bases_1}</td>
				<td align="right" class="print blue">{bases_2}</td>
				<td align="right" class="print blue">{bases_3}</td>
				<td align="right" class="print blue">{bases_4}</td>
				<td align="right" class="print blue">{bases_5}</td>
			</tr>
			<tr>
				<td class="print">+ Barredura</td>
				<td align="right" class="print blue">{barredura_0}</td>
				<td align="right" class="print blue">{barredura_1}</td>
				<td align="right" class="print blue">{barredura_2}</td>
				<td align="right" class="print blue">{barredura_3}</td>
				<td align="right" class="print blue">{barredura_4}</td>
				<td align="right" class="print blue">{barredura_5}</td>
			</tr>
			<tr>
				<td class="print">+ Pastillaje</td>
				<td align="right" class="print blue">{pastillaje_0}</td>
				<td align="right" class="print blue">{pastillaje_1}</td>
				<td align="right" class="print blue">{pastillaje_2}</td>
				<td align="right" class="print blue">{pastillaje_3}</td>
				<td align="right" class="print blue">{pastillaje_4}</td>
				<td align="right" class="print blue">{pastillaje_5}</td>
			</tr>
			<tr>
				<td class="print">+ Abono Empleados</td>
				<td align="right" class="print blue">{abono_emp_0}</td>
				<td align="right" class="print blue">{abono_emp_1}</td>
				<td align="right" class="print blue">{abono_emp_2}</td>
				<td align="right" class="print blue">{abono_emp_3}</td>
				<td align="right" class="print blue">{abono_emp_4}</td>
				<td align="right" class="print blue">{abono_emp_5}</td>
			</tr>
			<tr>
				<td class="print">+ Otros</td>
				<td align="right" class="print blue">{otros_0}</td>
				<td align="right" class="print blue">{otros_1}</td>
				<td align="right" class="print blue">{otros_2}</td>
				<td align="right" class="print blue">{otros_3}</td>
				<td align="right" class="print blue">{otros_4}</td>
				<td align="right" class="print blue">{otros_5}</td>
			</tr>
			<tr>
				<td class="print bold">= Total otros</td>
				<td align="right" class="print blue bold">{total_otros_0}</td>
				<td align="right" class="print blue bold">{total_otros_1}</td>
				<td align="right" class="print blue bold">{total_otros_2}</td>
				<td align="right" class="print blue bold">{total_otros_3}</td>
				<td align="right" class="print blue bold">{total_otros_4}</td>
				<td align="right" class="print blue bold">{total_otros_5}</td>
			</tr>
			<tr>
				<td class="print">+ Abono reparto</td>
				<td align="right" class="print blue">{abono_reparto_0}{por_abono_reparto_0}</td>
				<td align="right" class="print blue">{abono_reparto_1}{por_abono_reparto_1}</td>
				<td align="right" class="print blue">{abono_reparto_2}{por_abono_reparto_2}</td>
				<td align="right" class="print blue">{abono_reparto_3}{por_abono_reparto_3}</td>
				<td align="right" class="print blue">{abono_reparto_4}{por_abono_reparto_4}</td>
				<td align="right" class="print blue">{abono_reparto_5}{por_abono_reparto_5}</td>
			</tr>
			<tr>
				<td class="print"- >Errores</td>
				<td align="right" class="print red">{errores_0}</td>
				<td align="right" class="print red">{errores_1}</td>
				<td align="right" class="print red">{errores_2}</td>
				<td align="right" class="print red">{errores_3}</td>
				<td align="right" class="print red">{errores_4}</td>
				<td align="right" class="print red">{errores_5}</td>
			</tr>
			<tr>
				<td class="print font10 bold">= Ventas netas</td>
				<td align="right" class="print font10 blue bold">{ventas_netas_0}</td>
				<td align="right" class="print font10 blue bold">{ventas_netas_1}</td>
				<td align="right" class="print font10 blue bold">{ventas_netas_2}</td>
				<td align="right" class="print font10 blue bold">{ventas_netas_3}</td>
				<td align="right" class="print font10 blue bold">{ventas_netas_4}</td>
				<td align="right" class="print font10 blue bold">{ventas_netas_5}</td>
			</tr>
			<tr>
				<td colspan="7" class="print">&nbsp;</td>
			</tr>
			<tr>
				<td class="print">+ Inventario inicial</td>
				<td align="right" class="print blue">{inv_ant_0}</td>
				<td align="right" class="print blue">{inv_ant_1}</td>
				<td align="right" class="print blue">{inv_ant_2}</td>
				<td align="right" class="print blue">{inv_ant_3}</td>
				<td align="right" class="print blue">{inv_ant_4}</td>
				<td align="right" class="print blue">{inv_ant_5}</td>
			</tr>
			<tr>
				<td class="print">+ Compras</td>
				<td align="right" class="print blue">{compras_0}</td>
				<td align="right" class="print blue">{compras_1}</td>
				<td align="right" class="print blue">{compras_2}</td>
				<td align="right" class="print blue">{compras_3}</td>
				<td align="right" class="print blue">{compras_4}</td>
				<td align="right" class="print blue">{compras_5}</td>
			</tr>
			<tr>
				<td class="print">+ Mercancias</td>
				<td align="right" class="print blue">{mercancias_0}</td>
				<td align="right" class="print blue">{mercancias_1}</td>
				<td align="right" class="print blue">{mercancias_2}</td>
				<td align="right" class="print blue">{mercancias_3}</td>
				<td align="right" class="print blue">{mercancias_4}</td>
				<td align="right" class="print blue">{mercancias_5}</td>
			</tr>
			<tr>
				<td class="print">- Inventario final</td>
				<td align="right" class="print blue">{inv_act_0}</td>
				<td align="right" class="print blue">{inv_act_1}</td>
				<td align="right" class="print blue">{inv_act_2}</td>
				<td align="right" class="print blue">{inv_act_3}</td>
				<td align="right" class="print blue">{inv_act_4}</td>
				<td align="right" class="print blue">{inv_act_5}</td>
			</tr>
			<tr>
				<td class="print bold">= Mat. prima utilizada</td>
				<td align="right" class="print blue bold">{mat_prima_utilizada_0}</td>
				<td align="right" class="print blue bold">{mat_prima_utilizada_1}</td>
				<td align="right" class="print blue bold">{mat_prima_utilizada_2}</td>
				<td align="right" class="print blue bold">{mat_prima_utilizada_3}</td>
				<td align="right" class="print blue bold">{mat_prima_utilizada_4}</td>
				<td align="right" class="print blue bold">{mat_prima_utilizada_5}</td>
			</tr>
			<tr>
				<td class="print">+ Mano de obra</td>
				<td align="right" class="print blue">{mano_obra_0}</td>
				<td align="right" class="print blue">{mano_obra_1}</td>
				<td align="right" class="print blue">{mano_obra_2}</td>
				<td align="right" class="print blue">{mano_obra_3}</td>
				<td align="right" class="print blue">{mano_obra_4}</td>
				<td align="right" class="print blue">{mano_obra_5}</td>
			</tr>
			<tr>
				<td class="print">+ Panaderos</td>
				<td align="right" class="print blue">{panaderos_0}</td>
				<td align="right" class="print blue">{panaderos_1}</td>
				<td align="right" class="print blue">{panaderos_2}</td>
				<td align="right" class="print blue">{panaderos_3}</td>
				<td align="right" class="print blue">{panaderos_4}</td>
				<td align="right" class="print blue">{panaderos_5}</td>
			</tr>
			<tr>
				<td class="print">+ Gastos de fabricación</td>
				<td align="right" class="print blue">{gastos_fab_0}</td>
				<td align="right" class="print blue">{gastos_fab_1}</td>
				<td align="right" class="print blue">{gastos_fab_2}</td>
				<td align="right" class="print blue">{gastos_fab_3}</td>
				<td align="right" class="print blue">{gastos_fab_4}</td>
				<td align="right" class="print blue">{gastos_fab_5}</td>
			</tr>
			<tr>
				<td class="print font10 bold">= Costo de producción</td>
				<td align="right" class="print font10 blue bold">{costo_produccion_0}</td>
				<td align="right" class="print font10 blue bold">{costo_produccion_1}</td>
				<td align="right" class="print font10 blue bold">{costo_produccion_2}</td>
				<td align="right" class="print font10 blue bold">{costo_produccion_3}</td>
				<td align="right" class="print font10 blue bold">{costo_produccion_4}</td>
				<td align="right" class="print font10 blue bold">{costo_produccion_5}</td>
			</tr>
			<tr>
				<td colspan="7" class="print">&nbsp;</td>
			</tr>
			<tr>
				<td class="print font10 bold">Utilidad bruta</td>
				<td align="right" class="print font10 blue bold">{utilidad_bruta_0}</td>
				<td align="right" class="print font10 blue bold">{utilidad_bruta_1}</td>
				<td align="right" class="print font10 blue bold">{utilidad_bruta_2}</td>
				<td align="right" class="print font10 blue bold">{utilidad_bruta_3}</td>
				<td align="right" class="print font10 blue bold">{utilidad_bruta_4}</td>
				<td align="right" class="print font10 blue bold">{utilidad_bruta_5}</td>
			</tr>
			<tr>
				<td colspan="7" class="print">&nbsp;</td>
			</tr>
			<tr>
				<td class="print">- Pan comprado</td>
				<td align="right" class="print red">{pan_comprado_0}</td>
				<td align="right" class="print red">{pan_comprado_1}</td>
				<td align="right" class="print red">{pan_comprado_2}</td>
				<td align="right" class="print red">{pan_comprado_3}</td>
				<td align="right" class="print red">{pan_comprado_4}</td>
				<td align="right" class="print red">{pan_comprado_5}</td>
			</tr>
			<tr>
				<td class="print">- Gastos generales</td>
				<td align="right" class="print red">{gastos_generales_0}</td>
				<td align="right" class="print red">{gastos_generales_1}</td>
				<td align="right" class="print red">{gastos_generales_2}</td>
				<td align="right" class="print red">{gastos_generales_3}</td>
				<td align="right" class="print red">{gastos_generales_4}</td>
				<td align="right" class="print red">{gastos_generales_5}</td>
			</tr>
			<tr>
				<td class="print">- Gastos de caja</td>
				<td align="right" class="print red">{gastos_caja_0}</td>
				<td align="right" class="print red">{gastos_caja_1}</td>
				<td align="right" class="print red">{gastos_caja_2}</td>
				<td align="right" class="print red">{gastos_caja_3}</td>
				<td align="right" class="print red">{gastos_caja_4}</td>
				<td align="right" class="print red">{gastos_caja_5}</td>
			</tr>
			<tr>
				<td class="print">- Reservas</td>
				<td align="right" class="print red">{reservas_0}</td>
				<td align="right" class="print red">{reservas_1}</td>
				<td align="right" class="print red">{reservas_2}</td>
				<td align="right" class="print red">{reservas_3}</td>
				<td align="right" class="print red">{reservas_4}</td>
				<td align="right" class="print red">{reservas_5}</td>
			</tr>
			<tr>
				<td class="print">- Gastos pagados por otras cias.</td>
				<td align="right" class="print red">{gastos_otras_cias_0}</td>
				<td align="right" class="print red">{gastos_otras_cias_1}</td>
				<td align="right" class="print red">{gastos_otras_cias_2}</td>
				<td align="right" class="print red">{gastos_otras_cias_3}</td>
				<td align="right" class="print red">{gastos_otras_cias_4}</td>
				<td align="right" class="print red">{gastos_otras_cias_5}</td>
			</tr>
			<tr>
				<td class="print font10 bold">= Total de gastos</td>
				<td align="right" class="print font10 red bold">{total_gastos_0}</td>
				<td align="right" class="print font10 red bold">{total_gastos_1}</td>
				<td align="right" class="print font10 red bold">{total_gastos_2}</td>
				<td align="right" class="print font10 red bold">{total_gastos_3}</td>
				<td align="right" class="print font10 red bold">{total_gastos_4}</td>
				<td align="right" class="print font10 red bold">{total_gastos_5}</td>
			</tr>
			<tr>
				<td class="print font10 bold">+ Ingresos extraordinarios</td>
				<td align="right" class="print font10 blue bold">{ingresos_ext_0}</td>
				<td align="right" class="print font10 blue bold">{ingresos_ext_1}</td>
				<td align="right" class="print font10 blue bold">{ingresos_ext_2}</td>
				<td align="right" class="print font10 blue bold">{ingresos_ext_3}</td>
				<td align="right" class="print font10 blue bold">{ingresos_ext_4}</td>
				<td align="right" class="print font10 blue bold">{ingresos_ext_5}</td>
			</tr>
			<tr>
				<td colspan="7" class="print">&nbsp;</td>
			</tr>
			<tr>
				<td class="print font10 bold">Utilidad del mes</td>
				<td align="right" class="print font10 bold">{utilidad_neta_0}</td>
				<td align="right" class="print font10 bold">{utilidad_neta_1}</td>
				<td align="right" class="print font10 bold">{utilidad_neta_2}</td>
				<td align="right" class="print font10 bold">{utilidad_neta_3}</td>
				<td align="right" class="print font10 bold">{utilidad_neta_4}</td>
				<td align="right" class="print font10 bold">{utilidad_neta_5}</td>
			</tr>
			<tr>
				<td colspan="7" class="print">&nbsp;</td>
			</tr>
			<tr>
				<td class="print">M. prima / Vtas. - Pan comprado</td>
				<td align="right" class="print">{mp_vtas_0}</td>
				<td align="right" class="print">{mp_vtas_1}</td>
				<td align="right" class="print">{mp_vtas_2}</td>
				<td align="right" class="print">{mp_vtas_3}</td>
				<td align="right" class="print">{mp_vtas_4}</td>
				<td align="right" class="print">{mp_vtas_5}</td>
			</tr>
			<tr>
				<td class="print">Utilidad / Producción</td>
				<td align="right" class="print">{utilidad_pro_0}</td>
				<td align="right" class="print">{utilidad_pro_1}</td>
				<td align="right" class="print">{utilidad_pro_2}</td>
				<td align="right" class="print">{utilidad_pro_3}</td>
				<td align="right" class="print">{utilidad_pro_4}</td>
				<td align="right" class="print">{utilidad_pro_5}</td>
			</tr>
			<tr>
				<td class="print">M. prima / Producción</td>
				<td align="right" class="print">{mp_pro_0}</td>
				<td align="right" class="print">{mp_pro_1}</td>
				<td align="right" class="print">{mp_pro_2}</td>
				<td align="right" class="print">{mp_pro_3}</td>
				<td align="right" class="print">{mp_pro_4}</td>
				<td align="right" class="print">{mp_pro_5}</td>
			</tr>
			<tr>
				<td colspan="7" class="print">&nbsp;</td>
			</tr>
			<tr>
				<td class="print bold">Producción (FD)</td>
				<td align="right" class="print blue bold">{produccion_1_0}{por_produccion_1_0}</td>
				<td align="right" class="print blue bold">{produccion_1_1}{por_produccion_1_1}</td>
				<td align="right" class="print blue bold">{produccion_1_2}{por_produccion_1_2}</td>
				<td align="right" class="print blue bold">{produccion_1_3}{por_produccion_1_3}</td>
				<td align="right" class="print blue bold">{produccion_1_4}{por_produccion_1_4}</td>
				<td align="right" class="print blue bold">{produccion_1_5}{por_produccion_1_5}</td>
			</tr>
			<tr>
				<td class="print bold">Producción (FN)</td>
				<td align="right" class="print blue bold">{produccion_2_0}{por_produccion_2_0}</td>
				<td align="right" class="print blue bold">{produccion_2_1}{por_produccion_2_1}</td>
				<td align="right" class="print blue bold">{produccion_2_2}{por_produccion_2_2}</td>
				<td align="right" class="print blue bold">{produccion_2_3}{por_produccion_2_3}</td>
				<td align="right" class="print blue bold">{produccion_2_4}{por_produccion_2_4}</td>
				<td align="right" class="print blue bold">{produccion_2_5}{por_produccion_2_5}</td>
			</tr>
			<tr>
				<td class="print bold">Producción (Biz)</td>
				<td align="right" class="print blue bold">{produccion_3_0}{por_produccion_3_0}</td>
				<td align="right" class="print blue bold">{produccion_3_1}{por_produccion_3_1}</td>
				<td align="right" class="print blue bold">{produccion_3_2}{por_produccion_3_2}</td>
				<td align="right" class="print blue bold">{produccion_3_3}{por_produccion_3_3}</td>
				<td align="right" class="print blue bold">{produccion_3_4}{por_produccion_3_4}</td>
				<td align="right" class="print blue bold">{produccion_3_5}{por_produccion_3_5}</td>
			</tr>
			<tr>
				<td class="print bold">Producción (Rep)</td>
				<td align="right" class="print blue bold">{produccion_4_0}{por_produccion_4_0}</td>
				<td align="right" class="print blue bold">{produccion_4_1}{por_produccion_4_1}</td>
				<td align="right" class="print blue bold">{produccion_4_2}{por_produccion_4_2}</td>
				<td align="right" class="print blue bold">{produccion_4_3}{por_produccion_4_3}</td>
				<td align="right" class="print blue bold">{produccion_4_4}{por_produccion_4_4}</td>
				<td align="right" class="print blue bold">{produccion_4_5}{por_produccion_4_5}</td>
			</tr>
			<tr>
				<td class="print bold">Producción (Pic)</td>
				<td align="right" class="print blue bold">{produccion_8_0}{por_produccion_8_0}</td>
				<td align="right" class="print blue bold">{produccion_8_1}{por_produccion_8_1}</td>
				<td align="right" class="print blue bold">{produccion_8_2}{por_produccion_8_2}</td>
				<td align="right" class="print blue bold">{produccion_8_3}{por_produccion_8_3}</td>
				<td align="right" class="print blue bold">{produccion_8_4}{por_produccion_8_4}</td>
				<td align="right" class="print blue bold">{produccion_8_5}{por_produccion_8_5}</td>
			</tr>
			<tr>
				<td class="print bold">Producción (Gel)</td>
				<td align="right" class="print blue bold">{produccion_9_0}{por_produccion_9_0}</td>
				<td align="right" class="print blue bold">{produccion_9_1}{por_produccion_9_1}</td>
				<td align="right" class="print blue bold">{produccion_9_2}{por_produccion_9_2}</td>
				<td align="right" class="print blue bold">{produccion_9_3}{por_produccion_9_3}</td>
				<td align="right" class="print blue bold">{produccion_9_4}{por_produccion_9_4}</td>
				<td align="right" class="print blue bold">{produccion_9_5}{por_produccion_9_5}</td>
			</tr>
			<tr>
				<td class="print font10 bold">Producción total</td>
				<td align="right" class="print font10 blue bold">{produccion_0}</td>
				<td align="right" class="print font10 blue bold">{produccion_1}</td>
				<td align="right" class="print font10 blue bold">{produccion_2}</td>
				<td align="right" class="print font10 blue bold">{produccion_3}</td>
				<td align="right" class="print font10 blue bold">{produccion_4}</td>
				<td align="right" class="print font10 blue bold">{produccion_5}</td>
			</tr>
			<tr>
				<td class="print font10 bold">Faltante de pan</td>
				<td align="right" class="print font10 bold">{faltante_pan_0}</td>
				<td align="right" class="print font10 bold">{faltante_pan_1}</td>
				<td align="right" class="print font10 bold">{faltante_pan_2}</td>
				<td align="right" class="print font10 bold">{faltante_pan_3}</td>
				<td align="right" class="print font10 bold">{faltante_pan_4}</td>
				<td align="right" class="print font10 bold">{faltante_pan_5}</td>
			</tr>
			<tr>
				<td class="print font10 bold">Rezago inicial</td>
				<td align="right" class="print font10 blue bold">{rezago_ini_0}</td>
				<td align="right" class="print font10 blue bold">{rezago_ini_1}</td>
				<td align="right" class="print font10 blue bold">{rezago_ini_2}</td>
				<td align="right" class="print font10 blue bold">{rezago_ini_3}</td>
				<td align="right" class="print font10 blue bold">{rezago_ini_4}</td>
				<td align="right" class="print font10 blue bold">{rezago_ini_5}</td>
			</tr>
			<tr>
				<td class="print font10 bold">Rezago final</td>
				<td align="right" class="print font10 blue bold">{rezago_fin_0}</td>
				<td align="right" class="print font10 blue bold">{rezago_fin_1}</td>
				<td align="right" class="print font10 blue bold">{rezago_fin_2}</td>
				<td align="right" class="print font10 blue bold">{rezago_fin_3}</td>
				<td align="right" class="print font10 blue bold">{rezago_fin_4}</td>
				<td align="right" class="print font10 blue bold">{rezago_fin_5}</td>
			</tr>
			<tr>
				<td class="print font10 bold">Variación de rezago</td>
				<td align="right" class="print font10 bold">{var_rezago_0}</td>
				<td align="right" class="print font10 bold">{var_rezago_1}</td>
				<td align="right" class="print font10 bold">{var_rezago_2}</td>
				<td align="right" class="print font10 bold">{var_rezago_3}</td>
				<td align="right" class="print font10 bold">{var_rezago_4}</td>
				<td align="right" class="print font10 bold">{var_rezago_5}</td>
			</tr>
			<tr>
				<td class="print font10 bold">Efectivo</td>
				<td align="right" class="print font10 blue bold">{efectivo_0}</td>
				<td align="right" class="print font10 blue bold">{efectivo_1}</td>
				<td align="right" class="print font10 blue bold">{efectivo_2}</td>
				<td align="right" class="print font10 blue bold">{efectivo_3}</td>
				<td align="right" class="print font10 blue bold">{efectivo_4}</td>
				<td align="right" class="print font10 blue bold">{efectivo_5}</td>
			</tr>
			<tr>
				<td colspan="7" class="print">&nbsp;</td>
			</tr>
			<tr>
				<td class="print">Gastos de caja (ingresos)</td>
				<td align="right" class="print blue">{ingresos_0}</td>
				<td align="right" class="print blue">{ingresos_1}</td>
				<td align="right" class="print blue">{ingresos_2}</td>
				<td align="right" class="print blue">{ingresos_3}</td>
				<td align="right" class="print blue">{ingresos_4}</td>
				<td align="right" class="print blue">{ingresos_5}</td>
			</tr>
			<tr>
				<td class="print">Gastos de caja (egresos)</td>
				<td align="right" class="print red">{egresos_0}</td>
				<td align="right" class="print red">{egresos_1}</td>
				<td align="right" class="print red">{egresos_2}</td>
				<td align="right" class="print red">{egresos_3}</td>
				<td align="right" class="print red">{egresos_4}</td>
				<td align="right" class="print red">{egresos_5}</td>
			</tr>
			<tr>
				<td class="print">Total de gastos de caja</td>
				<td align="right" class="print">{total_gastos_caja_0}</td>
				<td align="right" class="print">{total_gastos_caja_1}</td>
				<td align="right" class="print">{total_gastos_caja_2}</td>
				<td align="right" class="print">{total_gastos_caja_3}</td>
				<td align="right" class="print">{total_gastos_caja_4}</td>
				<td align="right" class="print">{total_gastos_caja_5}</td>
			</tr>
			<tr>
				<td class="print">Depósitos</td>
				<td align="right" class="print blue">{depositos_0}</td>
				<td align="right" class="print blue">{depositos_1}</td>
				<td align="right" class="print blue">{depositos_2}</td>
				<td align="right" class="print blue">{depositos_3}</td>
				<td align="right" class="print blue">{depositos_4}</td>
				<td align="right" class="print blue">{depositos_5}</td>
			</tr>
			<tr>
				<td class="print">Otros depósitos</td>
				<td align="right" class="print blue">{otros_depositos_0}</td>
				<td align="right" class="print blue">{otros_depositos_1}</td>
				<td align="right" class="print blue">{otros_depositos_2}</td>
				<td align="right" class="print blue">{otros_depositos_3}</td>
				<td align="right" class="print blue">{otros_depositos_4}</td>
				<td align="right" class="print blue">{otros_depositos_5}</td>
			</tr>
			<tr>
				<td class="print">General</td>
				<td align="right" class="print">{general_0}</td>
				<td align="right" class="print">{general_1}</td>
				<td align="right" class="print">{general_2}</td>
				<td align="right" class="print">{general_3}</td>
				<td align="right" class="print">{general_4}</td>
				<td align="right" class="print">{general_5}</td>
			</tr>
			<tr>
				<td class="print">Diferencia</td>
				<td align="right" class="print">{diferencia_0}</td>
				<td align="right" class="print">{diferencia_1}</td>
				<td align="right" class="print">{diferencia_2}</td>
				<td align="right" class="print">{diferencia_3}</td>
				<td align="right" class="print">{diferencia_4}</td>
				<td align="right" class="print">{diferencia_5}</td>
			</tr>
		</table>
	</div>
</div>
<br style="page-break-after:always;" />
<!-- END BLOCK : reporte1 -->
<!-- START BLOCK : reporte2 -->
<div class="Reporte1">
	<div class="bold" align="center" style="margin-bottom:5px;">Oficinas Administrativas Mollendo</div>
	<div align="center" class="bold" style="margin-bottom:20px;">Comparativo entre Compañías<br />
	del mes de {mes}
	de {anio}</div>
	<div class="Datos">
		<table width="98%" class="print">
			<tr>
				<th width="16%" class="print" scope="col">&nbsp;</th>
				<th width="14%" class="print" scope="col">{nombre_cia_0}</th>
				<th width="14%" class="print" scope="col">{nombre_cia_1}</th>
				<th width="14%" class="print" scope="col">{nombre_cia_2}</th>
				<th width="14%" class="print" scope="col">{nombre_cia_3}</th>
				<th width="14%" class="print" scope="col">{nombre_cia_4}</th>
				<th width="14%" class="print" scope="col">{nombre_cia_5}</th>
			</tr>
			<tr>
				<td class="print">+ Venta</td>
				<td align="right" class="print blue">{venta_0}</td>
				<td align="right" class="print blue">{venta_1}</td>
				<td align="right" class="print blue">{venta_2}</td>
				<td align="right" class="print blue">{venta_3}</td>
				<td align="right" class="print blue">{venta_4}</td>
				<td align="right" class="print blue">{venta_5}</td>
			</tr>
			<tr>
					<td class="print">+ Otros</td>
					<td align="right" class="print blue">{otros_0}</td>
					<td align="right" class="print blue">{otros_1}</td>
					<td align="right" class="print blue">{otros_2}</td>
					<td align="right" class="print blue">{otros_3}</td>
					<td align="right" class="print blue">{otros_4}</td>
					<td align="right" class="print blue">{otros_5}</td>
			</tr>
			<tr>
					<td class="print font10 bold">= Ventas netas</td>
					<td align="right" class="print font10 blue bold">{ventas_netas_0}</td>
					<td align="right" class="print font10 blue bold">{ventas_netas_1}</td>
					<td align="right" class="print font10 blue bold">{ventas_netas_2}</td>
					<td align="right" class="print font10 blue bold">{ventas_netas_3}</td>
					<td align="right" class="print font10 blue bold">{ventas_netas_4}</td>
					<td align="right" class="print font10 blue bold">{ventas_netas_5}</td>
			</tr>
			<tr>
				<td colspan="7" class="print">&nbsp;</td>
			</tr>
			<tr>
				<td class="print">+ Inventario inicial</td>
				<td align="right" class="print blue">{inv_ant_0}</td>
				<td align="right" class="print blue">{inv_ant_1}</td>
				<td align="right" class="print blue">{inv_ant_2}</td>
				<td align="right" class="print blue">{inv_ant_3}</td>
				<td align="right" class="print blue">{inv_ant_4}</td>
				<td align="right" class="print blue">{inv_ant_5}</td>
			</tr>
			<tr>
				<td class="print">+ Compras</td>
				<td align="right" class="print blue">{compras_0}</td>
				<td align="right" class="print blue">{compras_1}</td>
				<td align="right" class="print blue">{compras_2}</td>
				<td align="right" class="print blue">{compras_3}</td>
				<td align="right" class="print blue">{compras_4}</td>
				<td align="right" class="print blue">{compras_5}</td>
			</tr>
			<tr>
				<td class="print">+ Mercancias</td>
				<td align="right" class="print blue">{mercancias_0}</td>
				<td align="right" class="print blue">{mercancias_1}</td>
				<td align="right" class="print blue">{mercancias_2}</td>
				<td align="right" class="print blue">{mercancias_3}</td>
				<td align="right" class="print blue">{mercancias_4}</td>
				<td align="right" class="print blue">{mercancias_5}</td>
			</tr>
			<tr>
				<td class="print">- Inventario final</td>
				<td align="right" class="print blue">{inv_act_0}</td>
				<td align="right" class="print blue">{inv_act_1}</td>
				<td align="right" class="print blue">{inv_act_2}</td>
				<td align="right" class="print blue">{inv_act_3}</td>
				<td align="right" class="print blue">{inv_act_4}</td>
				<td align="right" class="print blue">{inv_act_5}</td>
			</tr>
			<tr>
				<td class="print bold">= Mat. prima utilizada</td>
				<td align="right" class="print blue bold">{mat_prima_utilizada_0}</td>
				<td align="right" class="print blue bold">{mat_prima_utilizada_1}</td>
				<td align="right" class="print blue bold">{mat_prima_utilizada_2}</td>
				<td align="right" class="print blue bold">{mat_prima_utilizada_3}</td>
				<td align="right" class="print blue bold">{mat_prima_utilizada_4}</td>
				<td align="right" class="print blue bold">{mat_prima_utilizada_5}</td>
			</tr>
			<tr>
					<td class="print">+ Gastos de fabricación</td>
					<td align="right" class="print blue">{gastos_fabricacion_0}</td>
					<td align="right" class="print blue">{gastos_fabricacion_1}</td>
					<td align="right" class="print blue">{gastos_fabricacion_2}</td>
					<td align="right" class="print blue">{gastos_fabricacion_3}</td>
					<td align="right" class="print blue">{gastos_fabricacion_4}</td>
					<td align="right" class="print blue">{gastos_fabricacion_5}</td>
			</tr>
			<tr>
				<td class="print font10 bold">= Costo de producción</td>
				<td align="right" class="print font10 blue bold">{costo_produccion_0}</td>
				<td align="right" class="print font10 blue bold">{costo_produccion_1}</td>
				<td align="right" class="print font10 blue bold">{costo_produccion_2}</td>
				<td align="right" class="print font10 blue bold">{costo_produccion_3}</td>
				<td align="right" class="print font10 blue bold">{costo_produccion_4}</td>
				<td align="right" class="print font10 blue bold">{costo_produccion_5}</td>
			</tr>
			<tr>
				<td colspan="7" class="print">&nbsp;</td>
			</tr>
			<tr>
				<td class="print font10 bold">Utilidad bruta</td>
				<td align="right" class="print font10 blue bold">{utilidad_bruta_0}</td>
				<td align="right" class="print font10 blue bold">{utilidad_bruta_1}</td>
				<td align="right" class="print font10 blue bold">{utilidad_bruta_2}</td>
				<td align="right" class="print font10 blue bold">{utilidad_bruta_3}</td>
				<td align="right" class="print font10 blue bold">{utilidad_bruta_4}</td>
				<td align="right" class="print font10 blue bold">{utilidad_bruta_5}</td>
			</tr>
			<tr>
				<td colspan="7" class="print">&nbsp;</td>
			</tr>
			<tr>
					<td class="print">- Gastos generales</td>
					<td align="right" class="print red">{gastos_generales_0}</td>
					<td align="right" class="print red">{gastos_generales_1}</td>
					<td align="right" class="print red">{gastos_generales_2}</td>
					<td align="right" class="print red">{gastos_generales_3}</td>
					<td align="right" class="print red">{gastos_generales_4}</td>
					<td align="right" class="print red">{gastos_generales_5}</td>
			</tr>
			<tr>
				<td class="print">- Gastos de caja</td>
				<td align="right" class="print red">{gastos_caja_0}</td>
				<td align="right" class="print red">{gastos_caja_1}</td>
				<td align="right" class="print red">{gastos_caja_2}</td>
				<td align="right" class="print red">{gastos_caja_3}</td>
				<td align="right" class="print red">{gastos_caja_4}</td>
				<td align="right" class="print red">{gastos_caja_5}</td>
			</tr>
			<tr>
				<td class="print">- Reservas</td>
				<td align="right" class="print red">{reservas_0}</td>
				<td align="right" class="print red">{reservas_1}</td>
				<td align="right" class="print red">{reservas_2}</td>
				<td align="right" class="print red">{reservas_3}</td>
				<td align="right" class="print red">{reservas_4}</td>
				<td align="right" class="print red">{reservas_5}</td>
			</tr>
			<tr>
				<td class="print">- Gastos pagados por otras cias.</td>
				<td align="right" class="print red">{gastos_otras_cias_0}</td>
				<td align="right" class="print red">{gastos_otras_cias_1}</td>
				<td align="right" class="print red">{gastos_otras_cias_2}</td>
				<td align="right" class="print red">{gastos_otras_cias_3}</td>
				<td align="right" class="print red">{gastos_otras_cias_4}</td>
				<td align="right" class="print red">{gastos_otras_cias_5}</td>
			</tr>
			<tr>
				<td class="print font10 bold">= Total de gastos</td>
				<td align="right" class="print font10 red bold">{total_gastos_0}</td>
				<td align="right" class="print font10 red bold">{total_gastos_1}</td>
				<td align="right" class="print font10 red bold">{total_gastos_2}</td>
				<td align="right" class="print font10 red bold">{total_gastos_3}</td>
				<td align="right" class="print font10 red bold">{total_gastos_4}</td>
				<td align="right" class="print font10 red bold">{total_gastos_5}</td>
			</tr>
			<tr>
				<td class="print font10 bold">+ Ingresos extraordinarios</td>
				<td align="right" class="print font10 blue bold">{ingresos_ext_0}</td>
				<td align="right" class="print font10 blue bold">{ingresos_ext_1}</td>
				<td align="right" class="print font10 blue bold">{ingresos_ext_2}</td>
				<td align="right" class="print font10 blue bold">{ingresos_ext_3}</td>
				<td align="right" class="print font10 blue bold">{ingresos_ext_4}</td>
				<td align="right" class="print font10 blue bold">{ingresos_ext_5}</td>
			</tr>
			<tr>
				<td colspan="7" class="print">&nbsp;</td>
			</tr>
			<tr>
				<td class="print font10 bold">Utilidad del mes</td>
				<td align="right" class="print font10 bold">{utilidad_neta_0}</td>
				<td align="right" class="print font10 bold">{utilidad_neta_1}</td>
				<td align="right" class="print font10 bold">{utilidad_neta_2}</td>
				<td align="right" class="print font10 bold">{utilidad_neta_3}</td>
				<td align="right" class="print font10 bold">{utilidad_neta_4}</td>
				<td align="right" class="print font10 bold">{utilidad_neta_5}</td>
			</tr>
			<tr>
				<td colspan="7" class="print">&nbsp;</td>
			</tr>
			<tr>
				<td class="print">M. prima / Ventas</td>
				<td align="right" class="print">{mp_vtas_0}</td>
				<td align="right" class="print">{mp_vtas_1}</td>
				<td align="right" class="print">{mp_vtas_2}</td>
				<td align="right" class="print">{mp_vtas_3}</td>
				<td align="right" class="print">{mp_vtas_4}</td>
				<td align="right" class="print">{mp_vtas_5}</td>
			</tr>
			<tr>
				<td class="print">Pollos vendidos</td>
				<td align="right" class="print">{pollos_vendidos_0}</td>
				<td align="right" class="print">{pollos_vendidos_1}</td>
				<td align="right" class="print">{pollos_vendidos_2}</td>
				<td align="right" class="print">{pollos_vendidos_3}</td>
				<td align="right" class="print">{pollos_vendidos_4}</td>
				<td align="right" class="print">{pollos_vendidos_5}</td>
			</tr>
			<tr>
				<td class="print">Piernas de pavo</td>
				<td align="right" class="print">{p_pavo_0}</td>
				<td align="right" class="print">{p_pavo_1}</td>
				<td align="right" class="print">{p_pavo_2}</td>
				<td align="right" class="print">{p_pavo_3}</td>
				<td align="right" class="print">{p_pavo_4}</td>
				<td align="right" class="print">{p_pavo_5}</td>
			</tr>
		</table>
	</div>
</div>
<br style="page-break-after:always;" />
<!-- END BLOCK : reporte2 -->
<p align="center" class="noDisplay">
	<input name="cerrar" type="button" id="cerrar" value="Cerrar" />
</p>
</body>
</html>
