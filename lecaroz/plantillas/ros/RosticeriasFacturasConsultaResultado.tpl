<table class="table">
	<thead>
		<tr>
			<th colspan="10" align="left" scope="col">&nbsp;</th>
		</tr>
	</thead>
	<tbody>
		<!-- START BLOCK : pro -->
		<tr>
			<th colspan="10" align="left" class="font15" scope="col">{num_pro} {nombre_pro}{cuentas}</th>
		</tr>
		<tr>
			<th>Factura</th>
			<th>Fecha</th>
			<th>Compa&ntilde;&iacute;a</th>
			<th>Contado</th>
			<th>Facturado</th>
			<th>Total</th>
			<th>Pagado</th>
			<th>Banco</th>
			<th>Folio</th>
			<th>Cobrado</th>
			<!-- <th>CFD</th> -->
		</tr>
		<!-- START BLOCK : row -->
		<tr id="row{id}">
			<td>{num_fact}</td>
			<td align="center" class="orange">{fecha}</td>
			<td>{num_cia} {nombre_cia}</td>
			<td align="right" class="orange">{contado}</td>
			<td align="right" class="blue">{facturado}</td>
			<td align="right" class="green bold">{total}</td>
			<td align="center" class="green">{fecha_pago}</td>
			<td align="center">{banco}</td>
			<td align="right">{folio}</td>
			<td align="center" class="blue">{fecha_cobro}</td>
			<!-- <td align="right">
				<img src="/lecaroz/iconos/magnify{cfd_disabled}.png" alt="{id}" name="visualizar"{icono_class} style="margin-right:4px;" id="visualizar" />
				<img src="/lecaroz/iconos/printer{cfd_disabled}.png" alt="{id}" name="imprimir"{icono_class} style="margin-right:4px;" id="imprimir" />
				<img src="/lecaroz/iconos/download{cfd_disabled}.png" alt="{id}" name="descargar"{icono_class} id="descargar" />
			</td> -->
		</tr>
		<!-- END BLOCK : row -->
		<tr>
			<th colspan="3" align="right">Total</th>
			<th align="right" class="font12">{contado}</th>
			<th align="right" class="font12">{facturado}</th>
			<th align="right" class="font12">{total}</th>
			<th colspan="4" align="right">&nbsp;</th>
		</tr>
		<tr>
			<td colspan="10" align="center">&nbsp;</td>
		</tr>
		<!-- END BLOCK : pro -->
	</tbody>
	<tfoot>
		<tr>
			<th colspan="3" align="right">Total general</th>
			<th align="right" class="font12">{contado}</th>
			<th align="right" class="font12">{facturado}</th>
			<th align="right" class="font12">{total}</th>
			<th colspan="4" align="right">&nbsp;</th>
		</tr>
	</tfoot>
</table>
<p>
	<input type="button" name="regresar" id="regresar" value="Regresar" />
	&nbsp;&nbsp;
	<input type="button" name="reporte" id="reporte" value="Reporte para imprimir" />
	&nbsp;&nbsp;
	<input type="button" name="exportar" id="exportar" value="Exportar a Excel" />
</p>
