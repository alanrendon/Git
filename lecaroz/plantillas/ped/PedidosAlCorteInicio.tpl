<form name="Datos" class="FormValidator FormStyles" id="Datos">
	<table class="tabla_captura">
		<tr class="linea_off">
			<th align="left" scope="row">Producto</th>
			<td><input name="codmp" type="text" class="valid Focus toPosInt right" id="codmp" size="3" />
			<input name="nombre_mp" type="text" id="nombre_mp" size="30" /></td>
		</tr>
		<tr class="linea_on">
			<th align="left" scope="row">Compa&ntilde;&iacute;a(s)</th>
			<td><input name="cias" type="text" class="valid toInterval" id="cias" style="width:98%;" size="30" /></td>
		</tr>
		<tr class="linea_off">
			<th align="left" scope="row">Omitir compa&ntilde;&iacute;a(s)</th>
			<td><input name="omitir_cias" type="text" class="valid toInterval" id="omitir_cias" style="width:98%;" size="30" /></td>
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
			<th align="left" scope="row">Fecha de corte</th>
			<td><input name="fecha" type="text" class="valid Focus toDate center" id="fecha" value="{fecha}" size="10" maxlength="10" /></td>
		</tr>
		<tr class="linea_on">
			<th align="left" scope="row">Hojas al día</th>
			<td><input name="fecha_hoja" type="text" class="valid Focus toDate center" id="fecha_hoja" value="{fecha_hoja}" size="10" maxlength="10" /></td>
		</tr>
	</table>
	<p>
		<input type="button" name="siguiente" id="siguiente" value="Siguiente" />
	</p>
</form>