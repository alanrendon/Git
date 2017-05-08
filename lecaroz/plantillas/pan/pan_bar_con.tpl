<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Untitled Document</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../../styles/impresion.css" rel="stylesheet" type="text/css">
</head>

<body>
<!-- START BLOCK : listado -->
<table width="100%" align="center">
  <tr>
    <td>&nbsp;</td>
    <td class="print_encabezado" align="center">Oficinas Administrativas MOLLENDO S. de R.L. y C.V. </td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td width="20%">&nbsp;</td>
    <td width="60%" class="print_encabezado" align="center">Comprobantes de Barredura <br>
      al {dia} de {mes} de {anio} </td>
    <td width="20%">&nbsp;</td>
  </tr>
</table>
  <br>
  <table width="70%" align="center" cellpadding="0" cellspacing="0" class="print">
    <tr>
      <th class="print" scope="col">Comprobante</th>
      <th class="print" scope="col">Importe</th>
      <th colspan="2" class="print" scope="col">N&uacute;mero y Nombre de la Compa&ntilde;&iacute;a </th>
    </tr>
    <!-- START BLOCK : color -->
	<tr>
      <th colspan="4" class="vprint_total">Comprador: {no_color} {color}</th>
    </tr>
    <!-- START BLOCK : comprador -->
	<tr>
      <td class="print">{comprobante}</td>
      <td class="rprint">{importe}</td>
      <td class="print">{num_cia}</td>
      <td class="vprint">{nombre_cia}</td>
    </tr>
	<!-- END BLOCK : comprador -->
    <tr>
      <th class="print">&nbsp;</th>
      <th class="rprint_total">{total}</th>
      <th colspan="2" class="print">&nbsp;</th>
    </tr>
	  <!-- END BLOCK : color -->
    <tr>
      <td colspan="4">&nbsp;</td>
    </tr>
</table>
<!-- END BLOCK : listado -->
</body>
</html>
