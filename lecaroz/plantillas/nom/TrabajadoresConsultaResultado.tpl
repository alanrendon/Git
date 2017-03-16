<p><img src="/lecaroz/iconos/plus.png" width="16" height="16">
	<input type="button" name="alta" id="alta" value="Alta de trabajador"{alta_disabled} />
</p>
<table class="tabla_captura">
	<!-- START BLOCK : cia -->
	<tr>
		<th colspan="11" align="left" class="font14" scope="col"><span style="float:right;" class="font10">Trabajadores: {numero_trabajadores}<br />Afiliados: {afiliados}</span>{num_cia} {nombre_cia}</th>
	</tr>
	<tr>
		<th colspan="11" align="left" class="font10" scope="col">
			<img src="/lecaroz/iconos/fingerprint_green.png" width="16" height="16" alt=""> Chequeos correctos,
			<img src="/lecaroz/iconos/fingerprint_yellow.png" width="16" height="16" alt=""> No ha checado en m&aacute;s de 20 d&iacute;as,
			<img src="/lecaroz/iconos/fingerprint_red.png" width="16" height="16" alt=""> No ha ingresado el id, el id es incorrecto o nunca ha checado
		</th>
	</tr>
	<tr>
		<th>#</th>
		<th>No. emp.</th>
		<th>Trabajador</th>
		<th>Puesto</th>
		<th>Turno</th>
		<th>No. afiliación</th>
		<th>Antigüedad</th>
		<th>Documentos</th>
		<th>Saldo</th>
		<th>Status</th>
		<th><img src="/lecaroz/iconos/plus{no_alta}.png" alt="{num_cia}" name="alta_cia" width="16" height="16" id="alta_cia" title="Alta"></th>
	</tr>
	<!-- START BLOCK : trabajador -->
	<tr class="linea_{row_color}">
		<td align="right" nowrap="nowrap" class="light_gray">{num}</td>
		<td align="right" nowrap="nowrap"{no_firma}>{num_emp}</td>
		<td nowrap="nowrap" class="{trabajador_color}">{nombre_trabajador}</td>
		<td nowrap="nowrap">{puesto}</td>
		<td nowrap="nowrap">{turno}</td>
		<td nowrap="nowrap">{num_afiliacion}</td>
		<td nowrap="nowrap">{fecha_alta}</td>
		<td nowrap="nowrap">{documentos}</td>
		<td align="right" nowrap="nowrap" class="red" style="overflow:hidden;">{saldo}</td>
		<td nowrap="nowrap" class="{status_color}">{status}</td>
		<td nowrap="nowrap">
			<img src="/lecaroz/iconos/magnify.png" alt="{id}" name="ver" width="16" height="16" id="ver" />
			<img src="/lecaroz/iconos/article{con_documentos}.png" alt="{id}" name="documentos" width="16" height="16" id="documentos" />
			<img src="/lecaroz/iconos/burst.png" alt="{id}" name="cursos" width="16" height="16" id="cursos" />
			<img src="/lecaroz/iconos/pencil{no_modificar}.png" alt="{id}" name="modificar" width="16" height="16" id="modificar" title="Modificar" />
			<img src="/lecaroz/iconos/cancel_round{no_baja}.png" alt="{id}" name="baja" width="16" height="16" id="baja" title="Baja" />
			<img src="/lecaroz/imagenes/pension{no_pension}.png" alt="{id}" name="pension" width="16" height="16" id="pension" title="Pensión">
			<img src="/lecaroz/iconos/accept_green{no_reactivar}.png" alt="{id}" name="reactivar" width="16" height="16" id="reactivar" title="Reactivar" />
		</td>
	</tr>
	<!-- END BLOCK : trabajador -->
	<tr>
		<td colspan="11">&nbsp;</td>
	</tr>
	<!-- END BLOCK : cia -->
</table>
<p>
	<input type="button" name="regresar" id="regresar" value="Regresar" />
</p>
