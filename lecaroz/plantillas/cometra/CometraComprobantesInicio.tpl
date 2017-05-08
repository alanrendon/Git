<form name="inicio_form" class="FormValidator" id="inicio_form">
	<table class="table">
		<thead>
			<tr>
				<th colspan="2" scope="col">&nbsp;</th>
			</tr>
		</thead>
		<tbody>
			<!-- <tr>
				<td class="bold">Compañía(s)</td>
				<td><input name="cias" type="text" class="validate toInterval" id="cias" size="40" /></td>
			</tr>
			<tr>
				<td class="bold">Banco</td>
				<td>
					<select name="banco" id="banco" class="logo_banco">
						<option value="" class="logo_banco"></option>
						<option value="1" class="logo_banco logo_banco_1">BANORTE</option>
						<option value="2" class="logo_banco logo_banco_2">SANTANDER</option>
					</select>
				</td>
			</tr> -->
			<tr>
				<td class="bold">Periodo</td>
				<td>
					<input name="fecha1" type="text" class="validate focus toDate center" id="fecha1" size="10" value="{fecha1}" />
					al
					<input name="fecha2" type="text" class="validate focus toDate center" id="fecha2" size="10" value="{fecha2}" />
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
		<input type="button" name="consultar" id="consultar" value="Consultar" />
	</p>
</form>
