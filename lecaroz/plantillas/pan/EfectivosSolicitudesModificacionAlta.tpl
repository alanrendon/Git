<form action="" method="post" name="alta_solicitud" class="FormValidator" id="alta_solicitud">
	<table class="table">
		<thead>
			<tr>
				<th colspan="2">&nbsp;</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td class="bold">Compañía</td>
				<td><input name="num_cia" type="text" class="validate focus toPosInt right" id="num_cia" size="3" />
					<input name="nombre_cia" type="text" disabled="disabled" id="nombre_cia" size="40" /></td>
			</tr>
			<tr>
				<td class="bold">Fecha</td>
				<td><input name="fecha" type="text" class="validate focus toDate center" id="fecha" size="10" maxlength="10" /></td>
			</tr>
			<tr>
				<td class="bold">Campos</td>
				<td><input name="caja_am" type="checkbox" id="caja_am" value="1" />
					Caja AM<br />
					<input name="error_am" type="checkbox" id="error_am" value="1" />
					Error AM<br />
					<input name="caja_pm" type="checkbox" id="caja_pm" value="1" />
					Caja PM<br />
					<input name="error_pm" type="checkbox" id="error_pm" value="1" />
					Error PM<br />
					<input name="pastel" type="checkbox" id="pastel" value="1" />
					Pastel<br />
					<input name="pastillaje" type="checkbox" id="pastillaje" value="1" />
					Pastillaje<br />
					<input name="otros" type="checkbox" id="otros" value="1" />
					Otros<br />
					<input name="clientes" type="checkbox" id="clientes" value="1" />
					Clientes<br />
					<input name="corte_pan" type="checkbox" id="corte_pan" value="1" />
					Corte pan<br />
					<input name="corte_pastel" type="checkbox" id="corte_pastel" value="1" />
					Corte pastel<br />
					<input type="checkbox" name="desc_pastel" id="desc_pastel" />
					Descuento de pastel</td>
			</tr>
			<tr>
				<td class="bold">Observaciones</td>
				<td><textarea name="observaciones" cols="45" rows="5" class="validate toText cleanText toUpper" id="observaciones"></textarea></td>
			</tr>
		</tbody>
		<tfoot>
			<tr>
				<td colspan="2">&nbsp;</td>
			</tr>
		</tfoot>
	</table>
	<p>
		<input type="button" name="cancelar" id="cancelar" value="Cancelar" />
		&nbsp;&nbsp;
		<input type="button" name="alta" id="alta" value="Alta de solicitud" />
	</p>
</form>
