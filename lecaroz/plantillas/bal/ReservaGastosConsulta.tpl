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
<p>
	<button type="button" id="modificar"><img src="/lecaroz/iconos/plus.png" width="16" height="16" /> Modificar</button>
</p>
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
			<td align="right">{num_cia}</td>
			<td>{nombre_cia}</td>
			<td align="right" class="bold">{acumulado}</td>
			<!-- START BLOCK : td_mes -->
			<td align="right">{importe}</td>
			<!-- END BLOCK : td_mes -->
			<td align="right" class="bold">{total}</td>
		</tr>
		<!-- END BLOCK : row -->
	</tbody>
	<tfoot>
		<tr>
			<th align="right" colspan="2">Totales</th>
			<!-- START BLOCK : total_mes -->
			<th align="right">{total}</th>
			<!-- END BLOCK : total_mes -->
			<th align="right" class="bold">{total}</th>
		</tr>
	</tfoot>
</table>
<p>
	<input type="submit" name="regresar" id="regresar" value="Regresar">
</p>
