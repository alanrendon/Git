<form name="alta_pago" class="FormValidator" id="alta_pago">
	<table class="table">
		<thead>
			<tr>
				<th colspan="2" scope="col">&nbsp;</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td class="bold">Compa&ntilde;&iacute;a</td>
				<td>
					<input name="num_cia" type="text" class="validate focus toPosInt right" id="num_cia" size="3" />
					<input name="nombre_cia" type="text" id="nombre_cia" size="30" disabled="disabled" />
				</td>
			</tr>
			<tr>
				<td class="bold">Periodo</td>
				<td>
					<select name="mes1" id="mes1">
						<!-- START BLOCK : mes1 -->
						<option value="{mes}"{selected}>{nombre_mes}</option>
						<!-- END BLOCK : mes1 -->
					</select>
					<input name="anio1" type="text" class="validate focus toPosInt center" id="anio1" size="4" maxlength="4" value="{anio1}" />
					al
					<select name="mes2" id="mes2">
						<!-- START BLOCK : mes2 -->
						<option value="{mes}"{selected}>{nombre_mes}</option>
						<!-- END BLOCK : mes2 -->
					</select>
					<input name="anio2" type="text" class="validate focus toPosInt center" id="anio2" size="4" maxlength="4" value="{anio2}" />
				</td>
			</tr>
			<tr>
				<td class="bold">Concepto</td>
				<td><input name="concepto" type="text" class="validate toText cleanText toUpper" id="concepto" size="40" maxlength="100" /></td>
			</tr>
			<tr>
				<td class="bold">Importe</td>
				<td><input name="importe" type="text" class="validate focus numberFormat right" precision="2" id="importe" value="" size="10" /></td>
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
		<input type="button" name="alta" id="alta" value="Alta" />
	</p>
</form>
