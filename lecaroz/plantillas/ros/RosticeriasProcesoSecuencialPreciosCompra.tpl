<form action="" method="post" name="precios_compra_form" class="FormValidator" id="precios_compra_form" style="height:400px; overflow:auto;">
	<table align="center" class="table">
		<thead>
			<tr>
				<th>Producto</th>
				<th>Proveedor</th>
				<th>Precio</th>
				<th><img src="/lecaroz/iconos/plus_round.png" id="agregar_precio_compra" class="icono" width="16" height="16" /></th>
			</tr>
		</thead>
		<tbody>
			<!-- START BLOCK : precio_compra -->
			<tr>
				<td>{codmp} {nombre_mp}</td>
				<td>{num_pro} {nombre_pro}</td>
				<td>
					<input name="precio_compra_data[]" type="hidden" id="precio_compra_data" value="{data}" />
					<input name="precio_compra[]" type="text" class="validate focus numberPosFormat right" precision="4" id="precio_compra" size="10" value="{precio_compra}" />
				</td>
				<td><img src="/lecaroz/iconos/accept_green.png" id="borrar_precio_compra" data-index="{i}" class="icono" width="16" height="16" /></td>
			</tr>
			<!-- END BLOCK : precio_compra -->
		</tbody>
		<tfoot>
			<tr>
				<td colspan="4" scope="row">&nbsp;</td>
			</tr>
		</tfoot>
	</table>
</form>
