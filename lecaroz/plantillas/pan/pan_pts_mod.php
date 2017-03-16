<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Untitled Document</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../../styles/tablas.css" rel="stylesheet" type="text/css">
<link href="../../styles/pages.css" rel="stylesheet" type="text/css">
</head>

<body>
<!-- START BLOCK : orden -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Modificaci&oacute;n de Productos </p>
  <form action="./pan_pts_con.php" method="get" name="form"><table class="tabla">
    <tr>
      <th class="vtabla" scope="row"><input name="tipo" type="radio" value="codigo" checked>
        Por c&oacute;digo 
        <input name="cod_producto" type="text" class="insert" id="cod_producto" size="4" maxlength="4"></th>
    </tr>
    <tr>
      <th class="vtabla" scope="row"><input name="tipo" type="radio" value="listado">
        Listado</th>
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
<p class="title">Modificaci&oacute;n de Productos </p>
  <table class="tabla">
    <tr>
      <th class="tabla" scope="col">C&oacute;digo</th>
      <th class="tabla" scope="col">Nombre</th>
      <th class="tabla" scope="col">&nbsp;</th>
    </tr>
    <!-- START BLOCK : fila -->
	<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
      <td class="tabla">{cod_producto}</td>
      <td class="vtabla">{nombre}</td>
      <td class="vtabla"><input type="button" class="boton" value="Modificar">
        <input type="button" class="boton" value="Borrar"></td>
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
