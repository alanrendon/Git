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
<td align="center" valign="middle"><p class="title">D&eacute;positos Pendientes del Mes</p>
  <form action="./ban_pen_mes.php" method="get" name="form">
    <input name="tmp" type="hidden" id="tmp">
    <table class="tabla">
    <!-- START BLOCK : pan -->
	<tr>
      <th class="vtabla" scope="row">No incluir compa&ntilde;&iacute;a(s)</th>
      <td class="vtabla"><input name="num_cia[]" type="text" class="insert" id="num_cia" onFocus="tmp.value=this.value" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) num_cia[1].select()" size="3" maxlength="3">
	  <input name="num_cia[]" type="text" class="insert" id="num_cia" onFocus="tmp.value=this.value" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) num_cia[2].select()" size="3" maxlength="3">
	  <input name="num_cia[]" type="text" class="insert" id="num_cia" onFocus="tmp.value=this.value" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) num_cia[3].select()" size="3" maxlength="3">
	  <input name="num_cia[]" type="text" class="insert" id="num_cia" onFocus="tmp.value=this.value" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) num_cia[4].select()" size="3" maxlength="3">
	  <input name="num_cia[]" type="text" class="insert" id="num_cia" onFocus="tmp.value=this.value" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) num_cia[5].select()" size="3" maxlength="3">
	  <input name="num_cia[]" type="text" class="insert" id="num_cia" onFocus="tmp.value=this.value" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) num_cia[6].select()" size="3" maxlength="3">
	  <input name="num_cia[]" type="text" class="insert" id="num_cia" onFocus="tmp.value=this.value" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) num_cia[7].select()" size="3" maxlength="3">
	  <input name="num_cia[]" type="text" class="insert" id="num_cia" onFocus="tmp.value=this.value" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) num_cia[8].select()" size="3" maxlength="3">
	  <input name="num_cia[]" type="text" class="insert" id="num_cia" onFocus="tmp.value=this.value" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) num_cia[9].select()" size="3" maxlength="3">
	  <input name="num_cia[]" type="text" class="insert" id="num_cia" onFocus="tmp.value=this.value" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) fecha.select()" size="3" maxlength="3"></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Administrador</th>
      <td class="vtabla"><select name="idadmin" class="insert" id="idadmin">
        <option value="" selected></option>
        <option value="-1" style="font-weight: bold;">AGRUPADO POR ADMINISTRADOR</option>
		<!-- START BLOCK : admin -->
		<option value="{id}">{nombre}</option>
		<!-- END BLOCK : admin -->
      </select></td>
    </tr>
	<!-- END BLOCK : pan -->
    <tr>
      <th class="vtabla" scope="row">Fecha de Corte </th>
      <td class="vtabla"><input name="fecha" type="text" class="insert" id="fecha" onChange="actualiza_fecha(this)" onKeyDown="if (event.keyCode == 13 && num_cia[0]) num_cia[0].select(); else if (event.keyCode == 13) this.blur()" value="{fecha}" size="10" maxlength="10"></td>
    </tr>
  </table>  
    <p>
    <input type="button" class="boton" value="Siguiente" onClick="validar(this.form)">
  </p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
<!--
function validar(form) {
	if (form.fecha.value.length < 8) {
		alert("Debe especificar la fecha de corte");
		form.fecha.select();
		return false;
	}
	else {
		form.submit();
	}
}

window.onload = document.form.num_cia[0] ? document.form.num_cia[0].select() : document.form.fecha.select();
-->
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
    <td width="60%" class="print_encabezado" align="center">Dep&oacute;sitos Pendientes<br>
      al {dia} de {mes} de {anio} </td>
    <td width="20%">&nbsp;</td>
  </tr>
</table>
<br>
<table align="center" class="print">
  <tr>
    <th colspan="2" class="tabla" scope="col">Compa&ntilde;&iacute;a</th>
    <th class="tabla" scope="col">D&iacute;as</th>
  </tr>
  <!-- START BLOCK : fila -->
  <tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
    <td class="tabla">{num_cia}</td>
    <td class="vtabla">{nombre_cia}</td>
    <td class="vtabla">{dias}</td>
  </tr>
  <!-- END BLOCK : fila -->
</table>
{salto}
<!-- END BLOCK : listado -->
</body>
</html>
