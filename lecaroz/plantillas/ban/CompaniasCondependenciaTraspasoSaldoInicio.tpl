<form name="Datos" class="FormValidator FormStyles" id="Datos">
	<table class="tabla_captura">
		<tr class="linea_off">
			<th align="left" scope="row">Compañía(s)</th>
			<td><input name="cias" type="text" class="valid toInterval" id="cias" size="40" /></td>
		</tr>
		<tr class="linea_on">
			<th align="left" scope="row">Banco</th>
			<td><select name="banco" id="banco">
				<option value="1" selected="selected">BANORTE</option>
				<option value="2">SANTANDER</option>
			</select></td>
		</tr>
	</table>
	<p>
		<input type="button" name="siguiente" id="siguiente" value="Siguiente" />
	</p>
</form>