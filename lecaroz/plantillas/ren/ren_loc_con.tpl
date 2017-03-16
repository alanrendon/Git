<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Untitled Document</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../../styles/tablas.css" rel="stylesheet" type="text/css">
<link href="../../styles/pages.css" rel="stylesheet" type="text/css">
<link href="../../styles/impresion.css" rel="stylesheet" type="text/css">
</head>

<body>
<!-- START BLOCK : datos -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Listado de Locales</p>
  <table class="tabla">
    <tr>
      <th class="vtabla" scope="row">Inmobiliaria</th>
      <td class="vtabla"><input name="arr" type="text" class="insert" id="arr" size="3"></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Local</th>
      <td class="vtabla"><input name="local" type="text" class="insert" id="local" size="3"></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Orden</th>
      <td class="vtabla"><input name="tipo" type="radio" value="1" checked>
        Inmobiliaria<br>
        <input name="tipo" type="radio" value="2">
        Local</td>
    </tr>
  </table>  <p>
    <input type="button" class="boton" value="Siguiente"> 
    </p></td>
</tr>
</table>
<!-- END BLOCK : datos -->
<!-- START BLOCK : por_arr -->
<table width="100%">
  <tr>
    <td>&nbsp;</td>
    <td class="print_encabezado" align="center">Oficinas Administrativas Mollendo S. de R.L. y C.V. </td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td width="20%">&nbsp;</td>
    <td width="60%" class="print_encabezado" align="center">Listado de Locales </td>
    <td width="20%">&nbsp;</td>
  </tr>
</table>
<br>
<table align="center" class="print">
  <!-- START BLOCK : arr -->
  <tr>
    <th colspan="9" class="print" scope="col">{arr}</th>
  </tr>
  <tr>
    <th class="print">Local</th>
    <th class="print">Arrendatario</th>
    <th class="print">Bloque</th>
    <th class="print">Fecha Inicio </th>
    <th class="print">Fecha Final </th>
    <th class="print">Renta</th>
    <th class="print">Mantenimiento</th>
    <th class="print">Retenci&oacute;n I.S.R. </th>
    <th class="print">Retenci&oacute;n I.V.A. </th>
  </tr>
  <!-- START BLOCK : local -->
  <tr>
    <td class="print">{local} {nombre} </td>
    <td class="print">{arr}</td>
    <td class="print">{bloque}</td>
    <td class="print">{fecha_ini}</td>
    <td class="print">{fecha_fin}</td>
    <td class="print">{renta}</td>
    <td class="print">{mant}</td>
    <td class="print">{isr}</td>
    <td class="print">{ret}</td>
  </tr>
  <!-- END BLOCK : local -->
  <tr>
    <td colspan="9" class="print">&nbsp;</td>
  </tr>
  <!-- END BLOCK : arr -->
</table>
<!-- END BLOCK : por_arr -->
<!-- START BLOCK : por_local -->

<!-- END BLOCK : por_local 
</body>
</html>

