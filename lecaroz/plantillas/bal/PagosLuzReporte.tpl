<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Reporte de Facturas Electr&oacute;nicas</title>
<link href="/lecaroz/styles/screen.css" rel="stylesheet" type="text/css" media="screen" />
<link href="/lecaroz/styles/print.css" rel="stylesheet" type="text/css" media="print" />
<link href="/lecaroz/styles/font-style.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="jscripts/mootools/mootools-1.2-core.js"></script>
<script type="text/javascript" src="jscripts/bal/PagosLuzReporte.js"></script>
</head>

<body>
<!-- START BLOCK : reporte -->
<table width="98%" align="center" class="encabezado">
  <tr>
    <td align="left">{num_cia}</td>
	<td align="center">{nombre_cia}<br />
		Reporte de pagos de luz</td>
    <td align="right">{num_cia}</td>
  </tr>
</table>
<br />
<table width="98%" align="center" class="print">
	<tr>
		<th class="print" scope="col">A&ntilde;o</th>
		<th class="print" scope="col">Ene</th>
		<th class="print" scope="col">Feb</th>
		<th class="print" scope="col">Mar</th>
		<th class="print" scope="col">Abr</th>
		<th class="print" scope="col">May</th>
		<th class="print" scope="col">Jun</th>
		<th class="print" scope="col">Jul</th>
		<th class="print" scope="col">Ago</th>
		<th class="print" scope="col">Sep</th>
		<th class="print" scope="col">Oct</th>
		<th class="print" scope="col">Nov</th>
		<th class="print" scope="col">Dic</th>
		<th class="print" scope="col">Total</th>
		<th class="print" scope="col">Promedio</th>
	</tr>
	<!-- START BLOCK : row -->
	<tr>
		<td align="center" class="print bold">{anio}</td>
		<td align="right" class="print">{1}</td>
		<td align="right" class="print">{2}</td>
		<td align="right" class="print">{3}</td>
		<td align="right" class="print">{4}</td>
		<td align="right" class="print">{5}</td>
		<td align="right" class="print">{6}</td>
		<td align="right" class="print">{7}</td>
		<td align="right" class="print">{8}</td>
		<td align="right" class="print">{9}</td>
		<td align="right" class="print">{10}</td>
		<td align="right" class="print">{11}</td>
		<td align="right" class="print">{12}</td>
		<td align="right" class="print bold">{total}</td>
		<td align="right" class="print bold">{promedio}</td>
	</tr>
	<!-- END BLOCK : row -->
	<tr>
		<th align="right" class="print">Total mes{1}</th>
		<th align="right" class="print">{1}</th>
		<th align="right" class="print">{2}</th>
		<th align="right" class="print">{3}</th>
		<th align="right" class="print">{4}</th>
		<th align="right" class="print">{5}</th>
		<th align="right" class="print">{6}</th>
		<th align="right" class="print">{7}</th>
		<th align="right" class="print">{8}</th>
		<th align="right" class="print">{9}</th>
		<th align="right" class="print">{10}</th>
		<th align="right" class="print">{11}</th>
		<th align="right" class="print">{12}</th>
		<th align="right" class="print">{total}</th>
		<th align="right" class="print">&nbsp;</th>
	</tr>
</table>
{salto} 
<!-- END BLOCK : reporte -->
<p align="center" class="noDisplay">
  <input name="cerrar" type="button" id="cerrar" value="Cerrar" />
</p>
</body>
</html>
