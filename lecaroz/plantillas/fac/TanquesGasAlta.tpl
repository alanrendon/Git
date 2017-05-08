<form name="alta_tanque" class="FormValidator" id="alta_tanque">
	<table class="table">
		<thead>
			<tr>
				<th colspan="2" scope="col">&nbsp;</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td class="bold">Compa&ntilde;&iacute;a</td>
				<td>
					<input name="num_cia" type="text" class="validate focus toPosInt right" id="num_cia" size="3" />
					<input name="nombre_cia" type="text" id="nombre_cia" size="30" disabled="disabled" />
				</td>
			</tr>
			<tr>
				<td class="bold">N&uacute;mero</td>
				<td>
					<input name="num_tanque" type="text" class="validate focus toPosInt right" id="num_tanque" size="3" />
				</td>
			</tr>
			<tr>
				<td class="bold">Nombre</td>
				<td><input name="nombre" type="text" class="validate toText cleanText toUpper" id="nombre" size="40" maxlength="200" /></td>
			</tr>
			<tr>
				<td class="bold">Capacidad</td>
				<td><input name="capacidad" type="text" class="validate focus numberFormat right" precision="2" id="capacidad" value="" size="10" /></td>
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
