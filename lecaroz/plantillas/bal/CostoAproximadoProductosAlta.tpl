<form name="alta_producto" class="FormValidator" id="alta_producto">
	<table class="table">
		<thead>
			<tr>
				<th colspan="2">&nbsp;</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td align="left" class="bold font12">Nombre producto</td>
				<td align="left" class="bold font12">
					<input name="nombre_producto" type="text" id="nombre_producto" class="validate toText cleanText toUpper" value="" size="30">
				</td>
			</tr>
		</tbody>
		<tfoot>
			<tr>
				<td colspan="2">&nbsp;</td>
			</tr>
		</tfoot>
	</table>
	<br />
	<table class="table" id="captura_table">
		<thead>
			<tr>
				<th>Cantidad</th>
				<th>Producto</th>
				<th>Unidad</th>
				<th>Costo promedio</th>
				<th>Costo consumo</th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td class="bold right" colspan="4">Total consumo</td>
				<td>
					<input name="total_consumo" type="text" class="bold right green font12" id="total_consumo" size="8" value="0.00" readonly="readonly">
				</td>
			</tr>
			<tr>
				<td class="bold right" colspan="4">Raya (<input name="porc_raya" type="text" class="validate focus toPosFloat bold right orange" id="porc_raya" value="0.00" size="5">%)</td>
				<td>
					<input name="raya" type="text" class="bold right red font12" id="raya" size="8" value="0.00" readonly="readonly">
				</td>
			</tr>
			<tr>
				<td class="bold right" colspan="4">Costo total</td>
				<td>
					<input name="costo_total" type="text" class="bold right blue font12" id="costo_total" size="8" value="0.00" readonly="readonly">
				</td>
			</tr>
		</tfoot>
		<tbody>
		</tbody>
	</table>
	<p>
		<input type="button" name="cancelar" id="cancelar" value="Cancelar" />
		&nbsp;&nbsp;
		<input type="button" name="alta" id="alta" value="Alta" />
	</p>
</form>
