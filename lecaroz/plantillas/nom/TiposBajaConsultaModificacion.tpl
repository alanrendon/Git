<form name="Datos" class="FormValidator FormStyles" id="Datos">
	<input name="id" type="hidden" value="{id}" />
	<table class="tabla_captura">
		<tr>
			<th align="left" scope="row">Nombre tipo</th>
			<td><input name="nombre_tipo_baja" type="text" class="valid toText clearText toUpper" id="nombre_tipo_baja" value="{nombre_tipo_baja}" size="40" maxlength="255" /></td>
		</tr>
		<tr>
			<th align="left" scope="row">Permite reingreso</th>
			<td><input name="permite_reingreso" type="radio" value="FALSE"{permite_reingreso_f} />
				No
				<input name="permite_reingreso" type="radio" value="TRUE"{permite_reingreso_t} />
				Si</td>
		</tr>
	</table>
	<p>
		<input type="button" name="cancelar" id="cancelar" value="Cancelar" />
		&nbsp;&nbsp;
		<input type="button" name="modificar" id="modificar" value="Modificar" />
	</p>
</form>
