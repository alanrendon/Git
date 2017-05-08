<form name="definir_fecha" class="FormValidator" id="definir_fecha">
	<input name="num_cia" type="hidden" id="num_cia" value="{num_cia}" />
	<table class="table">
		<thead>
			<tr>
				<th colspan="2" scope="row">&nbsp;</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td class="bold font12" scope="row">Compa&ntilde;&iacute;</td>
				<td class="bold font12">{num_cia} {nombre_cia}</td>
			</tr>
			<tr>
				<td class="bold font12" scope="row">Fecha</td>
				<td>
					<input name="fecha" type="text" class="validate focus toDate center bold font12" id="fecha" size="10" maxlength="10" />
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
		<input type="button" name="cancelar" id="cancelar" value="Cancelar" />
		&nbsp;&nbsp;
		<input type="button" name="siguiente" id="siguiente" value="Siguiente" />
	</p>
</form>
