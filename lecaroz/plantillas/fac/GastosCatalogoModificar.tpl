<form name="modificar_gasto" class="FormValidator" id="modificar_gasto">
	<input name="gasto" type="hidden" id="gasto" value="{gasto}">
	<table class="table">
		<thead>
			<tr>
				<th colspan="2" scope="col">&nbsp;</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td class="bold">Descripcion</td>
				<td><input name="descripcion" type="text" class="validate toText cleanText toUpper" id="descripcion" value="{descripcion}" size="40" maxlength="50" /></td>
			</tr>
			<tr>
				<td class="bold">Balance</td>
				<td><select name="balance" id="balance">
						<option value="0"{balance_0}>NO INCLUIDO</option>
						<option value="1"{balance_1}>OPERACION</option>
						<option value="2"{balance_2}>GENERAL</option>
					</select></td>
			</tr>
			<tr>
				<td class="bold">Tipo</td>
				<td><input name="tipo" type="radio" value="FALSE"{tipo_f} />
					Variable
					<input type="radio" name="tipo" value="TRUE"{tipo_t} />
					Fijo</td>
			</tr>
			<tr>
				<td class="bold">Aplicación</td>
				<td><input name="aplicacion" type="radio" value="FALSE"{aplicacion_f} />
					Panadería
					<input type="radio" name="aplicacion" value="TRUE"{aplicacion_t} />
					Reparto</td>
			</tr>
			<tr>
				<td class="bold">Orden</td>
				<td><input name="orden" type="text" class="validate focus toPosInt center" id="orden" value="{orden}" size="2" /></td>
			</tr>
			<tr>
				<td class="bold">Pan comprado</td>
				<td><input name="pan_comprado" type="checkbox" id="pan_comprado" value="1"{pan_comprado} />
					Si</td>
			</tr>
			<tr>
				<td class="bold">% Descuento</td>
				<td><input name="descuento" type="text" class="validate focus numberPosFormat center" precision="2" id="descuento" value="{descuento}" size="5" maxlength="5" /></td>
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
