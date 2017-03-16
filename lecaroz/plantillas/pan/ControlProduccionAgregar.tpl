<form name="Datos" class="formulario" id="Datos">
  <table class="tabla_captura">
    <tr>
      <th align="left" scope="row">Compa&ntilde;&iacute;a</th>
      <td class="linea_off"><input name="num_cia" type="hidden" id="num_cia" value="{num_cia}" />
        <strong>{num_cia} {nombre} </strong></td>
    </tr>
    <tr>
      <th align="left" scope="row">Turno</th>
      <td class="linea_on">
	    <select name="cod_turno" id="cod_turno">
          <!-- START BLOCK : turno -->
		  <option value="{cod}">{nombre}</option>
		  <!-- END BLOCK : turno -->
        </select>
	  </td>
    </tr>
    <tr>
      <th align="left" scope="row">Producto&nbsp;&nbsp;<img src="imagenes/info.png" name="productos" width="16" height="16" id="productos" /></th>
      <td class="linea_off"><input name="cod_producto" type="text" class="cap toPosInt alignCenter" id="cod_producto" size="3" />
        <input name="nombre_producto" type="text" class="disabled" id="nombre_producto" size="60" /></td>
    </tr>
    <tr>
      <th align="left" scope="row">Orden</th>
      <td class="linea_on"><input name="num_orden" type="text" class="cap toPosInt alignCenter" id="num_orden" value="{orden}" size="5" /></td>
    </tr>
    <tr>
      <th align="left" scope="row">Precio Raya </th>
      <td class="linea_off"><input name="precio_raya" type="text" class="cap numPosFormat4 alignRight" id="precio_raya" size="10" /></td>
    </tr>
    <tr>
      <th align="left" scope="row">% Raya </th>
      <td class="linea_on"><input name="porc_raya" type="text" class="cap numPosFormat2 alignRight" id="porc_raya" size="10" /></td>
    </tr>
    <tr>
      <th align="left" scope="row">Precio Venta </th>
      <td class="linea_off"><input name="precio_venta" type="text" class="cap numPosFormat3 alignRight" id="precio_venta" size="10" /></td>
    </tr>
    <tr>
    	<th align="left" scope="row">Tantos</th>
    	<td class="linea_off"><input name="tantos" type="text" class="cap numPosFormat2 alignRight" id="tantos" size="10" /></td>
    	</tr>
  </table>
  <p>
    <input name="regresar" type="button" class="boton" id="regresar" value="Regresar" />
&nbsp;&nbsp;
<input name="agregarControl" type="button" class="boton" id="agregarControl" value="Agregar Control" />
</p>
</form>