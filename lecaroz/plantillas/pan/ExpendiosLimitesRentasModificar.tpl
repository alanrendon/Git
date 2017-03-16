<form name="modificar_limite" class="FormValidator" id="modificar_limite">
	<input type="hidden" name="id" id="id" value="{id}" />
	<table class="table">
		<thead>
			<tr>
				<th colspan="2" scope="col">&nbsp;</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td class="bold">Compa&ntilde;&iacute;a</td>
				<td><input name="num_cia" type="text" class="validate toPosInt" id="num_cia" size="3" value="{num_cia}" /><input name="nombre_cia" type="text" id="nombre_cia" size="30" value="{nombre_cia}" disabled="disabled" /></td>
			</tr>
			<tr>
				<td class="bold">Nombre</td>
				<td>
					<input name="nombre" type="text" class="validate toText cleanText toUpper" id="nombre" size="40" maxlength="100" value="{nombre}" />
				</td>
			</tr>
			<tr>
				<td class="bold">Expendio</td>
				<td><input name="num_exp" type="text" class="validate toPosInt" id="num_exp" size="3" value="{num_exp}" /><input name="nombre_exp" type="text" class="validate toPosInt" id="nombre_exp" size="30" value="{nombre_exp}" disabled="disabled" /></td>
			</tr>
			<tr>
				<td class="bold">Contrato</td>
				<td>
					<input type="radio" name="contrato" id="contrato_f" value="FALSE"{contrato_f} />No
					<input type="radio" name="contrato" id="contrato_f" value="TRUE"{contrato_t} />Si
				</td>
			</tr>
			<tr>
				<td class="bold">Tipo de pago</td>
				<td>
					<input type="radio" name="tipo_pago" id="tipo_pago_1" value="1"{tipo_pago_1} />Efectivo
					<input type="radio" name="tipo_pago" id="tipo_pago_2" value="2"{tipo_pago_2} />Cheque
				</td>
			</tr>
			<tr>
				<td class="bold">Periodo</td>
				<td>
					<input type="text" name="fecha_inicio" class="validate focus toDate center" id="fecha_inicio" value="{fecha_inicio}" size="10" maxlength="10" />
					al
					<input type="text" name="fecha_termino" class="validate focus toDate center" id="fecha_termino" value="{fecha_termino}" size="10" maxlength="10" />
				</td>
			</tr>
			<tr>
				<td class="bold">Importe</td>
				<td>
					<input name="importe_bruto" type="text" class="right green" id="importe_bruto" size="10" value="{importe_bruto}" readonly="readonly" />
				</td>
			</tr>
			<tr>
				<td class="bold">I.V.A.</td>
				<td>
					<input name="iva" type="text" class="right red" id="iva" size="10" value="{iva}" readonly="readonly" />
					<input type="checkbox" name="aplicar_iva" id="aplicar_iva"{aplicar_iva} />
				</td>
			</tr>
			<tr>
				<td class="bold">Ret. I.V.A.</td>
				<td>
					<input name="ret_iva" type="text" class="right blue" id="ret_iva" size="10" value="{ret_iva}" readonly="readonly" />
					<input type="checkbox" name="aplicar_ret" id="aplicar_ret"{aplicar_ret} />
				</td>
			</tr>
			<tr>
				<td class="bold">Ret. I.S.R.</td>
				<td>
					<input name="ret_isr" type="text" class="right blue" id="ret_isr" size="10" value="{ret_isr}" readonly="readonly" />
				</td>
			</tr>
			<tr>
				<td class="bold">Total</td>
				<td>
					<input name="importe" type="text" class="validate focus numberPosFormat right bold font12 green" precision="2" id="importe" size="10" value="{importe}" />
				</td>
			</tr>
		</tbody>
		<tfoot>
			<tr>
				<td colspan="2">&nbsp;</td>
			</tr>
		</tfoot>
	</table>
	<p>
		<input type="button" name="cancelar" id="cancelar" value="Cancelar" />
		&nbsp;&nbsp;
		<input type="button" name="modificar" id="modificar" value="Modificar" />
	</p>
</form>
