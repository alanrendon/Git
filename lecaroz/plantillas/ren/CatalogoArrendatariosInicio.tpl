<form action="" method="post" name="Datos" class="FormValidator FormStyles" id="Datos">
	<table class="tabla_captura">
		<tr class="linea_off">
			<th align="left" scope="row">Arrendador(es)</th>
			<td><input name="arrendadores" type="text" class="valid toInterval" id="arrendadores" size="40" /></td>
			</tr>
		<tr class="linea_on">
			<th align="left" scope="row">Bloque</th>
			<td><input name="bloque[]" type="checkbox" id="bloque" value="1" checked="checked" />
				Internos<br />
				<input name="bloque[]" type="checkbox" id="bloque" value="2" checked="checked" />
				Externos</td>
			</tr>
		<tr class="linea_off">
			<th align="left" scope="row">Arrendatario(s)</th>
			<td><select name="arrendatarios[]" size="5" multiple="multiple" id="arrendatarios" style="width:98%;">
				</select></td>
			</tr>
		<tr class="linea_on">
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
		<tr class="linea_off">
			<th align="left" scope="row">Incluir vacios</th>
			<td><input name="vacios" type="checkbox" id="vacios" value="1" />
				Si</td>
		</tr>
		</table>
	<p>
		<input type="button" name="consultar" id="consultar" value="Consultar" />
	</p>
</form>
