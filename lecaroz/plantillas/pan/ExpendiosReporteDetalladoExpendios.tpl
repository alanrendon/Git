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
<script type="text/javascript" src="jscripts/pan/ExpendiosReporteDetalladoExpendios.js"></script>
</head>

<body>
<!-- START BLOCK : reporte -->
<table width="98%" align="center" class="encabezado">
	<tr>
		<td width="100%" align="center"><span style="float:left;">{num_cia}</span><span style="float:right;">{num_cia}</span>{nombre_cia}<br />
		Reporte	de	expendios<br />
		del	{fecha1} al {fecha2}<br /></td>
	</tr>
</table>
<br />
<table width="98%" align="center" class="print">
	<tr>
		<th class="print">Expendio</th>
		<th class="print">Rezago<br />
			inicial</th>
		<th class="print">Partidas</th>
		<th class="print">Total</th>
		<th class="print">Diferencia</th>
		<th class="print">%</th>
		<th class="print">Abonos</th>
		<th class="print">Devuelto</th>
		<th class="print">%</th>
		<th class="print">Rezago<br />
			final</th>
		<th class="print">Diferencia<br />
			de rezago</th>
		<th class="print">Abonos<br />
			promedio</th>
		<th class="print">Días</th>
		<th class="print">Diferencia+<br />Devoluci&oacute;n</th>
		<th class="print">%Diferencia+<br />%Devoluci&oacute;n</th>
	</tr>
	<!-- START BLOCK : expendio -->
	<tr>
		<td class="print">{num_exp} {nombre_exp}</td>
		<td align="right" class="print red">{rezago_inicial}</td>
		<td align="right" class="print blue">{partidas}</td>
		<td align="right" class="print green">{total}</td>
		<td align="right" class="print orange">{diferencia}</td>
		<td align="right" class="print blue">{ganancia}</td>
		<td align="right" class="print blue">{abonos}</td>
		<td align="right" class="print red">{devuelto}</td>
		<td align="right" class="print red">{porcentaje_devuelto}</td>
		<td align="right" class="print red">{rezago_final}</td>
		<td align="right" class="print{diferencia_color}">{diferencia_rezago}</td>
		<td align="right" class="print blue">{abonos_promedio}</td>
		<td align="right" class="print">{dias}</td>
		<td align="right" class="print">{diferencia_devolucion}</td>
		<td align="right" class="print">{pdiferencia_pdevolucion}</td>
	</tr>
	<!-- END BLOCK : expendio -->
	<tr>
		<th align="right" class="print font10">Totales</th>
		<th align="right" class="print font10 red">{rezago_inicial}</th>
		<th align="right" class="print font10 blue">{partidas}</th>
		<th align="right" class="print font10 green">{total}</th>
		<th align="right" class="print font10 orange">{diferencia}</th>
		<th align="right" class="print font10">{ganancia}</th>
		<th align="right" class="print font10 blue">{abonos}</th>
		<th align="right" class="print font10 red">{devuelto}</th>
		<th align="right" class="print font10 red">{porcentaje_devuelto}</th>
		<th align="right" class="print font10 red">{rezago_final}</th>
		<th colspan="3" align="right" class="print font10">&nbsp;</th>
		<th align="right" class="print font10">{diferencia_devolucion}</th>
		<th align="right" class="print font10">{pdiferencia_pdevolucion}</th>
	</tr>
</table>
{salto} 
<!-- END BLOCK : reporte -->
<p align="center" class="noDisplay">
	<input name="cerrar" type="button" id="cerrar" value="Cerrar" />
</p>
</body>
</html>
