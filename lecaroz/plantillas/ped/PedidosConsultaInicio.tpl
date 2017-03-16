<form name="Datos" class="FormValidator FormStyles" id="Datos">
	<table class="tabla_captura">
		<tr class="linea_off">
			<th align="left" scope="row">Folio(s)</th>
			<td><input name="folios" type="text" class="valid toInterval" id="folios" size="30" /></td>
		</tr>
		<tr class="linea_on">
			<th align="left" scope="row">Compa&ntilde;&iacute;a(s)</th>
			<td><input name="cias" type="text" class="valid toInterval" id="cias" size="30" /></td>
		</tr>
		<tr class="linea_off">
			<th align="left" scope="row">Administrador</th>
			<td><select name="admin" id="admin">
				<option value=""></option>
				<!-- START BLOCK : admin -->
				<option value="{value}">{text}</option>
				<!-- END BLOCK : admin -->
			</select></td>
		</tr>
		<tr class="linea_on">
			<th align="left" scope="row">Producto(s)</th>
			<td><input name="mps" type="text" class="valid toInterval" id="mps" size="30" /></td>
		</tr>
		<tr class="linea_off">
			<th align="left" scope="row">Proveedor(es)</th>
			<td><input name="pros" type="text" class="valid toInterval" id="pros" size="30" /></td>
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
			<th align="left" scope="row">Omitir proveedore(es)</th>
			<td><input name="omitir_pros" type="text" class="valid toInterval" id="omitir_pros" size="30" /></td>
		</tr>
		<tr class="linea_off">
			<th align="left" scope="row">Periodo</th>
			<td><input name="fecha1" type="text" class="valid Focus toDate center" id="fecha1" size="10" maxlength="10" /> 
				al 
					<input name="fecha2" type="text" class="valid Focus toDate center" id="fecha2" size="10" maxlength="10" /></td>
		</tr>
	</table>
	<br />
	<p>
		<input name="consultar" type="button" class="boton" id="consultar" value="Consultar" />
	</p>
</form>