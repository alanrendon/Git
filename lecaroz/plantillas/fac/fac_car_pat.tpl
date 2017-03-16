<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Documento sin t&iacute;tulo</title>
<link href="../../styles/tablas.css" rel="stylesheet" type="text/css" />
<link href="../../styles/pages.css" rel="stylesheet" type="text/css" />
</head>

<body>
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Carta Patronal</p>
  <form action="./fac_car_pat.php" method="" name="form" target="carta">
    <input name="tmp" type="hidden" id="tmp" />
    <table class="tabla">
    <tr>
      <th class="vtabla" scope="row">Compa&ntilde;&iacute;a</th>
      <td class="vtabla"><input name="num_cia" type="text" class="insert" id="num_cia" onfocus="tmp.value=this.value;this.select()" onchange="if (isInt(this,tmp)) cambiaCia()" onkeydown="if (event.keyCode == 13) fecha.select()" size="3" />
        <input name="nombre_cia" type="text" disabled="disabled" class="vnombre" id="nombre_cia" size="40" /></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Empleado</th>
      <td class="vtabla"><select name="id_emp" class="insert" id="id_emp">
        <option value=""></option>
      </select>      </td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Alta IMSS </th>
      <td class="vtabla"><input name="fecha" type="text" class="insert" id="fecha" onfocus="tmp.value=this.value;this.select()" onchange="inputDateFormat(this)" onkeydown="if (event.keyCode == 13) sal.select()" size="10" maxlength="10" /></td>
    </tr>
	<tr>
      <th class="vtabla" scope="row">Salario Diario </th>
      <td class="vtabla"><input name="sal" type="text" class="rinsert" id="sal" onfocus="tmp.value=this.value;this.select()" onchange="inputFormat(this,2)" onkeydown="if (event.keyCode == 13) sal_int.select()" size="12" /></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Salario Diario Integrado </th>
      <td class="vtabla"><input name="sal_int" type="text" class="rinsert" id="sal_int" onfocus="tmp.value=this.value;this.select()" onchange="inputFormat(this,2)" onkeydown="if (event.keyCode == 13) num_cia.select()" size="12" /></td>
    </tr>
  </table>  
  <p>
    <input type="button" class="boton" value="Siguiente" onclick="validar()" />
</p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
<!--
var f = document.form;

function cambiaCia() {
	if (f.num_cia.value == '' || f.num_cia.value == '0') {
		f.num_cia.value = '';
		f.nombre_cia.value = '';
		
		f.id_emp.length = 1;
		f.id_emp.options[0].value = '';
		f.id_emp.options[0].text = '';
	}
	else {
		var myConn = new XHConn();
	
		if (!myConn)
			alert("XMLHTTP no disponible. Trate con un nuevo/mejor navegador.");
		
		// Pedir datos
		myConn.connect('./fac_car_pat.php', 'GET', 'c=' + get_val(f.num_cia), obtenerCia);
	}
}

var obtenerCia = function (oXML) {
	var result = oXML.responseText;
	
	if (result == '') {
		alert('La compañía no se encuentra en el catálogo');
		f.num_cia.value = f.tmp.value;
		f.num_cia.select();
	}
	else
		f.nombre_cia.value = result;
	
	listaEmpleados();
}

function listaEmpleados() {
	if (get_val(f.num_cia) <= 0) {
		f.id_emp.length = 1;
		f.id_emp.options[0].value = '';
		f.id_emp.options[0].text = '';
	}
	else {
		var myConn = new XHConn();
	
		if (!myConn)
			alert("XMLHTTP no disponible. Trate con un nuevo/mejor navegador.");
		
		// Pedir datos
		myConn.connect('./fac_car_pat.php', 'GET', 'ce=' + get_val(f.num_cia), generarListadoEmpleados);
	}
}

var generarListadoEmpleados = function (oXML) {
	var result = oXML.responseText, j, tmp;
	
	if (result == '-1') {
		alert('La compañía no tiene empleados con Infonavit');
		
		f.num_cia.value = '';
		f.nombre_cia.value = '';
		f.num_cia.select();
		
		f.id_emp.length = 1;
		f.id_emp.options[0].value = '';
		f.id_emp.options[0].text = '';
		
		return false;
	}
	
	result = result.split('|');
	
	f.id_emp.length = result.length + 1;
	f.id_emp.options[0].value = '';
	f.id_emp.options[0].text = '';
	for (j = 0; j < result.length; j++) {
		tmp = result[j].split('/');
		
		f.id_emp.options[j + 1].value = tmp[0];
		f.id_emp.options[j + 1].text = tmp[1];
	}
}

function validar() {
	if (get_val(f.num_cia) <= 0) {
		alert('Debe especificar la compañía');
		f.num_cia.select();
	}
	else if (get_val(f.id_emp) <= 0) {
		alert('Debe seleccionar un empleado');
		f.id_emp.focus();
	}
	else if (f.fecha.value.length < 8) {
		alert('Debe capturar la fecha de alta en el IMSS');
		f.fecha.select();
	}
	else if (get_val(f.sal) <= 0) {
		alert('Debe capturar el salario');
		f.sal.select();
	}
	else if (get_val(f.sal_int) <= 0) {
		alert('Debe capturar el salario integrado');
		f.sal_int.select();
	}
	else if (get_val(f.sal) >= get_val(f.sal_int)) {
		alert('El salario no puede ser mayor al salario integrado');
		f.sal.select();
	}
	else if (get_val(f.sal_int) > get_val(f.sal) * 1.20) {
		alert('El salario integrado no puede ser mayor al 20% del salario');
		f.sal_int.select();
	}
	else {
		var win = window.open("", "carta", "toolbar=no,location=no,directories=no,status=no,menubar=yes,scrollbars=yes,resizable=yes,width=1024,height=768");
		f.submit();
		win.focus();
	}
}

window.onload = f.num_cia.select();
//-->
</script>
</body>
</html>
