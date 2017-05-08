<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf8" />
<title>Auxiliar de Inventario</title>
<link href="/lecaroz/styles/screen.css" rel="stylesheet" type="text/css" media="screen" />
<link href="/lecaroz/styles/print.css" rel="stylesheet" type="text/css" media="print" />
<link href="/lecaroz/styles/font-style.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="/lecaroz/jscripts/mootools/mootools-1.2-core.js"></script>
<script type="text/javascript" src="/lecaroz/jscripts/mootools/mootools-1.2-more.js"></script>
<script type="text/javascript" src="/lecaroz/jscripts/bal/AuxInv.js"></script>
</head>

<body>
<!-- START BLOCK : reporte -->
<table width="100%" align="center" class="encabezado" style="border-collapse:collapse;">
	<tr>
		<td>{num_cia}</td>
		<td align="center">{nombre_cia}</td>
		<td align="right">{num_cia}</td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td align="center">{codmp} {nombre_mp}<br />{mes1} DE {anio1} A {mes2} DE {anio2}</td>
		<td>&nbsp;</td>
	</tr>
</table>
<br />
<table width="98%" align="center" class="print">
	<tr>
		<th rowspan="2" class="print" scope="col">Fecha</th>
		<th colspan="2" class="print" scope="col">Inventario inicio</th>
		<th rowspan="2" class="print" scope="col">Proveedor</th>
		<th rowspan="2" class="print" scope="col">Documento</th>
		<th colspan="2" class="print" scope="col">Entradas</th>
		<th colspan="2" class="print" scope="col">Salidas</th>
		<th colspan="2" class="print" scope="col">Inventario termino</th>
		<th rowspan="2" class="print" scope="col">Precio<br />
			Promedio</th>
		<th rowspan="2" class="print" scope="col">Dif.</th>
	</tr>
	<tr>
		<th class="print">Existencia</th>
		<th class="print">Costo</th>
		<th class="print">Unidades</th>
		<th class="print">Costo</th>
		<th class="print">Unidades</th>
		<th class="print">Costo</th>
		<th class="print">Existencia</th>
		<th class="print">Costo</th>
	</tr>
	<tr>
		<th align="right" class="print">Inicial</th>
		<th align="right" class="print font10">{existencia_ini}</th>
		<th align="right" class="print font10">{costo_ini}</th>
		<th colspan="8" align="right" class="print">&nbsp;</th>
		<th align="right" class="print font10">{precio_ini}</th>
		<th align="right" class="print">&nbsp;</th>
	</tr>
	<!-- START BLOCK : mov -->
	<!-- START BLOCK : mov_yes -->
	<tr id="row" style="background-color:#{bgcolor}">
		<td align="center" class="print">{fecha}</td>
		<td align="right" class="print">{existencia_ini}</td>
		<td align="right" class="print">{costo_ini}</td>
		<td class="print">{num_pro} {nombre_pro}</td>
		<td class="print">{num_fact}</td>
		<td align="right" class="print blue">{unidades_entrada}</td>
		<td align="right" class="print blue">{costo_entrada}</td>
		<td align="right" class="print red">{unidades_salida}</td>
		<td align="right" class="print red">{costo_salida}</td>
		<td align="right" class="print">{existencia_fin}</td>
		<td align="right" class="print">{costo_fin}</td>
		<td align="right" class="print green">{precio}</td>
		<td align="right" class="print">{dif}</td>
	</tr>
	<!-- END BLOCK : mov_yes -->
	<!-- START BLOCK : mov_no -->
	<tr id="row" style="background-color:#{bgcolor}">
		<td colspan="11" align="center" class="print bold red">SIN MOVIMIENTOS EL DIA {fecha}</td>
	</tr>
	<!-- END BLOCK : mov_no -->
	<!-- END BLOCK : mov -->
	<tr>
		<th colspan="5" align="right" class="print">Final</th>
		<th align="right" class="print blue font10">{unidades_entrada}</th>
		<th align="right" class="print blue font10">{costo_entrada}</th>
		<th align="right" class="print red font10">{unidades_salida}</th>
		<th align="right" class="print red font10">{costo_salida}</th>
		<th align="right" class="print font10">{existencia}</th>
		<th align="right" class="print font10">{costo}</th>
		<th align="right" class="print font10">{precio}</th>
		<th class="print">&nbsp;</th>
	</tr>
</table>
<!-- START BLOCK : totales -->
<br />
<table align="center" class="print">
	<tr>
		<th colspan="5" class="print font12">Totales</th>
	</tr>
	<tr>
		<th class="print font12">Tipo</th>
		<th class="print font12">Inicial</th>
		<th class="print font12">Entradas</th>
		<th class="print font12">Salidas</th>
		<th class="print font12">Final</th>
	</tr>
	<!-- START BLOCK : tipo -->
	<tr>
		<td class="print font12 bold">{tipo}</td>
		<td class="print font12 bold green right">{inicio}</td>
		<td class="print font12 bold blue right">{entradas}</td>
		<td class="print font12 bold red right">{salidas}</td>
		<td class="print font12 bold green right">{fin}</td>
	</tr>
	<!-- END BLOCK : tipo -->
</table>
<!-- END BLOCK : totales -->
<br style="page-break-after:always;" />
<!-- END BLOCK : reporte -->
<!-- START BLOCK : no_result -->
<table align="center" class="font font16 bold">
	<tr>
		<td>No hay resultados</td>
	</tr>
</table>
<!-- END BLOCK : no_result -->
<p align="center" class="noDisplay">
	<input name="cerrar" type="button" id="cerrar" value="Cerrar" />
</p>
</body>
</html>
