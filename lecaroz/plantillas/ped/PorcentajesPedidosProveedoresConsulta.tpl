<form name="Tabla" class="FormValidator FormStyles" id="Tabla">
	<table class="tabla_captura">
		<tr>
			<th scope="col">Compañía</th>
			<!-- START BLOCK : th -->
			<th colspan="2" scope="col">{num_pro} {nombre_pro}&nbsp;<img src="/lecaroz/iconos/refresh.png" alt="{num_pro}" name="actualizar_porcentajes" width="16" height="16" id="actualizar_porcentajes" /></th>
			<!-- END BLOCK : th -->
			<th scope="col">Porcentaje</th>
		</tr>
		<!-- START BLOCK : cia -->
		<tr class="linea_{row_color}" id="row">
			<td nowrap><input name="num_cia[]" type="hidden" id="num_cia" value="{num_cia}" />
				{num_cia} {nombre_cia}</td>
			<!-- START BLOCK : pro -->
			<td align="center" nowrap><input name="num_pro_{i}[]" type="hidden" id="num_pro_{i}" value="{num_pro}" />
				<input name="porcentaje_{i}[]" type="text" class="valid Focus numberPosFormat right" id="porcentaje_{i}" value="{porcentaje}" size="5" maxlength="5" precision="2" num_pro="{num_pro}" />
				%</td>
			<td align="center"><select name="presentacion_{i}[]" id="presentacion_{i}">
				<!-- START BLOCK : presentacion -->
				<option value="{value}"{selected}>{text}</option>
				<!-- END BLOCK : presentacion -->
			</select></td>
			<!-- END BLOCK : pro -->
			<td class="bold" align="center" nowrap><input name="total[]" type="text" disabled="disabled" class="bold right blue" id="total" value="{total}" size="5" />
				%</td>
		</tr>
		<!-- END BLOCK : cia -->
	</table>
	<p>
		<input type="button" name="cancelar" id="cancelar" value="Cancelar" />
	&nbsp;&nbsp;
	<input type="button" name="actualizar" id="actualizar" value="Actualizar" />
	</p>
</form>