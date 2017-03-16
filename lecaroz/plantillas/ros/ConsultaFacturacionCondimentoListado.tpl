<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Auxiliar de Inventario</title>

<link href="../../styles/screen.css" rel="stylesheet" type="text/css" media="screen" />
<link href="../../styles/print.css" rel="stylesheet" type="text/css" media="print" />
<link href="../../styles/font-style.css" rel="stylesheet" type="text/css" />

<link href="styles/screen.css" rel="stylesheet" type="text/css" media="screen" />
<link href="styles/print.css" rel="stylesheet" type="text/css" media="print" />
<link href="styles/font-style.css" rel="stylesheet" type="text/css" />

<script type="text/javascript" src="jscripts/mootools/mootools-1.2-core.js"></script>
<script type="text/javascript" src="jscripts/mootools/mootools-1.2-more.js"></script>
<script type="text/javascript" src="jscripts/ros/ConsultaFacturacionCondimentoListado.js"></script>
</head>

<body>
<!-- START BLOCK : result -->
<table width="100%" align="center" class="encabezado" style="border-collapse:collapse;">
  <tr>
    <td>&nbsp;</td>
    <td align="center">Facturaci&oacute;n de Condimento  <br />
    {fecha1} al {fecha2} </td>
    <td>&nbsp;</td>
  </tr>
</table>
<br />
<table align="center" class="print">
  <tr>
    <th class="print" scope="col">Compa&ntilde;&iacute;a</th>
    <th class="print" scope="col">Fecha</th>
    <th class="print" scope="col">Kilos</th>
    <th class="print" scope="col">Precio</th>
    <th class="print" scope="col">Importe</th>
  </tr>
  <!-- START BLOCK : row -->
  <tr id="row">
    <td class="print">{num_cia} {nombre} </td>
    <td class="print">{fecha}</td>
    <td align="right" class="print">{kilos}</td>
    <td align="right" class="print">{precio}</td>
    <td align="right" class="print">{importe}</td>
  </tr>
  <!-- END BLOCK : row -->
  <tr>
    <th colspan="2" align="right" class="print">Totales</th>
    <th align="right" class="print">{kilos}</th>
    <th align="right" class="print">&nbsp;</th>
    <th align="right" class="print">{total}</th>
  </tr>
</table>
<!-- END BLOCK : result -->
<!-- START BLOCK : no_result -->
<table align="center" class="font font16 bold">
  <tr>
    <td>No hay resultados </td>
  </tr>
</table>
<!-- END BLOCK : no_result -->
<p align="center" class="noDisplay">
  <input name="cerrar" type="button" id="cerrar" value="Cerrar" />
</p>
</body>
</html>
