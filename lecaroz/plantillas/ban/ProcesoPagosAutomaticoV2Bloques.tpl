<!-- START BLOCK : datos -->
		<form name="Datos" class="formulario" id="Datos">
			<table class="tabla_captura">
				<tr>
					<th align="left" scope="row">Compa&ntilde;&iacute;as </th>
					<td class="linea_off"><input name="cias_intervalo" type="text" class="cap toInterval" id="cias_intervalo" size="30" /></td>
				</tr>
				<tr>
					<th align="left" scope="row">Proveedores </th>
					<td class="linea_on"><input name="pros_intervalo" type="text" class="cap toInterval" id="pros_intervalo" size="30" /></td>
				</tr>
				<tr>
					<th align="left" scope="row">Fecha de corte </th>
					<td class="linea_off"><input name="fecha_corte" type="text" class="cap toDate alignCenter" id="fecha_corte" value="{fecha}" size="10" maxlength="10" /></td>
				</tr>
				<tr>
					<th align="left" scope="row">Fecha cheque y/o p&oacute;liza </th>
					<td class="linea_on"><input name="fecha_cheque" type="text" class="cap toDate alignCenter" id="fecha_cheque" value="{fecha}" size="10" maxlength="10" /></td>
				</tr>
				<tr>
					<th align="left" scope="row">D&iacute;as en dep&oacute;sito </th>
					<td class="linea_off"><input name="dias_deposito" type="text" class="cap toPosInt alignCenter" id="dias_deposito" value="1" size="3" />
					<input name="dep_tra" type="checkbox" class="checkbox" id="dep_tra" value="1" checked="checked" />
					<span class="font8">(aplicar a transferencias)</span></td>
				</tr>
				<tr>
					<th align="left" scope="row">Banco</th>
					<td class="linea_on">
				<select name="cuenta" id="cuenta">
				<option value="-3" selected="selected">Alternar saldos</option>
							<option value="-2">Alternar (Santander-Banorte)</option>
				<option value="-1">Alternar (Banorte-Santander)</option>
							<option value="1">Banorte</option>
							<option value="2">Santander</option>
						</select>
			</td>
				</tr>
				<tr>
					<th align="left" scope="row">Próximos depósitos</th>
					<td class="linea_off"><select name="prox" id="prox">
						<option value="1" selected="selected">BANORTE</option>
						<option value="2">SANTANDER</option>
			 		</select></td>
			 		</tr>
				<tr>
					<th align="left" scope="row">Tipo de pago </th>
					<td class="linea_on">
				<select name="tipo_pago" id="tipo_pago">
							<option value="0" selected="selected">Cheques y transferencias</option>
							<option value="1">Cheques (todos los proveedores)</option>
							<option value="2">Solo proveedores con cheque</option>
							<option value="3">Solo proveedores con transferencia</option>
						</select>
					</td>
				</tr>
				<tr>
					<th align="left" scope="row">Criterio para pago </th>
					<td class="linea_off">
				<select name="criterio" id="criterio">
					<option value="0" selected="selected">Antig&uuml;edad</option>
					<option value="1">Prioridad</option>
				</select>
					</td>
				</tr>
				<tr>
					<th align="left" scope="row">Proveedores sin pago </th>
					<td class="linea_on"><input name="pros_sin_pago" type="text" class="cap toInterval" id="pros_sin_pago" size="30" /></td>
				</tr>
				<tr>
					<th align="left" scope="row">Compa&ntilde;&iacute;as que no pagaran </th>
					<td class="linea_off"><input name="cias_no_pago" type="text" class="cap toInterval" id="cias_no_pago" size="30" /></td>
				</tr>
				<tr>
					<th align="left" scope="row">Pagos obligatorios </th>
					<td class="linea_on"><input name="pagos_obligados" type="text" class="cap toInterval" id="pagos_obligados" size="30" /></td>
				</tr>
				<tr>
					<th align="left" scope="row">N&uacute;mero de cheques </th>
					<td class="linea_off"><input name="num_cheques" type="text" class="cap numberPosFormat alignRight" id="num_cheques" size="5" /></td>
				</tr>
			</table>
			<p>
				<input name="siguiente" type="button" class="boton" id="siguiente" value="Siguiente" />
			</p>
		</form>
<!-- END BLOCK : datos -->
<!-- START BLOCK : facturas -->
		<form name="Facturas" class="formulario" id="Facturas">
		<input name="fecha_cheque" id="fecha_cheque" type="hidden" value="{fecha_cheque}" />
		<table class="tabla_captura">
				<tr>
					<th colspan="8" align="left" scope="col"><input name="checkall" type="checkbox" id="checkall" checked="checked" />
					Marcar todos <span style="float:right;"><img src="imagenes/flag_blue.png" width="16" height="16" />Normales, <img src="imagenes/flag_red.png" width="16" height="16" />Obligatorios</span></th>
				</tr>
				<tr>
					<td colspan="8" align="left" scope="col">&nbsp;</td>
				</tr>
				<!-- START BLOCK : cia -->
			<tr>
					<th colspan="8" align="left" class="font12" scope="col">{num_cia} {nombre}</th>
				</tr>
				<!-- START BLOCK : saldo -->
			<tr style="background-color:#{color_banco};">
					<td colspan="6" class="bold" align="right" scope="col">[{cuenta}] {banco}</td>
					<td colspan="2" align="right" class="bold" scope="col">{saldo}</td>
				</tr>
			<!-- END BLOCK : saldo -->
			<tr style="background-color:#{color_banco};">
				<th colspan="6" class="font12" align="right" scope="col">Saldo para pagar </th>
				<th colspan="2" align="right" class="font12" scope="col">{saldo}</th>
				</tr>
				<tr>
					<th><input name="checkblock" type="checkbox" id="checkblock" alt="{num_cia}" checked="checked" /></th>
					<th>Proveedor</th>
					<th>Compa&ntilde;&iacute;a</th>
					<th>Fecha</th>
					<th>Factura</th>
					<th>Concepto</th>
					<th>Importe</th>
					<th><img src="imagenes/info.png" width="16" height="16" /></th>
				</tr>
				<!-- START BLOCK : row -->
			<tr style="background-color:#{color_banco};">
					<td align="center"><input name="id[]" type="checkbox" id="id" alt="{cia_pago}" value="{id}" checked="checked" /></td>
					<td>{num_pro} {nombre}</td>
					<td>{num_cia} {nombre_cia}</td>
					<td align="center">{fecha}</td>
					<td align="right">{num_fact}</td>
					<td>{concepto}</td>
					<td align="right">{importe}</td>
					<td align="center"><img src="imagenes/flag_{flag_color}.png" width="16" height="16" /></td>
			</tr>
			<!-- END BLOCK : row -->
				<tr>
					<th colspan="6" align="right">Total a pagar </th>
					<th class="font12" id="total{num_cia}" align="right">{total}</th>
					<th class="font12" id="total{num_cia}" align="right">&nbsp;</th>
				</tr>
        <!-- START BLOCK : saldo_restante -->
        <tr>
          <th colspan="6" align="right">Saldo restante [{banco}]</th>
          <th class="font12" id="total{num_cia}" align="right">{saldo_restante}</th>
          <th class="font12" id="total{num_cia}" align="right">&nbsp;</th>
        </tr>
        <!-- END BLOCK : saldo_restante -->
				<tr>
					<td colspan="8">&nbsp;</td>
				</tr>
			<!-- END BLOCK : cia -->
				<tr>
					<th colspan="6" align="right" class="font16">Total a pagar </th>
					<th id="gran_total" align="right" class="font16">{total}</th>
					<th id="gran_total" align="right" class="font16">&nbsp;</th>
				</tr>
				<tr>
					<th colspan="6" align="right" class="font16">N&uacute;mero de Facturas </th>
					<th id="num_facts" align="right" class="font16">{num_facts}</th>
					<th id="num_facts" align="right" class="font16">&nbsp;</th>
				</tr>
			</table>
		 <p>
				<input name="regresar" type="button" class="boton" id="regresar" value="Regresar" />
			&nbsp;&nbsp;
			<input name="siguiente" type="button" class="boton" id="siguiente" value="Siguiente" />
			</p>
		</form>
<!-- END BLOCK : facturas -->
<!-- START BLOCK : pagado -->
<table class="tabla_captura">
	<tr>
		<th scope="col">Compa&ntilde;&iacute;a</th>
		<th scope="col">Banco</th>
		<th scope="col">Folio</th>
		<th scope="col">Tipo</th>
		<th scope="col">Proveedor</th>
		<th scope="col">Concepto</th>
		<th scope="col">Facturas</th>
		<th scope="col">Importe</th>
	</tr>
	<!-- START BLOCK : pago -->
	<tr class="linea_{color}">
		<td>{num_cia} {nombre_cia}{cia_pago}</td>
		<td>{banco}</td>
		<td align="right">{folio}</td>
		<td align="center">{tipo}</td>
		<td>{num_pro} {nombre_pro} </td>
		<td>{concepto}</td>
		<td>{facturas}</td>
		<td align="right">{importe}</td>
	</tr>
	<!-- START BLOCK : pago -->
	<tr>
		<th colspan="7" align="right">Total</th>
		<th align="right">{total}</th>
	</tr>
	<tr>
		<th colspan="7" align="right">Facturas</th>
		<th align="right">{facs}</th>
	</tr>
</table>
<p>
	<input name="regresar" type="button" id="regresar" value="Regresar" />
</p>
<!-- END BLOCK : pagado -->
