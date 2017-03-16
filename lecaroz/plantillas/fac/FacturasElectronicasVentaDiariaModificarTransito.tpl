<!-- START BLOCK : result -->
<form name="Transito" class="FormValidator FormStyles" id="Transito">
	<input name="num_cia" type="hidden" id="num_cia" value="{num_cia}" />
	<table class="tabla_captura">
		<tr>
			<th scope="col"><img src="/lecaroz/iconos/cancel_round.png" title="Los elementos seleccionados serán borrados" width="16" height="16" /></th>
			<th scope="col">Fecha</th>
			<th scope="col">Cliente</th>
			<th scope="col">RFC</th>
			<th scope="col">Importe</th>
			<th scope="col">I.V.A.</th>
			<th scope="col">Total</th>
		</tr>
		<!-- START BLOCK : row -->
		<tr class="linea_{row_color}">
			<td align="center"><input name="folio[]" type="hidden" id="folio" value="{folio}" />				<input name="del[]" type="checkbox" id="del" value="{folio}" /></td>
			<td align="center"><input name="fecha[]" type="text" class="valid Focus toDate center" id="fecha" value="{fecha}" size="10" maxlength="10" /></td>
			<td>{nombre_cliente}</td>
			<td>{rfc}</td>
			<td align="right" class="green">{importe}</td>
			<td align="right" class="red">{iva}</td>
			<td align="right" class="blue">{total}</td>
		</tr>
		<!-- END BLOCK : row -->
		<tr>
			<th colspan="6" align="right">Total</th>
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