<table class="table">
	<thead>
		<tr>
			<th>A&ntilde;o</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td class="bold font12">{anio}</td>
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
			<th colspan="5">&nbsp;</th>
		</tr>
	</thead>
	<tbody>
		<!-- START BLOCK : producto -->
		<tr>
			<th class="font12 left" colspan="5">{codmp} {nombre_mp}</th>
		</tr>
		<tr>
			<th>Mes</th>
			<th>Existencia<br>inicial</th>
			<th>Compras</th>
			<th>Consumos</th>
			<th>Existencia<br>final</th>
		</tr>
		<!-- START BLOCK : mes -->
		<tr>
			<td class="bold">{mes}</td>
			<td class="right bold">{existencia_inicial}</td>
			<td class="right blue">{compras}</td>
			<td class="right red">{consumos}</td>
			<td class="right bold">{existencia_final}</td>
		</tr>
		<!-- END BLOCK : mes -->
		<tr>
			<th>Totales</th>
			<th class="right">{existencia_inicial}</th>
			<th class="right">{compras}</th>
			<th class="right">{consumos}</th>
			<th class="right">{existencia_final}</th>
		</tr>
		<tr>
			<td colspan="5">&nbsp;</td>
		</tr>
		<!-- END BLOCK : producto -->
	</tbody>
	<tfoot>
		<tr>
			<td colspan="5">&nbsp;</td>
		</tr>
	</tfoot>
</table>
<p>
	<button type="button" id="regresar" />Regresar</button>
	&nbsp;&nbsp;
	<button type="button" id="reporte" />Reporte para imprimir</button>
	<!-- &nbsp;&nbsp;
	<button type="button" id="exportar" />Exportar a CSV</button> -->
</p>
