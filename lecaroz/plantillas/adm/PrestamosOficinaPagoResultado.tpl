<form name="Datos" class="FormValidator FormStyles" id="Datos">
  <table class="tabla_captura">
	<!-- START BLOCK : cia -->
	<tr>
	  <th colspan="5" align="left" class="font12" scope="col">{num_cia} {nombre_cia} </th>
	</tr>
	<tr>
	  <th>Empleado</th>
	  <th>Fecha</th>
	  <th>Debe</th>
	  <th>Importe</th>
	  <th>Saldo</th>
	</tr>
	<!-- START BLOCK : row -->
	<tr class="linea_{color}">
	  <td><input name="num_cia[]" type="hidden" id="num_cia[]" value="{num_cia}" />
		<input name="id[]" type="hidden" id="id" value="{id}" />
	  {empleado}</td>
	  <td><input name="fecha[]" type="text" class="valid Focus toDate center" id="fecha" size="10" maxlength="10" /></td>
	  <td><input name="debe[]" type="text" disabled="disabled" class="right" id="debe" value="{saldo}" size="10" /></td>
	  <td><input name="importe[]" type="text" class="valid Focus numberPosFormat right" precision="2" id="importe" size="10" /></td>
	  <td><input name="saldo[]" type="text" class="right" id="saldo" value="{saldo}" size="10" readonly="true" /></td>
	</tr>
	<!-- END BLOCK : row -->
	<tr>
	  <td colspan="5">&nbsp;</td>
	</tr>
	<!-- END BLOCK : cia -->
  </table>
  <br />
  <p>
    <input name="cancelar" type="button" class="boton" id="cancelar" value="Cancelar" />
	&nbsp;&nbsp;
	<input name="pagar" type="button" class="boton" id="pagar" value="Pagar" />
  </p>
</form>