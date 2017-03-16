<form name="Result" class="formulario" id="Result">
<table class="tabla_captura">
	<!-- START BLOCK : cia -->
	<tr>
	  <th colspan="7" align="left" style="font-size:12pt;" scope="col">{num_cia} {nombre_cia} </th>
	</tr>
	<tr>
	  <th>&nbsp;</th>
	  <th>Fecha</th>
	  <th>Venta AM </th>
	  <th>Error AM </th>
	  <th>Venta PM </th>
	  <th>Error PM </th>
	  <th>Venta en Puerta </th>
	</tr>
	<!-- START BLOCK : row -->
	<tr id="row" class="linea_{color}">
	  <td><input name="id[]" type="checkbox" id="id" value="{id}" checked="checked" /></td>
	  <td align="center">{fecha}</td>
	  <td align="right" style="color:#060;">{am}</td>
	  <td align="right" style="color:#C00;">{am_error}</td>
	  <td align="right" style="color:#060;">{pm}</td>
	  <td align="right" style="color:#C00;">{pm_error}</td>
	  <td align="right" style="color:#00C;">{venta_pta}</td>
	</tr>
	<!-- END BLOCK : row -->
	<tr>
	  <td colspan="7">&nbsp;</td>
	</tr>
	<!-- END BLOCK : cia -->
  </table>
  <p>
	<input name="cancelar" type="button" class="boton" id="cancelar" value="Cancelar" />
	&nbsp;&nbsp;
	<input name="autorizar" type="button" class="boton" id="autorizar" value="Autorizar" />
  </p>
</form>