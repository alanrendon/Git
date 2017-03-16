<form name="Datos" class="FormValidator FormStyles" id="Datos">
	<table class="tabla_captura">
		<tr class="linea_off">
			<th align="left" scope="row">Compañía(s)</th>
			<td><input name="cias" type="text" class="valid toInterval" id="cias" size="40" /></td>
		</tr>
		<tr class="linea_on">
			<th align="left" scope="row">Periodo</th>
			<td><input name="fecha1" type="text" class="valid Focus toDate center" id="fecha1" value="{fecha1}" size="10" maxlength="10" />
				y
				<input name="fecha2" type="text" class="valid Focus toDate center" id="fecha2" value="{fecha2}" size="10" maxlength="10" /></td>
		</tr>
		<tr class="linea_off">
			<th align="left" scope="row">Periodo de pago</th>
			<td><input name="fecha_pago1" type="text" class="valid Focus toDate center" id="fecha_pago1" value="" size="10" maxlength="10" />
				y
				<input name="fecha_pago2" type="text" class="valid Focus toDate center" id="fecha_pago2" value="" size="10" maxlength="10" /></td>
		</tr>
		<tr class="linea_on">
			<th align="left" scope="row">Folio(s)</th>
			<td><input name="folios" type="text" class="valid toIntervalChars toUpper" id="folios" size="40" /></td>
		</tr>
		<tr class="linea_off">
			<th align="left" scope="row">Estado</th>
			<td><input name="pendientes" type="checkbox" id="pendientes" value="1" checked="checked" />
				Pendientes<br />
				<input name="pagadas" type="checkbox" id="pagadas" value="1" checked="checked" />
				Pagadas</td>
		</tr>
	</table>
	<p>
		<input type="button" name="consultar" id="consultar" value="Consultar" />
	</p>
</form>
