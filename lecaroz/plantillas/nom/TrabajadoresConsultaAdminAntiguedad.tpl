<form name="DatosAntiguedad" class="FormValidator FormStyles" id="DatosAntiguedad">
	<input name="id" type="hidden" id="id" value="{id}">
	<input name="i" type="hidden" id="i" value="{i}">
	<table class="tabla_captura">
		<tr class="linea_off">
			<th align="left" scope="row">Trabajador</th>
			<td nowrap>{nombre}</td>
		</tr>
		<tr class="linea_on">
			<th align="left" scope="row">Puesto</th>
			<td nowrap>{puesto}</td>
		</tr>
		<tr class="linea_off">
			<th align="left" scope="row">Turno</th>
			<td nowrap>{turno}</td>
		</tr>
		<tr class="linea_on">
			<td colspan="2" align="left" scope="row">&nbsp;</td>
		</tr>
		<tr class="linea_off">
			<th align="left" scope="row"><input name="tipo_antiguedad" type="radio" value="calculo" checked="checked" />
			Cálculo</th>
			<td nowrap><select name="anios" id="anios">
					<!-- START BLOCK : anio -->
					<option value="{value}"{selected}>{text}</option>
					<!-- END BLOCK : anio -->
				</select>
				Año(s)
				<select name="meses" id="meses">
					<!-- START BLOCK : mes -->
					<option value="{value}"{selected}>{text}</option>
					<!-- END BLOCK : mes -->
				</select>
				Mes(es)</td>
		</tr>
		<tr class="linea_on">
			<th align="left" scope="row"><input type="radio" name="tipo_antiguedad" value="fecha" />
			Fecha</th>
			<td><input name="fecha_alta" type="text" class="valid Focus toDate center" id="fecha_alta" value="{fecha_alta}" size="10" maxlength="10" /></td>
		</tr>
	</table>
	<p>
		<input type="button" name="cancelar" id="cancelar" value="Cancelar" />
	&nbsp;&nbsp;
	<input type="button" name="actualizar" id="actualizar" value="Actualizar" />
	</p>
</form>