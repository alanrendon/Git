<form name="alta_gasto" class="FormValidator" id="alta_gasto">
	<table class="table">
		<thead>
			<tr>
				<th colspan="2" scope="col">&nbsp;</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td class="bold">Descripcion</td>
				<td><input name="descripcion" type="text" class="validate toText cleanText toUpper" id="descripcion" size="40" maxlength="50" /></td>
			</tr>
			<tr>
				<td class="bold">Balance</td>
				<td><select name="balance" id="balance">
						<option value="0" selected="selected">NO INCLUIDO</option>
						<option value="1">OPERACION</option>
						<option value="2">GENERAL</option>
					</select></td>
			</tr>
			<tr>
				<td class="bold">Tipo</td>
				<td><input name="tipo" type="radio" value="FALSE" checked="checked" />
					Variable
					<input type="radio" name="tipo" value="TRUE" />
					Fijo</td>
			</tr>
			<tr>
				<td class="bold">Aplicación</td>
				<td><input name="aplicacion" type="radio" value="FALSE" checked="checked" />
					Panadería
					<input type="radio" name="aplicacion" value="TRUE" />
					Reparto</td>
			</tr>
			<tr>
				<td class="bold">Orden</td>
				<td><input name="orden" type="text" class="validate focus toPosInt center" id="orden" value="2" size="2" /></td>
			</tr>
			<tr>
				<td class="bold">Pan comprado</td>
				<td><input name="pan_comprado" type="checkbox" id="pan_comprado" value="1" />
					Si</td>
			</tr>
			<tr>
				<td class="bold">% Descuento</td>
				<td><input name="descuento" type="text" class="validate focus numberPosFormat center" precision="2" id="descuento" value="0.00" size="5" maxlength="5" /></td>
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
