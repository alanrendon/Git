<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Reporte de Rentas</title>
<link href="styles/screen.css" rel="stylesheet" type="text/css" media="screen" />
<link href="styles/print.css" rel="stylesheet" type="text/css" media="print" />
<link href="styles/font-style.css" rel="stylesheet" type="text/css" />
<link href="/lecaroz/styles/screen.css" rel="stylesheet" type="text/css" media="screen" />
<link href="/lecaroz/styles/print.css" rel="stylesheet" type="text/css" media="print" />
<link href="/lecaroz/styles/font-style.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="jscripts/mootools/mootools-1.2-core.js"></script>
<script type="text/javascript" src="jscripts/ren/ArrendamientosVencidosReporte.js"></script>
</head>

<body>
<!-- START BLOCK : reporte -->
<table width="98%" align="center" class="encabezado">
	<tr>
		<td width="100%" align="center"><span style="float:left;">{num_cia}</span><span style="float:right;">{num_cia}</span>{nombre_cia}<br />
		Reporte	de	expendios por día<br />
		del	{fecha1} al {fecha2}<br /></td>
	</tr>
</table>
<br />
<table align="center" class="print">
	<!-- START BLOCK : expendio -->
	<tr>
		<th colspan="9" align="left" class="print font12">{num_exp} {nombre_exp}</th>
	</tr>
	<tr>
		<th class="print">Día</th>
		<th class="print">Rezago<br />
			inicial</th>
		<th class="print">Partidas</th>
		<th class="print">Total</th>
		<th class="print">Diferencia</th>
		<th class="print">%</th>
		<th class="print">Abonos</th>
		<th class="print">Devuelto</th>
		<th class="print">Rezago<br />
			final</th>
	</tr>
	<!-- START BLOCK : dia -->
	<tr>
		<td align="center" class="print">{fecha}</td>
		<td align="right" class="print red">{rezago_inicial}</td>
		<td align="right" class="print blue">{partidas}</td>
		<td align="right" class="print green">{total}</td>
		<td align="right" class="print orange">{diferencia}</td>
		<td align="right" class="print blue">{ganancia}</td>
		<td align="right" class="print blue">{abonos}</td>
		<td align="right" class="print red">{devuelto}</td>
		<td align="right" class="print red">{rezago_final}</td>
	</tr>
	<!-- END BLOCK : dia -->
	<tr>
		<th align="right" class="print font10">Totales</th>
		<th align="right" class="print font10 red">{rezago_inicial}</th>
		<th align="right" class="print font10 blue">{partidas}</th>
		<th align="right" class="print font10 green">{total}</th>
		<th align="right" class="print font10 orange">{diferencia}</th>
		<th align="right" class="print font10">&nbsp;</th>
		<th align="right" class="print font10 blue">{abonos}</th>
		<th align="right" class="print font10 red">{devuelto}</th>
		<th align="right" class="print font10 red">{rezago_final}</th>
	</tr>
	<tr>
		<th align="right" class="print font10">Promedio</th>
		<th align="right" class="print font10 red">&nbsp;</th>
		<th align="right" class="print font10 blue">{ppartidas}</th>
		<th align="right" class="print font10 green">{ptotal}</th>
		<th align="right" class="print font10 orange">{pdiferencia}</th>
		<th align="right" class="print font10">&nbsp;</th>
		<th align="right" class="print font10 blue">{pabonos}</th>
		<th align="right" class="print font10 red">{pdevuelto}</th>
		<th align="right" class="print font10">&nbsp;</th>
	</tr>
	<tr>
		<td colspan="9" align="right" class="print font10">&nbsp;</td>
	</tr>
	<!-- START BLOCK : expendio -->
</table>
{salto} 
<!-- END BLOCK : reporte -->
<p align="center" class="noDisplay">
	<input name="cerrar" type="button" id="cerrar" value="Cerrar" />
</p>
</body>
</html>
