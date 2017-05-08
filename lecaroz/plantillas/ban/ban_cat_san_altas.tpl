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
<td align="center" valign="middle"><p class="title">Alta de Movimientos Bancarios Santander</p>
  <form action="./ban_cat_san_altas.php" method="post" name="form"><table class="tabla">
    <tr>
      <th class="vtabla" scope="row">C&oacute;digo de Movimiento</th>
      <td class="vtabla"><input name="cod_mov" type="text" class="insert" id="cod_mov" onFocus="temp.value=this.value" onChange="isInt(this, temp)" onKeyDown="if (event.keyCode == 13) cod_banco[0].select()" value="{cod_mov}" size="4" maxlength="3"></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">C&oacute;digo en el Banco </th>
      <td class="vtabla"><input name="cod_banco[]" type="text" class="insert" id="cod_banco" onFocus="temp.value=this.value" onChange="isInt(this, temp)" onKeyDown="if (event.keyCode == 13) cod_banco[1].select()" size="4" maxlength="4">
        <input name="cod_banco[]" type="text" class="insert" id="cod_banco" onFocus="temp.value=this.value" onChange="isInt(this, temp)" onKeyDown="if (event.keyCode == 13) cod_banco[2].select()" size="4" maxlength="4">
        <input name="cod_banco[]" type="text" class="insert" id="cod_banco" onFocus="temp.value=this.value" onChange="isInt(this, temp)" onKeyDown="if (event.keyCode == 13) cod_banco[3].select()" size="4" maxlength="4">
        <input name="cod_banco[]" type="text" class="insert" id="cod_banco" onFocus="temp.value=this.value" onChange="isInt(this, temp)" onKeyDown="if (event.keyCode == 13) cod_banco[4].select()" size="4" maxlength="4">
		<input name="cod_banco[]" type="text" class="insert" id="cod_banco" onFocus="temp.value=this.value" onChange="isInt(this, temp)" onKeyDown="if (event.keyCode == 13) cod_banco[5].select()" size="4" maxlength="4">
		<input name="cod_banco[]" type="text" class="insert" id="cod_banco" onFocus="temp.value=this.value" onChange="isInt(this, temp)" onKeyDown="if (event.keyCode == 13) cod_banco[6].select()" size="4" maxlength="4">
		<input name="cod_banco[]" type="text" class="insert" id="cod_banco" onFocus="temp.value=this.value" onChange="isInt(this, temp)" onKeyDown="if (event.keyCode == 13) cod_banco[7].select()" size="4" maxlength="4">
		<input name="cod_banco[]" type="text" class="insert" id="cod_banco" onFocus="temp.value=this.value" onChange="isInt(this, temp)" onKeyDown="if (event.keyCode == 13) cod_banco[8].select()" size="4" maxlength="4">
		<input name="cod_banco[]" type="text" class="insert" id="cod_banco" onFocus="temp.value=this.value" onChange="isInt(this, temp)" onKeyDown="if (event.keyCode == 13) cod_banco[9].select()" size="4" maxlength="4">
        <input name="cod_banco[]" type="text" class="insert" id="cod_banco" onFocus="temp.value=this.value" onChange="isInt(this, temp)" onKeyDown="if (event.keyCode == 13) descripcion.select()" size="4" maxlength="4"></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Descripci&oacute;n</th>
      <td class="vtabla"><input name="descripcion" type="text" class="vinsert" id="descripcion" onKeyDown="if (event.keyCode == 13) cod_mov.select()" size="30" maxlength="30"></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Tipo de Movimiento</th>
      <td class="vtabla"><input name="tipo_mov" type="radio" value="FALSE" checked>
        Abono
          <input name="tipo_mov" type="radio" value="TRUE">
          Cargo</td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Aplica al Balance </th>
      <td class="vtabla"><input name="entra_bal" type="radio" value="TRUE" checked>
        Si
          <input name="entra_bal" type="radio" value="FALSE">
          No</td>
    </tr>
  </table>  
  <p>
    <input type="button" class="boton" value="Alta de Código" onClick="validar(this.form)">
  </p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
<!--
function validar(form) {
	if (form.cod_mov.value <= 0) {
		alert("Debe especificar el código de movimiento");
		form.cod_mov.select();
		return false;
	}
	else if (form.descripcion.value.length < 5) {
		alert("Debe escribir la descripcion del movimiento");
		form.descripcion.focus();
		return false;
	}
	else {
		var count = 0;
		
		for (i = 0; i < form.cod_banco.length; i++) {
			count += parseInt(form.cod_banco[i].value) > 0 || form.cod_banco[i].value == "0" ? 1 : 0;
		}
		
		if (count == 0) {
			alert("Debe asociar el movimiento al menos con un movimiento");
			form.cod_banco[0].select();
			return false;
		}
		else if (confirm("¿Son correctos los datos?")) {
			form.submit();
		}
		else {
			form.cod_mov.select();
			return false;
		}
	}
}

window.onload = document.form.cod_mov.select();
-->
</script>
</body>
</html>
