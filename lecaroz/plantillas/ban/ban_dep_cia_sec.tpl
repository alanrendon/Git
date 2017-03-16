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
<td align="center" valign="middle"><table class="tabla">
  <tr>
    <th class="tabla" scope="col">Compa&ntilde;ia</th>
    <th class="tabla" scope="col">Banco</th>
    <th class="tabla" scope="col">Fecha</th>
    <th class="tabla" scope="col">Importe</th>
  </tr>
  <tr>
    <td class="tabla">{num_cia} {nombre_cia} </td>
    <td class="tabla">{banco}</td>
    <td class="tabla">{fecha}</td>
    <td class="tabla">{importe}</td>
  </tr>
</table>
  <br />
  <form action="./ban_dep_cia_sec.php" method="post" name="form">
    <input name="tmp" type="hidden" id="tmp" />
    <input name="id" type="hidden" id="id" value="{id}" />
    <table class="tabla">
    <tr>
      <th class="tabla" scope="col">Cambiar a </th>
    </tr>
    <tr>
      <td class="tabla"><input name="num_cia_sec" type="text" class="insert" id="num_cia_sec" onfocus="tmp.value=this.value;this.select()" onchange="if (isInt(this,tmp)) cambiaCia()" onkeydown="if (event.keyCode == 13) this.blur()" value="{num_cia_sec}" size="3" />
        <input name="nombre_cia_sec" type="text" disabled="disabled" class="vnombre" id="nombre_cia_sec" value="{nombre_cia_sec}" size="40" /></td>
    </tr>
  </table>  <p>
    <input type="button" class="boton" value="Cancelar" onclick="self.close()" />
    &nbsp;&nbsp;
    <input name="cambiar" type="button" class="boton" id="cambiar" onclick="validar()" value="Cambiar" />
  </p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
<!--
var f = document.form;

function cambiaCia() {
	if (f.num_cia_sec.value == '' || f.num_cia_sec.value == '0') {
		f.num_cia_sec.value = '';
		f.nombre_cia_sec.value = '';
	}
	else {
		var myConn = new XHConn();
	
		if (!myConn)
			alert("XMLHTTP no disponible. Trate con un nuevo/mejor navegador.");
		
		// Pedir datos
		myConn.connect('./ban_dep_cia_sec.php', 'GET', 'c=' + get_val(f.num_cia_sec), obtenerCia);
	}
}

var obtenerCia = function (oXML) {
	var result = oXML.responseText;
	
	if (result == '') {
		alert('La compañía no se encuentra en el catálogo');
		f.num_cia_sec.value = f.tmp.value;
		f.num_cia_sec.select();
	}
	else
		f.nombre_cia_sec.value = result;
}

function validar() {
	if (confirm('¿Desea cambiar el origen del depósito?'))
		f.submit();
	else
		f.num_cia_sec.select();
}

window.onload = f.num_cia_sec.select();
//-->
</script>
<!-- END BLOCK : mod -->
<!-- START BLOCK : cerrar -->
<script language="javascript" type="text/javascript">
<!--
function cerrar() {
	window.opener.document.location.reload();
	self.close();
}

window.onload = cerrar();
//-->
</script>
<!-- END BLOCK : cerrar -->
</body>
</html>
