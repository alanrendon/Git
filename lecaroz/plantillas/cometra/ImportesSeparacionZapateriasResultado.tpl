<table class="tabla_captura">
	<tr>
		<th scope="col">Compañía</th>
		<!-- START BLOCK : title -->
		<th scope="col">{dia}</th>
		<!-- END BLOCK : title --> 
	</tr>
	<!-- START BLOCK : row -->
	<tr class="linea_{row_color}">
		<td nowrap="nowrap"><input name="num_cia[]" type="hidden" id="num_cia" value="{num_cia}" />
			{num_cia} {nombre_cia}</td>
		<!-- START BLOCK : cell -->
		<td align="center"><input name="importe{i}[]" type="text" class="valid Focus numberPosFormat right" id="importe{i}" value="{importe}" size="8" precision="2" i="{i}" /></td>
		<!-- END BLOCK : cell --> 
	</tr>
	<!-- END BLOCK : row -->
</table>
<p>
	<input type="button" name="actualizar" id="actualizar" value="Actualizar" />
</p>
