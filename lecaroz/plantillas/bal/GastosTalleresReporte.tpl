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
<script type="text/javascript" src="/lecaroz/jscripts/mootools1.4/mootools-core-1.4.5.js"></script>
<script type="text/javascript" src="jscripts/bal/GastosTalleresReporte.js"></script>
</head>

<body>
<!-- START BLOCK : reporte -->
<table width="98%" align="center" class="encabezado">
	<tr>
		<td width="100%" align="center">Reporte de gastos de talleres<br />
		del {fecha1} al {fecha2}</td>
	</tr>
</table>
<br />
<table align="center" class="print">
	<!-- START BLOCK : cia -->
	<tr>
		<th colspan="6" align="left" class="print font12" scope="col">{num_cia} {nombre_cia}</th>
	</tr>
	<tr>
		<th class="print" scope="col">Proveedor</th>
		<th class="print" scope="col">Factura</th>
		<th class="print" scope="col">Fecha</th>
		<th class="print" scope="col">Concepto</th>
		<th class="print" scope="col">Gasto</th>
		<th class="print" scope="col">Importe</th>
	</tr>
	<!-- START BLOCK : row -->
	<tr>
		<td class="print">{num_pro} {nombre_pro}</td>
		<td class="print purple">{factura}</td>
		<td align="center" class="print orange">{fecha}</td>
		<td class="print">{concepto}</td>
		<td class="print green">{gasto} {descripcion}</td>
		<td align="right" class="print blue">{importe}</td>
	</tr>
	<!-- END BLOCK : row -->
	<tr>
		<th colspan="5" align="right" class="print font10">Total</th>
		<th align="right" class="print font10">{total}</th>
	</tr>
	<tr>
		<td colspan="6" align="right" class="print">&nbsp;</td>
	</tr>
	<!-- END BLOCK : cia -->
	<tr>
		<th colspan="5" align="right" class="print font12">Gran total</th>
		<th align="right" class="print font12">{total}</th>
	</tr>
</table>
 {salto} 
<!-- END BLOCK : reporte -->
<p align="center" class="noDisplay">
	<input name="cerrar" type="button" id="cerrar" value="Cerrar" />
</p>
</body>
</html>
