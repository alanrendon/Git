<table class="table">
	<thead>
		<tr>&nbsp;</tr>
	</thead>
	<tbody>
		<!-- START BLOCK : pro -->
		<tr>
			<th colspan="16" align="left" class="font14" scope="col">{num_cia} {nombre_cia} [{rfc_cia}]</th>
		</tr>
		<tr>
			<th>Factura</th>
			<th>Fecha</th>
			<th>Proveedor</th>
			<th>Concepto</th>
			<th>Gasto</th>
			<th>Importe</th>
			<th>Descuentos</th>
			<th>I.V.A.</th>
			<th>I.E.P.S.</th>
			<th>Ret. I.V.A.</th>
			<th>Ret. I.S.R.</th>
			<th>Total</th>
			<th>Pagado</th>
			<th>Banco</th>
			<th>Folio</th>
			<th>Cobrado</th>
		</tr>
		<!-- START BLOCK : row -->
		<tr id="row{id}">
			<td>{num_fact}</td>
			<td align="center" class="orange">{fecha}</td>
			<td>{num_pro} {nombre_pro}</td>
			<td>{concepto}</td>
			<td>{gasto} {nombre_gasto}</td>
			<td align="right" class="green">{importe}</td>
			<td align="right" class="blue">{descuentos}</td>
			<td align="right" class="orange">{iva}</td>
			<td align="right" class="orange">{ieps}</td>
			<td align="right" class="orange">{ret_iva}</td>
			<td align="right" class="orange">{ret_isr}</td>
			<td align="right" class="green bold">{total}</td>
			<td align="center" class="green">{fecha_pago}</td>
			<td align="center">{banco}</td>
			<td align="right">{folio}</td>
			<td align="center" class="blue">{fecha_cobro}</td>
		</tr>
		<!-- END BLOCK : row -->
		<tr>
			<th colspan="5" align="right">Total</th>
			<th align="right" class="font12">{importe}</th>
			<th align="right" class="font12">{descuentos}</th>
			<th align="right" class="font12">{iva}</th>
			<th align="right" class="font12">{ieps}</th>
			<th align="right" class="font12">{ret_iva}</th>
			<th align="right" class="font12">{ret_isr}</th>
			<th align="right" class="font12">{total}</th>
			<th colspan="4" align="right">&nbsp;</th>
		</tr>
		<tr>
			<td colspan="16" align="center">&nbsp;</td>
		</tr>
		<!-- END BLOCK : pro -->
	</tbody>
	<tfoot>
		<tr>
			<th colspan="5" align="right">Total general</th>
			<th align="right" class="font12">{importe}</th>
			<th align="right" class="font12">{descuentos}</th>
			<th align="right" class="font12">{iva}</th>
			<th align="right" class="font12">{ieps}</th>
			<th align="right" class="font12">{ret_iva}</th>
			<th align="right" class="font12">{ret_isr}</th>
			<th align="right" class="font12">{total}</th>
			<th>&nbsp;</th>
		</tr>
	</tfoot>
</table>
<p>
	<button type="button" id="regresar">Regresar</button>
	<!-- &nbsp;&nbsp;
	<input type="button" name="listado" id="listado" value="Listado para imprimir" />
	&nbsp;&nbsp;
	<input type="button" name="exportar" id="exportar" value="Exportar a Excel" /> -->
</p>
