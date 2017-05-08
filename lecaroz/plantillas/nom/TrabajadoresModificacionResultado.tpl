<form name="Datos" id="Datos">
	<table class="tabla_captura">
		<!-- START BLOCK : cia -->
		<tr>
			<th colspan="6" align="left" scope="col">{num_cia} {nombre_cia}</th>
		</tr>
		<tr>
			<th>#</th>
			<th>Trabajador</th>
			<th>R.F.C.</th>
			<th>Ingreso</th>
			<th>Status</th>
			<th>Usuario</th>
		</tr>
		<!-- START BLOCK : trabajador -->
		<tr class="linea_{row_color}" idemp="{id}">
			<td align="right">{num_emp}</td>
			<td>{nombre_trabajador}</td>
			<td>{rfc}</td>
			<td align="center">{fecha_alta}</td>
			<td align="center" class="{status_color}">{status}</td>
			<td>{usuario}</td>
		</tr>
		<!-- END BLOCK : trabajador -->
		<tr>
			<td colspan="6">&nbsp;</td>
		</tr>
		<!-- END BLOCK : cia -->
	</table>
	<p>
		<input type="button" name="regresar" id="regresar" value="Regresar" />
	</p>
</form>