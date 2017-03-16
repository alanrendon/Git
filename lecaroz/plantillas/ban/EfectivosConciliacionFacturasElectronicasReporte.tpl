<div style="width:500px; height:300px; overflow:auto;">
	<table align="center" class="table">
		<thead>
			<tr>
				<th colspan="4" scope="col">&nbsp;</th>
			</tr>
		</thead>
		<tbody>
			<!-- START BLOCK : emisor -->
			<tr>
				<th colspan="4" align="left" class="font12" scope="col">{num_cia} {nombre_cia}</th>
			</tr>
			<tr>
				<th>Factura</th>
				<th>Fecha</th>
				<th>Estatus</th>
				<th>Importe</th>
			</tr>
			<!-- START BLOCK : row -->
			<tr>
				<td align="right">{factura}</td>
				<td align="center">{fecha}</td>
				<td>{estatus}</td>
				<td align="right">{importe}</td>
			</tr>
			<!-- END BLOCK : row -->
			<tr>
				<th colspan="3" align="right">Total</th>
				<th align="right">{total}</th>
			</tr>
			<tr>
				<td colspan="4">&nbsp;</td>
			</tr>
			<!-- END BLOCK : emisor -->
		</tbody>
		<tfoot>
			<tr>
				<td colspan="4">&nbsp;</td>
			</tr>
		</tfoot>
	</table>
</div>
