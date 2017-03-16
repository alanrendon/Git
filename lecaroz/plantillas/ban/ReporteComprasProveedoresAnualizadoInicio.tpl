<form name="inicio_form" class="FormValidator" id="inicio_form">
	<table class="table">
		<thead>
			<tr>
				<th colspan="2" scope="col">&nbsp;</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td class="bold">Proveedor(es)</td>
				<td><input name="pros" type="text" class="validate toInterval" id="pros" size="40" /></td>
			</tr>
			<tr>
				<td class="bold">Compa&ntilde;&iacute;a(s)</td>
				<td><input name="cias" type="text" class="validate toInterval" id="cias" size="40" /></td>
			</tr>
			<tr>
				<td class="bold">Administrador</td>
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
				<td class="bold">A&ntilde;o</td>
				<td><input name="anio" type="text" class="validate focus toPosInt center" id="anio" size="4" value="{anio}" /></td>
			</tr>
			<tr>
				<td class="bold">Filtros</td>
				<td>
					<input name="filtro" type="radio" id="filtro0" value="" checked="" />
					Todos<br />
					<input name="filtro" type="radio" id="filtro1" value="f.codgastos IN (33, 90)" />
					Matería prima<br />
					<input name="filtro" type="radio" id="filtro2" value="f.codgastos NOT IN (33, 90)" />
					Varios
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
