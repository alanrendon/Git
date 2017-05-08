<p><img src="/lecaroz/iconos/plus.png" width="16" height="16" />
	<input type="button" name="alta" id="alta" value="Alta de curso" />
</p>
<!-- START BLOCK : result -->
<table class="tabla_captura">
	<tr>
		<th>Nombre</th>
		<th>Descripci&oacute;n</th>
		<th>Periodo de<br />
			aplicaci&oacute;n</th>
		<th><img src="/lecaroz/imagenes/tool16x16.png" width="16" height="16" /></th>
	</tr>
	<!-- START BLOCK : curso -->
	<tr id="row{id}" class="linea_{color}">
		<td valign="top" nowrap="nowrap">{nombre_curso}</td>
		<td valign="top" nowrap="nowrap">{descripcion_curso}</td>
		<td align="center" valign="top" nowrap="nowrap">{periodo_aplicacion}</td>
		<td align="center" valign="top" nowrap="nowrap"><img src="/lecaroz/iconos/accept{blank}.png" alt="{id}" name="status" width="16" height="16" id="status" />&nbsp;<img src="/lecaroz/iconos/magnify.png" alt="{id}" name="ver" width="16" height="16" id="ver" />&nbsp;<img src="/lecaroz/imagenes/pencil16x16.png" alt="{id}" name="mod" width="16" height="16" id="mod" />&nbsp;<img src="/lecaroz/iconos/cancel_round.png" alt="{id}" name="baja" width="16" height="16" id="baja" /></td>
		</tr>
	<!-- END BLOCK : curso -->
	</table>
<!-- END BLOCK : result -->
<p>
	<input type="button" name="regresar" id="regresar" value="Regresar" />
</p>