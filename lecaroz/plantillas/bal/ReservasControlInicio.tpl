<form action="" method="post" name="inicio" class="FormValidator" id="inicio">
	<table class="table">
		<thead>
			<tr>
				<th scope="col" colspan="2">&nbsp;</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td>Compa&ntilde;&iacute;a</td>
				<td>
					<input name="num_cia" type="text" id="num_cia" class="validate focus toPosInt center" size="3" />
					<input name="nombre_cia" type="text" id="nombre_cia" size="30" disabled="disabled" />
				</td>
			</tr>
			<tr>
				<td>A&ntilde;o</td>
				<td>
					<input name="anio" type="text" id="anio" class="validate focus toPosInt center" size="3" value="{anio}" />
				</td>
			</tr>
			<tr>
				<td>Reserva</td>
				<td>
					<select name="reserva" id="reserva">
						<!-- START BLOCK : reserva -->
						<option value="{value}">{text}</option>
						<!-- END BLOCK : reserva -->
					</select>
				</td>
			</tr>
		</tbody>
		<tfoot>
			<tr>
				<td colspan="2">&nbsp;</td>
			</tr>
		</tfoot>
	</table>
	<p>
		<input name="consultar" type="button" id="consultar" value="Consultar" />
	</p>
</form>
