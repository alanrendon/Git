<form name="inicio_form" class="FormValidator" id="inicio_form">
	<table class="table">
		<thead>
			<tr>
				<th colspan="2" scope="col">&nbsp;</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td class="bold">Compa&ntilde;&iacute;a(s)</td>
				<td><input name="cias" type="text" class="validate toInterval" id="cias" size="40" /></td>
			</tr>
			<tr>
				<td class="bold">Omitir compa&ntilde;&iacute;a(s)</td>
				<td><input name="omitir" type="text" class="validate toInterval" id="omitir" size="40" /></td>
			</tr>
			<tr>
				<td class="bold">Administrador</td>
				<td>
					<select name="admin" id="admin">
						<option value=""></option>
						<!-- START BLOCK : admin -->
						<option value="{value}">{text}</option>
						<!-- END BLOCK : admin -->
					</select>
				</td>
			</tr>
			<tr>
				<td class="bold">Fecha</td>
				<td>
					<input name="fecha_corte" type="text" class="validate focus toDate center" id="fecha_corte" size="10" maxlength="10" value="{fecha_corte}" />
				</td>
			</tr>
			<tr>
				<td class="bold">Diferencia máxima</td>
				<td>
					<input name="diferencia_maxima" type="text" id="diferencia_maxima" class="validate focus numberPosFormat right" precision="2" size="10" value="{diferencia_maxima}">
				</td>
			</tr>
			<tr>
				<td colspan="2">&nbsp;</td>
			</tr>
			<tr>
				<td class="bold">Mensajes</td>
				<td id="mensajes">&nbsp;</td>
			</tr>
		</tbody>
		<tfoot>
			<tr>
				<td colspan="2">&nbsp;</td>
			</tr>
		</tfoot>
	</table>
	<p>
		<button type="button" id="generar" disabled="">Generar</button>
	</p>
</form>
