<table class="table">
	<thead>
		<tr>
			<th>Proveedor</th>
			<th>Saldo</th>
			<th># Facturas</th>
			<th>Más antigua</th>
			<th>Validadas</th>
			<th>Por aclarar</th>
		</tr>
	</thead>
	<tbody>
		<!-- START BLOCK : row -->
		<tr>
			<td>{num_pro} {nombre_pro}</td>
			<td class="right blue">{saldo}</td>
			<td class="right orange">{facturas}</td>
			<td class="center purple">{mas_antigua}</td>
			<td class="right green">{validadas}</td>
			<td class="right red">{por_aclarar}</td>
		</tr>
		<!-- END BLOCK : row -->
	</tbody>
	<tfoot>
		<tr>
			<th class="bold right">Totales</th>
			<th class="bold right">{saldo}</th>
			<th class="bold right">{facturas}</th>
			<th class="bold right">&nbsp;</th>
			<th class="bold right">{validadas}</th>
			<th class="bold right">{por_aclarar}</th>
		</tr>
	</tfoot>
</table>
<p>
	<button type="button" id="regresar" />Regresar</button>
	&nbsp;&nbsp;
	<button type="button" id="reporte" />Reporte para impreso</button>
	&nbsp;&nbsp;
	<button type="button" id="exportar" />Exportar a CSV</button>
</p>
