<table class="table">
	<thead>
		<tr>
			<th>&nbsp;</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td class="bold font12">{banco}</td>
		</tr>
	</tbody>
	<tfoot>
		<tr>
			<td>&nbsp;</td>
		</tr>
	</tfoot>
</table>
<br />
<table class="table">
	<thead>
		<tr>
			<th>Compa&ntilde;&iacute;a</th>
			<th>R.F.C.</th>
			<th>Saldo<br />bancos</th>
			<th>Saldo<br />libros</th>
			<th>Pagos no<br />cobrados</th>
			<th>Saldo<br />proveedores</th>
			<th>Libros -<br />proveedores</th>
			<th>Utilidad<br />estimada</th>
			<th>Diferencia<br />contable</th>
			<th>Inventario<br />inicial</th>
			<th>Perdidas<br />anteriores</th>
			<th>Devoluciones<br />de I.V.A.</th>
		</tr>
	</thead>
	<tbody>
		<!-- START BLOCK : row -->
		<tr>
			<td>{num_cia} {nombre_cia}</td>
			<td>{rfc_cia}</td>
			<td class="right">{saldo_bancos}</td>
			<td class="right bold">{saldo_libros}</td>
			<td class="right">{pagos_no_cobrados}</td>
			<td class="right bold">{saldo_proveedores}</td>
			<td class="right">{dif_libros_proveedores}</td>
			<td class="right">{utilidad_estimada}</td>
			<td class="right">{diferencia_contable}</td>
			<td class="right">{inventario_inicial}</td>
			<td class="right">{perdidas}</td>
			<td class="right">{devoluciones_iva}</td>
		</tr>
		<!-- END BLOCK : row -->
	</tbody>
	<tfoot>
		<tr>
			<td colspan="12">&nbsp;</td>
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
			<th>Libros -<br />proveedores</th>
			<th>Utilidad<br />estimada</th>
			<th>Diferencia<br />contable</th>
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
			<td class="right bold font12">{utilidad_estimada}</td>
			<td class="right bold font12">{diferencia_contable}</td>
			<td class="right bold font12">{inventario_inicial}</td>
			<td class="right bold font12">{perdidas}</td>
			<td class="right bold font12">{devoluciones_iva}</td>
		</tr>
	</tbody>
	<tfoot>
		<tr>
			<td colspan="10">&nbsp;</td>
		</tr>
	</tfoot>
</table>
<!-- END BLOCK : totales -->
<p>
	<button type="button" id="regresar" />Regresar</button>
	&nbsp;&nbsp;
	<button type="button" id="reporte" />Reporte para impreso</button>
	&nbsp;&nbsp;
	<button type="button" id="exportar" />Exportar a CSV</button>
</p>
