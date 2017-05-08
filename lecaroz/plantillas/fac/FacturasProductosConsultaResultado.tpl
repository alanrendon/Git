<table class="table">
	<thead>
		<tr>
			<th colspan="20" align="left" scope="col">&nbsp;</th>
		</tr>
	</thead>
	<tbody>
		<!-- START BLOCK : mp -->
		<tr>
			<th colspan="20" align="left" class="font14" scope="col">{codmp} {producto}</th>
		</tr>
		<tr>
			<th>Proveedor</th>
			<th>Factura</th>
			<th>Fecha</th>
			<th>Compa&ntilde;&iacute;a</th>
			<th>Cantidad</th>
			<th>Contenido</th>
			<th>Unidad</th>
			<th>Precio</th>
			<th>Importe</th>
			<th>Costales</th>
			<th>Desc. 1</th>
			<th>Desc. 2</th>
			<th>Desc. 3</th>
			<th>I.V.A.</th>
			<th>I.E.P.S.</th>
			<th>Total</th>
			<th>Pagado</th>
			<th>Banco</th>
			<th>Folio</th>
			<th>Cobrado</th>
		</tr>
		<!-- START BLOCK : row -->
		<tr id="row{id}">
			<td>{num_pro} {nombre_pro}</td>
			<td>{num_fact}</td>
			<td align="center" class="orange">{fecha}</td>
			<td>{num_cia} {nombre_cia}</td>
			<td align="right" class="green"><strong>{cantidad}</strong></td>
			<td align="right" class="blue"><strong>{contenido}</strong></td>
			<td>{unidad}</td>
			<td align="right" class="green">{precio}</td>
			<td align="right" class="red">{importe}</td>
			<td align="right" class="blue">{costales}</td>
			<td align="right" class="blue">{desc1}</td>
			<td align="right" class="blue">{desc2}</td>
			<td align="right" class="blue">{desc3}</td>
			<td align="right red">{iva}</td>
			<td align="right red">{ieps}</td>
			<td align="right" class="red"><strong>{total}</strong></td>
			<td align="center" class="green">{fecha_pago}</td>
			<td align="center">{banco}</td>
			<td align="right">{folio}</td>
			<td align="center" class="blue">{fecha_cobro}</td>
		</tr>
		<!-- END BLOCK : row -->
		<tr>
			<th colspan="4" align="right">Totales</th>
			<th align="right" class="font12">{cantidad}</th>
			<th colspan="3" align="right">&nbsp;</th>
			<th align="right" class="font12">{importe}</th>
			<th align="right" class="font12">{costales}</th>
			<th align="right" class="font12">{desc1}</th>
			<th align="right" class="font12">{desc2}</th>
			<th align="right" class="font12">{desc3}</th>
			<th align="right" class="font12">{iva}</th>
			<th align="right" class="font12">{ieps}</th>
			<th align="right" class="font12">{total}</th>
			<th colspan="4" class="font12">&nbsp;</th>
		</tr>
		<tr>
			<td colspan="20" align="center">&nbsp;</td>
		</tr>
		<!-- END BLOCK : mp -->
	</tbody>
	<tfoot>
		<tr>
			<td colspan="20" align="center">&nbsp;</td>
		</tr>
	</tfoot>
</table>
<p>
	<input type="button" name="regresar" id="regresar" value="Regresar" />
</p>
