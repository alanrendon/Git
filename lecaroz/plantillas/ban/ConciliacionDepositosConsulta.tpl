<form name="consulta_form" class="FormValidator" id="consulta_form">
	<input name="banco" type="hidden" id="banco" value="{banco}">
	<table class="table">
		<tbody>
			<tr>
				<th colspan="2">&nbsp;</th>
			</tr>
		</tbody>
		<tbody>
			<tr>
				<td class="bold font14">Banco</td>
				<td class="bold font14">{nombre_banco}</td>
			</tr>
		</tbody>
		<tfoot>
			<tr>
				<td colspan="2">&nbsp;</td>
			</tr>
		</tfoot>
	</table>
	<br>
	<table class="table">
		<thead>
			<tr>
				<th class="left" colspan="5">
					<input type="checkbox" id="check_all"> Seleccionar todo
				</th>
			</tr>
		</thead>
		<tbody>
			<!-- START BLOCK : cia -->
			<tr>
				<th class="font12 left" colspan="5">
					{num_cia} {nombre_cia}
					&nbsp;&nbsp;&nbsp;<span style="float:right;">Cuenta: {cuenta}</span>
				</th>
			</tr>
			<tr>
				<th align="center">
					<input type="checkbox" id="check_cia_{num_cia}" value="{num_cia}">
				</th>
				<th>Fecha</th>
				<th class="right">Importe</th>
				<th class="left">C&oacute;digo</th>
				<th class="left">Concepto</th>
			</tr>
			<!-- START BLOCK : deposito -->
			<tr id="row_{index}">
				<td class="center">
					<input name="deposito[]" type="checkbox" id="deposito_{index}" data-index="{index}" data-cia="{num_cia}" data-importe="{importe}" value='{deposito_data}'>
					<input name="tarjeta[]" type="hidden" id="tarjeta_{index}" data-index="{index}" data-cia="{num_cia}" value='' disabled="">
				</td>
				<td class="center">
					<input name="fecha[]" type="text" class="validate focus toDate center" id="fecha_{index}" size="10" maxlength="10" value="{fecha}" disabled="">
				</td>
				<td class="bold right" id="importe_{index}">{importe}</td>
				<td>
					<select name="cod_mov[]" id="cod_mov_{index}" data-index="{index}" disabled="">
						<!-- START BLOCK : cod_mov -->
						<option value="{value}" class="{class}">{text}</option>
						<!-- END BLOCK : cod_mov -->
					</select>
				</td>
				<td>
					<input name="concepto[]" type="text" class="validate toUpper cleanText" id="concepto_{index}" size="30" value="{concepto}" disabled="">
				</td>
			</tr>
			<!-- END BLOCK : deposito -->
			<tr>
				<th class="font12 right" colspan="2">Total depositado:</th>
				<th class="font12" id="total_depositos_{num_cia}" align="right">{total_depositos}</th>
				<th colspan="2">&nbsp;</th>
			</tr>
			<tr>
				<th class="font12 right" colspan="2">Total seleccionado:</th>
				<th class="font12" id="total_seleccion_{num_cia}" align="right">0.00</th>
				<th colspan="2">&nbsp;</th>
			</tr>
			<tr>
				<td colspan="5">&nbsp;</td>
			</tr>
			<!-- END BLOCK : cia -->
		</tbody>
		<tfoot>
			<tr>
				<td colspan="5">&nbsp;</td>
			</tr>
		</tfoot>
	</table>
	<p>
		<button type="button" id="regresar">Regresar</button>
		&nbsp;&nbsp;<button type="button" id="conciliar">Conciliar</button>
	</p>
</form>
