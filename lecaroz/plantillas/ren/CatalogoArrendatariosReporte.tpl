<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Contratos de arrendamiento vencidos</title>
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
		<td width="100%" align="center">Reporte de arrendatarios</td>
	</tr>
</table>
<br />
<table width="98%" align="center" class="print">
	<!-- START BLOCK : arrendador -->
	<tr>
		<th colspan="13" align="left" class="print font12" scope="col">{arrendador} {nombre_arrendador}</th>
	</tr>
	<tr>
		<th class="print">#</th>
		<th class="print">Alias</th>
		<th class="print">Arrendatario</th>
		<th class="print">R.F.C.</th>
		<th class="print">Giro</th>
		<th class="print">Periodo de arrendamiento</th>
		<th class="print">Renta</th>
		<th class="print">Mantenimiento</th>
		<th class="print">Subtotal</th>
		<th class="print">I.V.A.</th>
		<th class="print">Retención<br />
			I.V.A.</th>
		<th class="print">Retención<br />
			I.S.R.</th>
		<th class="print">Total</th>
	</tr>
	<!-- START BLOCK : arrendatario -->
	<tr>
		<td align="right" class="print">{arrendatario}</td>
		<td class="print">{alias_arrendatario}</td>
		<td class="print">{nombre_arrendatario}</td>
		<td nowrap="nowrap" class="print">{rfc}</td>
		<td nowrap="nowrap" class="print">{giro}</td>
		<td align="center" nowrap="nowrap" class="print green">{periodo_arrendamiento}</td>
		<td align="right" class="print blue">{renta}</td>
		<td align="right" class="print blue">{mantenimiento}</td>
		<td align="right" class="print bold blue">{subtotal}</td>
		<td align="right" class="print blue">{iva}</td>
		<td align="right" class="print red">{retencion_iva}</td>
		<td align="right" class="print red">{retencion_isr}</td>
		<td align="right" class="print bold blue">{total}</td>
	</tr>
	<!-- END BLOCK : arrendatario -->
	<tr>
		<th colspan="6" align="right" class="print">Totales</th>
		<th align="right" class="print blue">{renta}</th>
		<th align="right" class="print blue">{mantenimiento}</th>
		<th align="right" class="print bold blue">{subtotal}</th>
		<th align="right" class="print blue">{iva}</th>
		<th align="right" class="print red">{retencion_iva}</th>
		<th align="right" class="print red">{retencion_isr}</th>
		<th align="right" class="print bold blue">{total}</th>
	</tr>
	<tr>
		<td colspan="13" class="print">&nbsp;</td>
	</tr>
	<!-- END BLOCK : arrendador -->
</table>
{salto} 
<!-- END BLOCK : reporte -->
<p align="center" class="noDisplay">
	<input name="cerrar" type="button" id="cerrar" value="Cerrar" />
</p>
</body>
</html>
