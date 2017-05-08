<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Untitled Document</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../../styles/tablas.css" rel="stylesheet" type="text/css">
<link href="file:///C|/Documents%20and%20Settings/John%20Talbain/Escritorio/Lecaroz/styles/pages.css" rel="stylesheet" type="text/css">
</head>

<body>
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Captura Manual de Cheques</p>
  <form action="./ban_che_man_v2.php" method="post" name="form">
    <input name="tmp" type="hidden" id="tmp">
    <table class="tabla">
    <tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
      <th class="vtabla" scope="row">Cuenta</th>
      <td colspan="7" class="vtabla"><select name="cuenta" class="insert" id="cuenta" onChange="cambiaSaldo()">
        <option value="1" {cuenta1}>BANORTE</option>
        <option value="2" selected {cuenta2}>SANTANDER</option>
      </select></td>
      </tr>
    <tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
      <th class="vtabla" scope="row">Compa&ntilde;&iacute;a</th>
      <td colspan="5" class="vtabla"><input name="num_cia" type="text" class="insert" id="num_cia" onFocus="tmp.value=this.value;this.select();" onChange="if (isInt(this,tmp)) cambiaCia(this,nombre_cia)" onKeyDown="if (event.keyCode == 13) fecha.select()" value="{num_cia}" size="3" maxlength="3">
        <input name="nombre_cia" type="text" class="vnombre" id="nombre_cia" value="{nombre_cia}" size="50" readonly="true">        </td>
      <th class="vtabla">Saldo</th>
      <td class="vtabla"><input name="saldo" type="text" class="rnombre" id="saldo" style="width: 100%;" value="{saldo}" size="12" readonly="true"></td>
    </tr>
    <tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
      <th class="vtabla" scope="row">Fecha</th>
      <td colspan="7" class="vtabla"><input name="fecha" type="text" class="insert" id="fecha" onChange="actualiza_fecha(this)" onKeyDown="if (event.keyCode == 13) num_pro.select()" value="{fecha}" size="10" maxlength="10"></td>
    </tr>
    <tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
      <th class="vtabla" scope="row">Proveedor</th>
      <td colspan="7" class="vtabla"><input name="num_pro" type="text" class="insert" id="num_pro" onFocus="tmp.value=this.value;this.select();" onChange="if (isInt(this,tmp)) cambiaPro(this,nombre_pro)" onKeyDown="if (event.keyCode == 13) concepto.select()" value="{num_pro}" size="3" maxlength="4">
        <input name="nombre_pro" type="text" class="vnombre" id="nombre_pro" style="width: auto;" value="{nombre_pro}" size="68" readonly="true"></td>
    </tr>
    <tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
      <th class="vtabla" scope="row">Concepto</th>
      <td colspan="7" class="vtabla"><input name="concepto" type="text" class="vinsert" id="concepto" style="width: 100%;" onKeyDown="if (event.keyCode == 13) codgastos.select()" value="{concepto}" size="50" maxlength="200"></td>
    </tr>
    <tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
      <th class="vtabla" scope="row">C&oacute;digo de Gasto</th>
      <td colspan="7" class="vtabla"><input name="codgastos" type="text" class="insert" id="codgastos" onFocus="tmp.value=this.value;this.select();" onChange="if (isInt(this,tmp)) cambiaGasto(this,nombre_gasto)" onKeyDown="if (event.keyCode == 13) num_fact[0].select()" value="{codgastos}" size="3" maxlength="4">
        <input name="nombre_gasto" type="text" class="vnombre" id="nombre_gasto" style="width: auto;" value="{nombre_gasto}" size="68" readonly="true"></td>
    </tr>
    <tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
      <th rowspan="5" class="vtabla" scope="row">Facturas</th>
      <th class="vtabla">N&uacute;mero</th>
      <td class="vtabla"><input name="num_fact[]" type="text" class="insert" id="num_fact" style="width: 100%;" onFocus="tmp.value=this.value;this.select();" onChange="isInt(this,temp)" onKeyDown="if (event.keyCode == 13) importe[0].select()" value="{num_fact1}" size="10"></td>
      <th class="vtabla">Importe</th>
      <td class="vtabla"><input name="importe[]" type="text" class="rinsert" id="importe" style="width: 100%;" onFocus="tmp.value=this.value;this.select()" onBlur="totalFac(0)" onKeyDown="if (event.keyCode == 13) num_fact[1].select()" value="{importe0}" size="10"></td>
      <th class="vtabla"><input name="iva0" type="checkbox" id="iva0" value="15" onClick="totalFac(0)" {iva0}>
        IVA</th>
      <th class="vtabla">Total</th>
      <th class="vtabla"><input name="total[]" type="text" class="rnombre" id="total" style="width: 100%;" value="{total0}" size="10" readonly="true"></th>
    </tr>
	<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
      <th class="vtabla">N&uacute;mero</th>
      <td class="vtabla"><input name="num_fact[]" type="text" class="insert" id="num_fact" style="width: 100%;" onFocus="tmp.value=this.value;this.select();" onChange="isInt(this,temp)" onKeyDown="if (event.keyCode == 13) importe[1].select()" value="{num_fact2}" size="10"></td>
      <th class="vtabla">Importe</th>
      <td class="vtabla"><input name="importe[]" type="text" class="rinsert" id="importe" style="width: 100%;" onFocus="tmp.value=this.value;this.select()" onBlur="totalFac(1)" onKeyDown="if (event.keyCode == 13) num_fact[2].select()" value="{importe1}" size="10"></td>
      <th class="vtabla"><input name="iva1" type="checkbox" id="iva1" value="15" onClick="totalFac(1)" {iva1}>
        IVA</th>
      <th class="vtabla">Total</th>
      <th class="vtabla"><input name="total[]" type="text" class="rnombre" id="total" style="width: 100%;" value="{total1}" size="10" readonly="true"></th>
    </tr>
	<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
      <th class="vtabla">N&uacute;mero</th>
      <td class="vtabla"><input name="num_fact[]" type="text" class="insert" id="num_fact" style="width: 100%;" onFocus="tmp.value=this.value;this.select();" onChange="isInt(this,temp)" onKeyDown="if (event.keyCode == 13) importe[2].select()" value="{num_fact2}" size="10"></td>
      <th class="vtabla">Importe</th>
      <td class="vtabla"><input name="importe[]" type="text" class="rinsert" id="importe" style="width: 100%;" onFocus="tmp.value=this.value;this.select()" onBlur="totalFac(2)" onKeyDown="if (event.keyCode == 13) num_fact[3].select()" value="{importe2}" size="10"></td>
      <th class="vtabla"><input name="iva2" type="checkbox" id="iva2" value="15" onClick="totalFac(2)" {iva2}>
        IVA</th>
      <th class="vtabla">Total</th>
      <th class="vtabla"><input name="total[]" type="text" class="rnombre" id="total" style="width: 100%;" value="{total2}" size="10" readonly="true"></th>
    </tr>
	<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
      <th class="vtabla">N&uacute;mero</th>
      <td class="vtabla"><input name="num_fact[]" type="text" class="insert" id="num_fact" style="width: 100%;" onFocus="tmp.value=this.value;this.select();" onChange="isInt(this,temp)" onKeyDown="if (event.keyCode == 13) importe[3].select()" value="{num_fact3}" size="10"></td>
      <th class="vtabla">Importe</th>
      <td class="vtabla"><input name="importe[]" type="text" class="rinsert" id="importe" style="width: 100%;" onFocus="tmp.value=this.value;this.select()" onBlur="totalFac(3)" onKeyDown="if (event.keyCode == 13) num_fact[4].select()" value="{importe3}" size="10"></td>
      <th class="vtabla"><input name="iva3" type="checkbox" id="iva3" value="15" onClick="totalFac(3)" {iva3}>
        IVA</th>
      <th class="vtabla">Total</th>
      <th class="vtabla"><input name="total[]" type="text" class="rnombre" id="total" style="width: 100%;" value="{total3}" size="10" readonly="true"></th>
    </tr>
	<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
      <th class="vtabla">N&uacute;mero</th>
      <td class="vtabla"><input name="num_fact[]" type="text" class="insert" id="num_fact" style="width: 100%;" onFocus="tmp.value=this.value;this.select();" onChange="isInt(this,temp)" onKeyDown="if (event.keyCode == 13) importe[4].select()" value="{num_fact4}" size="10"></td>
      <th class="vtabla">Importe</th>
      <td class="vtabla"><input name="importe[]" type="text" class="rinsert" id="importe" style="width: 100%;" onFocus="tmp.value=this.value;this.select()" onBlur="totalFac(4)" onKeyDown="if (event.keyCode == 13) num_cia.select()" value="{importe4}" size="10"></td>
      <th class="vtabla"><input name="iva4" type="checkbox" id="iva4" value="15" onClick="totalFac(4)" {iva4}>
        IVA</th>
      <th class="vtabla">Total</th>
      <th class="vtabla"><input name="total[]" type="text" class="rnombre" id="total" style="width: 100%;" value="{total4}" size="10" readonly="true"></th>
    </tr>
    <tr>
      <th colspan="7" class="vtabla" scope="row" style="font-size: 12pt;">Total Cheque </th>
      <th class="vtabla"><input name="total_cheque" type="text" class="rnombre" id="total[]" style="width: 100%; font-size: 12pt;" value="{total_cheque}" size="10" readonly="true"></th>
    </tr>
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
var saldo1 = new Array();
var saldo2 = new Array();
var pro = new Array();
var gasto = new Array();

<!-- START BLOCK : cia -->
cia[{num_cia}] = "{nombre}";
<!-- END BLOCK : cia -->
<!-- START BLOCK : saldo1 -->
saldo1[{num_cia}] = "{saldo}";
<!-- END BLOCK : saldo1 -->
<!-- START BLOCK : saldo2 -->
saldo2[{num_cia}] = "{saldo}";
<!-- END BLOCK : saldo2 -->
<!-- START BLOCK : pro -->
pro[{num_pro}] = "{nombre}";
<!-- END BLOCK : pro -->
<!-- START BLOCK : gasto -->
gasto[{codgastos}] = "{descripcion}";
<!-- END BLOCK : gasto -->

function cambiaCia(num, nombre) {
	var saldo = eval("saldo" + form.cuenta.value);
	
	if (num.value == "") {
		nombre.value = "";
		form.saldo.value = "";
	}
	else if (cia[num.value] != null) {
		nombre.value = cia[num.value];
		if (saldo[num.value] != null) {
			form.saldo.value = saldo[num.value];
		}
		else {
			form.saldo.value = "0.00";
		}
	}
	else {
		alert("La compañía no se encuentra en el catalogo");
		num.value = num.form.tmp.value;
		num.select();
	}
}

function cambiaSaldo() {
	var saldo = eval("saldo" + form.cuenta.value);
	
	if (form.num_cia.value == "") {
		return false;
	}
	else {
		if (saldo[form.num_cia.value] != null) {
			form.saldo.value = saldo[form.num_cia.value];
		}
		else {
			form.saldo.value = "0.00";
		}
	}
}

function cambiaPro(num, nombre) {
	if (num.value == "")
		nombre.value = "";
	else if (pro[num.value] != null)
		nombre.value = pro[num.value];
	else {
		alert("El proveedor no se encuentra en el catalogo");
		num.value = num.form.tmp.value;
		num.select();
	}
}

function cambiaGasto(num, nombre) {
	if (num.value == "")
		nombre.value = "";
	else if (gasto[num.value] != null)
		nombre.value = gasto[num.value];
	else {
		alert("El código de gasto no se encuentra en el catalogo");
		num.value = num.form.tmp.value;
		num.select();
	}
}

function totalFac(i) {
	if (form.importe[i].value == "" || form.importe[i].value == 0) {
		form.importe[i].value = "";
	}
	else {
		if (isNaN(parseFloat(form.importe[i].value.replace(",", "")))) {
			alert("Solo se permiten n\u00FAmeros");
			form.importe[i].value = form.tmp.value;
			return false;
		}
		
		var importe = parseFloat(form.importe[i].value.replace(",", ""));
		
		if (importe < 0) {
			alert("El importe de la factura no puede ser menor a cero");
			form.importe[i].value = form.tmp.value;
			return false;
		}
		
		var iva = form.eval("iva" + i).checked ? 1.15 : 1;
		var total = importe * iva;
		
		tmp = new oNumero(importe);
		form.importe[i].value = tmp.formato(2, true);
		
		tmp = new oNumero(total);
		form.total[i].value = tmp.formato(2, true);
	}
	totalCheque();
}

function totalCheque() {
	var total = 0;
	
	for (i = 0; i < form.total.length; i++) {
		total += !isNaN(parseFloat(form.total[i].value.replace(",", ""))) ? parseFloat(form.total[i].value.replace(",", "")) : 0;
	}
	
	tmp = new oNumero(total);
	form.total_cheque.value = tmp.formato(2, true);
}

function validar() {
	var total_cheque = !isNaN(parseFloat(form.total_cheque.value.replace(",", ""))) ? parseFloat(form.total_cheque.value.replace(",", "")) : 0;
	
	if (form.num_cia.value <= 0) {
		alert("Debe especificar la compañía");
		form.num_cia.select();
		return false;
	}
	else if (form.fecha.length < 8) {
		alert("Debe especificar la fecha");
		form.fecha.select();
		return false;
	}
	else if (form.num_pro.value <= 0) {
		alert("Debe especificar el proveedor");
		form.num_pro.select();
		return false;
	}
	else if (form.concepto.value == "") {
		alert("Debe poner el concepto");
		form.concepto.select();
		return false;
	}
	else if (form.codgastos.value <= 0) {
		alert("Debe especificar el código de gasto");
		form.codgastos.select();
		return false;
	}
	else if (total_cheque <= 0) {
		alert("El importe del cheque no puede ser cero");
		form.importe[0].select();
		return false;
	}
	else if (confirm("¿Son correctos los datos?")) {
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
</body>
</html>
