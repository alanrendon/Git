<div style="height: 300px; overflow:auto;">
	<!-- START BLOCK : reimpresos -->
	<p class="bold blue">Las siguientes polizas estan listas para imprimir, por favor vaya al programa de impresión de cheques y proceda a generarlas.</p>
	<table align="center" class="table">
		<thead>
			<tr>
				<th>Compañía</th>
				<th>Banco</th>
				<th>Folio</th>
				<th>Fecha</th>
				<th>Beneficiario</th>
				<th>Importe</th>
			</tr>
		</thead>
		<tbody>
			<!-- START BLOCK : reimpreso -->
			<tr>
				<td>{num_cia} {nombre_cia}</td>
				<td>{banco}</td>
				<td align="right">{folio}</td>
				<td align="center">{fecha}</td>
				<td>{beneficiario}</td>
				<td align="right">{importe}</td>
			</tr>
			<!-- END BLOCK : reimpreso -->
		</tbody>
		<tfoot>
			<tr>
				<td colspan="6">{polizas} poliza(s) a reimprimir</td>
			</tr>
		</tfoot>
	</table>
	<!-- END BLOCK : reimpresos -->
	<!-- START BLOCK : not_found -->
	<p class="bold red">Las siguientes polizas no fueron encontradas en el sistema.</p>
	<table align="center" class="table">
		<thead>
			<tr>
				<th>Compañía</th>
				<th>Banco</th>
				<th>Folio</th>
				<th>Fecha</th>
			</tr>
		</thead>
		<tbody>
			<!-- START BLOCK : nf -->
			<tr>
				<td>{num_cia} {nombre_cia}</td>
				<td>{banco}</td>
				<td align="right">{folio}</td>
				<td align="center">{fecha}</td>
			</tr>
			<!-- END BLOCK : nf -->
		</tbody>
		<tfoot>
			<tr>
				<td colspan="4">{polizas} poliza(s) no encontrada(s)</td>
			</tr>
		</tfoot>
	</table>
	<!-- END BLOCK : not_found -->
</div>
