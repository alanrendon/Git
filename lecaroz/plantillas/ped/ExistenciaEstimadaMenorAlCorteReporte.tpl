<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Pedidos</title>
<link href="/lecaroz/styles/screen.css" rel="stylesheet" type="text/css" media="screen" />
<link href="/lecaroz/styles/print.css" rel="stylesheet" type="text/css" media="print" />
<link href="/lecaroz/styles/font-style.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="jscripts/mootools/mootools-1.2-core.js"></script>
<script type="text/javascript" src="jscripts/mootools/extensiones.js"></script>
<script type="text/javascript" src="jscripts/ped/ExistenciaEstimadaMenorAlCorteReporte.js"></script>
</head>

<body>
<!-- START BLOCK : reporte -->
<table width="98%" align="center" class="encabezado">
	<tr>
		<td width="15%" class="font14">{codmp}</td>
		<td width="70%" align="center" class="font14">{nombre_mp}</td>
		<td width="15%" align="right" class="font14">{codmp}</td>
	</tr>
	<tr>
		<td class="font8">&nbsp;</td>
		<td align="center">Existencia estimada menor a {dias}</td>
		<td align="right" class="font8 bold">&nbsp;</td>
	</tr>
</table>
<br />
<table align="center" class="print">
	<tr>
		<th colspan="2" class="print">Compa&ntilde;&iacute;a</th>
		<th class="print">Existencia</th>
		<th class="print">Consumo<br /></th>
		<th class="print">&Uacute;ltimo d&iacute;a<br />
			de consumo</th>
		<th class="print">Promedio</th>
		<th class="print">D&iacute;as</th>
		<th style="border-top:none; border-bottom:none;">&nbsp;</th>
		<th class="print">&Uacute;ltimo<br>proveedor</th>
		<th class="print">&Uacute;ltima<br>compra</th>
	</tr>
	<!-- START BLOCK : row -->
	<tr id="row">
		<td align="right" class="print">{num_cia}</td>
		<td class="print">{nombre_cia}</td>
		<td align="right" class="print blue">{existencia}</td>
		<td align="right" class="print red">{consumo}</td>
		<td align="center" class="print orange">{fecha}</td>
		<td align="right" class="print red">{promedio}</td>
		<td align="center" class="print red">{dias}</td>
		<td style="border-top:none; border-bottom:none;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
		<td class="print">{num_pro} {nombre_pro}</td>
		<td align="center" class="print">{ultima_compra}</td>
	</tr>
	<!-- END BLOCK : row -->
</table>
{salto}
<!-- END BLOCK : reporte -->
<p align="center" class="noDisplay">
	<input name="cerrar" type="button" id="cerrar" value="Cerrar" />
</p>
</body>
</html>
