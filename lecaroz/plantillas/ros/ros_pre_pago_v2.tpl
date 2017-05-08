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
<!-- START BLOCK : pagos -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><form action="./ros_pre_pago_v2.php" method="post" name="form">
    <input name="tmp" type="hidden" id="tmp">
    <table class="tabla">
  <tr>
    <th colspan="2" class="tabla" scope="col">Empleado</th>
    <th class="tabla" scope="col">Prestamo</th>
    <th class="tabla" scope="col">A Cuenta </th>
    <th class="tabla" scope="col">Resta</th>
  </tr>
  <!-- START BLOCK : prestamo -->
  <tr id="row{i}" onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
    <td class="rtabla"><input name="id[]" type="hidden" id="id" value="{id}">
      <input name="num_emp[]" type="hidden" id="num_emp" value="{num_emp}">
      {num_emp}</td>
    <td class="vtabla">{nombre}</td>
    <td class="tabla">      <input name="prestamo[]" type="text" class="rnombre" id="prestamo" value="{prestamo}" size="10" readonly="true"></td>
    <td class="tabla"><input name="importe[]" type="text" class="rinsert" id="importe" onFocus="tmp.value=this.value;this.select();colorRow({i})" onBlur="if (formatoCampo(this)) calculaResta({i})" onKeyDown="if (event.keyCode == 13) {
if (importe.length == undefined) this.blur();
else importe[{next}].select();
}" value="{importe}" size="10"></td>
    <td class="tabla"><input name="resta[]" type="hidden" id="resta" value="{resta}"><input name="resta_real[]" type="text" class="rnombre" id="resta_real" value="{resta_real}" size="10" readonly="true"></td>
  </tr>
  <!-- END BLOCK : prestamo -->
  <tr>
    <th colspan="3" class="rtabla">Total A Cuenta </th>
    <th class="tabla"><input name="total" type="text" class="rnombre" id="total" value="{total}" size="10" readonly="true"></th>
    <th class="tabla">&nbsp;</th>
  </tr>
</table>
<!-- START BLOCK : tmp -->
    <br>
    <table width="200" border="1" class="tabla">
      <tr>
        <th class="tabla" scope="col">No.</th>
        <th class="tabla" scope="col">Pseudonimo</th>
        <th class="tabla" scope="col">Importe</th>
      </tr>
	  <!-- START BLOCK : tmprow -->
      <tr>
        <td class="tabla"><input name="num_tmp[]" type="text" class="insert" id="num_tmp" onFocus="tmp.value=this.value;this.select()" onChange="if (isInt(this,tmp)) buscarEmp({i})" onKeyDown="if (num_tmp.length == undefined) {
if (event.keyCode == 13) this.blur();
}
else {
if (event.keyCode == 13) num_tmp[{next}].select();
}" value="{idemp}" size="4"></td>
        <td class="vtabla">{pseudonimo}</td>
        <td class="rtabla"><input name="importe_tmp[]" type="text" disabled class="rnombre" id="importe_tmp" value="{importe}" size="10"></td>
      </tr>
	  <!-- END BLOCK : tmprow -->
    </table>
<!-- END BLOCK : tmp -->
    <p>
    <input type="button" class="boton" value="Cancelar" onClick="self.close()">
&nbsp;&nbsp;    
<input type="button" class="boton" value="Siguiente" onClick="validar()">
  </p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
<!--
var form = document.form;

function calculaResta(i)  {
	var input_prestamo, input_importe, input_resta;
	input_prestamo = form.prestamo.length == undefined ? form.prestamo : form.prestamo[i];
	input_importe = form.importe.length == undefined ? form.importe : form.importe[i];
	input_resta = form.resta.length == undefined ? form.resta : form.resta[i];
	input_resta_real = form.resta_real.length == undefined ? form.resta_real : form.resta_real[i];
	
	if (input_importe.value == "" || input_importe.value == "0") {
		input_importe.value == "";
		input_resta_real.value = input_resta.value;
		input_importe.select();
	}
	else {
		var prestamo, importe, resta, resta_real, tmp;
		
		prestamo = parseFloat(input_prestamo.value.replace(",", ""));
		importe = parseFloat(input_importe.value.replace(",", ""));
		resta = !isNaN(parseFloat(input_resta.value.replace(",", ""))) ? parseFloat(input_resta.value.replace(",", "")) : 0;
		
		resta_real = resta - importe;
		
		if (resta_real < 0) {
			alert("No puede abonar más de lo que se debe");
			input_importe.value = form.tmp.value;
			input_importe.select();
			return false;
		}
		
		tmp = new oNumero(resta_real);
		input_resta_real.value = tmp.formato(2, true);
	}
	
	calculaTotal();
}

function calculaTotal() {
	var total = 0, tmp;
	if (form.importe.length == undefined) {
		total += !isNaN(parseFloat(form.importe.value.replace(",", ""))) ? parseFloat(form.importe.value.replace(",", "")) : 0;
	}
	else {
		for (var i = 0; i < form.importe.length; i++) {
			total += !isNaN(parseFloat(form.importe[i].value.replace(",", ""))) ? parseFloat(form.importe[i].value.replace(",", "")) : 0;
		}
	}
	
	tmp = new oNumero(total);
	form.total.value = tmp.formato(2, true);
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
	
	var tmp = new oNumero(value);
	campo.value = tmp.formato(2, true);
	
	return true;
}

function buscarEmp(i) {
	var num_tmp = form.num_tmp.length == undefined ? get_val(form.num_tmp) : get_val(form.num_tmp[i]);
	var importe = form.importe_tmp.length == undefined ? get_val(form.importe_tmp) : get_val(form.importe_tmp[i]);
	var num_emp = new Array();
	var ok = false;
	
	if (form.num_emp.length == undefined)
		num_emp[0] = get_val(form.num_emp);
	else
		for (var j = 0; j < form.num_emp.length; j++)
			num_emp[j] = get_val(form.num_emp[j]);
	
	for (j = 0; j < num_emp.length; j++)
		if (num_tmp == num_emp[j]) {
			if (form.num_emp.length == undefined)
				form.importe.value = form.importe_tmp.length == undefined ? form.importe_tmp.value : form.importe_tmp[i].value;
			else
				form.importe[j].value = form.importe_tmp.length == undefined ? form.importe_tmp.value : form.importe_tmp[i].value;
			
			calculaResta(i);
			
			ok = true;
		}
	
	if (!ok) {
		alert('El empleado no tiene prestamos pendientes');
		if (form.num_tmp.length == undefined) {
			form.num_tmp.value = form.tmp.value;
			form.num_tmp.select();
		}
		else {
			form.num_tmp[i].value = form.tmp.value;
			form.num_tmp[i].select();
		}
	}
}

function validar() {
	var total = parseFloat(form.total.value.replace(",", ""));
	
	if (total < 0) {
		alert("Debe abonar algo a cuenta");
		return false;
	}
	else {
		form.submit();
	}
}

function colorRow(i) {
	for (var j = 0; j < form.id.length; j++)
		if (j == i)
			document.getElementById("row" + j).style.backgroundColor = "#ACD2DD";
		else
			document.getElementById("row" + j).style.backgroundColor = "";
}

window.onload = form.importe.length == undefined ? form.importe.select() : form.importe[0].select();
-->
</script>
<!-- END BLOCK : pagos -->
<!-- START BLOCK : cerrar -->
<script language="javascript" type="text/javascript">
<!--
function cerrar() {
	window.opener.document.form.siguiente.disabled = false;
	self.close();
}

window.onload = cerrar();
-->
</script>
<!-- END BLOCK : cerrar -->
</body>
</html>
