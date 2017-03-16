<form name="datos-form" class="FormValidator" id="datos-form">
	<input name="num_cia" type="hidden" id="num_cia" value="{num_cia}" />
	<input name="fecha" type="hidden" id="fecha" value="{fecha}" />
	<table id="datos-table" class="table">
		<thead>
			<tr>
				<th>Compa&ntilde;&iacute;a</th>
				<th>Banco</th>
				<th>Folio</th>
				<th>Fecha<br />cheque</th>
				<th>Beneficiario</th>
				<th>Factura</th>
				<th>Fecha<br />factura</th>
				<th>Gasto</th>
				<th>Importe</th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="9">&nbsp;</td>
			</tr>
		</tfoot>
	</table>
	<p>
		<input name="cancelar" type="button" id="cancelar" value="Cancelar" />
		<input name="registrar" type="button" id="registrar" value="Generar reporte" />
	</p>
</form>