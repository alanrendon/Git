<form name="Efectivo" class="FormValidator FormStyles" id="Efectivo">
	<input name="num_cia" type="hidden" id="num_cia" value="{num_cia}">
	<input name="dia" type="hidden" id="dia" value="{dia}">
	<input name="porcentajes" type="hidden" id="porcentajes" value="{porcentajes}" />
	<table class="tabla_captura">
		<tr>
			<th scope="col">Efectivo</th>
		</tr>
		<tr>
			<td align="center"><input name="efectivo" type="text" class="valid Focus numberPosFormat right" precision="2" id="efectivo" size="10" /></td>
		</tr>
	</table>
	<p>
		<input type="button" name="cancelar" id="cancelar" value="Cancelar" />
	&nbsp;&nbsp;
	<input type="button" name="imponer" id="imponer" value="Imponer" />
	</p>
</form>