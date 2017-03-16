<form name="Datos" class="FormValidator FormStyles" id="Datos">
	<input name="id" type="hidden" value="{id}">
	<table class="tabla_captura">
		<tr class="linea_off">
			<th align="left" scope="row">Nombre del curso</th>
			<td><input name="nombre_curso" type="text" class="valid toText cleanText toUpper" id="nombre_curso" value="{nombre_curso}" size="50" maxlength="200" /></td>
		</tr>
		<tr class="linea_on">
			<th align="left" scope="row">Periodo de aplicaci&oacute;n</th>
			<td><input name="fecha_inicio" type="text" class="valid Focus toDate center" id="fecha_inicio" value="{fecha_inicio}" size="10" maxlength="10" />
				al
				<input name="fecha_termino" type="text" class="valid Focus toDate center" id="fecha_termino" value="{fecha_termino}" size="10" maxlength="10" /></td>
		</tr>
		<tr class="linea_off">
			<th align="left" scope="row">Descripci&oacute;n del curso</th>
			<td><textarea name="descripcion_curso" cols="45" rows="5" class="valid toText toUpper" id="descripcion_curso">{descripcion_curso}</textarea></td>
		</tr>
	</table>
	<p>
		<input type="button" name="cancelar" id="cancelar" value="Cancelar" />
		&nbsp;&nbsp;
		<input type="button" name="modificar" id="modificar" value="Modificar" />
	</p>
</form>
