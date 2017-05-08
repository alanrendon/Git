<table class="tabla_captura">
  <tr>
    <th colspan="12" align="left" scope="col" class="font8"><input name="checkall" type="checkbox" id="checkall" />
      Seleccionar todos </th>
  </tr>
  <!-- START BLOCK : emisor -->
  <tr>
	<th colspan="13" align="left" scope="col" class="font12">{emisor} {nombre_emisor} </th>
  </tr>
  <tr>
	<th><input name="checkemisor[]" type="checkbox" id="checkemisor" value="{emisor}" /></th>
	<th>Folio</th>
	<th>Fecha</th>
	<th>Pagada</th>
	<th>Cliente</th>
	<th>Importe</th>
	<th>Descuento</th>
	<th>I.E.P.S.</th>
	<th>I.V.A.</th>
	<th>Retenci&oacute;n I.V.A.</th>
	<th>Retenci&oacute;n I.S.R.</th>
	<th>Total</th>
	<th><img src="imagenes/tool16x16.png" width="16" height="16" /></th>
  </tr>
  <!-- START BLOCK : factura -->
  <tr class="{color}" id="row_{id}">
	<td align="center"><input name="id" type="checkbox" id="id" value="{id}" emisor="{emisor}" /></td>
	<td align="right">{folio}</td>
	<td align="center" class="blue">{fecha}</td>
	<td align="center" class="orange">{fecha_pago}</td>
	<td>{nombre_cliente}</td>
	<td align="right" class="green">{importe}</td>
	<td align="right" class="orange">{descuento}</td>
	<td align="right" class="red">{ieps}</td>
	<td align="right" class="red">{iva}</td>
	<td align="right" class="purple">{retencion_iva}</td>
	<td align="right" class="purple">{retencion_isr}</td>
	<td align="right" class="blue">{total}</td>
	<td align="center"><img src="/lecaroz/iconos/magnify.png" alt="{id}" name="visualizar" width="16" height="16" id="visualizar" />&nbsp;&nbsp;<img src="/lecaroz/iconos/printer.png" alt="{id}" name="imprimir" width="16" height="15" id="imprimir" />&nbsp;&nbsp;<img src="/lecaroz/iconos/download.png" alt="{id}" name="descargar" width="16" height="16" id="descargar" />&nbsp;&nbsp;<img src="/lecaroz/iconos/envelope.png" alt="{id}" name="email" width="16" height="16" id="email" />&nbsp;&nbsp;<img src="/lecaroz/iconos/{refresh_icon}.png" alt="{id}" name="reimpresion" width="16" height="16" id="reimpresion" />&nbsp;&nbsp;<img src="/lecaroz/iconos/{cancel_icon}.png" alt="{id}" name="cancelar" width="16" height="16" id="cancelar" /></td>
  </tr>
  <!-- END BLOCK : factura -->
  <tr>
	<th colspan="5" align="right">Totales</th>
	<th align="right" class="font12">{importe}</th>
	<th align="right" class="font12">{descuento}</th>
	<th align="right" class="font12">{ieps}</th>
	<th align="right" class="font12">{iva}</th>
	<th align="right" class="font12">{retencion_iva}</th>
	<th align="right" class="font12">{retencion_isr}</th>
	<th align="right" class="font12">{total}</th>
	<th>&nbsp;</th>
  </tr>
  <tr>
	<td colspan="13">&nbsp;</td>
  </tr>
  <!-- END BLOCK : emisor -->
</table>
<p>
  <input name="regresar" type="button" id="regresar" value="Regresar" />
&nbsp;&nbsp;
<input name="imprimir_seleccion" type="button" id="imprimir_seleccion" value="Imprimir selecci&oacute;n" />
&nbsp;&nbsp;
<input name="descargar_seleccion" type="button" id="descargar_seleccion" value="Descargar selecci&oacute;n" />
&nbsp;&nbsp;
<input name="reporte_seleccion" type="button" id="reporte_seleccion" value="Consultar reporte" />
&nbsp;&nbsp;
<input name="csv_seleccion" type="button" id="csv_seleccion" value="Descargar reporte" />
</p>
