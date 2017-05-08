<form name="inicio" class="FormValidator" id="inicio">
	<table class="table">
		<thead>
			<tr>
				<th colspan="2" scope="row">&nbsp;</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td align="left" scope="row">Folio(s) contabilidad</td>
				<td>
					<input name="folios" type="text" class="validate focus toInterval" id="folios" size="30" />
				</td>
			</tr>
			<tr>
				<td align="left" scope="row">Periodo</td>
				<td>
					<input name="fecha1" type="text" class="validate focus toDate center" id="fecha1" size="10" maxlength="10" />
					al
					<input name="fecha2" type="text" class="validate focus toDate center" id="fecha2" size="10" maxlength="10" />
				</td>
			</tr>
			<tr>
				<td align="left" scope="row">Contador</td>
				<td>
					<select name="conta" id="conta">
						<option value="" selected="selected"></option>
						<!-- START BLOCK : conta -->
						<option value="{value}">{text}</option>
						<!-- END BLOCK : conta -->
					</select>
				</td>
			</tr>
			<tr>
				<td align="left" scope="row">Compa&ntilde;&iacute;a(s)</td>
				<td>
					<input name="cias" type="text" class="validate focus toInterval" id="cias" size="30" />
				</td>
			</tr>
			<tr>
				<td align="left" scope="row">Proveedor(es)</td>
				<td>
					<input name="pros" type="text" class="validate focus toInterval" id="pros" size="30" />
				</td>
			</tr>
			<tr>
				<td align="left" scope="row">Factura(s)</td>
				<td>
					<input name="facs" type="text" class="validate focus toIntervalChars toUpper" id="facs" size="30" />
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
		<input type="button" name="nuevo" id="nuevo" value="Nuevo reporte" />&nbsp;&nbsp;
		<input type="button" name="consultar" id="consultar" value="Consultar" />
	</p>
</form>
