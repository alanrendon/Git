<form name="Datos" id="Datos" class="FormValidator FormStyles">
	<table class="tabla_captura" id="Tabla">
		<tr>
			<th scope="col">Compa&ntilde;&iacute;a</th>
			<th scope="col">Producto</th>
			<th scope="col">Proveedor</th>
			<th scope="col">Presentaci&oacute;n</th>
			<th scope="col">Cantidad</th>
		</tr>
		<tr class="linea_off">
			<td align="center" nowrap="nowrap"><input name="num_cia[]" type="text" class="valid Focus toPosInt right" id="num_cia" size="3" /><input name="nombre_cia[]" type="text" disabled="disabled" id="nombre_cia" size="30" /></td>
			<td align="center" nowrap="nowrap"><input name="codmp[]" type="text" class="valid Focus toPosInt right" id="codmp" size="3" /><input name="nombre_mp[]" type="text" disabled="disabled" id="nombre_mp" size="30" />
			<input type="hidden" name="existencia[]" id="existencia">
			<input type="hidden" name="consumo[]" id="consumo"></td>
			<td align="center"><select name="num_pro[]" id="num_pro" style="width:98%;">
			</select></td>
			<td align="center"><select name="presentacion[]" id="presentacion" style="width:98%">
			</select></td>
			<td align="center"><input name="cantidad[]" type="text" class="valid Focus numberPosFormat right" precision="2" id="cantidad" size="8" /></td>
		</tr>
	</table>
	<p>
		<input type="button" name="cancelar" id="cancelar" value="Cancelar" />
	&nbsp;&nbsp;
	<input type="button" name="registrar" id="registrar" value="Registrar pedido" />
	</p>
</form>