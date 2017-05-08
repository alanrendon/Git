<form name="Datos" class="FormValidator FormStyles" id="Datos">
	<table class="tabla_captura">
		<tr class="linea_off">
			<th align="left" scope="row">Compa&ntilde;&iacute;a(s)</th>
			<td><input name="cias" type="text" class="valid toInterval" id="cias" size="30" /></td>
		</tr>
		<tr class="linea_on">
			<th align="left" scope="row">Omitir compañía(s)</th>
			<td><input name="omitir" type="text" class="valid toInterval" id="omitir" size="30" /></td>
		</tr>
		<tr class="linea_off">
			<th align="left" scope="row">Administrador</th>
			<td><select name="admin" id="admin">
					<option value=""></option>
					<!-- START BLOCK : admin -->
					<option value="{value}">{text}</option>
					<!-- END BLOCK : admin -->
				</select></td>
		</tr>
		<tr class="linea_on">
			<th align="left" scope="row">A&ntilde;o</th>
			<td><input name="anio" type="text" class="valid Focus toInt center" id="anio" value="{anio}" size="4" maxlength="4" /></td>
		</tr>
		<tr class="linea_off">
			<th align="left" scope="row">Mes</th>
			<td><select name="mes" id="mes">
					<option value="1"{1}>ENERO</option>
					<option value="2"{2}>FEBRERO</option>
					<option value="3"{3}>MARZO</option>
					<option value="4"{4}>ABRIL</option>
					<option value="5"{5}>MAYO</option>
					<option value="6"{6}>JUNIO</option>
					<option value="7"{7}>JULIO</option>
					<option value="8"{8}>AGOSTO</option>
					<option value="9"{9}>SEPTIEMBRE</option>
					<option value="10"{10}>OCTUBRE</option>
					<option value="11"{11}>NOVIEMBRE</option>
					<option value="12"{12}>DICIEMBRE</option>
				</select></td>
		</tr>
		<tr class="linea_on">
			<th align="left" scope="row">D&iacute;a</th>
			<td><input name="dia" type="text" class="valid Focus toInt center" id="dia" value="{dia}" size="2" maxlength="2" /></td>
		</tr>
	</table>
	<br />
	<p>
		<input name="consultar" type="button" class="boton" id="consultar" value="Consultar" />
	</p>
</form>
