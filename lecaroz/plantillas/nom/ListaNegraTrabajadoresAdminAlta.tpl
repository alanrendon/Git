<form name="alta_registro" class="FormValidator" id="alta_registro">
	<table class="table">
		<thead>
			<tr>
				<th colspan="2" scope="col">&nbsp;</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td class="bold">Nombre</td>
				<td><input name="nombre" type="text" class="validate toText cleanText toUpper" id="nombre" size="40" maxlength="50" /></td>
			</tr>
			<tr>
				<td class="bold">Ap. Paterno</td>
				<td><input name="ap_paterno" type="text" class="validate toText cleanText toUpper" id="ap_paterno" size="40" /></td>
			</tr>
			<tr>
				<td class="bold">Ap. Materno</td>
				<td><input name="ap_materno" type="text" class="validate toText cleanText toUpper" id="ap_materno" size="40" /></td>
			</tr>
			<tr>
				<td class="bold">Tipo</td>
				<td><select name="tipo" id="tipo">
						<!-- START BLOCK : tipo -->
						<option value="{value}">{text}</option>
						<!-- END BLOCK : tipo -->
					</select></td>
			</tr>
			<tr>
				<td class="bold">Observaciones</td>
				<td><textarea name="observaciones" class="validate toText cleanText toUpper" id="observaciones" cols="45" rows="5"></textarea></td>
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
		<input type="button" name="alta" id="alta" value="Alta" />
	</p>
</form>
