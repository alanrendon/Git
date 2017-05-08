<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Rezagos mensuales de expendios</title>
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
		<td width="10%" align="left" valign="top">{num_cia}</td>
		<td align="center">{nombre_cia}<br />
			Rezagos mensuales de expendios<br />
		de {mes1} de {anio1} a {mes2} de {anio2}</td>
		<td width="10%" align="right" valign="top">{num_cia}</td>
	</tr>
</table>
<br />
<table align="center" class="print">
	<tr>
		<th nowrap="nowrap" class="print">#</th>
		<th nowrap="nowrap" class="print">Expendio</th>
		<!-- START BLOCK : mes -->
		<th nowrap="nowrap" class="print">{mes}</th>
		<!-- END BLOCK : mes -->	</tr>
	<!-- START BLOCK : expendio -->
	<tr id="row">
		<td align="right" nowrap="nowrap" class="print">{num_exp}</td>
		<td nowrap="nowrap" class="print">{nombre_exp}</td>
		<!-- START BLOCK : rezago -->
		<td align="right" nowrap="nowrap" class="print">{rezago}</td>
		<!-- END BLOCK : rezago -->	</tr>
	<!-- END BLOCK : expendio -->
	<tr>
		<th colspan="2" align="right" nowrap="nowrap" class="print">Totales</th>
		<!-- START BLOCK : total -->
		<th align="right" nowrap="nowrap" class="print">{total}</th>
		<!-- END BLOCK : total -->	</tr>
</table>
{salto} 
<!-- END BLOCK : reporte -->
<p align="center" class="noDisplay">
	<input name="cerrar" type="button" id="cerrar" value="Cerrar" />
</p>
</body>
</html>
