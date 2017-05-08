<table class="tabla_captura">
	<tr>
		<th class="font12" colspan="8" align="left" scope="col">{nombre_trabajador}</th>
	</tr>
	<tr>
		<th>Compañía</th>
		<th>Número</th>
		<th>Alta</th>
		<th>Alta<br />
		I.M.S.S.</th>
		<th>Baja</th>
		<th>Baja<br />
			I.M.S.S.</th>
		<th>Año</th>
		<th>Aguinaldo</th>
	</tr>
	<!-- START BLOCK : row -->
	<tr class="linea_{row_color}">
		<td nowrap>{num_cia} {nombre_cia}</td>
		<td align="right">{num_emp}</td>
		<td align="center" class="blue">{fecha_alta}</td>
		<td align="center" class="blue">{fecha_alta_imss}</td>
		<td align="center" class="red">{fecha_baja}</td>
		<td align="center" class="red">{fecha_baja_imss}</td>
		<td align="center" class="orange">{anio}</td>
		<td align="right" class="green">{aguinaldo}</td>
	</tr>
	<!-- END BLOCK : row -->
</table>
<p>
	<input type="button" name="cerrar" id="cerrar" value="Cerrar" />
</p>