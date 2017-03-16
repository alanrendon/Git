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
<script language="javascript" type="text/javascript">
	function valida_registro() {
		if (document.form.num_cia.value <= 0) {
			alert("Debe especificar una compañía");
			document.form.num_cia.select();
			return false;
		}
		else
			document.form.submit();
	}
</script>
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
<p class="title">Saldo Inicial </p>
<form name="form" method="get" action="./ban_sal_ini.php">
<input name="temp" type="hidden">
<table class="tabla">
  <tr>
    <th class="vtabla" scope="row">Compa&ntilde;&iacute;a</th>
    <td class="vtabla"><input name="num_cia" type="text" class="insert" id="num_cia" onFocus="form.temp.value=this.value" onChange="isInt(this,form.temp)" size="3" maxlength="3"></td>
  </tr>
</table>
<p>
  <input name="enviar" type="button" class="boton" id="enviar" value="Siguiente">
</p>
</form>
</td>
</tr>
</table>
<script language="javascript" type="text/javascript">window.onload = document.form.num_cia.select();</script>
<!-- END BLOCK : datos -->
<!-- START BLOCK : captura -->
<script language="javascript" type="text/javascript">
	function valida_registro() {
		if (document.form.saldo_libros.value < 0) {
			alert("Debe especificar el saldo en libros");
			document.form.saldo_libros.select();
			return false;
		}
		else if (document.form.saldo_bancos.value < 0) {
			alert("Debe especificar el saldo en bancos");
			document.form.saldo_bancos.select();
		}
		else
			if (confirm("¿Son correctos los datos?"))
				document.form.submit();
			else
				return false;
	}
</script>
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
<p class="title">Saldo Inicial </p>
<form name="form" method="post" action="./ban_sal_ini.php?tabla={tabla}">
<input name="temp" type="hidden">
<table class="tabla">
  <tr>
    <th class="vtabla" scope="row">Compa&ntilde;&iacute;a</th>
    <td class="vtabla"><input name="num_cia" type="hidden" id="num_cia" value="{num_cia}">
    {num_cia} - {nombre_cia} </td>
  </tr>
  <tr>
    <th class="vtabla" scope="row">Saldo en libros </th>
    <td class="vtabla"><input name="saldo_libros" type="text" class="vinsert" id="saldo_libros" onFocus="form.temp.value=this.value" onChange="isFloat(this,2,form.temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 40) form.saldo_bancos.select();
else if (event.keyCode == 38) form.saldo_libros.select();" size="12" maxlength="12"></td>
  </tr>
  <tr>
    <th class="vtabla" scope="row">Saldo en bancos </th>
    <td class="vtabla"><input name="saldo_bancos" type="text" class="vinsert" id="saldo_bancos" onFocus="form.temp.value=this.value" onChange="isFloat(this,2,form.temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 40) form.saldo_libros.select();
else if (event.keyCode == 38) form.saldo_bancos.select();" size="12" maxlength="12"></td>
  </tr>
</table>

<p>
  <input name="enviar" type="button" class="boton" id="enviar" value="Capturar" onClick="valida_registro()">
</p>
</form>
</td>
</tr>
</table>
<script language="javascript" type="text/javascript">window.onload = document.form.saldo_libros.select();</script>
<!-- END BLOCK : captura -->
</body>
</html>
