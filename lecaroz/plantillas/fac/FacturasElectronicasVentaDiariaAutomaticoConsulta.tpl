<table class="table">
	<thead>
		<tr>
			<th colspan="2">&nbsp;</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td class="bold font12">Periodo</td>
			<td class="bold font12">{mes} {anio}</td>
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
			<!-- START BLOCK : dia -->
			<th>{dia}</th>
			<!-- END BLOCK : dia -->
			<th>Total</th>
			<th>Promedio</th>
		</tr>
	</thead>
	<tbody>
		<!-- START BLOCK : row -->
		<tr>
			<td class="bold">{num_cia} {nombre_cia}</td>
			<!-- START BLOCK : kilos -->
			<td align="right">{kilos}</td>
			<!-- END BLOCK : kilos -->
			<td align="right" class="bold">{total}</td>
			<td align="right" class="bold green">{promedio}</td>
		</tr>
		<!-- END BLOCK : row -->
	</tbody>
	<tfoot>
		<tr>
			<th align="right" class="font12">Totales</th>
			<!-- START BLOCK : total -->
			<th align="right" class="font12">{total}</th>
			<!-- END BLOCK : total -->
			<th align="right" class="font12">{total}</th>
			<th align="right" class="font12">{promedio}</th>
		</tr>
	</tfoot>
</table>
<p>
	<button type="button" id="regresar">Regresar</button>
	<!-- &nbsp;&nbsp;
	<button type="button" id="reporte">Reporte para imprimir</button> -->
	&nbsp;&nbsp;
	<button type="button" id="exportar">Exportar a CSV</button>
</p>
