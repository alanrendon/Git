<form name="inicio" class="FormValidator" id="inicio">
	<table class="table">
		<thead>
			<tr>
				<th colspan="2" scope="row">&nbsp;</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td align="left" scope="row">Nombre</td>
				<td><input name="nombre" type="text" class="validate toText cleanText toUpper" id="nombre" size="40" /></td>
			</tr>
			<tr>
				<td align="left" scope="row">Ap. paterno</td>
				<td><input name="ap_paterno" type="text" class="validate toText cleanText toUpper" id="ap_paterno" size="40" /></td>
			</tr>
			<tr>
				<td align="left" scope="row">Ap. materno</td>
				<td><input name="ap_materno" type="text" class="validate toText cleanText toUpper" id="ap_materno" size="40" /></td>
			</tr>
			<tr>
				<td align="left" scope="row">Tipo</td>
				<td>
					<!-- START BLOCK : tipo -->
					<input name="tipo[]" type="checkbox" id="tipo" value="{tipo}" checked="checked" />
					{descripcion} 
					<!-- END BLOCK : tipo -->
				</td>
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
