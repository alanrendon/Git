<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Comparativo Anual de Precios de Productos de Rosticer&iacute;a </title>
<link href="styles/screen.css" rel="stylesheet" type="text/css" media="screen" />
<link href="styles/print.css" rel="stylesheet" type="text/css" media="print" />
<link href="styles/font-style.css" rel="stylesheet" type="text/css" />
<link href="../../styles/screen.css" rel="stylesheet" type="text/css" media="screen" />
<link href="../../styles/print.css" rel="stylesheet" type="text/css" media="print" />
<link href="../../styles/font-style.css" rel="stylesheet" type="text/css" />

<script type="text/javascript" src="jscripts/mootools/mootools-1.2-core.js"></script>
<script type="text/javascript" src="jscripts/ros/PrecioPolloAnualReporte.js"></script>
</head>

<body>
<!-- START BLOCK : reporte -->
<table width="98%" align="center" class="encabezado">
  <tr>
    <td align="center">Comparativo Anual de Precios de Productos de Rosticer&iacute;a </td>
  </tr>
  <tr>
    <td align="center">{codmp} {nombre_mp} A&ntilde;o {anio} </td>
  </tr>
</table>
<br />
<table align="center" class="print">
  <tr>
    <th class="print">Compa&ntilde;&iacute;a</th>
    <th width="40" class="print">Ene</th>
    <th width="40" class="print">Feb</th>
    <th width="40" class="print">Mar</th>
    <th width="40" class="print">Abr</th>
    <th width="40" class="print">May</th>
    <th width="40" class="print">Jun</th>
    <th width="40" class="print">Jul</th>
    <th width="40" class="print">Ago</th>
    <th width="40" class="print">Sep</th>
    <th width="40" class="print">Oct</th>
    <th width="40" class="print">Nov</th>
    <th width="40" class="print">Dic</th>
  </tr>
  <!-- START BLOCK : row -->
  <tr id="row">
    <td class="print bold">{num_cia} {nombre_cia}</td>
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
