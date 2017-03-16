<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Reporte de Facturas Electr&oacute;nicas</title>
<link href="styles/screen.css" rel="stylesheet" type="text/css" media="screen" />
<link href="styles/print.css" rel="stylesheet" type="text/css" media="print" />
<link href="styles/font-style.css" rel="stylesheet" type="text/css" />
<link href="../../styles/screen.css" rel="stylesheet" type="text/css" media="screen" />
<link href="../../styles/print.css" rel="stylesheet" type="text/css" media="print" />
<link href="../../styles/font-style.css" rel="stylesheet" type="text/css" />

<script type="text/javascript" src="jscripts/mootools/mootools-1.2-core.js"></script>
<script type="text/javascript" src="jscripts/fac/FacturasElectronicasConsultaReporte.js"></script>
</head>

<body>
<table width="98%" align="center" class="encabezado">
  <tr>
    <td align="center">Reporte de Facturas Electr&oacute;nicas </td>
  </tr>
</table>
<br />
<table align="center" class="print">
  <!-- START BLOCK : emisor -->
  <tr>
    <th colspan="7" align="left" class="print font14" scope="col">{emisor} {nombre_emisor} </th>
  </tr>
  <tr>
    <th class="print">Folio</th>
    <th class="print">Fecha</th>
    <th class="print">Pagada</th>
    <th class="print">Cliente</th>
    <th class="print">Importe</th>
    <th class="print">I.V.A.</th>
    <th class="print">Total</th>
  </tr>
  <!-- START BLOCK : factura -->
  <tr id="row">
    <td align="right" class="print">{folio}</td>
    <td align="center" class="print blue">{fecha}</td>
    <td align="center" class="print orange">{fecha_pago}</td>
    <td class="print">{nombre_cliente}</td>
    <td align="right" class="print green">{importe}</td>
    <td align="right" class="print red">{iva}</td>
    <td align="right" class="print blue">{total}</td>
  </tr>
  <!-- END BLOCK : factura -->
  <tr style="border-bottom:double 5px #000;">
    <th colspan="6" align="right" class="print font10">Total </th>
    <th align="right" class="print font10">{total}</th>
  </tr>
  <tr>
    <td colspan="7" class="print">&nbsp;</td>
  </tr>
  <!-- END BLOCK : emisor -->
</table>
<p align="center" class="noDisplay">
  <input name="cerrar" type="button" id="cerrar" value="Cerrar" />
</p>
</body>
</html>
