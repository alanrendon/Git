<form action="" method="post" name="alta" class="FormValidator" id="alta">
	<table class="table">
		<thead>
			<tr>
				<th colspan="2">&nbsp;</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td align="left" scope="row" class="bold">Contador</td>
				<td><input name="nombre" type="text" class="validate toText cleanText toUpper" id="nombre" size="30" maxlength="70" /></td>
			</tr>
			<tr>
				<td align="left" scope="row" class="bold">Email</td>
				<td><input name="email" type="text" class="validate focus toEmail" id="email" size="30" maxlength="200" /></td>
			</tr>
			<tr>
				<td align="left" scope="row" class="bold">Usuario asociado</td>
				<td><select name="iduser" id="iduser">
						<option value=""></option>
						<!-- START BLOCK : iduser -->
						<option value="{value}"{disabled}>{text}</option>
						<!-- END BLOCK : iduser -->
					</select></td>
			</tr>
		</tbody>
		<tfoot>
			<tr>
				<th colspan="2">&nbsp;</th>
			</tr>
		</tfoot>
	</table>
	<p>
		<input type="button" name="cancelar" id="cancelar" value="Cancelar" />
		&nbsp;&nbsp;
		<input type="button" name="do_alta" id="do_alta" value="Alta" />
	</p>
</form>
