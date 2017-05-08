<p>
	<button type="button" id="alta">
		<img src="/lecaroz/iconos/plus_round.png" width="16" height="16" />
		Alta
	</button>
</p>
<table class="table">
	<thead>
		<th colspan="7">&nbsp;</th>
	</thead>
	<tbody>
		<!-- START BLOCK : cia -->
		<tr>
			<th colspan="7" class="left font12">{num_cia} {nombre_cia}</th>
		</tr>
		<tr>
			<th scope="col">Tipo</th>
			<th scope="col">Proveedor</th>
			<th scope="col"># cuenta</th>
			<th scope="col">Recurrencia</th>
			<th scope="col">Propietario</th>
			<th scope="col">Observaciones</th>
			<th scope="col"><img src="/lecaroz/iconos/plus_round.png" width="16" height="16" id="alta_cia" class="icono" data-cia="{num_cia}" /></th>
		</tr>
		<!-- START BLOCK : row -->
		<tr>
			<td>{tipo}</td>
			<td>{num_pro} {nombre_pro}</td>
			<td>{cuenta}</td>
			<td>{recurrencia}</td>
			<td>{arrendador} {nombre_arrendador}</td>
			<td>{observaciones}</td>
			<td align="center">
				<img src="/lecaroz/iconos/pencil.png" alt="{id}" name="mod" width="16" height="16" class="icono" id="mod" />
			</td>
		</tr>
		<!-- END BLOCK : row -->
		<!-- END BLOCK : cia -->
	</tbody>
	<tfoot>
		<tr>
			<td colspan="7">&nbsp;</td>
		</tr>
	</tfoot>
</table>
<p>
	<button type="button" id="regresar">
		Regresar
	</button>
</p>
