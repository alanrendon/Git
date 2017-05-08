<p class="bold font12">Paso 2. Distribuci&oacute;n de pedidos entre proveedores</p>
<form name="Datos" id="Datos" class="FormValidator FormStyles">
	<input name="dias" type="hidden" id="dias" value="{dias}" />
	<input name="complemento" type="hidden" id="complemento" value="{complemento}" />
	<table class="tabla_captura">
		<tr>
			<th colspan="11" align="left" class="font8" scope="col"><input name="checkall" type="checkbox" id="checkall" checked="checked" />
			Seleccionar todo</th>
		</tr>
		<!-- START BLOCK : cia -->
		<tr>
			<th colspan="11" align="left" class="font14" scope="col">{num_cia} {nombre_cia}</th>
		</tr>
		<tr>
			<th><input name="checkblock" type="checkbox" id="checkblock" value="{num_cia}" checked="checked" /></th>
			<th colspan="2">Producto</th>
			<th colspan="2">Pedido</th>
			<th colspan="2">Entregar</th>
			<th>Precio</th>
			<th>Costo<br>(sin iva)</th>
			<th colspan="2">Proveedor</th>
		</tr>
		<!-- START BLOCK : pedido -->
		<tr class="linea_{row_color}" id="row">
			<td align="center"><input name="pedido[]" type="checkbox" id="pedido" value="{datos_pedido}" num_cia="{num_cia}"{disabled} checked="checked" /></td>
			<td align="right">{codmp}</td>
			<td>{nombre_mp}</td>
			<td align="right" class="green">{pedido}</td>
			<td class="green">{unidad}</td>
			<td align="right" class="orange bold">{entregar}</td>
			<td class="orange">{presentacion}</td>
			<td align="right" class="red">{precio}</td>
			<td align="right" class="red bold">{costo}</td>
			<td align="right" class="blue">{num_pro}</td>
			<td class="blue">{nombre_pro}</td>
		</tr>
		<!-- END BLOCK : pedido -->
		<tr>
			<td colspan="11">&nbsp;</td>
		</tr>
		<!-- END BLOCK : cia -->
	</table>
	<p class="red bold font12">NOTA: El mínimo a pedir de azucar de primera y segunda es de 5 bultos.</p>
	<p>
		<input type="button" name="cancelar" id="cancelar" value="Cancelar" />
	&nbsp;&nbsp;
	<input type="button" name="siguiente" id="siguiente" value="Realizar pedidos" />
	</p>
</form>