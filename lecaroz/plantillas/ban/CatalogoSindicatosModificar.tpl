<form action="" method="post" name="modificar" class="FormValidator" id="modificar">
	<input name="id" type="hidden" id="id" value="{id}">
	<table class="table">
		<thead>
			<tr>
				<th colspan="2">&nbsp;</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td align="left" scope="row" class="bold">Sindicato</td>
				<td><input name="nombre" type="text" class="validate toText cleanText toUpper" id="nombre" value="{nombre}" size="30" maxlength="70" /></td>
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
		<input type="button" name="do_modificar" id="do_modificar" value="Modificar" />
	</p>
</form>
