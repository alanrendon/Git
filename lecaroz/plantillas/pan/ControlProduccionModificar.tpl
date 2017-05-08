<form name="Datos" class="formulario" id="Datos">
  <table class="tabla_captura">
    <tr>
      <th align="left" scope="row">Compa&ntilde;&iacute;a</th>
      <td class="linea_off"><input name="id" type="hidden" id="id" value="{id}" />
        <input name="num_cia" type="hidden" id="num_cia" value="{num_cia}" />
        <strong>{num_cia} {nombre} </strong></td>
    </tr>
    <tr>
      <th align="left" scope="row">Turno</th>
      <td class="linea_on"><strong>
        <input name="cod_turno" type="hidden" id="cod_turno" value="{cod_turno}" />
      {turno}</strong></td>
    </tr>
    <tr>
      <th align="left" scope="row">Producto</th>
      <td class="linea_off"><strong>{cod_producto} {producto} </strong></td>
    </tr>
    <tr>
      <th align="left" scope="row">Orden</th>
      <td class="linea_on"><input name="num_orden" type="text" class="cap toPosInt alignCenter" id="num_orden" value="{num_orden}" size="5" />
      <input name="num_orden_old" type="hidden" id="num_orden_old" value="{num_orden}" /></td>
    </tr>
    <tr>
      <th align="left" scope="row">Precio Raya </th>
      <td class="linea_off"><input name="precio_raya" type="text" class="cap numPosFormat4 alignRight" id="precio_raya" value="{precio_raya}" size="10" /></td>
    </tr>
    <tr>
      <th align="left" scope="row">% Raya </th>
      <td class="linea_on"><input name="porc_raya" type="text" class="cap numPosFormat2 alignRight" id="porc_raya" value="{porc_raya}" size="10" /></td>
    </tr>
    <tr>
      <th align="left" scope="row">Precio Venta </th>
      <td class="linea_off"><input name="precio_venta" type="text" class="cap numPosFormat3 alignRight" id="precio_venta" value="{precio_venta}" size="10" /></td>
    </tr>
	 <tr>
      <th align="left" scope="row">Tantos </th>
      <td class="linea_off"><input name="tantos" type="text" class="cap numPosFormat2 alignRight" id="tantos" value="{tantos}" size="10" /></td>
    </tr>
  </table>
  <p>
    <input name="regresar" type="button" class="boton" id="regresar" value="Regresar" />
&nbsp;&nbsp;
<input name="actualizarControl" type="button" class="boton" id="actualizarControl" value="Actualizar Control" />
</p>
  </form>