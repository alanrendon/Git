<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Untitled Document</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../../styles/tablas.css" rel="stylesheet" type="text/css">
<link href="../../styles/pages.css" rel="stylesheet" type="text/css">
<link href="file:///C|/Documents%20and%20Settings/John%20Talbain/Escritorio/Lecaroz/styles/impresion.css" rel="stylesheet" type="text/css">
</head>

<body>
<!-- START BLOCK : datos -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Reporte de N&oacute;minas</p>
  <form action="./fac_nom_con.php" method="get" name="form"><table class="tabla">
    <tr>
      <th class="vtabla" scope="row"><input name="tipo" type="radio" value="pendientes_cia" onClick="next.disabled = false">
        Pendientes por Compa&ntilde;&iacute;a </th>
      </tr>
    <tr>
      <th class="vtabla" scope="row"><input name="tipo" type="radio" value="pendientes_sup" onClick="next.disabled = false">
        Pendientes por Supervisor </th>
      </tr>
    <tr>
      <th class="vtabla" scope="row"><input name="tipo" type="radio" value="recibidas" onClick="next.disabled = false">
        Ya recibidas </th>
      </tr>
    <tr>
      <th class="vtabla" scope="row">Hasta la Semana
        <input name="semana" type="text" class="insert" id="semana" onFocus="temp.value=this.value" onChange="isInt(this,temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 38 || event.keyCode == 40) anio.select();" size="3" maxlength="2"></th>
      </tr>
    <tr>
      <th class="vtabla" scope="row">A&ntilde;o
        <input name="anio" type="text" class="insert" id="anio" onFocus="temp.value=this.value" onChange="isInt(this,temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 38 || event.keyCode == 40) semana.select();" value="{anio}" size="4" maxlength="4"></th>
      </tr>
  </table>  <p>
    <input name="next" type="button" disabled="true" class="boton" id="next" value="Siguiente" onClick="valida_registro(form)">
    </p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
	function valida_registro(form) {
		if (form.semana.value <= 0) {
			alert("Debe especificar la semana");
			form.semana.select();
			return false;
		}
		else if (form.anio.value <= 2000) {
			alert("Debe especificar el año");
			form.anio.select();
			return false;
		}
		else
			form.submit();
	}

	window.onload = document.form.semana.select();
</script>
<!-- END BLOCK : datos -->

<!-- START BLOCK : listado_pendientes -->
<table width="100%">
  <tr>
    <td>&nbsp;</td>
    <td class="print_encabezado" align="center">Oficinas Administrativas MOLLENDO S. de R.L. y C.V. </td>
    <td class="print_encabezado" align="right">{timestamp}</td>
  </tr>
  <tr>
    <td width="20%">&nbsp;</td>
    <td width="60%" class="print_encabezado" align="center">Reporte de N&oacute;minas Faltantes del A&ntilde;o {anio}<br>
      a la Semana No. {semana} {admin}</td>
    <td width="20%">&nbsp;</td>
  </tr>
</table>
  <br>
  <!-- START BLOCK : bloque_pen -->
  <table width="100%" align="center" cellpadding="0" cellspacing="0" class="print">
    <tr>
      <th colspan="2" class="print" scope="col">Panader&iacute;a</th>
      <th class="print" scope="col">Semanas Pendientes de Recibirse </th>
    </tr>
    <!-- START BLOCK : fila_pen -->
	<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
      <td width="5%" class="print">{num_cia}</td>
      <td width="25%" class="vprint">{nombre_cia}</td>
      <td width="75%" class="vprint">{semanas}</td>
    </tr>
	<!-- END BLOCK : fila_pen -->
  </table>
  <br style="page-break-after:always;">
  <!-- END BLOCK : bloque_pen -->
  <!-- END BLOCK : listado_pendientes -->

<!-- START BLOCK : listado_recibidos -->
<table width="100%">
  <tr>
    <td>&nbsp;</td>
    <td class="print_encabezado" align="center">Oficinas Administrativas MOLLENDO S. de R.L. y C.V. </td>
    <td class="print_encabezado" align="right">{timestamp}</td>
  </tr>
  <tr>
    <td width="20%">&nbsp;</td>
    <td width="60%" class="print_encabezado" align="center">Reporte de N&oacute;minas Recibidas del A&ntilde;o {anio}<br>
      a la Semana No. {semana} {admin}</td>
    <td width="20%">&nbsp;</td>
  </tr>
</table>
  <br>
  <!-- START BLOCK : bloque_rec -->
  <table width="100%" align="center" cellpadding="0" cellspacing="0" class="print">
	<tr>
      <th colspan="2" class="print" scope="col">Panader&iacute;a</th>
      <th class="print" scope="col">Semanas Ya Recibidas</th>
    </tr>
    <!-- START BLOCK : fila_rec -->
	<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
      <td width="5%" class="print">{num_cia}</td>
      <td width="25%" class="vprint">{nombre_cia}</td>
      <td width="75%" height="35" class="vprint">{semanas}</td>
    </tr>
	<!-- END BLOCK : fila_rec -->
  </table>
  <br style="page-break-after:always;">
  <!-- END BLOCK : bloque_rec -->
<!-- END BLOCK : listado_recibidos -->

<!-- START BLOCK : listado_pen_sup -->
<table width="100%"  height="49%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="top">
<table width="100%">
  <tr>
    <td>&nbsp;</td>
    <td class="print_encabezado" align="center">Oficinas Administrativas MOLLENDO S. de R.L. y C.V. </td>
    <td class="print_encabezado" align="right">{timestamp}</td>
  </tr>
  <tr>
    <td width="20%">&nbsp;</td>
    <td width="60%" class="print_encabezado" align="center">Reporte de N&oacute;minas Faltantes del A&ntilde;o {anio}<br>
      a la Semana No. {semana} {admin}</td>
    <td width="20%">&nbsp;</td>
  </tr>
</table>
  <h5><br>
  </h5>
  <table width="100%" align="center" cellpadding="0" cellspacing="0" class="print">
    <tr>
      <th colspan="2" class="print" scope="col">Panader&iacute;a</th>
      <th class="print" scope="col">Semanas Pendientes de Recibirse </th>
    </tr>
    <!-- START BLOCK : fila_pen_sup -->
	<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
      <td width="5%" class="print">{num_cia}</td>
      <td width="25%" class="vprint">{nombre_cia}</td>
      <td width="75%" class="vprint">{semanas}</td>
    </tr>
	<!-- END BLOCK : fila_pen_sup -->
  </table></td>
</tr>
</table>
<!-- END BLOCK : listado_pen_sup -->
</body>
</html>
