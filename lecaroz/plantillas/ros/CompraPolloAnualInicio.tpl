<form name="inicio_form" class="FormValidator" id="inicio_form">
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
				<td class="bold">Administrador</td>
				<td>
					<select name="admin" id="admin">
						<option value=""></option>
						<!-- START BLOCK : admin -->
						<option value="{value}">{text}</option>
						<!-- END BLOCK : admin -->
					</select>
				</td>
			</tr>
			<tr>
				<td class="bold">A&ntilde;o</td>
				<td>
					<input name="anio" type="text" class="validate focus toPosInt center" id="anio" size="4" maxlength="4" value="{anio}" />
				</td>
			</tr>
			<tr>
				<td class="bold">Proveedor</td>
				<td>
					<select name="num_pro" id="num_pro">
						<option value=""></option>
						<option value="13">13 POLLOS GUERRA</option>
						<option value="482">482 CENTRAL DE POLLOS Y CARNES S.A. DE C.V.</option>
						<option value="204">204 GONZALEZ AYALA JOSE REGINO</option>
						<option value="2112">2112 COMERCIALIZADORA DE CARNES DE MEXICO S DE RL DE CV</option>
						<option value="1757">1757 BOTANAS DEL CARRITO SA DE CV</option>
						<option value="2324">2324 ALIMENTOS ESPECIALIZADOS GALICIA, S.A. DE C.V.</option>
						<option value="289" class="red">COMPRAS DIRECTAS</option>
					</select>
				</td>
			</tr>
			<tr>
				<td class="bold">Tipo</td>
				<td>
					<input type="radio" name="tipo" id="tipo_compras" value="compras" checked=""> Compras
					<input type="radio" name="tipo" id="tipo_ventas" value="ventas"> Ventas
				</td>
			</tr>
			<tr>
				<td class="bold">Producto</td>
				<td>
					<select name="codmp" id="codmp">
						<option value="">TODOS LOS POLLOS (S&Oacute;LO SUMA C&Oacute;DIGOS 160, 600, 700 Y 573)</option>
						<!-- START BLOCK : mp -->
						<option value="{value}">{value} {text}</option>
						<!-- END BLOCK : mp -->
					</select>
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
