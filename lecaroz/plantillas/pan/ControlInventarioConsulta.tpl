<input name="num_cia" type="hidden" id="num_cia" value="{num_cia}" />
<input name="fecha" type="hidden" id="fecha" value="{fecha}" />
<table class="table">
	<thead>
		<tr>
			<th scope="col">Compa&ntilde;&iacute;a</th>
			<th scope="col">Mes</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td class="bold font12">{num_cia} {nombre_cia}</td>
			<td class="bold font12">{mes} {anio}</td>
		</tr>
	</tbody>
	<tfoot>
		<tr>
			<td colspan="2">&nbsp;</td>
		</tr>
	</tfoot>
</table>
<p><img src="/lecaroz/iconos/plus.png" width="16" height="16" />
	<input type="button" name="alta" id="alta" value="Alta de productos" />
</p>
<table class="table">
	<thead>
		<tr>
			<th colspan="2" scope="col">Producto</th>
			<th scope="col">Inicio</th>
			<th scope="col">Real</th>
			<th scope="col">Virtual</th>
			<th scope="col">Diferencia</th>
		</tr>
	</thead>
	<tbody>
		<!-- START BLOCK : row -->
		<tr>
			<td>{cod}</td>
			<td>{nombre_mp}</td>
			<td align="right">{existencia_inicio}</td>
			<td align="right">{existencia_real}</td>
			<td align="right">{existencia_virtual}</td>
			<td align="right">{existencia_diferencia}</td>
		</tr>
		<!-- END BLOCK : row -->
	</tbody>
	<tfoot>
		<tr>
			<td colspan="6">&nbsp;</td>
		</tr>
	</tfoot>
</table>
<p>
	<input type="submit" name="regresar" id="regresar" value="Regresar">
</p>
