<form name="inicio" class="FormValidator" id="inicio">
	<table class="table">
		<thead>
			<tr>
				<th colspan="2" scope="col">&nbsp;</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td class="bold">Compañía(s)</td>
				<td><input name="cias" type="text" class="validate toInterval" id="cias" size="40" /></td>
			</tr>
			<tr>
				<td class="bold">Banco</td>
				<td><select name="banco" id="banco" class="logo_banco">
						<option value="" class="logo_banco"></option>
						<option value="1" class="logo_banco logo_banco_1">BANORTE</option>
						<option value="2" class="logo_banco logo_banco_2">SANTANDER</option>
					</select></td>
			</tr>
			<tr>
				<td class="bold">Periodo</td>
				<td><input name="fecha1" type="text" class="validate focus toDate center" id="fecha1" value="{fecha1}" size="10" maxlength="10" />
					al
					<input name="fecha2" type="text" class="validate focus toDate center" id="fecha2" value="{fecha2}" size="10" maxlength="10" /></td>
			</tr>
			<tr>
				<td class="bold">Cobrado</td>
				<td><input name="cobrado1" type="text" class="validate focus toDate center" id="cobrado1" size="10" maxlength="10" />
					al
					<input name="cobrado2" type="text" class="validate focus toDate center" id="cobrado2" size="10" maxlength="10" /></td>
			</tr>
			<tr>
				<td class="bold">Opciones</td>
				<td><input name="cheques" type="checkbox" id="cheques" value="1" checked="checked" />
					<span class="bold blue">Cheques</span><br />
					<input name="transferencias" type="checkbox" id="transferencias" value="1" checked="checked" />
					<span class="bold green">Transferencias</span><br />
					<input name="otros" type="checkbox" id="otros" value="1" checked="checked" />
					<span class="bold orange">Otros</span><br />
					<br />
					<input name="pendientes" type="checkbox" id="pendientes" value="1" checked="checked" />
					<span class="bold blue">Pendientes</span><br />
					<input name="cobrados" type="checkbox" id="cobrados" value="1" checked="checked" />
					<span class="bold orange">Cobrados</span><br />
					<input name="cancelados" type="checkbox" id="cancelados" value="1" checked="checked" />
					<span class="bold red">Cancelados</span></td>
			</tr>
			<tr>
				<td class="bold">Proveedor(es)</td>
				<td><input name="pros" type="text" class="validate toInterval" id="pros" size="40" /></td>
			</tr>
			<tr>
				<td class="bold">Folio(s)</td>
				<td><input name="folios" type="text" class="validate toInterval" id="folios" size="40" /></td>
			</tr>
			<tr>
				<td class="bold">Gasto(s)</td>
				<td><input name="gastos" type="text" class="validate toInterval" id="gastos" size="40" /></td>
			</tr>
			<tr>
				<td class="bold">Omitir gasto(s)</td>
				<td><input name="omitir_gastos" type="text" class="validate toInterval" id="omitir_gastos" size="40" /></td>
			</tr>
			<tr>
				<td class="bold">Importe(s) o rango(s)</td>
				<td><input name="importes" type="text" class="validate toIntervalFloats" id="importes" size="40" /></td>
			</tr>
			<tr>
				<td class="bold">Concepto</td>
				<td><input name="concepto" type="text" class="validate toText cleanText toUpper" id="concepto" size="40" maxlength="1000" /></td>
			</tr>
		</tbody>
		<tfoot>
			<tr>
				<td colspan="2">&nbsp;</td>
			</tr>
		</tfoot>
	</table>
	<p>
		<input type="button" name="consultar" id="consultar" value="Consultar" />
	</p>
</form>
