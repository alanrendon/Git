<table class="table">
	<thead>
		<tr>
			<th colspan="2">&nbsp;</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<th align="left">Compa&ntilde;&iacute;a</th>
			<td class="bold font12">{num_cia} {nombre_cia}</td>
		</tr>
		<tr class="linea_on">
			<th align="left">R.F.C.</th>
			<td class="bold font12">{rfc_cia}</td>
		</tr>
		<tr>
			<th align="left">I.M.S.S.</th>
			<td class="bold font12">{no_imss}</td>
		</tr>
		<tr class="linea_on">
			<th align="left">Semana</th>
			<td class="bold font12">{semana}</td>
		</tr>
		<tr>
			<th align="left">Periodo</th>
			<td class="bold font12">{fecha1} al {fecha2}</td>
		</tr>
		<tr>
			<th align="left">Empleados</th>
			<td class="bold font12">{empleados}</td>
		</tr>
	</tbody>
	<tfoot>
		<tr>
			<th colspan="2">&nbsp;</th>
		</tr>
	</tfoot>
</table>
<br />
<table class="table">
	<thead>
		<tr>
			<th rowspan="2">No.</th>
			<th rowspan="2">Nombre del empleado</th>
			<th rowspan="2">Puesto</th>
			<th rowspan="2" nowrap="nowrap">Fecha de<br />ingreso</th>
			<th rowspan="2">CURP</th>
			<th rowspan="2">Afiliaci&oacute;n<br />I.M.S.S.</th>
			<th rowspan="2">Horario</th>
			<th colspan="7">Asistencia</th>
			<th colspan="{colspan}">Percepciones</th>
			<th colspan="6">Deducciones</th>
			<th rowspan="2" nowrap="nowrap">Neto a<br />Pagar</th>
		</tr>
		<tr>
			<th>S</th>
			<th>D</th>
			<th>L</th>
			<th>M</th>
			<th>M</th>
			<th>J</th>
			<th>V</th>
			<th>S.D.</th>
			<th>S.D.I.</th>
			<th>D.T.</th>
			<th>F.</th>
			<th>Inc.</th>
			<th>Sueldo</th>
			<th>P.D.</th>
			<th>V</th>
			<th>P.V.</th>
			{title_extra}
			<th>Total</th>
			<th>I.S.R.</th>
			<th nowrap="nowrap">Subsidio<br />al empleo</th>
			<th>Cr&eacute;dito<br />Infonavit</th>
			<th>Pensi&oacute;n<br />alimen.</th>
			<th>I.M.S.S.</th>
			<th>Total</th>
		</tr>
	</thead>
	<tbody>
		<!-- START BLOCK : row -->
		<tr id="row"{error}>
			<td align="right">{clave}</td>
			<td nowrap="nowrap">{nombre}</td>
			<td nowrap="nowrap">{puesto}</td>
			<td>{fecha_alta}</td>
			<td>{curp}</td>
			<td>{num_afiliacion}</td>
			<td align="center" nowrap="nowrap">{horario}</td>
			<td align="center" class="{color0}">{a0}</td>
			<td align="center" class="{color1}">{a1}</td>
			<td align="center" class="{color2}">{a2}</td>
			<td align="center" class="{color3}">{a3}</td>
			<td align="center" class="{color4}">{a4}</td>
			<td align="center" class="{color5}">{a5}</td>
			<td align="center" class="{color6}">{a6}</td>
			<td align="right" class="blue">{salario_diario}</td>
			<td align="right" class="blue">{salario_integrado}</td>
			<td align="center" class="blue">{dias_trabajados}</td>
			<td align="center" class="blue">{faltas}</td>
			<td align="center" class="blue">{incapacidades}</td>
			<td align="right" class="blue">{sueldo_semanal}</td>
			<td align="right" class="blue">{prima_dominical}</td>
			<td align="center" class="blue">{vacaciones}</td>
			<td align="right" class="blue">{prima_vacacional}</td>
			{importe_extra}
			<td align="right" class="blue bold">{total_percepciones}</td>
			<td align="right" class="red">{isr}</td>
			<td align="right" class="red">{subsidio_al_empleo}</td>
			<td align="right" class="red">{credito_infonavit}</td>
			<td align="right" class="red">{pension_alimenticia}</td>
			<td align="right" class="red">{imss}</td>
			<td align="right" class="red bold">{total_deducciones}</td>
			<td align="right" class="green bold">{total}</td>
		</tr>
		<!-- END BLOCK : row -->
	</tbody>
	<tfoot>
		<tr>
			<th colspan="14" align="right">Total</th>
			<th align="right" class="blue">{salario_diario}</th>
			<th align="right" class="blue">{salario_integrado}</th>
			<th align="right">&nbsp;</th>
			<th align="right">&nbsp;</th>
			<th align="right">&nbsp;</th>
			<th align="right" class="blue">{sueldo_semanal}</th>
			<th align="right" class="blue">{prima_dominical}</th>
			<th align="right" class="blue">&nbsp;</th>
			<th align="right" class="blue">{prima_vacacional}</th>
			{total_extra}
			<th align="right" class="blue">{total_percepciones}</th>
			<th align="right" class="red">{isr}</th>
			<th align="right" class="red">{subsidio_al_empleo}</th>
			<th align="right" class="red">{credito_infonavit}</th>
			<th align="right" class="red">{pension_alimenticia}</th>
			<th align="right" class="red">{imss}</th>
			<th align="right" class="red">{total_deducciones}</th>
			<th align="right">{total}</th>
		</tr>
	</tfoot>
</table>
<p>
	<button type="button" id="cancelar">Cancelar</button>
	&nbsp;&nbsp;
	<button type="button" id="registrar"{disabled}>Registrar datos</button>
</p>
