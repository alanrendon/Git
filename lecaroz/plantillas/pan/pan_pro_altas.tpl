<!-- tabla control_produccion -->
<script type="text/javascript" language="JavaScript">
	function valida_registro() {
		if(document.form.num_cia.value <= 0) {
			alert('Debe especificar una compañia');
			document.form.num_cia.select();
		}
		else if(document.form.cod_producto.value <= 0) {
			alert('Debe especificar una compañia');
			document.form.cod_producto.select();
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
<form name="form" action="./inser_pan_pro_altas.php?tabla={tabla}" method="post" onKeyDown="if (event.keyCode == 13) document.form.enviar.focus();">
<table class="tabla">
    <tr>
      <th width="271" class="vtabla">N&uacute;mero de Compa&ntilde;&iacute;a</th>
      <td width="123" class="vtabla"><input name="num_cia" type="text" class="insert" id="num_cia" size="5" maxlength="5"></td>
    </tr>
    <tr>
      <th class="vtabla">N&uacute;mero de Turno</th>
      <td class="vtabla">
	    <select name="cod_turno" class="insert">
		<!-- START BLOCK : turno -->
			<option value="{value}">{value} - {nombre}</option>
		<!-- END BLOCK : turno -->
		</select>
	  </td>
    </tr>
    <tr>
      <th class="vtabla">C&oacute;digo del Producto </th>
      <td class="vtabla"><input name="cod_producto" type="text" class="insert" id="cod_producto" size="5" maxlength="5"></td>
    </tr>
	<tr>
      <th class="vtabla">N&uacute;mero de orden </th>
      <td class="vtabla"><input name="num_orden" type="text" class="insert" id="num_orden" size="5" maxlength="5"></td>
    </tr>
    <tr>
      <th class="vtabla">Precio de raya por unidad producida </th>
      <td class="vtabla"><input name="precio_raya" type="text" class="insert" id="precio_raya_unidad" value="0" size="10" maxlength="6"></td>
    </tr>
    <tr>
      <th class="vtabla">Porcentaje de raya sobre la produccion </th>
      <td class="vtabla"><input name="porc_raya" type="text" class="insert" id="porc_raya_produccion" value="0" size="10" maxlength="5"></td>
    </tr>
    <tr>
      <th class="vtabla">Precio de venta por unidad </th>
      <td class="vtabla"><input name="precio_venta" type="text" class="insert" id="precio_venta_unidad" value="0" size="10" maxlength="5"></td>
    </tr>
  </table>
  <p>
  <img src="./menus/insert.gif" align="middle">&nbsp;&nbsp;<input type="button" name="enviar" class="boton" value="Capturar" onclick='valida_registro()'>
  <br><br>
  <img src="./menus/delete.gif" align="middle">&nbsp;&nbsp;<input type="button" class="boton" value="Borrar formulario" onclick='borrar()'>
  </p>
</form>
</td>
</tr>
</table>