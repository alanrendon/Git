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
			<td class="bold font12">Tipo</td>
			<td class="bold font12">COMPRAS</td>
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
			<th colspan="15">&nbsp;</th>
		</tr>
	</thead>
	<tbody>
		<!-- START BLOCK : mp -->
		<tr>
			<th colspan="15" align="left" class="font14">{codmp} {nombre_mp}</th>
		</tr>
		<tr>
			<td colspan="15">&nbsp;</td>
		</tr>
		<!-- START BLOCK : pro -->
		<tr>
			<th colspan="15" align="left" class="font12">{num_pro} {nombre_pro}</th>
		</tr>
		<tr>
			<th>Compa&ntilde;&iacute;a</th>
			<!-- START BLOCK : mes -->
			<th>{mes}</th>
			<!-- END BLOCK : mes -->
			<th>Total</th>
			<th>Promedio</th>
		</tr>
		<!-- START BLOCK : row -->
		<tr>
			<td class="bold">{num_cia} {nombre_cia}</td>
			<!-- START BLOCK : cantidad -->
			<td align="right"{color}>{cantidad}</td>
			<!-- END BLOCK : cantidad -->
			<td align="right" class="bold{color}">{total}</td>
			<td align="right" class="bold green">{promedio}</td>
		</tr>
		<!-- END BLOCK : row -->
		<tr>
			<th align="right">Totales proveedor</th>
			<!-- START BLOCK : total_pro -->
			<th align="right">{total}</th>
			<!-- END BLOCK : total_pro -->
			<th align="right">{total}</th>
			<th align="right">{promedio}</th>
		</tr>
		<tr>
			<td colspan="15">&nbsp;</td>
		</tr>
		<!-- END BLOCK : pro -->
		<tr>
			<th align="right" class="font12">Totales producto</th>
			<!-- START BLOCK : total_mp -->
			<th align="right" class="font12">{total}</th>
			<!-- END BLOCK : total_mp -->
			<th align="right" class="font12">{total}</th>
			<th align="right" class="font12">{promedio}</th>
		</tr>
		<tr>
			<td colspan="15">&nbsp;</td>
		</tr>
		<!-- END BLOCK : mp -->
	</tbody>
	<tfoot>
		<tr>
			<th align="right" class="font12">Totales generales</th>
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
	&nbsp;&nbsp;
	<button type="button" id="reporte">Reporte para imprimir</button>
	&nbsp;&nbsp;
	<button type="button" id="exportar">Exportar a CSV</button>
</p>
