<table class="table">
	<thead>
		<tr>
			<th colspan="3" align="left" scope="col"><span class="font14">{num_cia} {nombre_cia}</span></th>
		</tr>
		<tr>
			<th colspan="3" align="left" class="font12" scope="col">{num_emp} {empleado}</th>
		</tr>
		<tr>
			<th>Fecha</th>
			<th>Tipo</th>
			<th>Importe</th>
		</tr>
	</thead>
	<tbody>
		<!-- START BLOCK : row -->
		<tr id="row{id}">
			<td align="center">{fecha}</td>
			<td>{tipo}</td>
			<td align="right">{importe}</td>
		</tr>
		<!-- END BLOCK : row -->
		<tr>
			<th colspan="2" align="right">Saldo</th>
			<th align="right">{saldo}</th>
		</tr>
	</tbody>
	<tfoot>
		<tr>
			<td colspan="3" align="center">&nbsp;</td>
		</tr>
	</tfoot>
</table>
<p>
	<input type="button" name="regresar" id="regresar" value="Regresar" />
</p>
