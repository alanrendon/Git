<p><img src="/lecaroz/imagenes/insert16x16.png" width="16" height="16" />
	<input type="button" name="alta" id="alta" value="Alta de arrendatario" />
</p>
<!-- START BLOCK : result -->
<table width="98%" class="tabla_captura" id="arrendatarios">
	<!-- START BLOCK : arrendador -->
	<tr>
		<th colspan="15" align="left" class="font12" scope="col">{arrendador} {nombre_arrendador}<span style="float:right;" class="font8"><img src="/lecaroz/imagenes/flag_blue.png" width="16" height="16" /> Interno <img src="/lecaroz/imagenes/flag_red.png" width="16" height="16" /> Externo</span></th>
	</tr>
	<tr>
		<th>Alias</th>
		<th>Local</th>
		<th>Categor&iacute;a</th>
		<th>Arrendatario</th>
		<th>R.F.C.</th>
		<th>Periodo de<br />
			arrendamiento</th>
		<th>Renta</th>
		<th>Mantenimiento</th>
		<th>Subtotal</th>
		<th>I.V.A.</th>
		<th>Agua</th>
		<th>Retenci&oacute;n<br />
			I.V.A.</th>
		<th>Retenci&oacute;n<br />
			I.S.R.</th>
		<th>Total</th>
		<th><img src="/lecaroz/imagenes/insert16x16.png" alt="{arrendador}" name="alta_inm" width="16" height="16" id="alta_inm" /></th>
	</tr>
	<!-- START BLOCK : bloque -->
	<tbody id="bloque_{i}" index="{i}">
		<!-- START BLOCK : arrendatario -->
		<tr id="row{id}" class="linea_{color}">
			<td nowrap="nowrap" class="dragme" style="cursor:move;"><img src="/lecaroz/imagenes/flag_{flag_color}.png" width="16" height="16" />{arrendatario}</td>
			<td nowrap="nowrap">{local}</td>
			<td align="center">{categoria}</td>
			<td nowrap="nowrap">{nombre_arrendatario}</td>
			<td>{rfc}</td>
			<td align="center" nowrap="nowrap" class="green">{periodo_arrendamiento}</td>
			<td align="right" class="blue">{renta}</td>
			<td align="right" class="blue">{mantenimiento}</td>
			<td align="right" class="blue bold">{subtotal}</td>
			<td align="right" class="blue">{iva}</td>
			<td align="right" class="blue">{agua}</td>
			<td align="right" class="red">{retencion_iva}</td>
			<td align="right" class="red">{retencion_isr}</td>
			<td align="right" class="blue bold">{total}</td>
			<td align="center" nowrap="nowrap"><img src="/lecaroz/imagenes/pencil16x16{gray}.png" alt="{id}" name="mod" width="16" height="16" id="mod" />&nbsp;<img src="/lecaroz/iconos/cancel_round{gray}.png" alt="{id}" name="baja" width="16" height="16" id="baja" /></td>
		</tr>
		<!-- END BLOCK : arrendatario -->
	</tbody>
	<!-- END BLOCK : bloque -->
	<tr>
		<td colspan="15">&nbsp;</td>
	</tr>
	<!-- END BLOCK : arrendador -->
</table>
<!-- END BLOCK : result -->
<p>
	<input type="button" name="regresar" id="regresar" value="Regresar" />
	&nbsp;&nbsp;
	<input type="button" name="reporte" id="reporte" value="Reporte" />
</p>
