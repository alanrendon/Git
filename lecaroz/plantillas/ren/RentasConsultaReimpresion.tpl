<form action="" method="post" name="Datos" class="FormValidator FormStyles" id="Datos">
	<input name="idreciborenta" type="hidden" id="idreciborenta" value="{idreciborenta}" />
	<input name="idarrendatario" type="hidden" class="valid Focus toPosInt center" id="idarrendatario" value="{idarrendatario}" />
	<input name="idcfd" type="hidden" id="idcfd" value="{idcfd}">
	<table class="tabla_captura">
		<tr class="linea_off">
			<th align="left" scope="row">Arrendador</th>
			<td class="bold">{arrendador} {nombre_arrendador}</td>
		</tr>
		<tr class="linea_on">
			<th align="left" scope="row">Arrendatario</th>
			<td class="bold" id="arrendatario">{arrendatario} {nombre_arrendatario}</td>
		</tr>
		<tr class="linea_off">
			<td colspan="2" align="left" scope="row">&nbsp;</td>
		</tr>
		<tr class="linea_on">
			<th align="left" scope="row">A&ntilde;o</th>
			<td><input name="anio" type="text" class="valid Focus toPosInt center" id="anio" value="{anio}" size="4" maxlength="4" /></td>
		</tr>
		<tr class="linea_off">
			<th align="left" scope="row">Mes</th>
			<td><select name="mes" id="mes">
					<option value="1"{1}>ENERO</option>
					<option value="2"{2}>FEBRERO</option>
					<option value="3"{3}>MARZO</option>
					<option value="4"{4}>ABRIL</option>
					<option value="5"{5}>MAYO</option>
					<option value="6"{6}>JUNIO</option>
					<option value="7"{7}>JULIO</option>
					<option value="8"{8}>AGOSTO</option>
					<option value="9"{9}>SEPTIEMBRE</option>
					<option value="10"{10}>OCTUBRE</option>
					<option value="11"{11}>NOVIEMBRE</option>
					<option value="12"{12}>DICIEMBRE</option>
				</select></td>
		</tr>
		<tr class="linea_on">
			<th align="left" scope="row">Concepto de renta</th>
			<td><input name="concepto_renta" type="text" class="valid toText toUpper cleanText" id="concepto_renta" style="width:98%;" value="{concepto_renta}" size="40" maxlength="100" /></td>
		</tr>
		<tr class="linea_off">
			<th align="left" scope="row">Concepto de mantenimiento</th>
			<td><input name="concepto_mantenimiento" type="text" class="valid toText toUpper cleanText" id="concepto_mantenimiento" style="width:98%;" value="{concepto_mantenimiento}" size="40" maxlength="100" /></td>
		</tr>
		<tr class="linea_on">
			<th align="left" scope="row">Tipo de recibo</th>
			<td><input name="tipo_recibo" type="radio" id="tipo_recibo_1" value="1"{tipo_recibo_1} />
				Arrendamiento<br />
				<input type="radio" name="tipo_recibo" id="tipo_recibo_2" value="2"{tipo_recibo_2} />
				Complementario</td>
		</tr>
		<tr class="linea_off">
			<th align="left" scope="row">Motivo de reimpresión</th>
			<td><textarea name="motivo_cancelacion" class="valid onlyText cleanText toUpper" id="motivo_cancelacion" cols="45" rows="5"></textarea></td>
		</tr>
		<tr class="linea_on">
			<td colspan="2" align="left" scope="row">&nbsp;</td>
		</tr>
		<tr class="linea_off">
			<th align="left" scope="row">Renta</th>
			<td><input name="renta" type="text" class="valid Focus numberPosFormat right blue" id="renta" value="{renta}" size="12" precision="2" /></td>
		</tr>
		<tr class="linea_on">
			<th align="left" scope="row">Mantenimiento</th>
			<td><input name="mantenimiento" type="text" class="valid Focus numberPosFormat right blue" id="mantenimiento" value="{mantenimiento}" size="12" precision="2" /></td>
		</tr>
		<tr class="linea_off">
			<th align="left" scope="row">Subtotal</th>
			<td><input name="subtotal" type="text" class="right blue bold" id="subtotal" value="{subtotal}" size="12" readonly="readonly" /></td>
		</tr>
		<tr class="linea_on">
			<th align="left" scope="row"><input name="aplicar_iva" type="checkbox" id="aplicar_iva" value="1"{aplicar_iva} />
				I.V.A.</th>
			<td><input name="iva" type="text" class="right blue" id="iva" value="{iva}" size="12" readonly="readonly" />
				<input name="iva_renta" type="hidden" id="iva_renta" value="{iva_renta}" />
				<input name="iva_mantenimiento" type="hidden" id="iva_mantenimiento" value="{iva_mantenimiento}" /></td>
		</tr>
		<tr class="linea_off">

			<th align="left" scope="row">Agua</th>
			<td><input name="agua" type="text" class="valid Focus numberPosFormat right blue" id="agua" value="{agua}" size="12" precision="2" /></td>
		</tr>
		<tr class="linea_on">
			<th align="left" scope="row"><input name="aplicar_retenciones" type="checkbox" id="aplicar_retenciones" value="1"{aplicar_retencion} />
				Retenci&oacute;n I.V.A.</th>
			<td><input name="retencion_iva" type="text" class="right red" id="retencion_iva" value="{retencion_iva}" size="12" readonly="readonly" /></td>
		</tr>
		<tr class="linea_off">
			<th align="left" scope="row">Retenci&oacute;n I.S.R.</th>
			<td><input name="retencion_isr" type="text" class="right red" id="retencion_isr" value="{retencion_isr}" size="12" readonly="readonly" /></td>
		</tr>
		<tr class="linea_on">
			<th align="left" scope="row">Total</th>
			<td><input name="total" type="text" class="right blue bold font12" id="total" value="{total}" size="12" readonly="readonly" /></td>
		</tr>
	</table>
	<br />
	<p>
		<input name="cancelar" type="button" id="cancelar" value="Cancelar" />
		&nbsp;&nbsp;
		<input name="reimprimir" type="button" id="reimprimir" value="Reimprimir recibo de arrendamiento" />
	</p>
</form>