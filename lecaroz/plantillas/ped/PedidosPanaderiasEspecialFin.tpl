<p class="font12 bold">Pedidos generados</p>
<table class="tabla_captura">
	<tr class="linea_off">
		<th align="left" class="font12" scope="row">Folio</th>
		<td class="font12 bold">{folio}
		<input name="folio" type="hidden" id="folio" value="{folio}" /></td>
	</tr>
	<tr class="linea_on">
		<th align="left" class="font12" scope="row">Fecha</th>
		<td class="font12 bold">{fecha}</td>
	</tr>
	<tr class="linea_off">
		<th align="left" class="font12" scope="row">No. de Pedidos</th>
		<td class="font12 bold">{no_pedidos}</td>
	</tr>
</table>
<br />
<table class="tabla_captura">
	<tr>
		<th scope="col">Proveedor</th>
		<th scope="col">No. de <br />
			Pedidos</th>
		<th scope="col">Tel&eacute;fono(s)</th>
		<th scope="col">Correo(s) electr&oacute;nico(s)</th>
	</tr>
	<!-- START BLOCK : pro -->
	<tr class="linea_{row_color}">
		<td nowrap="nowrap">{num_pro} {nombre_pro}</td>
		<td align="right">{no_pedidos}</td>
		<td nowrap="nowrap">{telefonos}</td>
		<td nowrap="nowrap">{emails}</td>
	</tr>
	<!-- END BLOCK : pro -->
</table>
<p>
	<input type="button" name="reporte_cia" id="reporte_cia" value="Reporte por compa&ntilde;&iacute;a" />
&nbsp;&nbsp;
<input type="button" name="reporte_mp" id="reporte_mp" value="Reporte por producto" />
&nbsp;&nbsp;
<input type="button" name="reporte_pro" id="reporte_pro" value="Reporte por proveedor" />
</p>
<p>
	<input type="button" name="memo" id="memo" value="Memor&aacute;ndum para proveedores" />
	&nbsp;&nbsp;
	<input type="button" name="email" id="email" value="Enviar pedidos por email" />
</p>
<p>
	<input type="button" name="terminar" id="terminar" value="Terminar">
</p>
