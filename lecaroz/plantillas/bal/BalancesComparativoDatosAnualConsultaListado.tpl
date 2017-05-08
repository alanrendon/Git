<table class="table">
	<thead>
		<tr>
			<th colspan="2">&nbsp;</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td class="bold font12">A&ntilde;o</td>
			<td class="bold font12">{anio}</td>
		</tr>
		<tr>
			<td class="bold font12">Concepto</td>
			<td class="bold font12">{concepto}</td>
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
			<!-- START BLOCK : mes -->
			<th>{mes}</th>
			<!-- END BLOCK : mes -->
			<th>Total</th>
			<th>Promedio</th>
		</tr>
	</thead>
	<tbody>
		<!-- START BLOCK : row -->
		<!-- START BLOCK : subtitle -->
		<tr>
			<td colspan="{numcols}">&nbsp;</td>
		</tr>
		<tr>
			<th>Compa&ntilde;&iacute;a</th>
			<!-- START BLOCK : submes -->
			<th>{mes}</th>
			<!-- END BLOCK : submes -->
			<th>Total</th>
			<th>Promedio</th>
		</tr>
		<!-- END BLOCK : subtitle -->
		<tr>
			<td class="bold">{num_cia} {nombre_cia}</td>
			<!-- START BLOCK : importe -->
			<td align="right">{importe}</td>
			<!-- END BLOCK : importe -->
			<td align="right" class="bold">{total}</td>
			<td align="right" class="bold">{promedio}</td>
		</tr>
		<!-- START BLOCK : totales -->
		<tr>
			<th align="right">Totales</th>
			<!-- START BLOCK : total_mes -->
			<th align="right">{total_mes}</th>
			<!-- END BLOCK : total_mes -->
			<th align="right">{total_anio}</th>
			<th align="right">{promedio_anio}</th>
		</tr>
		<!-- END BLOCK : totales -->
		<!-- END BLOCK : row -->
	</tbody>
	<tfoot>
		<tr>
			<td colspan="{numcols}">&nbsp;</td>
		</tr>
	</tfoot>
</table>
<p>
	<button type="button" id="regresar">Regresar</button>
	&nbsp;&nbsp;
	<button type="button" id="reporte">Reporte para imprimir</button>
	&nbsp;&nbsp;
	<button type="button" id="graficas_barras">Reporte con gr&aacute;ficas de barras</button>
	&nbsp;&nbsp;
	<button type="button" id="graficas_lineas">Reporte con gr&aacute;ficas de lineas</button>
	&nbsp;&nbsp;
	<button type="button" id="exportar">Exportar a CSV</button>
</p>
