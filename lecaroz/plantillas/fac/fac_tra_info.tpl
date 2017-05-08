<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-2" />
<title>Untitled Document</title>
<link href="../../styles/tablas.css" rel="stylesheet" type="text/css" />
<link href="../../styles/pages.css" rel="stylesheet" type="text/css" />
</head>

<body>
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><table class="tabla">
  <tr>
    <th class="tabla" scope="col">Nombre</th>
    <th class="tabla" scope="col">Apellido Paterno</th>
    <th class="tabla" scope="col">Apellido Materno</th>
  </tr>
  <tr>
    <td class="tabla">{nombre}</td>
    <td class="tabla">{ap_paterno}</td>
    <td class="tabla">{ap_materno}</td>
  </tr>
</table>
  <br />
  <table class="tabla">
    <tr>
      <th class="tabla" scope="col">Compa&ntilde;&iacute;a</th>
      <th class="tabla" scope="col">N&uacute;mero</th>
      <th class="tabla" scope="col">Fecha Alta</th>
      <th class="tabla" scope="col">Fecha Alta<br />
        IMSS</th>
      <th class="tabla" scope="col">Fecha Baja</th>
      <th class="tabla" scope="col">Fecha Baja<br />
        IMSS</th>
      <th class="tabla" scope="col">&Uacute;ltimo<br />
        Aguinaldo</th>
      <th class="tabla" scope="col">A&ntilde;o</th>
    </tr>
    <!-- START BLOCK : fila -->
    <tr {style}>
      <td class="vtabla">{num_cia} {nombre}</td>
      <td class="tabla">{num_emp}</td>
      <td class="tabla">{fecha_alta}</td>
      <td class="tabla">{fecha_alta_imss}</td>
      <td class="tabla">{fecha_baja}</td>
      <td class="tabla">{fecha_baja_imss}</td>
      <td class="rtabla">{aguinaldo}</td>
      <td class="tabla">{anio}</td>
    </tr>
    <!-- END BLOCK : fila -->
  </table>
  <p>
    <input type="button" class="boton" value="Cerrar" onclick="self.close()" />
  </p></td>
</tr>
</table>
</body>
</html>
