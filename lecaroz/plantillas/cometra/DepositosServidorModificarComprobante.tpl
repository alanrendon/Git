<form name="Datos" id="Datos" class="FormValidator FormStyles">
<table class="tabla_captura">
  <tr class="linea_off">
	<th align="left" scope="row">Comprobante Actual </th>
	<td align="center" class="bold"><input name="comprobante_actual" type="text" class="textAlignCenter bold" id="comprobante_actual" value="{comprobante}" size="10" readonly="true" /></td>
  </tr>
  <tr class="linea_on">
	<th align="left" scope="row">Comprobante Nuevo </th>
	<td align="center"><input name="comprobante_nuevo" type="text" class="valid Focus onlyNumbers textAlignCenter bold" id="comprobante_nuevo" value="{comprobante}" size="10" /></td>
  </tr>
</table>
<p>
  <input name="cancelar" type="button" id="cancelar" value="Cancelar" />
  &nbsp;&nbsp;
  <input name="cambiar" type="button" id="cambiar" value="Cambiar" />
</p>
</form>