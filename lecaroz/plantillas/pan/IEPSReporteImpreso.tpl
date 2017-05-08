<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Reporte de I.E.P.S.</title>
<link href="styles/screen.css" rel="stylesheet" type="text/css" media="screen" />
<link href="styles/print.css" rel="stylesheet" type="text/css" media="print" />
<link href="styles/font-style.css" rel="stylesheet" type="text/css" />
<link href="/lecaroz/styles/screen.css" rel="stylesheet" type="text/css" media="screen" />
<link href="/lecaroz/styles/print.css" rel="stylesheet" type="text/css" media="print" />
<link href="/lecaroz/styles/font-style.css" rel="stylesheet" type="text/css" />

<script type="text/javascript" src="/lecaroz/jscripts/mootools1.4/mootools-core-1.4.5.js"></script>
<script type="text/javascript" src="/lecaroz/jscripts/mootools1.4/string.implement.js"></script>
<script type="text/javascript" src="/lecaroz/jscripts/mootools1.4/number.implement.js"></script>
<script type="text/javascript" src="/lecaroz/jscripts/mootools1.4/array.implement.js"></script>
<script type="text/javascript" src="/lecaroz/jscripts/mootools1.4/FormValidator.js"></script>
<script type="text/javascript" src="jscripts/pan/IEPSReporteImpreso.js"></script>
</head>

<body>
<table width="98%" align="center" class="encabezado">
	<tr>
		<td width="100%" align="center">Reporte de I.E.P.S.<br />
		del mes de {mes} de {anio}</td>
	</tr>
</table>
<br />
<table align="center" class="print">
	<tr>
		<th class="print">Compa&ntilde;&iacute;a</th>
		<!-- START BLOCK : turno_titulo -->
		<th class="print">{turno}</th>
		<!-- END BLOCK : turno_titulo -->
		<th class="print">Efectivo</th>
		<th class="print">Faltante<br />de pan</th>
		<th class="print">Total general</th>
		<th class="print">I.E.P.S.</th>
	</tr>
	<!-- START BLOCK : cia -->
	<tr>
		<td class="print">{num_cia} {nombre_cia}</td>
		<!-- START BLOCK : efectivo_turno -->
		<td class="print" align="right">{efectivo_turno}</td>
		<!-- END BLOCK : efectivo_turno -->
		<td align="right" class="print bold">{efectivo}</td>
		<td align="right" class="print bold">{faltante_pan}</td>
		<td align="right" class="print bold green">{total_general}</td>
		<td align="right" class="print bold blue">{ieps}</td>
	</tr>
	<!-- END BLOCK : cia -->
	<tr>
		<th align="right" class="print font10">Totales</th>
		<!-- START BLOCK : total_turno -->
		<th align="right" class="print font10"><span class="green">{total_turno}</span></th>
		<!-- END BLOCK : total_turno -->
		<th align="right" class="print font10"><span class="blue">{total_efectivo}</span></th>
		<th align="right" class="print font10"><span class="blue">{faltante_pan}</span></th>
		<th align="right" class="print font10"><span class="blue">{total_general}</span></th>
		<th align="right" class="print font10"><span class="blue">{total_ieps}</span></th>
	</tr>
</table>
{salto} 
<p align="center" class="noDisplay">
	<input name="cerrar" type="button" id="cerrar" value="Cerrar" />
</p>
</body>
</html>
