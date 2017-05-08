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
					Buscar todas<br />
					<input name="status" id="status_1" type="radio" value="1" />
					Buscar solo pendientes<br />
					<input name="status" id="status_2" type="radio" value="2" />
					Buscar solo pagadas<br />
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					<input name="pag" id="pag_0" type="radio" value="0" checked="checked" disabled="disabled" />
					Todas las pagadas<br />
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					<input name="pag" id="pag_1" type="radio" value="1" disabled="disabled" />
					Solo pendientes por cobrar<br />
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					<input name="pag" id="pag_2" type="radio" value="2" disabled="disabled" />
					Solo cobradas<br />
					<input name="pollos_facturado" type="checkbox" id="pollos_facturadas" value="1" checked="checked" />
					Facturado<br />
					<input name="pollos_contado" type="checkbox" id="pollos_contado" value="1" checked="checked" />
					Contado<br />
					<input name="agrupar_por_rfc" type="checkbox" id="agrupar_por_rfc" value="1" />
					Agrupar por R.F.C.<br />
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
