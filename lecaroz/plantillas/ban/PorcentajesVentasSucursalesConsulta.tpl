<p>
	<button type="button" id="alta_matriz"><img src="/lecaroz/iconos/plus.png" width="16" height="16" /> Alta de matriz</button>
</p>
<table class="table">
	<thead>
		<tr>
			<th colspan="3">&nbsp;</th>
		</tr>
	</thead>
	<tbody>
		<!-- START BLOCK : matriz -->
		<tr>
			<th class="font12 left">{matriz} {nombre_matriz}</th>
			<th class="font12 right">%{porcentaje}</th>
			<th>
				<img src="/lecaroz/iconos/cancel.png" data-matriz='{datos_matriz}' width="16" height="16" class="icono" id="baja_matriz" />
			</th>
		</tr>
		<tr>
			<th>Sucursal</th>
			<th>Porcentaje</th>
			<th>
				<img src="/lecaroz/iconos/plus.png" data-matriz='{datos_matriz}' width="16" height="16" class="icono" id="alta_sucursal" />
			</th>
		</tr>
		<!-- START BLOCK : row -->
		<tr>
			<td>{sucursal} {nombre_sucursal}</td>
			<td align="right">%{porcentaje}</td>
			<td align="center">
				<img src="/lecaroz/iconos/pencil.png" data-sucursal='{datos_sucursal}' width="16" height="16" class="icono" id="mod_sucursal" />&nbsp;
				<img src="/lecaroz/iconos/cancel.png" data-sucursal='{datos_sucursal}' width="16" height="16" class="icono" id="baja_sucursal" />
			</td>
		</tr>
		<!-- END BLOCK : row -->
		<tr>
			<td colspan="3">&nbsp;</td>
		</tr>
		<!-- END BLOCK : matriz -->
	</tbody>
	<tfoot>
		<tr>
			<td colspan="3">&nbsp;</td>
		</tr>
	</tfoot>
</table>
<p>
	<input type="submit" name="regresar" id="regresar" value="Regresar">
</p>
