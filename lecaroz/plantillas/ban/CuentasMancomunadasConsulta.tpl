<p>
	<button type="button" id="alta_principal"><img src="/lecaroz/iconos/plus.png" width="16" height="16" /> Alta de cuenta principal</button>
</p>
<table class="table">
	<thead>
		<tr>
			<th colspan="3">&nbsp;</th>
		</tr>
	</thead>
	<tbody>
		<!-- START BLOCK : principal -->
		<tr>
			<th class="font12 left">{principal} {nombre_principal}</th>
			<th class="font12">{rfc_principal}</th>
			<th>
				<img src="/lecaroz/iconos/cancel.png" data-principal='{datos_principal}' width="16" height="16" class="icono" id="baja_principal" />
			</th>
		</tr>
		<tr>
			<th>Secundaria</th>
			<th>R.F.C.</th>
			<th>
				<img src="/lecaroz/iconos/plus.png" data-principal='{datos_principal}' width="16" height="16" class="icono" id="alta_secundaria" />
			</th>
		</tr>
		<!-- START BLOCK : row -->
		<tr{dif}>
			<td>{secundaria} {nombre_secundaria}</td>
			<td>{rfc_secundaria}</td>
			<td align="center">
				<img src="/lecaroz/iconos/cancel.png" data-secundaria='{datos_secundaria}' width="16" height="16" class="icono" id="baja_secundaria" />
			</td>
		</tr>
		<!-- END BLOCK : row -->
		<tr>
			<td colspan="3">&nbsp;</td>
		</tr>
		<!-- END BLOCK : principal -->
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
