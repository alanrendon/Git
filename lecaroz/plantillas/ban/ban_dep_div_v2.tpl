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
<!-- START BLOCK : div -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Dividir
  Dep&oacute;sito</p>
  <form action="./ban_dep_div_v2.php" method="post" name="form">
    <input name="id" type="hidden" id="id" value="{id}">
    <input name="temp" type="hidden" id="temp">
    <table class="tabla">
    <tr>
      <th class="tabla" scope="col">Dep&oacute;sito</th>
    </tr>
    <tr>
      <th class="tabla"><input name="importe" type="text" disabled="true" class="nombre" id="importe" value="{importe}" size="10"></th>
    </tr>
  </table>  
  <br>
  <table class="tabla">
    <tr>
      <th class="tabla" scope="col">&nbsp;</th>
      <th class="tabla" scope="col">Importe</th>
    </tr>
    <tr>
      <th class="tabla">1</th>
      <td class="tabla"><input name="importe_div[]" type="text" class="rinsert" id="importe_div" onFocus="temp.value=this.value; this.select();" onChange="format(this); total_div();" onKeyDown="if (event.keyCode == 13) importe_div[1].select()" size="10"></td>
    </tr>
    <tr>
      <th class="tabla">2</th>
      <td class="tabla"><input name="importe_div[]" type="text" class="rinsert" id="importe_div" onFocus="temp.value=this.value; this.select();" onChange="format(this); total_div();" onKeyDown="if (event.keyCode == 13) importe_div[2].select()" size="10"></td>
    </tr>
    <tr>
      <th class="tabla">3</th>
      <td class="tabla"><input name="importe_div[]" type="text" class="rinsert" id="importe_div" onFocus="temp.value=this.value; this.select();" onChange="format(this); total_div();" onKeyDown="if (event.keyCode == 13) importe_div[3].select()" size="10"></td>
    </tr>
    <tr>
      <th class="tabla">4</th>
      <td class="tabla"><input name="importe_div[]" type="text" class="rinsert" id="importe_div" onFocus="temp.value=this.value; this.select();" onChange="format(this); total_div();" onKeyDown="if (event.keyCode == 13) importe_div[4].select()" size="10"></td>
    </tr>
    <tr>
      <th class="tabla">5</th>
      <td class="tabla"><input name="importe_div[]" type="text" class="rinsert" id="importe_div" onFocus="temp.value=this.value; this.select();" onChange="format(this); total_div();" onKeyDown="if (event.keyCode == 13) importe_div[0].select()" size="10"></td>
    </tr>
    <tr>
      <th class="tabla">Total</th>
      <th class="tabla"><input name="total" type="text" disabled="true" class="rnombre" id="total" value="0.00" size="10"></th>
    </tr>
    <tr>
      <th class="tabla">Resta</th>
      <th class="tabla"><input name="resta" type="text" disabled="true" class="rnombre" id="resta" value="0.00" size="10"></th>
    </tr>
  </table>  
  <p>
    <input type="button" class="boton" value="Cancelar" onClick="self.close()">
    &nbsp;&nbsp;
    <input type="button" class="boton" value="Dividir" onClick="validar()">
  </p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
<!--
var form = document.form;

function format(field) {
	if (field.value == "") {
		return false;
	}
	
	var number = parseFloat(field.value.replace(",", ""));
	if (isNaN(number)) {
		alert("Solo se permiten números");
		field.value = form.temp.value;
		return false;
	}
	
	var tmp = new oNumero(number);
	field.value = tmp.formato(2, true);
}

function total_div() {
	var total = 0;
	var importe = get_val(form.importe);
	
	for (i = 0; i < form.importe_div.length; i++) {
		total += !isNaN(parseFloat(form.importe_div[i].value.replace(",", ""))) ? parseFloat(form.importe_div[i].value.replace(",", "")) : 0;
	}
	
	var resta = importe - total;
	
	var tmp = new oNumero(total);
	form.total.value = tmp.formato(2, true);
	form.resta.value = numberFormat(resta, 2);
}

function validar() {
	var imp = !isNaN(parseFloat(form.importe.value.replace(",", ""))) ? parseFloat(form.importe.value.replace(",", "")) : 0;
	var imp_max = imp + 0.99;
	var imp_min = imp - 0.99;
	var total = !isNaN(parseFloat(form.total.value.replace(",", ""))) ? parseFloat(form.total.value.replace(",", "")) : 0;
	
	if (total < imp_min || total > imp_max) {
		alert("La suma de los importes debe ser igual al depósito original");
		form.importe_div[0].select();
		return false;
	}
	else if (confirm("¿Desea dividir el depósito?")) {
		form.submit();
	}
	else {
		form.importe_div[0].select();
		return false;
	}
}

window.onload = form.importe_div[0].select();
-->
</script>
<!-- END BLOCK : div -->
<!-- START BLOCK : cerrar -->
<script language="javascript" type="text/javascript">
<!--
function cerrar() {
	window.opener.document.location.reload();
	self.close();
}

window.onload = cerrar();
-->
</script>
<!-- END BLOCK : cerrar -->
</body>
</html>
