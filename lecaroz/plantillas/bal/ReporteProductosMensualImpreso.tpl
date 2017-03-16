<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Reporte de surtido de productos mensual</title>
<link href="styles/screen.css" rel="stylesheet" type="text/css" media="screen" />
<link href="styles/print.css" rel="stylesheet" type="text/css" media="print" />
<link href="styles/font-style.css" rel="stylesheet" type="text/css" />
<link href="/lecaroz/styles/screen.css" rel="stylesheet" type="text/css" media="screen" />
<link href="/lecaroz/styles/print.css" rel="stylesheet" type="text/css" media="print" />
<link href="/lecaroz/styles/font-style.css" rel="stylesheet" type="text/css" />
<link href="/lecaroz/styles/Tips.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="/lecaroz/jscripts/mootools1.4/mootools-core-1.4.5.js"></script>
<script type="text/javascript" src="/lecaroz/jscripts/mootools1.4/mootools-more-1.4.0.1.js"></script>
<script type="text/javascript" src="jscripts/bal/ReporteProductosMensualImpreso.js"></script>
<style type="text/css">
.info_tip {
	border-collapse: collapse;
	border: solid 1px #000;
	background-color: #fff;
}

.info_tip td,
.info_tip th {
	border: solid 1px #000;
}

.info_tip th {
	background-color: #999;
}
</style>
</head>

<body>
<!-- START BLOCK : reporte -->
<table width="98%" align="center" class="encabezado">
	<tr>
		<td width="100%" align="center">Reporte de surtido de {codmp} {producto} de {mes} de {anio}</td>
	</tr>
</table>
<br />
<table align="center" class="print">
	<tr>
		<th class="print">Compa&ntilde;&iacute;a</th>
		<!-- START BLOCK : dia_th -->
		<th class="print">{dia_mes}<br />{dia_semana}</th>
		<!-- END BLOCK : dia_th -->
		<th class="print">Total</th>
	</tr>
	<!-- START BLOCK : cia -->
	<tr>
		<td class="print" nowrap><strong>{num_cia} {nombre_cia}</strong></td>
		<!-- START BLOCK : dia_td -->
		<td class="print" align="right">{cantidad}</td>
		<!-- END BLOCK : dia_td -->
		<td class="print" align="right"><strong>{total_cia}</strong></td>
	</tr>
	<!-- END BLOCK : cia -->
	<tr>
		<th class="print" align="right">Totales</th>
		<!-- START BLOCK : total_dia -->
		<th class="print" align="right">{total_dia}</th>
		<!-- END BLOCK : total_dia -->
		<th class="print" align="right">{gran_total}</th>
	</tr>
</table>
{salto}
<!-- END BLOCK : reporte -->
<p align="center" class="noDisplay">
	<input name="cerrar" type="button" id="cerrar" value="Cerrar" />
</p>
</body>
</html>
