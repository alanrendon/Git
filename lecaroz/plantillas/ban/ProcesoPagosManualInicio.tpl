<form name="inicio_form" class="FormValidator" id="inicio_form">
	<table class="table">
		<thead>
			<tr>
				<th colspan="2" scope="col">&nbsp;</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td class="bold">Seleccionar proveedor inicial</td>
				<td>
					<input name="num_pro" type="text" class="validate focus toPosInt right" id="num_pro" size="3">
					<input name="nombre_pro" type="text" id="nombre_pro" size="30" disabled="">
				</td>
			</tr>
			<tr>
				<td class="bold">Compa&ntilde;&iacute;a(s)</td>
				<td><input name="cias" type="text" class="validate toInterval" id="cias" size="40"></td>
			</tr>
			<tr>
				<td class="bold">Omitir compa&ntilde;&iacute;a(s)</td>
				<td><input name="omitir_cias" type="text" class="validate toInterval" id="omitir_cias" size="40"></td>
			</tr>
			<tr>
				<td class="bold">Proveedor(es)</td>
				<td><input name="pros" type="text" class="validate toInterval" id="pros" size="40"></td>
			</tr>
			<tr>
				<td class="bold">Omitir proveedor(es)</td>
				<td><input name="omitir_pros" type="text" class="validate toInterval" id="omitir_pros" size="40"></td>
			</tr>
			<tr>
				<td class="bold">Fecha de corte</td>
				<td><input name="fecha_corte" type="text" class="validate toDate center" id="fecha_corte" value="{fecha}" size="10" maxlength="10" /></td>
			</tr>
			<tr>
				<td class="bold">Incluir compa&ntilde;&iacute;as sin cuenta</td>
				<td>
					<input name="sin_cuenta" type="checkbox" id="sin_cuenta" value="1" checked=""> Si
				</td>
			</tr>
			<tr>
				<td colspan="2">&nbsp;</td>
			</tr>
			<tr>
				<td class="bold">Fecha de pago</td>
				<td><input name="fecha_pago" type="text" class="validate toDate center" id="fecha_pago" value="{fecha}" size="10" maxlength="10" /></td>
			</tr>
			<tr>
				<td class="bold">Pagar con</td>
				<td>
					<select name="banco" id="banco">
						<option value="1">Banorte</option>
						<option value="2">Santander</option>
					</select>
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
		<button type="button" id="consultar">Consultar</button>
	</p>
</form>
