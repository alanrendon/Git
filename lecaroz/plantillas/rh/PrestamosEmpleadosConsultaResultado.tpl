<table class="table">
	<thead>
		<tr>
			<th colspan="9" align="left" scope="col">&nbsp;</th>
		</tr>
	</thead>
	<tbody>
		<!-- START BLOCK : cia -->
		<tr>
			<th colspan="9" align="left" class="font14" scope="col">{num_cia} {nombre_cia}</th>
		</tr>
		<tr>
			<th>#</th>
			<th>Empleado</th>
			<th>Fecha prestamo</th>
			<th>Saldo</th>
			<th>Abonos</th>
			<th>Último abono</th>
			<th>Importe abono</th>
			<th>Días de atraso</th>
			<th><img src="/lecaroz/imagenes/tool16x16.png" width="16" height="16" /></th>
		</tr>
		<!-- START BLOCK : row -->
		<tr id="row{id}">
			<td align="right">{num_emp}</td>
			<td>{empleado}</td>
			<td align="center" class="red">{fecha_prestamo}</td>
			<td align="right" class="red">{saldo}</td>
			<td align="right" class="blue">{abonos}</td>
			<td align="center" class="blue">{ultimo_abono}</td>
			<td align="right" nowrap="nowrap" class="blue">{abono}</td>
			<td align="right" class="red">{dias_atraso}</td>
			<td align="center" nowrap="nowrap"><img src="/lecaroz/iconos/magnify.png" alt="{id}" name="detalle" width="16" height="16" class="icono" id="detalle" /></td>
		</tr>
		<!-- END BLOCK : row -->
		<tr>
			<th colspan="3" align="right">Totales</th>
			<th align="right"><span class="red font12">{saldo}</span></th>
			<th align="right"><span class="blue font12">{abonos}</span></th>
			<th align="right">&nbsp;</th>
			<th align="right" nowrap="nowrap">&nbsp;</th>
			<th align="right"><span class="red font12">{dias_atraso}</span></th>
			<th align="center" nowrap="nowrap">&nbsp;</th>
		</tr>
		<tr>
			<td colspan="9" align="center">&nbsp;</td>
		</tr>
		<!-- END BLOCK : cia -->
	</tbody>
	<tfoot>
		<tr>
			<td colspan="9" align="center">&nbsp;</td>
		</tr>
	</tfoot>
</table>
<p>
	<input type="button" name="regresar" id="regresar" value="Regresar" />
</p>
