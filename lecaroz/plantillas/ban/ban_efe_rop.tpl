<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Untitled Document</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../../styles/pages.css" rel="stylesheet" type="text/css">
<link href="../../styles/tablas.css" rel="stylesheet" type="text/css">
<link href="../../styles/impresion.css" rel="stylesheet" type="text/css">
</head>

<body>
<!-- START BLOCK : datos -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Efectivos de Rosticerias y Porcentajes </p>
  <form action="./ban_efe_rop.php" method="get" name="form"><table class="tabla">
    <tr>
      <th class="vtabla" scope="row">Compa&ntilde;&iacute;a</th>
      <td class="vtabla"><input name="num_cia" type="text" class="insert" id="num_cia" onFocus="temp.value=this.value" onChange="isInt(this,temp)" onKeyDown="if (event.keyCode == 13) anio.select()" size="3" maxlength="3"></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Administrador</th>
      <td class="vtabla"><select name="admin" class="insert" id="admin">
        <option value="" selected></option>
		<!-- START BLOCK : admin -->
        <option value="{id}">{nombre}</option>
		<!-- END BLOCK : admin -->
      </select></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Mes</th>
      <td class="vtabla"><select name="mes" class="insert" id="mes">
        <option value="1" {1}>ENERO</option>
        <option value="2" {2}>FEBRERO</option>
        <option value="3" {3}>MARZO</option>
        <option value="4" {4}>ABRIL</option>
        <option value="5" {5}>MAYO</option>
        <option value="6" {6}>JUNIO</option>
        <option value="7" {7}>JULIO</option>
        <option value="8" {8}>AGOSTO</option>
        <option value="9" {9}>SEPTIEMBRE</option>
        <option value="10" {10}>OCTUBRE</option>
        <option value="11" {11}>NOVIEMBRE</option>
        <option value="12" {12}>DICIEMBRE</option>
      </select></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">A&ntilde;o</th>
      <td class="vtabla"><input name="anio" type="text" class="insert" id="anio" onFocus="temp.value=this.value" onChange="isInt(this,temp)" onKeyDown="if (event.keyCode == 13) num_cia.select()" value="{anio}" size="4" maxlength="4"></td>
    </tr>
  </table>  
  <p>
    <input type="button" class="boton" value="Siguiente" onClick="validar(this.form)">
  </p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
	function validar(form) {
		if (form.anio.value <= 0) {
			alert("Debe especificar el año");
			form.anio.select();
			return false;
		}
		else
			form.submit();
	}
	window.onload = document.form.num_cia.select();
</script>
<!-- END BLOCK : datos -->
<!-- START BLOCK : listado -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="top">
<table width="100%">
  <tr>
    <td>&nbsp;</td>
    <td class="print_encabezado" align="center">Oficinas Administrativas Mollendo S. de R.L. y C.V. </td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td width="20%">&nbsp;</td>
    <td width="60%" class="print_encabezado" align="center">Efectivos de Rosticerias y Porcentajes <br>
      al mes de {mes} de {anio} </td>
    <td width="20%">&nbsp;</td>
  </tr>
</table>
<br>
<table class="print">
  <tr>
    <th colspan="2" class="print" scope="col">Compa&ntilde;&iacute;a</th>
    <th class="print" scope="col">Efectivo</th>
    <th class="print" scope="col">Dep&oacute;sitos</th>
    <th class="print" scope="col"> Porcentaje </th>
    <th class="print" scope="col">I.V.A. Dep&oacute;sitos</th>
  </tr>
  <!-- START BLOCK : fila -->
  <tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
    <td class="rprint">&nbsp;&nbsp;{num_cia}&nbsp;&nbsp;</td>
    <td class="vprint">&nbsp;&nbsp;{nombre_cia}&nbsp;&nbsp;</td>
    <td class="rprint">&nbsp;&nbsp;<font color="#0000FF">{efectivo}</font>&nbsp;&nbsp;</td>
    <td class="rprint">&nbsp;&nbsp;<font color="#FF0000">{depositos}</font>&nbsp;&nbsp;</td>
    <td class="rprint">&nbsp;&nbsp;<font color="#0000FF">{porcentaje}</font>&nbsp;&nbsp;</td>
    <td class="rprint">&nbsp;&nbsp;<font color="#FF0000">{iva_dep}</font>&nbsp;&nbsp;</td>
  </tr>
  <!-- END BLOCK : fila -->
  <tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
    <th colspan="2" class="rprint">Total</th>
    <th class="rprint_total">{efectivo}</th>
    <th class="rprint_total">{depositos}</th>
    <th class="rprint_total">{porcentaje}</th>
    <th class="rprint_total">{iva_dep}</th>
  </tr>
</table>

</td>
</tr>
</table>
<!-- END BLOCK : listado -->
</body>
</html>
