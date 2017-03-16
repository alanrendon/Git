<form name="inicio_form" class="FormValidator" id="inicio_form">
	<table class="table">
		<thead>
			<tr>
				<th colspan="2" scope="col">&nbsp;</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td class="bold">Gasto</td>
				<td>
					<input name="gasto" type="text" class="validate focus toPosInt right" id="gasto" size="3">
					<input name="nombre_gasto" type="text" id="nombre_gasto" size="30" disabled="">
				</td>
			</tr>
			<tr>
				<td class="bold">Compa&ntilde;&iacute;a(s)</td>
				<td><input name="cias" type="text" class="validate toInterval" id="cias" size="40"></td>
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
				<td class="bold">A&ntilde;o(s)</td>
				<td>
					<input name="anio[]" type="text" class="validate focus toPosInt center" id="anio" size="4" value="{anio}">
					<input name="anio[]" type="text" class="validate focus toPosInt center" id="anio" size="4">
					<input name="anio[]" type="text" class="validate focus toPosInt center" id="anio" size="4">
					<input name="anio[]" type="text" class="validate focus toPosInt center" id="anio" size="4">
					<input name="anio[]" type="text" class="validate focus toPosInt center" id="anio" size="4">
				</td>
			</tr>
			<tr>
				<td class="bold">Mes</td>
				<td>
					<select name="mes" id="mes">
						<!-- START BLOCK : mes -->
						<option value="{value}"{selected}>{text}</option>
						<!-- END BLOCK : mes -->
					</select>
				</td>
			</tr>
			<tr>
				<td class="bold">Opciones</td>
				<td>
					<input name="filtro" type="radio" id="filtro_0" value="" checked=""> Todos
					<br><input name="filtro" type="radio" id="filtro_1" value="NULL"> Solo gastos
					<br><input name="filtro" type="radio" id="filtro_2" value="NOT NULL"> Solo pagos
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
		<button type="button" id="consultar">Consultar</button>
	</p>
</form>
