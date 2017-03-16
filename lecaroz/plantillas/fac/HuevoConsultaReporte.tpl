<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Facturas Pendientes de Pago</title>

<link href="/lecaroz/styles/screen.css" rel="stylesheet" type="text/css" media="screen" />
<link href="/lecaroz/styles/print.css" rel="stylesheet" type="text/css" media="print" />
<link href="/lecaroz/styles/font-style.css" rel="stylesheet" type="text/css" />

<script type="text/javascript" src="jscripts/mootools/mootools-1.2-core.js"></script>
<script type="text/javascript" src="jscripts/fac/HuevoConsultaReporte.js"></script>
</head>

<body>
<!-- START BLOCK : reporte_cia -->
<table width="98%" align="center" class="encabezado">
  <tr>
  	<td width="100%" align="center">Reporte de remisiones de huevo</td>
  	</tr>
</table>
<br />
<table align="center" class="print">
	<!-- START BLOCK : c_cia -->
	<tr>
		<th colspan="10" align="left" class="print font12" scope="col">{num_cia} {nombre_cia}</th>
	</tr>
	<!-- START BLOCK : c_pro -->
	<tr>
		<th colspan="10" align="left" class="print font10">{num_pro} {nombre_pro}</th>
	</tr>
	<tr>
		<th class="print">Remisi&oacute;n</th>
		<th class="print">Factura</th>
		<th class="print">Fecha</th>
		<th class="print">Cajas</th>
		<th class="print">Peso bruto<br />
		(pesadas)</th>
		<th class="print">Peso bruto<br />
			(remisi&oacute;n)</th>
		<th class="print">Tara</th>
		<th class="print">Peso neto</th>
		<th class="print">Precio</th>
		<th class="print">Total</th>
	</tr>
	<!-- START BLOCK : c_row -->
	<tr id="row">
		<td align="right" class="print green">{num_rem}</td>
		<td align="right" class="print blue">{num_fact}</td>
		<td align="center" class="print">{fecha}</td>
		<td align="right" class="print">{cajas}</td>
		<td align="right" class="print green">{peso_bruto_pesadas}</td>
		<td align="right" class="print blue">{peso_bruto_remision}</td>
		<td align="right" class="print red">{tara}</td>
		<td align="right" class="print blue">{peso_neto}</td>
		<td align="right" class="print orange">{precio}</td>
		<td align="right" class="print blue">{total}</td>
	</tr>
	<!-- END BLOCK : c_row -->
	<!-- END BLOCK : c_pro -->
	<tr>
		<td colspan="10" class="print">&nbsp;</td>
	</tr>
	<!-- END BLOCK : c_cia -->
</table>
{salto} 
<!-- END BLOCK : reporte_cia -->
<!-- START BLOCK : reporte_pro -->
<table width="98%" align="center" class="encabezado">
  <tr>
  	<td width="100%" align="center">Reporte de remisiones de huevo</td>
  	</tr>
</table>
<br />
<table align="center" class="print">
	<!-- START BLOCK : p_pro -->
	<tr>
		<th colspan="10" align="left" class="print font12" scope="col">{num_pro} {nombre_pro}</th>
	</tr>
	<!-- START BLOCK : p_cia -->
	<tr>
		<th colspan="10" align="left" class="print font10">{num_cia} {nombre_cia}</th>
	</tr>
	<tr>
		<th class="print">Remisi&oacute;n</th>
		<th class="print">Factura</th>
		<th class="print">Fecha</th>
		<th class="print">Cajas</th>
		<th class="print">Peso bruto<br />
		(pesadas)</th>
		<th class="print">Peso bruto<br />
			(remisi&oacute;n)</th>
		<th class="print">Tara</th>
		<th class="print">Peso neto</th>
		<th class="print">Precio</th>
		<th class="print">Total</th>
	</tr>
	<!-- START BLOCK : p_row -->
	<tr id="row">
		<td align="right" class="print green">{num_rem}</td>
		<td align="right" class="print blue">{num_fact}</td>
		<td align="center" class="print">{fecha}</td>
		<td align="right" class="print">{cajas}</td>
		<td align="right" class="print green">{peso_bruto_pesadas}</td>
		<td align="right" class="print blue">{peso_bruto_remision}</td>
		<td align="right" class="print red">{tara}</td>
		<td align="right" class="print blue">{peso_neto}</td>
		<td align="right" class="print orange">{precio}</td>
		<td align="right" class="print blue">{total}</td>
	</tr>
	<!-- END BLOCK : p_row -->
	<!-- END BLOCK : p_cia -->
	<tr>
		<td colspan="10" class="print">&nbsp;</td>
	</tr>
	<!-- END BLOCK : p_pro -->
</table>
{salto} 
<!-- END BLOCK : reporte_pro -->
<p align="center" class="noDisplay">
  <input name="cerrar" type="button" id="cerrar" value="Cerrar" />
</p>
</body>
</html>
