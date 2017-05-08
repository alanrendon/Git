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
<script type="text/javascript" src="jscripts/ped/ReportePedidos.js"></script>

</head>

<body>
<!-- START BLOCK : reporte -->
<table width="98%" align="center" class="encabezado">
  <tr>
    <td width="15%" class="font14">{num_cia}</td>
    <td width="70%" align="center" class="font14">{nombre_cia}</td>
    <td width="15%" align="right" class="font14">{num_cia}</td>
  </tr>
  <tr>
    <td class="font8">&nbsp;</td>
    <td align="center">Pedidos al {dia} de {mes} de {anio}<br />
    (d&iacute;as pedidos: {dias})</td>
    <td align="right" class="font8 bold">FOLIO: {folio}</td>
  </tr>
</table>
<br />
<table width="99%" align="center" class="print">
  <tr>
    <th colspan="2" class="print" scope="col">Producto</th>
    <th colspan="2" class="print" scope="col">Pedido</th>
    <th colspan="2" class="print" scope="col">Entregar</th>
    <th class="print" scope="col">Proveedor</th>
    <th class="print" scope="col">Tel&eacute;fono 1</th>
    <th class="print" scope="col">Tel&eacute;fono 2</th>
    <th class="print" scope="col">Email 1</th>
    <th class="print" scope="col">Email 2</th>
    <th class="print" scope="col">Email 3</th>
    </tr>
  <!-- START BLOCK : row -->
  <tr id="row">
    <td align="right" class="print">{codmp}</td>
    <td nowrap="nowrap" class="print">{nombre_mp}</td>
    <td align="right" class="print blue">{pedido}</td>
    <td align="left" class="print blue">{unidad}</td>
    <td align="right" class="print {color}">{entregar}</td>
    <td nowrap="nowrap" class="print {color}">{presentacion}</td>
    <td nowrap="nowrap" class="print">{num_pro} {nombre_pro}</td>
    <td nowrap="nowrap" class="print">{telefono1}</td>
    <td nowrap="nowrap" class="print">{telefono2}</td>
    <td class="print">{email1}</td>
    <td class="print">{email2}</td>
    <td class="print">{email3}</td>
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
