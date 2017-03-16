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
<td align="center" valign="middle"><p class="title">Vaciado de Dep&oacute;sitos de Cometra</p>
  <form action="./ban_com_dep_v2.php" method="get"><table class="tabla">
    <tr>
      <th class="vtabla" scope="row">Archivo</th>
      <td class="vtabla"><input name="file" type="file" class="insert" size="20" maxlength="20"></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Cuenta</th>
      <td class="vtabla"><select name="cuenta" class="insert" id="cuenta">
        <option value="1" selected>BANORTE</option>
        <option value="2">SANTANDER</option>
      </select></td>
    </tr>
  </table>  <p>
    <input type="button" class="boton" value="Siguiente">
  </p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
<!--
function
-->
</script>
<!-- END BLOCK : datos -->
<!-- START BLOCK : listado -->
<table width="100%">
  <tr>
    <td>&nbsp;</td>
    <td class="print_encabezado" align="center">Oficinas Administrativas Mollendo S. de R.L. y C.V. </td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td width="20%">&nbsp;</td>
    <td width="60%" class="print_encabezado" align="center">Dep&oacute;sitos Capturados<br>
    al d&iacute;a {dia} de {mes} de {anio} </td>
    <td width="20%">&nbsp;</td>
  </tr>
</table>
<br>
<table align="center" class="print">
  <tr>
    <th colspan="2" class="print" scope="col">Compa&ntilde;&iacute;a</th>
    <th class="print" scope="col">Cuenta</th>
    <th colspan="2" class="print" scope="col">C&oacute;digo</th>
    <th class="print" scope="col">Fecha</th>
    <th class="print" scope="col">Importe</th>
  </tr>
  <tr>
    <td class="rprint">{num_cia}</td>
    <td class="print">{nom<span class="vprint">bre</span>_cia}</td>
    <td class="print">{cuenta}</td>
    <td class="print">{cod<span class="rprint">_m</span>ov}</td>
    <td class="vprint">{descripcion}</td>
    <td class="print">{fecha}</td>
    <td class="print">{impo<span class="rprint">r</span>te}</td>
  </tr>
  <tr>
    <th colspan="6" class="rprint">Total</th>
    <th class="rprint_total">{total}</th>
  </tr>
</table>
<!-- END BLOCK : listado -->
</body>
</html>
