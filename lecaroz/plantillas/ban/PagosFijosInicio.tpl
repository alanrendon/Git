<form name="inicio" class="FormValidator" id="inicio">
	<table class="table">
		<thead>
			<tr>
				<th colspan="2" scope="row">&nbsp;</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td align="left" scope="row">Compa&ntilde;&iacute;a(s)</td>
				<td>
					<input name="cias" type="text" class="validate toInterval" id="cias" size="40" />
				</td>
			</tr>
			<tr>
				<td align="left" scope="row">Proveedor(es)</td>
				<td>
					<input name="pros" type="text" class="validate toInterval" id="pros" size="40" />
				</td>
			</tr>
			<tr>
				<td align="left" scope="row">Gasto(s)</td>
				<td>
					<input name="gastos" type="text" class="validate toInterval" id="gastos" size="40" />
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
		<input type="button" name="consultar" id="consultar" value="Consultar" />
	</p>
</form>
