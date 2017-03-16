<p><img src="/lecaroz/imagenes/insert16x16.png" width="16" height="16" /> &nbsp;
	<input type="button" name="alta" id="alta" value="Alta de producto" />
</p>
{index}
<table class="tabla_captura">
	<tr>
		<th scope="col">#</th>
		<th scope="col">Producto</th>
		<th scope="col">Unidad de<br />
			consumo</th>
		<th scope="col">Categoría</th>
		<th scope="col">Controlada</th>
		<th scope="col">Pedido<br />
			automático</th>
		<th scope="col">Sin<br />
			existencia</th>
		<th scope="col">Reporte de<br />m&aacute;ximos</th>
		<th scope="col">Grasa</th>
		<th scope="col">Az&uacute;car</th>
		<th scope="col">% I.E.P.S.</th>
		<th scope="col"><img src="/lecaroz/imagenes/tool16x16.png" width="16" height="16" /></th>
	</tr>
	<!-- START BLOCK : row -->
	<tr class="linea_{row_color}" id="row{codmp}">
		<td align="right">{codmp}</td>
		<td nowrap="nowrap" id="td">{imagen}{nombre_mp}</td>
		<td id="td_unidad{codmp}">{unidad}</td>
		<td nowrap="nowrap" id="td_categoria{codmp}">{categoria}</td>
		<td align="center"><img src="/lecaroz/iconos/accept{controlada}.png" alt="{codmp}" name="controlada{codmp}" width="16" height="16" id="controlada{codmp}" status="{status_controlada}" /></td>
		<td align="center"><img src="/lecaroz/iconos/accept{pedido}.png" alt="{codmp}" name="pedido{codmp}" width="16" height="16" id="pedido{codmp}" status="{status_pedido}" /></td>
		<td align="center"><img src="/lecaroz/iconos/accept{sin_existencia}.png" alt="{codmp}" name="sin_existencia{codmp}" width="16" height="16" id="sin_existencia{codmp}" status="{status_sin_existencia}" /></td>
		<td align="center"><img src="/lecaroz/iconos/accept{reporte_consumos_mas}.png" alt="{codmp}" name="reporte_consumos_mas{codmp}" width="16" height="16" id="reporte_consumos_mas{codmp}" status="{status_sin_existencia}" /></td>
		<td align="center"><img src="/lecaroz/iconos/accept{grasa}.png" alt="{codmp}" name="grasa{codmp}" width="16" height="16" id="grasa{codmp}" status="{status_sin_existencia}" /></td>
		<td align="center"><img src="/lecaroz/iconos/accept{azucar}.png" alt="{codmp}" name="azucar{codmp}" width="16" height="16" id="azucar{codmp}" status="{status_sin_existencia}" /></td>
		<td align="center">{ieps}</td>
		<td align="center" nowrap="nowrap">
			<img src="/lecaroz/imagenes/pencil16x16.png" alt="{codmp}" name="mod{codmp}" width="16" height="16" id="mod{codmp}" />
		</td>
	</tr>
	<!-- END BLOCK : row -->
</table>
{index}
<p>
	<input type="button" name="regresar" id="regresar" value="Regresar" />
	&nbsp;&nbsp;
	<input type="button" name="listado" id="listado" value="Listado" />
</p>
