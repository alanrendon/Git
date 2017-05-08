<form name="Datos" class="FormValidator FormStyles" id="Datos">
  <table class="tabla_captura">
	<tr class="linea_off">
	  <th align="left" scope="row">Fecha</th>
	  <td align="center" class="font12 bold">{fecha}</td>
	</tr>
	<tr class="linea_on">
	  <th align="left" scope="row">Comprobante</th>
	  <td align="center" class="font12 bold"><input name="comprobante" type="hidden" id="comprobante" value="{comprobante}" />
	  {comprobante}</td>
	</tr>
  </table>
  <br />
  <table class="tabla_captura">
	<tr>
	  <th scope="col">Compa&ntilde;&iacute;a</th>
	  <th scope="col">Tipo</th>
	  <th scope="col">Fecha</th>
	  <th scope="col">Concepto</th>
	  <th scope="col">Importe</th>
	</tr>
	<!-- START BLOCK : row -->
	<tr class="linea_off">
	  <td><input name="id[]" type="hidden" id="id" value="{id}" />
		<input name="num_cia[]" type="text" class="valid Focus toPosInt center" id="num_cia" value="{num_cia}" size="3" />
	  <input name="nombre_cia[]" type="text" id="nombre_cia" value="{nombre_cia}" size="30" readonly="readonly" /></td>
	  <td><input name="cod_mov[]" type="text" class="valid Focus toPosInt center" id="cod_mov" value="{cod_mov}" size="3" />
	  <input name="descripcion[]" type="text" id="descripcion" value="{descripcion}" size="9" readonly="readonly" /></td>
	  <td><input name="fecha[]" type="text" class="valid Focus toDate center" id="fecha" value="{fecha}" size="10" maxlength="10" /></td>
	  <td><input name="concepto[]" type="text" class="valid toText cleanText toUpper" id="concepto" value="{concepto}" size="30" maxlength="70" /></td>
	  <td><input name="importe[]" type="text" class="valid Focus numberPosFormat right" precision="2" id="importe" value="{importe}" size="10" /></td>
	</tr>
	<!-- END BLOCK : row -->
	<tr>
	  <th colspan="4" align="right">Total Comprobante </th>
	  <th><input name="total" type="text" class="right bold" id="total" value="{total}" size="10" readonly="readonly" /></th>
	</tr>
  </table>
  <p>
	<input name="cancelar" type="button" id="cancelar" value="Cancelar" />
  &nbsp;&nbsp;
  <input name="actualizar" type="button" id="actualizar" value="Actualizar" />
  </p>
</form>