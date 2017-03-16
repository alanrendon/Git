<table class="table">
	<thead>
		<tr>
			<th>Compa&ntilde;&iacute;a</th>
			<th>Cuenta</th>
			<th>Fecha</th>
			<th>C&oacute;digo</th>
			<th>Concepto</th>
			<th>Importe</th>
			<th>Separar</th>
			<th>Total</th>
		</tr>
	</thead>
	<tbody>
		<!-- START BLOCK : comprobante -->
		<tr>
			<th colspan="2" class="left">
				<input name="comprobante[]" type="checkbox" id="comprobante" value="{comprobante}" checked="checked" />
				{comprobante}
			</th>
			<th colspan="6" class="left">{banco}</th>
		</tr>
		<!-- START BLOCK : row -->
		<tr>
			<td>{num_cia} {nombre_cia}</td>
			<td>{cuenta}</td>
			<td class="center">{fecha}</td>
			<td>{cod_mov} {descripcion}</td>
			<td>{concepto}</td>
			<td class="right">{importe}</td>
			<td class="right green">{separar}</td>
			<td class="bold right">{total}</td>
		</tr>
		<!-- END BLOCK : row -->
		<!-- START BLOCK : total -->
		<tr>
			<th colspan="5" class="bold right">Total comprobante</th>
			<th class="bold right">{importe}</th>
			<th class="bold right">{separar}</th>
			<th class="bold right">{total}</th>
		</tr>
		<!-- END BLOCK : total -->
		<tr>
			<td colspan="8">&nbsp;</td>
		</tr>
		<!-- END BLOCK : comprobante -->
	</tbody>
	<tfoot>
		<tr>
			<td colspan="8">&nbsp;</td>
		</tr>
	</tfoot>
</table>
<!-- START BLOCK : totales -->
<br />
<table class="table">
	<thead>
		<tr>
			<th>Saldo<br />bancos</th>
			<th>Saldo<br />libros</th>
			<th>Pagos no<br />cobrados</th>
			<th>Saldo<br />proveedores</th>
			<th>Libros -<br />Proveedores</th>
			<th>Inventario<br />inicial</th>
			<th>Perdidas<br />anteriores</th>
			<th>Devoluciones<br />de I.V.A.</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td class="right bold font12">{saldo_bancos}</td>
			<td class="right bold font12">{saldo_libros}</td>
			<td class="right bold font12">{pagos_no_cobrados}</td>
			<td class="right bold font12">{saldo_proveedores}</td>
			<td class="right bold font12">{dif_libros_proveedores}</td>
			<td class="right bold font12">{inventario_inicial}</td>
			<td class="right bold font12">{perdidas}</td>
			<td class="right bold font12">{devoluciones_iva}</td>
		</tr>
	</tbody>
	<tfoot>
		<tr>
			<td colspan="8">&nbsp;</td>
		</tr>
	</tfoot>
</table>
<!-- END BLOCK : totales -->
<p>
	<button type="button" id="regresar" />Regresar</button>
	&nbsp;&nbsp;
	<button type="button" id="comprobantes" />Generar comprobantes</button>
	&nbsp;&nbsp;
	<button type="button" id="imprimir" />Imprimir fichas</button>
	<!-- &nbsp;&nbsp; -->
	<!-- <button type="button" id="reporte" />Reporte para impreso</button> -->
	<!-- &nbsp;&nbsp; -->
	<!-- <button type="button" id="exportar" />Exportar a CSV</button> -->
</p>
