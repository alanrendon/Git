<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Auxiliar de Inventario</title>
<link href="../../styles/screen.css" rel="stylesheet" type="text/css" media="screen" />
<link href="../../styles/print.css" rel="stylesheet" type="text/css" media="print" />
<link href="../../styles/font-style.css" rel="stylesheet" type="text/css" />
<link href="styles/screen.css" rel="stylesheet" type="text/css" media="screen" />
<link href="styles/print.css" rel="stylesheet" type="text/css" media="print" />
<link href="styles/font-style.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="jscripts/mootools/mootools-1.2-core.js"></script>
<script type="text/javascript" src="jscripts/mootools/mootools-1.2-more.js"></script>
<script type="text/javascript" src="jscripts/bal/AuxInv.js"></script>
</head>

<body>
<!-- START BLOCK : totales_real -->
<input name="num_cia" id="num_cia" type="hidden" value="{num_cia}" />
<input name="anio" id="anio" type="hidden" value="{anio}" />
<input name="mes" id="mes" type="hidden" value="{_mes}" />
<input name="inv" type="hidden" id="inv" value="{inv}" />
<table width="100%" align="center" class="encabezado" style="border-collapse:collapse;">
	<tr>
		<td>{num_cia}</td>
		<td align="center">{nombre_cia}</td>
		<td align="right">{num_cia}</td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td align="center">Auxiliar de Inventario<br />
			{mes} de {anio} </td>
		<td>&nbsp;</td>
	</tr>
</table>
<br />
<table width="98%" align="center" class="print" style="border-collapse:collapse;">
	<tr>
		<th colspan="2" rowspan="2" class="print" scope="col">Producto</th>
		<th colspan="3" class="print green" scope="col">Inventario Inicial </th>
		<th colspan="2" class="print blue" scope="col">Entradas</th>
		<th colspan="2" class="print red" scope="col">Salidas</th>
		<th colspan="3" class="print green" scope="col">Inventario Final </th>
	</tr>
	<tr>
		<th class="print green" scope="col">Existencia</th>
		<th class="print green" scope="col">Precio</th>
		<th class="print green" scope="col">Costo</th>
		<th class="print blue" scope="col">Unidades</th>
		<th class="print blue" scope="col">Costo</th>
		<th class="print red" scope="col">Unidades</th>
		<th class="print red" scope="col">Costo</th>
		<th class="print green" scope="col">Existencia</th>
		<th class="print green" scope="col">Precio</th>
		<th class="print green" scope="col">Costo</th>
	</tr>
	<!-- START BLOCK : mp_real -->
	<tr id="row">
		<td align="right" class="print"><a class="enlace" title="cod" alt="{codmp}">{codmp}</a></td>
		<td class="print">{nombre} </td>
		<td align="right" class="print green">{existencia_ini}</td>
		<td align="right" class="print green">{precio_ini}</td>
		<td align="right" class="print green">{costo_ini}</td>
		<td align="right" class="print blue">{entradas}</td>
		<td align="right" class="print blue">{compras}</td>
		<td align="right" class="print red">{salidas}</td>
		<td align="right" class="print red">{consumos}</td>
		<td align="right" class="print {style}">{existencia}</td>
		<td align="right" class="print green">{precio}</td>
		<td align="right" class="print {style}">{costo}</td>
	</tr>
	<!-- END BLOCK : mp_real -->
	<tr>
		<th colspan="4" align="right" class="print">Totales</th>
		<th align="right" class="print green font10">{costo_ini}</th>
		<th class="print">&nbsp;</th>
		<th align="right" class="print blue font10">{compras}</th>
		<th class="print">&nbsp;</th>
		<th align="right" class="print red font10">{consumos}</th>
		<th colspan="2" class="print">&nbsp;</th>
		<th align="right" class="print green font10">{costo}</th>
	</tr>
	<tr>
		<th colspan="6" align="right" class="print">Compras</th>
		<th align="right" class="print blue font10">{compras_facturas}</th>
		<th colspan="5" class="print">&nbsp;</th>
	</tr>
	<tr>
		<th colspan="6" align="right" class="print">Mercancias</th>
		<th align="right" class="print blue font10">{mercancias}</th>
		<th colspan="5" class="print">&nbsp;</th>
	</tr>
</table>
<!-- END BLOCK : totales_real -->
<!-- START BLOCK : detallado_real -->
<table width="100%" align="center" class="encabezado" style="border-collapse:collapse;">
	<tr>
		<td>{num_cia}</td>
		<td align="center">{nombre_cia}</td>
		<td align="right">{num_cia}</td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td align="center">{codmp} {nombre} <br />
			{mes} de {anio} </td>
		<td>&nbsp;</td>
	</tr>
</table>
<br />
<table width="98%" align="center" class="print">
	<tr>
		<th rowspan="2" class="print" scope="col">Fecha</th>
		<th rowspan="2" class="print" scope="col">Concepto</th>
		<th rowspan="2" class="print" scope="col">Proveedor</th>
		<th rowspan="2" class="print" scope="col">Precio</th>
		<th colspan="2" class="print" scope="col">Entradas</th>
		<th colspan="2" class="print" scope="col">Salidas</th>
		<th colspan="2" class="print" scope="col">Inventario</th>
		<th rowspan="2" class="print" scope="col">Precio<br />
			Promedio</th>
		<th rowspan="2" class="print" scope="col">Dif.</th>
	</tr>
	<tr>
		<th class="print">Unidades</th>
		<th class="print">Costo</th>
		<th class="print">Unidades</th>
		<th class="print">Turno</th>
		<th class="print">Existencia</th>
		<th class="print">Costo</th>
	</tr>
	<tr>
		<th colspan="8" align="right" class="print">Inventario Inicial</th>
		<th class="print font10">{existencia_ini}</th>
		<th class="print font10">{costo_ini}</th>
		<th class="print font10">{precio_ini}</th>
		<th class="print">&nbsp;</th>
	</tr>
	<!-- START BLOCK : mov_real -->
	<!-- START BLOCK : mov_real_yes -->
	<tr id="row" style="background-color:#{bgcolor}">
		<td align="center" class="print">{fecha}</td>
		<td class="print">{concepto}</td>
		<td class="print">{proveedor}</td>
		<td align="right" class="print green">{precio_mov}</td>
		<td align="right" class="print blue">{unidades_entrada}</td>
		<td align="right" class="print blue">{costo_entrada}</td>
		<td align="right" class="print red">{unidades_salida}</td>
		<td align="center" class="print"{color_turno}>{turno}</td>
		<td align="right" class="print {style}">{existencia}</td>
		<td align="right" class="print {style}">{costo}</td>
		<td align="right" class="print green">{precio}</td>
		<td align="right" class="print">{dif}</td>
	</tr>
	<!-- END BLOCK : mov_real_yes -->
	<!-- START BLOCK : mov_real_no -->
	<tr id="row" style="background-color:#{bgcolor}">
		<td colspan="12" align="center" class="print bold red">SIN MOVIMIENTOS EL DIA {fecha}</td>
	</tr>
	<!-- END BLOCK : mov_real_no -->
	<!-- END BLOCK : mov_real -->
	<tr>
		<th colspan="4" align="right" class="print">Totales</th>
		<th align="right" class="print blue font10">{unidades_entrada}</th>
		<th align="right" class="print blue font10">{costo_entrada}</th>
		<th align="right" class="print red font10">{unidades_salida}</th>
		<th align="right" class="print red font10">{costo_salida}</th>
		<th align="right" class="print font10">{existencia}</th>
		<th align="right" class="print font10">{costo}</th>
		<th align="right" class="print font10">{precio}</th>
		<th class="print">&nbsp;</th>
	</tr>
	<tr>
		<th colspan="4" align="right" class="print">Compras</th>
		<th align="right" class="print blue font10">{unidades_compras}</th>
		<th align="right" class="print blue font10">{costo_compras}</th>
		<th colspan="6" class="print">&nbsp;</th>
	</tr>
	<tr>
		<th colspan="4" align="right" class="print">Mercanc&iacute;as</th>
		<th align="right" class="print blue font10">{unidades_mercancias}</th>
		<th align="right" class="print blue font10">{costo_mercancias}</th>
		<th colspan="6" class="print">&nbsp;</th>
	</tr>
</table>
<br />
<table align="center" class="print">
	<tr>
		<th class="print" scope="col">Consumos</th>
		<th class="print" scope="col">Ene</th>
		<th class="print" scope="col">Feb</th>
		<th class="print" scope="col">Mar</th>
		<th class="print" scope="col">Abr</th>
		<th class="print" scope="col">May</th>
		<th class="print" scope="col">Jun</th>
		<th class="print" scope="col">Jul</th>
		<th class="print" scope="col">Ago</th>
		<th class="print" scope="col">Sep</th>
		<th class="print" scope="col">Oct</th>
		<th class="print" scope="col">Nov</th>
		<th class="print" scope="col">Dic</th>
	</tr>
	<tr>
		<th class="print" scope="row">{anio1}</th>
		<td align="right" class="print">{con1_1}</td>
		<td align="right" class="print">{con1_2}</td>
		<td align="right" class="print">{con1_3}</td>
		<td align="right" class="print">{con1_4}</td>
		<td align="right" class="print">{con1_5}</td>
		<td align="right" class="print">{con1_6}</td>
		<td align="right" class="print">{con1_7}</td>
		<td align="right" class="print">{con1_8}</td>
		<td align="right" class="print">{con1_9}</td>
		<td align="right" class="print">{con1_10}</td>
		<td align="right" class="print">{con1_11}</td>
		<td align="right" class="print">{con1_12}</td>
	</tr>
	<tr>
		<th class="print" scope="row">{anio2}</th>
		<td align="right" class="print">{con2_1}</td>
		<td align="right" class="print">{con2_2}</td>
		<td align="right" class="print">{con2_3}</td>
		<td align="right" class="print">{con2_4}</td>
		<td align="right" class="print">{con2_5}</td>
		<td align="right" class="print">{con2_6}</td>
		<td align="right" class="print">{con2_7}</td>
		<td align="right" class="print">{con2_8}</td>
		<td align="right" class="print">{con2_9}</td>
		<td align="right" class="print">{con2_10}</td>
		<td align="right" class="print">{con2_11}</td>
		<td align="right" class="print">{con2_12}</td>
	</tr>
</table>
<!-- START BLOCK : consumos_real -->
<br />
<table align="center" class="print">
	<tr>
		<th class="print" scope="col">Turno</th>
		<th class="print" scope="col">Consumo</th>
		<th class="print" scope="col">Costo</th>
	</tr>
	<!-- START BLOCK : consumo_real -->
	<tr>
		<td class="print">{turno}</td>
		<td align="right" class="print">{consumo}</td>
		<td align="right" class="print">{costo}</td>
	</tr>
	<!-- END BLOCK : consumo_real -->
</table>
<br />
<table align="center" class="print">
	<tr>
		<th class="print" scope="col">Consumo<br />
			promedio</th>
		<th class="print" scope="col">Existencia<br />
			estimada</th>
	</tr>
	<tr>
		<td align="center" class="print font12 bold">{consumo_promedio}</td>
		<td align="center" class="print font12 bold">{existencia_estimada}</td>
	</tr>
</table>
<!-- END BLOCK : consumos_real -->
<!-- START BLOCK : no_consumo_real -->
<p align="center" class="font14 bold red">NO HAY CONSUMOS</p>
<!-- END BLOCK : no_consumo_real -->
<!-- START BLOCK : precios_real -->
<br />
<table align="center" class="print">
	<tr>
		<th class="print" scope="col">Proveedor</th>
		<th class="print" scope="col">Fecha</th>
		<th class="print" scope="col">Precio</th>
	</tr>
	<!-- START BLOCK : precio_real -->
	<tr>
		<td class="print">{num_pro} {nombre_pro}</td>
		<td align="center" class="print">{fecha}</td>
		<td align="right" class="print">{precio}</td>
	</tr>
	<!-- END BLOCK : precio_real -->
</table>
<!-- END BLOCK : precios_real -->
<!-- END BLOCK : detallado_real -->
<!-- START BLOCK : totales_virtual -->
<input name="num_cia" id="num_cia" type="hidden" value="{num_cia}" />
<input name="anio" id="anio" type="hidden" value="{anio}" />
<input name="mes" id="mes" type="hidden" value="{_mes}" />
<input name="inv" type="hidden" id="inv" value="{inv}" />
<table width="100%" align="center" class="encabezado" style="border-collapse:collapse;">
	<tr>
		<td>{num_cia}</td>
		<td align="center">{nombre_cia}</td>
		<td align="right">{num_cia}</td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td align="center">Auxiliar de Inventario<br />
			{mes} de {anio} </td>
		<td>&nbsp;</td>
	</tr>
</table>
<br />
<table width="98%" align="center" class="print" style="border-collapse:collapse;">
	<tr>
		<th colspan="2" rowspan="2" class="print" scope="col">Producto</th>
		<th class="print green" scope="col">Inventario Inicial </th>
		<th class="print blue" scope="col">Entradas</th>
		<th class="print red" scope="col">Salidas</th>
		<th class="print green" scope="col">Inventario Final </th>
	</tr>
	<tr>
		<th class="print green" scope="col">Existencia</th>
		<th class="print blue" scope="col">Unidades</th>
		<th class="print red" scope="col">Unidades</th>
		<th class="print green" scope="col">Existencia</th>
	</tr>
	<!-- START BLOCK : mp_virtual -->
	<tr id="row">
		<td align="right" class="print"><a class="enlace" title="cod" alt="{codmp}">{codmp}</a></td>
		<td class="print">{nombre} </td>
		<td align="right" class="print green">{existencia_ini}</td>
		<td align="right" class="print blue">{entradas}</td>
		<td align="right" class="print red">{salidas}</td>
		<td align="right" class="print {style}">{existencia}</td>
	</tr>
	<!-- END BLOCK : mp_virtual -->
</table>
<!-- END BLOCK : totales_virtual -->
<!-- START BLOCK : detallado_virtual -->
<table width="100%" align="center" class="encabezado" style="border-collapse:collapse;">
	<tr>
		<td>{num_cia}</td>
		<td align="center">{nombre_cia}</td>
		<td align="right">{num_cia}</td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td align="center">{codmp} {nombre} <br />
			{mes} de {anio} </td>
		<td>&nbsp;</td>
	</tr>
</table>
<br />
<table width="98%" align="center" class="print">
	<tr>
		<th rowspan="2" class="print" scope="col">Fecha</th>
		<th rowspan="2" class="print" scope="col">Concepto</th>
		<th class="print" scope="col">Entradas</th>
		<th colspan="2" class="print" scope="col">Salidas</th>
		<th class="print" scope="col">Inventario</th>
	</tr>
	<tr>
		<th class="print">Unidades</th>
		<th class="print">Unidades</th>
		<th class="print">Turno</th>
		<th class="print">Existencia</th>
	</tr>
	<tr>
		<th colspan="5" align="right" class="print">Inventario Inicial</th>
		<th class="print font10">{existencia_ini}</th>
	</tr>
	<!-- START BLOCK : mov_virtual -->
	<!-- START BLOCK : mov_virtual_yes -->
	<tr id="row" style="background-color:#{bgcolor}">
		<td align="center" class="print">{fecha}</td>
		<td class="print">{concepto}</td>
		<td align="right" class="print blue">{unidades_entrada}</td>
		<td align="right" class="print red">{unidades_salida}</td>
		<td align="center" class="print"{color_turno}>{turno}</td>
		<td align="right" class="print green">{existencia}</td>
	</tr>
	<!-- END BLOCK : mov_virtual_yes -->
	<!-- START BLOCK : mov_virtual_no -->
	<tr id="row" style="background-color:#{bgcolor}">
		<td colspan="6" align="center" class="print bold red">SIN MOVIMIENTOS EL DIA {fecha}</td>
	</tr>
	<!-- END BLOCK : mov_virtual_no -->
	<!-- END BLOCK : mov_virtual -->
	<tr>
		<th colspan="2" align="right" class="print">Totales</th>
		<th align="right" class="print blue font10">{unidades_entrada}</th>
		<th align="right" class="print red font10">{unidades_salida}</th>
		<th align="right" class="print red font10">&nbsp;</th>
		<th align="right" class="print font10">{existencia}</th>
	</tr>
</table>
<br />
<table align="center" class="print">
	<tr>
		<th class="print" scope="col">Consumos</th>
		<th class="print" scope="col">Ene</th>
		<th class="print" scope="col">Feb</th>
		<th class="print" scope="col">Mar</th>
		<th class="print" scope="col">Abr</th>
		<th class="print" scope="col">May</th>
		<th class="print" scope="col">Jun</th>
		<th class="print" scope="col">Jul</th>
		<th class="print" scope="col">Ago</th>
		<th class="print" scope="col">Sep</th>
		<th class="print" scope="col">Oct</th>
		<th class="print" scope="col">Nov</th>
		<th class="print" scope="col">Dic</th>
	</tr>
	<tr>
		<th class="print" scope="row">{anio1}</th>
		<td align="right" class="print">{con1_1}</td>
		<td align="right" class="print">{con1_2}</td>
		<td align="right" class="print">{con1_3}</td>
		<td align="right" class="print">{con1_4}</td>
		<td align="right" class="print">{con1_5}</td>
		<td align="right" class="print">{con1_6}</td>
		<td align="right" class="print">{con1_7}</td>
		<td align="right" class="print">{con1_8}</td>
		<td align="right" class="print">{con1_9}</td>
		<td align="right" class="print">{con1_10}</td>
		<td align="right" class="print">{con1_11}</td>
		<td align="right" class="print">{con1_12}</td>
	</tr>
	<tr>
		<th class="print" scope="row">{anio2}</th>
		<td align="right" class="print">{con2_1}</td>
		<td align="right" class="print">{con2_2}</td>
		<td align="right" class="print">{con2_3}</td>
		<td align="right" class="print">{con2_4}</td>
		<td align="right" class="print">{con2_5}</td>
		<td align="right" class="print">{con2_6}</td>
		<td align="right" class="print">{con2_7}</td>
		<td align="right" class="print">{con2_8}</td>
		<td align="right" class="print">{con2_9}</td>
		<td align="right" class="print">{con2_10}</td>
		<td align="right" class="print">{con2_11}</td>
		<td align="right" class="print">{con2_12}</td>
	</tr>
</table>
<!-- START BLOCK : consumos_virtual -->
<br />
<table align="center" class="print">
	<tr>
		<th class="print" scope="col">Turno</th>
		<th class="print" scope="col">Consumo</th>
		<th class="print" scope="col">Costo</th>
	</tr>
	<!-- START BLOCK : consumo_virtual -->
	<tr>
		<td class="print">{turno}</td>
		<td align="right" class="print">{consumo}</td>
		<td align="right" class="print">{costo}</td>
	</tr>
	<!-- END BLOCK : consumo_virtual -->
</table>
<br />
<table align="center" class="print">
	<tr>
		<th class="print" scope="col">Consumo<br />
			promedio</th>
		<th class="print" scope="col">Existencia<br />
			estimada</th>
	</tr>
	<tr>
		<td align="center" class="print font12 bold">{consumo_promedio}</td>
		<td align="center" class="print font12 bold">{existencia_estimada}</td>
	</tr>
</table>
<!-- END BLOCK : consumos_virtual -->
<!-- START BLOCK : no_consumo_virtual -->
<p align="center" class="font14 bold red">NO HAY CONSUMOS</p>
<!-- END BLOCK : no_consumo_virtual -->
<!-- START BLOCK : precios_virtual -->
<br />
<table class="print">
	<tr>
		<th class="print" scope="col">Proveedor</th>
		<th class="print" scope="col">Fecha</th>
		<th class="print" scope="col">Precio</th>
	</tr>
	<!-- START BLOCK : precio_virtual -->
	<tr>
		<td class="print">{num_pro} {nombre_pro}</td>
		<td align="center" class="print">{fecha}</td>
		<td align="right" class="print">{precio}</td>
	</tr>
	<!-- END BLOCK : precio_virtual -->
</table>
<!-- END BLOCK : precios_virtual -->
<!-- END BLOCK : detallado_virtual -->
<!-- START BLOCK : no_result -->
<table align="center" class="font font16 bold">
	<tr>
		<td>No hay resultados </td>
	</tr>
</table>
<!-- END BLOCK : no_result -->
<p align="center" class="noDisplay">
	<input name="cerrar" type="button" id="cerrar" value="Cerrar" />
</p>
</body>
</html>
