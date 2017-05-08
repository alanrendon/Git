<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Reporte de bajas de trabajadores</title>
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
<script type="text/javascript" src="jscripts/nom/TrabajadoresBajasReporteImpreso.js"></script>
</head>

<body>
<table width="98%" align="center" class="encabezado">
	<tr>
		<td width="100%" align="center">Reporte de bajas de trabajadores<br />
		del {fecha1} al {fecha2}</td>
	</tr>
</table>
<br />
<table align="center" class="print">
	<!-- START BLOCK : tipo -->
	<tr>
		<th colspan="6" class="print font10 left">{tipo}</th>
	</tr>
	<tr>
		<th colspan="2" class="print">Compa&ntilde;&iacute;a</th>
		<th colspan="2" class="print">Trabajador</th>
		<th class="print">Alta</th>
		<th class="print">Baja</th>
	</tr>
	<!-- START BLOCK : row -->
	<tr>
		<td class="print right">{num_cia}</td>
		<td class="print">{nombre_cia}</td>
		<td class="print right">{num_emp}</td>
		<td class="print">{trabajador}</td>
		<td class="print blue center">{fecha_alta}</td>
		<td class="print red center">{fecha_baja}</td>
	</tr>
	<!-- END BLOCK : row -->
	<tr>
		<th colspan="6" class="print font10 right">Total de empleados: {total}</th>
	</tr>
	<tr>
		<td colspan="6" class="print">&nbsp;</td>
	</tr>
	<!-- END BLOCK : tipo -->
</table>
{salto} 
<p align="center" class="noDisplay">
	<input name="cerrar" type="button" id="cerrar" value="Cerrar" />
</p>
</body>
</html>
