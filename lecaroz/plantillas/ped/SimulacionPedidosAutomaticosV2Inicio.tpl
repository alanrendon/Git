<!-- START BLOCK : normal -->
<form name="Datos" class="FormValidator FormStyles" id="Datos">
	<table class="tabla_captura">
		<tr class="linea_off">
			<th align="left" scope="row">Compa&ntilde;&iacute;a(s)</th>
			<td><input name="cias" type="text" class="valid toInterval" id="cias" size="30" /></td>
		</tr>
		<tr class="linea_on">
			<th align="left" scope="row">Administrador</th>
			<td><select name="admin" id="admin">
				<option value=""></option>
				<!-- START BLOCK : admin -->
				<option value="{value}">{text}</option>
				<!-- END BLOCK : admin -->
			</select></td>
		</tr>
		<tr class="linea_off">
			<th align="left" scope="row">Producto(s)</th>
			<td><input name="mps" type="text" class="valid toInterval" id="mps" size="30" /></td>
		</tr>
		<tr class="linea_on">
			<th align="left" scope="row">Omitir compa&ntilde;&iacute;a(s)</th>
			<td><input name="omitir_cias" type="text" class="valid toInterval" id="omitir_cias" size="30" /></td>
		</tr>
		<tr class="linea_off">
			<th align="left" scope="row">Omitir producto(s)</th>
			<td><input name="omitir_mps" type="text" class="valid toInterval" id="omitir_mps" size="30" /></td>
		</tr>
		<tr class="linea_on">
			<th align="left" scope="row">Existencia</th>
			<td><input name="existencia" type="radio" value="1" checked="checked" />
				Al d&iacute;a
				<input type="radio" name="existencia" value="2" />
				Inicio de mes 
				<input type="radio" name="existencia" id="radio" value="3" />
				Inventario contado</td>
		</tr>
		<tr class="linea_off">
			<th align="left" scope="row">D&iacute;as pedidos </th>
			<td><input name="dias" type="text" class="valid Focus toPosInt center red" id="dias" value="37" size="4" /></td>
		</tr>
		<tr class="linea_on">
			<th align="left" scope="row">Complemento del mes</th>
			<td><input name="complemento" type="checkbox" id="complemento" value="1" />
				Si</td>
		</tr>
	</table>
	<br />
	<p>
		<input name="simular" type="button" class="boton" id="simular" value="Simular proceso" />
	</p>
</form>
<!-- END BLOCK : normal -->
<!-- START BLOCK : ipad -->
<form name="Datos" class="FormValidator FormStyles" id="Datos">
	<table class="tabla_captura">
		<tr class="linea_off">
			<th align="left" scope="row">Compa&ntilde;&iacute;a</th>
			<td><select name="cias" id="cias">
				<!-- START BLOCK : cia -->
				<option value="{value}">{text}</option>
				<!-- END BLOCK : cia -->
			</select></td>
		</tr>
		<tr class="linea_on">
			<th align="left" scope="row">Existencia</th>
			<td><input name="existencia" type="radio" value="1" checked="checked" />
				Al d&iacute;a
				<input type="radio" name="existencia" value="2" />
				Inicio de mes 
				<input type="radio" name="existencia" id="radio" value="3" />
				Inventario contado</td>
		</tr>
		<tr class="linea_off">
			<th align="left" scope="row">D&iacute;as pedidos </th>
			<td><input name="dias" type="text" class="valid Focus toPosInt center red" id="dias" value="37" size="4" /></td>
		</tr>
		<tr class="linea_on">
			<th align="left" scope="row">Complemento del mes</th>
			<td><input name="complemento" type="checkbox" id="complemento" value="1" />
				Si</td>
		</tr>
	</table>
	<br />
	<p>
		<input name="simular" type="button" class="boton" id="simular" value="Simular proceso" />
	</p>
</form>
<!-- END BLOCK : ipad -->