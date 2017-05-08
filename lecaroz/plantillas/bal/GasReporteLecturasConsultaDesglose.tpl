<table class="table">
	<thead>
		<tr>
			<th>&nbsp;</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td class="bold font12 center">{mes} {anio}</td>
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
			<th colspan="7">&nbsp;</th>
		</tr>
	</thead>
	<tbody>
		<!-- START BLOCK : cia -->
		<tr>
			<th class="left font12" colspan="7">{num_cia} {nombre_cia}</th>
		</tr>
		<tr>
			<th class="right">D&iacute;a</th>
			<th class="right">Lectura<br />inicial</th>
			<th class="right">Carga</th>
			<th class="right">Consumo</th>
			<th class="right">Lectura<br />final</th>
			<th class="right">Producci&oacute;n</th>
			<th class="right">Producci&oacute;n/<br />Consumo</th>
		</tr>
		<!-- START BLOCK : tanque -->
		<tr>
			<td class="bold" colspan="7">{num_tanque} {nombre_tanque}</td>
		</tr>
		<!-- START BLOCK : row -->
		<tr>
			<td class="right">{dia}</td>
			<td class="right green">{lectura_inicial}</td>
			<td class="right blue">{carga}</td>
			<td class="right red">{consumo}</td>
			<td class="right green">{lectura_final}</td>
			<td class="right purple">{produccion}</td>
			<td class="right orange">{pro_con}</td>
		</tr>
		<!-- END BLOCK : row -->
		<tr>
			<td colspan="7">&nbsp;</td>
		</tr>
		<!-- END BLOCK : tanque -->
		<!-- END BLOCK : cia -->
	</tbody>
	<tfoot>
		<tr>
			<td colspan="7">&nbsp;</td>
		</tr>
	</tfoot>
</table>
<p>
	<button type="button" id="regresar" />Regresar</button>
	<!-- &nbsp;&nbsp;
	<button type="button" id="reporte" />Reporte para impreso</button>
	&nbsp;&nbsp;
	<button type="button" id="exportar" />Exportar a CSV</button> -->
</p>
