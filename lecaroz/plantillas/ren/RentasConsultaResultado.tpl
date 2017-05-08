<table class="tabla_captura">
	<tr>
		<th colspan="13" align="left" class="font6" scope="col"><input name="checkall" type="checkbox" id="checkall" checked>
			Seleccionar todo</th>
	</tr>
	<!-- START BLOCK : arrendador -->
	<tr>
		<th colspan="13" align="left" class="font12" scope="col">{arrendador} {nombre_arrendador}</th>
	</tr>
	<tr>
		<th><input name="checkarrendador[]" type="checkbox" id="checkarrendador" value="{arrendador}" checked="checked" /></th>
		<th>Recibo</th>
		<th>Arrendatario</th>
		<th>Renta</th>
		<th>Mantenimiento</th>
		<th>Subtotal</th>
		<th>I.V.A.</th>
		<th>Agua</th>
		<th>Retención<br />
		I.V.A.</th>
		<th>Retención<br />
			I.S.R.</th>
		<th>Total</th>
		<th>Pagado</th>
		<th><img src="/lecaroz/imagenes/tool16x16.png" width="16" height="16" /></th>
	</tr>
	<!-- START BLOCK : mes -->
	<tr>
		<th colspan="13" align="left" class="font12"><img src="/lecaroz/iconos/calendar.png" width="16" height="16" /> {mes} {anio}</th>
	</tr>
	<!-- START BLOCK : recibo -->
	<tr class="linea_{row_color}" id="row{id}">
		<td align="center" nowrap="nowrap"><input name="id[]" type="checkbox" id="id" value="{id}" arrendador="{arrendador}" checked="checked" /></td>
		<td align="right" nowrap="nowrap">{num_fact}</td>
		<td nowrap="nowrap">{arrendatario} {nombre_arrendatario}</td>
		<td align="right" nowrap="nowrap" class="green">{renta}</td>
		<td align="right" nowrap="nowrap" class="green">{mantenimiento}</td>
		<td align="right" nowrap="nowrap" class="blue bold">{subtotal}</td>
		<td align="right" nowrap="nowrap" class="blue">{iva}</td>
		<td align="right" nowrap="nowrap" class="green">{agua}</td>
		<td align="right" nowrap="nowrap" class="red">{retencion_iva}</td>
		<td align="right" nowrap="nowrap" class="red">{retencion_isr}</td>
		<td align="right" nowrap="nowrap" class="blue bold">{total}</td>
		<td align="center" nowrap="nowrap" class="green">{pagado}</td>
		<td align="center" nowrap="nowrap"><img src="/lecaroz/iconos/magnify.png" alt="{id}" name="visualizar" width="16" height="16" id="visualizar" />&nbsp;<img src="/lecaroz/iconos/printer.png" alt="{id}" name="imprimir" width="16" height="15" id="imprimir" />&nbsp;<img src="/lecaroz/iconos/download.png" alt="{id}" name="descargar" width="16" height="16" id="descargar" />&nbsp;<img src="/lecaroz/iconos/envelope.png" alt="{id}" name="email" width="16" height="16" id="email" />&nbsp;<img src="/lecaroz/iconos/{refresh_icon}.png" alt="{id}" name="reimpresion" width="16" height="16" id="reimpresion">&nbsp;<img src="/lecaroz/iconos/{cancel_icon}.png" alt="{id}" name="cancelar" width="16" height="16" id="cancelar" /></td>
	</tr>
	<!-- END BLOCK : recibo -->
	<tr>
		<th colspan="10" align="right">Total mes</th>
		<th align="right" class="font12">{total}</th>
		<th colspan="2">&nbsp;</th>
	</tr>
	<!-- END BLOCK : mes -->
	<tr>
		<th colspan="10" align="right">Total inmobiliaria</th>
		<th align="right" class="font12">{total}</th>
		<th colspan="2">&nbsp;</th>
	</tr>
	<tr>
		<td colspan="13">&nbsp;</td>
	</tr>
	<!-- END BLOCK : arrendador -->
</table>
<p>
	<input type="button" name="regresar" id="regresar" value="Regresar" />
&nbsp;&nbsp;
<input type="submit" name="reporte" id="reporte" value="Consultar reporte" />
&nbsp;&nbsp;
<input type="button" name="exportar" id="exportar" value="Descargar reporte" />
&nbsp;&nbsp;
<input type="button" name="imprimir_seleccion" id="imprimir_seleccion" value="Imprimir selección" />
&nbsp;&nbsp;
<input type="button" name="descargar_seleccion" id="descargar_seleccion" value="Descargar selección" />
</p>