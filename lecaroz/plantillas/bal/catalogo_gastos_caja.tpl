<!-- tabla control_produccion -->
<script type="text/javascript" language="JavaScript">
	function valida_registro() {
		if(document.form.num_gasto.value <= 0) {
			alert('Debe especificar codigo');
			document.form.tipo_res.select();
		}
		else if(document.form.descripcion == "") {
			alert('Debe especificar la descripcion');
			document.form.descripcion.select();
		}

		else {
			if (confirm("¿Son correctos los datos del formulario?"))
				document.form.submit();
			else
				document.form.num_cia.select();
		}
	}
	
	function borrar() {
		if (confirm("¿Desea borrar el formulario?")) {
			document.form.reset();
			document.form.num_cia.select();
		}
		else
			document.form.num_cia.select();
	}
	
	
</script>
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
<p class="title">CAPTURA AL CATALOGO DE GASTOS DE OFICINA </P>
<form name="form" action="./insert_cat_gastos_caja.php?tabla={tabla}" method="post" onKeyDown="if (event.keyCode == 13) document.form.enviar.focus();">
<table class="tabla">
    <tr>
      <th width="271" class="vtabla">N&uacute;mero gasto </th>
      <td width="123" class="vtabla" align="center"><input name="num_gasto" type="hidden" class="insert" id="num_gasto" size="10" maxlength="5" value="{num_gasto}">
      {num_gasto}</td>
    </tr>
    <tr>
      <th class="vtabla">Descripci&oacute;n</th>
      <td class="vtabla"><input name="descripcion" type="text" class="vinsert" id="descripcion" size="50" maxlength="50">
	  </td>
    </tr>
  </table>
  <p>
  <img src="./menus/insert.gif" align="middle">&nbsp;&nbsp;<input type="button" name="enviar" class="boton" value="Capturar" onclick='valida_registro()'>
  &nbsp;
  <img src="./menus/delete.gif" align="middle">&nbsp;&nbsp;<input type="button" class="boton" value="Borrar formulario" onclick='borrar()'>
  </p>
</form>

</td>
</tr>
</table>
<script language="javascript" type="text/javascript">
	window.onload = document.form.descripcion.select();
</script>