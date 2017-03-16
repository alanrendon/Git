<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Documento sin t&iacute;tulo</title>
<link href="../../styles/tablas.css" rel="stylesheet" type="text/css" />
<link href="../../styles/pages.css" rel="stylesheet" type="text/css" />
<link href="../../styles/impresion.css" rel="stylesheet" type="text/css" />
</head>

<body>
<!-- START BLOCK : datos -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">B&uacute;squeda de Empleados Repetidos </p>
  <form action="./fac_emp_rep.php" method="get" name="form"><table class="tabla">
    <tr>
      <th class="vtabla" scope="row">Compa&ntilde;&iacute;a</th>
      <td class="vtabla"><input name="num_cia" type="text" class="insert" id="num_cia" onfocus="tmp.value=this.value;this.select()" onchange="if (isInt(this,tmp)) cambiaCia()" onkeydown="if (event.keyCode == 13) this.blur()" size="3" readonly="true" />
          <input name="nombre" type="text" id="nombre" size="30" /></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Comparar con otras compa&ntilde;&iacute;as </th>
      <td class="vtabla"><input name="comp" type="checkbox" id="comp" value="1" checked="checked" />
        Si</td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Incluir bajas </th>
      <td class="vtabla"><input name="bajas" type="checkbox" id="bajas" value="1" />
        Si</td>
    </tr>
  </table>
  <p>
    <input type="button" class="boton" value="Siguiente" />
</p></form></td>
</tr>
</table>
<!-- END BLOCK : datos -->
<!-- START BLOCK : result -->
<table width="100%">
  <tr>
    <td>&nbsp;</td>
    <td class="print_encabezado" align="center">Oficinas Administrativas Mollendo S. de R.L. y C.V. </td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td width="20%">&nbsp;</td>
    <td width="60%" class="print_encabezado" align="center">Empleados Repetidos </td>
    <td width="20%">&nbsp;</td>
  </tr>
</table>
<br>
<table align="center" class="tabla">
  <tr>
    <th class="tabla" scope="col">No.</th>
    <th class="tabla" scope="col">Empleado</th>
    <th class="tabla" scope="col">Compa&ntilde;&iacute;a</th>
    <th class="tabla" scope="col">Fecha Alta </th>
    <th class="tabla" scope="col">Alta IMSS </th>
    <th class="tabla" scope="col">Fecha Baja </th>
    <th class="tabla" scope="col">Baja IMSS </th>
  </tr>
  <!-- START BLOCK : fila -->
  <tr>
    <td class="tabla">{num_emp}</td>
    <td class="tabla">{nombre}</td>
    <td class="tabla">{num_cia}</td>
    <td class="tabla" style="color:#0000CC;">{fecha_alta}</td>
    <td class="tabla" style="color:#0000CC;">{alta_imss}</td>
    <td class="tabla" style="color:#CC0000;">{fecha_baja}</td>
    <td class="tabla" style="color:#CC0000;">{baja_imss}</td>
  </tr>
  <!-- END BLOCK : fila -->
  <!-- START BLOCK : blanco -->
  <tr>
    <td colspan="7" class="tabla">&nbsp;</td>
  </tr>
  <!-- END BLOCK : blanco -->
</table>
<!-- END BLOCK : result -->
</body>
</html>
