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
<!-- START BLOCK : prestamos -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><form action="./ros_pre_altas_v2.php" method="post" name="form">
    <input name="tmp" type="hidden" id="tmp">
    <table class="tabla">
  <tr>
    <th class="tabla" scope="col">Empleado</th>
    <th class="tabla" scope="col">Pseudonimo</th>
    <th class="tabla" scope="col">Importe</th>
  </tr>
  <!-- START BLOCK : fila -->
  <tr id="row{i}" onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
    <td class="tabla"><input name="id[]" type="hidden" id="id" value="{id}">
      <input name="num_emp[]" type="text" class="insert" id="num_emp" onFocus="tmp.value=this.value;this.select();colorRow({i})" onChange="if (isInt(this,tmp)) cambiaEmp({i})" onKeyDown="if (event.keyCode == 13) importe[{i}].select()" value="{num_emp}" size="4" maxlength="4">
      <input name="nombre[]" type="text" class="vnombre" id="nombre" value="{nombre}" size="50" readonly="true"></td>
    <td class="vtabla">{pseudonimo}</td>
    <td class="tabla"><input name="importe[]" type="text" class="rinsert" id="importe" onFocus="tmp.value=this.value;this.select();colorRow({i})" onBlur="if (formatoCampo(this)) calculaTotal()" onKeyDown="if (event.keyCode == 13) num_emp[{next}].select()" value="{importe}" size="10"></td>
  </tr>
  <!-- END BLOCK : fila -->
  <tr>
    <th class="rtabla">Total</th>
    <th class="rtabla">&nbsp;</th>
    <th class="tabla"><input name="total" type="text" class="rnombre" id="total" value="{total}" size="10" readonly="true"></th>
  </tr>
</table>
<p>
<input type="button" class="boton" value="Cancelar" onClick="self.close()">
&nbsp;&nbsp;    
<input type="button" class="boton" value="Alta de Empleados" onClick="altaEmpleado()">
&nbsp;&nbsp;
<input type="button" class="boton" value="Consulta" onClick="consultaEmp()">
&nbsp;&nbsp;    
<input type="button" class="boton" value="Siguiente" onClick="validar()">
</p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
<!--
var form = document.form, emp = new Array();
<!-- START BLOCK : emp -->
emp[{num_emp}] = new Array();
emp[{num_emp}]['id'] = {id};
emp[{num_emp}]['nombre'] = "{nombre}";
<!-- END BLOCK : emp -->

function cambiaEmp(i) {
	if (form.num_emp[i].value == "") {
		form.id[i].value = "";
		form.nombre[i].value = "";
		form.importe[i].value = "";
		
		calculaTotal();
	}
	else if (emp[form.num_emp[i].value] != null) {
		form.id[i].value = emp[form.num_emp[i].value]['id'];
		form.nombre[i].value = emp[form.num_emp[i].value]['nombre'];
	}
	else {
		alert("El empleado no se encuentra en el catálogo");
		form.num_emp[i].value = form.tmp.value;
		form.num_emp[i].select();
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
		campo.select();
		return false;
	}
	
	var value = parseFloat(campo.value.replace(",", ""));
	
	if (value < 0) {
		alert("No se permiten números negativos");
		campo.value = campo.form.tmp.value;
		campo.select();
		return false;
	}
	
	var tmp = new oNumero(value);
	campo.value = tmp.formato(2, true);
	
	calculaTotal();
	
	return true;
}

function calculaTotal() {
	var total = 0, tmp;
	
	for (var i = 0; i < form.importe.length; i++) {
		total += !isNaN(parseFloat(form.importe[i].value.replace(",", ""))) ? parseFloat(form.importe[i].value.replace(",", "")) : 0;
	}
	
	tmp = new oNumero(total);
	form.total.value = tmp.formato(2, true);
}

function validar() {
	form.submit();
}

function altaEmpleado() {
	var win = window.open("./ros_emp_altas_v2.php","Emp","left=192,top=144,toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=no,width=700,height=480");
	win.focus();
}

function consultaEmp() {
	var win = window.open("./ros_emp_con.php","Emp","left=192,top=144,toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=no,width=640,height=480");
	win.focus();
}

function colorRow(i) {
	for (var j = 0; j < form.num_emp.length; j++)
		if (j == i)
			document.getElementById("row" + j).style.backgroundColor = "#ACD2DD";
		else
			document.getElementById("row" + j).style.backgroundColor = "";
}

window.onload = form.num_emp[0].select();
-->
</script>
<!-- END BLOCK : prestamos -->
<!-- START BLOCK : cerrar -->
<script language="javascript" type="text/javascript">
<!--
function cerrar() {
	window.opener.form.siguiente.disabled = false;
	self.close();
}

window.onload = cerrar();
-->
</script>
<!-- END BLOCK : cerrar -->
</body>
</html>
