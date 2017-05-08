<form name="Pagar" class="FormValidator FormStyles" id="Pagar">
	<input name="idventa" type="hidden" id="idventa" value="{id}" />
	<table class="tabla_captura">
		<tr>
			<th scope="col">Fecha de pago</th>
		</tr>
		<tr>
			<td align="center"><input name="fecha_pago" type="text" class="valid Focus toDate center" id="fecha_pago" value="{fecha}" size="10" maxlength="10" /></td>
		</tr>
	</table>
	<p>
		<input type="button" name="cancelar" id="cancelar" value="Cancelar" />
		&nbsp;&nbsp;
		<input type="button" name="poner_fecha_pago" id="poner_fecha_pago" value="Pagar" />
	</p>
</form>
