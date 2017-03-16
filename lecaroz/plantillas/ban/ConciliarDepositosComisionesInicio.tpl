<form method="post" name="Datos" class="formulario" id="Datos">
      <table class="tabla_captura">
      <tr>
        <th align="left">Compa&ntilde;&iacute;a(s)</th>
        <td class="linea_off"><input name="cias" type="text" class="cap toInterval" id="cias" size="40" /></td>
      </tr>
      <tr>
        <th align="left">Administrador</th>
        <td class="linea_on"><select name="admin" id="admin">
          <option value="" selected="selected"></option>
		  <!-- START BLOCK : admin -->
          <option value="{id}">{nombre}</option>
		  <!-- END BLOCK : admin -->
          </select>		</td>
      </tr>
      <tr>
        <th align="left">Banco</th>
        <td class="linea_off"><select name="banco" id="banco">
          <option value="" selected="selected"></option>
          <option value="1">BANORTE</option>
          <option value="2">SANTANDER</option>
        </select>        </td>
      </tr>
      <tr>
        <th align="left">Periodo</th>
        <td class="linea_on"><input name="fecha1" type="text" class="cap toDate alignCenter" id="fecha1" size="10" maxlength="10" />
        al
          <input name="fecha2" type="text" class="cap toDate alignCenter" id="fecha2" size="10" maxlength="10" /></td>
      </tr>
      <tr>
        <th align="left">C&oacute;digos</th>
        <td class="linea_off"><select name="codigos[]" size="5" multiple="multiple" id="codigos" style="width:98%;">
        </select>        </td>
      </tr>
    </table>
      <p>
        <input name="buscar" type="button" class="boton" id="buscar" value="Buscar" />
      </p>
    </form>