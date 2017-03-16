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
<!-- START BLOCK : mod -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Modificar Pago Fijo</p>
  <form action="./ban_mod_che_fij.php" method="post" name="form">
    <input name="temp" type="hidden" id="temp">
    <input name="id" type="hidden" id="id" value="{id}"> 
    <input name="i" type="hidden" id="i" value="{i}">
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
    <tr>
      <td class="tabla"><input name="num_cia" type="text" class="insert" id="num_cia" onFocus="temp.value=this.value;this.select();" onBlur="if (isInt(this,temp)) cambiaCia(this, nombre_cia)" onKeyDown="if (event.keyCode == 13) num_proveedor.select()" value="{num_cia}" size="3" maxlength="3">
        <input name="nombre_cia" type="text" class="vnombre" id="nombre_cia" value="{nombre_cia}" size="20" readonly></td>
      <td class="tabla"><input name="num_proveedor" type="text" class="insert" id="num_proveedor" onFocus="temp.value=this.value;this.select();" onBlur="if (isInt(this,temp)) cambiaPro(this, nombre_pro)" onKeyDown="if (event.keyCode == 13) codgastos.select()" value="{num_pro}" size="3" maxlength="4">
        <input name="nombre_pro" type="text" class="vnombre" id="nombre_pro" value="{nombre_pro}" size="20" readonly></td>
      <td class="tabla"><input name="codgastos" type="text" class="insert" id="codgastos" onFocus="temp.value=this.value;this.select();" onBlur="if (isInt(this,temp)) cambiaGas(this, nombre_gas)" onKeyDown="if (event.keyCode == 13) concepto.select()" value="{codgastos}" size="3" maxlength="4">
        <input name="nombre_gas" type="text" class="vnombre" id="nombre_gas" value="{nombre_gas}" size="20" readonly></td>
      <td class="tabla"><input name="concepto" type="text" class="vinsert" id="concepto" onFocus="this.select()" onKeyDown="if (event.keyCode == 13) importe.select()" value="{concepto}" size="20" maxlength="20"></td>
      <td class="tabla"><input name="importe" type="text" class="rinsert" id="importe" onFocus="temp.value=this.value;this.select();" onBlur="validaCampo(this);" onChange=" calculaIVA();" onKeyDown="if (event.keyCode == 13) iva.select()" value="{importe}" size="8"></td>
      <td class="tabla"><input name="iva" type="text" class="insert" id="iva" onFocus="temp.value=this.value;this.select();" onBlur="validaCampo(this)" onKeyDown="if (event.keyCode == 13) ret_iva.select()" value="{iva}" size="8"></td>
      <td class="tabla"><input name="ret_iva" type="text" class="rinsert" id="ret_iva" onFocus="temp.value=this.value;this.select();" onBlur="validaCampo2(this)" onKeyDown="if (event.keyCode == 13) isr.select()" value="{ret_iva}" size="8"></td>
      <td class="tabla"><input name="isr" type="text" class="rinsert" id="isr" onFocus="temp.value=this.value;this.select();" onBlur="validaCampo2(this)" onKeyDown="if (event.keyCode == 13) num_cia.select()" value="{isr}" size="8"></td>
      <td class="tabla"><input name="total" type="text" class="rnombre" id="total" value="{total}" size="8" readonly></td>
    </tr>
  </table>  
  <p>
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

function validaCampo(campo) {
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
	
	calculaTotal();
}

function validaCampo2(campo) {
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
	
	calculaTotal();
}

function calculaIVA() {
	var importe = !isNaN(parseFloat(form.importe.value.replace(",", ""))) ? parseFloat(form.importe.value.replace(",", "")) : 0;
	var iva = !isNaN(parseFloat(form.iva.value.replace(",", ""))) ? parseFloat(form.iva.value.replace(",", "")) : 0;
	var ret_iva = !isNaN(parseFloat(form.ret_iva.value.replace(",", ""))) ? parseFloat(form.ret_iva.value.replace(",", "")) : 0;
	var isr = !isNaN(parseFloat(form.isr.value.replace(",", ""))) ? parseFloat(form.isr.value.replace(",", "")) : 0;
	
	if (iva > 0) {
		iva = Math.round((importe * 0.16) * 100) / 100;
	}
	
	if (ret_iva != 0) {
		ret_iva = Math.round((importe * 0.1066666667) * 100) / 100;
	}
	
	if (isr != 0) {
		isr = Math.round((importe * 0.10) * 100) / 100;
	}
	
	var tmp = new oNumero(iva);
	form.iva.value = iva > 0 ? tmp.formato(2, true) : '';
	
	var tmp = new oNumero(ret_iva);
	form.ret_iva.value = ret_iva > 0 ? '-' + tmp.formato(2, true) : '';
	
	var tmp = new oNumero(isr);
	form.isr.value = isr > 0 ? tmp.formato(2, true) : '';
	
	var total = importe + iva - ret_iva - isr;
	
	var tmp = new oNumero(total);
	form.total.value = total != 0 ? '-' + tmp.formato(2, true) : '';
	form.total.style.color = total > 0 ? "Blue" : "Red";
}

function calculaTotal() {
	var importe = !isNaN(parseFloat(form.importe.value.replace(",", ""))) ? parseFloat(form.importe.value.replace(",", "")) : 0;
	var iva = !isNaN(parseFloat(form.iva.value.replace(",", ""))) ? parseFloat(form.iva.value.replace(",", "")) : 0;
	var ret_iva = !isNaN(parseFloat(form.ret_iva.value.replace(",", ""))) ? parseFloat(form.ret_iva.value.replace(",", "")) : 0;
	var isr = !isNaN(parseFloat(form.isr.value.replace(",", ""))) ? parseFloat(form.isr.value.replace(",", "")) : 0;
	
	var total = importe + iva + ret_iva - isr;
	
	if (total != 0) {
		var tmp = new oNumero(total);
		form.total.value = tmp.formato(2, true);
		form.total.style.color = total > 0 ? "Blue" : "Red";
	}
	else {
		form.total.value = "";
	}
}

function validar() {
	var total = !isNaN(parseFloat(form.total.value.replace(",", ""))) ? parseFloat(form.total.value.replace(",", "")) : 0;
	var importe = !isNaN(parseFloat(form.importe.value.replace(",", ""))) ? parseFloat(form.importe.value.replace(",", "")) : 0;
	var iva = !isNaN(parseFloat(form.iva.value.replace(",", ""))) ? parseFloat(form.iva.value.replace(",", "")) : 0;
	var ret_iva = !isNaN(parseFloat(form.ret_iva.value.replace(",", ""))) ? parseFloat(form.ret_iva.value.replace(",", "")) : 0;
	var isr = !isNaN(parseFloat(form.isr.value.replace(",", ""))) ? parseFloat(form.isr.value.replace(",", "")) : 0;
	
	if (total < 0) {
		alert("El importe total del cheque no puede ser menor a cero");
		form.importe.select();
		return false;
	}
	else if (importe < 0) {
		alert("El importe no puede ser menor a cero");
		form.importe.select();
		return false;
	}
	else if (iva + ret_iva >= importe) {
		alert("El valor de los impuestos no puede ser mayor al importe");
		form.iva.select();
		return false;
	}
	else if (isr >= importe) {
		alert("El valor del ISR no puede ser mayor al importe");
		form.isr.select();
		return false;
	}
	else if (confirm("¿Son correctos los datos?")) {
		form.submit();
	}
	else {
		form.num_cia.select();
	}
}

window.onload = form.num_cia.select();
-->
</script>
<!-- END BLOCK : mod -->
<!-- START BLOCK : cerrar -->
<script language="javascript" type="text/javascript">
<!--
function cerrar(i) {
	var form = window.opener.document.form;
	
	if (form.id.length == undefined) {
		form.num_cia.value = "{num_cia} {nombre_cia}";
		form.num_pro.value = "{num_pro} {nombre_pro}";
		form.codgastos.value = "{codgastos} {nombre_gas}";
		form.concepto.value = "{concepto}";
		form.importe.value = "{importe}";
		form.iva.value = "{iva}";
		form.ret_iva.value = "{ret_iva}";
		form.isr.value = "{isr}";
		form.total.value = "{total}";
	}
	else {
		form.num_cia[i+1].value = "{num_cia}";
		form.num_pro[i+1].value = "{num_pro}";
		form.codgastos[i+1].value = "{codgastos}";
		form.concepto[i].value = "{concepto}";
		form.importe[i].value = "{importe}";
		form.iva[i].value = "{iva}";
		form.ret_iva[i].value = "{ret_iva}";
		form.isr[i].value = "{isr}";
		form.total[i].value = "{total}";
	}
	self.close();
}

window.onload = cerrar({i});
-->
</script>
<!-- END BLOCK : cerrar -->
</body>
</html>
