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
<!-- START BLOCK : cerrar -->
<script language="javascript" type="text/javascript">
	function cerrar() {
		window.opener.document.form.nombre[{i}].value = "{nombre}";
		self.close();
	}
	
	window.onload = cerrar();
</script>
<!-- END BLOCK : cerrar -->
<!-- START BLOCK : modificar -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
<table class="tabla">
  <tr>
    <th class="tabla" scope="col">Puesto</th>
    <th class="tabla" scope="col">Turno</th>
    </tr>
  <tr>
    <td class="tabla"><strong>{puesto}</strong></td>
    <td class="tabla"><strong>{turno}</strong></td>
    </tr>
</table>
<br>
<form action="./fac_tra_mod_nom.php" method="post" name="form">
<input type="hidden" name="id" value="{id}">
<input type="hidden" name="i" value="{i}">
<input type="hidden" name="tmp" id="tmp">
<table class="tabla">
  <tr>
    <th class="vtabla" scope="row">Esta en</th>
    <td class="vtabla"><input name="num_cia_emp" type="text" class="insert" id="num_cia_emp" onFocus="tmp.value=this.value;this.select()" onChange="if(isInt(this,tmp))cambiaCia(this,nombre_cia_emp)" onKeyDown="if(event.keyCode==13)ap_paterno.select()" value="{num_cia_emp}" size="3">
      <input name="nombre_cia_emp" type="text" disabled class="vnombre" id="nombre_cia_emp" value="{nombre_cia_emp}" size="30"></td>
  </tr>
  <tr>
    <td colspan="2" class="vtabla" scope="row">&nbsp;</td>
    </tr>
  <tr>
    <th class="vtabla" scope="row">Ap. Paterno</th>
    <td class="vtabla"><input name="ap_paterno" type="text" class="vinsert" id="ap_paterno" onKeyDown="if (event.keyCode == 13) ap_materno.select()" value="{ap_paterno}" size="20" maxlength="20"></td>
  </tr>
  <tr>
    <th class="vtabla" scope="row">Ap. Materno </th>
    <td class="vtabla"><input name="ap_materno" type="text" class="vinsert" id="ap_materno" onKeyDown="if (event.keyCode == 13) nombre.select()" value="{ap_materno}" size="20" maxlength="20"></td>
  </tr>
  <tr>
    <th class="vtabla" scope="row">Nombre</th>
    <td class="vtabla"><input name="nombre" type="text" class="vinsert" id="nombre" onKeyDown="if (event.keyCode == 13) num_cia_emp.select()" value="{nombre}" size="20" maxlength="20"></td>
  </tr>
</table>
  <p>
    <input type="button" class="boton" value="Cancelar" onClick="self.close()">
&nbsp;&nbsp;
<input type="button" class="boton" value="Modificar" onClick="validar(this.form)">
</p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
<!--

function cambiaCia(num_cia_emp, nombre_cia_emp) {
	if (num_cia_emp.value == '' || num_cia_emp.value == '0') {
		num_cia_emp.value = '';
		nombre_cia_emp.value = '';
	}
	else {
		var myConn = new XHConn();
	
		if (!myConn)
			alert("XMLHTTP no disponible. Trate con un nuevo/mejor navegador.");
		
		// Pedir datos
		myConn.connect('./fac_tra_mod_nom.php', 'GET', 'c=' + get_val(num_cia_emp), obtenerCia);
	}
}

var obtenerCia = function (oXML) {
	var result = oXML.responseText;
	
	if (result == '') {
		alert('La compañía no se encuentra en el catálogo');
		document.form.num_cia_emp.value = document.form.tmp.value;
		document.form.num_cia_emp.select();
	}
	else
		document.form.nombre_cia_emp.value = result;
}

function validar(form) {
	if (form.ap_paterno.value == "") {
		alert("Debe especificar el apellido paterno");
		form.ap_paterno.select();
		return false;
	}
	else if (form.nombre.value == "") {
		alert("Debe especificar el nombre");
		form.nombre.select();
		return false;
	}
	else {
		form.submit();
	}
}

window.onload = document.form.ap_paterno.select();
-->
</script>
<!-- END BLOCK : modificar -->
</body>
</html>
