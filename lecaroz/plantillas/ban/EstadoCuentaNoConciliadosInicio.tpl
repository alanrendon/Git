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
				<td class="bold">Omitir</td>
				<td><input name="omitir_cias" type="text" class="validate toInterval" id="omitir_cias" size="40" /></td>
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
				<td class="bold">Opciones</td>
				<td><input name="depositos" type="checkbox" id="depositos" value="1" checked="checked" />
					<span class="bold blue">Depositos
					<input name="codigos_depositos" type="text" class="validate toInterval" id="codigos_depositos" value="1,2,13,16,29,99" size="30" />
					</span><br />
					<input name="cargos" type="checkbox" id="cargos" value="1" checked="checked" />
					<span class="bold red">Cargos</span>
					<input name="codigos_cargos" type="text" class="validate toInterval" id="codigos_cargos" size="30" /></td>
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
