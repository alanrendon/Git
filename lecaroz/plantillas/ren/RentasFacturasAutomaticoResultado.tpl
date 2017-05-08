<input name="anio" type="hidden" id="anio" value="{anio}">
<input name="mes" type="hidden" id="mes" value="{mes}">
<table class="tabla_captura">
	<tr class="linea_off">
		<th align="left" class="font14" scope="row">A&ntilde;o</th>
		<td class="font14 bold">{anio}</td>
	</tr>
	<tr class="linea_on">
		<th align="left" class="font14" scope="row">Mes</th>
		<td class="font14 bold">{mes_escrito}</td>
	</tr>
</table>
<br />
<table class="tabla_captura">
	<tr>
		<th colspan="10" align="left" class="font8" scope="col"><input name="checkall" type="checkbox" id="checkall" checked="checked" />
			Seleccionar todos</th>
	</tr>
	<!-- START BLOCK : arrendador -->
	<tr>
		<th colspan="10" align="left" class="font12" scope="col">{arrendador} {nombre_arrendador} </th>
	</tr>
	<tr>
		<th><input name="checkarrendador" type="checkbox" id="checkarrendador" value="{arrendador}" checked /></th>
		<th>Arrendatario</th>
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
	</tr>
	<!-- START BLOCK : bloque -->
	<tr>
		<th colspan="10" align="left">{bloque}</th>
	</tr>
	<!-- START BLOCK : arrendatario -->
	<tr id="row" class="linea_{color}">
		<td align="center"><input name="id[]" type="checkbox" id="id" value="{id}" checked arrendador="{arrendador}"{disabled} /></td>
		<td nowrap="nowrap" class="{class}">{arrendatario} {nombre_arrendatario}</td>
		<td align="right" class="blue">{renta}</td>
		<td align="right" class="green">{mantenimiento}</td>
		<td align="right" class="blue">{subtotal}</td>
		<td align="right" class="red">{iva}</td>
		<td align="right" class="blue">{agua}</td>
		<td align="right" class="red">{retencion_iva}</td>
		<td align="right" class="red">{retencion_isr}</td>
		<td align="right" class="bold blue">{total}</td>
	</tr>
	<!-- END BLOCK : arrendatario --> 
	<!-- END BLOCK : bloque -->
	<tr>
		<td colspan="10">&nbsp;</td>
	</tr>
	<!-- END BLOCK : arrendador -->
</table>
<br />
<table class="tabla_captura">
	<tr>
		<td class="bold blue">Factura generada</td>
	</tr>
	<tr>
		<td class="underline green">Contratos vencidos</td>
	</tr>
	<tr>
		<td class="underline red">Sin incremento anual</td>
	</tr>
</table>
<p>
	<input name="regresar" type="button" id="regresar" value="Regresar" />
	&nbsp;&nbsp;
	<input name="generar" type="button" id="generar" value="Generar recibos de arrendamiento" />
</p>