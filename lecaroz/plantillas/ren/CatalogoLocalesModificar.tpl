<form action="" method="get" name="Datos" class="FormValidator FormStyles" id="Datos">
	<table class="tabla_captura">
		<tr class="linea_off">
			<th align="left" scope="row">Inmobiliaria</th>
			<td><input name="idlocal" type="hidden" id="idlocal" value="{idlocal}">
				<input name="idarrendador" type="hidden" id="idarrendador" value="{idarrendador}" />
				<input name="arrendador" type="text" class="valid Focus toPosInt center" id="arrendador" value="{arrendador}" size="3" readonly="readonly" />
				<input name="nombre_arrendador" type="text" disabled="disabled" id="nombre_arrendador" value="{nombre_arrendador}" size="60" /></td>
		</tr>
		<tr class="linea_on">
			<th align="left" scope="row">Alias</th>
			<td><input name="alias_local" type="text" class="valid toText cleanText toUpper" id="alias_local" value="{alias_local}" size="65" maxlength="100" /></td>
		</tr>
		<tr class="linea_off">
			<th align="left" scope="row">Categor&iacute;a</th>
			<td align="left" id="categoria" scope="row"><select name="categoria" id="categoria">
					<option value="1"{categoria_1}>CATEGORIA 1</option>
					<option value="2"{categoria_2}>CATEGORIA 2</option>
					<option value="3"{categoria_3}>CATEGORIA 3</option>
					<option value="4"{categoria_4}>CATEGORIA 4</option>
					<option value="5"{categoria_5}>CATEGORIA 5</option>
					<option value="6"{categoria_6}>CATEGORIA 6</option>
					<option value="7"{categoria_7}>CATEGORIA 7</option>
					<option value="8"{categoria_8}>CATEGORIA 8</option>
					<option value="9"{categoria_9}>CATEGORIA 9</option>
					<option value="10"{categoria_10}>CATEGORIA 10</option>
				</select></td>
		</tr>
		<tr>
			<th colspan="2" align="left" scope="row"><img src="/lecaroz/iconos/info.png" width="16" height="16" /> Informaci&oacute;n del local</th>
		</tr>
		<tr class="linea_on">
			<th align="left" scope="row">Tipo</th>
			<td align="left" id="tipo_local" scope="row"><select name="tipo_local" id="tipo_local2">
					<option value="1"{tipo_local_1}>COMERCIAL</option>
					<option value="2"{tipo_local_2}>VIVIENDA</option>
				</select></td>
		</tr>
		<tr class="linea_off">
			<th align="left" scope="row">Domicilio</th>
			<td align="left" id="domicilio_local" scope="row"><textarea name="domicilio" class="valid toText cleanText toUpper" id="domicilio" cols="65" rows="5">{domicilio}</textarea></td>
		</tr>
		<tr class="linea_on">
			<th align="left" scope="row">Superficie (m&#178;)</th>
			<td align="left" id="superficie_local" scope="row"><input name="superficie" type="text" class="valid Focus numberPosFormat right" id="superficie" value="{superficie}" size="8" precision="2"></td>
		</tr>
		<tr class="linea_off">
			<th align="left" scope="row">Cuenta de predial</th>
			<td align="left" id="superficie_local2" scope="row"><input name="cuenta_predial" type="text" class="valid Focus onlyNumbersAndLetters" id="cuenta_predial" value="{cuenta_predial}" size="15" maxlength="30" /></td>
		</tr>
	</table>
	<p>
		<input type="button" name="regresar" id="regresar" value="Regresar" />
		&nbsp;&nbsp;
		<input type="button" name="modificar" id="modificar" value="Modificar" />
	</p>
</form>
