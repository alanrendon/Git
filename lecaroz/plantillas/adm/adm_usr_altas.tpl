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
		window.opener.document.location.reload();
		self.close();
	}
	
	window.onload = cerrar();
</script>
<!-- END BLOCK : cerrar -->
<!-- START BLOCK : alta -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Alta de Usuario de Sistema</p>
  <form action="./adm_usr_altas.php" method="post" name="form"><table class="tabla">
    <tr>
      <th class="vtabla" scope="row">Nombre de Usuario</th>
      <td class="vtabla"><input name="username" type="text" class="vinsert" id="username2" onKeyDown="if (event.keyCode == 13 || event.keyCode == 40) password.select();
else if (event.keyCode == 38) apellido.select();" size="20" maxlength="20"></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Contrase&ntilde;a</th>
      <td class="vtabla"><input name="password" type="password" class="vinsert" id="password" onKeyDown="if (event.keyCode == 13 || event.keyCode == 40) temp_pass.select();
else if (event.keyCode == 38) username.select();" size="20" maxlength="20"></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Repetir contrase&ntilde;a </th>
      <td class="vtabla"><input name="temp_pass" type="password" class="vinsert" id="temp_pass" onKeyDown="if (event.keyCode == 13 || event.keyCode == 40) nombre.select();
else if (event.keyCode == 38) password.select();" size="20" maxlength="20"></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Nombre(s)</th>
      <td class="vtabla"><input name="nombre" type="text" class="vinsert" id="nombre" onKeyDown="if (event.keyCode == 13 || event.keyCode == 40) apellido.select();
else if (event.keyCode == 38) temp_pass.select();" size="20" maxlength="20"></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Apellido(s)</th>
      <td class="vtabla"><input name="apellido" type="text" class="vinsert" id="apellido" onKeyDown="if (event.keyCode == 13 || event.keyCode == 40) username.select();
else if (event.keyCode == 38) nombre.select();" size="35" maxlength="35"></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Men&uacute;s</th>
      <td class="vtabla"><input name="menu1" type="checkbox" id="menu1" value="TRUE"> Panaderías<br>
        <input name="menu2" type="checkbox" id="menu2" value="TRUE">
        Rosticer&iacute;a<br>
        <input name="menu3" type="checkbox" id="menu3" value="TRUE">
        Proveedores y Facturas<br>
        <input name="menu4" type="checkbox" id="menu4" value="TRUE">
        Pedidos<br>
        <input name="menu5" type="checkbox" id="menu5" value="TRUE">
        Rentas<br>
        <input name="menu6" type="checkbox" id="menu6" value="TRUE">
        Bancos<br>
        <input name="menu7" type="checkbox" id="menu7" value="TRUE">
        Balances<br>
        <input name="menu8" type="checkbox" id="menu8" value="TRUE">
        Administrador</td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Capturista</th>
      <td class="vtabla"><input name="capturista" type="checkbox" id="capturista" value="TRUE">
        Si</td>
    </tr>
  </table>  
  <p>
    <input type="button" class="boton" value="Cancelar" onClick="self.close()"> 
&nbsp;&nbsp;
<input type="button" class="boton" value="Alta" onClick="valida_registro(form)"> 
</p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
	function valida_registro(form) {
		if (form.username.value.length < 4) {
			alert("Debe especificar el nombre de usuario (4 letras mínimo)");
			form.username.select();
			return false;
		}
		else if (form.password.value.length < 4) {
			alert("Debe especificar la contraseña (4 letras mínimo)");
			form.password.select();
			return false;
		}
		else if (form.password.value != form.temp_pass.value) {
			alert("Las contraseñas no coinciden");
			form.password.select();
			return false;
		}
		else if (form.nombre.value == "") {
			alert("Debe especificar el nombre");
			form.nombre.select();
			return false;
		}
		else
			if (confirm("¿Son corfectos los datos?"))
				form.submit();
			else
				form.username.select();
	}
	
	window.onload = document.form.username.select();
</script>
<!-- END BLOCK : alta -->
</body>
</html>
