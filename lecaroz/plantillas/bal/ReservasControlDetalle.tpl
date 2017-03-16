<table class="table">
	<thead>
		<tr>
			<th scope="col" colspan="2">&nbsp;</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td class="bold font12">Compa&ntilde;&iacute;a</td>
			<td class="bold font12">{num_cia} {nombre_cia}</td>
		</tr>
		<tr>
			<td class="bold font12">A&ntilde;o</td>
			<td class="bold font12">{anio}</td>
		</tr>
		<tr>
			<td class="bold font12">Reserva</td>
			<td class="bold font12">{cod_reserva} {reserva}</td>
		</tr>
	</tbody>
	<tfoot>
		<tr>
			<td colspan="2">&nbsp;</td>
		</tr>
	</tfoot>
</table>
<br />
<form action="" method="post" name="consulta" class="FormValidator" id="consulta">
	<input name="num_cia" type="hidden" id="num_cia" value="{num_cia}" />
	<input name="reserva" type="hidden" id="reserva" value="{cod_reserva}" />
	<input name="anio" type="hidden" id="anio" value="{anio}" />
	<input name="distribuir_diferencia" type="hidden" id="distribuir_diferencia" value="{distribuir_diferencia}" />
	<table class="table">
		<thead>
			<tr>
				<th scope="col">Mes</th>
				<th scope="col">Reserva</th>
				<!-- START BLOCK : pagado_header -->
				<th scope="col">Pagado</th>
				<!-- END BLOCK : pagado_header -->
				<!-- START BLOCK : promedio_header -->
				<th scope="col">Promedio</th>
				<!-- END BLOCK : promedio_header -->
				<!-- START BLOCK : extra_info_header -->
				<th scope="col">&nbsp;</th>
				<th scope="col">Empleados</th>
				<th scope="col">Cobros<br />Infonavit</th>
				<th scope="col">% Riesgo</th>
				<th scope="col">Costo x<br />empleado</th>
				<!-- END BLOCK : extra_info_header -->
			</tr>
		</thead>
		<tbody>
			<!-- START BLOCK : reserva -->
			<tr>
				<td class="bold">
					<input name="mes[]" type="hidden" id="mes" value="{mes}" />
					<input name="status[]" type="hidden" id="status" value="{status}" />
					{nombre_mes}
				</td>
				<td class="right blue">
					<input name="reserva_input[]" type="hidden" id="reserva_input" value="{reserva_value}" />
					{reserva}
				</td>
				<!-- START BLOCK : pagado -->
				<td class="right red">
					<input name="pagado_input[]" type="hidden" id="pagado_input" value="{pagado_value}" />
					{pagado}
				</td>
				<!-- END BLOCK : pagado -->
				<!-- START BLOCK : promedio -->
				<td class="right green">
					<input name="promedio_input[]" type="hidden" id="promedio_input" value="{promedio_value}" />
					{promedio}
				</td>
				<!-- END BLOCK : promedio -->
				<!-- START BLOCK : extra_info -->
				<td>&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;</td>
				<td class="right blue">{empleados}</td>
				<td class="right orange">{infonavit}</td>
				<td class="right green">{riesgo}</td>
				<td class="right red">{costo_empleado}</td>
				<!-- END BLOCK : extra_info -->
			</tr>
			<!-- END BLOCK : reserva -->
			<tr>
				<th class="right">Totales</th>
				<th><span class="right blue" id="total_reserva">{total_reserva}</span></th>
				<!-- START BLOCK : total_pagado -->
				<th><span class="right red" id="total_pagado">{total_pagado}</span></th>
				<!-- END BLOCK : total_pagado -->
				<!-- START BLOCK : blank_promedio_1 -->
				<th>&nbsp;</th>
				<!-- END BLOCK : blank_promedio_1 -->
				<!-- START BLOCK : extra_info_footer_1 -->
				<th colspan="5">&nbsp;</th>
				<!-- END BLOCK : extra_info_footer_1 -->
			</tr>
		</tbody>
		<tfoot>
			<tr>
				<td class="bold right">Diferencia</td>
				<td class="bold center" colspan="{diferencia_span}"><span id="diferencia">{diferencia}</span></td>
				<!-- START BLOCK : blank_promedio_2 -->
				<td>&nbsp;</td>
				<!-- END BLOCK : blank_promedio_2 -->
				<!-- START BLOCK : extra_info_footer_2 -->
				<th colspan="5">&nbsp;</th>
				<!-- END BLOCK : extra_info_footer_2 -->
			</tr>
		</tfoot>
	</table>
	<p>
		<input name="anterior" type="button" id="anterior" value="&lt;&lt; Anterior" />
		<input name="ir_a" type="button" id="ir_a" value="Ir a" />
		<input name="num_cia_next" type="text" id="num_cia_next" class="validate focus toPosInt center" size="3" />
		<input name="nombre_cia_next" type="text" id="nombre_cia_next" size="30" disabled="disabled" />
		<input name="siguiente" type="button" id="siguiente" value="Siguiente &gt;&gt;" />
	</p>
	<p>
		<input name="terminar" type="button" id="terminar" value="Terminar" />
	</p>
</form>
