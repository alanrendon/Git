<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Untitled Document</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../../styles/impresion.css" rel="stylesheet" type="text/css">
<link href="../../styles/pages.css" rel="stylesheet" type="text/css">
<link href="../../styles/tablas.css" rel="stylesheet" type="text/css">
</head>

<body>
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Alta de Accionista</p>
  <form name="form" action="./adm_acc_altas.php?tabla={tabla}" method="post">
  <input name="temp" type="hidden">
  <table class="tabla">
    <tr>
      <th class="vtabla" scope="row">N&uacute;mero de accionista </th>
      <td class="vtabla"><input name="num" type="text" class="insert" id="num" onFocus="form.temp.value=this.value" onChange="isInt(this,form.temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 40) form.nombre.select();
else if (event.keyCode == 38) form.nombre_corto.select();" value="{num}" size="3" maxlength="3"></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Nombre</th>
      <td class="vtabla"><input name="nombre" type="text" class="vinsert" id="nombre" onKeyDown="if (event.keyCode == 13 || event.keyCode == 40) form.ap_pat.select();
else if (event.keyCode == 38) form.num.select();" size="50" maxlength="50"></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Apellido paterno </th>
      <td class="vtabla"><input name="ap_pat" type="text" class="vinsert" id="ap_pat" onKeyDown="if (event.keyCode == 13 || event.keyCode == 40) form.ap_mat.focus();
else if (event.keyCode == 38) form.nombre.select();" size="50" maxlength="50"></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Apellido materno</th>
      <td class="vtabla"><input name="ap_mat" type="text" class="vinsert" id="ap_mat" onKeyDown="if (event.keyCode == 13 || event.keyCode == 40) form.nombre_corto.select();
else if (event.keyCode == 38) form.ap_pat.select();" size="50" maxlength="50"></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Nombre corto </th>
      <td class="vtabla"><input name="nombre_corto" type="text" class="vinsert" id="nombre_corto" onKeyDown="if (event.keyCode == 13) form.alta.focus();
else if (event.keyCode == 40) form.num.select();
else if (event.keyCode == 38) form.num.select();" size="50" maxlength="50"></td>
    </tr>
  </table>  <p>
    <input name="alta" type="button" class="boton" id="alta" value="Alta de Accionista" onClick="valida_registro()">
  </p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
	function valida_registro() {
		if (document.form.num.value <= 0) {
			alert("Debe especificar el número de accionista");
			document.form.num.select();
			return false;
		}
		else if (document.form.nombre.value == "") {
			alert("Debe escribir el nombre");
			document.form.nombre.select();
			return false;
		}
		else if (document.form.ap_pat.value == "") {
			alert("Debe escribir el apellido paterno");
			document.form.ap_pat.select();
			return false;
		}
		else if (document.form.nombre_corto.value == "") {
			alert("Debe escribir el nombre corto del accionista");
			document.form.nombre_corto.select();
			return false;
		}
		else
			if (confirm("¿Son correctos los datos?"))
				document.form.submit();
			else
				document.form.nombre.select();
	}
	
	window.onload = document.form.num.select();
</script>
</body>
</html>
