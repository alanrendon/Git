<form method="post" name="Datos" class="FormStyles FormValidator" id="Datos">
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
	  </select>
	  </td>
	</tr>
  </table>
  <p>
	<input name="consultar" type="button" id="consultar" value="Consultar" />
  </p>
</form>