<table class="table">
	<thead>
		<tr>
			<th colspan="2">&nbsp;</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td class="bold font12">Concepto</td>
			<td class="bold font12">{cod_mov} {descripcion}</td>
		</tr>
		<tr>
			<td class="bold font12">Periodo</td>
			<td class="bold font12">{fecha1} al {fecha2}</td>
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
			<th>Importe</th>
		</tr>
	</thead>
	<tbody>
		<!-- START BLOCK : row -->
		<tr>
			<td>{num_cia} {nombre_cia}</td>
			<td class="right">{importe}</td>
		</tr>
		<!-- END BLOCK : row -->
	</tbody>
	<tfoot>
		<tr>
			<th align="right">Total general</th>
			<th class="right">{total}</th>
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
