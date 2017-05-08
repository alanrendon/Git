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
<!-- START BLOCK : list -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Modificaci&oacute;n de Movimientos Bancarios Santander </p>
  <table class="tabla">
    <tr>
      <th class="tabla" scope="col">C&oacute;digo</th>
      <th class="tabla" scope="col">C&oacute;digo en Banco</th>
      <th class="tabla" scope="col">Descripci&oacute;n</th>
      <th class="tabla" scope="col">Tipo</th>
      <th class="tabla" scope="col">Aplica Balance </th>
      <th class="tabla" scope="col">&nbsp;</th>
    </tr>
    <!-- START BLOCK : row -->
	<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
      <td class="tabla">{cod_mov}</td>
      <td class="tabla">{cod_banco}</td>
      <td class="vtabla">{descripcion}</td>
      <td class="tabla">{tipo_mov}</td>
      <td class="tabla">{bal}</td>
      <td class="tabla"><input type="button" class="boton" value="Modificar" onClick="mod({cod_mov})">
        <input type="button" class="boton" value="Borrar" onClick="del({cod_mov})"></td>
    </tr>
	<!-- END BLOCK : row -->
	<!-- START BLOCK : no_result -->
	<tr>
	  <td colspan="6" class="tabla">No hay movimientos </td>
	  </tr>
	<!-- END BLOCK : no_result -->
  </table></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
<!--
function mod(cod_mov) {
	document.location='./ban_cat_san_mod.php?mod=' + cod_mov;
}

function del(cod_mov) {
	if (confirm("¿Desea borrar el movimiento?")) {
		document.location='./ban_cat_san_mod.php?del=' + cod_mov;
	}
	else {
		return false;
	}
}
-->
</script>
<!-- END BLOCK : list -->
<!-- START BLOCK : mod -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Modificaci&oacute;n de Movimientos Bancarios Santander</p>
  <form action="./ban_cat_san_mod.php" method="post" name="form"><table class="tabla">
    <tr>
      <th class="vtabla" scope="row">C&oacute;digo de Movimiento</th>
      <td class="vtabla"><input name="cod_mov" type="text" class="insert" id="cod_mov" onFocus="temp.value=this.value" onChange="isInt(this, temp)" onKeyDown="if (event.keyCode == 13) cod_banco[0].select()" value="{cod_mov}" size="4" maxlength="3" readonly="true"></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">C&oacute;digo en el Banco </th>
      <td class="vtabla"><input name="cod_banco[]" type="text" class="insert" id="cod_banco" onFocus="temp.value=this.value" onChange="isInt(this, temp)" onKeyDown="if (event.keyCode == 13) cod_banco[1].select()" value="{cod_banco0}" size="4" maxlength="4">
        <input name="cod_banco[]" type="text" class="insert" id="cod_banco" onFocus="temp.value=this.value" onChange="isInt(this, temp)" onKeyDown="if (event.keyCode == 13) cod_banco[2].select()" value="{cod_banco1}" size="4" maxlength="4">
        <input name="cod_banco[]" type="text" class="insert" id="cod_banco" onFocus="temp.value=this.value" onChange="isInt(this, temp)" onKeyDown="if (event.keyCode == 13) cod_banco[3].select()" value="{cod_banco2}" size="4" maxlength="4">
        <input name="cod_banco[]" type="text" class="insert" id="cod_banco" onFocus="temp.value=this.value" onChange="isInt(this, temp)" onKeyDown="if (event.keyCode == 13) cod_banco[4].select()" value="{cod_banco3}" size="4" maxlength="4">
		<input name="cod_banco[]" type="text" class="insert" id="cod_banco" onFocus="temp.value=this.value" onChange="isInt(this, temp)" onKeyDown="if (event.keyCode == 13) cod_banco[5].select()" value="{cod_banco4}" size="4" maxlength="4">
		<input name="cod_banco[]" type="text" class="insert" id="cod_banco" onFocus="temp.value=this.value" onChange="isInt(this, temp)" onKeyDown="if (event.keyCode == 13) cod_banco[6].select()" value="{cod_banco5}" size="4" maxlength="4">
		<input name="cod_banco[]" type="text" class="insert" id="cod_banco" onFocus="temp.value=this.value" onChange="isInt(this, temp)" onKeyDown="if (event.keyCode == 13) cod_banco[7].select()" value="{cod_banco6}" size="4" maxlength="4">
		<input name="cod_banco[]" type="text" class="insert" id="cod_banco" onFocus="temp.value=this.value" onChange="isInt(this, temp)" onKeyDown="if (event.keyCode == 13) cod_banco[8].select()" value="{cod_banco7}" size="4" maxlength="4">
		<input name="cod_banco[]" type="text" class="insert" id="cod_banco" onFocus="temp.value=this.value" onChange="isInt(this, temp)" onKeyDown="if (event.keyCode == 13) cod_banco[9].select()" value="{cod_banco8}" size="4" maxlength="4">
        <input name="cod_banco[]" type="text" class="insert" id="cod_banco" onFocus="temp.value=this.value" onChange="isInt(this, temp)" onKeyDown="if (event.keyCode == 13) cod_banco[10].select()" value="{cod_banco9}" size="4" maxlength="4">
		<br />
		<input name="cod_banco[]" type="text" class="insert" id="cod_banco" onFocus="temp.value=this.value" onChange="isInt(this, temp)" onKeyDown="if (event.keyCode == 13) cod_banco[11].select()" value="{cod_banco10}" size="4" maxlength="4">
        <input name="cod_banco[]" type="text" class="insert" id="cod_banco" onFocus="temp.value=this.value" onChange="isInt(this, temp)" onKeyDown="if (event.keyCode == 13) cod_banco[12].select()" value="{cod_banco11}" size="4" maxlength="4">
        <input name="cod_banco[]" type="text" class="insert" id="cod_banco" onFocus="temp.value=this.value" onChange="isInt(this, temp)" onKeyDown="if (event.keyCode == 13) cod_banco[13].select()" value="{cod_banco12}" size="4" maxlength="4">
        <input name="cod_banco[]" type="text" class="insert" id="cod_banco" onFocus="temp.value=this.value" onChange="isInt(this, temp)" onKeyDown="if (event.keyCode == 13) cod_banco[14].select()" value="{cod_banco13}" size="4" maxlength="4">
		<input name="cod_banco[]" type="text" class="insert" id="cod_banco" onFocus="temp.value=this.value" onChange="isInt(this, temp)" onKeyDown="if (event.keyCode == 13) cod_banco[15].select()" value="{cod_banco14}" size="4" maxlength="4">
		<input name="cod_banco[]" type="text" class="insert" id="cod_banco" onFocus="temp.value=this.value" onChange="isInt(this, temp)" onKeyDown="if (event.keyCode == 13) cod_banco[16].select()" value="{cod_banco15}" size="4" maxlength="4">
		<input name="cod_banco[]" type="text" class="insert" id="cod_banco" onFocus="temp.value=this.value" onChange="isInt(this, temp)" onKeyDown="if (event.keyCode == 13) cod_banco[17].select()" value="{cod_banco16}" size="4" maxlength="4">
		<input name="cod_banco[]" type="text" class="insert" id="cod_banco" onFocus="temp.value=this.value" onChange="isInt(this, temp)" onKeyDown="if (event.keyCode == 13) cod_banco[18].select()" value="{cod_banco17}" size="4" maxlength="4">
		<input name="cod_banco[]" type="text" class="insert" id="cod_banco" onFocus="temp.value=this.value" onChange="isInt(this, temp)" onKeyDown="if (event.keyCode == 13) cod_banco[19].select()" value="{cod_banco18}" size="4" maxlength="4">
        <input name="cod_banco[]" type="text" class="insert" id="cod_banco" onFocus="temp.value=this.value" onChange="isInt(this, temp)" onKeyDown="if (event.keyCode == 13) descripcion.select()" value="{cod_banco19}" size="4" maxlength="4"></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Descripci&oacute;n</th>
      <td class="vtabla"><input name="descripcion" type="text" class="vinsert" id="descripcion" onKeyDown="if (event.keyCode == 13) cod_banco[0].select()" value="{descripcion}" size="30" maxlength="30"></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Tipo de Movimiento</th>
      <td class="vtabla"><input name="tipo_mov" type="radio" value="FALSE" {tipo_f}>
        Abono
          <input name="tipo_mov" type="radio" value="TRUE" {tipo_t}>
          Cargo</td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Aplica al Balance </th>
      <td class="vtabla"><input name="entra_bal" type="radio" value="TRUE" {bal_t}>
        Si
          <input name="entra_bal" type="radio" value="FALSE" {bal_f}>
          No</td>
    </tr>
  </table>  
  <p>
    <input type="button" class="boton" value="Cancelar" onClick="document.location='./ban_cat_san_mod.php'">
&nbsp;&nbsp;    
<input type="button" class="boton" value="Modificar" onClick="validar(this.form)">
  </p>
  </form></td>
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

window.onload = document.form.cod_banco[0].select();
-->
</script>
<!-- END BLOCK : mod -->
</body>
</html>
