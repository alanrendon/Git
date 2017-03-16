<script language="javascript" type="text/javascript">
	function valida_registro() {
		if (parseInt(document.form.cod_mov.value) <= 0) {
			alert("Debe especificar el código del movimiento");
			document.form.cod_mov.select();
			return false;
		}
		else if (document.form.descripcion.value == null) {
			alert("Debe escribir la descripción del código");
			document.form.descripcion.select();
			return false;
		}
		else
			if (confirm("¿Son correctos los datos?"))
				document.form.submit();
			else
				document.form.cod_mov.select();
	}
</script>
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
<p class="title">Alta de Movimientos Bancarios </p>
<form name="form" method="post" action="./ban_cat_mov.php?tabla={tabla}">
<input type="hidden" name="temp">
<table class="tabla">
  <tr>
    <th class="vtabla" scope="row">C&oacute;digo de movimiento </th>
    <td class="vtabla"><input name="cod_mov" type="text" class="vinsert" id="cod_mov" onFocus="form.temp.value=this.value" onChange="isInt(this,form.temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 40) form.cod_banco1.select();" value="{cod_mov}" size="5" maxlength="3"></td>
  </tr>
  <tr>
    <th class="vtabla" scope="row">C&oacute;digo en banco</th>
    <td class="vtabla"><input name="cod_banco1" type="text" class="vinsert" id="cod_banco1" onFocus="form.temp.value=this.value" onChange="isInt(this,form.temp)" onKeyDown="if (event.keyCode == 40) form.descripcion.select();
else if (event.keyCode == 38) form.cod_mov.select();
else if (event.keyCode == 13 || event.keyCode == 39) form.cod_banco2.select();" value="{cod_banco}" size="5" maxlength="3">
      <input name="cod_banco2" type="text" class="vinsert" id="cod_banco2" onFocus="form.temp.value=this.value" onChange="isInt(this,form.temp)" onKeyDown="if (event.keyCode == 40) form.descripcion.select();
else if (event.keyCode == 38) form.cod_mov.select();
else if (event.keyCode == 13 || event.keyCode == 39) form.cod_banco3.select();
else if (event.keyCode == 37) form.cod_banco1.select();" value="{cod_banco}" size="5" maxlength="3">
      <input name="cod_banco3" type="text" class="vinsert" id="cod_banco3" onFocus="form.temp.value=this.value" onChange="isInt(this,form.temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 40) form.descripcion.select();
else if (event.keyCode == 38) form.cod_mov.select();
else if (event.keyCode == 37) form.cod_banco2.select();" value="{cod_banco}" size="5" maxlength="3"></td>
  </tr>
  <tr>
    <th class="vtabla" scope="row">Descripci&oacute;n</th>
    <td class="vtabla"><input name="descripcion" type="text" class="vinsert" id="descripcion" onKeyDown="if (event.keyCode == 13 || event.keyCode == 40) form.cod_mov.select();
else if (event.keyCode == 38) form.cod_mov.select();" size="30" maxlength="30"></td>
  </tr>
  <tr>
    <th class="vtabla" scope="row">Tipo de movimiento </th>
    <td class="vtabla"><select name="tipo_mov" class="insert" id="tipo_mov">
      <option value="TRUE" selected>CARGO</option>
      <option value="FALSE">ABONO</option>
    </select></td>
  </tr>
  <tr>
    <th class="vtabla" scope="row">Aplica al balance</th>
    <td class="vtabla"><input name="entra_bal" type="radio" value="TRUE" checked>
      Si&nbsp;
      <input name="entra_bal" type="radio" value="FALSE">
      No</td>
  </tr>
</table>
<p>
  <input name="enviar" type="button" class="boton" id="enviar" onClick="valida_registro()" value="Alta de c&oacute;digo">
</p>
</form>
</td>
</tr>
</table>
<script language="javascript" type="text/javascript">window.onload = document.form.cod_mov.select();</script>
