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
				<td class="bold">R.F.C.</td>
				<td>
					<select name="rfc" id="rfc">
						<option value=""></option>
						<!-- START BLOCK : rfc -->
						<option value="{value}">{text}</option>
						<!-- END BLOCK : rfc -->
					</select>
				</td>
			</tr>
			<tr>
				<td class="bold">Administrador</td>
				<td>
					<select name="admin" id="admin">
						<option value="" class="logo_banco"></option>
						<!-- START BLOCK : admin -->
						<option value="{value}">{text}</option>
						<!-- END BLOCK : admin -->
					</select>
				</td>
			</tr>
			<tr>
				<td class="bold">Proveedor(es)</td>
				<td><input name="pros" type="text" class="validate toInterval" id="pros" size="40" /></td>
			</tr>
			<tr>
				<td class="bold">Gasto(s)</td>
				<td><input name="gastos" type="text" class="validate toInterval" id="gastos" size="40" /></td>
			</tr>
			<tr>
				<td class="bold">Periodo de facturaci&oacute;n</td>
				<td><input name="fecha1" type="text" class="validate focus toDate center" id="fecha1" value="{fecha1}" size="10" maxlength="10" />
					al
					<input name="fecha2" type="text" class="validate focus toDate center" id="fecha2" value="{fecha2}" size="10" maxlength="10" /></td>
			</tr>
			<tr>
				<td class="bold">Periodo de cobro</td>
				<td><input name="fecha_cobro1" type="text" class="validate focus toDate center" id="fecha_cobro1" value="{fecha_cobro1}" size="10" maxlength="10" />
					al
					<input name="fecha_cobro2" type="text" class="validate focus toDate center" id="fecha_cobro2" value="{fecha_cobro2}" size="10" maxlength="10" /></td>
			</tr>
			<tr>
				<td class="bold">Periodo de captura</td>
				<td><input name="fecha_cap1" type="text" class="validate focus toDate center" id="fecha_cap1" value="" size="10" maxlength="10" />
					al
					<input name="fecha_cap2" type="text" class="validate focus toDate center" id="fecha_cap2" value="" size="10" maxlength="10" /></td>
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
					<input name="ordenar_por_rfc" type="checkbox" id="ordenar_por_rfc" value="1" />
					Ordenar por R.F.C.<br />
					<input name="pollos_facturado" type="checkbox" id="pollos_facturadas" value="1" checked="checked" />
					(Pollos) Facturado<br />
					<input name="pollos_contado" type="checkbox" id="pollos_contado" value="1" checked="checked" />
					(Pollos) Contado<br />
					<input name="usuario" type="checkbox" id="usuario" value="1" />
					Mostrar solo las del usuario que consulta
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
