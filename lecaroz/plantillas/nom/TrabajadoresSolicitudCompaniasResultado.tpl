<table class="tabla_captura">
	<!-- START BLOCK : cia -->
	<tr align="left">
		<th colspan="6" scope="col" class="font12">{num_cia} {nombre_cia}</th>
	</tr>
	<tr>
		<th>Fecha</th>
		<th>Ap. paterno</th>
		<th>Ap. materno</th>
		<th>Nombre</th>
		<th>Observaciones</th>
		<th><img src="/lecaroz/imagenes/tool16x16.png" width="16" height="16" /></th>
	</tr>
	<!-- START BLOCK : tipo -->
	<tr align="left">
		<th colspan="6" class="{color}">{tipo}</th>
	</tr>
	<!-- START BLOCK : trabajador -->
	<tr class="linea_{row_color}">
		<td align="center">{fecha}</td>
		<td>{ap_paterno}</td>
		<td>{ap_materno}</td>
		<td>{nombre}</td>
		<td>{observaciones}</td>
		<td align="center"><img src="/lecaroz/iconos/accept.png" alt="{value}" name="validar" width="16" height="16" id="validar" />&nbsp;<img src="/lecaroz/iconos/cancel.png" alt="{id}" name="cancelar" width="16" height="16" id="cancelar" /></td>
	</tr>
	<!-- END BLOCK : trabajador --> 
	<!-- END BLOCK : tipo -->
	<tr>
		<td colspan="6">&nbsp;</td>
	</tr>
	<!-- END BLOCK : cia -->
</table>
<p>
	<input type="button" name="regresar" id="regresar" value="Regresar" />
</p>
