<p><img src="/lecaroz/iconos/plus.png" width="16" height="16" />
	<input type="button" name="alta" id="alta" value="Alta" />
</p>
<table class="table">
	<thead>
		<tr>
			<th colspan="10" scope="col">&nbsp;</th>
		</tr>
	</thead>
	<tbody>
		<!-- START BLOCK : cia -->
		<tr>
			<th colspan="10" align="left" class="font12" scope="col">{num_cia} {nombre_cia}</th>
		</tr>
		<tr>
			<th scope="col">Expendio</th>
			<th scope="col">Contrato</th>
			<th scope="col">Pago</th>
			<th scope="col">Periodo</th>
			<th scope="col">Importe</th>
			<th scope="col">I.V.A.</th>
			<th scope="col">Retenci&oacute;n<br />I.V.A.</th>
			<th scope="col">Retenci&oacute;n<br />I.S.R.</th>
			<th scope="col">Total</th>
			<th scope="col"><img src="/lecaroz/imagenes/tool16x16.png" width="16" height="16" /></th>
		</tr>
		<!-- START BLOCK : row -->
		<tr>
			<td>{num_exp} {nombre_exp}</td>
			<td align="center">{contrato}</td>
			<td>{pago}</td>
			<td>{periodo}</td>
			<td align="right" class="green">{importe}</td>
			<td align="right" class="red">{iva}</td>
			<td align="right" class="blue">{ret_iva}</td>
			<td align="right" class="blue">{ret_isr}</td>
			<td align="right" class="green">{total}</td>
			<td align="center"><img src="/lecaroz/iconos/pencil.png" alt="{id}" name="mod" width="16" height="16" class="icono" id="mod" />&nbsp;<img src="/lecaroz/iconos/cancel.png" alt="{id}" name="baja" width="16" height="16" class="icono" id="baja" /></td>
		</tr>
		<!-- END BLOCK : row -->
		<tr>
			<th colspan="8">&nbsp;</th>
			<th align="right">{total}</th>
			<th>&nbsp;</th>
		</tr>
		<tr>
			<td colspan="10">&nbsp;</td>
		</tr>
		<!-- END BLOCK : cia -->
	</tbody>
	<tfoot>
		<tr>
			<td colspan="10">&nbsp;</td>
		</tr>
	</tfoot>
</table>
<p>
	<input type="submit" name="regresar" id="regresar" value="Regresar">
</p>
