<input name="num_pro" type="hidden" id="num_pro" value="{num_pro}">
<table class="table">
	<thead>
		<tr>
			<th>&nbsp;</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td class="font12 bold">{num_pro} {nombre_pro}</td>
		</tr>
	</tbody>
	<tfoot>
		<tr>
			<td>&nbsp;</td>
		</tr>
	</tfoot>
</table>
<p>
	<button type="button" id="alta">
		<img src="/lecaroz/iconos/plus.png" width="16" height="16" />
		Alta
	</button>
</p>
<table class="table">
	<thead>
		<tr>
			<th colspan="2">Compa&ntilde;&iacute;a</th>
			<th>Referencia</th>
			<th><img src="/lecaroz/imagenes/tool16x16.png" width="16" height="16" /></th>
		</tr>
	</thead>
	<tbody>
		<!-- START BLOCK : row -->
		<tr>
			<td>{num_cia}</td>
			<td>{nombre_cia}</td>
			<td>{referencia}</td>
			<td align="center">
				<img src="/lecaroz/iconos/pencil.png" alt="{id}" width="16" height="16" class="icono" id="mod" />
				&nbsp;<img src="/lecaroz/iconos/cancel.png" alt="{id}" width="16" height="16" class="icono" id="baja" />
			</td>
		</tr>
		<!-- END BLOCK : row -->
	</tbody>
	<tfoot>
		<tr>
			<td colspan="4">&nbsp;</td>
		</tr>
	</tfoot>
</table>
<p>
	<button type="button" id="regresar">Regresar</button>
	&nbsp;&nbsp;<button type="button" id="importar">Importar archivo CSV</button>
</p>
