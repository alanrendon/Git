<form name="inicio_form" class="FormValidator" id="inicio_form">
	<table class="table">
		<thead>
			<tr>
				<th colspan="2" scope="row">&nbsp;</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td align="left" scope="row">Compa&ntilde;&iacute;a</td>
				<td>
					<input name="num_cia" type="text" class="validate focus toPosInt right" id="num_cia" size="3" value="" />
					<input name="nombre_cia" type="text" id="nombre_cia" size="40" value="" disabled="disabled" />
				</td>
			</tr>
			<tr>
				<td>Periodo</td>
				<td>
					<select name="periodo" id="periodo">
					</select>
				</td>
			</tr>
			<tr>
				<td>Archivo</td>
				<td>
					<input name="archivo_carga[]" type="file" id="archivo_carga" value="" />
				</td>
			</tr>
			<tr>
				<td>Columna extra</td>
				<td>
					<input name="nombre_extra" type="text" class="validate cleanText toUpper" id="nombre_extra" size="40" value="" />
				</td>
			</tr>
			<tr>
				<td>Leyenda para columna extra</td>
				<td>
					<input name="leyenda_extra" type="text" class="validate cleanText toUpper" id="leyenda_extra" size="40" value="" />
				</td>
			</tr>
		</tbody>
		<tfoot>
			<tr>
				<td colspan="2" scope="row">&nbsp;</td>
			</tr>
		</tfoot>
	</table>
	<p>
		<button type="button" id="cargar_datos">Cargar datos</button>
	</p>
</form>
