<form name="Precios" class="FormValidator FormStyles" id="Precios">
	<table class="tabla_captura">
		<tr>
			<th scope="col">Dia</th>
			<!-- START BLOCK : th -->
			<th scope="col">{num_pro} {nombre_pro}</th>
			<!-- END BLOCK : th -->
			<th scope="col">Observaciones</th>
		</tr>
		<!-- START BLOCK : row -->
		<tr id="row" class="linea_{row_color}">
			<td align="center"><input name="dia[]" type="hidden" id="dia" value="{dia}" />
			{dia}</td>
			<!-- START BLOCK : td -->
			<td align="center"><input name="num_pro{dia}[]" type="hidden" id="num_pro{dia}" value="{num_pro}" dia="{dia}" />						<input name="precio{dia}[]" type="text" class="valid Focus numberPosFormat right" id="precio{dia}" value="{precio}" size="8" precision="2" dia="{dia}" /></td>
			<!-- END BLOCK : td -->
			<td align="center"><input name="observaciones{dia}" type="text" class="valid toText cleanText toUpper" id="observaciones{dia}" value="{observaciones}" size="30" maxlength="200" /></td>
		</tr>
		<!-- END BLOCK : row -->
	</table>
	<p>
		<input type="button" name="actualizar" id="actualizar" value="Actualizar" />
	</p>
</form>