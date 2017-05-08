<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Untitled Document</title>
<link href="../../styles/tablas.css" rel="stylesheet" type="text/css" />
<link href="../../styles/pages.css" rel="stylesheet" type="text/css" />
</head>

<body>
<!-- START BLOCK : datos -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Alta de Documento de Movimiento de Empleado</p>
  <form action="./fac_doc_emp_alta.php" method="get" name="form">
    <input type="hidden" name="tmp" id="tmp" />
    <table class="tabla">
    <tr>
      <th class="vtabla" scope="row">Compa&ntilde;&iacute;a</th>
      <td class="vtabla"><input name="num_cia" type="text" class="insert" id="num_cia" onfocus="tmp.value=this.value;this.select()" onchange="if(isInt(this,tmp))cambiaCia()" onkeydown="if(event.keyCode==13)this.blur()" size="3" />
        <input name="nombre" type="text" disabled="disabled" class="vnombre" id="nombre" size="30" /></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Empleado</th>
      <td class="vtabla"><select name="id_emp" class="insert" id="id_emp">
        <option value=""></option>
      </select></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Tipo</th>
      <td class="vtabla"><select name="tipo" class="insert" id="tipo">
        <option value="1" selected="selected">ALTA</option>
        <option value="0">BAJA</option>
        <option value="2">MODIFICACION</option>
      </select>
      </td>
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
		f.nombre.value = '';
		
		f.id_emp.length = 1;
		f.id_emp.options[0].value = '';
		f.id_emp.options[0].text = '';
	}
	else {
		var myConn = new XHConn();
	
		if (!myConn)
			alert("XMLHTTP no disponible. Trate con un nuevo/mejor navegador.");
		
		// Pedir datos
		myConn.connect('./fac_doc_emp_alta.php', 'GET', 'c=' + get_val(f.num_cia), obtenerCia);
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
		f.nombre.value = result;
	
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
		myConn.connect('./fac_doc_emp_alta.php', 'GET', 'ce=' + get_val(f.num_cia), generarListadoEmpleados);
	}
}

var generarListadoEmpleados = function (oXML) {
	var result = oXML.responseText, j, tmp;
	
	if (result == '-1') {
		alert('La compañía no tiene empleados con Infonavit');
		
		f.num_cia.value = '';
		f.nombre.value = '';
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
	if (get_val(f.num_cia) == 0) {
		alert('Debe especificar la compañía');
		f.num_cia.select();
	}
	else if (get_val(f.id_emp) == 0) {
		alert('Debe especificar al empleado');
		f.id_emp.focus();
	}
	else
		f.submit();
}

window.onload = f.num_cia.select();
//-->
</script>
<!-- END BLOCK : datos -->
<!-- START BLOCK : scan -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Alta de Documento de Movimiento de Empleado</p>
  <p><font face="Geneva, Arial, Helvetica, sans-serif">Digitalizar documentos.</font> </p>
  <p>
        <applet code="com.asprise.util.jtwain.web.UploadApplet.class" 
			codebase="http://192.168.1.250/lecaroz/jtwain/"
			archive="JTwain.jar"
			width="600" height="470">
              <param name="DOWNLOAD_URL" value="http://192.168.1.250/lecaroz/jtwain/AspriseJTwain.dll">
			  <param name="DLL_NAME" value="AspriseJTwain.dll">
			  <param name="UPLOAD_URL" value="http://192.168.1.250/lecaroz/fac_doc_emp_alta.php?accion=upload&id_emp={id_emp}&tipo={tipo}">
			  <param name="UPLOAD_PARAM_NAME" value="doc">
			  <param name="UPLOAD_EXTRA_PARAMS" value="A=B">
			  <param name="UPLOAD_OPEN_URL" value="http://192.168.1.250/lecaroz/fac_doc_emp_alta.php">
			  <param name="UPLOAD_OPEN_TARGET" value="_self">
			  Su navegador no soporta java applets
    </applet>
</p>
  <p>
    <input type="button" class="boton" value="Cancelar" onClick="document.location='./fac_doc_emp_alta.php'">
  </p></td>
</tr>
</table>
<!-- END BLOCK : scan -->
</body>
</html>
