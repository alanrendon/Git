<form name="modificar_form" class="FormValidator" id="modificar_form">
	<input name="id" type="hidden" id="id" value="{id}" />
	<table class="table">
		<thead>
			<tr>
				<th colspan="2">&nbsp;</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td class="bold">Compa&ntilde;&iacute;a</td>
				<td>
					<input name="num_cia" type="text" class="validate focus toPosInt right" id="num_cia" size="3" value="{num_cia}" />
					<input name="nombre_cia" type="text" id="nombre_cia" size="40" value="{nombre_cia}" disabled="disabled" />
				</td>
			</tr>
			<tr>
				<td class="bold">Tipo</td>
				<td>
					<input name="tipo" type="radio" id="tipo_1" value="1"{tipo_1} />
					Agua
					<input name="tipo" type="radio" id="tipo_2" value="2"{tipo_2} />
					Predial
				</td>
			</tr>
			<tr>
				<td class="bold">Proveedor</td>
				<td>
					<input name="num_pro" type="text" class="validate focus toPosInt right" id="num_pro" size="3" value="{num_pro}" />
					<input name="nombre_pro" type="text" id="nombre_pro" size="40" value="{nombre_pro}" disabled="disabled" />
				</td>
			</tr>
			<tr>
				<td class="bold">Cuenta</td>
				<td>
					<input name="cuenta" type="text" class="validate focus onlyNumbers" id="cuenta" size="15" maxlength="50" value="{cuenta}" />
				</td>
			</tr>
			<tr>
				<td class="bold">Recurrencia</td>
				<td>
					<input name="recurrencia" type="radio" id="recurrencia_1" value="1"{recurrencia_1} />
					Mensual
					<input name="recurrencia" type="radio" id="recurrencia_2" value="2"{recurrencia_2} />
					Bimestral
					<input name="recurrencia" type="radio" id="recurrencia_3" value="3"{recurrencia_3} />
					Anual
				</td>
			</tr>
			<tr>
				<td class="bold">Propietario</td>
				<td>
					<input name="idarrendador" type="hidden" id="idarrendador" value="{idarrendador}" />
					<input name="arrendador" type="text" class="validate focus toPosInt right" id="arrendador" size="3" value="{arrendador}" />
					<input name="nombre_arrendador" type="text" id="nombre_arrendador" size="40" value="{nombre_arrendador}" disabled="disabled" />
				</td>
			</tr>
			<tr>
				<td class="bold">Observaciones</td>
				<td>
					<textarea name="observaciones" class="validate toText cleanText toUpper" id="observaciones" rows="5" style="width:98%;">{observaciones}</textarea>
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
		<button type="button" id="cancelar">
			Cancelar
		</button>
		&nbsp;&nbsp;
		<button type="button" id="modificar">
			Modificar
		</button>
	</p>
</form>
