<form action="" method="get" name="Locales" class="FormValidator FormStyles" id="Locales">
<table align="center" class="tabla_captura">
  <tr>
	<th scope="col">Recibo de arrendamiento</th>
    <th scope="col">Cheque</th>
  </tr>
  <tr>
	<td align="center"><select name="recibos" id="recibos">
		<!-- START BLOCK : recibo -->
		<option value="{id}">[{mes} {anio}] {arrendatario} {nombre_arrendatario} - {renta}</option>
		<!-- END BLOCK : recibo -->
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
