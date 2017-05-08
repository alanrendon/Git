<p><img src="/lecaroz/iconos/plus.png" width="16" height="16" />
	<input type="button" name="alta" id="alta" value="Alta" />
</p>
<table class="table">
	<thead>
		<th colspan="8">&nbsp;</th>
	</thead>
	<tbody>
		<tr>
			<th scope="col">#</th>
			<th scope="col">Nombre</th>
			<th scope="col">Alias</th>
			<th scope="col">Raz&oacute;n social</th>
			<th scope="col">R.F.C.</th>
			<th scope="col">Tipo de<br />persona</th>
			<th scope="col">Tipo de<br />empresa</th>
			<th scope="col"><img src="/lecaroz/imagenes/tool16x16.png" width="16" height="16" /></th>
		</tr>
		<!-- START BLOCK : row -->
		<tr>
			<td align="right">{num_cia}</td>
			<td>{nombre_cia}</td>
			<td>{nombre_corto}</td>
			<td>{razon_social}</td>
			<td>{rfc}</td>
			<td>{tipo_persona}</td>
			<td>{tipo_cia}</td>
			<td align="center"><img src="/lecaroz/iconos/pencil.png" alt="{num_cia}" name="mod" width="16" height="16" class="icono" id="mod" /></td>
		</tr>
		<!-- END BLOCK : row -->
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
