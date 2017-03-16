<table class="table">
	<thead>
		<tr>
			<th colspan="2">&nbsp;</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td class="bold font12">Periodo</td>
			<td class="bold font12">{fecha1} al {fecha2}</td>
		</tr>
	</tbody>
	<tfoot>
		<tr>
			<td colspan="2">&nbsp;</td>
		</tr>
	</tfoot>
</table>
<br />
<table class="table">
	<thead>
		<tr>
			<th colspan="10">&nbsp;</th>
		</tr>
	</thead>
	<tbody>
		<!-- START BLOCK : cia -->
		<tr>
			<th colspan="10" class="font12 left">{num_cia} {nombre_cia}</th>
		</tr>
		<tr>
			<th colspan="4">Dep&oacute;sitos</th>
			<th>&nbsp;</th>
			<th colspan="5">Facturas</th>
		</tr>
		<tr>
			<th>Banco</th>
			<th>Cobrado</th>
			<th>C&oacute;digo</th>
			<th>Importe</th>
			<th>&nbsp;</th>
			<th>Emisor</th>
			<th>Folio</th>
			<th>Receptor</th>
			<th>R.F.C.</th>
			<th>Importe</th>
		</tr>
		<!-- START BLOCK : dia -->
		<tr>
			<td colspan="10" class="bold font12">{fecha}</td>
		</tr>
		<!-- START BLOCK : row -->
		<tr>
			<td class="center">{banco}</td>
			<td class="center orange">{cobrado}</td>
			<td class="purple">{cod_mov} {descripcion}</td>
			<td class="right blue">{importe_deposito}</td>
			<td>&nbsp;</td>
			<td class="orange">{num_cia} {emisor}</td>
			<td class="right purple">{folio}</td>
			<td class="blue">{receptor}</td>
			<td class="blue">{rfc}</td>
			<td class="right green">{importe_factura}</td>
		</tr>
		<!-- END BLOCK : row -->
		<tr>
			<td colspan="3" class="bold right">Total depositado</td>
			<td class="bold right">{total_depositado}</td>
			<td>&nbsp;</td>
			<td colspan="4" class="bold right">Total facturado</td>
			<td class="bold right">{total_facturado}</td>
		</tr>
		<tr>
			<td colspan="3" class="bold right">Diferencia</td>
			<td class="bold right">{diferencia}</td>
			<td colspan="6">&nbsp;</td>
		</tr>
		<tr>
			<td colspan="11">&nbsp;</td>
		</tr>
		<!-- END BLOCK : dia -->
		<tr>
			<td colspan="3" class="bold font12 right">Total depositado en cuenta</td>
			<td class="bold font12 right">{total_depositado}</td>
			<td>&nbsp;</td>
			<td colspan="4" class="bold font12 right">Total facturado en cuenta</td>
			<td class="bold font12 right">{total_facturado}</td>
		</tr>
		<tr>
			<td colspan="10">&nbsp;</td>
		</tr>
		<!-- END BLOCK : cia -->
	</tbody>
	<tfoot>
		<tr>
			<th colspan="3" class="bold font12 right">Total general depositado</th>
			<th class="bold font12 right">{total_depositado}</th>
			<th>&nbsp;</th>
			<th colspan="4" class="bold font12 right">Total general facturado</th>
			<th class="bold font12 right">{total_facturado}</th>
		</tr>
	</tfoot>
</table>
<p>
	<button type="button" id="regresar" />Regresar</button>
	&nbsp;&nbsp;
	<button type="button" id="reporte" />Reporte para impreso</button>
	&nbsp;&nbsp;
	<button type="button" id="exportar" />Exportar a CSV</button>
</p>
