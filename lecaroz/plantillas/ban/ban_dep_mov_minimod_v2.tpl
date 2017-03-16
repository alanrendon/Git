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
<td align="center" valign="middle"><p class="title">Modificar Movimiento</p>
  <form action="./ban_dep_mov_minimod_v2.php" method="post" name="form">
    <input name="id" type="hidden" id="id" value="{id}">
    <input name="cuenta" type="hidden" id="cuenta" value="{cuenta}">
    <input name="num_cia" type="hidden" id="num_cia" value="{num_cia}"> 
    <input name="tmp" type="hidden" id="tmp">
	<table class="tabla">
      <tr>
        <th class="tabla" scope="col">Compa&ntilde;&iacute;a</th>
      </tr>
      <tr>
        <th class="tabla" style="font-size:12pt; ">{num_cia} - {nombre} </th>
      </tr>
    </table>   
    <br>   
    <table class="tabla">
    <tr>
      <th class="tabla" scope="col">Fecha</th>
      <th class="tabla" scope="col">C&oacute;digo</th>
      <th class="tabla" scope="col">Concepto</th>
      <th class="tabla" scope="col">Importe</th>
    </tr>
    <tr>
      <td class="tabla"><input name="fecha_con" type="hidden" id="fecha_con" value="{fecha_con}">
      <input name="fecha" type="text" class="insert" id="fecha" onChange="actualiza_fecha(this)" onKeyDown="if (event.keyCode == 13) concepto.select()" value="{fecha}" size="10" maxlength="10"></td>
      <td class="tabla"><select name="cod_mov" class="insert" id="cod_mov">
        <!-- START BLOCK : cod_mov -->
		<option value="{cod_mov}" {selected}>{cod_mov} {descripcion}</option>
		<!-- END BLOCK : cod_mov -->
      </select></td>
      <td class="tabla"><input name="concepto_ant" type="hidden" id="concepto_ant" value="{concepto}">      	<input name="concepto" type="text" class="vinsert" id="concepto" onKeyDown="if (event.keyCode == 13) fecha.select()" value="{concepto}" size="50" maxlength="200"></td>
      <td class="rtabla"><input name="importe" type="text" class="rnombre" id="importe" value="{importe}" size="10" readonly="true"></td>
    </tr>
  </table>  
    <!-- START BLOCK : tarjeta -->
	<br>
    <table class="tabla">
      <!-- START BLOCK : cargo -->
	  <tr>
        <th class="vtabla" scope="row">Cargo</th>
        <td class="vtabla"><select name="cod_mov_car[]" class="insert" id="cod_mov_car">
          <option value=""></option>
		  <!-- START BLOCK : cod_cargo -->
		  <option value="{cod}" {selected}>{cod} {nombre}</option>
		  <!-- END BLOCK : cod_cargo -->
        </select></td>
        <td class="vtabla"><input name="cargo[]" type="text" class="rinsert" id="cargo" style="color: #CC0000;" onFocus="tmp.value=this.value;this.select()" onChange="if (input_format(this,2,true)) calculaTotal()" onKeyDown="if (event.keyCode == 13) {campo}.select()" value="{cargo}" size="10"></td>
      </tr>
	  <!-- END BLOCK : cargo -->
      <!-- START BLOCK : abono -->
	  <tr>
        <th class="vtabla" scope="row">Abono</th>
        <td class="vtabla"><select name="cod_mov_abo[]" class="insert" id="cod_mov_abo">
          <!-- START BLOCK : cod_abono -->
		  <option value="{cod}" {selected}>{cod} {nombre}</option>
		  <!-- END BLOCK : cod_abono -->
        </select></td>
        <td class="vtabla"><input name="abono[]" type="text" class="rinsert" id="abono" style="color: #0000CC;" onFocus="tmp.value=this.value;this.select()" onChange="if (input_format(this,2,true)) calculaTotal()" onKeyDown="if (event.keyCode == 13) {campo}.select()" value="{abono}" size="10"></td>
      </tr>
	  <!-- END BLOCK : abono -->
      <tr>
        <th colspan="2" class="rtabla" scope="row">Total</th>
        <th class="vtabla"><input name="total" type="text" class="rnombre" id="total" value="{total}" size="10" readonly="true" style="color: #{color};"></th>
      </tr>
    </table>
	<!-- END BLOCK : tarjeta -->
    <p>
    <input type="button" class="boton" value="Cancelar" onClick="self.close()">
&nbsp;&nbsp;
<input type="button" class="boton" value="Modificar" onClick="validar(this.form)"> 
</p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
<!--
var f = document.form;

function calculaTotal() {
	var cargos = 0, abonos = 0, total = 0;
	
	for (var i = 0; i < f.cargo.length; i++)
		cargos += get_val(f.cargo[i]);
	
	for (i = 0; i < f.abono.length; i++)
		abonos += get_val(f.abono[i]);
	
	total = abonos - cargos;
	
	f.total.value = number_format(total, 2);
	f.total.style.color = total > 0 ? "0000CC" : "CC0000";
}

function validar() {
	var ok = true;
	
	if (f.cargo)
		for (var i = 0; i < f.cargo.length; i++)
			if (get_val(f.cargo[i]) > 0 && f.cod_mov_car[i].value == "")
				ok = false;
	
	if (f.fecha.value.length < 8) {
		alert("Debe especificar la fecha de movimiento");
		f.fecha.focus();
		return false;
	}
	else if (f.concepto.value.length < 3) {
		alert("Debe especificar el concepto del movimiento");
		f.concepto.focus();
		return false;
	}
	else if (!ok) {
		alert("Debe especificar todos los códigos de los cargos capturados");
		f.cargo[0].select();
		return false;
	}
	else if (f.total && get_val(f.total) != 0 && get_val(f.importe) < get_val(f.total)) {
		alert("El total desglosado debe ser menor o igual al importe del movimiento");
		f.cargo[0].select();
		return false;
	}
	else if (confirm("¿Desea modificar y conciliar el movimiento?")) {
		f.submit();
	}
	else {
		f.fecha.select();
	}
}

window.onload = document.form.fecha.select();
-->
</script>
<!-- END BLOCK : datos -->
<!-- START BLOCK : cerrar -->
<script language="javascript" type="text/javascript">
<!--
function cerrar() {
	var mainDoc = window.opener.opener.document;
	var openerWin = window.opener;
	
	mainDoc.location = mainDoc.location + "#{num_cia}";
	mainDoc.location.reload();
	openerWin.close();
	self.close();
}

window.onload = cerrar();
-->
</script>
<!-- END BLOCK : cerrar -->
</body>
</html>
