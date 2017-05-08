<form name="Datos" class="FormValidator FormStyles" id="Datos">
	<table class="tabla_captura">
		<tr class="linea_off">
			<th align="left" scope="row">Compañía(s)</th>
			<td><input name="cias" type="text" class="valid toInterval" id="cias" size="30" /></td>
		</tr>
		<tr class="linea_on">
			<th align="left" scope="row">Trabajador(es)</th>
			<td><input name="trabajadores" type="text" class="valid toInterval" id="trabajadores" size="30" /></td>
		</tr>
		<t class="linea_off"r>
			<th align="left" scope="row">Nombre(s)</th>
			<td><input name="nombre" type="text" class="valid onlyText cleanText" id="nombre" size="30" /></td>
		</tr>
		<tr class="linea_on">
			<th align="left" scope="row">Ap. paterno</th>
			<td><input name="ap_paterno" type="text" class="valid onlyText cleanText" id="ap_paterno" size="30" /></td>
		</tr>
		<tr class="linea_off">
			<th align="left" scope="row">Ap. materno</th>
			<td><input name="ap_materno" type="text" class="valid" id="ap_materno" size="30" /></td>
		</tr>
		<tr class="linea_on">
			<th align="left" scope="row">R.F.C.</th>
			<td><input name="rfc" type="text" class="valid toRFC" id="rfc" size="13" maxlength="13" /></td>
		</tr>
	</table>
	<p>
		<input type="button" name="buscar" id="buscar" value="Buscar empleados" />
	</p>
</form>