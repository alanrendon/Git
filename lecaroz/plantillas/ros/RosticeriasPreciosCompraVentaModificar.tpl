<table class="table">
	<thead>
		<tr>
			<th colspan="2">&nbsp;</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td align="left" class="bold font12">Compa&ntilde;&iacute;a</td>
			<td align="left" class="bold font12">{num_cia} {nombre_cia}</td>
		</tr>
	</tbody>
	<tfoot>
		<tr>
			<td colspan="2">&nbsp;</td>
		</tr>
	</tfoot>
</table>
<br />
<form name="modificar_producto" class="FormValidator" id="modificar_producto">
	<input name="id" type="hidden" name="id" value="{id}" />
	<input name="num_cia" type="hidden" id="num_cia" value="{num_cia}" />
	<table class="table">
		<thead>
			<tr>
				<th colspan="2" scope="col">&nbsp;</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td class="bold">Producto</td>
				<td>
					<input name="codmp" type="text" class="validate focus toPosInt right" id="codmp" size="3" value="{codmp}" />
					<input name="nombre_mp" type="text" id="nombre_mp" size="30" readonly="readonly" value="{nombre_mp}" />
				</td>
			</tr>
			<tr>
				<td class="bold">Nombre alternativo (alias)</td>
				<td>
					<input name="nombre_alt" type="text" class="validate toText cleanText toUpper" id="nombre_alt" size="40" maxlength="100" value="{nombre_alt}" />
				</td>
			</tr>
			<tr>
				<td class="bold">Proveedor</td>
				<td>
					<input name="num_pro" type="text" class="validate focus toPosInt right" id="num_pro" size="3" value="{num_pro}" />
					<input name="nombre_pro" type="text" id="nombre_pro" size="30" disabled="disabled" value="{nombre_pro}" />
				</td>
			</tr>
			<tr>
				<td class="bold">Precio compra</td>
				<td><input name="precio_compra" type="text" class="validate focus numberFormat right red" precision="4" id="precio_compra" size="10" value="{precio_compra}" /></td>
			</tr>
			<tr>
				<td class="bold">Precio venta</td>
				<td><input name="precio_venta" type="text" class="validate focus numberFormat right blue" precision="2" id="precio_venta" size="10" value="{precio_venta}" /></td>
			</tr>
			<tr>
				<td class="bold">Con decimales para punto de venta</td>
				<td>
					<input name="decimales" type="radio" id="decimales_f" value="FALSE"{decimales_f}> No
					<input name="decimales" type="radio" id="decimales_t" value="TRUE"{decimales_t}> Si
				</td>
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
