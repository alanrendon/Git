<table class="table">
	<thead>
		<tr>
			<th colspan="25" align="left" scope="col"><input type="checkbox" id="checkall"> Marcar todos</th>
		</tr>
	</thead>
	<tbody>
		<!-- START BLOCK : pro -->
		<tr>
			<th colspan="25" align="left" class="font15" scope="col">{num_pro} {nombre_pro}{cuentas}</th>
		</tr>
		<tr>
			<th><input type="checkbox" id="checkpro" value="{num_pro}"></th>
			<th>Factura</th>
			<th>Fecha</th>
			<th>Recibido</th>
			<th>Compa&ntilde;&iacute;a</th>
			<th>R.F.C.</th>
			<th>Concepto</th>
			<th>Gasto</th>
			<th>Importe</th>
			<th>Faltantes</th>
			<th>Dif.<br />precio</th>
			<th>Devoluciones</th>
			<th>Descuentos</th>
			<th>I.V.A.</th>
			<th>Ret. I.V.A.</th>
			<th>Ret. I.S.R.</th>
			<th>Fletes</th>
			<th>Otros</th>
			<th>Total</th>
			<th>Pagado</th>
			<th>Banco</th>
			<th>Folio</th>
			<th>Cobrado</th>
			<th><img src="/lecaroz/imagenes/tool16x16.png" /></th>
			<th>CFD</th>
		</tr>
		<!-- START BLOCK : row -->
		<tr id="row{id}">
			<td><input name="id[]" type="checkbox" id="id_{id}" value="{id}" data-pro="{num_pro}"></td>
			<td>{num_fact}</td>
			<td align="center" class="orange">{fecha}</td>
			<td align="center" class="orange">{recibido}</td>
			<td>{num_cia} {nombre_cia}</td>
			<td>{rfc_cia}</td>
			<td>{concepto}</td>
			<td>{gasto} {nombre_gasto}</td>
			<td align="right" class="green">{importe}</td>
			<td align="right" class="blue">{faltantes}</td>
			<td align="right" class="blue">{dif_precio}</td>
			<td align="right" class="blue">{devoluciones}</td>
			<td align="right" class="blue">{descuentos}</td>
			<td align="right" class="red">{iva}</td>
			<td align="right" class="orange">{ret_iva}</td>
			<td align="right" class="orange">{ret_isr}</td>
			<td align="right" class="purple">{fletes}</td>
			<td align="right" class="purple">{otros}</td>
			<td align="right" class="green bold">{total}</td>
			<td align="center" class="green">{fecha_pago}</td>
			<td align="center">{banco}</td>
			<td align="right">{folio}</td>
			<td align="center" class="blue">{fecha_cobro}</td>
			<td align="right">
				<img src="/lecaroz/iconos/cancel_round{cancelar_disabled}.png" alt="{data_fac}" name="cancelar"{icono_cancelar_class} style="margin-right:4px;" id="cancelar" />
			</td>
			<td align="right">
				<img src="/lecaroz/iconos/magnify{cfd_disabled}.png" alt="{id}" name="visualizar"{icono_cfd_class} style="margin-right:4px;" id="visualizar" />
				<img src="/lecaroz/iconos/printer{cfd_disabled}.png" alt="{id}" name="imprimir"{icono_cfd_class} style="margin-right:4px;" id="imprimir" />
				<img src="/lecaroz/iconos/download{cfd_disabled}.png" alt="{id}" name="descargar"{icono_cfd_class} id="descargar" />
			</td>
		</tr>
		<!-- END BLOCK : row -->
		<tr>
			<th colspan="8" align="right">Total</th>
			<th align="right" class="font12">{importe}</th>
			<th align="right" class="font12">{faltantes}</th>
			<th align="right" class="font12">{dif_precio}</th>
			<th align="right" class="font12">{devoluciones}</th>
			<th align="right" class="font12">{descuentos}</th>
			<th align="right" class="font12">{iva}</th>
			<th align="right" class="font12">{ret_iva}</th>
			<th align="right" class="font12">{ret_isr}</th>
			<th align="right" class="font12">{fletes}</th>
			<th align="right" class="font12">{otros}</th>
			<th align="right" class="font12">{total}</th>
			<th colspan="6" align="right">&nbsp;</th>
		</tr>
		<tr>
			<td colspan="25" align="center">&nbsp;</td>
		</tr>
		<!-- END BLOCK : pro -->
	</tbody>
	<tfoot>
		<tr>
			<th colspan="8" align="right">Total general</th>
			<th align="right" class="font12">{importe}</th>
			<th align="right" class="font12">{faltantes}</th>
			<th align="right" class="font12">{dif_precio}</th>
			<th align="right" class="font12">{devoluciones}</th>
			<th align="right" class="font12">{descuentos}</th>
			<th align="right" class="font12">{iva}</th>
			<th align="right" class="font12">{ret_iva}</th>
			<th align="right" class="font12">{ret_isr}</th>
			<th align="right" class="font12">{fletes}</th>
			<th align="right" class="font12">{otros}</th>
			<th align="right" class="font12">{total}</th>
			<th colspan="6" align="right">&nbsp;</th>
		</tr>
	</tfoot>
</table>
<p>
	<button type="button" id="regresar">Regresar</button>
	&nbsp;&nbsp;
	<button type="button" id="cargar_conta">Cargar contabilidad</button>
	<!-- &nbsp;&nbsp;
	<input type="button" name="listado" id="listado" value="Listado para imprimir" />
	&nbsp;&nbsp;
	<input type="button" name="exportar" id="exportar" value="Exportar a Excel" /> -->
</p>
