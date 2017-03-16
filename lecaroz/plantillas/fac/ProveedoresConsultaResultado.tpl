<p><img src="/lecaroz/imagenes/insert16x16.png" width="16" height="16" /> &nbsp;
	<input type="button" name="alta" id="alta" value="Alta de proveedor" />
</p>
<table class="tabla_captura">
	<tr>
		<th scope="col">#</th>
		<th scope="col">Proveedor</th>
		<th scope="col">R.F.C.</th>
		<th scope="col">Contacto</th>
		<th scope="col">Tel&eacute;fono</th>
		<th scope="col">Correo electr&oacute;nico</th>
		<th scope="col">Forma<br />
			de pago</th>
		<th scope="col"><img src="/lecaroz/imagenes/tool16x16.png" width="16" height="16" /></th>
	</tr>
	<!-- START BLOCK : row -->
	<tr class="linea_{row_color}">
		<td align="right" nowrap="nowrap">{num_pro}</td>
		<td nowrap="nowrap">{nombre_pro}</td>
		<td nowrap="nowrap">{rfc}</td>
		<td nowrap="nowrap">{contacto}</td>
		<td nowrap="nowrap">{telefonos}</td>
		<td nowrap="nowrap">{emails}</td>
		<td nowrap="nowrap">{forma_pago}</td>
		<td nowrap="nowrap"><img src="/lecaroz/iconos/info.png" alt="{num_pro}" name="info" width="16" height="16" id="info" />&nbsp;<img src="/lecaroz/iconos/pencil.png" alt="{num_pro}" name="mod" width="16" height="16" id="mod" /></td>
	</tr>
	<!-- END BLOCK : row -->
</table>
<p>
	<input type="button" name="regresar" id="regresar" value="Regresar" />
</p>
