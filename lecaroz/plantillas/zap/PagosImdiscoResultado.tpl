<table class="tabla_captura">
	<tr>
		<th scope="col"><input name="checkall" type="checkbox" id="checkall" value="1" checked="checked" /></th>
		<th scope="col">Compañía</th>
		<th scope="col">Fecha</th>
		<th scope="col">Pago</th>
		<th scope="col">Folio</th>
		<th scope="col">Importe</th>
		<th scope="col"><img src="/lecaroz/imagenes/tool16x16.png" width="16" height="16" /></th>
	</tr>
	<!-- START BLOCK : row -->
	<tr class="linea_{row_color}" id="row{id}">
		<td align="center"><input name="id[]" type="checkbox" id="id" value="{id}" checked="checked" /></td>
		<td>{num_cia} {nombre_cia}</td>
		<td align="center">{fecha}</td>
		<td align="center" id="fecha_pago{id}">{fecha_pago}</td>
		<td align="right" id="folio{id}">{folio}</td>
		<td align="right" id="importe{id}">{importe}</td>
		<td align="center"><img src="/lecaroz/iconos/calendar.png" alt="{id}" name="pagar" width="16" height="16" id="pagar" />&nbsp;<img src="/lecaroz/iconos/cancel_round.png" alt="{id}" name="baja" width="16" height="16" id="baja" /></td>
	</tr>
	<!-- END BLOCK : row -->
	<tr>
		<th colspan="5" align="right">Total</th>
		<th align="right" id="total">{total}</th>
		<th>&nbsp;</th>
	</tr>
</table>
<p>
	<input type="button" name="regresar" id="regresar" value="Regresar" />
	&nbsp;&nbsp;
	<input type="button" name="baja_seleccion" id="baja_seleccion" value="Borrar seleccionados" />
	&nbsp;&nbsp;
	<input type="button" name="pagar_seleccion" id="pagar_seleccion" value="Pagar seleccionados" />
</p>
