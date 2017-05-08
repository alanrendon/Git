<p><img src="/lecaroz/iconos/plus.png" width="16" height="16" />
	<input type="button" name="alta" id="alta" value="Alta" />
</p>
<table class="table">
	<thead>
		<tr>
			<th scope="col">Reserva</th>
			<th scope="col">Gasto</th>
			<th scope="col">Aplicar<br />
				promedio</th>
			<th scope="col">Distribuir<br />
				diferencia</th>
			<th scope="col"><img src="/lecaroz/imagenes/tool16x16.png" width="16" height="16" /></th>
		</tr>
	</thead>
	<tbody>
		<!-- START BLOCK : row -->
		<tr>
			<td>{reserva} {descripcion_reserva}</td>
			<td>{gasto} {descripcion_gasto}</td>
			<td align="center">{aplicar_promedio}</td>
			<td align="center">{distribuir_diferencia}</td>
			<td align="center"><img src="/lecaroz/iconos/pencil.png" alt="{reserva}" name="mod" width="16" height="16" class="icono" id="mod" />&nbsp;<img src="/lecaroz/iconos/cancel.png" alt="{reserva}" name="baja" width="16" height="16" class="icono" id="baja" /></td>
		</tr>
		<!-- END BLOCK : row -->
	</tbody>
	<tfoot>
		<tr>
			<td colspan="5">&nbsp;</td>
		</tr>
	</tfoot>
</table>
