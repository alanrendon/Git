<table class="tabla_captura">
  <tr class="linea_off">
	<th align="left" class="font14" scope="row">A&ntilde;o</th>
	<td class="font14 bold">{anio}</td>
  </tr>
  <tr class="linea_on">
	<th align="left" class="font14" scope="row">Mes</th>
	<td class="font14 bold">{mes_escrito}</td>
  </tr>
</table>
<br />
<table class="tabla_captura">
  <!-- START BLOCK : emisor -->
  <tr>
	<th colspan="11" align="left" class="font14" scope="col">{emisor} {nombre_emisor}</th>
  </tr>
  <tr>
    <th>Folio</th>
	<th>Local</th>
	<th>Renta</th>
	<th>Mantenimiento</th>
	<th>Subtotal</th>
	<th>I.V.A.</th>
	<th>Agua</th>
	<th>Retenci&oacute;n<br>
    I.V.A.</th>
	<th>Retenci&oacute;n<br>
    I.S.R.</th>
	<th>Total</th>
	<th>Status</th>
  </tr>
  <!-- START BLOCK : row -->
  <tr id="row" class="linea_{color}">
    <td align="right">{folio}</td>
	<td>{local} {arrendatario}</td>
	<td align="right" class="blue">{renta}</td>
	<td align="right" class="green">{mantenimiento}</td>
	<td align="right" class="blue">{subtotal}</td>
	<td align="right" class="red">{iva}</td>
	<td align="right" class="blue">{agua}</td>
	<td align="right" class="red">{retencion_iva}</td>
	<td align="right" class="red">{retencion_isr}</td>
	<td align="right" class="blue bold">{total}</td>
	<td class="bold blue">{status}</td>
  </tr>
  <!-- END BLOCK : row -->
  <tr>
	<td colspan="11">&nbsp;</td>
  </tr>
  <!-- END BLOCK : emisor -->
</table>
<p>
  <input name="terminar" type="button" id="terminar" value="Terminar">
</p>
