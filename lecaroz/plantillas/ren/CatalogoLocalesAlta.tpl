<form action="" method="get" name="Datos" class="FormValidator FormStyles" id="Datos">
	<table class="tabla_captura">
		<tr class="linea_off">
			<th align="left" scope="row">Inmobiliaria</th>
			<td><input type="hidden" name="idarrendador" id="idarrendador" />
				<input name="arrendador" type="text" class="valid Focus toPosInt center" id="arrendador" size="3" />
				<input name="nombre_arrendador" type="text" disabled="disabled" id="nombre_arrendador" size="60" /></td>
		</tr>
		<tr class="linea_on">
			<th align="left" scope="row">Alias</th>
			<td><input name="alias_local" type="text" class="valid toText cleanText toUpper" id="alias_local" size="65" maxlength="100" /></td>
		</tr>
		<tr class="linea_off">
			<th align="left" scope="row">Categor&iacute;a</th>
			<td align="left" id="categoria" scope="row"><select name="categoria" id="categoria">
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
				</select></td>
		</tr>
		<tr>
			<th colspan="2" align="left" scope="row"><img src="/lecaroz/iconos/info.png" width="16" height="16" /> Informaci&oacute;n del local</th>
		</tr>
		<tr class="linea_on">
			<th align="left" scope="row">Tipo</th>
			<td align="left" id="tipo_local" scope="row"><select name="tipo_local" id="tipo_local">
					<option value="1">COMERCIAL</option>
					<option value="2">VIVIENDA</option>
				</select></td>
		</tr>
		<tr class="linea_off">
			<th align="left" scope="row">Domicilio</th>
			<td align="left" id="domicilio_local" scope="row"><textarea name="domicilio" class="valid toText cleanText toUpper" id="domicilio" cols="65" rows="5"></textarea></td>
		</tr>
		<tr class="linea_on">
			<th align="left" scope="row">Superficie (m&#178;)</th>
			<td align="left" id="superficie_local" scope="row"><input name="superficie" type="text" class="valid Focus numberPosFormat right" precision="2" id="superficie" size="8"></td>
		</tr>
		<tr class="linea_off">
			<th align="left" scope="row">Cuenta de predial</th>
			<td align="left" id="superficie_local2" scope="row"><input name="cuenta_predial" type="text" class="valid Focus onlyNumbersAndLetters" id="cuenta_predial" size="15" maxlength="30" /></td>
		</tr>
	</table>
	<p>
		<input type="button" name="regresar" id="regresar" value="Regresar" />
		&nbsp;&nbsp;
		<input type="button" name="alta" id="alta" value="Alta" />
	</p>
</form>
