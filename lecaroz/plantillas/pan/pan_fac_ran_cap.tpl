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
<td align="center" valign="middle"><p class="title">Facturas del Rancho<br>
  Captura de Litros
</p>
  <form action="./pan_fac_ran_cap.php" method="get" name="form">
    <input name="tmp" type="hidden" id="tmp">
    <table class="tabla">
    <tr>
      <th class="vtabla" scope="row">Compa&ntilde;&iacute;a</th>
      <td class="vtabla"><input name="num_cia" type="text" class="insert" id="num_cia" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) anio.select()" size="3" maxlength="3"></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Mes</th>
      <td class="vtabla"><select name="mes" class="insert" id="mes">
        <option value="1"{1}>ENERO</option>
        <option value="2"{2}>FEBRERO</option>
        <option value="3"{3}>MARZO</option>
        <option value="4"{4}>ABRIL</option>
        <option value="5"{5}>MAYO</option>
        <option value="6"{6}>JUNIO</option>
        <option value="7"{7}>JULIO</option>
        <option value="8"{8}>AGOSTO</option>
        <option value="9"{9}>SEPTIEMBRE</option>
        <option value="10"{10}>OCTUBRE</option>
        <option value="11"{11}>NOVIEMBRE</option>
        <option value="12"{12}>DICIEMBRE</option>
      </select></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">A&ntilde;o</th>
      <td class="vtabla"><input name="anio" type="text" class="insert" id="anio" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) num_cia.select()" value="{anio}" size="4" maxlength="4"></td>
    </tr>
  </table>  <p>
    <input type="button" class="boton" value="Siguiente" onClick="validar(this.form)">
  </p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
<!--
function validar(form) {
	if (form.num_cia.value <= 0) {
		alert("Debe especificar la compañía");
		form.num_cia.select();
		return false;
	}
	else if (form.anio.value < 2000) {
		alert("Debe especificar el año");
		form.anio.select();
		return false;
	}
	else
		form.submit();
}

window.onload = document.form.num_cia.select();
-->
</script>
<!-- END BLOCK : datos -->
<!-- START BLOCK : captura -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Facturas del Rancho<br>
    Captura de Litros</p>
  <table class="tabla">
    <tr>
      <th class="tabla" scope="col">Compa&ntilde;&iacute;a</th>
      <th class="tabla" scope="col">Mes</th>
      <th class="tabla" scope="col">A&ntilde;o</th>
    </tr>
    <tr>
      <td class="tabla" style="font-size: 12pt; font-weight: bold;">{num_cia} {nombre} </td>
      <td class="tabla" style="font-size: 12pt; font-weight: bold;">{mes_escrito}</td>
      <td class="tabla" style="font-size: 12pt; font-weight: bold;">{anio}</td>
    </tr>
  </table>  
  <br>
  <form action="./pan_fac_ran_cap.php" method="post" name="form">
    <input name="tmp" type="hidden" id="tmp">
    <input name="num_cia" type="hidden" id="num_cia" value="{num_cia}">
    <input name="mes" type="hidden" id="mes" value="{mes}">
    <input name="anio" type="hidden" id="anio" value="{anio}">
    <input name="precio" type="hidden" id="precio" value="{precio}">    
    <table class="tabla">
    <tr>
      <th class="tabla" scope="col">D&iacute;a</th>
      <th class="tabla" scope="col">Factura</th>
      <th class="tabla" scope="col">Bidones</th>
      <th class="tabla" scope="col">Litros</th>
      <th class="tabla" scope="col">Importe</th>
      <th class="tabla" scope="col">Proceso</th>
    </tr>
    <!-- START BLOCK : fila -->
	<tr>
      <td class="tabla"><input name="dia[]" type="hidden" id="dia" value="{dia}">
        <input name="status[]" type="hidden" id="status" value="{status}">
        <strong>{dia}</strong></td>
      <td class="tabla"><input name="num_fact[]" type="text" class="insert" id="num_fact" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) bidones[{i}].select();
else if (event.keyCode == 38) num_fact[{back}].select();
else if (event.keyCode == 40) num_fact[{next}].select();" value="{num_fact}" size="10" maxlength="10" {readonly}></td>
      <td class="tabla"><input name="bidones[]" type="text" class="rinsert" id="bidones" onFocus="tmp.value=this.value;this.select()" onChange="if (input_format(this,2,true)) calculaImporte({i})" onKeyDown="if (event.keyCode == 13) num_fact[{next}].select();
else if (event.keyCode == 37) num_fact[{i}].select();
else if (event.keyCode == 38) bidones[{back}].select();
else if (event.keyCode == 40) bidones[{next}].select();" value="{bidones}" size="10" {readonly}></td>
      <td class="tabla"><input name="litros[]" type="text" class="rnombre" id="litros" value="{litros}" size="10" readonly="true"></td>
      <td class="tabla"><input name="importe[]" type="text" disabled="true" class="rnombre" id="importe" value="{importe}" size="10"></td>
	  <td class="tabla">{proceso}</td>
	</tr>
	<!-- END BLOCK : fila -->
    <tr>
      <th colspan="3" class="rtabla">Total</th>
      <th class="rtabla"><input name="total_litros" type="text" class="rnombre" id="total_litros" value="{total_litros}" size="10" disabled></th>
      <th class="tabla"><input name="total_importe" type="text" class="rnombre" id="total_importe" value="{total_importe}" size="10" disabled></th>
      <th class="tabla">&nbsp;</th>
    </tr>
  </table>  
    <p>
    <input type="button" class="boton" value="Regresar" onClick="document.location='./pan_fac_ran_cap.php'">
&nbsp;&nbsp;    
<input type="button" class="boton" value="Siguiente" onClick="validar()"> 
    </p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
<!--
var form = document.form, litros_x_bidon = 20;

function calculaImporte(i) {
	if (form.bidones[i].value == "0" || form.bidones[i].value == "") {
		form.bidones[i].value
		form.litros[i].value = "";
		form.importe[i].value = "";
	}
	else {
		var bidones = !isNaN(parseFloat(form.bidones[i].value.replace(",", ""))) ? parseFloat(form.bidones[i].value.replace(",", "")) : 0;
		var precio = !isNaN(parseFloat(form.precio.value.replace(",", ""))) ? parseFloat(form.precio.value.replace(",", "")) : 0;
		
		var litros = bidones * litros_x_bidon;
		var importe = litros * precio;
		form.litros[i].value = number_format(litros, 2);
		form.importe[i].value = number_format(importe, 2);
	}
	
	totales();
}

function totales() {
	var litros = 0, importe = 0;
	
	for (var i = 0; i < form.dia.length; i++) {
		litros += !isNaN(parseFloat(form.litros[i].value.replace(",", ""))) ? parseFloat(form.litros[i].value.replace(",", "")) : 0;
		importe += !isNaN(parseFloat(form.importe[i].value.replace(",", ""))) ? parseFloat(form.importe[i].value.replace(",", "")) : 0;
	}
	
	form.total_litros.value = number_format(litros, 2);
	form.total_importe.value = number_format(importe, 2);
}

function validar() {
	for (var i = 0; i < form.dia.length; i++)
		if (form.litros[i].value != "" && parseFloat(form.litros[i].value.replace(",", "")) > 999) {
			alert("No se pueden capturar mas de 999 litros por día");
			form.litros[i].select();
			return false;
		}
	
	if (confirm("¿Son correctos los datos?"))
		form.submit();
	else
		return false;
}

window.onload = form.num_fact[0].select();
-->
</script>
<!-- END BLOCK : captura -->
</body>
</html>
