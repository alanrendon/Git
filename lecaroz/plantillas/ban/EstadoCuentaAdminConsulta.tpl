<table class="table">
	<thead>
		<tr>
			<th colspan="10" align="left" scope="col">&nbsp;</th>
		</tr>
	</thead>
	<tbody>
		<!-- START BLOCK : cia -->
		<tr>
			<th colspan="10" align="left" class="font14" scope="col">{num_cia} {nombre_cia}</th>
		</tr>
		<tr>
			<th>Banco</th>
			<th>Fecha</th>
			<th>Conciliado</th>
			<th>Deposito</th>
			<th>Cargo</th>
			<th>Folio</th>
			<th>Beneficiario</th>
			<th>Concepto</th>
			<th>Código</th>
			<th><img src="/lecaroz/imagenes/tool16x16.png" width="16" height="16" /></th>
		</tr>
		<!-- START BLOCK : row -->
		<tr id="row{id}">
			<td align="center"><img src="/lecaroz/imagenes/{banco}16x16.png" width="16" height="16" /></td>
			<td align="center" class="green">{fecha}</td>
			<td align="center" class="orange">{conciliado}</td>
			<td align="right" class="blue">{deposito}</td>
			<td align="right" class="red">{cargo}</td>
			<td align="right">{folio}</td>
			<td nowrap="nowrap">{beneficiario}</td>
			<td>{concepto}</td>
			<td nowrap="nowrap">{codigo}</td>
			<td align="center" nowrap="nowrap"><img src="/lecaroz/iconos/pencil.png" alt="{id}" name="mod" width="16" height="16" class="icono" id="mod" />&nbsp;<img src="/lecaroz/iconos/cancel{baja_disabled}.png" alt="{id}" name="baja" width="16" height="16" class="icono" id="baja" /></td>
		</tr>
		<!-- END BLOCK : row -->
		<tr>
			<td colspan="10" align="center">&nbsp;</td>
		</tr>
		<!-- END BLOCK : cia -->
	</tbody>
	<tfoot>
		<tr>
			<td colspan="10" align="center">&nbsp;</td>
		</tr>
	</tfoot>
</table>
<p>
	<input type="button" name="regresar" id="regresar" value="Regresar" />
</p>
