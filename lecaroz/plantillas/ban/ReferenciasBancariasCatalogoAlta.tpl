<form name="alta_form" class="FormValidator" id="alta_form">
	<input name="num_pro" type="hidden" id="num_pro" value="{num_pro}">
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
					<input name="num_cia" type="text" class="validate focus toPosInt center" id="num_cia" size="3" />
					<input name="nombre_cia" type="text" id="nombre_cia" size="30" disabled="disabled" />
				</td>
			</tr>
			<tr>
				<td class="bold">Referencia</td>
				<td><input name="referencia" type="text" class="validate focus toText" id="referencia" value="" size="30" maxlength="10" /></td>
			</tr>
		</tbody>
		<tfoot>
			<tr>
				<td colspan="2">&nbsp;</td>
			</tr>
		</tfoot>
	</table>
	<p>
		<button type="button" id="cancelar">Cancelar</button>
		&nbsp;&nbsp;
		<button type="button" id="alta">Alta</button>
	</p>
</form>
