<form name="Precios" class="FormValidator FormStyles" id="Precios">
	<table class="tabla_captura">
		<tr>
			<th scope="col">Dia</th>
			<th nowrap="nowrap" scope="col">{num_pro} {nombre_pro}</th>
		</tr>
		<tr class="linea_{row_color}">
			<td align="center"><input name="dia[]" type="hidden" id="dia" value="{dia}" />
			{dia}</td>
			<td align="center"><input name="num_pro{dia}[]" type="hidden" id="num_pro{dia}" value="{num_pro}" />						<input name="precio{dia}[]" type="text" id="precio{dia}" size="8" /></td>
		</tr>
	</table>
	<p>
		<input type="button" name="actualizar" id="actualizar" value="Actualizar" />
	</p>
</form>