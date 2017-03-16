<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252" />
<title>Documento sin t&iacute;tulo</title>
<link href="../../styles/tablas.css" rel="stylesheet" type="text/css" />
<link href="../../styles/pages.css" rel="stylesheet" type="text/css" />
</head>

<body>
<!-- START BLOCK : datos -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Generador de C&oacute;digos de Autorizaci&oacute;n </p>
  <form action="./adm_gen_cod.php" method="get" name="form">
    <input name="tmp" type="hidden" id="tmp" />
    <table class="tabla">
    <tr>
      <th class="vtabla" scope="row">Compa&ntilde;&iacute;a</th>
      <td class="vtabla"><input name="num_cia" type="text" class="insert" id="num_cia" onfocus="tmp.value=this.value;this.select()" onchange="if (isInt(this,tmp)) cambiaCia()" onkeydown="if (event.keyCode == 13) generar.focus()" size="3" />
        <input name="nombre" type="text" disabled="disabled" class="vnombre" id="nombre" size="40" /></td>
    </tr>
  </table>  <p>
    <input name="generar" type="button" class="boton" id="generar" value="Generar" onclick="validar()" />
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
		myConn.connect('./adm_gen_cod.php', 'GET', 'c=' + get_val(f.num_cia), obtenerCia);
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
}

function validar() {
	if (get_val(f.num_cia) <= 0) {
		alert('Debe especificar la compañía');
		f.num_cia.select();
		return false;
	}
	else {
		var myConn = new XHConn();
	
		if (!myConn)
			alert("XMLHTTP no disponible. Trate con un nuevo/mejor navegador.");
		
		// Pedir datos
		myConn.connect('./adm_gen_cod.php', 'POST', 'c=' + get_val(f.num_cia), codigo);
	}
}

var codigo = function (oXML) {
	var result = oXML.responseText;
	
	if (result == '') {
		alert('No se pudo generar el código');
		f.num_cia.select();
	}
	else {
		/*var win = window.open('', '', 'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=no,width=200,height=100');
		win.document.writeln(result);
		win.focus();*/alert('CODIGO DE AUTORIZACION: ' + result);
		return;
	}
}

window.onload = f.num_cia.select();
//-->
</script>
<!-- END BLOCK : datos -->
<!-- START BLOCK : codigo -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p style="font-size:16pt; font-weight:bold;">{codigo}</p>
  <p>
    <input type="button" class="boton" value="Cerrar" onclick="self.close()" />
  </p></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
<!--
window.onload = return;
//-->
</script>
<!-- END BLOCK : codigo -->
</body>
</html>
