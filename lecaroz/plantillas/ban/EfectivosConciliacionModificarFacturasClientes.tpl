<form action="" method="post" name="modificar_facturas_clientes" class="FormValidator" id="modificar_facturas_clientes">
	<table class="table">
		<thead>
			<tr>
				<th scope="col">Fecha de<br />
					emisión</th>
				<th scope="col">Fecha<br />
					de pago</th>
				<th scope="col">Factura</th>
				<th scope="col">Cliente</th>
				<th scope="col">R.F.C.</th>
				<th scope="col">Importe</th>
				<th scope="col">I.V.A.</th>
				<th scope="col">Total</th>
			</tr>
		</thead>
		<tbody>
			<!-- START BLOCK : factura -->
			<tr>
				<td align="center"><input name="idfactura[]" type="hidden" id="idfactura" value="{id}" />
					{fecha_emision}</td>
				<td align="center"><input name="fecha_pago[]" type="text" class="validate focus toDate center" id="fecha_pago" value="{fecha_pago}" size="10" maxlength="10" /></td>
				<td align="right">{factura}</td>
				<td>{cliente}</td>
				<td>{rfc}</td>
				<td align="right" class="green">{importe}</td>
				<td align="right" class="orange">{iva}</td>
				<td align="right" class="blue">{total}</td>
			</tr>
			<!-- END BLOCK : factura -->
		</tbody>
		<tfoot>
			<tr class="bold">
				<td colspan="7" align="right">Total de facturas de clientes</td>
				<td align="right">{total}</td>
			</tr>
		</tfoot>
	</table>
</form>
