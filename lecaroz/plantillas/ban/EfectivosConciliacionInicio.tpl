<form action="" method="post" name="inicio" class="FormValidator" id="inicio">
	<table class="table">
		<thead>
			<tr>
				<th colspan="2" align="left" scope="row">&nbsp;</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td align="left" class="bold" scope="row">Fecha de corte</td>
				<td><input name="fecha" type="text" class="validate focus toDate center" id="fecha" value="{fecha}" size="10" maxlength="10" /></td>
			</tr>
			<tr>
				<td align="left" class="bold" scope="row">Depósitos de oficina</td>
				<td><input name="tipo" type="radio" value="1" checked="checked" />
					Por día<br />
					<input type="radio" name="tipo" value="2" />
					Totales</td>
			</tr>
			<tr>
				<td align="left" class="bold" scope="row">Administrador</td>
				<td>
					<select name="admin" id="admin">
						<option value=""></option>
						<!-- START BLOCK : admin -->
						<option value="{value}">{text}</option>
						<!-- END BLOCK : admin -->
					</select>
				</td>
			</tr>
		</tbody>
		<tfoot>
			<tr>
				<th colspan="2" align="left" scope="row">&nbsp;</th>
			</tr>
		</tfoot>
	</table>
	<p>
		<input type="button" name="siguiente" id="siguiente" value="Siguiente" />
	</p>
</form>
