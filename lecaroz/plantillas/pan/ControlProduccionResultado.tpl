<form name="Datos" class="formulario" id="Datos">
<p><img src="imagenes/insert16x16.png" width="16" height="16" />&nbsp;
<input name="agregar" type="button" class="boton" id="agregar" value="Agregar Control" />
</p>
<table class="tabla_captura">
<tr>
  <th colspan="7" align="left" scope="col" style="font-size:14pt;"><input name="num_cia" type="hidden" id="num_cia" value="{num_cia}">{num_cia} {nombre}</th>
</tr>
<!-- START BLOCK : turno -->
<tr>
  <th colspan="7" align="left" scope="col">{cod} {turno} </th>
  </tr>
<tr>
  <th>Producto</th>
  <th>Precio<br />
	Raya</th>
  <th>% Raya </th>
  <th>Precio<br />
	Venta</th>
  <th>Tantos</th>
  <th>Orden</th>
  <th><img src="imagenes/tool16x16.png" width="16" height="16" /></th>
</tr>
<!-- START BLOCK : producto -->
<tr class="linea_{row_color}">
  <td>{cod} {producto}</td>
  <td align="right" style="color:#C00;">{precio_raya}</td>
  <td align="right" style="color:#C00;">{porc_raya}</td>
  <td align="right" style="color:#00C;">{precio_venta}</td>
  <td align="right">{tantos}</td>
  <td align="center">{orden}</td>
  <td><img src="imagenes/pencil16x16.png" alt="{id}" name="mod" width="16" height="16" id="mod" />&nbsp;&nbsp;<img src="imagenes/delete16x16.png" alt="{id}" name="del" width="16" height="16" id="del" /></td>
</tr>
<!-- END BLOCK : producto -->
<tr>
  <td colspan="7" class="linea_{row_color}">&nbsp;</td>
</tr>
<!-- END BLOCK : turno -->
</table>
<p>
<input name="regresar" type="button" class="boton" id="regresar" value="Regresar" />
</p>
</form>