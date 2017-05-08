<div style="width:800px; height:300px; overflow:auto;">
	<p class="bold font12 center">Existen diferencias entre los saldos del sistema y los saldos del banco. Haga click en el bot&oacute;n <kbd>Aceptar</kbd> para poder continuar.</p>
	<table align="center" class="table">
		<thead>
			<tr>
				<th colspan="8" scope="col">&nbsp;</th>
			</tr>
		</thead>
		<tbody>
			<!-- START BLOCK : banco -->
			<tr>
				<th colspan="8" align="left" class="font12" scope="col">{banco}</th>
			</tr>
			<tr>
				<th>Compa&ntilde;&iacute;a</th>
				<th>Cuenta</th>
				<th>Saldo<br />
					en sistema</th>
				<th>Dep&oacute;sitos<br />
					pendientes</th>
				<th>Cargos<br />
					pendientes</th>
				<th>Saldo<br />
					total</th>
				<th>Saldo en<br />
					banco</th>
				<th>Diferencia</th>
			</tr>
			<!-- START BLOCK : row -->
			<tr>
				<td>{num_cia} {nombre_cia}</td>
				<td>{cuenta}</td>
				<td align="right" class="bold green">{saldo_sistema}</td>
				<td align="right" class="blue">{abonos_pendientes}</td>
				<td align="right" class="red">{cargos_pendientes}</td>
				<td align="right" class="bold green">{saldo_total}</td>
				<td align="right" class="bold orange">{saldo_banco}</td>
				<td align="right" class="bold">{diferencia}</td>
			</tr>
			<!-- END BLOCK : row -->
			<tr>
				<th colspan="2" align="right">Totales</th>
				<th align="right"><span class="green">{saldo_sistema}</span></th>
				<th align="right"><span class="blue">{abonos_pendientes}</span></th>
				<th align="right"><span class="red">{cargos_pendientes}</span></th>
				<th align="right"><span class="green">{saldo_total}</span></th>
				<th align="right"><span class="orange">{saldo_banco}</span></th>
				<th align="right">&nbsp;</th>
			</tr>
			<tr>
				<td colspan="8">&nbsp;</td>
			</tr>
			<!-- END BLOCK : banco -->
		</tbody>
		<tfoot>
			<tr>
				<td colspan="8">&nbsp;</td>
			</tr>
		</tfoot>
	</table>
</div>
