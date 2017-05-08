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
<script type="text/javascript" src="jscripts/fac/FacturasPendientesReporte.js"></script>
</head>

<body>
<!-- START BLOCK : reporte -->
<table width="98%" align="center" class="encabezado">
	<tr>
		<td width="100%" align="center">Reporte de Rentas</td>
	</tr>
</table>
<br />
<table align="center" class="print">
	<!-- START BLOCK : arrendador -->
	<tr>
		<th colspan="11" align="left" class="print font12">{arrendador} {nombre_arrendador}</th>
	</tr>
	<tr>
		<th class="print">Recibo</th>
		<th class="print">Arrendatario</th>
		<th class="print">Renta</th>
		<th class="print">Mantenimiento</th>
		<th class="print">Subtotal</th>
		<th class="print">I.V.A.</th>
		<th class="print">Agua</th>
		<th class="print">Retención<br />
		I.V.A.</th>
		<th class="print">Retención<br />
			I.S.R.</th>
		<th class="print">Total</th>
		<th class="print">Pagado</th>
	</tr>
	<!-- START BLOCK : mes -->
	<tr>
		<th colspan="11" align="left" class="print font10">{mes} {anio}</th>
	</tr>
	<!-- START BLOCK : row -->
	<tr id="row">
		<td align="right" class="print{status}">{num_fact}</td>
		<td class="print{status}">{arrendatario} {nombre_arrendatario}</td>
		<td align="right" class="print green">{renta}</td>
		<td align="right" class="print green">{mantenimiento}</td>
		<td align="right" class="print blue bold">{subtotal}</td>
		<td align="right" class="print blue">{iva}</td>
		<td align="right" class="print blue">{agua}</td>
		<td align="right" class="print red">{retencion_iva}</td>
		<td align="right" class="print red">{retencion_isr}</td>
		<td align="right" class="print blue bold">{total}</td>
		<td align="center" class="print green">{pagado}</td>
	</tr>
	<!-- END BLOCK : row -->
	<tr>
		<th colspan="9" align="right" class="print">Total mes</th>
		<th align="right" class="print">{total}</th>
		<th align="right" class="print">&nbsp;</th>
	</tr>
	<!-- END BLOCK : mes -->
	<tr>
		<th colspan="9" align="right" class="print">Total inmobiliaria</th>
		<th align="right" class="print font10">{total}</th>
		<th align="right" class="print">&nbsp;</th>
	</tr>
	<tr>
		<td colspan="11" align="right" class="print">&nbsp;</td>
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
