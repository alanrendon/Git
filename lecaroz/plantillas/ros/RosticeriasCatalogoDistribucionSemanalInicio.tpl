<form name="inicio" class="FormValidator" id="inicio">
	<table class="table">
		<thead>
			<tr>
				<th colspan="2" scope="row">&nbsp;</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td align="left" scope="row">Compa&ntilde;&iacute;(s)</td>
				<td>
					<input name="cias" type="text" class="validate toInterval" id="cias" size="40" />
				</td>
			</tr>
			<tr>
				<td align="left" scope="row">Proveedor</td>
				<td>
					<select name="num_pro" id="num_pro">
						<!-- START BLOCK : pro -->
						<option value="{value}">{text}</option>
						<!-- END BLOCK : pro -->
					</select>
				</td>
			</tr>
			<tr>
				<td align="left" scope="row">Semana</td>
				<td>
					<select name="fecha_inicio_semana" id="fecha_inicio_semana">
						<!-- START BLOCK : semana -->
						<option value="{fecha1}">SEMANA DEL {fecha1} AL {fecha2}</option>
						<!-- END BLOCK : semana -->
					</select>
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
