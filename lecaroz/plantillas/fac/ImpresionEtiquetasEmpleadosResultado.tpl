<table class="tabla_captura">
	<tr>
		<th colspan="2" align="left" class="font8" scope="col"><input name="checkall" type="checkbox" id="checkall" checked="checked" />
			Seleccionar todos</th>
	</tr>
	<!-- START BLOCK : cia -->
	<tr>
		<th colspan="2" align="left" class="font12" scope="col"><input name="checkblock[]" type="checkbox" id="checkblock" value="{num_cia}" checked="checked" />
			{num_cia} {nombre_cia}</th>
	</tr>
	<!-- START BLOCK : empleado -->
	<tr class="linea_{color}">
		<td align="center"><input name="id[]" type="checkbox" id="id" value="{id}" cia="{num_cia}" checked="checked" /></td>
		<td>{num_emp} {empleado}</td>
	</tr>
	<!-- END BLOCK : empleado -->
	<tr>
		<td colspan="2">&nbsp;</td>
	</tr>
	<!-- END BLOCK : cia -->
</table>
<p>
	<input type="button" name="nuevo" id="nuevo" value="Realizar nueva búsqueda">
	<input type="button" name="imprimir" id="imprimir" value="Imprimir etiquetas" />
</p>