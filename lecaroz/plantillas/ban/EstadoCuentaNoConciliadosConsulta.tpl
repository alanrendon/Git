<table class="table">
	<thead>
		<tr>
			<th colspan="8" align="left" scope="col">&nbsp;</th>
		</tr>
	</thead>
	<tbody>
		<!-- START BLOCK : cia -->
		<tr>
			<th colspan="8" align="left" class="font14" scope="col">{num_cia} {nombre_cia}{cuentas}</th>
		</tr>
		<tr>
			<th>Banco</th>
			<th>Fecha</th>
			<th>Deposito</th>
			<th>Cargo</th>
			<th>Folio</th>
			<th>Beneficiario</th>
			<th>Concepto</th>
			<th>Código</th>
		</tr>
		<!-- START BLOCK : row -->
		<tr id="row{id}">
			<td align="center"><img src="/lecaroz/imagenes/{banco}16x16.png" width="16" height="16" /></td>
			<td align="center" class="green">{fecha}</td>
			<td align="right" class="blue">{deposito}</td>
			<td align="right" class="red">{cargo}</td>
			<td align="right">{folio}</td>
			<td nowrap="nowrap">{beneficiario}</td>
			<td>{concepto}</td>
			<td nowrap="nowrap">{codigo}</td>
		</tr>
		<!-- END BLOCK : row -->
		<tr>
			<th colspan="2" align="right">Total</th>
			<th align="right"><span class="blue">{depositos}</span></th>
			<th align="right"><span class="red">{cargos}</span></th>
			<th colspan="4" align="right">&nbsp;</th>
		</tr>
		<tr>
			<td colspan="8" align="center">&nbsp;</td>
		</tr>
		<!-- END BLOCK : cia -->
	</tbody>
	<tfoot>
		<tr>
			<td height="16" colspan="8" align="center">&nbsp;</td>
		</tr>
	</tfoot>
</table>
<p>
	<input type="button" name="regresar" id="regresar" value="Regresar" />
&nbsp;&nbsp;
<input type="button" name="listado" id="listado" value="Listado para imprimir" />
&nbsp;&nbsp;
<input type="button" name="exportar" id="exportar" value="Exportar a Excel" />
</p>
