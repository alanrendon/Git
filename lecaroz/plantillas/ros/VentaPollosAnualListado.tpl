<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Venta de Pollos</title>
<link href="../../styles/screen.css" rel="stylesheet" type="text/css" media="screen" />
<link href="../../styles/print.css" rel="stylesheet" type="text/css" media="print" />
<link href="../../styles/font-style.css" rel="stylesheet" type="text/css" />
<link href="styles/screen.css" rel="stylesheet" type="text/css" media="screen" />
<link href="styles/print.css" rel="stylesheet" type="text/css" media="print" />
<link href="styles/font-style.css" rel="stylesheet" type="text/css" />

<script type="text/javascript" src="jscripts/mootools/mootools-1.2-core.js"></script>
<script type="text/javascript" src="jscripts/ros/VentaPollosAnualListado.js"></script>
</head>

<body>
<!-- START BLOCK : listado -->
<table width="100%" class="encabezado">
  <tr>
    <td align="center" class="font12">Venta de Pollos{anio} </td>
  </tr>
</table>
<br />
<table align="center" class="print">
  <tr>
    <th class="print" scope="col">Compa&ntilde;&iacute;a</th>
    <!-- START BLOCK : titulo -->
	<th class="print" scope="col">{titulo}</th>
	<!-- END BLOCK : titulo -->
  </tr>
  <!-- START BLOCK : fila -->
  <tr>
    <td class="print">{num_cia} {nombre} </td>
	<!-- START BLOCK : valor -->
    <td align="right" class="print">{valor}</td>
	<!-- END BLOCK : valor -->
  </tr>
  <!-- END BLOCK : fila -->
  <!-- START BLOCK : totales -->
  <tr>
    <th align="right" class="print">Totales</th>
	<!-- START BLOCK : total -->
    <th align="right" class="print font10">{total}</th>
	<!-- END BLOCK : total -->
  </tr>
  <!-- END BLOCK : totales -->
</table>
{salto}
<!-- END BLOCK : listado -->
<p align="center" class="noDisplay">
  <input name="cerrar" type="button" id="cerrar" value="Cerrar" />
</p>
</body>
</html>
