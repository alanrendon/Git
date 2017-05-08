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
				<td><input name="cias" type="text" class="validate toInterval" id="cias" size="40"></td>
			</tr>
			<tr>
				<td class="bold">Administrador</td>
				<td>
					<select name="admin" id="admin">
						<option value=""></option>
						<option value="-1" style="font-weight:bold;">Agrupado por Administrador</option>
						<!-- START BLOCK : admin -->
						<option value="{value}">{text}</option>
						<!-- END BLOCK : admin -->
					</select>
				</td>
			</tr>
			<tr>
				<td class="bold">A&ntilde;o(s)</td>
				<td>
					<input name="anio[]" type="text" class="validate focus toPosInt center" id="anio" size="4" value="{anio}">
					<input name="anio[]" type="text" class="validate focus toPosInt center" id="anio" size="4">
					<input name="anio[]" type="text" class="validate focus toPosInt center" id="anio" size="4">
					<input name="anio[]" type="text" class="validate focus toPosInt center" id="anio" size="4">
					<input name="anio[]" type="text" class="validate focus toPosInt center" id="anio" size="4">
				</td>
			</tr>
			<tr>
				<td class="bold">Mes</td>
				<td>
					<select name="mes" id="mes">
						<!-- START BLOCK : mes -->
						<option value="{value}"{selected}>{text}</option>
						<!-- END BLOCK : mes -->
					</select>
				</td>
			</tr>
			<tr>
				<td class="bold">Conceptos</td>
				<td>
					<select name="concepto" id="concepto">
						<option style="font-weight:bold; text-decoration:underline; font-weight:bold; color:#C00;" disabled="disabled">PANADERIAS</option>
						<option value="venta_puerta">Venta en puerta</option>
						<option value="bases">Bases</option>
						<option value="barredura">Barredura</option>
						<option value="pastillaje">Pastillaje</option>
						<option value="abono_emp">Abono empleados</option>
						<option value="otros">Otros</option>
						<option value="total_otros">Total otros</option>
						<option value="abono_reparto">Abono reparto</option>
						<option value="errores">Errores</option>
						<option value="ventas_netas" style="font-weight:bold; color:#00C;">Ventas netas</option>
						<option value="inv_ant">Inventario anterior</option>
						<option value="compras">Compras</option>
						<option value="mercancias">Mercancias</option>
						<option value="inv_act">Inventario actual</option>
						<option value="mat_prima_utilizada">Mat. prima utilizada</option>
						<option value="mano_obra">Mano de obra</option>
						<option value="panaderos">Panaderos</option>
						<option value="gastos_fab">Gastos de fabricaci&oacute;n</option>
						<option value="costo_produccion" style="font-weight:bold; color:#00C;">Costo de produccion</option>
						<option value="utilidad_bruta" style="font-weight:bold; color:#00C;">Utilidad bruta</option>
						<option value="pan_comprado">Pan comprado</option>
						<option value="gastos_generales">Gastos generales</option>
						<option value="gastos_caja">Gastos por caja</option>
						<option value="reserva_aguinaldos">Reserva para Aguinaldos</option>
						<option value="gastos_otras_cias">Gastos Pagados por otras</option>
						<option value="total_gastos" style="font-weight: bold; color:#00C;">Total de gastos</option>
						<option value="ingresos_ext" style="font-weight: bold; color:#00C;">Ingresos extraordinarios</option>
						<option value="utilidad_neta" style="font-weight: bold; color:#00C;">Utilidad del Mes</option>
						<option value="produccion_total" style="font-weight: bold; color:#060">Producci&oacute;n total</option>
						<option value="faltante_pan" style="font-weight: bold; color:#060">Faltante de pan</option>
						<option value="rezago_ini" style="font-weight: bold; color:#060">Rezago inicial</option>
						<option value="rezago_fin" style="font-weight: bold; color:#060">Rezago final</option>
						<option value="efectivo" style="font-weight: bold; color:#060">Efectivo</option>
						<option value="utilidad_bruta_pro" style="font-weight: bold; color:#060">Utilidad bruta / Producci&oacute;n</option>
						<option value="utilidad_pro" style="font-weight: bold; color:#060">Utilidad neta / producci&oacute;n</option>
						<option value="mp_pro" style="font-weight: bold; color:#060">Materia prima / producci&oacute;n</option>
						<option value="clientes" style="font-weight: bold; color:#060">Clientes</option>
						<option disabled="disabled"></option>
						<option style="font-weight:bold; text-decoration:underline; font-weight:bold; color:#C00;" disabled="disabled">ROSTICERIAS</option>
						<option value="utilidad_ventas" style="font-weight: bold; color:#060">Utilidad / Ventas</option>
						<option value="utilidad_mat_prima" style="font-weight: bold; color:#060">Utilidad / Materia prima</option>
						<option value="mat_prima_ventas" style="font-weight: bold; color:#060">Materia prima / Ventas</option>
						<option value="pollos" style="font-weight: bold; color:#060">Pollos</option>
						<option value="pescuezos" style="font-weight: bold; color:#060">Pescuezos</option>
						<option value="precio_pollo" style="font-weight: bold; color:#060">Precio por kilo</option>
						<option value="kilos_pollo" style="font-weight: bold; color:#060">Kilos de pollo comprados</option>
					</select>
				</td>
			</tr>
			<tr>
				<td class="bold">Opciones</td>
				<td>
					<input name="incluir_ingresos_ext" type="checkbox" id="incluir_ingresos_ext" value="1" checked=""> Incluir ingresos extraordinarios
					<br><input name="descontar_errores" type="checkbox" id="descontar_errores" value="1" checked=""> Restar errores de la venta en puerta
					<br><input name="sumar_importes" type="checkbox" id="sumar_importes" value="1"> Sumar importes a utilidad del mes <button type="button">Ver</button>
				</td>
			</tr>
			<tr>
				<td class="bold">Tipo de reporte</td>
				<td>
					<input name="tipo_reporte" type="radio" id="tipo_reporte_1" value="listado" checked=""> Listado
					<br><input name="tipo_reporte" type="radio" id="tipo_reporte_2" value="promedios"> Promedios
					<br><input name="tipo_reporte" type="radio" id="tipo_reporte_3" value="acumulado"> Acumulado
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
		<button type="button" id="consultar">Consultar</button>
	</p>
</form>
