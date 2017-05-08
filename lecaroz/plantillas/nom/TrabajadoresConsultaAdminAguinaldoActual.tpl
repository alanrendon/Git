<form name="DatosAguinaldo" class="FormValidator FormStyles" id="DatosAguinaldo">
	<input name="id" type="hidden" id="id" value="{id}">
	<input name="i" type="hidden" id="i" value="{i}">
	<input name="num_cia" type="hidden" id="num_cia" value="{num_cia}">
	<input name="anio" type="hidden" id="anio" value="{anio}" />
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
			<th align="left" scope="row">Aguinaldo {anio_act}</th>
			<td nowrap><input name="importe" type="text" class="valid Focus numberPosFormat right" precision="2" id="importe" value="{importe}" size="10" /></td>
		</tr>
</table>
	<p>
		<input type="button" name="cancelar" id="cancelar" value="Cancelar" />
	&nbsp;&nbsp;
	<input type="button" name="actualizar" id="actualizar" value="Actualizar" />
	</p>
</form>