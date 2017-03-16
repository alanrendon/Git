<form action="" method="post" name="Datos" class="FormValidator FormStyles" id="Datos">
	<table class="tabla_captura">
		<tr class="linea_off">
			<th align="left" scope="row">Compa&ntilde;&iacute;a(s)</th>
			<td><input name="cias" type="text" class="valid toInterval" id="cias" size="40" /></td>
			</tr>
		<tr>
			<th>Administrador</th>
			<td>
				<select>
					<option value=""></option>
					<!-- START BLOCK : admin -->
					<option value="{value}">{text}</option>
					<!-- END BLOCK : admin -->
				</select>
			</td>
		</tr>
		<tr class="linea_on">
			<th align="left" scope="row">Periodo</th>
			<td><input name="fecha1" type="text" class="valid Focus toDate center" id="fecha1" size="10" maxlength="10" value="{fecha1}" />
				al
					<input name="fecha2" type="text" class="valid Focus toDate center" id="fecha2" size="10" maxlength="10" value="{fecha2}" /></td>
		</tr>
		</table>
	<p>
		<input type="button" name="consultar" id="consultar" value="Consultar" />
	</p>
</form>