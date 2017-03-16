<form name="modificar_producto" class="FormValidator" id="modificar_producto">
	<input name="producto" type="hidden" id="producto" value="{producto}">
	<table class="table">
		<thead>
			<tr>
				<th colspan="2" scope="col">&nbsp;</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td class="bold">Código</td>
				<td>{producto}</td>
			</tr>
			<tr>
				<td class="bold">Descripcion</td>
				<td><input name="descripcion" type="text" class="validate toText cleanText toUpper" id="descripcion" value="{descripcion}" size="40" maxlength="50" /></td>
			</tr>
			<tr>
				<td class="bold">Tipo</td>
				<td>
					<select name="tipo_pan" id="tipo_pan">
						<!-- START BLOCK : tipo_pan -->
						<option value="{value}"{selected}>{text}</option>
						<!-- END BLOCK : tipo_pan -->
					</select>
				</td>
			</tr>
			<tr>
				<td class="bold">Control de producci&oacute;n</td>
				<td>
					<input type="radio" value="TRUE" name="control_produccion" id="control_produccion_true"{control_produccion_t} /> Si <input type="radio" value="FALSE" name="control_produccion" id="control_produccion_false"{control_produccion_f} /> No
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
		<input type="button" name="modificar" id="modificar" value="Modificar" />
	</p>
</form>
