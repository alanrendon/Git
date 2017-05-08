<p><img src="/lecaroz/iconos/plus.png" width="16" height="16" />&nbsp;
	<input type="button" name="alta_riesgo" id="alta_riesgo" value="Alta de riesgo de trabajo" />
</p>
<table class="table">
	<thead>
		<tr>
			<th colspan="4" scope="col">&nbsp;</th>
		</tr>
	</thead>
	<tbody>
		<!-- START BLOCK : cia -->
		<tr>
			<th colspan="4" align="left" nowrap="nowrap" class="font14">{num_cia} {nombre_cia}</th>
		</tr>
		<tr>
			<th>Folio</th>
			<th>Trabajador</th>
			<th>Fecha</th>
			<th><img src="/lecaroz/imagenes/tool16x16.png" width="16" height="16" /></th>
		</tr>
		<!-- START BLOCK : row -->
		<tr>
			<td align="right">{folio}</td>
			<td nowrap="nowrap">{nombre_trabajador}</td>
			<td align="center">{fecha}</td>
			<td align="center" nowrap="nowrap"><img src="/lecaroz/iconos/info.png" alt="{id}" name="info" class="icono" width="16" height="16" id="info" />&nbsp;&nbsp;<img src="/lecaroz/iconos/magnify.png" alt="{id}" name="ver" class="icono" width="16" height="16" id="ver" />&nbsp;&nbsp;<img src="/lecaroz/iconos/download.png" alt="{id}" name="download" class="icono" width="16" height="16" id="download" />&nbsp;&nbsp;<img src="/lecaroz/iconos/pencil.png" alt="{id}" name="mod" class="icono" width="16" height="16" id="mod" />&nbsp;&nbsp;<img src="/lecaroz/iconos/cancel.png" alt="{id}" name="baja" class="icono" width="16" height="16" id="baja" /></td>
		</tr>
		<!-- END BLOCK : row -->
		<tr>
			<td colspan="4">&nbsp;</td>
		</tr>
		<!-- END BLOCK : cia -->
	</tbody>
	<tfoot>
		<tr>
			<td colspan="4">&nbsp;</td>
		</tr>
	</tfoot>
</table>
<p>
	<input type="button" name="regresar" id="regresar" value="Regresar" />
</p>
