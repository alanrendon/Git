<input name="num_cia" type="hidden" id="num_cia" value="{num_cia}" />
<table class="table">
	<thead>
		<tr>
			<th colspan="2">&nbsp;</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td align="left" class="bold font12">Compa&ntilde;&iacute;a</td>
			<td align="left" class="bold font12">{num_cia} {nombre_cia}</td>
		</tr>
	</tbody>
	<tfoot>
		<tr>
			<td colspan="2">&nbsp;</td>
		</tr>
	</tfoot>
</table>
<p>
	<img src="/lecaroz/iconos/plus.png" width="16" height="16" />
	<input type="button" name="alta" id="alta" value="Alta" />
</p>
<table class="table" id="controles">
	<thead>
		<tr>
			<th scope="col">Producto</th>
			<th scope="col">Alias</th>
			<th scope="col">Proveedor</th>
			<th scope="col">Precio<br />de compra</th>
			<th scope="col">Precio<br />de venta</th>
			<th scope="col">Con decimales</th>
			<th scope="col"><img src="/lecaroz/imagenes/tool16x16.png" width="16" height="16" /></th>
		</tr>
	</thead>
	<tbody>
		<!-- START BLOCK : row -->
		<tr id="row_{id}">
			<td class="dragme" style="cursor:move;">{codmp} {nombre_mp}</td>
			<td class="green">{nombre_alt}</td>
			<td>{num_pro} {nombre_pro}</td>
			<td class="right red">{precio_compra}</td>
			<td class="right blue">{precio_venta}</td>
			<td class="center">{con_decimales}</td>
			<td class="center"><img src="/lecaroz/iconos/pencil.png" alt="{id}" name="mod" width="16" height="16" class="icono" id="mod" />&nbsp;<img src="/lecaroz/iconos/cancel.png" alt="{id}" name="baja" width="16" height="16" class="icono" id="baja" /></td>
		</tr>
		<!-- END BLOCK : row -->
	</tbody>
	<tfoot>
		<tr>
			<td colspan="7">&nbsp;</td>
		</tr>
	</tfoot>
</table>
<p>
	<input type="submit" name="regresar" id="regresar" value="Regresar">
</p>
