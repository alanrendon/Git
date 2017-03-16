<table class="table">
	<thead>
		<tr>
			<th colspan="2">&nbsp;</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td class="bold font12">Fecha de corte</td>
			<td class="bold font12">{fecha_corte}</td>
		</tr>
	</tbody>
	<tfoot>
		<tr>
			<td colspan="2">&nbsp;</td>
		</tr>
	</tfoot>
</table>
<br />
<table class="table">
	<thead>
		<tr>
			<th colspan="5">&nbsp;</th>
		</tr>
	</thead>
	<tbody>
		<!-- START BLOCK : cia -->
		<tr>
			<th class="font12" align="left" colspan="5">{num_cia} {nombre_cia}</th>
		</tr>
		<tr>
			<th>D&iacute;a</th>
			<th>Efectivo</th>
			<th>Clientes</th>
			<th>Ventas</th>
			<th>Estatus</th>
		</tr>
		<!-- START BLOCK : obs -->
		<tr>
			<tr>
				<td colspan="5" class="bold red">{obs}</td>
			</tr>
		</tr>
		<!-- END BLOCK : obs -->
		<!-- START BLOCK : row -->
		<tr>
			<td class="bold" align="right">{dia}</td>
			<td align="right" class="bold blue">{efectivo}</td>
			<td align="right" class="bold green">{clientes}</td>
			<td align="right" class="bold orange">{venta}</td>
			<td class="bold">{estatus}</td>
		</tr>
		<!-- END BLOCK : row -->
		<tr>
			<th align="right">Totales</th>
			<th align="right">{efectivo}</th>
			<th align="right">{clientes}</th>
			<th align="right">{venta}</th>
			<th>&nbsp;</th>
		</tr>
		<tr>
			<td colspan="5">&nbsp;</td>
		</tr>
		<!-- END BLOCK : cia -->
	</tbody>
	<tfoot>
		<tr>
			<th colspan="5">&nbsp;</th>
		</tr>
	</tfoot>
</table>
<p>
	<button type="button" id="regresar">Regresar</button>
</p>
