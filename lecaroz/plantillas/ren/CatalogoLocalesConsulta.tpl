<p><img src="/lecaroz/imagenes/insert16x16.png" width="16" height="16" />
	<input type="button" name="alta" id="alta" value="Alta de local" />
</p>
<!-- START BLOCK : result -->
<table width="98%" class="tabla_captura">
	<!-- START BLOCK : arrendador -->
	<tr>
		<th colspan="8" align="left" class="font12" scope="col">{arrendador} {nombre_arrendador}</th>
		</tr>
	<tr>
		<th>#</th>
		<th>Alias</th>
		<th>Categor&iacute;a</th>
		<th>Domicilio</th>
		<th>Tipo</th>
		<th nowrap>Cuenta predial</th>
		<th nowrap>Superficie (m&sup2;)</th>
		<th><img src="/lecaroz/imagenes/insert16x16.png" alt="{arrendador}" name="alta_inm" width="16" height="16" id="alta_inm" /></th>
		</tr>
	<!-- START BLOCK : arrendatario -->
	<tr id="row{id}" class="linea_{color}">
		<td align="right">{local}</td>
		<td>{alias_local}</td>
		<td align="center">{categoria}</td>
		<td>{domicilio}</td>
		<td>{tipo_local}</td>
		<td class="blue">{cuenta_predial}</td>
		<td align="right" class="blue">{superficie}</td>
		<td align="center" nowrap="nowrap"><img src="/lecaroz/imagenes/pencil16x16.png" alt="{id}" name="mod" width="16" height="16" id="mod" />&nbsp;<img src="/lecaroz/iconos/cancel_round{gray}.png" alt="{id}" name="baja" width="16" height="16" id="baja" /></td>
		</tr>
	<!-- END BLOCK : arrendatario -->
	<tr>
		<td colspan="8">&nbsp;</td>
		</tr>
	<!-- END BLOCK : arrendador -->
	</table>
<!-- END BLOCK : result -->
<p>
	<input type="button" name="regresar" id="regresar" value="Regresar" />
&nbsp;&nbsp;
<input type="button" name="listado" id="listado" value="Listado" />
</p>
