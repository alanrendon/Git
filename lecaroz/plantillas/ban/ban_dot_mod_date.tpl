<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Documento sin t&iacute;tulo</title>
<link href="../../styles/tablas.css" rel="stylesheet" type="text/css" />
<link href="../../styles/pages.css" rel="stylesheet" type="text/css" />
</head>

<body>
<!-- START BLOCK : mod -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
<form action="./ban_dot_mod_date.php" method="post" name="form">
  <input name="tmp" type="hidden" id="tmp" />
  <input name="id" type="hidden" id="id" value="{id}" />
  <table class="tabla">
  <tr>
    <th class="tabla" scope="col">Compa&ntilde;&iacute;a</th>
    <th class="tabla" scope="col">Fecha</th>
    <th class="tabla" scope="col">Importe</th>
  </tr>
  <tr>
    <td class="tabla"><input name="num_cia" type="text" class="insert" id="num_cia" onfocus="tmp.value=this.value;this.select()" onchange="if (isInt(this,tmp)) cambiaCia()" onkeydown="if (event.keyCode == 13) fecha.select()" value="{num_cia}" size="3" />
      <input name="nombre" type="text" disabled="disabled" class="vnombre" id="nombre" value="{nombre}" size="30" /></td>
    <td class="tabla"><input name="fecha" type="text" class="insert" id="fecha" onfocus="tmp.value=this.value;this.select()" onchange="inputDateFormat(this)" onkeydown="if (event.keyCode == 13) next.focus()" value="{fecha}" size="10" maxlength="10" /></td>
    <td class="rtabla">{importe}</td>
  </tr>
</table>
  <p>
    <input type="button" class="boton" value="Cancelar" onclick="self.close()" />
  &nbsp;&nbsp;
  <input name="next" type="button" class="boton" id="next" onclick="validar()" value="Modificar" />
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
	}
	else {
		var myConn = new XHConn();
	
		if (!myConn)
			alert("XMLHTTP no disponible. Trate con un nuevo/mejor navegador.");
		
		// Pedir datos
		myConn.connect('./ban_dot_mod_date.php', 'GET', 'c=' + get_val(f.num_cia), obtenerCia);
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

function validar() {
	if (get_val(f.num_cia) <= 0) {
		alert('Debe especificar la compañía');
		f.num_cia.select();
	}
	else if (f.fecha.length < 8) {
		alert('Debe poner la fecha de captura');
		f.fecha.select();
	}
	else if (confirm('¿Son correctos los datos?'))
		f.submit();
	else
		f.fecha.select();
}

window.onload = f.fecha.select();
//-->
</script>
<!-- END BLOCK : mod -->
<!-- START BLOCK : close -->
<script language="javascript" type="text/javascript">
<!--
function cerrar() {
	window.opener.document.location.reload();
	self.close();
}

window.onload = cerrar();
//-->
</script>
<!-- END BLOCK : close -->
</body>
</html>
