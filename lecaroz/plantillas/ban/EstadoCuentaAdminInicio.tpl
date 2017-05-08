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
				<td class="bold">Acreditado a</td>
				<td><input name="acreditados" type="text" class="validate toInterval" id="acreditados" size="40" /></td>
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
				<td class="bold">Conciliado</td>
				<td><input name="conciliado1" type="text" class="validate focus toDate center" id="conciliado1" size="10" maxlength="10" />
al
	<input name="conciliado2" type="text" class="validate focus toDate center" id="conciliado2" size="10" maxlength="10" /></td>
			</tr>
			<tr>
				<td class="bold">Comprobante(s)</td>
				<td><input name="comprobantes" type="text" class="validate toInterval" id="comprobantes" size="40" /></td>
			</tr>
			<tr>
				<td class="bold">Opciones</td>
				<td><input name="depositos" type="checkbox" id="depositos" value="1" checked="checked" />
					<span class="bold blue">Depositos</span><br />
					<input name="cargos" type="checkbox" id="cargos" value="1" checked="checked" />
					<span class="bold red">Cargos</span><br />
					<br />
					<input name="pendientes" type="checkbox" id="pendientes" value="1" checked="checked" />
					<span class="bold green">Pendientes</span><br />
					<input name="conciliados" type="checkbox" id="conciliados" value="1" checked="checked" />
					<span class="bold orange">Conciliados</span></td>
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
				<td class="bold">Importe(s) o rango(s)</td>
				<td><input name="importes" type="text" class="validate toIntervalFloats" id="importes" size="40" /></td>
			</tr>
			<tr>
				<td class="bold">Código(s)</td>
				<td><select name="codigos[]" size="10" multiple="multiple" id="codigos" style="width:100%;">
					</select></td>
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
