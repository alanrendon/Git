
<form name="proceso_secuencial" class="FormValidator" id="proceso_secuencial">
	<input name="num_cia" type="hidden" id="num_cia" value="{num_cia}" />
	<input name="fecha" type="hidden" id="fecha" value="{fecha}" />
	<table class="table">
		<thead>
			<tr>
				<th colspan="2">&nbsp;</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td class="bold font12">Compa&ntilde;&iacute;a</td>
				<td class="bold font12">{num_cia} {nombre_cia}</td>
			</tr>
			<tr>
				<td class="bold font12">Fecha</td>
				<td class="bold font12">{fecha_escrita}</td>
			</tr>
		</tbody>
		<tfoot>
			<tr>
				<td colspan="2">&nbsp;</td>
			</tr>
		</tfoot>
	</table>
	<br />
	<div id="compra_directa" class="show">
		<div class="bold font14" style="margin-bottom:12px;">Compra directa</div>
		<table class="table">
			<thead>
				<tr>
					<th>Producto</th>
					<th>Cantidad</th>
					<th>Kilos</th>
					<th>Precio</th>
					<th>Importe</th>
					<th>Aplica<br />gastos</th>
					<th>Proveedor</th>
					<th>Factura o<br>remisi&oacute;n</th>
				</tr>
			</thead>
			<tbody>
				<!-- START BLOCK : cd_row -->
				<tr>
					<td>
						<input name="cd_codmp[]" type="text" id="cd_codmp" class="validate focus toPosInt right" size="3" value="{cd_codmp}" />
						<input name="cd_nombremp[]" type="text" id="cd_nombremp" size="20" value="{cd_nombremp}" disabled="disabled" />
						<input name="cd_precio_compra[]" type="hidden" id="cd_precio_compra" value="{cd_precio}" />
						<input name="cd_precio_inv[]" type="hidden" id="cd_precio_inv" value="{cd_precio_inv}" />
						<input name="cd_min[]" type="hidden" id="cd_min" value="{cd_min}" />
						<input name="cd_max[]" type="hidden" id="cd_max" value="{cd_max}" />
					</td>
					<td>
						<input name="cd_cantidad[]" type="text" id="cd_cantidad" class="validate focus numberPosFormat right" precision="2" size="6" value="{cd_cantidad}" />
					</td>
					<td>
						<input name="cd_kilos[]" type="text" id="cd_kilos" class="validate focus numberPosFormat right" precision="2" size="6" value="{cd_kilos}" />
					</td>
					<td>
						<input name="cd_precio[]" type="text" id="cd_precio" class="right" size="6" value="{cd_precio}" readonly="readonly" />
					</td>
					<td>
						<input name="cd_importe[]" type="text" id="cd_importe" class="bold blue right" size="8" value="{cd_importe}" readonly="readonly" />
					</td>
					<td class="center">
						<input name="cd_aplica_gasto[]" type="checkbox" id="cd_aplica_gasto" value="{i}"{cd_aplica_gasto} />
					</td>
					<td>
						<input name="cd_num_pro[]" type="text" id="cd_num_pro" class="validate focus toPosInt right" size="3" value="{cd_num_pro}"{cd_num_pro_readonly} />
						<input name="cd_nombre_pro[]" type="text" id="cd_nombre_pro" size="20" value="{cd_nombre_pro}" disabled="disabled" />
					</td>
					<td>
						<input name="cd_num_fact[]" type="text" id="cd_num_fact" class="validate onlyNumbersAndLetters cleanText toUpper" size="20" value="{cd_num_fact}"{cd_num_pro_readonly} />
					</td>
				</tr>
				<!-- END BLOCK : cd_row -->
			</tbody>
			<tfoot>
				<tr>
					<td colspan="2"><button type="button" id="modificar_precios_compra">Precios de compra</button></td>
					<td colspan="2" class="bold right">Total compras</td>
					<td><input name="cd_total" type="text" id="cd_total" class="bold right" size="8" value="{cd_total}" readonly="readonly" /></td>
					<td colspan="4">&nbsp;</td>
				</tr>
			</tfoot>
		</table>
		<p>
			<button type="button" id="cd_cancelar">Cancelar</button>
			&nbsp;&nbsp;
			<button type="button" id="cd_siguiente">Siguiente</button>
		</p>
	</div>
	<div id="ventas" class="hide">
		<div class="bold font14" style="margin-bottom:12px;">Ventas</div>
		<table class="table">
			<thead>
				<tr>
					<th>Producto</th>
					<th>Existencia</th>
					<th>Cantidad</th>
					<th>Precio</th>
					<th>Importe</th>
				</tr>
			</thead>
			<tbody>
				<!-- START BLOCK : v_row -->
				<tr>
					<td>
						<input name="v_id[]" type="hidden" id="v_id" value="{v_id}" />
						<input name="v_codmp[]" type="hidden" id="v_codmp" value="{v_codmp}" />
						{v_codmp} {v_nombremp}
					</td>
					<td>
						<input name="v_existencia_inicial[]" type="hidden" id="v_existencia_inicial" value="{v_existencia_inicial}" />
						<input name="v_compras[]" type="hidden" id="v_compras" value="{v_compras}" />
						<input name="v_sin_existencia[]" type="hidden" id="v_sin_existencia" value="{v_sin_existencia}" />
						<input name="v_existencia[]" type="text" id="v_existencia" class="bold right {v_existencia_color}" size="6" value="{v_existencia}" readonly="readonly" />
					</td>
					<td>
						<input name="v_cantidad[]" type="text" id="v_cantidad" class="validate focus numberPosFormat right" precision="2" size="6" value="{v_cantidad}" />
					</td>
					<td>
						<input name="v_precio[]" type="text" id="v_precio" class="right" size="6" value="{v_precio_venta}" style="cursor:pointer;" readonly="readonly" />
					</td>
					<td>
						<input name="v_importe[]" type="text" id="v_importe" class="bold right blue" size="8" value="{v_importe}" readonly="readonly" />
					</td>
				</tr>
				<!-- END BLOCK : v_row -->
				<tr>
					<td colspan="4" class="bold right">Otros ingresos</td>
					<td>
						<input name="v_otros" type="text" id="v_otros" class="validate focus numberPosFormat right bold green" precision="2" size="8" value="{v_otros}" />
					</td>
				</tr>
			</tbody>
			<tfoot>
				<tr>
					<td colspan="4" class="bold right">Total ventas</td>
					<td><input name="v_total" type="text" id="v_total" class="bold right" size="8" value="{v_total}" readonly="readonly" /></td>
				</tr>
			</tfoot>
		</table>
		<p>
			<button type="button" id="v_anterior">Anterior</button>
			&nbsp;&nbsp;
			<button type="button" id="v_cancelar">Cancelar</button>
			&nbsp;&nbsp;
			<button type="button" id="v_siguiente">Siguiente</button>
		</p>
	</div>
	<div id="gastos" class="hide">
		<div class="bold font14" style="margin-bottom:12px;">Gastos</div>
		<table class="table">
			<thead>
				<tr>
					<th>C&oacute;digo</th>
					<th>Concepto</th>
					<th>Importe</th>
				</tr>
			</thead>
			<tbody>
				<!-- START BLOCK : g_row -->
				<tr>
					<td>
						<input name="g_codgastos[]" type="text" id="g_codgastos" class="validate focus toPosInt right" size="3" value="{g_codgastos}" />
						<input name="g_descripcion[]" type="text" id="g_descripcion" size="30" value="{g_descripcion}" disabled="disabled" />
					</td>
					<td>
						<input name="g_concepto[]" type="text" id="g_concepto" class="validate toText cleanText toUpper" size="50" maxlength="255" value="{g_concepto}" />
					</td>
					<td>
						<input name="g_importe[]" type="text" id="g_importe" class="validate focus numberPosFormat right" precision="2" size="10" value="{g_importe}" />
						<input name="g_cantidad[]" type="hidden" id="g_cantidad" value="{g_cantidad}" />
					</td>
				</tr>
				<!-- END BLOCK : g_row -->
			</tbody>
			<tfoot>
				<tr>
					<td colspan="2" class="bold right">Total</td>
					<td><input name="g_total" type="text" id="g_total" class="bold right" size="10" value="{g_total}" readonly="readonly" /></td>
				</tr>
				<tr>
					<td colspan="2" class="bold right">Total compras</td>
					<td><input name="g_compras" type="text" id="g_compras" class="bold right" size="10" value="{g_compras}" readonly="readonly" /></td>
				</tr>
				<tr>
					<td colspan="2" class="bold right">Total gastos</td>
					<td><input name="g_gastos" type="text" id="g_gastos" class="bold right" size="10" value="{g_gastos}" readonly="readonly" /></td>
				</tr>
			</tfoot>
		</table>
		<p>
			<button type="button" id="g_anterior">Anterior</button>
			&nbsp;&nbsp;
			<button type="button" id="g_cancelar">Cancelar</button>
			&nbsp;&nbsp;
			<button type="button" id="g_siguiente">Siguiente</button>
		</p>
	</div>
	<div id="prestamos" class="hide">
		<div class="bold font14" style="margin-bottom:12px;">Prestamos a empleados</div>
		<table class="table">
			<thead>
				<tr>
					<th colspan="6">Prestamos en sistema</th>
				</tr>
				<tr>
					<th>Empleado</th>
					<th>Saldo inicio</th>
					<th>Prestamo</th>
					<th>Abono</th>
					<th>Saldo final</th>
					<th><img src="/lecaroz/iconos/plus.png" id="alta_prestamo" class="icono" width="16" height="16" /></th>
				</tr>
			</thead>
			<tbody id="prestamos_sistema_table">
				<!-- START BLOCK : p_prestamo_sistema -->
				<tr>
					<td>
						<input name="p_id_emp[]" type="hidden" id="p_id_emp" value="{p_id_emp}" />
						{p_num_emp} {p_nombre_emp}
					</td>
					<td>
						<input name="p_saldo_emp_inicio[]" type="text" id="p_saldo_emp_inicio" class="bold right green" size="10" value="{p_saldo_emp_inicio}" readonly="readonly" />
					</td>
					<td>
						<input name="p_prestamo[]" type="text" id="p_prestamo" class="validate focus numberPosFormat right red" precision="2" size="10" value="{p_prestamo}" />
					</td>
					<td>
						<input name="p_abono[]" type="text" id="p_abono" class="validate focus numberPosFormat right blue" precision="2" size="10" value="{p_abono}" />
					</td>
					<td>
						<input name="p_saldo_emp_final[]" type="text" id="p_saldo_emp_final" class="bold right green" size="10" value="{p_saldo_emp_final}" readonly="readonly" />
					</td>
					<td>&nbsp;</td>
				</tr>
				<!-- END BLOCK : p_prestamo_sistema -->
			</tbody>
			<tfoot>
				<tr>
					<td class="bold right">Total</td>
					<td>
						<input name="p_total_saldo_inicio" type="text" id="p_total_saldo_inicio" class="bold right green" size="10" value="{p_total_saldo_inicio}" readonly="readonly" />
					</td>
					<td>
						<input name="p_total_prestamos" type="text" id="p_total_prestamos" class="bold right red" size="10" value="{p_total_prestamos}" readonly="readonly" />
					</td>
					<td>
						<input name="p_total_abonos" type="text" id="p_total_abonos" class="bold right blue" size="10" value="{p_total_abonos}" readonly="readonly" />
					</td>
					<td>
						<input name="p_total_saldo_final" type="text" id="p_total_saldo_final" class="bold right green" size="10" value="{p_total_saldo_final}" readonly="readonly" />
					</td>
					<td>&nbsp;</td>
				</tr>
			</tfoot>
		</table>
		<br />
		<table class="table">
			<thead>
				<tr>
					<th colspan="6">Prestamos en rosticer&iacute;a</th>
				</tr>
				<tr>
					<th>Empleado</th>
					<th>Movimiento</th>
					<th>Importe</th>
				</tr>
			</thead>
			<tbody>
				<!-- START BLOCK : p_mov_ros -->
				<tr>
					<td>
						<input name="p_id_tmp[]" type="hidden" id="p_id_tmp" value="{p_id_tmp}" />
						<input name="p_id_emp_tmp[]" type="hidden" id="p_id_emp_tmp" value="" />
						<input name="p_num_emp[]" type="text" id="p_num_emp" class="right" size="4" value="" readonly="readonly" />
						{p_nombre_emp}
					</td>
					<td class="{p_color}">
						<input name="p_tipo[]" type="text" id="p_tipo" class="{p_color}" size="10" value="{p_tipo}" readonly="readonly" />
					</td>
					<td>
						<input name="p_importe[]" type="text" id="p_importe" class="right {p_color}" size="10" value="{p_importe}" readonly="readonly" />
					</td>
				</tr>
				<!-- END BLOCK : p_mov_ros -->
			</tbody>
			<tfoot>
				<tr>
					<td colspan="3">&nbsp;</td>
				</tr>
			</tfoot>
		</table>
		<p>
			<button type="button" id="p_anterior">Anterior</button>
			&nbsp;&nbsp;
			<button type="button" id="p_cancelar">Cancelar</button>
			&nbsp;&nbsp;
			<button type="button" id="p_siguiente">Siguiente</button>
		</p>
	</div>
	<div id="tanques_gas" class="hide">
		<div class="bold font14" style="margin-bottom:12px;">Tanques de gas</div>
		<table class="table">
			<thead>
				<tr>
					<th>Tanque</th>
					<th>Capacidad</th>
					<th>Lectura</th>
					<th>Entrada</th>
					<th>Nota</th>
				</tr>
			</thead>
			<tbody id="prestamos_sistema_table">
				<!-- START BLOCK : tanque -->
				<tr>
					<td>{num_tanque} {nombre_tanque}</td>
					<td align="right" class="green">{capacidad}</td>
					<td align="right" class="red">{lectura}</td>
					<td align="right" class="blue">{entrada}</td>
					<td class="blue">{nota}</td>
				</tr>
				<!-- END BLOCK : tanque -->
				<!-- START BLOCK : no_tanques -->
				<tr colspan="5">
					<td class="bold red font14">La rosticer&iacute;a no tiene tanques de gas</td>
				</tr>
				<!-- END BLOCK : no_tanques -->
			</tbody>
			<tfoot>
				<tr>
					<td colspan="5">&nbsp;</td>
				</tr>
			</tfoot>
		</table>
		<p>
			<button type="button" id="tan_anterior">Anterior</button>
			&nbsp;&nbsp;
			<button type="button" id="tan_cancelar">Cancelar</button>
			&nbsp;&nbsp;
			<button type="button" id="tan_siguiente">Siguiente</button>
		</p>
	</div>
	<div id="totales" class="hide">
		<div class="bold font14" style="margin-bottom:12px;">Totales</div>
		<table class="table">
			<thead>
				<tr>
					<th colspan="2">&nbsp;</th>
				</tr>
			</thead>
			<tbody id="prestamos_sistema_table">
				<tr>
					<td class="font12 bold">Compras</td>
					<td id="total_compras_td" class="font12 bold right">0.00</td>
				</tr>
				<tr>
					<td class="font12 bold">Ventas</td>
					<td id="total_ventas_td" class="font12 bold right">0.00</td>
				</tr>
				<tr>
					<td class="font12 bold">Gastos</td>
					<td id="total_gastos_td" class="font12 bold right">0.00</td>
				</tr>
				<tr>
					<td class="font12 bold">Prestamos</td>
					<td id="total_prestamos_td" class="font12 bold right">0.00</td>
				</tr>
				<tr>
					<td class="font12 bold">Abonos</td>
					<td id="total_abonos_td" class="font12 bold right">0.00</td>
				</tr>
				<tr>
					<td class="font14 bold">Efectivo</td>
					<td id="total_efectivo_td" class="font14 bold right">0.00</td>
				</tr>
			</tbody>
			<tfoot>
				<tr>
					<td colspan="2">&nbsp;</td>
				</tr>
			</tfoot>
		</table>
		<p>
			<button type="button" id="t_anterior">Anterior</button>
			&nbsp;&nbsp;
			<button type="button" id="t_cancelar">Cancelar</button>
			&nbsp;&nbsp;
			<button type="button" id="t_siguiente">Terminar</button>
		</p>
	</div>
</form>
