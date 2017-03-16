<p><img src="/lecaroz/iconos/plus.png" width="16" height="16" />&nbsp;
	<input type="button" name="alta" id="alta" value="Nueva solicitud" />
</p>
<table align="center" class="table">
	<thead>
		<tr>
			<th colspan="5" align="left" class="font12" scope="col">&nbsp;</th>
		</tr>
	</thead>
	<tbody>
		<!-- START BLOCK : cia -->
		<tr>
			<th colspan="5" align="left" class="font12" scope="col">{num_cia} {nombre_cia}</th>
		</tr>
		<tr>
			<th>Descripción de solicitud</th>
			<th>Solicitado</th>
			<th>Autorizado</th>
			<th>Aclarado</th>
			<th><img src="/lecaroz/imagenes/tool16x16.png" width="16" height="16" /></th>
		</tr>
		<!-- START BLOCK : row -->
		<tr>
			<td>{descripcion}</td>
			<td align="center" class="red">{solicitado}</td>
			<td align="center" class="orange">{autorizado}</td>
			<td align="center" class="green">{aclarado}</td>
			<td align="center" nowrap="nowrap"><img src="/lecaroz/iconos/pencil{mod_disabled}.png" alt="{id}" name="mod" class="icono" width="16" height="16" id="mod" />&nbsp;<img src="/lecaroz/iconos/cancel_round{baja_disabled}.png" name="baja" class="icono" width="16" height="16" id="baja" /></td>
		</tr>
		<!-- END BLOCK : row -->
		<tr>
			<td colspan="5">&nbsp;</td>
		</tr>
		<!-- END BLOCK : cia -->
	</tbody>
	<tfoot>
		<tr>
			<td colspan="5">&nbsp;</td>
		</tr>
	</tfoot>
</table>
<p>
	<input type="button" name="regresar" id="regresar" value="Regresar" />
</p>
