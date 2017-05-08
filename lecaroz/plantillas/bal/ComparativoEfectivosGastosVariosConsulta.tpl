<table class="table">
	<thead>
		<tr>
			<th>Periodo</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td align="center" class="bold font12">del {fecha1} al {fecha2}</td>
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
			<th>Dep&oacute;sitos</th>
			<th>Gastos y<br />n&oacute;minas</th>
			<th>Proveedores<br />materia prima</th>
			<th>Proveedores<br />varios</th>
			<th>Impuestos</th>
			<th>Total</th>
		</tr>
	</thead>
	<tbody>
		<!-- START BLOCK : row -->
		<tr>
			<td>{num_cia} {nombre_cia}</td>
			<td>{rfc_cia}</td>
			<td class="right blue">{depositos}</td>
			<td class="right red">{nominas}</td>
			<td class="right red">{mat_prima}</td>
			<td class="right red">{varios}</td>
			<td class="right red">{impuestos}</td>
			<td class="right">{total}</td>
		</tr>
		<!-- END BLOCK : row -->
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
	<button type="button" id="reporte" />Reporte para impreso</button>
	&nbsp;&nbsp;
	<button type="button" id="exportar" />Exportar a CSV</button>
</p>
