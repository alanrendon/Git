<p><img src="/lecaroz/iconos/plus.png" width="16" height="16" />
	<input type="button" name="alta" id="alta" value="Alta" />
</p>
<table class="table">
	<thead>
		<th colspan="7">&nbsp;</th>
	</thead>
	<tbody>
		<!-- START BLOCK : cia -->
		<tr>
			<th colspan="7" align="left" class="font12">{num_cia} {nombre_cia}</th>
		</tr>
		<tr>
			<th scope="col">Inicio</th>
			<th scope="col">T&eacute;rmino</th>
			<th scope="col">Concepto</th>
			<th scope="col">Importe</th>
			<th scope="col">Resta por anticipar</th>
			<th scope="col">Meses<br />restantes</th>
			<th scope="col"><img src="/lecaroz/imagenes/tool16x16.png" width="16" height="16" /></th>
		</tr>
		<!-- START BLOCK : row -->
		<tr>
			<td align="center"{activo}>{inicio}</td>
			<td align="center"{activo}>{termino}</td>
			<td{activo}>{concepto}</td>
			<td align="right"{activo}>{importe}</td>
			<td align="right"{activo}>{resta}</td>
			<td align="right"{activo}>{meses}</td>
			<td align="center"><img src="/lecaroz/iconos/pencil.png" alt="{id}" name="mod" width="16" height="16" class="icono" id="mod" />&nbsp;<img src="/lecaroz/iconos/cancel.png" alt="{id}" name="baja" width="16" height="16" class="icono" id="baja" /></td>
		</tr>
		<!-- END BLOCK : row -->
		<tr>
			<td colspan="7">&nbsp;</td>
		</tr>
		<!-- END BLOCK : cia -->
	</tbody>
	<tfoot>
		<tr>
			<td colspan="8">&nbsp;</td>
		</tr>
	</tfoot>
</table>
<p>
	<input type="submit" name="regresar" id="regresar" value="Regresar">
</p>
