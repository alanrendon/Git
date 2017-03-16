<form name="consulta_form" class="FormValidator" id="consulta_form">
	<input name="current" type="hidden" id="current" value="{num_pro}">
	<input name="fecha_pago" type="hidden" id="fecha_pago" value="{fecha_pago}">
	<input name="banco" type="hidden" id="banco" value="{banco}">
	<table class="table">
		<tbody>
			<tr>
				<th colspan="2">&nbsp;</th>
			</tr>
		</tbody>
		<tbody>
			<tr>
				<td class="bold font14">Proveedor</td>
				<td class="bold font14">{num_pro} {nombre_pro}</td>
			</tr>
			<tr>
				<td class="bold font14">Tipo de pago</td>
				<td class="bold font14">{tipo_pago}</td>
			</tr>
			<tr>
				<td class="bold font14">Banco</td>
				<td class="bold font14">{nombre_banco}</td>
			</tr>
			<tr>
				<td class="bold font14">Fecha de pago</td>
				<td class="bold font14">{fecha_pago}</td>
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
				<th colspan="5">
					<div class="font12 left">{num_cia} {nombre_cia}<span style="float:right;">Cuenta: {cuenta}</span></div>
					<!-- START BLOCK : saldo_inicio -->
					<div>
						<input type="hidden" id="saldo_inicio_{num_cia}" value="{saldo}">
						<div class="font12 right" style="width:80%; float:left;">Saldo para pagar:</div>
						<div class="font12 right" style="width:20%; float:left;">{saldo_inicio}</div>
					</div>
					<!-- END BLOCK : saldo_inicio -->
					<!-- START BLOCK : cuentas_pago -->
					<div class="font12 right">
						Cuenta para pagar:
						<select id="num_cia_pago_{num_cia}" style="font-size:12pt; font-weight:bold;" data-cia="{num_cia}">
							<!-- START BLOCK : cia_pago -->
							<option value="{num_cia}">[{cuenta}] {num_cia} {nombre_cia}</option>
							<!-- END BLOCK : cia_pago -->
						</select>
					</div>
					<!-- END BLOCK : cuentas_pago -->
				</th>
			</tr>
			<tr>
				<th align="center">
					<input type="checkbox" id="check_cia_{num_cia}" value="{num_cia}">
				</th>
				<th class="left">Factura</th>
				<th>Fecha</th>
				<th class="left">Concepto</th>
				<th class="right">Importe</th>
			</tr>
			<!-- START BLOCK : factura -->
			<tr id="row_{index}" class="unchecked">
				<td class="center">
					{factura_checkbox}
				</td>
				<td>{num_fact}</td>
				<td class="center">{fecha}</td>
				<td>{concepto}</td>
				<td class="bold right">{importe}</td>
			</tr>
			<!-- END BLOCK : factura -->
			<tr>
				<th class="font12 right" colspan="4">Total a pagar:</th>
				<th class="font12" id="total_pago_{num_cia}" align="right">0.00</th>
			</tr>
			<tr>
				<th class="font12 right" colspan="4">Total a pagar (otras compa&ntilde;&iacute;as):</th>
				<th class="font12" id="total_pago_otras_cias_{num_cia}" align="right">0.00</th>
			</tr>
			<!-- START BLOCK : saldo_final -->
			<tr>
				<th class="font12 right" colspan="4">Saldo despues de pagar:</th>
				<th id="saldo_final_{num_cia}" class="font12 right">{saldo_final}</th>
			</tr>
			<!-- END BLOCK : saldo_final -->
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
	<br>
	<table class="table">
		<thead>
			<tr>
				<th colspan="4">&nbsp;</th>
			</tr>
		</thead>
		<tr>
			<td>
				<button type="button" id="salir">Salir</button>
			</td>
			<td>
				<button type="button" id="pagar_salir">Pagar y salir</button>
			</td>
			<td>
				<input type="text" class="validate focus toPosInt right" id="next" size="3" value="">
				<input type="text" id="nombre_next" size="30" value="" disabled="">
			</td>
			<td>
				<button type="button" id="siguiente">Siguiente</button>
			</td>
		</tr>
		<tfoot>
			<tr>
				<td colspan="4">&nbsp;</td>
			</tr>
		</tfoot>
	</table>
</form>
