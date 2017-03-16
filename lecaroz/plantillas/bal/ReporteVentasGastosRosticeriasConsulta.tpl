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
			<td class="bold font12">Mes</td>
			<td class="bold font12">{mes}</td>
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
			<th>Ventas</th>
			<th>Gastos de<br>operaci&oacute;n</th>
			<th>Gastos<br>generales</th>
			<th>Gastos<br>de caja</th>
			<th>Total de<br>gastos</th>
			<th>Costo de<br>materia prima</th>
			<th>Pollos<br>chicos</th>
			<th>Pollos<br>grandes</th>
			<th>Pescuezos</th>
		</tr>
	</thead>
	<tbody>
		<!-- START BLOCK : row -->
		<tr>
			<td>{num_cia} {nombre_cia}</td>
			<td class="right blue bold">{ventas}</td>
			<td class="right orange">{gastos_1}</td>
			<td class="right dark_gray">{gastos_2}</td>
			<td class="right purple">{gastos_3}</td>
			<td class="right red bold">{total_gastos}</td>
			<td class="right green bold">{costo_mat_prima}</td>
			<td class="right green bold">{pollos_chicos}</td>
			<td class="right green bold">{pollos_grandes}</td>
			<td class="right green bold">{pescuezos}</td>
		</tr>
		<!-- END BLOCK : row -->
	</tbody>
	<tfoot>
		<tr>
			<th class="bold right">Totales</th>
			<th class="bold right">{ventas}</th>
			<th class="bold right">{gastos_1}</th>
			<th class="bold right">{gastos_2}</th>
			<th class="bold right">{gastos_3}</th>
			<th class="bold right">{total_gastos}</th>
			<th class="bold right">{costo_mat_prima}</th>
			<th class="bold right">{pollos_chicos}</th>
			<th class="bold right">{pollos_grandes}</th>
			<th class="bold right">{pescuezos}</th>
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
