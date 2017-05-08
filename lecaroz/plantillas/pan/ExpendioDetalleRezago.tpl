<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Reporte de movientos del expendio</title>
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
<table width="98%" align="center" class="encabezado">
	<tr>
		<td width="15%" valign="top">{num_cia}</td>
		<td width="70%" align="center" valign="top">{nombre_cia}<br />
			Reporte de movientos del expendio<br />
			{num_expendio} {nombre_expendio}</td>
		<td width="15%" align="right" valign="top">{num_cia}</td>
	</tr>
</table>
<br />
<table align="center" class="print">
	<tr>
		<th class="print">#</th>
		<th class="print">Fecha</th>
		<th class="print">Saldo<br />
			inicial</th>
		<th class="print">Reparto</th>
		<th class="print">Total</th>
		<th class="print">Diferencia</th>
		<th class="print">% ganancia</th>
		<th class="print">Abono</th>
		<th class="print">Devuelto</th>
		<th class="print">Saldo<br />
			final</th>
		<th class="print">&nbsp;</th>
	</tr>
	<!-- START BLOCK : row -->
	<tr>
		<td align="right" class="print bold">{num}</td>
		<td align="center" class="print">{fecha}</td>
		<td align="right" class="print red">{saldo_ini}</td>
		<td align="right" class="print blue">{reparto}</td>
		<td align="right" class="print green">{total}</td>
		<td align="right" class="print">{diferencia}</td>
		<td align="right" class="print">{ganancia}</td>
		<td align="right" class="print blue">{abono}</td>
		<td align="right" class="print orange">{devuelto}</td>
		<td align="right" class="print red">{saldo_fin}</td>
		<td align="right" class="print red"><input name="id[]" type="checkbox" id="id" value="{id}" /></td>
	</tr>
	<!-- END BLOCK : row -->
	<tr>
		<th colspan="3" align="right" class="print">Totales</th>
		<th align="right" class="print blue">{reparto}</th>
		<th align="right" class="print green">{total}</th>
		<th align="right" class="print">{diferencia}</th>
		<th align="right" class="print">&nbsp;</th>
		<th align="right" class="print blue">{abono}</th>
		<th align="right" class="print orange">{devuelto}</th>
		<th align="right" class="print">&nbsp;</th>
		<th align="right" class="print">&nbsp;</th>
	</tr>
	<tr>
		<td colspan="11" class="print">&nbsp;</td>
	</tr>
	<tr>
		<th colspan="5" align="left" class="print font12">Rezago al día</th>
		<th colspan="6" align="right" class="print font12">{rezago_al_dia}</th>
	</tr>
</table>
{salto} 
<p align="center" class="noDisplay">
	<input name="cerrar" type="button" id="cerrar" value="Cerrar" />
</p>
</body>
</html>
