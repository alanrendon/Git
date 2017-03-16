<form name="alta_producto" class="FormValidator" id="alta_producto">
	<table class="table">
		<thead>
			<tr>
				<th colspan="2" scope="col">&nbsp;</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td class="bold">Descripci&oacute;n</td>
				<td><input name="descripcion" type="text" class="validate toText cleanText toUpper" id="descripcion" size="40" maxlength="50" /></td>
			</tr>
			<tr>
				<td class="bold">Tipo</td>
				<td>
					<select name="tipo_pan" id="tipo_pan">
						<!-- START BLOCK : tipo_pan -->
						<option value="{value}">{text}</option>
						<!-- END BLOCK : tipo_pan -->
					</select>
				</td>
			</tr>
			<tr>
				<td class="bold">Control de producci&oacute;n</td>
				<td>
					<input type="radio" value="TRUE" name="control_produccion" id="control_produccion_true" checked="checked" /> Si <input type="radio" value="FALSE" name="control_produccion" id="control_produccion_false" /> No
				</td>
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
