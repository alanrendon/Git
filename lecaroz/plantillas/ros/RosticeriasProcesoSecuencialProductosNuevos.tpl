<p class="font12 bold">Los siguientes productos no estan en el inventario o no tienen precio de venta.</p>
<table class="table">
	<thead>
		<tr>
			<th colspan="2" scope="row">&nbsp;</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td class="bold font12">Compa&ntilde;&iacute;a</td>
			<td class="bold font12">{num_cia} {nombre_cia}</td>
		</tr>
		<tr>
			<td class="bold font12">Fecha</td>
			<td class="bold font12">{fecha}</td>
		</tr>
	</tbody>
	<tfoot>
		<tr>
			<td colspan="2" scope="row">&nbsp;</td>
		</tr>
	</tfoot>
</table>
<br />
<table class="table">
	<thead>
		<tr>
			<th scope="row">Producto</th>
			<th scope="row">Precio<br />de venta</th>
		</tr>
	</thead>
	<tbody>
		<!-- START BLOCK : row -->
		<tr>
			<td>{codmp} {nombremp}</td>
			<td class="right">{precio_venta}</td>
		</tr>
		<!-- END BLOCK : row -->
	</tbody>
	<tfoot>
		<tr>
			<td colspan="2" scope="row">&nbsp;</td>
		</tr>
	</tfoot>
</table>
<p>
	<input type="button" name="cancelar" id="cancelar" value="Cancelar" />
</p>
