<form name="Rentas" class="FormValidator FormStyles" id="Rentas">
	<table class="tabla_captura">
		<tr>
			<th scope="col">Inmobiliaria</th>
			<th scope="col">Fecha</th>
			<th scope="col">Recibo</th>
			<th scope="col">Importe</th>
		</tr>
		<tr>
			<td><input name="id[]" type="hidden" id="id[]" value="{id}" />
				{num_cia} {nombre_cia}</td>
			<td align="center">{fecha}</td>
			<td><select name="select" id="select">
					<option value="" selected="selected"></option>
					<!-- START BLOCK : recibo -->
					<option value="{value}">{text}</option>
					<!-- END BLOCK : recibo -->
				</select></td>
			<td align="right">{importe}</td>
		</tr>
	</table>
	<p>
		<input type="button" name="cancelar_rentas" id="cancelar_rentas" value="Cancelar" />
		&nbsp;&nbsp;
		<input type="button" name="button" id="button" value="Bot&oacute;n" />
	</p>
</form>
