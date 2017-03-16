<form name="Anotaciones" class="FormValidator FormStyles" id="Anotaciones">
	<table class="tabla_captura">
		<tr>
			<th scope="col">Proveedor</th>
			<th scope="col">Anotaciones</th>
		</tr>
		<!-- START BLOCK : row -->
		<tr class="linea_{row_color}">
			<td nowrap="nowrap"><input name="num_pro_anotacion[]" type="hidden" id="num_pro_anotacion" value="{num_pro}" />
			{num_pro} {nombre_pro}</td>
			<td><textarea name="anotacion[]" cols="45" rows="3" class="valid toText toUpper cleanText" id="anotacion"></textarea></td>
		</tr>
		<!-- END BLOCK : row -->
	</table>
	<p>
		<input type="button" name="popup_close" id="popup_close" value="Cancelar" />
	&nbsp;&nbsp;
	<input type="button" name="terminar" id="terminar" value="Terminar" />
	</p>
</form>