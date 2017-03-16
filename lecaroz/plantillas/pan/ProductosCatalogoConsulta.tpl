<p><img src="/lecaroz/iconos/plus.png" width="16" height="16" />
	<input type="button" name="alta" id="alta" value="Alta" />
</p>
<table class="table">
	<thead>
		<tr>
			<th colspan="2" scope="col">Producto</th>
			<th scope="col">Tipo</th>
			<th scope="col">Control</th>
			<th scope="col"><img src="/lecaroz/imagenes/tool16x16.png" width="16" height="16" /></th>
		</tr>
	</thead>
	<tbody>
		<!-- START BLOCK : row -->
		<tr>
			<td>{producto}</td>
			<td>{descripcion}</td>
			<td>{tipo_pan}</td>
			<td align="center">{control_produccion}</td>
			<td align="center"><img src="/lecaroz/iconos/pencil.png" alt="{producto}" name="mod" width="16" height="16" class="icono" id="mod" />&nbsp;<img src="/lecaroz/iconos/cancel.png" alt="{producto}" name="baja" width="16" height="16" class="icono" id="baja" /></td>
		</tr>
		<!-- END BLOCK : row -->
	</tbody>
	<tfoot>
		<tr>
			<td colspan="8">&nbsp;</td>
		</tr>
	</tfoot>
</table>
<p>
	<input type="submit" name="regresar" id="regresar" value="Regresar">
</p>
