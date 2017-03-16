<table class="tabla_captura">
  <!-- START BLOCK : emisor -->
  <tr>
	<th colspan="4" align="left" class="font14" scope="col">{num_cia} {nombre_cia}</th>
  </tr>
  <tr>
    <th>Folio</th>
	<th>Fecha</th>
	<th>Status</th>
	<th>Importe</th>
  </tr>
  <!-- START BLOCK : row -->
  <tr id="row">
    <td align="right">{folio}</td>
	<td align="center">{fecha}</td>
	<td>{status}</td>
	<td align="right">{importe}</td>
  </tr>
  <!-- END BLOCK : row -->
  <tr>
	<th colspan="3" align="right">Total</th>
	<th align="right" class="font12">{total}</th>
  </tr>
  <tr>
	<td colspan="4">&nbsp;</td>
  </tr>
  <!-- END BLOCK : emisor -->
</table>
<p>
  <input name="terminar" type="button" id="terminar" value="Terminar">
</p>
