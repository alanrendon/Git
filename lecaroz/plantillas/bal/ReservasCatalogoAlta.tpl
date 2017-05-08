<form action="" method="post" name="alta_reserva" class="FormValidator" id="alta_reserva">
	<table class="table">
		<tr>
			<th align="left" scope="row">Descripción</th>
			<td><input name="descripcion_reserva" type="text" class="validate toText cleanText toUpper" id="descripcion_reserva" size="30" maxlength="30" /></td>
		</tr>
		<tr>
			<th align="left" scope="row">Gasto</th>
			<td><input name="gasto" type="text" class="validate focus toPosInt center" id="gasto" size="3" />
				<input name="descripcion_gasto" type="text" disabled="disabled" id="descripcion_gasto" size="30" /></td>
		</tr>
		<tr>
			<th align="left" scope="row">Aplicar promedio</th>
			<td><input name="aplicar_promedio" type="checkbox" id="aplicar_promedio" value="1" />
				Sí</td>
		</tr>
		<tr>
			<th align="left" scope="row">Distribuir diferencia</th>
			<td><input name="distribuir_diferencia" type="checkbox" id="distribuir_diferencia" value="1" />
				Sí</td>
		</tr>
	</table>
	<p>
		<input type="button" name="cancelar" id="cancelar" value="Cancelar" />
		&nbsp;&nbsp;
		<input type="button" name="alta" id="alta" value="Alta" />
	</p>
</form>
