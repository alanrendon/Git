<form name="Datos" class="FormStyles FormValidator" id="Datos">
	<table class="tabla_captura">
		<tr class="linea_off">
			<th align="left" scope="row">A&ntilde;o</th>
			<td><input name="anio" type="text" id="anio" class="valid Focus toPosInt center" value="{anio}" size="4" maxlength="4" /></td>
		</tr>
		<tr class="linea_on">
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
		<tr class="linea_off">
			<th align="left" scope="row">Inmobiliaria(s)</th>
			<td><input name="arrendadores" type="text" class="valid toInterval" id="arrendadores" size="40" /></td>
		</tr>
		<tr class="linea_on">
			<th align="left" scope="row">Bloque(s)</th>
			<td><input name="internos" type="checkbox" id="internos" value="1" checked="checked" />
				Internos<br />
				<input name="externos" type="checkbox" id="externos" value="1" checked="checked" />
				Externos</td>
		</tr>
		<tr class="linea_off">
			<th align="left" scope="row">Categor&iacute;a</th>
			<td>
				<select name="categoria" id="categoria">
					<option value=""></option>
					<option value="1">CATEGORIA 1</option>
					<option value="2">CATEGORIA 2</option>
					<option value="3">CATEGORIA 3</option>
					<option value="4">CATEGORIA 4</option>
					<option value="5">CATEGORIA 5</option>
					<option value="6">CATEGORIA 6</option>
					<option value="7">CATEGORIA 7</option>
					<option value="8">CATEGORIA 8</option>
					<option value="9">CATEGORIA 9</option>
					<option value="10">CATEGORIA 10</option>
				</select>
			</td>
		</tr>
	</table>
	<p>
		<input name="consultar" type="button" id="consultar" value="Consultar" />
	</p>
</form>
