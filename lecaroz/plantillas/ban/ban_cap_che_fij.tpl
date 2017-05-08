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
<td align="center" valign="middle"><p class="title">Captura de Pagos Fijos </p>
  <form action="./ban_cap_che_fij.php" method="post" name="form">
    <input name="temp" type="hidden" id="temp">
    <table class="tabla">
    <tr>
      <th class="tabla" scope="col">Compa&ntilde;&iacute;a</th>
      <th class="tabla" scope="col">Proveedor</th>
      <th class="tabla" scope="col">Gasto</th>
      <th class="tabla" scope="col">Concepto</th>
      <th class="tabla" scope="col">Importe</th>
      <th class="tabla" scope="col">IVA</th>
      <th class="tabla" scope="col">Ret IVA </th>
      <th class="tabla" scope="col">ISR</th>
      <th class="tabla" scope="col">Total</th>
    </tr>
	<!-- START BLOCK : fila -->
    <tr>
      <td class="tabla"><input name="num_cia[]" type="text" class="insert" id="num_cia" onFocus="temp.value=this.value;this.select();" onBlur="if (isInt(this,temp)) cambiaCia(this, nombre_cia[{i}])" onKeyDown="if (event.keyCode == 13) num_proveedor[{i}].select()" size="3" maxlength="3">
        <input name="nombre_cia[]" type="text" disabled="true" class="vnombre" id="nombre_cia" size="20"></td>
      <td class="tabla"><input name="num_proveedor[]" type="text" class="insert" id="num_proveedor" onFocus="temp.value=this.value;this.select();" onBlur="if (isInt(this,temp)) cambiaPro(this, nombre_pro[{i}])" onKeyDown="if (event.keyCode == 13) codgastos[{i}].select()" size="3" maxlength="4">
        <input name="nombre_pro[]" type="text" disabled="true" class="vnombre" id="nombre_pro" size="20"></td>
      <td class="tabla"><input name="codgastos[]" type="text" class="insert" id="codgastos" onFocus="temp.value=this.value;this.select();" onBlur="if (isInt(this,temp)) cambiaGas(this, nombre_gas[{i}])" onKeyDown="if (event.keyCode == 13) concepto[{i}].select()" size="3" maxlength="4">
        <input name="nombre_gas[]" type="text" disabled="true" class="vnombre" id="nombre_gas" size="20"></td>
      <td class="tabla"><input name="concepto[]" type="text" class="vinsert" id="concepto" onFocus="this.select()" onKeyDown="if (event.keyCode == 13) importe[{i}].select()" size="20" maxlength="20"></td>
      <td class="tabla"><input name="importe[]" type="text" class="rinsert" id="importe" onFocus="temp.value=this.value;this.select();" onBlur="validaCampo(this, {i})" onKeyDown="if (event.keyCode == 13) iva[{i}].select()" size="10"></td>
      <td class="tabla"><input name="iva[]" type="text" class="insert" id="iva" onFocus="temp.value=this.value;this.select();" onBlur="validaCampo(this, {i})" onKeyDown="if (event.keyCode == 13) ret_iva[{i}].select()" size="8"></td>
      <td class="tabla"><input name="ret_iva[]" type="text" class="rinsert" id="ret_iva" onFocus="temp.value=this.value;this.select();" onBlur="validaCampo2(this, {i})" onKeyDown="if (event.keyCode == 13) isr[{i}].select()" size="8"></td>
      <td class="tabla"><input name="isr[]" type="text" class="rinsert" id="isr" onFocus="temp.value=this.value;this.select();" onBlur="validaCampo2(this, {i})" onKeyDown="if (event.keyCode == 13) num_cia[{next}].select()" size="8"></td>
      <td class="tabla"><input name="total[]" type="text" class="rnombre" id="total" size="10" readonly="true"></td>
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
var pro = new Array();
var gas = new Array();
<!-- START BLOCK : cia -->
cia[{num_cia}] = "{nombre_cia}";
<!-- END BLOCK : cia -->
<!-- START BLOCK : pro -->
pro[{num_pro}] = "{nombre_pro}";
<!-- END BLOCK : pro -->
<!-- START BLOCK : gas -->
gas[{codgastos}] = "{nombre_gas}";
<!-- END BLOCK : gas -->

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

function cambiaPro(num, nombre) {
	if (num.value == "")
		nombre.value = "";
	else if (pro[num.value] != null)
		nombre.value = pro[num.value];
	else {
		alert("El proveedor no se encuentra en el catalogo");
		num.value = num.form.temp.value;
		num.select();
	}
}

function cambiaGas(num, nombre) {
	if (num.value == "")
		nombre.value = "";
	else if (gas[num.value] != null)
		nombre.value = gas[num.value];
	else {
		alert("El gasto no se encuentra en el catalogo");
		num.value = num.form.temp.value;
		num.select();
	}
}

function validaCampo(campo, i) {
	if (campo.value == "" || campo.value == "0") {
		campo.value = "";
	}
	else {
		var tmp = new oNumero(parseFloat(form.temp.value.replace(",", "")));
		
		if (isNaN(parseFloat(campo.value.replace(",", "")))) {
			alert("Solo se permiten n\u00FAmeros");
			campo.value = form.temp.value == "" || form.temp.value == "0" ? "" : tmp.formato(2, true);
			return false;
		}
		
		var field = parseFloat(campo.value.replace(",", ""));
		
		if (field < 0) {
			alert("El valor del campo no puede ser menor a 0");
			campo.value = form.temp.value == "" || form.temp.value == "0" ? "" : tmp.formato(2, true);
			return false;
		}
		
		tmp = new oNumero(field);
		campo.value = tmp.formato(2, true);
	}
	
	calculaTotal(i);
}

function validaCampo2(campo, i) {
	if (campo.value == "" || campo.value == "0") {
		campo.value = "";
	}
	else {
		var tmp = new oNumero(parseFloat(form.temp.value.replace(",", "")));
		
		if (isNaN(parseFloat(campo.value.replace(",", "")))) {
			alert("Solo se permiten n\u00FAmeros");
			campo.value = form.temp.value == "" || form.temp.value == "0" ? "" : tmp.formato(2, true);
			return false;
		}
		
		var field = parseFloat(campo.value.replace(",", ""));
		
		tmp = new oNumero(field);
		campo.value = tmp.formato(2, true);
	}
	
	calculaTotal(i);
}

function calculaTotal(i) {
	var importe = !isNaN(parseFloat(form.importe[i].value.replace(",", ""))) ? parseFloat(form.importe[i].value.replace(",", "")) : 0;
	var iva = !isNaN(parseFloat(form.iva[i].value.replace(",", ""))) ? parseFloat(form.iva[i].value.replace(",", "")) : 0;
	var ret_iva = !isNaN(parseFloat(form.ret_iva[i].value.replace(",", ""))) ? parseFloat(form.ret_iva[i].value.replace(",", "")) : 0;
	var isr = !isNaN(parseFloat(form.isr[i].value.replace(",", ""))) ? parseFloat(form.isr[i].value.replace(",", "")) : 0;
	
	var total = importe + iva + ret_iva - isr;
	
	if (total != 0) {
		var tmp = new oNumero(total);
		form.total[i].value = tmp.formato(2, true);
		form.total[i].style.color = total > 0 ? "Blue" : "Red";
	}
	else {
		form.total[i].value = "";
	}
}

function validar() {
	var count= 0;
	
	for (i = 0; i < form.num_cia.length; i++) {
		if (form.num_cia[i].value != "" && form.num_proveedor[i].value != "" && form.codgastos[i].value != "" && form.total[i].value != "") {
			var total = !isNaN(parseFloat(form.total[i].value.replace(",", ""))) ? parseFloat(form.total[i].value.replace(",", "")) : 0;
			var importe = !isNaN(parseFloat(form.importe[i].value.replace(",", ""))) ? parseFloat(form.importe[i].value.replace(",", "")) : 0;
			var iva = !isNaN(parseFloat(form.iva[i].value.replace(",", ""))) ? parseFloat(form.iva[i].value.replace(",", "")) : 0;
			var ret_iva = !isNaN(parseFloat(form.ret_iva[i].value.replace(",", ""))) ? parseFloat(form.ret_iva[i].value.replace(",", "")) : 0;
			var isr = !isNaN(parseFloat(form.isr[i].value.replace(",", ""))) ? parseFloat(form.isr[i].value.replace(",", "")) : 0;
			
			if (total < 0) {
				alert("El importe total del cheque no puede ser menor a cero");
				form.importe[i].select();
				return false;
			}
			if (importe < 0) {
				alert("El importe no puede ser menor a cero");
				form.importe[i].select();
				return false;
			}
			if (iva + ret_iva >= importe) {
				alert("El valor de los impuestos no puede ser mayor al importe");
				form.iva[i].select();
				return false;
			}
			if (isr >= importe) {
				alert("El valor del ISR no puede ser mayor al importe");
				form.isr[i].select();
				return false;
			}
			count++;
		}
	}
	
	if (count > 0) {
		if (confirm("¿Son correctos los datos?")) {
			form.submit();
		}
		else {
			form.num_cia[0].select();
		}
	}
	else {
		alert("Debe capturar datos");
		form.num_cia[0].select();
		return false;
	}
}

window.onload = form.num_cia[0].select();
-->
</script>
</body>
</html>
