<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Facturas Pendientes de Pago</title>
<link href="styles/screen.css" rel="stylesheet" type="text/css" media="screen" />
<link href="styles/print.css" rel="stylesheet" type="text/css" media="print" />
<link href="styles/font-style.css" rel="stylesheet" type="text/css" />
<link href="/lecaroz/styles/screen.css" rel="stylesheet" type="text/css" media="screen" />
<link href="/lecaroz/styles/print.css" rel="stylesheet" type="text/css" media="print" />
<link href="/lecaroz/styles/font-style.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="jscripts/mootools/mootools-1.2-core.js"></script>
<script type="text/javascript" src="jscripts/fac/ReporteFacturasMateriaPrima.js"></script>
</head>

<body>
<!-- START BLOCK : reporte -->
<input name="num_pro" type="hidden" id="num_pro" value="{num_pro}" />
<input name="codmp" type="hidden" id="codmp" value="{codmp}" />
<input name="anio" type="hidden" id="anio" value="{anio}" />
<input name="mes" type="hidden" id="mes" value="{_mes}" />
<table width="98%" align="center" class="encabezado">
	<tr>
		<td width="15%" align="center">{num_pro}</td>
		<td width="70%" align="center">{nombre_pro}</td>
		<td width="15%" align="center">{num_pro}</td>
	</tr>
	<tr>
		<td colspan="3" align="center">Reporte de facturas para el producto &quot;{producto}&quot; del mes de {mes} de {anio}</td>
	</tr>
</table>
<br />
<table align="center" class="print">
	<!-- START BLOCK : cia -->
	<tr>
		<th colspan="4" align="left" class="print font12">{num_cia} {nombre_cia}</th>
	</tr>
	<tr>
		<th class="print">Fecha</th>
		<th class="print">Factura</th>
		<th class="print">Cantidad</th>
		<th class="print">Importe</th>
	</tr>
	<!-- START BLOCK : row -->
	<tr id="row">
		<td align="center" class="print">{fecha}</td>
		<td align="right" class="print">{num_fact}</td>
		<td align="right" class="print">{cantidad}</td>
		<td align="right" class="print">{importe}</td>
	</tr>
	<!-- END BLOCK : row -->
	<tr>
		<th colspan="2" align="right" class="print font12">Totales</th>
		<th align="right" class="print font12">{cantidad}</th>
		<th align="right" class="print font12">{total}</th>
	</tr>
	<tr>
		<td colspan="4" align="center" class="print">&nbsp;</td>
	</tr>
	<!-- END BLOCK : cia -->
	<tr>
		<th colspan="2" align="right" class="print font14">Gran total</th>
		<th align="right" class="print font14">{cantidad}</th>
		<th align="right" class="print font14">{total}</th>
	</tr>
</table>
{salto} 
<!-- END BLOCK : reporte -->
<p align="center" class="noDisplay">
	<input name="cerrar" type="button" id="cerrar" value="Cerrar" />
&nbsp;&nbsp;
<input type="button" name="exportar" id="exportar" value="Exportar a excel" />
</p>
</body>
</html>
