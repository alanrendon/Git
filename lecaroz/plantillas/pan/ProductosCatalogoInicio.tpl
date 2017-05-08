<form name="inicio" class="FormValidator" id="inicio">
	<table class="table">
		<thead>
			<tr>
				<th colspan="2" scope="row">&nbsp;</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td align="left" scope="row">Productos(s)</td>
				<td><input name="productos" type="text" class="validate toInterval" id="productos" size="40" /></td>
			</tr>
			<tr>
				<td align="left" scope="row">Descripcion</td>
				<td><input name="descripcion" type="text" class="validate toText cleanText toUpper" id="descripcion" size="40" /></td>
			</tr>
		</tbody>
		<tfoot>
			<tr>
				<td colspan="2" scope="row">&nbsp;</td>
			</tr>
		</tfoot>
	</table>
	<p>
		<input type="button" name="consultar" id="consultar" value="Consultar" />
	</p>
</form>
