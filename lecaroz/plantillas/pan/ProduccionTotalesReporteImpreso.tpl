<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Reporte de producci&oacute;n</title>
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
<table width="98%" align="center" class="encabezado">
	<tr>
		<td width="100%" align="center">Reporte de producci&oacute;n del a&ntilde;o {anio}</td>
	</tr>
</table>
<br />
<table align="center" class="print">
	<!-- START BLOCK : cia -->
	<tr>
		<th colspan="14" align="left" class="print font12" scope="col">{num_cia} {nombre_cia}</th>
	</tr>
	<tr>
		<th class="print">Producto</th>
		<th class="print">Ene</th>
		<th class="print">Feb</th>
		<th class="print">Mar</th>
		<th class="print">Abr</th>
		<th class="print">May</th>
		<th class="print">Jun</th>
		<th class="print">Jul</th>
		<th class="print">Ago</th>
		<th class="print">Sep</th>
		<th class="print">Oct</th>
		<th class="print">Nov</th>
		<th class="print">Dic</th>
		<th class="print">Total</th>
	</tr>
	<!-- START BLOCK : row -->
	<tr>
		<td class="print">{cod} {producto}</td>
		<td align="right" class="print">{pro_1}</td>
		<td align="right" class="print">{pro_2}</td>
		<td align="right" class="print">{pro_3}</td>
		<td align="right" class="print">{pro_4}</td>
		<td align="right" class="print">{pro_5}</td>
		<td align="right" class="print">{pro_6}</td>
		<td align="right" class="print">{pro_7}</td>
		<td align="right" class="print">{pro_8}</td>
		<td align="right" class="print">{pro_9}</td>
		<td align="right" class="print">{pro_10}</td>
		<td align="right" class="print">{pro_11}</td>
		<td align="right" class="print">{pro_12}</td>
		<td align="right" class="print bold">{total}</td>
	</tr>
	<!-- END BLOCK : row -->
	<tr>
		<th align="right" class="print font10">Totales</th>
		<th align="right" class="print font10">{tot_1}</th>
		<th align="right" class="print font10">{tot_2}</th>
		<th align="right" class="print font10">{tot_3}</th>
		<th align="right" class="print font10">{tot_4}</th>
		<th align="right" class="print font10">{tot_5}</th>
		<th align="right" class="print font10">{tot_6}</th>
		<th align="right" class="print font10">{tot_7}</th>
		<th align="right" class="print font10">{tot_8}</th>
		<th align="right" class="print font10">{tot_9}</th>
		<th align="right" class="print font10">{tot_10}</th>
		<th align="right" class="print font10">{tot_11}</th>
		<th align="right" class="print font10">{tot_12}</th>
		<th align="right" class="print font10">{total}</th>
	</tr>
	<tr>
		<td colspan="14" class="print">&nbsp;</td>
	</tr>
	<!-- END BLOCK : cia -->
</table>
{salto} 
<p align="center" class="noDisplay">
	<input name="cerrar" type="button" id="cerrar" value="Cerrar" />
</p>
</body>
</html>
