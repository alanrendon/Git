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
			<th rowspan="2">Compa&ntilde;&iacute;a</th>
			<th colspan="2">{mes}</th>
			<th rowspan="2">Diferencia</th>
			<th rowspan="2">% Incremento</th>
		</tr>
		<tr>
			<th>{anio_ant}</th>
			<th>{anio}</th>
		</tr>
	</thead>
	<tbody>
		<!-- START BLOCK : row -->
		<!-- START BLOCK : subtitle -->
		<tr>
			<td colspan="5">&nbsp;</td>
		</tr>
		<tr>
			<th rowspan="2">Compa&ntilde;&iacute;a</th>
			<th colspan="2">{mes}</th>
			<th rowspan="2">Diferencia</th>
			<th rowspan="2">% Incremento</th>
		</tr>
		<tr>
			<th>{anio_ant}</th>
			<th>{anio}</th>
		</tr>
		<!-- END BLOCK : subtitle -->
		<tr>
			<td class="bold">{num_cia} {nombre_cia}</td>
			<td align="right">{importe_ant}</td>
			<td align="right">{importe}</td>
			<td align="right" class="bold {color}">{diferencia}</td>
			<td align="right" class="bold {color}">{incremento}</td>
		</tr>
		<!-- START BLOCK : totales -->
		<tr>
			<th align="right">Totales</th>
			<th align="right">{total_ant}</th>
			<th align="right">{total}</th>
			<th align="right">{diferencia}</th>
			<th align="right">&nbsp;</th>
		</tr>
		<!-- END BLOCK : totales -->
		<!-- END BLOCK : row -->
	</tbody>
	<!-- START BLOCK : no_incremento -->
	<tbody>
		<tr>
			<td colspan="5">&nbsp;</td>
		</tr>
		<tr>
			<th rowspan="2">Compa&ntilde;&iacute;a</th>
			<th colspan="2">{mes}</th>
			<th rowspan="2">Diferencia</th>
			<th rowspan="2">% Incremento</th>
		</tr>
		<tr>
			<th>{anio_ant}</th>
			<th>{anio}</th>
		</tr>
		<!-- START BLOCK : row_no_incremento -->
		<!-- START BLOCK : subtitle_no_incremento -->
		<tr>
			<td colspan="5">&nbsp;</td>
		</tr>
		<tr>
			<th rowspan="2">Compa&ntilde;&iacute;a</th>
			<th colspan="2" align="center">{mes}</th>
			<th rowspan="2">Diferencia</th>
			<th rowspan="2">% Incremento</th>
		</tr>
		<tr>
			<th>{anio_ant}</th>
			<th>{anio}</th>
		</tr>
		<!-- END BLOCK : subtitle_no_incremento -->
		<tr>
			<td class="bold">{num_cia} {nombre_cia}</td>
			<td align="right">{importe_ant}</td>
			<td align="right">{importe}</td>
			<td align="right" class="bold {color}">{diferencia}</td>
			<td>&nbsp;</td>
		</tr>
		<!-- START BLOCK : totales_no_incremento -->
		<tr>
			<th align="right">Totales</th>
			<th align="right">{total_ant}</th>
			<th align="right">{total}</th>
			<th align="right">{diferencia}</th>
			<th align="right">&nbsp;</th>
		</tr>
		<!-- END BLOCK : totales_no_incremento -->
		<!-- END BLOCK : row_no_incremento -->
	</tbody>
	<!-- END BLOCK : no_incremento -->
	<tfoot>
		<tr>
			<th colspan="5">&nbsp;</th>
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
	<button type="button" id="exportar">Exportar a CSV</button>
</p>
