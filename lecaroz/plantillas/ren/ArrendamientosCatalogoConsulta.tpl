<p><img src="/lecaroz/imagenes/insert16x16.png" width="16" height="16" />
	<input type="button" name="alta" id="alta" value="Alta de arrendamiento" />
</p>
<!-- START BLOCK : result -->
<table width="98%" class="tabla_captura">
	<!-- START BLOCK : cia -->
	<tr>
		<th colspan="16" align="left" class="font12" scope="col">{num_cia} {nombre_cia}</th>
		</tr>
	<tr>
		<th>Alias</th>
		<th>Arrendador</th>
		<th>R.F.C.</th>
		<th>C.U.R.P.</th>
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
		<th>Efectivo</th>
		<th>Gran<br />
			total</th>
		<th><img src="/lecaroz/imagenes/insert16x16.png" alt="{num_cia}" name="alta_inm" width="16" height="16" id="alta_arr" /></th>
		</tr>
	<!-- START BLOCK : arrendamiento -->
	<tr id="row{id}" class="linea_{color}">
		<td nowrap="nowrap">{arrendamiento}</td>
		<td nowrap="nowrap">{nombre_arrendador}</td>
		<td>{rfc}</td>
		<td nowrap="nowrap" class="green">{curp}</td>
		<td align="center" nowrap="nowrap" class="green">{periodo_arrendamiento}</td>
		<td align="right" class="blue">{renta}</td>
		<td align="right" class="blue">{mantenimiento}</td>
		<td align="right" class="blue bold">{subtotal}</td>
		<td align="right" class="blue bold">{iva}</td>
		<td align="right" class="blue">{agua}</td>
		<td align="right" class="red">{retencion_iva}</td>
		<td align="right" class="red">{retencion_isr}</td>
		<td align="right" class="blue bold">{total}</td>
		<td align="right" class="blue bold">{renta_efectivo}</td>
		<td align="right" class="blue bold">{gran_total}</td>
		<td align="center" nowrap="nowrap"><img src="/lecaroz/imagenes/pencil16x16.png" alt="{id}" name="mod" width="16" height="16" id="mod" />&nbsp;<img src="/lecaroz/iconos/cancel_round.png" alt="{id}" name="baja" width="16" height="16" id="baja" /></td>
		</tr>
	<!-- END BLOCK : arrendamiento -->
	<tr>
		<td colspan="16">&nbsp;</td>
		</tr>
	<!-- END BLOCK : cia -->
	</table>
<!-- END BLOCK : result -->
<p>
	<input type="button" name="regresar" id="regresar" value="Regresar" />
&nbsp;&nbsp;
<input type="button" name="listado" id="listado" value="Listado" />
</p>