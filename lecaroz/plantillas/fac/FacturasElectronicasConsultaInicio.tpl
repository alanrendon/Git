<!-- START BLOCK : normal -->
<form action="" method="post" name="Datos" class="FormValidator FormStyles" id="Datos">
<table class="tabla_captura">
  <tr class="linea_off">
	<th align="left" scope="row">Compa&ntilde;&iacute;a(s)</th>
	<td><input name="cias" type="text" class="valid toInterval" id="cias" size="30" /></td>
  </tr>
  <tr class="linea_on">
	<th align="left" scope="row">Administrador</th>
	<td><select name="admin" id="admin">
	    <option value=""></option>
	    <!-- START BLOCK : admin -->
	    <option value="{id}">{nombre}</option>
	    <!-- END BLOCK : admin -->
	  </select>
	</td>
  </tr>
  <tr class="linea_off">
  	<th align="left" scope="row">Contador</th>
  	<td><select name="contador" id="contador">
  		<option value=""></option>
  		<!-- START BLOCK : contador -->
  		<option value="{id}">{nombre}</option>
  		<!-- END BLOCK : contador -->
  		</select></td>
  	</tr>
  <tr class="linea_on">
	<th align="left" scope="row">Periodo</th>
	<td><input name="fecha1" type="text" class="valid Focus toDate center" id="fecha1" value="{fecha1}" size="10" maxlength="10" />
	  al
	  <input name="fecha2" type="text" class="valid Focus toDate center" id="fecha2" value="{fecha2}" size="10" maxlength="10" /></td>
  </tr>
  <tr class="linea_off">
	<th align="left" scope="row">Folio(s)</th>
	<td><input name="folios" type="text" class="valid toInterval" id="folios" size="30" /></td>
  </tr>
  <tr class="linea_on">
	<th align="left" scope="row">Estatus</th>
	<td><input name="pendientes" type="checkbox" id="pendientes" value="1" checked="checked" />
	Pendientes<br />
	<input name="pagadas" type="checkbox" id="pagadas" value="1" checked="checked" />
	Pagadas</td>
  </tr>
  <tr class="linea_off">
	<th align="left" scope="row">Tipo</th>
	<td><input name="tipo[]" type="checkbox" id="tipo" value="1" checked="checked" />
	  Ventas<br />
	  <input name="tipo[]" type="checkbox" id="tipo" value="2" checked="checked" />
	  Clientes<br />
	  <input name="tipo[]" type="checkbox" id="tipo" value="3" checked="checked" />
	  Oficinas y talleres<br />
	  <input name="tipo[]" type="checkbox" id="tipo" value="4" checked="checked" />
	  Condimento<br />
	  <input name="tipo[]" type="checkbox" id="tipo" value="5" checked="checked" />
	  Rentas<br />
	  <input name="tipo[]" type="checkbox" id="tipo" value="7" checked="checked" />
	  Capacitación<br />
	  <input name="tipo[]" type="checkbox" id="tipo" value="6" checked="checked" />
	  Otros</td>
  </tr>
  <tr class="linea_on">
	<th align="left" scope="row">Incluir canceladas </th>
	<td><input name="canceladas" type="checkbox" id="canceladas" value="1" />
	Si</td>
  </tr>
</table>
<p>
  <input name="consultar" type="button" id="consultar" value="Consultar" />
</p>
</form>
<!-- END BLOCK : normal -->
<!-- START BLOCK : ipad -->
<form action="" method="post" name="Datos" class="FormValidator FormStyles" id="Datos">
<table class="tabla_captura">
  <tr class="linea_off">
	<th align="left" scope="row">Compa&ntilde;&iacute;a(s)</th>
	<td><select name="cias" id="cias">
	  <!-- START BLOCK : cia -->
      <option value="{num_cia}">{num_cia} {nombre_cia}</option>
      <!-- END BLOCK : cia -->
	  </select></td>
  </tr>
  <tr class="linea_off">
    <th align="left" scope="row">Periodo</th>
    <td><input name="fecha1" type="text" class="valid Focus toDate center" id="fecha1" value="{fecha1}" size="10" maxlength="10" />
      al
      <input name="fecha2" type="text" class="valid Focus toDate center" id="fecha2" value="{fecha2}" size="10" maxlength="10" /></td>
  </tr>
  <tr class="linea_on">
	<th align="left" scope="row">Folio(s)</th>
	<td><input name="folios" type="text" class="valid toInterval" id="folios" size="30" /></td>
  </tr>
  <tr class="linea_off">
	<th align="left" scope="row">Estatus</th>
	<td><input name="pendientes" type="checkbox" id="pendientes" value="1" checked="checked" />
	Pendientes<br />
	<input name="pagadas" type="checkbox" id="pagadas" value="1" checked="checked" />
	Pagadas</td>
  </tr>
  <tr class="linea_on">
	<th align="left" scope="row">Tipo</th>
	<td><input name="tipo[]" type="checkbox" id="tipo" value="1" checked="checked" />
	  Ventas<br />
	  <input name="tipo[]" type="checkbox" id="tipo" value="2" checked="checked" />
	  Clientes<br />
	  <input name="tipo[]" type="checkbox" id="tipo" value="3" checked="checked" />
	  Oficinas y talleres<br />
	  <input name="tipo[]" type="checkbox" id="tipo" value="4" checked="checked" />
	  Condimento<br />
	  <input name="tipo[]" type="checkbox" id="tipo" value="5" checked="checked" />
	  Rentas<br />
	  <input name="tipo[]" type="checkbox" id="tipo" value="6" checked="checked" />
	  Otros</td>
  </tr>
  <tr class="linea_off">
	<th align="left" scope="row">Incluir canceladas </th>
	<td><input name="canceladas" type="checkbox" id="canceladas" value="1" />
	Si</td>
  </tr>
</table>
<p>
  <input name="consultar" type="button" id="consultar" value="Consultar" />
</p>
</form>
<!-- END BLOCK : ipad -->