<p><img src="/lecaroz/iconos/plus.png" width="16" height="16" />
	<input type="button" name="alta" id="alta" value="Alta de pago" />
</p>
<table class="table">
	<thead>
	</thead>
	<tbody>
		<!-- START BLOCK : cia -->
		<tr>
			<th align="left" class="font12" colspan="11">{num_cia} {nombre_cia}</th>
		</tr>
		<tr>
			<th>Beneficiario</th>
			<th>Gasto</th>
			<th>Concepto</th>
			<th align="right">Importe</th>
			<th align="right">I.V.A.</th>
			<th align="right">Ret. I.V.A.</th>
			<th align="right">I.S.R.</th>
			<th align="right">Cedular</th>
			<th align="right">Total</th>
			<th>Tipo<br />renta</th>
			<th align="center">
				<img src="/lecaroz/iconos/info.png" width="16" height="16" />
			</th>
		</tr>
		<!-- START BLOCK : row -->
		<tr>
			<td>{num_pro} {nombre_pro}</td>
			<td>{cod} {gasto}</td>
			<td>{concepto}</td>
			<td align="right" class="green">{importe}</td>
			<td align="right" class="red">{iva}</td>
			<td align="right" class="blue">{ret_iva}</td>
			<td align="right" class="blue">{isr}</td>
			<td align="right" class="blue">{cedular}</td>
			<td align="right" class="bold">{total}</td>
			<td>{tipo_renta}</td>
			<td align="center">
				<img src="/lecaroz/iconos/pencil.png" alt="{id}" name="mod" width="16" height="16" class="icono" id="mod" />&nbsp;
				<img src="/lecaroz/iconos/cancel_round.png" alt="{id}" name="del" width="16" height="16" class="icono" id="del" />
			</td>
		</tr>
		<!-- END BLOCK : row -->
		<!-- END BLOCK : cia -->
	</tbody>
	<tfoot>
		<tr>
			<td colspan="11">&nbsp;</td>
		</tr>
	</tfoot>
</table>
<p>
	<button type="button" id="regresar">Regresar</button>&nbsp;&nbsp;
	<button type="button" id="generar">Generar pagos fijos</button>
</p>
