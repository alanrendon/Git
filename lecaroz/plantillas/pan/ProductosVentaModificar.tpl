<form name="modificar_producto" class="FormValidator" id="modificar_producto">
	<input type="hidden", name="id_producto_venta" id="id_producto_venta" value="{id_producto_venta}" />
	<table class="table">
		<thead>
			<tr>
				<th colspan="2" scope="col">&nbsp;</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td class="bold">Compa&ntilde;&iacute;a</td>
				<td><input name="num_cia" type="text" class="validate focus toPosInt right" id="num_cia" size="3" value="{num_cia}" readonly="readonly" /><input name="nombre_cia" type="text" id="nombre_cia" size="30" value="{nombre_cia}" disabled="disabled" /></td>
			</tr>
			<tr>
				<td class="bold">Producto</td>
				<td><input name="cod_producto" type="text" class="validate focus toPosInt right" id="cod_producto" size="3" value="{cod_producto}" readonly="readonly" /><input name="nombre_pro" type="text" id="nombre_pro" size="30" value="{nombre_pro}" readonly="readonly" /></td>
			</tr>
			<tr>
				<td class="bold">Precio de venta</td>
				<td><input name="precio_venta" type="text" class="validate focus numberPosFormat right" precision="2" id="precio_venta" size="10" value="{precio_venta}"{readonly} /></td>
			</tr>
			<tr>
				<td class="bold">Acepta decimales</td>
				<td>
					<input type="radio" name="decimales" id="decimales_0" value="0"{decimales_0} />No
					<input type="radio" name="decimales" id="decimales_1" value="1"{decimales_1} />Si
				</td>
			</tr>
			<tr>
				<td class="bold">Venta m&aacute;xima</td>
				<td><input name="venta_maxima" type="text" class="validate focus numberPosFormat right" precision="2" id="venta_maxima" size="10" value="{venta_maxima}" /></td>
			</tr>
			<tr>
				<td class="bold">Orden</td>
				<td><input name="orden" type="text" class="validate focus toPosInt right" id="orden" size="5" value="{orden}" /></td>
			</tr>
		</tbody>
		<tfoot>
			<tr>
				<td colspan="2">&nbsp;</td>
			</tr>
		</tfoot>
	</table>
	<p>
		<input type="button" name="cancelar" id="cancelar" value="Cancelar" />
		&nbsp;&nbsp;
		<input type="button" name="modificar" id="modificar" value="Modificar" />
	</p>
</form>
