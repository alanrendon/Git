<form name="Datos" id="Datos" class="FormValidator FormStyles">
	<table class="tabla_captura">
		<tr class="linea_off">
			<th align="left" scope="row">Producto</th>
			<td class="font12 bold">{codmp} {nombre_mp}</td>
		</tr>
		<tr class="linea_on">
			<th align="left" scope="row">Pedir para el d&iacute;a</th>
			<td class="font12 bold">{fecha}</td>
		</tr>
	</table>
	<p class="bold font12">Paso 2. Distribuci&oacute;n de pedidos entre proveedores</p>
	<table class="tabla_captura">
		<tr>
			<th><input name="checkall" type="checkbox" id="checkall" checked="checked" /></th>
			<th colspan="2">Compañía</th>
			<th colspan="2">Pedido</th>
			<th colspan="2">Entregar</th>
			<th>Precio</th>
			<th>Costo<br>(sin iva)</th>
			<th colspan="2">Proveedor</th>
		</tr>
		<!-- START BLOCK : row -->
		<tr class="linea_{row_color}" id="row">
			<td align="center"><input name="pedido[]" type="checkbox" id="pedido" value="{datos_pedido}" num_cia="{num_cia}"{disabled} checked="checked" /></td>
			<td align="right">{num_cia}</td>
			<td>{nombre_cia}</td>
			<td align="right" class="green">{pedido}</td>
			<td class="green">{unidad}</td>
			<td align="right" class="orange bold">{entregar}</td>
			<td class="orange">{presentacion}</td>
			<td align="right" class="red">{precio}</td>
			<td align="right" class="red bold">{costo}</td>
			<td align="right" class="blue">{num_pro}</td>
			<td class="blue">{nombre_pro}</td>
		</tr>
		<!-- END BLOCK : row -->
		<tr>
			<td colspan="11">&nbsp;</td>
		</tr>
	</table>
	<p>
		<input type="button" name="cancelar" id="cancelar" value="Cancelar" />
	&nbsp;&nbsp;
	<input type="button" name="registrar" id="registrar" value="Realizar pedidos" />
	</p>
</form>