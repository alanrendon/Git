<table class="table">
	<thead>
		<tr>
			<th colspan="2" scope="col">Compañía</th>
			<th scope="col">Saldo</th>
			<th scope="col">&Uacute;ltima<br />factura</th>
		</tr>
	</thead>
	<tbody>
		<!-- START BLOCK : row -->
		<tr>
			<td>{num_cia}</td>
			<td>{nombre_cia}</td>
			<td align="right"><a href="javascript:void(0)" class="enlace blue" id="detalle" alt="{data_saldo}">{saldo}</a></td>
			<td align="center"><a href="javascript:void(0)" class="enlace red" id="detalle" alt="{data_ultima}">{ultima}</a></td>
		</tr>
		<!-- END BLOCK : row -->
	</tbody>
	<tfoot>
		<tr>
			<td colspan="2">&nbsp;</td>
			<td align="right"><b>{total}</b></td>
			<td>&nbsp;</td>
		</tr>
	</tfoot>
</table>
<p>
	<input type="submit" name="regresar" id="regresar" value="Regresar">
</p>
