<form name="inicio" class="FormValidator" id="inicio">
	<table class="table">
		<thead>
			<tr>
				<th colspan="2" align="left" scope="row">&nbsp;</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td align="left" scope="row">Compañía(s)</td>
				<td><input name="cias" type="text" class="validate toInterval" id="cias" size="40" /></td>
			</tr>
			<tr>
				<td align="left" scope="row">Incluir</td>
				<td><input name="pendientes" type="checkbox" id="pendientes" value="1" checked="checked" />
					Pendientes<br />
					<input name="aclarados" type="checkbox" id="aclarados" value="1" />
					Aclarados</td>
			</tr>
		</tbody>
		<tfoot>
			<tr>
				<td colspan="2" align="left" scope="row">&nbsp;</td>
			</tr>
		</tfoot>
	</table>
	<p>
		<input type="button" name="consultar" id="consultar" value="Consultar" />
	</p>
</form>
