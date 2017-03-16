<form action="" method="get" class="FormValidator" id="inicio">
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
				<td align="left" scope="row">Administrador</td>
				<td><select name="admin" id="admin">
						<option value="" selected="selected"></option>
						<!-- START BLOCK : admin -->
						<option value="{value}">{text}</option>
						<!-- END BLOCK : admin -->
					</select></td>
			</tr>
			<tr>
				<td align="left" scope="row">Contador</td>
				<td><select name="contador" id="contador">
						<option value="" selected="selected"></option>
						<!-- START BLOCK : contador -->
						<option value="{value}">{text}</option>
						<!-- END BLOCK : contador -->
					</select></td>
			</tr>
			<tr>
				<td align="left" scope="row">Periodo</td>
				<td><input name="fecha1" type="text" class="validate focus toDate center" id="fecha1" size="10" maxlength="10">
					al
					<input name="fecha2" type="text" class="validate focus toDate center" id="fecha2" size="10" maxlength="10" /></td>
			</tr>
		</tbody>
		<tfoot>
			<tr>
				<th colspan="2" align="left" scope="row">&nbsp;</th>
			</tr>
		</tfoot>
	</table>
	<p>
		<input type="button" name="consultar" id="consultar" value="Consultar" />
	</p>
</form>
