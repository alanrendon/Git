<form action="" method="post" name="modificar_tipo" class="FormValidator" id="modificar_tipo">
	<input name="reserva" type="hidden" id="reserva" value="{reserva}">
	<table class="table">
		<tr>
			<th align="left" scope="row">Descripci&oacute;n</th>
			<td><input name="descripcion" type="text" class="validate toText cleanText toUpper" id="descripcion" value="{descripcion}" size="30" maxlength="100" /></td>
		</tr>
	</table>
	<p>
		<input type="button" name="cancelar" id="cancelar" value="Cancelar" />
		&nbsp;&nbsp;
		<input type="button" name="modificar" id="modificar" value="Modificar" />
	</p>
</form>
