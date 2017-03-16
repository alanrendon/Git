<form action="" method="post" name="alta" class="FormValidator" id="alta">
	<table class="table">
		<thead>
			<tr>
				<th colspan="2">&nbsp;</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td align="left" scope="row" class="bold">Notario</td>
				<td><input name="nombre" type="text" class="validate toText cleanText toUpper" id="nombre" size="30" maxlength="50" /></td>
			</tr>
			<tr>
				<td align="left" scope="row" class="bold">Número</td>
				<td><input name="num_notario" type="text" class="validate focus toPosInt center" id="num_notario" size="5"></td>
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
