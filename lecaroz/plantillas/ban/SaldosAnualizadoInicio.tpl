<form name="inicio_form" class="FormValidator" id="inicio_form">
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
				<td class="bold">Admnistrador</td>
				<td>
					<select name="admin" id="admin">
						<option value=""></option>
						<!-- START BLOCK : admin -->
						<option value="{value}">{text}</option>
						<!-- END BLOCK : admin -->
					</select>
				</td>
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
			</tr>
			<tr>
				<td class="bold">A&ntilde;o</td>
				<td><input name="anio" type="text" class="validate focus toPosInt center" id="anio" size="4" value="{anio}" /></td>
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
