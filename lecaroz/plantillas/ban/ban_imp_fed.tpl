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
<td align="center" valign="middle"><p class="title">Pago de Impuestos Federales por Entrada de Archivo</p>
  <form action="./ban_imp_fed.php" method="post" name="form" enctype="multipart/form-data">
  <input name="MAX_FILE_SIZE" type="hidden" value="5242880">
  <table class="tabla">
    <tr>
      <th class="vtabla" scope="row">Ruta del archivo </th>
      <td class="vtabla"><input name="file" type="file" class="insert"></td>
    </tr>
  </table>  <p>
    <input type="button" class="boton" value="Siguiente" onClick="validar(this.form)"> 
    </p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
	function validar(form) {
		if (form.file.value.length < 12) {
			alert("Debe especificar el nombre y la ruta del archivo");
			form.file.focus();
			return false;
		}
		else if (confirm("¿Esta seguro de generar los pagos de impuestos?"))
			form.submit();
		else
			form.file.focus();
	}
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
    <td width="60%" class="print_encabezado" align="center">Pago de Impuestos Federales <br>
    el {dia} de {mes} de {anio} </td>
    <td width="20%">&nbsp;</td>
  </tr>
</table>
  <br>
  <table align="center" class="print">
    <tr>
      <th class="print" scope="col">&nbsp;</th>
      <th class="print" scope="col">Compa&ntilde;&iacute;a</th>
      <th class="print" scope="col">Cuenta</th>
      <th class="print" scope="col">Fecha</th>
      <th class="print" scope="col">Folio</th>
      <th class="print" scope="col">Importe</th>
    </tr>
    <!-- START BLOCK : fila -->
	<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
      <td class="rprint">{num_cia}</td>
      <td class="vprint">{nombre_cia}</td>
      <td class="print">{cuenta}</td>
      <td class="print">{fecha}</td>
      <td class="print">{folio}</td>
      <td class="rprint">{importe}</td>
    </tr>
	<!-- END BLOCK : fila -->
  </table>
<!-- END BLOCK : listado -->
<!-- START BLOCK : no_result -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">No hay resultados</p>
  <p>
    <input type="button" class="boton" onClick="document.location='./ban_imp_fed.php'" value="Regresar">
  </p></td>
</tr>
</table>
<!-- END BLOCK : no_result -->
</body>
</html>
