<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Untitled Document</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../../styles/pages.css" rel="stylesheet" type="text/css">
<link href="../../styles/tablas.css" rel="stylesheet" type="text/css">
</head>

<body>
<!-- START BLOCK : modificar -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Modificar Cargo </p>
  <table class="tabla">
  <tr>
    <th class="tabla" scope="row">Compa&ntilde;&iacute;a</th>
    <th class="tabla">Cuenta</th>
  </tr>
  <tr>
    <td class="tabla" scope="row">{num_cia} - {nombre_cia} </td>
    <td class="tabla">{cuenta}</td>
  </tr>
</table>
  <br>
  <form action="./ban_esc_car_minimod.php" method="post" name="form">
  <input name="temp" type="hidden">
  <input name="id" type="hidden" value="{id}">
  <table class="tabla">
    <tr>
      <th class="vtabla" scope="row">Fecha <font size="-2">(ddmmaa)</font> </th>
      <td class="vtabla"><input name="fecha" type="text" class="insert" id="fecha" onChange="actualiza_fecha(this)" onKeyDown="if (event.keyCode == 13) concepto.select();" value="{fecha}" size="10" maxlength="10"></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Concepto</th>
      <td class="vtabla"><input name="concepto" type="text" class="vinsert" id="concepto" onKeyDown="if (event.keyCode == 13) fecha.select();" value="{concepto}" size="50" maxlength="200"></td>
    </tr>
  </table>  
  <p>
    <input type="button" class="boton" value="Cancelar" onClick="self.close()">
&nbsp;&nbsp;    
<input type="button" class="boton" value="Modificar" onClick="valida_registro(form)">
  </p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
	function valida_registro(form) {
		if (form.fecha.value == "") {
			alert("Debe especificar la fecha");
			form.fecha.select();
			return false;
		}
		else if (form.concepto.value == "") {
			alert("Debe especificar el concepto");
			form.concepto.select();
			return false;
		}
		else
			if (confirm("¿Son correctos los datos?"))
				form.submit();
			else
				form.fecha.select();
	}
	
	window.onload = document.form.fecha.select();
</script>
<!-- END BLOCK : modificar -->
<!-- START BLOCK : cerrar -->
<script language="javascript" type="text/javascript">
	function cerrar() {
		window.opener.document.location.reload();
		self.close();
	}
	
	window.onload = cerrar();
</script>
<!-- END BLOCK : cerrar -->
<!-- START BLOCK : cerrar_error -->
<script language="javascript" type="text/javascript">window.onload = self.close()</script>
<!-- END BLOCK : cerrar_error -->
</body>
</html>
