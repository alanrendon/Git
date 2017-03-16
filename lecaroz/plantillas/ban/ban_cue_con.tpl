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
<td align="center" valign="middle"><p class="title">Cat&aacute;logo de Cuentas </p>
  <form action="./ban_cue_con.php" method="get" name="form">
  <input name="listado" type="hidden" value="1">
  <input name="temp" type="hidden">
  <table class="tabla">
    <tr>
      <th class="vtabla" scope="col">Compa&ntilde;&iacute;a</th>
      <td class="vtabla" scope="col"><input name="num_cia" type="text" class="insert" id="num_cia" onFocus="temp.value=this.value" onChange="isInt(this,temp)" size="3" maxlength="3"></td>
    </tr>
    <tr>
      <th class="vtabla" scope="col">Cuenta</th>
      <td class="vtabla" scope="col"><select name="cuenta" class="insert" id="cuenta">
        <option value="1" selected>BANORTE</option>
        <option value="2">SANTANDER SERFIN</option>
      </select></td>
    </tr>
  </table>  
  <p>
    <input type="button" class="boton" value="Siguiente" onClick="valida_registro(this.form);">
  </p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
	function valida_registro(form) {
		form.submit();
	}
	
	window.onload = document.form.num_cia.select();
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
    <td width="60%" class="print_encabezado" align="center">Listado de Cuentas por Compa&ntilde;&iacute;a <br>
      al {dia} de {mes} de {anio} </td>
    <td width="20%">&nbsp;</td>
  </tr>
</table>
  <br>
  <table width="80%" align="center" class="print">
    <tr>
      <th width="10%" class="print" scope="col">No. Cia. </th>
      <th width="15%" class="print" scope="col">Cuenta</th>
      <th width="75%" class="print" scope="col">Nombre</th>
    </tr>
    <!-- START BLOCK : fila -->
	<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
      <td class="print">{num_cia}</td>
      <td class="print">{cuenta}</td>
      <td class="vprint">{nombre}</td>
    </tr>
	<!-- END BLOCK : fila -->
  </table>
  <br style="page-break-after:always;">
<!-- END BLOCK : listado -->
</body>
</html>
