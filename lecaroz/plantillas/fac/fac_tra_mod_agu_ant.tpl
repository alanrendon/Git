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
		window.opener.document.form.aguinaldo_ant[{i}].value = "{aguinaldo}";
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
    <th colspan="2" class="tabla" scope="col">Nombre</th>
    </tr>
  <tr>
    <td colspan="2" class="tabla" scope="col"><strong>{nombre}</strong></td>
    </tr>
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
<form action="./fac_tra_mod_agu_ant.php" method="post" name="form" onKeyDown="if (event.keyCode == 13) return false">
<input type="hidden" name="id" value="{id}">
<input type="hidden" name="i" value="{i}">
<input type="hidden" name="fecha" value="{fecha}">
<table class="tabla">
  <tr>
    <th class="vtabla" scope="row">Aguinaldo</th>
    <td class="vtabla"><input name="aguinaldo" type="text" class="rinsert" id="aguinaldo" value="" size="10" maxlength="10"></td>
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
	function validar(form) {
		if (form.aguinaldo.value <= 0) {
			alert("Debe especificar el importe del aguinaldo");
			form.aguinaldo.select();
			return false;
		}
		else
			form.submit();
	}
	
	window.onload = document.form.aguinaldo.select();
</script>
<!-- END BLOCK : modificar -->
</body>
</html>
