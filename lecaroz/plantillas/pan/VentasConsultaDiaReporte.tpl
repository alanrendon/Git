<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Reporte de Facturas Electr&oacute;nicas</title>
<link href="/lecaroz/styles/screen.css" rel="stylesheet" type="text/css" media="screen" />
<link href="/lecaroz/styles/print.css" rel="stylesheet" type="text/css" media="print" />
<link href="/lecaroz/styles/font-style.css" rel="stylesheet" type="text/css" />

<script type="text/javascript" src="jscripts/mootools/mootools-1.2-core.js"></script>
<script type="text/javascript" src="jscripts/pan/ProduccionConsultaPiezasDiaReporte.js"></script>
</head>

<body>
<table width="98%" align="center" class="encabezado">
  <tr>
    <td align="center">VENTAS DEL DIA {fecha}</td>
  </tr>
</table>
<br />
<table align="center" class="print">
	<tr>
		<th class="print" scope="col">Compañía</th>
		<th class="print" scope="col">Venta</th>
		<th class="print" scope="col">Pastel</th>
	</tr>
	<!-- START BLOCK : row -->
	<tr id="row">
		<td class="print">{num_cia} {nombre_cia}</td>
		<td align="right" class="print blue">{venta}</td>
		<td align="right" class="print green">{pastel}</td>
	</tr>
	<!-- END BLOCK : row -->
</table>
<p align="center" class="noDisplay">
	<input name="cerrar" type="button" id="cerrar" value="Cerrar" />
</p>
</body>
</html>
