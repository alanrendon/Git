<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Comparativo de rayas y precios de producción</title>
<link href="/lecaroz/styles/screen.css" rel="stylesheet" type="text/css" media="screen" />
<link href="/lecaroz/styles/print.css" rel="stylesheet" type="text/css" media="print" />
<link href="/lecaroz/styles/font-style.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="jscripts/mootools/mootools-1.2-core.js"></script>
<script type="text/javascript" src="jscripts/mootools/mootools-1.2-more.js"></script>
<script type="text/javascript" src="jscripts/adm/ReporteAccionistas.js"></script>
</head>

<body>
<!-- START BLOCK : reporte -->
<table width="98%" align="center" class="print">
	<tr>
		<th width="28%" class="print" scope="col">Producto</th>
		<th width="9%" colspan="2" class="print" scope="col">{num_cia0} {nombre_cia0}</th>
		<th width="9%" colspan="2" class="print" scope="col">{num_cia1} {nombre_cia1}</th>
		<th width="9%" colspan="2" class="print" scope="col">{num_cia2} {nombre_cia2}</th>
		<th width="9%" colspan="2" class="print" scope="col">{num_cia3} {nombre_cia3}</th>
		<th width="9%" colspan="2" class="print" scope="col">{num_cia4} {nombre_cia4}</th>
		<th width="9%" colspan="2" class="print" scope="col">{num_cia5} {nombre_cia5}</th>
		<th width="9%" colspan="2" class="print" scope="col">{num_cia6} {nombre_cia6}</th>
	</tr>
	<!-- START BLOCK : turno -->
	<tr>
		<th align="left" class="print" scope="col">{nombre_turno}</th>
		<th class="print" scope="col">Raya</th>
		<th class="print" scope="col">Venta</th>
		<th class="print" scope="col">Raya</th>
		<th class="print" scope="col">Venta</th>
		<th class="print" scope="col">Raya</th>
		<th class="print" scope="col">Venta</th>
		<th class="print" scope="col">Raya</th>
		<th class="print" scope="col">Venta</th>
		<th class="print" scope="col">Raya</th>
		<th class="print" scope="col">Venta</th>
		<th class="print" scope="col">Raya</th>
		<th class="print" scope="col">Venta</th>
		<th class="print" scope="col">Raya</th>
		<th class="print" scope="col">Venta</th>
	</tr>
	<!-- START BLOCK : row -->
	<tr id="row">
		<td align="left" class="print">{producto} {nombre_producto}</td>
		<td align="right" class="print red">{raya0}</td>
		<td align="right" class="print blue">{venta0}</td>
		<td align="right" class="print red">{raya1}</td>
		<td align="right" class="print blue">{venta1}</td>
		<td align="right" class="print red">{raya2}</td>
		<td align="right" class="print blue">{venta2}</td>
		<td align="right" class="print red">{raya3}</td>
		<td align="right" class="print blue">{venta3}</td>
		<td align="right" class="print red">{raya4}</td>
		<td align="right" class="print blue">{venta4}</td>
		<td align="right" class="print red">{raya5}</td>
		<td align="right" class="print blue">{venta5}</td>
		<td align="right" class="print red">{raya6}</td>
		<td align="right" class="print blue">{venta6}</td>
	</tr>
	<!-- END BLOCK : row --> 
	<!-- END BLOCK : turno -->
</table>
<br style="page-break-after:always;">
<!-- END BLOCK : reporte -->
<p align="center" class="noDisplay">
	<input name="cerrar" type="button" id="cerrar" value="Cerrar" />
</p>
</body>
</html>
