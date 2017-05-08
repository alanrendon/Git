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
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Captura de Gastos de Caja Fijos</p>
  <form action="./bal_gas_caj_fij.php" method="post" name="form">
    <input name="temp" type="hidden" id="temp">
    <table class="tabla">
      <tr>
        <th class="tabla" scope="col">Concepto</th>
        <th class="tabla" scope="col">Balance</th>
        <th class="tabla" scope="col">Tipo</th>
      </tr>
      <tr>
        <td class="tabla"><select name="cod_gastos_all" class="insert" id="cod_gastos_all" onChange="cambiaGasto(this.form)">
        <!-- START BLOCK : gasto_all -->
		<option value="{id}">{descripcion}</option>
		<!-- END BLOCK : gasto_all -->
      </select></td>
        <td class="tabla"><select name="bal_all" class="insert" id="bal_all" onChange="cambiaBal(this.form)">
          <option value="TRUE" selected>SI</option>
          <option value="FALSE">NO</option>
        </select></td>
        <td class="tabla"><select name="tipo_mov_all" class="insert" id="tipo_mov_all" onChange="cambiaTipo(this.form)">
          <option value="FALSE" selected>EGRESO</option>
          <option value="FALSE">INGRESO</option>
        </select></td>
      </tr>
    </table>    
    <br>
    <table class="tabla">
    <tr>
      <th colspan="2" class="tabla" scope="col">Compa&ntilde;&iacute;a</th>
      <th class="tabla" scope="col">Importe</th>
      <th class="tabla" scope="col">Concepto</th>
      <th class="tabla" scope="col">Comeentario</th>
      <th class="tabla" scope="col">Balance</th>
      <th class="tabla" scope="col">Tipo</th>
      </tr>
    <!-- START BLOCK : fila -->
	<tr>
      <td class="tabla"><input name="num_cia[]" type="text" class="insert" id="num_cia" onFocus="temp.value=this.value" onChange="if (isInt(this,temp)) cambiaCia(this,nombre_cia[{i}])" onKeyDown="if (event.keyCode == 13) {
if (num_cia.length == undefined) importe.select();
else importe[{i}].select();
}" size="3" maxlength="3"></td>
      <td class="tabla"><input name="nombre_cia[]" type="text" disabled="true" class="vnombre" id="nombre_cia" size="20" maxlength="20"></td>
      <td class="tabla"><input name="importe[]" type="text" class="rinsert" id="importe" onFocus="temp.value=this.value" onChange="isFloat(this,2,temp)" onKeyDown="if (event.keyCode == 13) {
if (num_cia.length == undefined) comentario.select();
else comentario[{i}].select();
}" size="10" maxlength="10"></td>
      <td class="tabla"><select name="cod_gastos[]" class="insert" id="cod_gastos">
        <!-- START BLOCK : gasto -->
		<option value="{id}">{descripcion}</option>
		<!-- END BLOCK : gasto -->
      </select></td>
      <td class="tabla"><input name="comentario[]" type="text" class="vinsert" id="comentario" onKeyDown="if (event.keyCode == 13) {
if (num_cia.length == undefined) num_cia.select();
else num_cia[{next}].select();
}" size="30" maxlength="30"></td>
      <td class="tabla"><select name="bal[]" class="insert" id="bal">
        <option value="TRUE" selected>SI</option>
        <option value="FALSE">NO</option>
      </select></td>
      <td class="tabla"><select name="tipo_mov[]" class="insert" id="tipo_mov">
        <option value="FALSE" selected>EGRESO</option>
        <option value="TRUE">INGRESO</option>
      </select></td>
      </tr>
	  <!-- END BLOCK : fila -->
  </table>  
    <p>
    <input type="button" class="boton" value="Siguiente" onClick="validar()">
  </p></form></td>
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

function cambiaGasto(form) {
	for (i = 0; i < form.num_cia.length; i++) {
		form.cod_gastos[i].selectedIndex = form.cod_gastos_all.selectedIndex;
	}
}

function cambiaBal(form) {
	for (i = 0; i < form.num_cia.length; i++) {
		form.bal[i].selectedIndex = form.bal_all.selectedIndex;
	}
}

function cambiaTipo(form) {
	for (i = 0; i < form.num_cia.length; i++) {
		form.tipo_mov[i].selectedIndex = form.tipo_mov_all.selectedIndex;
	}
}

function validar() {
	if (confirm("¿Son correctos los datos?")) {
		form.submit();
	}
	else {
		if (form.num_cia.length == undefined) {
			form.num_cia.select();
		}
		else {
			form.num_cia[0].select();
		}
	}
}

window.onload = form.num_cia.length == undefined ? form.num_cia.select() : form.num_cia[0].select();
-->
</script>
</body>
</html>
