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
<td align="center" valign="middle"><p class="title">Consulta de Gastos de Caja Fijos</p>
  <form action="./bal_gas_caj_fij_mod.php" method="get" name="form">
    <input name="temp" type="hidden" id="temp">
    <table class="tabla">
    <tr>
      <th class="vtabla" scope="row">Compa&ntilde;&iacute;a</th>
      <td class="vtabla"><input name="num_cia" type="text" class="insert" id="num_cia" onFocus="temp.value=this.value" onChange="isInt(this,temp)" onKeyDown="if (event.keyCode == 13) this.blur()" size="3" maxlength="3"></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">C&oacute;digo</th>
      <td class="vtabla"><select name="cod_gastos" class="insert" id="cod_gastos">
        <option value="" selected></option>
		<!-- START BLOCK : cod_gastos -->
		<option value="{id}">{id} {descripcion}</option>
		<!-- END BLOCK : cod_gastos -->
      </select></td>
    </tr>
  </table>  <p>
    <input type="button" class="boton" value="Siguiente" onClick="validar(this.form)">
  </p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
<!--
function validar(form) {
	if (form.num_cia.value != "" && form.num_cia.value <= 0) {
		alert("Debe especificar la compañía");
		form.num_cia.select();
		return false;
	}
	else {
		form.submit();
	}
}

window.onload = document.form.num_cia.select();
-->
</script>
<!-- END BLOCK : datos -->
<!-- START BLOCK : listado -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Gastos de Caja Fijos </p>
  <form action="" method="get" name="form">
    <input name="num_cia" type="hidden" id="num_cia" value="{num_cia}">
    <input name="cod_gastos" type="hidden" id="cod_gastos" value="{cod_gastos}">    
    <table class="tabla">
    <tr>
      <th colspan="2" class="tabla" scope="col">Compa&ntilde;&iacute;a</th>
      <th class="tabla" scope="col">Concepto</th>
      <th class="tabla" scope="col">Comentario</th>
      <th class="tabla" scope="col">Tipo</th>
      <th class="tabla" scope="col">Balance</th>
      <th class="tabla" scope="col">Importe</th>
      <th class="tabla" scope="col">Acci&oacute;n</th>
    </tr>
    <!-- START BLOCK : fila -->
	<tr>
      <td class="tabla"><input name="num_cia[]" type="text" disabled="true" class="nombre" id="num_cia" value="{num_cia}" size="3"></td>
      <td class="tabla"><input name="nombre_cia[]" type="text" disabled="true" class="vnombre" id="nombre_cia" value="{nombre_cia}" size="20"></td>
      <td class="tabla"><input name="cod_gastos[]" type="text" disabled="true" class="vnombre" id="cod_gastos" value="{cod_gastos}" size="25"></td>
      <td class="tabla"><input name="comentario[]" type="text" disabled="true" class="vnombre" id="comentario" value="{comentario}" size="25"></td>
      <td class="tabla"><input name="tipo_mov[]" type="text" disabled="true" class="nombre" id="tipo_mov" value="{tipo_mov}" size="7" maxlength="7"></td>
      <td class="tabla"><input name="balance[]" type="text" disabled="true" class="nombre" id="balance" value="{balance}" size="2" maxlength="2"></td>
      <td class="tabla"><input name="importe[]" type="text" disabled="true" class="rnombre" id="importe" value="{importe}" size="10" maxlength="10"></td>
      <td class="tabla"><input type="button" class="boton" value="Modificar" onClick="modificar({id}, {i})">
        <input type="button" class="boton" value="Borrar" onClick="borrar({id})"></td>
    </tr>
	<!-- END BLOCK : fila -->
  </table>
  </form>  
  <p>
    <input type="button" class="boton" value="Regresar" onClick="document.location='./bal_gas_caj_fij_mod.php'">
  </p></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
<!--
var form = document.form;

function borrar(id) {
	if (confirm("¿Desea borrar el registro?")) {
		document.location = "./bal_gas_caj_fij_mod.php?id=" + id + "&num_cia=" + form.num_cia.value + "&cod_gastos=" + form.cod_gastos.value;
	}
	else {
		return false;
	}
}

function modificar(id, i) {
	var mod = window.open("./bal_gas_caj_fij_minimod.php?id=" + id + "&i=" + i,"mod","top=234,left=0,toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=no,width=1024,height=300");
}
-->
</script>
<!-- END BLOCK : listado -->
</body>
</html>
