<form name="Datos" class="FormValidator FormStyles" id="Datos">
	<table class="tabla_captura" id="TablaDatos">
		<!-- START BLOCK : cia -->
		<tr>
			<th colspan="11" align="left" class="font14" scope="col">{num_cia} {nombre_cia}
				<input name="cia[]" type="hidden" id="cia" value="{num_cia}" />
				<input name="arrastre_diferencia" type="hidden" id="arrastre_diferencia" value="{arrastre_diferencia}" cia="{num_cia}" />
				<input name="periodo" type="hidden" id="periodo" value="{periodo}" cia="{num_cia}" /></th>
		</tr>
		<tr>
			<th colspan="6" align="right"><span style="float:left;" class="font8 bold">* = Efectivo desglosado</span>Diferencia Inicial</th>
			<th align="right">{diferencia_inicial}</th>
			<td width="50" align="right">&nbsp;</td>
			<th colspan="3" align="right">&nbsp;</th>
		</tr>
		<tr>
			<th><input name="checkblock" type="checkbox" id="checkblock" checked="checked" cia="{num_cia}" /></th>
			<th>D&iacute;a</th>
			<th>Efectivo</th>
			<th nowrap="nowrap">Facturas en<br />
				tr&aacute;nsito</th>
			<th nowrap="nowrap">Facturas <br />
				de clientes</th>
			<th nowrap="nowrap">Facturas <br />
				de venta</th>
			<th nowrap="nowrap">Diferencia<br />
				en facturas</th>
			<td>&nbsp;</td>
			<th nowrap="nowrap">Diferencia<br />
				en efectivos</th>
			<th>Usuario</th>
			<th nowrap="nowrap">Rango de<br />
				notas de venta</th>
		</tr>
		<!-- START BLOCK : dia -->
		<tr class="linea_{color_row}">
			<td align="center" class="linea_{color_row}"><input name="datos[]" type="checkbox" id="datos" value="{datos}"{checked} cia="{num_cia}" dia="{dia}" index="{index}"{disabled} /></td>
			<td align="right" class="linea_{color_row}">{dia}</td>
			<td align="right" class="blue">{depositos}</td>
			<td align="right" class="linea_{color_row}"><a title="{param}" class="enlace green" id="tran-{num_cia}-{dia}">{facturas_transito}</a></td>
			<td align="right" class="linea_{color_row}"><a title="{param}" class="enlace orange" id="cli-{num_cia}-{dia}">{facturas_clientes}</a></td>
			<td align="right" id="fp-{num_cia}-{dia}" class="red">{facturas_venta}</td>
			<td align="right" id="dif-{num_cia}-{dia}" class="{color}">{diferencia_venta}</td>
			<td align="right" id="dif-{num_cia}-{dia}">&nbsp;</td>
			<td align="right" id="dif-{num_cia}-{dia}" class="{color_dif_efectivo}">{diferencia_efectivo}</td>
			<td>{usuario}</td>
			<td align="center" nowrap="nowrap"><input name="nota-ini-{num_cia}-{dia}" type="text" class="valid Focus onlyNumbersAndLetters right toUpper" id="nota-ini-{num_cia}-{dia}" size="8" cia="{num_cia}" dia="{dia}" />
				a la
				<input name="nota-fin-{num_cia}-{dia}" type="text" class="valid Focus onlyNumbersAndLetters right toUpper" id="nota-fin-{num_cia}-{dia}" size="8" cia="{num_cia}" dia="{dia}" /></td>
		</tr>
		<!-- END BLOCK : dia -->
		<tr>
			<th colspan="2" align="right">Total</th>
			<th align="right" class="blue">{depositos}</th>
			<th align="right" class="green">{facturas_transito}</th>
			<th align="right" class="orange">{facturas_clientes}</th>
			<th align="right" id="tfp-{num_cia}" class="red">{facturas_venta}</th>
			<th align="right" id="tdif-{num_cia}" class="{color}">{diferencia_venta}</th>
			<td align="right" id="tdif-{num_cia}">&nbsp;</td>
			<th align="right" id="tdif-{num_cia}" class="{color}">&nbsp;</th>
			<th align="right" id="tdif-{num_cia}" class="{color}">&nbsp;</th>
			<th align="right" id="tdif-{num_cia}" class="{color}">&nbsp;</th>
		</tr>
		<tr>
			<td colspan="11">&nbsp;</td>
		</tr>
		<!-- END BLOCK : cia -->
	</table>
	<p>
		<input name="regresar" type="button" id="regresar" value="Regresar">
		&nbsp;&nbsp;
		<input name="generar" type="button" id="generar" value="Generar Facturas Electr&oacute;nicas">
	</p>
</form>
