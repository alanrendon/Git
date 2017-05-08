<table class="tabla_captura">
	<!-- START BLOCK : cia -->
	<tr>
		<th colspan="7" align="left" class="font14" scope="col">{num_cia} {nombre_cia}</th>
	</tr>
	<tr>
		<th>#</th>
		<th>No. emp.</th>
		<th>Trabajador</th>
		<th>Puesto</th>
		<th>Turno</th>
		<th>Saldo</th>
		<th>Status</th>
	</tr>
	<!-- START BLOCK : trabajador -->
	<tr class="linea_{row_color}">
		<td align="right" nowrap="nowrap" class="light_gray">{num}</td>
		<td align="right" nowrap="nowrap"{no_firma}>{num_emp}</td>
		<td nowrap="nowrap" class="{trabajador_color}">{nombre_trabajador}</td>
		<td nowrap="nowrap">{puesto}</td>
		<td nowrap="nowrap">{turno}</td>
		<td align="right" nowrap="nowrap" class="red" style="overflow:hidden;">{saldo}</td>
		<td nowrap="nowrap" class="{status_color}">{status}</td>
	</tr>
	<!-- END BLOCK : trabajador -->
	<tr>
		<td colspan="7">&nbsp;</td>
	</tr>
	<!-- END BLOCK : cia -->
</table>
<p>
	<input type="button" name="regresar" id="regresar" value="Regresar" />
</p>
