<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Depósitos de renta</title>
<link href="styles/screen.css" rel="stylesheet" type="text/css" media="screen" />
<link href="styles/print.css" rel="stylesheet" type="text/css" media="print" />
<link href="styles/font-style.css" rel="stylesheet" type="text/css" />
<link href="/lecaroz/styles/screen.css" rel="stylesheet" type="text/css" media="screen" />
<link href="/lecaroz/styles/print.css" rel="stylesheet" type="text/css" media="print" />
<link href="/lecaroz/styles/font-style.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="jscripts/mootools/mootools-1.2-core.js"></script>
<script type="text/javascript" src="jscripts/ban/RentasDepositosReporte.js"></script>
</head>

<body>
<!-- START BLOCK : reporte -->
<table width="98%" align="center" class="encabezado">
	<tr>
		<td align="center">Oficinas Administrativas Mollendo, S. de R.L. de C.V.</td>
	</tr>
	<tr>
		<td align="center">Depósitos de renta capturados el {fecha}</td>
	</tr>
</table>
<br />
<table align="center" class="print">
	<tr>
		<th class="print" scope="col">Compañía</th>
		<th class="print" scope="col">Cuenta</th>
		<th class="print" scope="col">Fecha</th>
		<th class="print" scope="col">Concepto</th>
		<th class="print" scope="col">Importe</th>
	</tr>
	<!-- START BLOCK : row -->
	<tr>
		<td class="print">{num_cia} {nombre_cia}</td>
		<td align="center" class="print">{cuenta}</td>
		<td align="center" class="print">{fecha}</td>
		<td class="print">{concepto}</td>
		<td align="right" class="print">{importe}</td>
	</tr>
	<!-- END BLOCK : row -->
	<tr>
		<th colspan="4" align="right" class="print">Total</th>
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
