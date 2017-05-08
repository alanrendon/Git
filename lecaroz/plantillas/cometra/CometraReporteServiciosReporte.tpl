<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Reporte de Servicios Cometra</title>
<link href="styles/screen.css" rel="stylesheet" type="text/css" media="screen" />
<link href="styles/print.css" rel="stylesheet" type="text/css" media="print" />
<link href="styles/font-style.css" rel="stylesheet" type="text/css" />
<link href="/lecaroz/styles/screen.css" rel="stylesheet" type="text/css" media="screen" />
<link href="/lecaroz/styles/print.css" rel="stylesheet" type="text/css" media="print" />
<link href="/lecaroz/styles/font-style.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="jscripts/mootools/mootools-1.2-core.js"></script>
<script type="text/javascript" src="jscripts/cometra/CometraReporteServiciosReporte.js"></script>
</head>

<body>
<!-- START BLOCK : reporte -->
<table width="98%" align="center" class="encabezado">
	<tr>
		<td width="100%" align="center">Reporte de Servicios Cometra<br />
		{mes} de {anio}</td>
	</tr>
</table>
<br />
<table align="center" class="print">
	<tr>
		<th nowrap="nowrap" class="print">#</th>
		<th nowrap="nowrap" class="print">Compañía</th>
		<!-- START BLOCK : dia -->
		<th nowrap="nowrap" class="print">{dia}</th>
		<!-- END BLOCK : dia -->
		<th nowrap="nowrap" class="print">Total</th>
		<th nowrap="nowrap" class="print">Servicios</th>
		<th nowrap="nowrap" class="print">Miles</th>
		<th nowrap="nowrap" class="print">M. de<br />
		llave</th>
		<th nowrap="nowrap" class="print">Total <br />
		servicios</th>
		<th nowrap="nowrap" class="print">I.V.A.</th>
		<th nowrap="nowrap" class="print">Retenciones</th>
		<th nowrap="nowrap" class="print">Gran<br />total</th>
	</tr>
	<!-- START BLOCK : row -->
	<tr id="row">
		<td align="right" nowrap="nowrap" class="print">{num_cia}</td>
		<td nowrap="nowrap" class="print">{nombre_cia}</td>
		<!-- START BLOCK : importe -->
		<td align="right" nowrap="nowrap" class="print" style="overflow:hidden;">{importe}</td>
		<!-- END BLOCK : importe -->
		<td align="right" nowrap="nowrap" class="print bold" style="overflow:hidden;">{total}</td>
		<td align="right" nowrap="nowrap" class="print bold" style="overflow:hidden;">{servicios}</td>
		<td align="right" nowrap="nowrap" class="print bold" style="overflow:hidden;">{miles}</td>
		<td align="right" nowrap="nowrap" class="print bold" style="overflow:hidden;">{llave}</td>
		<td align="right" nowrap="nowrap" class="print bold" style="overflow:hidden;">{total_servicios}</td>
		<td align="right" nowrap="nowrap" class="print bold" style="overflow:hidden;">{iva}</td>
		<td align="right" nowrap="nowrap" class="print bold" style="overflow:hidden;">{retenciones}</td>
		<td align="right" nowrap="nowrap" class="print bold" style="overflow:hidden;">{gran_total}</td>
	</tr>
	<!-- END BLOCK : row -->
	<tr>
		<th colspan="2" align="right" nowrap="nowrap" class="print" style="overflow:hidden;">Totales</th>
		<!-- START BLOCK : total -->
		<th align="right" nowrap="nowrap" class="print" style="overflow:hidden;">{total}</th>
		<!-- END BLOCK : total -->
		<th align="right" nowrap="nowrap" class="print" style="overflow:hidden;">{total}</th>
		<th align="right" nowrap="nowrap" class="print" style="overflow:hidden;">{servicios}</th>
		<th align="right" nowrap="nowrap" class="print" style="overflow:hidden;">{miles}</th>
		<th align="right" nowrap="nowrap" class="print" style="overflow:hidden;">{llave}</th>
		<th align="right" nowrap="nowrap" class="print" style="overflow:hidden;">{total_servicios}</th>
		<th align="right" nowrap="nowrap" class="print" style="overflow:hidden;">{iva}</th>
		<th align="right" nowrap="nowrap" class="print" style="overflow:hidden;">{retenciones}</th>
		<th align="right" nowrap="nowrap" class="print" style="overflow:hidden;">{gran_total}</th>
	</tr>
</table>
{salto} 
<!-- END BLOCK : reporte -->
<p align="center" class="noDisplay">
	<input name="cerrar" type="button" id="cerrar" value="Cerrar" />
</p>
</body>
</html>
