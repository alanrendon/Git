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
<td align="center" valign="middle"><p class="title">Captura de Reservas</p>
  <form action="./bal_cap_res_v2.php" method="post" name="form">
    <input name="tmp" type="hidden" id="tmp">
    <table class="tabla">
    <tr>
      <th class="vtabla" scope="row">Compa&ntilde;&iacute;a</th>
      <td class="vtabla"><input name="num_cia" type="text" class="insert" id="num_cia" onFocus="tmp.value=this.value;this.select()" onChange="if (isInt(this,tmp)) cambiaCia(this,nombre)" onKeyDown="if (event.keyCode == 13) anio.select()" size="3">
        <input name="nombre" type="text" disabled="true" class="vnombre" id="nombre" size="30"></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">A&ntilde;o</th>
      <td class="vtabla"><input name="anio" type="text" class="insert" id="anio" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) importe_gral.select()" value="{anio}" size="4" maxlength="4"></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">C&oacute;digo</th>
      <td class="vtabla"><select name="cod_reserva" class="insert" id="cod_reserva">
        <!-- START BLOCK : cod -->
		<option value="{cod}">{cod} {nombre}</option>
		<!-- END BLOCK : cod -->
      </select></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Importe</th>
      <td class="vtabla"><input name="importe_gral" type="text" class="rinsert" id="importe_gral" onFocus="tmp.value=this.value;this.select()" onChange="if (input_format(this,2,true)) updateInputs()" onKeyDown="if (event.keyCode == 13) num_cia.select()" size="10"></td>
    </tr>
  </table>  
  <br>
  <table class="tabla">
    <tr>
      <th class="tabla" scope="col">Mes</th>
      <th class="tabla" scope="col">Importe</th>
    </tr>
    <tr>
      <td class="vtabla"><input name="mes[]" type="checkbox" id="mes" onClick="updateInputs()" value="1" checked>
        Enero</td>
      <td class="tabla"><input name="importe[]" type="text" class="rnombre" id="importe" value="0.00" size="10" readonly="true"></td>
    </tr>
    <tr>
      <td class="vtabla"><input name="mes[]" type="checkbox" id="mes" onClick="updateInputs()" value="2" checked>
        Febrero</td>
      <td class="tabla"><input name="importe[]" type="text" class="rnombre" id="importe" value="0.00" size="10" readonly="true"></td>
    </tr>
    <tr>
      <td class="vtabla"><input name="mes[]" type="checkbox" id="mes" onClick="updateInputs()" value="3" checked>
        Marzo</td>
      <td class="tabla"><input name="importe[]" type="text" class="rnombre" id="importe" value="0.00" size="10" readonly="true"></td>
    </tr>
    <tr>
      <td class="vtabla"><input name="mes[]" type="checkbox" id="mes" onClick="updateInputs()" value="4" checked>
        Abril</td>
      <td class="tabla"><input name="importe[]" type="text" class="rnombre" id="importe" value="0.00" size="10" readonly="true"></td>
    </tr>
    <tr>
      <td class="vtabla"><input name="mes[]" type="checkbox" id="mes" onClick="updateInputs()" value="5" checked>
        Mayo</td>
      <td class="tabla"><input name="importe[]" type="text" class="rnombre" id="importe" value="0.00" size="10" readonly="true"></td>
    </tr>
    <tr>
      <td class="vtabla"><input name="mes[]" type="checkbox" id="mes" onClick="updateInputs()" value="6" checked>
        Junio</td>
      <td class="tabla"><input name="importe[]" type="text" class="rnombre" id="importe" value="0.00" size="10" readonly="true"></td>
    </tr>
    <tr>
      <td class="vtabla"><input name="mes[]" type="checkbox" id="mes" onClick="updateInputs()" value="7" checked>
        Julio</td>
      <td class="tabla"><input name="importe[]" type="text" class="rnombre" id="importe" value="0.00" size="10" readonly="true"></td>
    </tr>
    <tr>
      <td class="vtabla"><input name="mes[]" type="checkbox" id="mes" onClick="updateInputs()" value="8" checked>
        Agosto</td>
      <td class="tabla"><input name="importe[]" type="text" class="rnombre" id="importe" value="0.00" size="10" readonly="true"></td>
    </tr>
    <tr>
      <td class="vtabla"><input name="mes[]" type="checkbox" id="mes" onClick="updateInputs()" value="9" checked>
        Septiembre</td>
      <td class="tabla"><input name="importe[]" type="text" class="rnombre" id="importe" value="0.00" size="10" readonly="true"></td>
    </tr>
    <tr>
      <td class="vtabla"><input name="mes[]" type="checkbox" id="mes" onClick="updateInputs()" value="10" checked>
        Octubre</td>
      <td class="tabla"><input name="importe[]" type="text" class="rnombre" id="importe" value="0.00" size="10" readonly="true"></td>
    </tr>
    <tr>
      <td class="vtabla"><input name="mes[]" type="checkbox" id="mes" onClick="updateInputs()" value="11" checked>
        Noviembre</td>
      <td class="tabla"><input name="importe[]" type="text" class="rnombre" id="importe" value="0.00" size="10" readonly="true"></td>
    </tr>
    <tr>
      <td class="vtabla"><input name="mes[]" type="checkbox" id="mes" onClick="updateInputs()" value="12">
        Diciembre</td>
      <td class="tabla"><input name="importe[]" type="text" class="rnombre" id="importe" value="0.00" size="10" readonly="true"></td>
    </tr>
    <tr>
      <th class="rtabla">Total</th>
      <th class="tabla"><input name="total" type="text" class="rnombre" id="total" value="0.00" size="10"></th>
    </tr>
  </table>  <p>
    <input type="button" class="boton" value="Siguiente" onClick="validar()">
  </p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
<!--
var form = document.form, cia = new Array();

<!-- START BLOCK : cia -->
cia[{num_cia}] = "{nombre}";
<!-- END BLOCK : cia -->

function cambiaCia(num, nombre) {
	if (num.value == "") {
		nombre.value = "";
	}
	else if (cia[num.value] != null) {
		nombre.value = cia[num.value];
	}
	else {
		alert("La compañía no se encuentra en el catalogo");
		num.value = num.form.tmp.value;
		num.select();
	}
}

function updateInputs() {
	for (var i = 0; i < form.importe.length; i++)
		if (form.mes[i].checked)
			form.importe[i].value = form.importe_gral.value != "" ? form.importe_gral.value : "0.00";
		else
			form.importe[i].value = "0.00";
	
	total();
}

function total() {
	var tmp, total = 0;
	
	for (var i = 0; i < form.importe.length; i++)
		total += !isNaN(parseFloat(form.importe[i].value.replace(",", ""))) ? parseFloat(form.importe[i].value.replace(",", "")) : 0;
	
	form.total.value = number_format(total, 2);
}

function validar() {
	var meses = 0;
	
	for (var i = 0; i < form.mes.length; i++)
		meses += form.mes[i].checked ? 1 : 0;
	
	if (form.num_cia.value <= 0) {
		alert("Debe especificar la compañía");
		form.num_cia.select();
		return false;
	}
	else if ((!isNaN(parseFloat(form.importe_gral.value.replace(",", ""))) ? parseFloat(form.importe_gral.value.replace(",", "")) : 0) <= 0) {
		alert("Debe especificar el importe de la reserva");
		form.importe_gral.select();
		return false;
	}
	else if (form.anio.value <= 0) {
		alert("Debe especificar el año de la reserva");
		form.anio.select();
		return false;
	}
	else if (meses == 0) {
		alert("Debe seleccionar al menos un mes");
		return false;
	}
	else if (confirm("¿Son correctos los datos?"))
		form.submit();
	else
		return false;
}

window.onload = form.num_cia.select();
-->
</script>
</body>
</html>
