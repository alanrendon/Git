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
				<td class="bold">Periodo</td>
				<td><input name="fecha1" type="text" class="validate focus toDate center" id="fecha1" value="{fecha1}" size="10" maxlength="10" />
					al
					<input name="fecha2" type="text" class="validate focus toDate center" id="fecha2" value="{fecha2}" size="10" maxlength="10" /></td>
			</tr>
			<tr>
				<td class="bold">Folio(s)</td>
				<td><input name="folios" type="text" class="validate toInterval" id="folios" size="40" /></td>
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
