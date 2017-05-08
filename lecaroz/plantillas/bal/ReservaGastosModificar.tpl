<form name="modificar_form" class="FormValidator" id="modificar_form">
	<input name="gasto" type="hidden" id="gasto" value="{gasto}">
	<input name="anio" type="hidden" id="anio" value="{anio}">
	<table class="table">
		<thead>
			<tr>
				<th colspan="2">&nbsp;</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td class="bold font12">Gasto</td>
				<td class="bold font12">{gasto} {nombre_gasto}</td>
			</tr>
			<tr>
				<td class="bold font12">A&ntilde;o</td>
				<td class="bold font12">{anio}</td>
			</tr>
		</tbody>
		<tfoot>
			<tr>
				<td colspan="2">&nbsp;</td>
			</tr>
		</tfoot>
	</table>
	<br>
	<table class="table">
		<thead>
			<tr>
				<th colspan="2">Compa&ntilde;&iacute;a</th>
				<th>Acumulado</th>
				<!-- START BLOCK : th_mes -->
				<th>{mes}</th>
				<!-- END BLOCK : th_mes -->
				<th>Total</th>
			</tr>
		</thead>
		<tbody>
			<!-- START BLOCK : row -->
			<tr>
				<td align="right">
					<input name="num_cia[]" type="hidden" id="num_cia" value="{num_cia}">
					{num_cia}
				</td>
				<td>{nombre_cia}</td>
				<td>
					<input name="acumulado[]" type="text" class="right bold" id="acumulado_{row}" value="{acumulado}" size="8" readonly="" data-row="{row}">
				</td>
				<!-- START BLOCK : td_mes -->
				<td>
					<input name="importe_{row}[]" type="text" class="validate focus numberColorFormat right" precision="2" id="importe_{row}_{mes}" size="8" value="{importe}" data-index="{index}" data-row="{row}" data-mes="{mes}">
				</td>
				<!-- END BLOCK : td_mes -->
				<td>
					<input name="total[]" type="text" class="right bold" id="total_{row}" value="{total}" size="8" readonly="" data-row="{row}">
				</td>
			</tr>
			<!-- END BLOCK : row -->
		</tbody>
		<tfoot>
			<tr>
				<td colspan="16">&nbsp;</td>
			</tr>
		</tfoot>
	</table>
</form>
<p>
	<button type="button" id="regresar">Regresar</button>
	&nbsp;&nbsp;
	<button type="button" id="modificar">Modificar</button>
</p>
