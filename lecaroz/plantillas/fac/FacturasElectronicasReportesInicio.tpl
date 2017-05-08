<form action="" method="post" name="Datos" class="FormValidator FormStyles" id="Datos">
	<table class="tabla_captura">
		<tr class="linea_off">
			<th align="left" scope="row">Compa&ntilde;&iacute;a(s)</th>
			<td><input name="cias" type="text" class="valid toInterval" id="cias" size="30" /></td>
		</tr>
		<tr class="linea_on">
			<th align="left" scope="row">Contador</th>
			<td><select name="contador" id="contador">
				<option value=""></option>
				<!-- START BLOCK : contador -->
				<option value="{value}">{text}</option>
				<!-- END BLOCK : contador -->
			</select></td>
		</tr>
		<tr class="linea_off">
			<th align="left" scope="row">Auditor</th>
			<td><select name="auditor" id="auditor">
				<option value=""></option>
				<!-- START BLOCK : auditor -->
				<option value="{value}">{text}</option>
				<!-- END BLOCK : auditor -->
			</select></td>
		</tr>
		<tr class="linea_on">
			<th align="left" scope="row">A&ntilde;o</th>
			<td><input name="anio" type="text" class="valid Focus toPosInt center" id="anio" value="{anio}" size="4" maxlength="4" /></td>
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
	</table>
	<p>
		<input type="button" name="generar" id="generar" value="Generar reportes" />
	</p>
</form>