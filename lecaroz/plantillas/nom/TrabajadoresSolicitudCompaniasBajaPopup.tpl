<form name="DatosBaja" id="DatosBaja">
	<table class="tabla_captura">
		<tr>
			<th colspan="7" align="left" scope="col"><input name="idaltaimss" type="hidden" id="idaltaimss" value="{id}">
				{ap_paterno} {ap_materno} {nombre}</th>
		</tr>
		<tr>
			<td colspan="7">&nbsp;</td>
		</tr>
		<tr>
			<th>&nbsp;</th>
			<th>#</th>
			<th>Ap. paterno</th>
			<th>Ap. materno</th>
			<th>Nombre</th>
			<th>R.F.C.</th>
			<th>Alta</th>
		</tr>
		<!-- START BLOCK : trabajador -->
		<tr class="linea_{row_color}">
			<td><input type="radio" name="id" value="{id}" /></td>
			<td align="right">{num_emp}</td>
			<td>{ap_paterno}</td>
			<td>{ap_materno}</td>
			<td>{nombre}</td>
			<td>{rfc}</td>
			<td align="center">{fecha_alta}</td>
		</tr>
		<!-- END BLOCK : trabajador -->
	</table>
	<p>
		<input type="button" name="cancelar" id="cancelar" value="Cancelar" />
		&nbsp;&nbsp;
		<input type="button" name="baja" id="baja" value="Dar de baja" />
	</p>
</form>
