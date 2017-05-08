<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Untitled Document</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../../styles/impresion.css" rel="stylesheet" type="text/css">
<link href="../../styles/tablas.css" rel="stylesheet" type="text/css">
<link href="../../styles/pages.css" rel="stylesheet" type="text/css">
</head>

<body>
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Consulta de Costos de Inventario</p>
  <table class="tabla">
    <tr>
      <th class="vtabla" scope="col">&nbsp;</th>
      <th class="vtabla" scope="col">&nbsp;</th>
    </tr>
    <tr>
      <th class="vtabla">&nbsp;</th>
      <td class="vtabla">&nbsp;</td>
    </tr>
    <tr>
      <th class="vtabla">&nbsp;</th>
      <td class="vtabla">&nbsp;</td>
    </tr>
  </table>  <p>&nbsp; </p></td>
</tr>
</table>

<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="top">
<table width="100%">
  <tr>
    <td>&nbsp;</td>
    <td class="print_encabezado" align="center">Oficinas Administrativas MOLLENDO S. de R.L. y C.V. </td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td width="20%">&nbsp;</td>
    <td width="60%" class="print_encabezado" align="center">Costo Total de Inventarios<br>
      al {dia} de {mes} de {anio} </td>
    <td width="20%">&nbsp;</td>
  </tr>
</table>
  <br>
  <table class="print">
    <tr>
      <th class="print" scope="col">Num. Cia. </th>
      <th class="print" scope="col">Nombre</th>
      <th class="print" scope="col">Costo Total Inventario </th>
    </tr>
    <tr>
      <td class="print">{num_cia}</td>
      <td class="vprint">{nombre_cia}</td>
      <td class="rprint">{costo_total}</td>
    </tr>
    <tr>
      <th class="print">&nbsp;</th>
      <th class="print">&nbsp;</th>
      <th class="rprint_total">{gran_total}</th>
    </tr>
  </table>
</td>
</tr>
</table>

<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="top">
<table width="100%">
  <tr>
    <td class="print_encabezado">Cia.: {num_cia} </td>
    <td class="print_encabezado" align="center">{nombre_cia}</td>
    <td class="print_encabezado" align="right">Cia.: {num_cia} </td>
  </tr>
  <tr>
    <td width="20%">&nbsp;</td>
    <td width="60%" class="print_encabezado" align="center">Costo de Inventarios por Materia Prima <br>
      al {dia} de {mes} de {anio} </td>
    <td width="20%">&nbsp;</td>
  </tr>
</table>
  <br>
  <table class="print">
    <tr>
      <th class="print" scope="col">C&oacute;digo</th>
      <th class="print" scope="col">Nombre</th>
      <th class="print" scope="col">Existencia</th>
      <th class="print" scope="col">Costo Unitario</th>
      <th class="print" scope="col">Costo Total </th>
    </tr>
    <tr>
      <td class="print">{codmp}</td>
      <td class="print">{nombre_mp}</td>
      <td class="print">{existencia}</td>
      <td class="print">{costo_unitario}</td>
      <td class="print">{costo_total}</td>
    </tr>
    <tr>
      <th class="print">&nbsp;</th>
      <th class="print">&nbsp;</th>
      <th class="print">&nbsp;</th>
      <th class="print">&nbsp;</th>
      <th class="print">{gran_total}</th>
    </tr>
  </table>
</td>
</tr>
</table>
</body>
</html>
