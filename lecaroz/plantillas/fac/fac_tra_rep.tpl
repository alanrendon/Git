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
<!-- START BLOCK : datos -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">B&uacute;squeda de Empleados Repetidos </p>
  <form action="./fac_tra_rep.php" method="get" name="form">
  <input name="buscar" type="hidden" value="1">
  <table class="tabla">
    <tr>
      <th class="vtabla" scope="col">&nbsp;</th>
      <td class="vtabla" scope="col">&nbsp;</td>
    </tr>
    <tr>
      <th class="vtabla">&nbsp;</th>
      <td class="vtabla">&nbsp;</td>
    </tr>
  </table>  <p>
    <input type="button" class="boton" value="Siguiente" onClick="valida_registro(form)">
  </p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
	function valida_registro(form) {
		form.submit();
	}
</script>
<!-- END BLOCK : datos -->

<!-- START BLOCK : result -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Empleados Repetidos</p>
  <table class="tabla">
    <tr>
      <th class="tabla" scope="col">No.</th>
      <th class="tabla" scope="col">Nombre</th>
      <th class="tabla" scope="col">Compa&ntilde;&iacute;a</th>
      <th class="tabla" scope="col">Turno</th>
      <th class="tabla" scope="col">Puesto</th>
      <th class="tabla" scope="col">Status</th>
      <th class="tabla" scope="col">No. Afiliaci&oacute;n </th>
      <th class="tabla" scope="col">Prestamos</th>
      <th class="tabla" scope="col">Infonavit</th>
      <th class="tabla" scope="col">Antig&uuml;edad</th>
    </tr>
    <!-- START BLOCK : fila -->
	<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
      <td class="tabla">{num_emp}</td>
      <td class="vtabla">{nombre}</td>
      <td class="vtabla">{num_cia} - {nombre_cia} </td>
      <td class="tabla">{turno}</td>
      <td class="tabla">{puesto}</td>
      <td class="tabla"><strong>{status}</strong></td>
      <td class="tabla">{num_afiliacion}</td>
      <td class="tabla">{prestamo}</td>
      <td class="tabla">{infonavit}</td>
      <td class="tabla">{fecha_alta}</td>
	</tr>
	<!-- END BLOCK : fila -->
  </table>  <p>
    <input type="button" class="boton" value="Regresar"> 
    </p></td>
</tr>
</table>
<!-- END BLOCK : result -->
</body>
</html>
