<form action="" method="get" name="Datos" class="FormValidator FormStyles" id="Datos">
<table class="tabla_captura">
  <tr class="linea_off">
	 <th align="left" scope="row">Compa&ntilde;&iacute;a(s)</th>
	 <td><input name="cias" type="text" class="valid toInterval" id="cias" size="30" /></td>
  </tr>
  <tr class="linea_on">
	<th align="left" scope="row">Administrador</th>
	<td><select name="contador" id="contador">
	    <option value=""></option>
	    <!-- START BLOCK : contador -->
	    <option value="{id}">{nombre}</option>
	    <!-- END BLOCK : contador -->
	  </select>
	</td>
  </tr>
  <tr class="linea_off">
	<th align="left" scope="row">Estado</th>
	<td><input name="status[]" type="checkbox" id="status" value="1" checked="checked" />
		En uso<br />
		<input name="status[]" type="checkbox" id="status" value="2" checked="checked" />
		Terminados<br />
		<input name="status[]" type="checkbox" id="status" value="3" checked="checked" />
		Pendientes<br />
		<input name="status[]" type="checkbox" id="status" value="0" checked="checked" />
		Cancelados</td>
	</tr>
  <tr class="linea_on">
	<th align="left" scope="row">Tipo</th>
	<td><input name="tipo_serie[]" type="checkbox" id="tipo_serie" value="1" checked="checked" />
		Facturas<br />
		<input name="tipo_serie[]" type="checkbox" id="tipo_serie" value="2" checked="checked" />
		Recibos de Arrendamiento<br />
		<input name="tipo_serie[]" type="checkbox" id="tipo_serie" value="3" checked="checked" />
		Notas de Cr&eacute;dito</td>
	</tr>
</table>
<p>
  <input type="button" name="consultar" id="consultar" value="Consultar" />
</p>
</form>