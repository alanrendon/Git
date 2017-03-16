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
<td align="center" valign="middle"><p class="title">Busqueda de Empleados Repetidos</p>
  <p>
    <input type="button" class="boton" value="Comenzar Busqueda" onClick="document.location='./fac_tra_dup.php?search=1'">
  </p></td>
</tr>
</table>
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
    <td width="60%" class="print_encabezado" align="center">Empleados Repetidos </td>
    <td width="20%">&nbsp;</td>
  </tr>
</table>
<br>
<table align="center" class="print">
  <tr>
    <th colspan="2" class="print" scope="col">Compa&ntilde;ia</th>
    <th class="print" scope="col">No. Empleado </th>
    <th class="print" scope="col">Nombre</th>
    <th class="print" scope="col">Puesto</th>
    <th class="print" scope="col">Turno</th>
    <th class="print" scope="col">Fecha Alta </th>
  </tr>
  <!-- START BLOCK : fila -->
  <tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
    <td class="rprint">{num_cia}</td>
    <td class="vprint">{nombre_cia}</td>
    <td class="rprint">{num_emp}</td>
    <td class="vprint">{nombre}</td>
    <td class="vprint">{puesto}</td>
    <td class="vprint">{turno}</td>
    <td class="print">{fecha_alta}</td>
  </tr>
  <!-- END BLOCK : fila -->
</table>
<!-- END BLOCK : listado -->
</body>
</html>
