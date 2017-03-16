<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Untitled Document</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../../styles/tablas.css" rel="stylesheet" type="text/css">
<link href="../../styles/pages.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="jscripts/mootools/mootools-1.2-core.js"></script>
</head>

<body>
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Captura de Cheques de N&oacute;mina y Gastos </p>
  <form action="./ban_che_mul_v2.php" method="post" name="form">
  <input name="tmp" type="hidden">
  <table class="tabla">
    <tr>
      <th class="tabla" scope="col">Fecha</th>
      <th class="tabla" scope="col">C&oacute;digo de Gasto </th>
      <th class="tabla" scope="col">Concepto</th>
      <th class="tabla" scope="col">Cuenta</th>
      <th class="tabla" scope="col">Poliza</th>
      <th class="tabla" scope="col">A cuenta</th>
      <th class="tabla" scope="col">Transferencia</th>
    </tr>
    <tr>
      <td class="tabla"><input name="fecha" type="text" class="insert" id="fecha" onChange="actualiza_fecha(this)" onKeyDown="if (event.keyCode == 13) codgastos.select()" value="{fecha}" size="10" maxlength="10"></td>
      <td class="tabla"><input name="codgastos" type="text" class="insert" id="codgastos" onFocus="tmp.value=this.value" onChange="if (isInt(this,tmp)) cambiaGasto(this, nombre_gasto)" onKeyDown="if (event.keyCode == 13) concepto.select()" size="4" maxlength="4">
        <input name="nombre_gasto" type="text" disabled="true" class="vnombre" id="nombre_gasto" size="30"></td>
      <td class="tabla"><input name="concepto" type="text" class="vinsert" id="concepto" onKeyDown="if (event.keyCode == 13) num_cia[0].select()" size="50" maxlength="50"></td>
      <td class="tabla"><select name="cuenta" class="insert" id="cuenta">
        <option value="1" selected>BANORTE</option>
        <option value="2">SANTANDER SERFIN</option>
      </select></td>
      <td class="tabla"><input name="poliza" type="checkbox" id="poliza" value="1"></td>
      <td class="tabla"><input name="acuenta" type="checkbox" id="acuenta" value="1"></td>
      <td class="tabla"><input name="trans" type="checkbox" id="trans" value="1"></td>
    </tr>
  </table>
  <p style="font-family:Arial, Helvetica, sans-serif;font-size:12pt;font-weight:bold;color:#C00;">NOTA: le recordamos que si es cheque a cuenta de algun gasto no debe aparecer en pendientes.</p>
  <table class="tabla">
    <tr>
      <th class="tabla" scope="col">Compa&ntilde;&iacute;a</th>
      <th class="tabla" scope="col">Proveedor</th>
      <th class="tabla" scope="col">Saldo</th>
      <th class="tabla" scope="col">Importe</th>
    </tr>
    <!-- START BLOCK : fila -->
	<tr>
      <td class="tabla"><input name="num_cia[]" type="text" class="insert" id="num_cia" onFocus="tmp.value=this.value;this.select()" onChange="if (isInt(this,tmp)) cambiaCia({i})" onKeyDown="if (event.keyCode == 13) num_pro[{i}].select()" size="3" maxlength="3">          
        <input name="nombre_cia[]" type="text" disabled="true" class="vnombre" id="nombre_cia" size="40"></td><td class="tabla"><input name="num_pro[]" type="text" class="insert" id="num_pro" onFocus="tmp.value=this.value;this.select()" onChange="if (isInt(this,tmp)) cambiaPro({i})" onKeyDown="if (event.keyCode == 13) importe[{i}].select()" size="4" maxlength="4">
        <input name="nombre_pro[]" type="text" class="vnombre" id="nombre_pro" size="40" readonly="true"></td>
        <td class="tabla"><input name="saldo[]" type="text" disabled="true" class="rnombre" id="saldo" size="10"></td>
        <td class="tabla"><input name="importe[]" type="text" class="rinsert" id="importe" onFocus="tmp.value=this.value" onChange="formatoCampo(this)" onKeyDown="if (event.keyCode == 13) num_cia[{next}].select()" size="10"></td>
    </tr>
	<!-- END BLOCK : fila -->
  </table>
    <p>
    <input type="button" class="boton" value="Siguiente" onClick="valida_registro(form)">
  </p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
<!--
var form = document.form;
var cia = new Array(), pro = new Array(), gasto = new Array(), saldo1 = new Array(), saldo2 = new Array();
<!-- START BLOCK : cia -->
cia[{num_cia}] = new Array();
cia[{num_cia}]['nombre_cia'] = "{nombre_cia}";
cia[{num_cia}]['num_pro'] = {num_pro};
cia[{num_cia}]['nombre_pro'] = "{nombre_pro}";
<!-- END BLOCK : cia -->
<!-- START BLOCK : pro -->
pro[{num_pro}] = "{nombre}";
<!-- END BLOCK : pro -->
<!-- START BLOCK : gasto -->
gasto[{codgastos}] = "{nombre}";
<!-- END BLOCK : gasto -->
<!-- START BLOCK : saldo1 -->
saldo1[{num_cia}] = "{saldo}";
<!-- END BLOCK : saldo1 -->
<!-- START BLOCK : saldo2 -->
saldo2[{num_cia}] = "{saldo}";
<!-- END BLOCK : saldo2 -->

function cambiaCia(i) {
	var saldo = eval("saldo" + form.cuenta.value);
	
	if (form.num_cia[i].value == "") {
		form.nombre_cia[i].value = "";
		form.num_pro[i].value = "";
		form.nombre_pro[i].value = "";
		form.saldo[i].value = "";
		form.importe[i].value = "";
	}
	else if (cia[form.num_cia[i].value] != null) {
		form.nombre_cia[i].value = cia[form.num_cia[i].value]['nombre_cia'];
		form.num_pro[i].value = cia[form.num_cia[i].value]['num_pro'];
		form.nombre_pro[i].value = cia[form.num_cia[i].value]['nombre_pro'];
		
		if (saldo[form.num_cia[i].value] != null) {
			form.saldo[i].value = saldo[form.num_cia[i].value];
		}
		else {
			form.saldo[i].value = "0.00";
		}
	}
	else {
		alert("La compañía no se encuentra en el catalogo");
		form.num_cia[i].value = form.tmp.value;
		form.num_cia[i].select();
	}
}

function cambiaPro(i) {
	if (form.num_pro[i].value == "") {
		form.num_pro[i].value = "";
	}
	else if (pro[form.num_pro[i].value] != null) {
		form.nombre_pro[i].value = pro[form.num_pro[i].value];
	}
	else {
		alert("El proveedor no se encuentra en el catalogo");
		form.num_pro[i].value = form.tmp.value;
		num.select();
	}
}

function valida_registro() {
	if (form.fecha.value == "") {
		alert("Debe especificar la fecha");
		form.fecha.select();
		return false;
	}
	else if (form.codgastos.value <= 0) {
		alert("Debe especificar el código de gasto");
		form.cod_gastos.select();
		return false;
	}
	else if (form.concepto.value == "") {
		alert("Debe especificar el concepto");
		form.concepto.select();
		return false;
	}
	else
		if (confirm("¿Son correctos los datos?"))
			form.submit();
		else
			form.fecha.select();
}

function cambiaGasto(num, nombre) {
	if (num.value == "") {
		nombre.value = "";
	}
	else if (gasto[num.value] != null) {
		nombre.value = gasto[num.value];
	}
	else {
		alert("El código no se encuentra en el catalogo");
		num.value = num.form.tmp.value;
		num.select();
	}
}

function formatoCampo(campo) {
	if (campo.value == "" || campo.value == "0") {
		campo.value = "";
		return true;
	}
	else if (isNaN(parseFloat(campo.value.replace(",", "")))) {
		alert("Solo se permiten números");
		campo.value = campo.form.tmp.value;
		return false;
	}
	
	var value = parseFloat(campo.value.replace(",", ""));
	
	if (value < 0) {
		alert("No se permiten números negativos");
		campo.value = campo.form.tmp.value;
		return false;
	}
	
	if (document.form.codgastos.value.toInt() == 134) {
		var int_part = Math.floor(value);
		var dec_part = (value - int_part).round(2);
		
		var _pow = dec_part * 100;
		
		var new_dec_part = _pow % 5 == 0 ? _pow / 100 : (_pow / 10).round() / 10;
		
		value = int_part + new_dec_part;
	}
	
	var tmp = new oNumero(value);
	campo.value = tmp.formato(2, true);
	
	return true;
}

window.onload = form.fecha.select();
-->
</script>
</body>
</html>
