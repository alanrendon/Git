<p><img src="/lecaroz/iconos/plus.png" width="16" height="16" />
	<input type="button" name="alta" id="alta" value="Alta" />
</p>
<table class="table">
	<thead>
		<tr>
			<th colspan="2" scope="col">Gasto</th>
			<th scope="col">Balance</th>
			<th scope="col">Tipo</th>
			<th scope="col">Aplicación</th>
			<th scope="col">Pan comprado</th>
			<th scope="col">% de<br>
				descuento</th>
			<th scope="col"><img src="/lecaroz/imagenes/tool16x16.png" width="16" height="16" /></th>
		</tr>
	</thead>
	<tbody>
		<!-- START BLOCK : row -->
		<tr>
			<td>{gasto}</td>
			<td>{descripcion}</td>
			<td>{balance}</td>
			<td>{tipo}</td>
			<td>{aplicacion}</td>
			<td align="center">{pan_comprado}</td>
			<td align="right">{descuento}</td>
			<td align="center"><img src="/lecaroz/iconos/pencil.png" alt="{gasto}" name="mod" width="16" height="16" class="icono" id="mod" />&nbsp;<img src="/lecaroz/iconos/cancel.png" alt="{gasto}" name="baja" width="16" height="16" class="icono" id="baja" /></td>
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
