<form name="inicio_form" class="FormValidator" id="inicio_form">
	<table class="table">
		<thead>
			<tr>
				<th colspan="2" scope="row">&nbsp;</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td class="bold" align="left" scope="row">Gasto</td>
				<td>
					<input name="gasto" type="text" class="validate focus toPosInt right" id="gasto" size="3" />
					<input name="nombre_gasto" type="text" class="validate focus toPosInt right" id="nombre_gasto" size="30" />
				</td>
			</tr>
			<tr>
				<td class="bold" align="left" scope="row">A&ntilde;o</td>
				<td><input name="anio" type="text" class="validate focus toPosInt center" id="anio" size="4" value="{anio}" /></td>
			</tr>
			<tr>
				<td class="bold" align="left" scope="row">Compa&ntilde;&iacute;a(s)</td>
				<td><input name="cias" type="text" class="validate toInterval" id="cias" size="40" /></td>
			</tr>
		</tbody>
		<tfoot>
			<tr>
				<td colspan="2" scope="row">&nbsp;</td>
			</tr>
		</tfoot>
	</table>
	<p>
		<input type="button" name="consultar" id="consultar" value="Consultar" />
	</p>
</form>
