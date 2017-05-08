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
<script type="text/javascript" src="jscripts/fac/FacturasPendientesReporte.js"></script>
</head>

<body>
<!-- START BLOCK : reporte -->
<table width="98%" align="center" class="encabezado">
	<tr>
		<td width="100%" align="center">Facturas pendientes de pago al {fecha}</td>
	</tr>
</table>
<br />
<table align="center" class="print">
	<!-- START BLOCK : cia -->
	<tr>
		<th colspan="5" align="left" class="print font10">{num_cia} {nombre_cia}</th>
	</tr>
	<tr>
		<th class="print">#</th>
		<th class="print">Proveedor</th>
		<th class="print">Fecha</th>
		<th class="print">Factura</th>
		<th class="print">Importe</th>
	</tr>
	<!-- START BLOCK : row -->
	<tr id="row">
		<td align="right" class="print">{num_pro}</td>
		<td class="print">{nombre_pro}</td>
		<td align="center" class="print">{fecha}</td>
		<td align="right" class="print">{num_fact}</td>
		<td align="right" class="print">{importe}</td>
	</tr>
	<!-- END BLOCK : row -->
	<tr>
		<th colspan="4" align="right" class="print">Total</th>
		<th align="right" class="print font10">{total}</th>
	</tr>
	<tr>
		<td colspan="5" align="right" class="print">&nbsp;</td>
	</tr>
	<!-- END BLOCK : cia -->
</table>
{salto} 
<!-- END BLOCK : reporte -->
<p align="center" class="noDisplay">
	<input name="cerrar" type="button" id="cerrar" value="Cerrar" />
</p>
</body>
</html>
