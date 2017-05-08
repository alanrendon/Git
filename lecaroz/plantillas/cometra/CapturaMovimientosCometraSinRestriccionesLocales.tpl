<form action="" method="get" name="Locales" class="FormValidator FormStyles" id="Locales">
<table align="center" class="tabla_captura">
  <tr>
	<th scope="col">Fecha</th>
	<th scope="col">Local</th>
    <th scope="col">Cheque</th>
  </tr>
  <tr>
	<td align="center"><select name="mes" id="mes">
		<option value="01"{1}>ENERO</option>
		<option value="02"{2}>FEBRERO</option>
		<option value="03"{3}>MARZO</option>
		<option value="04"{4}>ABRIL</option>
		<option value="05"{5}>MAYO</option>
		<option value="06"{6}>JUNIO</option>
		<option value="07"{7}>JULIO</option>
		<option value="08"{8}>AGOSTO</option>
		<option value="09"{9}>SEPTIEMBRE</option>
		<option value="10"{10}>OCTUBRE</option>
		<option value="11"{11}>NOVIEMBRE</option>
		<option value="12"{12}>DICIEMBRE</option>
	  </select>
	  <input name="anio" type="text" class="valid Focus toPosInt center" id="anio" value="{anio}" size="4" maxlength="4" /></td>
	<td align="center"><select name="locales" id="locales">
		<!-- START BLOCK : local -->
		<option value="{id}">{num_local} {nombre_local} - {renta}</option>
		<!-- END BLOCK : local -->
	  </select>	</td>
    <td align="center"><input name="cheque" type="checkbox" id="cheque" value="1" /></td>
  </tr>
</table>
</form>
<p align="center">
  <input name="cancelar" type="button" id="cancelar" value="Cancelar" />
  &nbsp;&nbsp;
  <input name="aceptar" type="button" id="aceptar" value="Aceptar" />
</p>
