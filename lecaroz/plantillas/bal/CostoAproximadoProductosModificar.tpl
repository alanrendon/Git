<form name="modificar_producto" class="FormValidator" id="modificar_producto">
	<input name="id" type="hidden" id="id" value="{id}">
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
					<input name="nombre_producto" type="text" id="nombre_producto" class="validate toText cleanText toUpper" value="{nombre_producto}" size="30">
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
					<input name="total_consumo" type="text" class="bold right green font12" id="total_consumo" size="8" value="{total_consumo}" readonly="readonly">
				</td>
			</tr>
			<tr>
				<td class="bold right" colspan="4">Raya (<input name="porc_raya" type="text" class="validate focus toPosFloat bold right orange" id="porc_raya" value="{porc_raya}" size="5">%)</td>
				<td>
					<input name="raya" type="text" class="bold right red font12" id="raya" size="8" value="{raya}" readonly="readonly">
				</td>
			</tr>
			<tr>
				<td class="bold right" colspan="4">Costo total</td>
				<td>
					<input name="costo_total" type="text" class="bold right blue font12" id="costo_total" size="8" value="{costo_total}" readonly="readonly">
				</td>
			</tr>
		</tfoot>
		<tbody>
			<!-- START BLOCK : row -->
			<tr>
				<td>
					<input name="row_id[]" type="hidden" id="row_id_{i}" value="{row_id}">
					<input name="cantidad[]" type="text" id="cantidad_{i}" class="validate focus toFloat right" size="10" value="{cantidad}">
				</td>
				<td>
					<input name="codmp[]" type="text" id="codmp_{i}" class="validate focus toPosInt right" size="3" value="{codmp}"><input name="nombre_mp[]" type="text" id="nombre_mp_{i}" size="20" value="{nombre_mp}" disabled="disabled">
				</td>
				<td>
					<input name="unidad[]" type="text" id="unidad_{i}" size="10" value="{unidad}" disabled="disabled">
				</td>
				<td>
					<input name="precio_unidad[]" type="text" id="precio_unidad_{i}" class="right green" size="10" value="{precio_unidad}" readonly="readonly">
				</td>
				<td>
					<input name="costo_consumo[]" type="text" id="costo_consumo_{i}" class="right red" size="10" value="{costo_consumo}" readonly="readonly">
				</td>
			</tr>
			<!-- END BLOCK : row -->
		</tbody>
	</table>
	<p>
		<input type="button" name="cancelar" id="cancelar" value="Cancelar" />
		&nbsp;&nbsp;
		<input type="button" name="modificar" id="modificar" value="Modificar" />
	</p>
</form>
