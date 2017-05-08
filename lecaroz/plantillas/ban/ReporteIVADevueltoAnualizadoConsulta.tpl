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
			<th>Compa&ntilde;&iacute;a</th>
			<th>Ene</th>
			<th>Feb</th>
			<th>Mar</th>
			<th>Abr</th>
			<th>May</th>
			<th>Jun</th>
			<th>Jul</th>
			<th>Ago</th>
			<th>Sep</th>
			<th>Oct</th>
			<th>Nov</th>
			<th>Dic</th>
			<th>Total</th>
		</tr>
	</thead>
	<tbody>
		<!-- START BLOCK : row -->
		<tr>
			<td>{num_cia} {nombre_cia}</td>
			<td class="right green">{mes1}</td>
			<td class="right blue">{mes2}</td>
			<td class="right green">{mes3}</td>
			<td class="right blue">{mes4}</td>
			<td class="right green">{mes5}</td>
			<td class="right blue">{mes6}</td>
			<td class="right green">{mes7}</td>
			<td class="right blue">{mes8}</td>
			<td class="right green">{mes9}</td>
			<td class="right blue">{mes10}</td>
			<td class="right green">{mes11}</td>
			<td class="right blue">{mes12}</td>
			<td class="right bold">{total}</td>
		</tr>
		<!-- END BLOCK : row -->
	</tbody>
	<tfoot>
		<tr>
			<th align="right">Totales</th>
			<th class="right">{total1}</th>
			<th class="right">{total2}</th>
			<th class="right">{total3}</th>
			<th class="right">{total4}</th>
			<th class="right">{total5}</th>
			<th class="right">{total6}</th>
			<th class="right">{total7}</th>
			<th class="right">{total8}</th>
			<th class="right">{total9}</th>
			<th class="right">{total10}</th>
			<th class="right">{total11}</th>
			<th class="right">{total12}</th>
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
