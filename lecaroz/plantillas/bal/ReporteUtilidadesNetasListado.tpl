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
		<td width="100%" align="center">Reporte de utilidades netas<br />
		al mes de {mes} de {anio}</td>
	</tr>
</table>
<br />
<table align="center" class="print">
	<tr>
		<th colspan="2" class="print" scope="col">Compañía</th>
		<!-- START BLOCK : mes -->
		<th colspan="2" class="print" scope="col">{mes}</th>
		<!-- END BLOCK : mes -->
		<th colspan="2" class="print" scope="col">Total</th>
		<th colspan="2" class="print" scope="col">Promedio</th>
	</tr>
	<!-- START BLOCK : row -->
	<tr>
		<td align="right" class="print">{num_cia}</td>
		<td class="print">{nombre_cia}</td>
		<!-- START BLOCK : utilidad -->
		<td align="right" class="print">{utilidad}</td>
		<td align="right" class="print orange">{porcentaje}</td>
		<!-- END BLOCK : utilidad -->
		<td align="right" class="print bold">{total}</td>
		<td align="right" class="print bold orange">{porcentaje_total}</td>
		<td align="right" class="print bold green">{promedio}</td>
		<td align="right" class="print bold blue">{porcentaje_promedio}</td>
	</tr>
	<!-- END BLOCK : row -->
	<tr>
		<th colspan="2" align="right" class="print">Total</th>
		<!-- START BLOCK : total_mes -->
		<th align="right" class="print">{total}</th>
		<th align="right" class="print orange">{porcentaje}</th>
		<!-- END BLOCK : total_mes -->
		<th align="right" class="print">{total}</th>
		<th align="right" class="print orange">{porcentaje_total}</th>
		<th align="right" class="print">{promedio}</th>
		<th align="right" class="print blue">{porcentaje_promedio}</th>
	</tr>
</table>
{salto} 
<!-- END BLOCK : reporte -->
<p align="center" class="noDisplay">
	<input name="cerrar" type="button" id="cerrar" value="Cerrar" />
</p>
</body>
</html>
