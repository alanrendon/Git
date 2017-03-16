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
<!-- START BLOCK : datos -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Modificar Nombre </p>
  <form action="./ban_nom_mod.php" method="post" name="form">
    <input name="id" type="hidden" id="id" value="{id}">
    <table class="tabla">
    <tr>
      <th class="vtabla" scope="row">N&uacute;mero</th>
      <td class="vtabla"><input name="num" type="text" class="insert" id="num" value="{num}" size="3" readonly="true"></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Nombre</th>
      <td class="vtabla"><input name="nombre" type="text" class="vinsert" id="nombre" onKeyDown="if (event.keyCode == 13)  mod.focus()" value="{nombre}" size="50" maxlength="100"></td>
    </tr>
  </table>  <p>
    <input type="button" class="boton" value="Cancelar" onClick="self.close()">
&nbsp;&nbsp;    
<input name="mod" type="button" class="boton" id="mod" value="Modificar" onClick="validar()">
  </p>
  </form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
<!--
var f = document.form;

function validar() {
	if (f.nombre.length < 3) {
		alert("Debe escribir un nombre o frase");
		f.nombre.focus();
		return false;
	}
	else if (confirm("¿Son correctos los datos?"))
		f.submit();
	else
		nombre.focus();
}

window.onload = f.nombre.focus();
//-->
</script>
<!-- END BLOCK : datos -->
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
