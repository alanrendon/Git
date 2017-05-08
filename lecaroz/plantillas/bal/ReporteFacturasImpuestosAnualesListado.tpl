<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Reporte de impuestos de facturas anual</title>
<link href="styles/screen.css" rel="stylesheet" type="text/css" media="screen" />
<link href="styles/print.css" rel="stylesheet" type="text/css" media="print" />
<link href="styles/font-style.css" rel="stylesheet" type="text/css" />
<link href="/lecaroz/styles/screen.css" rel="stylesheet" type="text/css" media="screen" />
<link href="/lecaroz/styles/print.css" rel="stylesheet" type="text/css" media="print" />
<link href="/lecaroz/styles/font-style.css" rel="stylesheet" type="text/css" />
<link href="/lecaroz/styles/Tips.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="/lecaroz/jscripts/mootools1.4/mootools-core-1.4.5.js"></script>
<script type="text/javascript" src="/lecaroz/jscripts/mootools1.4/mootools-more-1.4.0.1.js"></script>
<script type="text/javascript" src="jscripts/bal/ReporteFacturasImpuestosAnualesListado.js"></script>
<style type="text/css">
#info_tip {
	border-collapse: collapse;
	border: solid 1px #000;
	background-color: #fff;
}

#info_tip td,
#info_tip th {
	border: solid 1px #000;
}

#info_tip th {
	background-color: #999;
}
</style>
</head>

<body>
<!-- START BLOCK : reporte -->
<table width="98%" align="center" class="encabezado">
	<tr>
		<td width="100%" align="center">Reporte de impuestos de facturas del a&ntilde;o {anio}</td>
	</tr>
</table>
<br />
<table align="center" class="print">
	<!-- START BLOCK : cia -->
	<tr>
		<th class="print left font12" colspan="15">{num_cia} {nombre_cia}</th>
	</tr>
	<tr>
		<th class="print">Concepto</th>
		<!-- START BLOCK : mes -->
		<th class="print">{mes}</th>
		<!-- END BLOCK : mes -->
		<th class="print">Total</th>
		<th class="print">Promedio</th>
	</tr>
	<!-- START BLOCK : concepto -->
	<tr>
		<td class="print bold">{concepto}</td>
		<!-- START BLOCK : importe -->
		<td align="right" class="print">{importe}</td>
		<!-- END BLOCK : importe -->
		<td align="right bold" class="print bold">{total}</td>
		<td align="right bold" class="print bold green">{promedio}</td>
	</tr>
	<!-- START BLOCK : concepto -->
	<tr>
		<td colspan="15" class="print">&nbsp;</td>
	</tr>
	<!-- END BLOCK : cia -->
</table>
{salto}
<!-- END BLOCK : reporte -->
<p align="center" class="noDisplay">
	<input name="cerrar" type="button" id="cerrar" value="Cerrar" />
</p>
</body>
</html>
