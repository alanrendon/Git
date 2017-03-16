<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Asignaci&oacute;n de productos por proveedor</title>
<link href="/lecaroz/styles/screen.css" rel="stylesheet" type="text/css" media="screen" />
<link href="/lecaroz/styles/print.css" rel="stylesheet" type="text/css" media="print" />
<link href="/lecaroz/styles/font-style.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="jscripts/mootools/mootools-1.2-core.js"></script>
<script type="text/javascript" src="jscripts/mootools/extensiones.js"></script>
<script type="text/javascript" src="jscripts/ped/DistribucionProductosProveedoresReporte.js"></script>
</head>

<body>
<!-- START BLOCK : reporte1 -->
<table width="98%" align="center" class="encabezado">
	<tr>
		<td width="15%" class="font14">{codmp}</td>
		<td width="70%" align="center" class="font14">{nombre_mp}</td>
		<td width="15%" align="right" class="font14">{codmp}</td>
	</tr>
	<tr>
		<td class="font8">&nbsp;</td>
		<td align="center">Asignaci&oacute;n de productos por proveedor</td>
		<td align="right" class="font8 bold">&nbsp;</td>
	</tr>
</table>
<br />
<table align="center" class="print">
	<tr>
		<th colspan="2" class="print" scope="col">Compa&ntilde;&iacute;a</th>
		<th class="print" scope="col">Distribuci&oacute;n</th>
	</tr>
	<!-- START BLOCK : row1 -->
	<tr id="row">
		<td align="right" class="print">{num_cia}</td>
		<td class="print">{nombre_cia}</td>
		<td class="print">{distribucion}</td>
	</tr>
	<!-- END BLOCK : row1 -->
</table>
<br style="page-break-after:always;" />
<!-- END BLOCK : reporte1 -->
<!-- START BLOCK : reporte2 -->
<table width="98%" align="center" class="encabezado">
	<tr>
		<td width="15%" class="font14">{num_cia}</td>
		<td width="70%" align="center" class="font14">{nombre_cia}</td>
		<td width="15%" align="right" class="font14">{num_cia}</td>
	</tr>
	<tr>
		<td class="font8">&nbsp;</td>
		<td align="center">Asignaci&oacute;n de productos por proveedor</td>
		<td align="right" class="font8 bold">&nbsp;</td>
	</tr>
</table>
<br />
<table align="center" class="print">
	<tr>
		<th colspan="2" class="print" scope="col">Producto</th>
		<th class="print" scope="col">Distribuci&oacute;n</th>
	</tr>
	<!-- START BLOCK : row2 -->
	<tr id="row">
		<td align="right" class="print">{codmp}</td>
		<td class="print">{nombre_mp}</td>
		<td class="print">{distribucion}</td>
	</tr>
	<!-- END BLOCK : row2 -->
</table>
<br style="page-break-after:always;" />
<!-- END BLOCK : reporte2 -->
<p align="center" class="noDisplay">
	<input name="cerrar" type="button" id="cerrar" value="Cerrar" />
</p>
</body>
</html>
