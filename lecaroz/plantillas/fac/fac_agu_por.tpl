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
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Cat&aacute;logo de Porcentajes para Aguinaldos</p>
  <form action="./fac_agu_por.php" method="get" name="form">
  <input name="temp" type="hidden">
  <table class="tabla">
    <tr>
      <th class="tabla" scope="col">Porcentaje Anterior </th>
      <th class="tabla" scope="col">Nuevo Porcentaje </th>
    </tr>
    <tr>
      <td class="tabla"><input name="porcentaje_ant" type="hidden" id="porcentaje_ant" value="{porcentaje_ant}">
      {porcentaje}</td>
      <td class="tabla"><input name="porcentaje_nuevo" type="text" class="insert" id="porcentaje_nuevo" onFocus="temp.value=this.value" onChange="isFloat(this,2,temp)" onKeyDown="if (event.keyCode == 13) return false" value="{porcentaje_ant}" size="6" maxlength="6"></td>
    </tr>
  </table>
  <p>    
<input type="button" class="boton" value="Actualizar" onClick="valida_registro(form)"> 
    </p>
  </form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
	function valida_registro(form) {
		if (confirm("¿Son correctos los datos?"))
			form.submit();
		else
			form.porcentaje_nuevo.select();
	}
	
	window.onload = document.form.porcentaje_nuevo.select();
</script>
</body>
</html>
