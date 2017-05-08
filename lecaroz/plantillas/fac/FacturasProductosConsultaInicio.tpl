<form name="inicio" class="FormValidator" id="inicio">
	<table class="table">
		<thead>
			<tr>
				<th colspan="2" scope="col">&nbsp;</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td class="bold">Compa&ntilde;&iacute;a(s)</td>
				<td><input name="cias" type="text" class="validate toInterval" id="cias" size="40" /></td>
			</tr>
			<tr>
				<td class="bold">Proveedor(es)</td>
				<td><input name="pros" type="text" class="validate toInterval" id="pros" size="40" /></td>
			</tr>
			<tr>
				<td class="bold">Producto(s)</td>
				<td><input name="mps" type="text" class="validate toInterval" id="mps" size="40" /></td>
			</tr>
			<tr>
				<td class="bold">Periodo</td>
				<td><input name="fecha1" type="text" class="validate focus toDate center" id="fecha1" value="{fecha1}" size="10" maxlength="10" />
					al
					<input name="fecha2" type="text" class="validate focus toDate center" id="fecha2" value="{fecha2}" size="10" maxlength="10" /></td>
			</tr>
			<tr>
				<td class="bold">Facturas</td>
				<td><input name="facturas" type="text" class="validate toIntervalChars toUpper" id="facturas" size="40" /></td>
			</tr>
			<tr>
				<td class="bold">Opciones</td>
				<td>
					<input name="status" id="status_0" type="radio" value="0" checked="checked" />
					Todas<br />
					<input name="status" id="status_1" type="radio" value="1" />
					Pendientes<br />
					<input name="status" id="status_2" type="radio" value="2" />
					Pagadas<br />
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					<input name="pag" id="pag_0" type="radio" value="0" checked="checked" disabled="disabled" />
					Todas<br />
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					<input name="pag" id="pag_1" type="radio" value="1" disabled="disabled" />
					Pendientes<br />
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					<input name="pag" id="pag_2" type="radio" value="2" disabled="disabled" />
					Cobradas<br />
					<input name="usuario" type="checkbox" id="usuario" value="1" />
					Por usuario
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
		<input type="button" name="consultar" id="consultar" value="Consultar" />
	</p>
</form>
