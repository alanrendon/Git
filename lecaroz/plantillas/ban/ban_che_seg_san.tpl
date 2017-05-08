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
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Chequera de Seguridad Santander</p>
  <form action="./ban_che_seg_san.php" method="get" name="form"><table class="tabla">
    <tr>
      <th class="vtabla" scope="row">D&iacute;as de Vigencia</th>
      <td class="tabla"><input name="dias" type="text" class="insert" id="dias" onFocus="tmp.value=this.value" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) this.blur()" value="182" size="3" maxlength="3"></td>
    </tr>
  </table>  <p>
    <input type="button" class="boton" value="Generar Archivo" onClick="validar(this.form)">
  </p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
<!--
function validar(form) {
	var dias = !isNaN(parseInt(form.dias.value)) ? parseInt(form.dias.value) : 0;

	if (dias < 30) {
		alert("La vigencia de los cheques debe ser igual o mayor a un mes");
		form.dias.value = 30;
		form.dias.select();
		return false;
	}
	else if (dias > 182) {
		alert("La vigencia de los cheques no puede ser mayor a 6 meses");
		form.dias.value = 182;
		form.dias.select();
		return false;
	}
	else if (confirm("¿Desea generar el archivo para la chequera de seguridad con los últimos cheques emitidos?")) {
		form.submit();
	}
	else {
		form.dias.select();
		return false;
	}
}

window.onload = document.form.dias.select();
-->
</script>
</body>
</html>
