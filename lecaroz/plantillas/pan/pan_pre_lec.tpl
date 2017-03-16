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
<td align="center" valign="middle"><p class="title">Facturas del Rancho<br>
    Precio por Litro de Leche </p>
  <form action="./pan_pre_lec.php" method="get" name="form">
    <input name="tmp" type="hidden" id="tmp">
    <table class="tabla">
    <tr>
      <th class="vtabla" scope="col">Precio</th>
      <td class="vtabla" scope="col"><input name="precio" type="text" class="insert" id="precio" onFocus="tmp.value=this.value;this.select()" onChange="isFloat(this,2,tmp)" onKeyDown="if (event.keyCode == 13) this.blur()" value="{precio}" size="8" maxlength="8"></td>
    </tr>
  </table>  <p>
    <input type="button" class="boton" value="Actualizar" onClick="validar(this.form)">
  </p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
<!--
function validar(form) {
	if (form.precio.value <= 0) {
		alert("Debe especificar el precio");
		form.precio.select();
		return false;
	}
	else
		form.submit();
}

window.onload = document.form.precio.select();
-->
</script>
</body>
</html>
