<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Untitled Document</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="./styles/impresion.css" rel="stylesheet" type="text/css">
</head>

<body>
<!-- START BLOCK : listado -->
<table width="100%">
  <tr>
    <td>&nbsp;</td>
    <td class="print_encabezado" align="center">Oficinas Administrativas Mollendo S. de R.L. y C.V. </td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td width="20%">&nbsp;</td>
    <td width="60%" class="print_encabezado" align="center">Totales de Aguinaldos </td>
    <td width="20%">&nbsp;</td>
  </tr>
</table>
<br>
<table width="100%" align="center" class="print">
  <tr>
    <th width="5%" class="print" scope="col" style="font-size:10pt">Cia.</th>
    <th width="65%" class="print" scope="col" style="font-size:10pt">Nombre</th>
    <th width="15%" class="print" scope="col" style="font-size:10pt">No. Empleados </th>
    <th width="15%" class="print" scope="col" style="font-size:10pt">Total Aguinaldos </th>
  </tr>
  <!-- START BLOCK : fila -->
  <tr>
    <td class="rprint" style="font-size:10pt">{num_cia}</td>
    <td class="vprint" style="font-size:10pt">{nombre_cia}</td>
    <td class="print" style="font-size:10pt">{num_empleados}</td>
    <td class="rprint" style="font-size:10pt">{total_aguinaldos}</td>
  </tr>
  <!-- END BLOCK : fila -->
  <!-- START BLOCK : totales -->
  <tr>
    <th colspan="2" class="print" style="font-size:10pt">Total</th>
    <th class="print_total" style="font-size:10pt">{num_empleados}</th>
    <th class="rprint_total" style="font-size:10pt">{total_aguinaldos}</th>
  </tr>
  <!-- END BLOCK : totales -->
</table>
<!-- START BLOCK : desglose -->
<br>
<table align="center" class="print">
  <tr>
    <th class="print" scope="col" style="font-size:10pt">Total de Billetes </th>
    <th class="print" scope="col" style="font-size:10pt">Totales</th>
  </tr>
  <!-- START BLOCK : den -->
  <tr>
    <td class="vprint" style="font-size:10pt "><strong>{cantidad}</strong> billetes de <strong>{denominacion}</strong> </td>
    <td class="rprint" style="font-size:10pt ">{importe}</td>
  </tr>
  <!-- END BLOCK : den -->
  <tr>
    <th class="rprint" style="font-size:10pt">Total</th>
    <th class="rprint_total" style="font-size:10pt">{total}</th>
  </tr>
</table>
<!-- END BLOCK : desglose -->
<!-- START BLOCK : salto -->
<br style="page-break-after:always;">
<!-- END BLOCK : salto -->

<!-- END BLOCK : listado -->
</body>
</html>
