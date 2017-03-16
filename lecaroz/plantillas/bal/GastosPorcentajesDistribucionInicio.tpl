<form name="inicio_form" class="FormValidator" id="inicio_form">
	<table class="table">
		<thead>
			<tr>
				<th colspan="2" scope="row">&nbsp;</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td align="left" scope="row">Gasto</td>
				<td>
					<input name="codgastos" type="text" class="validate focus toPosInt right" id="codgastos" size="3" value="" />
					<input name="descripcion" type="text" id="descripcion" size="40" value="" disabled="disabled" />
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
	<!-- START BLOCK : gastos -->
	<table class="table">
		<thead>
			<tr>
				<th>C&oacute;digos con porcentajes</th>
			</tr>
		</thead>
		<tbody>
			<!-- START BLOCK : row -->
			<tr>
				<td align="left" scope="row">{codgastos} {descripcion}</td>
			</tr>
			<!-- END BLOCK : row -->
		</tbody>
		<tfoot>
			<tr>
				<td>&nbsp;</td>
			</tr>
		</tfoot>
	</table>
	<!-- START BLOCK : gastos -->
</form>
