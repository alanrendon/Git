<form name="datos-form" class="FormValidator" id="datos-form">
	<input name="id" type="hidden" id="id" value="{id}" />
	<table class="table">
		<thead>
			<tr>
				<th colspan="2">&nbsp;</th>
			</tr>
		</thead>
		<tbody id="datos-table">
			<tr>
				<td class="bold">Compa&ntilde;&iacute;a</td>
				<td>
					<input name="num_cia" type="text" class="validate focus toPosInt right" id="num_cia" size="3" value="{num_cia}" />
					<input name="nombre_cia" type="text" id="nombre_cia" size="30" disabled="disabled" />
				</td>
			</tr>
			<tr>
				<td class="bold">Proveedor</td>
				<td>
					<input name="num_pro" type="text" class="validate focus toPosInt right" id="num_pro" size="3" value="{num_pro}" />
					<input name="nombre_pro" type="text" id="nombre_pro" size="30" disabled="disabled" />
				</td>
			</tr>
			<tr>
				<td class="bold">Gasto</td>
				<td>
					<input name="cod" type="text" class="validate focus toPosInt right" id="cod" size="3" value="{cod}" />
					<input name="gasto" type="text" id="gasto" size="30" disabled="disabled" />
				</td>
			</tr>
			<tr>
				<td class="bold">Concepto</td>
				<td>
					<input name="concepto" type="text" class="validate toText cleanText toUpper" id="concepto" size="40" maxlength="200" value="{concepto}" />
				</td>
			</tr>
			<tr>
				<td class="bold">Importe</td>
				<td>
					<input name="importe" type="text" class="validate focus numberPosFormat right" precision="2" id="importe" size="10" value="{importe}" />
				</td>
			</tr>
			<tr>
				<td class="bold">I.V.A.</td>
				<td>
					<input name="iva" type="text" class="validate focus numberPosFormat right red" precision="2" id="iva" size="10" value="{iva}" />
				</td>
			</tr>
			<tr>
				<td class="bold">Ret. I.V.A.</td>
				<td>
					<input name="ret_iva" type="text" class="validate focus numberFormat right blue" precision="2" id="ret_iva" size="10" value="{ret_iva}" />
				</td>
			</tr>
			<tr>
				<td class="bold">I.S.R.</td>
				<td>
					<input name="isr" type="text" class="validate focus numberPosFormat right blue" precision="2" id="isr" size="10" value="{isr}" />
				</td>
			</tr>
			<tr>
				<td class="bold">Cedular</td>
				<td>
					<input name="cedular" type="text" class="validate focus numberPosFormat right blue" precision="2" id="cedular" size="10" value="{cedular}" />
				</td>
			</tr>
			<tr>
				<td class="bold">Total</td>
				<td>
					<input name="total" type="text" class="bold right" id="total" size="10" value="{total}" />
				</td>
			</tr>
			<tr>
				<td class="bold">Tipo renta</td>
				<td>
					<select name="tipo_renta" id="tipo_renta">
						<option value="1"{tipo_renta_1}>INTERNA</option>
						<option value="2"{tipo_renta_2}>EXTERNA</option>
					</select>
				</td>
			</tr>
		</tbody>
		<tfoot>
			<tr>
				<td colspan="10">&nbsp;</td>
			</tr>
		</tfoot>
	</table>
	<p>
		<button type="button" id="cancelar">Cancelar</button>
		<button type="button" id="modificar">Modificar</button>
	</p>
</form>
