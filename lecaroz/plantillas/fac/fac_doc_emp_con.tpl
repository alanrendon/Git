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
<td align="center" valign="middle"><p class="title">Consulta de Documento de Movimiento de Empleado</p>
  <form action="./fac_doc_emp_con.php" method="get" name="form">
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
        <option value="" selected="selected"></option>
        <option value="1">ALTA</option>
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
	f.submit();
}

window.onload = f.num_cia.select();
//-->
</script>
<!-- END BLOCK : datos -->
<!-- START BLOCK : consulta -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
  <p class="title">Consulta de Documento de Movimiento de Empleado</p>
  <form action="fac_doc_emp_con.php" method="post" name="form" target="imp"><table class="tabla">
    <!-- START BLOCK : cia -->
    <tr>
      <th colspan="3" class="vtabla" scope="col">{num_cia} {nombre}</th>
      </tr>
    <tr>
      <th class="tabla">&nbsp;</th>
      <th class="tabla">Empleado</th>
      <th class="tabla">Documentos</th>
    </tr>
    <!-- START BLOCK : emp -->
    <tr>
      <td class="tabla"><input name="ids[]" type="checkbox" id="ids" value="{ids}" /></td>
      <td class="vtabla">{num_emp} {nombre}</td>
      <td class="tabla">
        <!-- START BLOCK : doc -->
        <img src="img_doc_emp.php?id={id}&width=180" onmouseover="this.style.cursor='pointer'" onmouseout="this.style.cursor='default'" onclick="show({id})" />&nbsp;&nbsp;
        <!-- END BLOCK : doc -->
      </td>
    </tr>
    <!-- END BLOCK : emp -->
    <tr>
      <td colspan="3" class="tabla">&nbsp;</td>
      </tr>
      <!-- END BLOCK : cia -->
  </table>  
  <p>
    <input type="button" class="boton" value="Regresar" onclick="document.location='fac_doc_emp_con.php'" />
  &nbsp;&nbsp;
  <input type="button" class="boton" value="Imprimir" onclick="imprimir()" />
  </p></form></td>
</tr>
</table>
<script language="javascript" type="application/javascript">
<!--
var f = document.form;

function show(id) {
	var win = window.open('img_doc_emp.php?id=' + id + '&width=965', '', 'toolbar=no,location=no,directories=no,status=no,menubar=yes,scrollbars=yes,resizable=yes,width=1000,height=600');
	win.focus();
}

function imprimir() {
	var cont = 0;
	if (f.ids.length == undefined)
		cont += f.ids.checked ? 1 : 0;
	else
		for (var i = 0; i < f.ids.length; i++)
			cont += f.ids[i].checked ? 1 : 0;
	
	if (cont == 0) {
		alert('Debe seleccionar al menos un registro');
		return false;
	}
	
	var win = window.open('', 'imp', 'toolbar=no,location=no,directories=no,status=no,menubar=yes,scrollbars=yes,resizable=yes,width=800,height=600');
	f.submit();
	
	win.focus();
}
//-->
</script>
<!-- END BLOCK : consulta -->
</body>
</html>
