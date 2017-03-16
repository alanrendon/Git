<div style="max-width:900px;max-height:600px;overflow:auto;">
	<table class="table" align="center">
		<thead>
			<tr>
				<th>Compa&ntilde;&iacute;a</th>
				<th>Proveedor</th>
				<th>Factura</th>
				<th>Fecha</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td>{num_cia} {nombre_cia}</td>
				<td>{num_pro} {nombre_pro}</td>
				<td>{num_fact}</td>
				<td>{fecha}</td>
			</tr>
		</tbody>
		<tfoot>
			<tr>
				<td colspan="4" align="center">&nbsp;</td>
			</tr>
		</tfoot>
	</table>
	<br />
	<table class="table" align="center">
		<thead>
			<tr>
				<th>Producto</th>
				<th>Cantidad</th>
				<th>Kilos</th>
				<th>Precio</th>
				<th>Total</th>
			</tr>
		</thead>
		<tbody>
			<!-- START BLOCK : row -->
			<tr>
				<td>{codmp} {nombre_mp}</td>
				<td align="right" class="blue">{cantidad}</td>
				<td align="right" class="orange">{kilos}</td>
				<td align="right" class="purple">{precio}</td>
				<td align="right" class="bold green">{total}</td>
			</tr>
			<!-- END BLOCK : row -->
		</tbody>
		<tfoot>
			<tr>
				<td colspan="4" class="bold font12" align="right">Total</td>
				<td class="bold font12" align="right">{total}</td>
			</tr>
		</tfoot>
	</table>
</div>
