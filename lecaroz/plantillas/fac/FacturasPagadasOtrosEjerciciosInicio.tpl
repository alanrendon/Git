<form name="inicio" class="FormValidator" id="inicio">
	<table class="table">
		<thead>
			<tr>
				<th colspan="2" scope="col">&nbsp;</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td class="bold">Compa&ntilde;&iacute;a(s)</td>
				<td><input name="cias" type="text" class="validate toInterval" id="cias" size="40" /></td>
			</tr>
			<tr>
				<td class="bold">R.F.C.</td>
				<td>
					<select name="rfc" id="rfc">
						<option value=""></option>
						<!-- START BLOCK : rfc -->
						<option value="{value}">{text}</option>
						<!-- END BLOCK : rfc -->
					</select>
				</td>
			</tr>
			<tr>
				<td class="bold">Administrador</td>
				<td>
					<select name="admin" id="admin">
						<option value="" class="logo_banco"></option>
						<!-- START BLOCK : admin -->
						<option value="{value}">{text}</option>
						<!-- END BLOCK : admin -->
					</select>
				</td>
			</tr>
			<tr>
				<td class="bold">Proveedor(es)</td>
				<td><input name="pros" type="text" class="validate toInterval" id="pros" size="40" /></td>
			</tr>
			<tr>
				<td class="bold">Gasto(s)</td>
				<td><input name="gastos" type="text" class="validate toInterval" id="gastos" size="40" /></td>
			</tr>
			<tr>
				<td class="bold">A&ntilde;o</td>
				<td><input name="anio" type="text" class="validate focus toPosInt center" id="anio" value="{anio}" size="4" maxlength="4" /></td>
			</tr>
			<tr>
				<td class="bold">Opciones</td>
				<td>
					<input name="ordenar_por_rfc" type="checkbox" id="ordenar_por_rfc" value="1" checked="checked" />
					Ordenar por R.F.C.
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
