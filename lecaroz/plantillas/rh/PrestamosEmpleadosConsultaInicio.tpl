<form name="inicio" class="FormValidator" id="inicio">
	<table class="table">
		<thead>
			<tr>
				<th colspan="2" align="left" scope="row">&nbsp;</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td class="bold" scope="row">Compa&ntilde;&iacute;a(s)</td>
				<td><input name="cias" type="text" class="validate toInterval" id="cias" size="30" /></td>
			</tr>
			<tr>
				<td class="bold" scope="row">Administrador</td>
				<td><select name="admin" id="admin">
						<option value=""></option>
						<!-- START BLOCK : admin -->
						<option value="{value}">{text}</option>
						<!-- END BLOCK : admin -->
					</select></td>
			</tr>
			<tr>
				<td class="bold" scope="row">Empleado(s)</td>
				<td><input name="empleados" type="text" class="validate toInterval" id="empleados" size="30" /></td>
			</tr>
			<tr>
				<td class="bold" scope="row">Nombre(s)</td>
				<td><input name="nombre" type="text" class="validate toText cleanText toUpper" id="nombre" size="30" /></td>
			</tr>
			<tr>
				<td class="bold" scope="row">Ap. paterno</td>
				<td><input name="ap_paterno" type="text" class="validate toText cleanText toUpper" id="ap_paterno" size="30" /></td>
			</tr>
			<tr>
				<td class="bold" scope="row">Ap. materno</td>
				<td><input name="ap_materno" type="text" class="validate toText cleanText toUpper" id="ap_materno" size="30" /></td>
			</tr>
			<tr>
				<td class="bold" scope="row">R.F.C.</td>
				<td><input name="rfc" type="text" class="validate toRFCopcional" id="rfc" size="30" /></td>
			</tr>
		</tbody>
		<tfoot>
			<tr>
				<th colspan="2" align="left" scope="row">&nbsp;</th>
			</tr>
		</tfoot>
	</table>
	<p>
		<input name="consultar" type="button" class="boton" id="consultar" value="Consultar" />
	</p>
</form>
