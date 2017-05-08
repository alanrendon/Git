<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Untitled Document</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="file:///C|/Documents%20and%20Settings/John%20Talbain/Escritorio/Lecaroz/styles/pages.css" rel="stylesheet" type="text/css">
<link href="file:///C|/Documents%20and%20Settings/John%20Talbain/Escritorio/Lecaroz/styles/tablas.css" rel="stylesheet" type="text/css">
</head>

<body>
<!-- START BLOCK : cerrar -->
<script language="javascript" type="text/javascript">
<!--
function cerrar() {
	var form = window.opener.document.form;
	
	if (form.num_cia.length == undefined) {
		form.num_cia.value = "{num_cia}";
		form.nombre_cia.value = "{nombre_cia}";
		form.importe.value = "{importe}";
		form.comentario.value = "{comentario}";
		form.tipo_mov.value = "{tipo_mov}";
		form.cod_gastos.value = "{cod_gastos}";
		form.balance.value = "{balance}";
	}
	else {
		form.num_cia[{i} + 1].value = "{num_cia}";
		form.nombre_cia[{i}].value = "{nombre_cia}";
		form.importe[{i}].value = "{importe}";
		form.comentario[{i}].value = "{comentario}";
		form.tipo_mov[{i}].value = "{tipo_mov}";
		form.cod_gastos[{i} + 1].value = "{cod_gastos}";
		form.balance[{i}].value = "{balance}";
	}
	self.close();
}

window.onload = cerrar();
-->
</script>
<!-- END BLOCK : cerrar -->
<!-- START BLOCK : mod -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Modificaci&oacute;n de Gastos de Caja Fijos</p>
  <form action="./bal_gas_caj_fij_minimod.php" method="post" name="form">
    <input name="id" type="hidden" id="id" value="{id}">
    <input name="i" type="hidden" id="i" value="{i}">
    <input name="temp" type="hidden" id="temp">
    <table class="tabla">
    <tr>
      <th colspan="2" class="tabla" scope="col">Compa&ntilde;&iacute;a</th>
      <th class="tabla" scope="col">Importe</th>
      <th class="tabla" scope="col">Concepto</th>
      <th class="tabla" scope="col">Comentario</th>
      <th class="tabla" scope="col">Balance</th>
      <th class="tabla" scope="col">Tipo</th>
      </tr>
	<tr>
      <td class="tabla"><input name="num_cia" type="text" class="insert" id="num_cia" onFocus="temp.value=this.value" onChange="if (isInt(this,temp)) cambiaCia(this,nombre_cia)" onKeyDown="if (event.keyCode == 13) importe.select()" value="{num_cia}" size="3" maxlength="3"></td>
      <td class="tabla"><input name="nombre_cia" type="text" disabled="true" class="vnombre" id="nombre_cia" value="{nombre_cia}" size="20" maxlength="20"></td>
      <td class="tabla"><input name="importe" type="text" class="rinsert" id="importe" onFocus="temp.value=this.value" onChange="isFloat(this,2,temp)" onKeyDown="if (event.keyCode == 13) comentario.select()" value="{importe}" size="10" maxlength="10"></td>
      <td class="tabla"><select name="cod_gastos" class="insert" id="cod_gastos">
        <!-- START BLOCK : gasto -->
		<option value="{id}" {selected}>{descripcion}</option>
		<!-- END BLOCK : gasto -->
      </select></td>
      <td class="tabla"><input name="comentario" type="text" class="vinsert" id="comentario" onKeyDown="if (event.keyCode == 13) num_cia.select()" value="{comentario}" size="20" maxlength="30"></td>
      <td class="tabla"><input name="bal" type="radio" value="TRUE" {bal_true}>
        Si
          <input name="bal" type="radio" value="FALSE" {bal_false}>
          No</td>
      <td class="tabla"><input name="tipo_mov" type="radio" value="FALSE" {tipo_false}>
          Egreso
          <input name="tipo_mov" type="radio" value="TRUE" {tipo_true}>
          Ingreso</td>
      </tr>
  </table>  <p>
    <input type="button" class="boton" value="Cancelar" onClick="self.close()">
&nbsp;&nbsp;    
<input type="button" class="boton" value="Modificar" onClick="validar()">
  </p>
  </form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
<!--
var form = document.form;
var cia = new Array();
<!-- START BLOCK : cia -->
cia[{num_cia}] = "{nombre_cia}";
<!-- END BLOCK : cia -->

function cambiaCia(num, nombre) {
	if (num.value == "")
		nombre.value = "";
	else if (cia[num.value] != null)
		nombre.value = cia[num.value];
	else {
		alert("La compañía no se encuentra en el catalogo");
		num.value = num.form.temp.value;
		num.select();
	}
}

function validar() {
	if (form.num_cia.value <= 0) {
		alert("Debe especificar la compañía");
		form.num_cia.select();
		return false;
	}
	else if (form.importe.value <= 0) {
		alert("Debe especificar el importe");
		form.importe.select();
		return false;
	}
	else if (confirm("¿Desea actualizar los datos?")) {
		form.submit();
	}
	else {
		form.num_cia.select();
		return false;
	}
}

window.onload = form.num_cia.select();
-->
</script>
<!-- END BLOCK : mod -->
</body>
</html>
