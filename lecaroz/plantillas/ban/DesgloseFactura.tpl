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
<script type="text/javascript" src="jscripts/ban/DesgloseFactura.js"></script>
</head>

<body>
<!-- START BLOCK : materia_prima -->
<table align="center" class="print">
  <tr>
    <th class="print" scope="col">Cantidad</th>
    <th class="print" scope="col">Producto</th>
    <th class="print" scope="col">Contenido</th>
    <th class="print" scope="col">Unidad</th>
    <th class="print" scope="col">Precio</th>
    <th class="print" scope="col">Importe</th>
    <th class="print" scope="col">Descuentos</th>
    <th class="print" scope="col">I.V.A.</th>
    <th class="print" scope="col">Total</th>
  </tr>
  <!-- START BLOCK : mp -->
  <tr>
    <td align="right" class="print">{cantidad}</td>
    <td class="print">{codmp} {nombre} </td>
    <td align="right" class="print">{contenido}</td>
    <td class="print">{unidad}</td>
    <td align="right" class="print">{precio}</td>
    <td align="right" class="print">{importe}</td>
    <td align="right" class="print">{descuentos}</td>
    <td align="right" class="print">{iva}</td>
    <td align="right" class="print">{total}</td>
  </tr>
  <!-- END BLOCK : mp -->
  <tr>
    <th colspan="8" align="right" class="print">Total Factura </th>
    <th align="right" class="print">{total}</th>
  </tr>
</table>
<!-- END BLOCK : materia_prima -->
<!-- START BLOCK : gas -->
<table align="center" class="print">
  <tr>
    <th class="print" scope="col">Litros</th>
    <th class="print" scope="col">Precio</th>
    <th class="print" scope="col">Importe</th>
  </tr>
  <!-- START BLOCK : tanque -->
  <tr>
    <td class="print">{litros}</td>
    <td class="print">{precio}</td>
    <td class="print">{importe}</td>
  </tr>
  <!-- END BLOCK : tanque -->
  <tr>
    <th colspan="2" align="right" class="print">Total Factura </th>
    <th class="print">{total}</th>
  </tr>
</table>
<!-- END BLOCK : gas -->
<p align="center" class="noDisplay">
  <input name="cerrar" type="button" id="cerrar" value="Cerrar" />
</p>
</body>
</html>
