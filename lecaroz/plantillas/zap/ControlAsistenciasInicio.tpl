<form name="Datos" class="formulario" id="Datos">
  <table class="tabla_captura">
  <tr class="linea_off">
    <th align="left" scope="row">Compa&ntilde;&iacute;a</th>
    <td align="left"><input name="num_cia" type="text" class="cap toPosInt alignCenter" id="num_cia" size="3" />
      <input name="nombre_cia" type="text" class="disabled" id="nombre_cia" size="30" /></td>
  </tr>
  <tr class="linea_on">
    <th align="left" scope="row">Periodo</th>
    <td align="left"><input name="fecha1" type="text" class="cap toDate alignCenter" id="fecha1" value="{fecha1}" size="10" maxlength="10" />
      al
        <input name="fecha2" type="text" class="cap toDate alignCenter" id="fecha2" value="{fecha2}" size="10" maxlength="10" /></td>
  </tr>
  <tr class="linea_off">
    <th align="left" scope="row">Empleado(s)</th>
    <td align="left"><select name="idemp[]" size="10" multiple="multiple" id="idemp" style="width:100%;">
      <option value=""></option>
    </select>
    </td>
  </tr>
</table>
  <p>
    <input name="consultar" type="button" class="boton" id="consultar" value="Consultar" />
  </p>
  </form>