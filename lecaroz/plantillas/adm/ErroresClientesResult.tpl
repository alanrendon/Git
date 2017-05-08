<form name="Result" class="formulario" id="Result">
<table class="tabla_captura">
	<tr>
	  <th colspan="6" align="left" style="font-size:8pt;" scope="col"><input name="checkall" type="checkbox" id="checkall" checked="checked" />
      Seleccionar todos</th>
	</tr>
	<!-- START BLOCK : cia -->
	<tr>
	  <th colspan="6" align="left" style="font-size:12pt;" scope="col">{num_cia} {nombre_cia} </th>
	</tr>
	<tr>
	  <th>&nbsp;</th>
	  <th>Fecha</th>
	  <th>Clientes</th>
	  <th>Promedio</th>
	  <th>Diferencia</th>
	  <th>%</th>
	</tr>
	<!-- START BLOCK : row -->
	<tr id="row" class="linea_{color}">
	  <td><input name="id[]" type="checkbox" id="id" value="{id}" checked="checked" /></td>
	  <td align="center">{fecha}</td>
	  <td align="right" style="color:#{color_diferencia};">{clientes}</td>
	  <td align="right" style="color:#060;">{promedio}</td>
	  <td align="right" style="color:#{color_diferencia};">{diferencia}</td>
	  <td align="right" style="color:#{color_diferencia};{subr}">{porc}</td>
	</tr>
	<!-- END BLOCK : row -->
	<tr>
	  <td colspan="6">&nbsp;</td>
	</tr>
	<!-- END BLOCK : cia -->
  </table>
  <p>
	<input name="cancelar" type="button" class="boton" id="cancelar" value="Cancelar" />
	&nbsp;&nbsp;
	<input name="autorizar" type="button" class="boton" id="autorizar" value="Autorizar" />
  </p>
</form>