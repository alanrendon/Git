<table class="table">
	<thead>
		<tr>
			<th colspan="2">&nbsp;</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td class="bold font12">D&iacute;a</td>
			<td class="bold font12">{dia} DE {mes}</td>
		</tr>
	</tbody>
	<tfoot>
		<tr>
			<td colspan="2">&nbsp;</td>
		</tr>
	</tfoot>
</table>
<br />
<table class="table">
	<thead>
		<tr>
			<th>Compa&ntilde;&iacute;a</th>
			<th>{anio1}</th>
			<th>{anio2}</th>
			<th>{anio3}</th>
			<th>{anio4}</th>
			<th>{anio5}</th>
			<th>% Variación</th>
		</tr>
	</thead>
	<tbody>
		<!-- START BLOCK : row -->
		<tr>
			<td>{num_cia} {nombre_cia}</td>
			<td class="right blue">{ventas1}</td>
			<td class="right green">{ventas2}</td>
			<td class="right orange">{ventas3}</td>
			<td class="right red">{ventas4}</td>
			<td class="right purple">{ventas5}</td>
			<td class="right">{pvar}</td>
		</tr>
		<!-- END BLOCK : row -->
	</tbody>
	<tfoot>
		<tr>
			<th class="bold right">Totales</th>
			<th class="bold right">{total1}</th>
			<th class="bold right">{total2}</th>
			<th class="bold right">{total3}</th>
			<th class="bold right">{total4}</th>
			<th class="bold right">{total5}</th>
			<th>&nbsp;</th>
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
