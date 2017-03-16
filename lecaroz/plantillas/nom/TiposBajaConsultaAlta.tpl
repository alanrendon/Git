<form name="Datos" class="FormValidator FormStyles" id="Datos">
	<table class="tabla_captura">
		<tr>
			<th align="left" scope="row">Nombre tipo</th>
			<td><input name="nombre_tipo_baja" type="text" class="valid toText clearText toUpper" id="nombre_tipo_baja" size="40" maxlength="255"></td>
		</tr>
		<tr>
			<th align="left" scope="row">Permite reingreso</th>
			<td><input name="permite_reingreso" type="radio" value="FALSE" checked="checked" />
				No
				<input name="permite_reingreso" type="radio" value="TRUE" />
				Si</td>
		</tr>
	</table>
	<p>
		<input type="button" name="cancelar" id="cancelar" value="Cancelar" />
		&nbsp;&nbsp;
		<input type="button" name="alta" id="alta" value="Alta" />
	</p>
</form>
