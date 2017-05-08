<form name="consulta_form" class="FormValidator" id="consulta_form">
	<input name="num_cia" type="hidden" id="num_cia" value="{num_cia}">
	<input name="fecha" type="hidden" id="fecha" value="{fecha}">
	<input name="anio" type="hidden" id="anio" value="{anio}">
	<input name="mes" type="hidden" id="mes" value="{mes}">
	<table class="table">
		<tbody>
			<tr>
				<th colspan="2">&nbsp;</th>
			</tr>
		</tbody>
		<tbody>
			<tr>
				<td class="bold font14">Compa&ntilde;&iacute;a</td>
				<td class="bold font14">{num_cia} {nombre_cia}</td>
			</tr>
			<tr>
				<td class="bold font14">Fecha</td>
				<td class="bold font14">{dia} DE {mes_escrito} DE {anio}</td>
			</tr>
		</tbody>
		<tfoot>
			<tr>
				<td colspan="2">&nbsp;</td>
			</tr>
		</tfoot>
	</table>
	<br>
	<!-- START BLOCK : leyenda_no_control -->
	<p class="bold font12 red">Existen productos o turnos (marcados en rojo) que no est&aacute;n en el control de av&iacute;o, favor de verificar.</p>
	<!-- END BLOCK : leyenda_no_control -->
	<table class="table">
		<thead>
			<tr>
				<th>Producto</th>
				<th>Existencia<br>anterior</th>
				<th>Entradas</th>
				<th>Total</th>
				<th>FD</th>
				<th>FN</th>
				<th>BD</th>
				<th>REP</th>
				<th>PIC</th>
				<th>GEL</th>
				<th>DES</th>
				<th>Consumo<br>total</th>
				<th>Para<br>ma&ntilde;ana</th>
			</tr>
		</thead>
		<tbody>
			<!-- START BLOCK : row -->
			<tr{no_control}>
				<td class="bold"><a href="javascript:;" class="aux enlace black" data-mp='{codmp}'>{codmp} {nombre_mp}</a></td>
				<td class="bold right {existencia_inicio_color}">{existencia_inicio}</td>
				<td class="bold right blue">{movimiento_0}</td>
				<td class="bold right green">{total}</td>
				<td class="bold right" style="color:#990000">{movimiento_1}</td>
				<td class="bold right" style="color:#990033">{movimiento_2}</td>
				<td class="bold right" style="color:#990066">{movimiento_3}</td>
				<td class="bold right" style="color:#990099">{movimiento_4}</td>
				<td class="bold right" style="color:#9900CC">{movimiento_8}</td>
				<td class="bold right" style="color:#9900FF">{movimiento_9}</td>
				<td class="bold right" style="color:#9966FF">{movimiento_10}</td>
				<td class="bold right red">{consumos}</td>
				<td class="bold right {existencia_fin_color}">{existencia_fin}</td>
			</tr>
			<!-- END BLOCK : row -->
		</tbody>
		<tfoot>
			<tr>
				<td colspan="13">&nbsp;</td>
			</tr>
		</tfoot>
	</table>
	<!-- START BLOCK : consumos_excedentes -->
	<p class="bold font12 red">Los siguientes productos sobrepasan el promedio de consumo.</p>
	<table class="table">
		<thead>
			<tr>
				<th>Producto</th>
				<th>Turno</th>
				<th>Promedio</th>
				<th>Consumo</th>
				<th>% Excedente</th>
			</tr>
		</thead>
		<tbody>
			<!-- START BLOCK : row_consumo_excedente -->
			<tr>
				<td class="bold">
					<input name="consumo_excedente[]" type="hidden" id="consumo_excedente" value='{consumo_excedente}'>
					{codmp} {nombre_mp}
				</td>
				<td class="bold">{turno}</td>
				<td class="bold right green">{promedio}</td>
				<td class="bold right red">{consumo}</td>
				<td class="bold right{por_diferencia_color}">{por_diferencia}</td>
			</tr>
			<!-- END BLOCK : row_consumo_excedente -->
		</tbody>
		<tfoot>
			<tr>
				<td colspan="5">&nbsp;</td>
			</tr>
		</tfoot>
	</table>
	<!-- END BLOCK : consumos_excedentes -->
	<!-- START BLOCK : sin_promedios -->
	<p class="bold font12 red">Los siguientes productos no tienen promedio de consumo.</p>
	<table class="table">
		<thead>
			<tr>
				<th>Producto</th>
				<th>Turno</th>
				<th>Consumo</th>
			</tr>
		</thead>
		<tbody>
			<!-- START BLOCK : row_sin_promedio -->
			<tr>
				<td class="bold">
					<input name="sin_promedio[]" type="hidden" id="sin_promedio" value='{sin_promedio}'>
					{codmp} {nombre_mp}
				</td>
				<td class="bold">{turno}</td>
				<td class="bold right red">{consumo}</td>
			</tr>
			<!-- END BLOCK : row_sin_promedio -->
		</tbody>
		<tfoot>
			<tr>
				<td colspan="3">&nbsp;</td>
			</tr>
		</tfoot>
	</table>
	<!-- END BLOCK : sin_promedios -->
	<!-- START BLOCK : existencias_excedentes -->
	<p class="bold font12 red">Los siguientes productos tienen inventario para mas de 25 d&iacute;as.</p>
	<table class="table">
		<thead>
			<tr>
				<th>Producto</th>
				<th>Consumo de<br>25 d&iacute;as</th>
				<th>Existencia</th>
				<th>% Excedente</th>
			</tr>
		</thead>
		<tbody>
			<!-- START BLOCK : row_existencia_excedente -->
			<tr>
				<td class="bold">{codmp} {nombre_mp}</td>
				<td class="bold right red">{consumos}</td>
				<td class="bold right green">{existencia}</td>
				<td class="bold right{por_diferencia_color}">{por_diferencia}</td>
			</tr>
			<!-- END BLOCK : row_existencia_excedente -->
		</tbody>
		<tfoot>
			<tr>
				<td colspan="4">&nbsp;</td>
			</tr>
		</tfoot>
	</table>
	<!-- END BLOCK : existencias_excedentes -->
	<p>
		<button type="button" id="regresar">Regresar</button>
		&nbsp;&nbsp;<button type="button" id="validar"{disabled}>Validar</button>
	</p>
</form>
