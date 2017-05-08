<p><img src="/lecaroz/iconos/plus.png" width="16" height="16" />
	<input type="button" name="alta" id="alta" value="Alta" />
</p>
<table class="table">
	<thead>
		<tr>
			<th colspan="10" scope="col">&nbsp;</th>
		</tr>
	</thead>
	<tbody>
		<!-- START BLOCK : cia -->
		<tr>
			<th colspan="5" align="left" class="font12" scope="col">{num_cia} {nombre_cia}</th>
		</tr>
		<tr>
			<th scope="col">#</th>
			<th scope="col">Producto</th>
			<th scope="col">Precio</th>
			<th scope="col">Tipo</th>
			<th scope="col"><img src="/lecaroz/imagenes/tool16x16.png" width="16" height="16" /></th>
		</tr>
		<!-- START BLOCK : row -->
		<tr>
			<td align="right" style="padding-right:20px;">{orden}</td>
			<td>{cod} {nombre}</td>
			<td align="right">{precio}</td>
			<td>{tipo}</td>
			<td align="center"><img src="/lecaroz/iconos/pencil{disabled_mod}.png" alt="{id}" name="mod" width="16" height="16" class="icono" id="mod" />&nbsp;<img src="/lecaroz/iconos/cancel{disabled_baja}.png" alt="{id}" name="baja" width="16" height="16" class="icono" id="baja" /></td>
		</tr>
		<!-- END BLOCK : row -->
		<tr>
			<td colspan="5">&nbsp;</td>
		</tr>
		<!-- END BLOCK : cia -->
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
