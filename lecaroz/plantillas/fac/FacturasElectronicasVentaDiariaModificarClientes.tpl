<!-- START BLOCK : result -->
<form name="Clientes" class="FormValidator FormStyles" id="Clientes">
	<input name="periodo" type="hidden" id="periodo" value="{periodo}" />
	<table class="tabla_captura">
		<tr>
			<th scope="col">Fecha de<br />
				emisi&oacute;n</th>
			<th scope="col">Fecha <br />
				de pago</th>
			<th scope="col">Folio</th>
			<th scope="col">Cliente</th>
			<th scope="col">RFC</th>
			<th scope="col">Importe</th>
			<th scope="col">I.V.A.</th>
			<th scope="col">Importe</th>
		</tr>
		<!-- START BLOCK : row -->
		<tr class="linea_{row_color}">
			<td align="center"><input name="id[]" type="hidden" id="id" value="{id}" />
			{fecha}</td>
			<td align="center"><input name="fecha_pago[]" type="text" class="valid Focus toDate center" id="fecha_pago" value="{fecha_pago}" size="10" maxlength="10" /></td>
			<td align="right">{folio}</td>
			<td>{nombre_cliente}</td>
			<td>{rfc}</td>
			<td align="right" class="green">{importe}</td>
			<td align="right" class="red">{iva}</td>
			<td align="right" class="blue">{total}</td>
		</tr>
		<!-- END BLOCK : row -->
		<tr>
			<th colspan="7" align="right">Total</th>
			<th align="right">{total}</th>
		</tr>
	</table>
	<p>
		<input type="button" name="cancelar" id="cancelar" value="Cancelar" />
	&nbsp;&nbsp;
	<input type="button" name="aplicar" id="aplicar" value="Aplicar cambios" />
	</p>
</form>
<!-- END BLOCK : result -->
<!-- START BLOCK : no_result -->
<input type="button" name="cancelar" id="cancelar" value="Cancelar" />
<!-- END BLOCK : no_result -->