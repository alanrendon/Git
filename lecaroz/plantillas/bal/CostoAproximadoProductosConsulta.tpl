<p>
	<img src="/lecaroz/iconos/plus.png" width="16" height="16" />
	<input type="button" name="alta" id="alta" value="Alta" />
</p>
<table class="table" id="controles">
	<thead>
		<tr>
			<th scope="col">Producto</th>
			<th scope="col">Total consumo</th>
			<th scope="col">Raya</th>
			<th scope="col">Costo total</th>
			<th scope="col"><img src="/lecaroz/imagenes/tool16x16.png" width="16" height="16" /></th>
		</tr>
	</thead>
	<tbody>
		<!-- START BLOCK : row -->
		<tr id="row_{id}">
			<td>{nombre_producto}</td>
			<td class="right green">{total_consumo}</td>
			<td class="right red">{raya}</td>
			<td class="right blue">{costo_total}</td>
			<td class="center"><img src="/lecaroz/iconos/pencil.png" alt="{id}" name="mod" width="16" height="16" class="icono" id="mod" />&nbsp;<img src="/lecaroz/iconos/cancel.png" alt="{id}" name="baja" width="16" height="16" class="icono" id="baja" /></td>
		</tr>
		<!-- END BLOCK : row -->
	</tbody>
	<tfoot>
		<tr>
			<td colspan="5">&nbsp;</td>
		</tr>
	</tfoot>
</table>
<p>
	<input type="submit" name="regresar" id="regresar" value="Regresar">
</p>
