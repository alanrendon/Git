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
	<!-- START BLOCK : mp -->
	<table class="table" align="center">
		<thead>
			<tr>
				<th>Producto</th>
				<th>Cantidad</th>
				<th>Contenido</th>
				<th>Unidad</th>
				<th>Precio</th>
				<th>Importe</th>
				<th>Desc. 1</th>
				<th>Desc. 2</th>
				<th>Desc. 3</th>
				<th>I.V.A.</th>
				<th>I.E.P.S.</th>
				<th>Total</th>
			</tr>
		</thead>
		<tbody>
			<!-- START BLOCK : row_mp -->
			<tr>
				<td>{codmp} {nombre_mp}</td>
				<td align="right" class="blue">{cantidad}</td>
				<td align="right" class="orange">{contenido}</td>
				<td>{unidad}</td>
				<td align="right" class="orange">{precio}</td>
				<td align="right" class="green">{importe}</td>
				<td align="right" class="blue">{desc1}</td>
				<td align="right" class="blue">{desc2}</td>
				<td align="right" class="blue">{desc3}</td>
				<td align="right" class="red">{iva}</td>
				<td align="right" class="red">{ieps}</td>
				<td align="right" class="bold green">{total}</td>
			</tr>
			<!-- END BLOCK : row_mp -->
		</tbody>
		<tfoot>
			<tr>
				<td colspan="5" class="bold font12" align="right">Total</td>
				<td class="bold font12" align="right">{importe}</td>
				<td class="bold font12" align="right">{desc1}</td>
				<td class="bold font12" align="right">{desc2}</td>
				<td class="bold font12" align="right">{desc3}</td>
				<td class="bold font12" align="right">{iva}</td>
				<td class="bold font12" align="right">{ieps}</td>
				<td class="bold font12" align="right">{total}</td>
			</tr>
		</tfoot>
	</table>
	<!-- END BLOCK : mp -->
	<!-- START BLOCK : gas -->
	<table class="table" align="center">
		<thead>
			<tr>
				<th>Litros</th>
				<th>Precio<br />x litro</th>
				<th>Importe</th>
				<th>I.V.A.</th>
				<th>Total</th>
			</tr>
		</thead>
		<tbody>
			<!-- START BLOCK : row_gas -->
			<tr>
				<td align="right" class="blue">{litros}</td>
				<td align="right" class="orange">{precio}</td>
				<td align="right" class="green">{importe}</td>
				<td align="right" class="red">{iva}</td>
				<td align="right" class="bold green">{total}</td>
			</tr>
			<!-- END BLOCK : row_gas -->
		</tbody>
		<tfoot>
			<tr>
				<td colspan="2" class="bold font12" align="right">Total</td>
				<td class="bold font12" align="right">{importe}</td>
				<td class="bold font12" align="right">{iva}</td>
				<td class="bold font12" align="right">{total}</td>
			</tr>
		</tfoot>
	</table>
	<!-- END BLOCK : gas -->
	<!-- START BLOCK : pollos -->
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
			<!-- START BLOCK : row_pollos -->
			<tr>
				<td>{codmp} {nombre_mp}</td>
				<td align="right" class="blue">{cantidad}</td>
				<td align="right" class="orange">{kilos}</td>
				<td align="right" class="orange">{precio}</td>
				<td align="right" class="bold green">{total}</td>
			</tr>
			<!-- END BLOCK : row_pollos -->
		</tbody>
		<tfoot>
			<tr>
				<td colspan="4" class="bold font12" align="right">Total</td>
				<td class="bold font12" align="right">{total}</td>
			</tr>
		</tfoot>
	</table>
	<!-- END BLOCK : pollos -->
</div>
