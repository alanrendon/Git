<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Untitled Document</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="./styles/impresion.css" rel="stylesheet" type="text/css">
</head>

<body>
<!-- START BLOCK : hoja -->
<table width="100%" align="center">
  <tr>
    <td>&nbsp;</td>
    <td class="print_encabezado" align="center">Oficinas Administrativas MOLLENDO S. de R.L. y C.V. </td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td width="20%">&nbsp;</td>
    <td width="60%" class="print_encabezado" align="center">Comparativo al Mes de {mes} de {anio} </td>
    <td width="20%">&nbsp;</td>
  </tr>
</table>
  <br>
  <table width="100%" align="center" class="print">
    <tr>
      <th colspan="2" class="print" scope="col">N&uacute;mero y Nombre</th>
      <th class="print" scope="col">Total Efectivo </th>
      <th class="print" scope="col">General</th>
      <th class="print" scope="col">Diferencia</th>
      <th class="print" scope="col">Efectivo</th>
      <th class="print" scope="col">Dep&oacute;sitos</th>
      <th class="print" scope="col">Otros</th>
      <th class="print" scope="col">Faltantes</th>
      <th class="print" scope="col">Diferencia</th>
    </tr>
    <!-- START BLOCK : fila -->
	<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
      <td width="5%" class="print">{num_cia}</td>
      <td width="25%" class="vprint">{nombre_cia}</td>
      <td width="10%" class="rprint">{total_efectivo}</td>
      <td width="10%" class="rprint">{utilidad}</td>
      <td width="10%" class="rprint">{diferencia1}</td>
      <td width="10%" class="rprint">{efectivo}</td>
      <td width="10%" class="rprint">{depositos}</td>
      <td width="10%" class="rprint">{otros}</td>
      <td width="10%" class="rprint">{faltantes}</td>
      <td width="10%" class="rprint">{diferencia2}</td>
    </tr>
	<!-- END BLOCK : fila -->
	<!-- START BLOCK : total -->
	<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
	  <th colspan="2" class="print">Total</th>
	  <th class="rprint_total">{total_efectivo}</th>
	  <th class="print">&nbsp;</th>
	  <th class="rprint_total">{diferencia1}</th>
	  <th colspan="4" class="rprint">&nbsp;</th>
	  <th class="rprint_total">{diferencia2}</th>
    </tr>
	<!-- END BLOCK : total -->
</table>
<br style="page-break-after:always;">
<!-- END BLOCK : hoja -->
</body>
</html>
