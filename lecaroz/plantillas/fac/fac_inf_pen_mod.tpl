<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Documento sin t&iacute;tulo</title>
<link href="../../styles/tablas.css" rel="stylesheet" type="text/css" />
<link href="../../styles/pages.css" rel="stylesheet" type="text/css" />
<link href="../../styles/impresion.css" rel="stylesheet" type="text/css" />
</head>

<body>
<!-- START BLOCK : datos -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Modificaci&oacute;n de Pagos Pendientes de Infonavit</p>
  <form action="./fac_inf_pen_mod.php" method="get" name="form">
    <input name="tmp" type="hidden" id="tmp" />
    <table class="tabla">
    <tr>
      <th class="vtabla" scope="row">Compa&ntilde;&iacute;a</th>
      <td class="vtabla"><input name="num_cia" type="text" class="insert" id="num_cia" onfocus="tmp.value=this.value;this.select()" onchange="if (isInt(this,tmp)) cambiaCia()" onkeydown="if (event.keyCode == 13) this.blur()" size="3" />
        <input name="nombre_cia" type="text" disabled="disabled" class="vnombre" id="nombre_cia" size="30" /></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Empleado</th>
      <td class="vtabla"><select name="id_emp" class="insert" id="id_emp">
        <option value=""></option>
      </select>
      </td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Administrador</th>
      <td class="vtabla"><select name="idadmin" class="insert" id="idadmin">
        <option value="" selected="selected"></option>
        <!-- START BLOCK : admin -->
		<option value="{id}">{admin}</option>
		<!-- END BLOCK : admin -->
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
		myConn.connect('./fac_inf_pen_mod.php', 'GET', 'c=' + get_val(f.num_cia), obtenerCia);
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
		myConn.connect('./fac_inf_pen_mod.php', 'GET', 'ce=' + get_val(f.num_cia), generarListadoEmpleados);
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
	f.submit();
}

window.onload = f.num_cia.select();
//-->
</script>
<!-- END BLOCK : datos -->
<!-- START BLOCK : listado -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
<p class="title">Modificaci&oacute;n de Pagos Pendientes de Infonavit {anyo}</p>
<form action="fac_inf_pen_mod.php" method="post" name="form"><table align="center" class="tabla">
  <!-- START BLOCK : cia -->
  <tr>
    <th colspan="{colspan}" class="vtabla" scope="col" style="font-size:12pt;">{num_cia} {nombre} </th>
  </tr>
  <tr>
    <th class="tabla">Empleado</th>
    <!-- START BLOCK : column_name -->
	<th class="tabla"{colspan}>{mes}</th>
	<!-- END BLOCK : column_name -->
  </tr>
  <!-- START BLOCK : row -->
  <tr>
    <td class="vtabla">{num} {nombre} </td>
    <!-- START BLOCK : cel -->
	{cel}
	<!-- END BLOCK : cel -->
  </tr>
  <!-- END BLOCK : row -->
  <tr>
    <th colspan="{colspan_total}" class="rtabla">Total Compañ&iacute;a </th>
	<th class="rtabla">{total}</th>
  </tr>
  <tr>
    <td colspan="{colspan}" class="tabla">&nbsp;</td>
  </tr>
  <!-- END BLOCK : cia -->
  <tr>
    <th colspan="{colspan_total}" class="rtabla">Total General </th>
	<th class="rtabla" style="font-size:14pt;">{total}</th>
  </tr>
</table>
<p>
<input type="button" class="boton" value="Regresar" onclick="document.location='fac_inf_pen_mod.php'" />
&nbsp;&nbsp;
<input type="button" class="boton" value="Borrar" onclick="borrar()" />
</p></form>
</td>
</tr>
</table>
<script language="javascript" type="application/javascript">
<!--
var f = document.form;

function borrar() {
	var id = [], j = 0;
	
	if (f.id.length == undefined && f.id.checked)
		id[j++] = f.id.value;
	else
		for (var i = 0; i < f.id.length; i++)
			if (f.id[i].checked)
				id[j++] = f.id[i].value;
	
	if (id.length == 0) {
		alert('Debe seleccionar al menos un registro');
		return false;
	}
	
	if (!confirm('¿Desea borrar los registros seleccionados?'))
		return false;
	
	var string = '';
	
	for (var i = 0; i < id.length; i++)
		string += (i > 0 ? '&' : '') + 'id[]=' + id[i];
	
	var myConn = new XHConn();
	
	if (!myConn)
		alert("XMLHTTP no disponible. Trate con un nuevo/mejor navegador.");
	
	myConn.connect('./fac_inf_pen_mod.php', 'POST', string, postBorrar);
}

var postBorrar = function(oXML) {
	document.location.reload();
}
-->
</script>
<!-- END BLOCK : listado -->
</body>
</html>
