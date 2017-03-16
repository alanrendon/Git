<!-- START BLOCK : datos -->
<script language="javascript" type="text/javascript">
	function valida_registro() {
		if (document.form.tipo == "codigo" && document.form.cod_mov <= 0) {
			alert("Debe especificar un código");
			document.form.cod_mov.select();
		}
		else
			document.form.submit();
	}
</script>
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
<p class="title">Modificaci&oacute;n del Cat&aacute;logo de Movimientos Bancarios </p>
<form name="form" method="get" action="./ban_cat_mov_mod.php">
<table class="tabla">
  <tr>
    <th class="vtabla" scope="row"><input name="mov" type="radio" value="mod" checked>
    C&oacute;digo
    <input name="cod_mov" type="text" class="insert" id="cod_mov" size="3" maxlength="3"></th>
  </tr>
  <tr>
    <th class="vtabla" scope="row"><input name="mov" type="radio" value="lis">
      Listar c&oacute;digos </th>
  </tr>
</table>

<p>
  <input name="enviar" type="submit" class="boton" id="enviar2" value="Modificar" onClick="valida_registro();">
</p>
</form>
</td>
</tr>
</table>
<!-- END BLOCK : datos -->

<!-- START BLOCK : listado -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
<p class="title">Modificaci&oacute;n del Cat&aacute;logo de Movimientos Bancarios </p>
<table class="tabla">
  <tr>
    <th class="tabla" scope="col">C&oacute;digo</th>
    <th class="tabla" scope="col">C&oacute;digo en Banco </th>
    <th class="tabla" scope="col">Descripci&oacute;n</th>
    <th class="tabla" scope="col">Tipo</th>
    <th class="tabla" scope="col">Aplica balance </th>
    <th class="tabla" scope="col">&nbsp;</th>
  </tr>
  <!-- START BLOCK : fila -->
  <tr>
    <td class="vtabla">{cod_mov}</td>
    <td class="tabla">
	<!-- START BLOCK : cod_banco -->
	{cod_banco}
	<!-- END BLOCK : cod_banco -->
	</td>
    <td class="vtabla">{descripcion}</td>
    <td class="tabla">{tipo_mov}</td>
    <td class="tabla">{entra_bal}</td>
    <td class="tabla"><input name="modificar" type="button" class="boton" id="modificar" onClick="window.location = './ban_cat_mov_mod.php?mov=mod&cod_mov={cod_mov}';" value="Modificar">
    <input name="eliminar" type="button" class="boton" id="eliminar" onClick="if (confirm('¿Desea borrar el registro?'))
window.location = './ban_cat_mov_mod.php?mov=del&cod_mov={cod_mov}';
else return false;" value="Eliminar"></td>
  </tr>
  <!-- END BLOCK : fila -->
</table>
</td>
</tr>
</table>
<!-- END BLOCK : listado -->

<!-- START BLOCK : modificar -->
<script language="javascript" type="text/javascript">
	function valida_registro() {
		if (confirm("¿Son correctos los datos?"))
			document.form.submit();
		else return false;
	}
</script>
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
<p class="title">Modificaci&oacute;n del Cat&aacute;logo de Movimientos Bancarios </p>
<form name="form" method="post" action="./ban_cat_mov_mod.php?tabla={tabla}">
<table class="tabla">
  <tr>
    <th class="vtabla" scope="row">C&oacute;digo de Movimiento </th>
    <td class="vtabla"><input name="cod_mov" type="hidden" value="{cod_mov}">
      <strong>{cod_mov}</strong></td>
  </tr>
  <tr>
    <th class="vtabla" scope="row">C&oacute;digo en banco </th>
    <td class="vtabla"><input name="id1" type="hidden" value="{id1}"><input name="cod_banco1" type="text" class="vinsert" id="cod_banco1" onFocus="form.temp.value=this.value" onChange="isInt(this,form.temp)" onKeyDown="if (event.keyCode == 40) form.descripcion.select();
else if (event.keyCode == 13 || event.keyCode == 39) form.cod_banco2.select();" value="{cod_banco1}" size="5" maxlength="3">
      <input name="id2" type="hidden" value="{id2}"><input name="cod_banco2" type="text" class="vinsert" id="cod_banco2" onFocus="form.temp.value=this.value" onChange="isInt(this,form.temp)" onKeyDown="if (event.keyCode == 40) form.descripcion.select();
else if (event.keyCode == 13 || event.keyCode == 39) form.cod_banco3.select();
else if (event.keyCode == 37) form.cod_banco1.select();" value="{cod_banco2}" size="5" maxlength="3">
      <input name="id3" type="hidden" value="{id3}"><input name="cod_banco3" type="text" class="vinsert" id="cod_banco3" onFocus="form.temp.value=this.value" onChange="isInt(this,form.temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 40) form.descripcion.select();
else if (event.keyCode == 37) form.cod_banco2.select();" value="{cod_banco3}" size="5" maxlength="3"></td>
  </tr>
  <tr>
    <th class="vtabla" scope="row">Descripci&oacute;n</th>
    <td class="vtabla"><input name="descripcion" type="text" class="vinsert" id="descripcion" onKeyDown="if (event.keyCode == 13 || event.keyCode == 40) form.moficar.focus();
else if (event.keyCode == 38) form.cod_banco1.select();" value="{descripcion}" size="30" maxlength="30"></td>
  </tr>
  <tr>
    <th class="vtabla" scope="row">Tipo de movimiento </th>
    <td class="vtabla"><select name="tipo_mov" class="insert" id="tipo_mov">
      <option value="TRUE" {cargo}>CARGO</option>
      <option value="FALSE" {abono}>ABONO</option>
    </select></td>
  </tr>
  <tr>
    <th class="vtabla" scope="row">Aplica al balance </th>
    <td class="vtabla"><input name="entra_bal" type="radio" value="TRUE" {si}>
      Si&nbsp;
      <input name="entra_bal" type="radio" value="FALSE" {no}>
      No</td>
  </tr>
</table>

<p>
  <input name="modificar" type="button" class="boton" id="modificar" value="Modificar" onClick="valida_registro()">
</p>
</form>
</td>
</tr>
</table>
<script language="javascript" type="text/javascript">window.onload = document.form.cod_banco1.select()</script>
<!-- END BLOCK : modificar -->
