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
<!-- START BLOCK : orden -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Consulta de Productos </p>
  <form action="./pan_pts_con.php" method="get" name="form"><table class="tabla">
    <tr>
      <th class="vtabla" scope="row"><input name="orden" type="radio" value="codigo" checked>
        Por c&oacute;digo </th>
    </tr>
    <tr>
      <th class="vtabla" scope="row"><input name="orden" type="radio" value="nombre">
        Por nombre </th>
    </tr>
  </table>  <p>
    <input name="Submit" type="submit" class="boton" value="Siguiente">
  </p></form></td>
</tr>
</table>
<!-- END BLOCK : orden -->
<!-- START BLOCK : listado -->
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
    <td width="60%" class="print_encabezado" align="center">Listado de Productos </td>
    <td width="20%">&nbsp;</td>
  </tr>
</table>
  <br>
  <table class="tabla">
    <tr>
      <th class="print" scope="col">C&oacute;digo</th>
      <th class="print" scope="col">Nombre</th>
      <th class="print" scope="col">Precio</th>
    </tr>
    <!-- START BLOCK : fila -->
	<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
      <td class="print">{cod_producto}</td>
      <td class="vprint">{nombre}</td>
      <td class="vprint">{precio}</td>
      
    </tr>
	<!-- END BLOCK : fila -->
  </table>
  <p>
    <input type="button" class="boton" value="Regresar" onClick="history.back()">
  </p></td>
</tr>
</table>
<!-- END BLOCK : listado -->
</body>
</html>
