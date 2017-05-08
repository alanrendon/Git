<form name="Datos" class="FormValidator" id="inicio_form">
	<table class="table">
		<thead>
			<tr>
				<th colspan="2" align="left" class="bold" scope="row">&nbsp;</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td align="left" class="bold" scope="row">Compa&ntilde;&iacute;a(s)</td>
				<td><input name="cias" type="text" class="validate toInterval" id="cias" size="40" /></td>
			</tr>
			<tr>
				<td align="left" class="bold" scope="row">Administrador</td>
				<td><select name="admin" id="admin">
						<option value=""></option>
						<!-- START BLOCK : admin -->
						<option value="{value}">{text}</option>
						<!-- END BLOCK : admin -->
					</select></td>
			</tr>
			<tr>
				<td align="left" class="bold" scope="row">Gasto(s) generales</td>
				<td><input name="gastos" type="text" class="validate toInterval" id="gastos" size="40" />
			</tr>
			<tr>
				<td align="left" class="bold" scope="row">Gasto(s) de caja</td>
				<td><input name="gastos_caja" type="text" class="validate toInterval" id="gastos_caja" size="40" />
			</tr>
			<tr>
				<td align="left" class="bold" scope="row">A&ntilde;o</td>
				<td><input name="anio" type="text" class="validate focus toPosInt center" id="anio" value="{anio}" size="4" maxlength="4" /></td>
			</tr>
		</tbody>
		<tfoot>
			<tr>
				<td colspan="2" align="left" class="bold" scope="row">&nbsp;</td>
			</tr>
		</tfoot>
	</table>
	<p>
		<!--<input type="button" name="exportar" id="exportar" value="Exportar" />
		&nbsp;&nbsp;-->
		<button type="button" id="consultar">Consultar</button>
	</p>
</form>
