<form action="" method="get" name="Datos" class="FormValidator FormStyles" id="Datos">
	<table class="tabla_captura">
		<tr class="linea_off">
			<th align="left" scope="row">Compa&ntilde;&iacute;a</th>
			<td><input name="num_cia" type="text" class="valid Focus toPosInt center" id="num_cia" size="3" />
				<input name="nombre_cia" type="text" disabled="disabled" id="nombre_cia" size="60" /></td>
		</tr>
		<tr class="linea_on">
			<th align="left" scope="row">Alias</th>
			<td><input name="alias_arrendamiento" type="text" class="valid toText cleanText toUpper" id="alias_arrendamiento" size="65" maxlength="200" /></td>
		</tr>
		<tr>
			<td colspan="2" align="left" scope="row">&nbsp;</td>
		</tr>
		<tr>
			<th colspan="2" align="left" scope="row"><img src="/lecaroz/iconos/info.png" width="16" height="16" /> Informaci&oacute;n de arrendamiento</th>
		</tr>
		<tr class="linea_off">
			<th align="left" scope="row">Arrendador</th>
			<td><input name="nombre_arrendador" type="text" class="valid toText cleanText toUpper" id="nombre_arrendador" size="65" maxlength="200" /></td>
		</tr>
		<tr class="linea_on">
			<th align="left" scope="row">R.F.C.</th>
			<td><input name="rfc" type="text" class="valid toRFC toUpper" id="rfc" size="13" maxlength="13" /></td>
		</tr>
		<tr class="linea_off">
			<th align="left" scope="row">C.U.R.P.</th>
			<td><input name="curp" type="text" id="curp" style="valid toCurp toUpper" size="18" maxlength="18" /></td>
		</tr>
		<tr class="linea_on">
			<th align="left" scope="row">Tipo de persona</th>
			<td><input type="radio" name="tipo_persona" value="FALSE" />
				F&iacute;sica
				<input name="tipo_persona" type="radio" value="TRUE" checked="checked" />
				Moral</td>
		</tr>
		<tr>
			<th colspan="2" align="left" scope="row"><img src="/lecaroz/iconos/info.png" width="16" height="16" /> Domicilio fiscal</th>
		</tr>
		<tr class="linea_off">
			<th align="left" scope="row">Calle</th>
			<td><input name="calle" type="text" class="valid toText toUpper cleanText" id="calle" size="40" maxlength="100" />
				No. Ext.:
				<input name="no_exterior" type="text" class="valid toText toUpper cleanText" id="no_exterior" size="5" maxlength="50" />
				No. Int.:
				<input name="no_interior" type="text" class="valid toText toUpper cleanText" id="no_interior" size="5" maxlength="50" /></td>
		</tr>
		<tr class="linea_on">
			<th align="left" scope="row">Colonia</th>
			<td><input name="colonia" type="text" class="valid toText toUpper cleanText" id="colonia" size="40" maxlength="100" /></td>
		</tr>
		<tr class="linea_off">
			<th align="left" scope="row">Delegaci&oacute;n/Municipio</th>
			<td><input name="municipio" type="text" class="valid toText toUpper cleanText" id="municipio" size="40" maxlength="100" /></td>
		</tr>
		<tr class="linea_on">
			<th align="left" scope="row">Estado</th>
			<td><input name="estado" type="text" class="valid toText toUpper cleanText" id="estado" size="40" maxlength="100" /></td>
		</tr>
		<tr class="linea_off">
			<th align="left" scope="row">Pais</th>
			<td><input name="pais" type="text" class="valid toText toUpper cleanText" id="pais" size="40" maxlength="100" /></td>
		</tr>
		<tr class="linea_on">
			<th align="left" scope="row">C&oacute;digo postal</th>
			<td><input name="codigo_postal" type="text" class="valid onlyNumbers" id="codigo_postal" size="5" maxlength="20" /></td>
		</tr>
		<tr>
			<td colspan="2" align="left" scope="row">&nbsp;</td>
		</tr>
		<tr>
			<th colspan="2" align="left" scope="row"><img src="/lecaroz/iconos/info.png" width="16" height="16" /> Informaci&oacute;n de contacto</th>
		</tr>
		<tr class="linea_off">
			<th align="left" scope="row">Contacto</th>
			<td><input name="contacto" type="text" class="valid toText cleanText toUpper" id="contacto" size="40" maxlength="100" /></td>
		</tr>
		<tr class="linea_on">
			<th align="left" scope="row">Tel&eacute;fono 1</th>
			<td><input name="telefono1" type="text" class="valid Focus toPhoneNumber" id="telefono1" size="20" maxlength="20" /></td>
		</tr>
		<tr class="linea_off">
			<th align="left" scope="row">Tel&eacute;fono 2</th>
			<td><input name="telefono2" type="text" class="valid Focus toPhoneNumber" id="telefono2" size="20" maxlength="20" /></td>
		</tr>
		<tr class="linea_on">
			<th align="left" scope="row">Correo electr&oacute;nico</th>
			<td><input name="email" type="text" class="valid Focus toEmail" id="email" size="40" maxlength="100" /></td>
		</tr>
		<tr>
			<td colspan="2" align="left" scope="row">&nbsp;</td>
		</tr>
		<tr>
			<th colspan="2" align="left" scope="row"><img src="/lecaroz/iconos/info.png" width="16" height="16" /> Informaci&oacute;n de contrato</th>
		</tr>
		<tr class="linea_off">
			<th align="left" scope="row">Fecha de inicio</th>
			<td><input name="fecha_inicio" type="text" class="valid toDate center" id="fecha_inicio" size="10" maxlength="10" /></td>
		</tr>
		<tr class="linea_on">
			<th align="left" scope="row">Fecha de t&eacute;rmino</th>
			<td><input name="fecha_termino" type="text" class="valid toDate center" id="fecha_termino" size="10" maxlength="10" /></td>
		</tr>
		<tr class="linea_off">
			<th align="left" scope="row">Método de pago</th>
			<td><input name="metodo_pago" type="radio" value="1" checked="checked" />
				Crédito
				<input type="radio" name="metodo_pago" value="2" />
				Contado</td>
		</tr>
		<tr>
			<td colspan="2" align="left" scope="row">&nbsp;</td>
		</tr>
		<tr>
			<th colspan="2" align="left" scope="row"><img src="/lecaroz/iconos/info.png" width="16" height="16" /> Informaci&oacute;n  de renta</th>
		</tr>
		<tr class="linea_off">
			<th align="left" scope="row">Renta</th>
			<td>$
				<input name="renta" type="text" class="valid Focus numberPosFormat right blue" id="renta" style="width:150px;" size="14" precision="2" /></td>
		</tr>
		<tr class="linea_on">
			<th align="left" scope="row">Mantenimiento</th>
			<td>$
				<input name="mantenimiento" type="text" class="valid Focus numberPosFormat right blue" id="mantenimiento" style="width:150px;" size="14" precision="2" /></td>
		</tr>
		<tr class="linea_off">
			<th align="left" scope="row">Subtotal</th>
			<td>$
				<input name="subtotal" type="text" class="right bold blue" id="subtotal" style="width:150px;" size="14" readonly="readonly" /></td>
		</tr>
		<tr class="linea_on">
			<th align="left" scope="row"><input name="aplicar_iva" type="checkbox" id="aplicar_iva" value="1" />
			I.V.A.</th>
			<td>$
				<input name="iva" type="text" class="right bold blue" id="iva" style="width:150px;" size="14" readonly="readonly" /></td>
		</tr>
		<tr class="linea_off">
			<th align="left" scope="row">Agua</th>
			<td>$
				<input name="agua" type="text" class="valid Focus numberPosFormat right blue" precision="2" id="agua" style="width:150px;" size="14" /></td>
		</tr>
		<tr class="linea_on">
			<th align="left" scope="row"><input name="aplicar_retencion_iva" type="checkbox" id="aplicar_retencion_iva" value="1" />
				Retenci&oacute;n de I.V.A.</th>
			<td>$
				<input name="retencion_iva" type="text" class="right bold red" id="retencion_iva" style="width:150px;" size="14" readonly="readonly" /></td>
		</tr>
		<tr class="linea_off">
			<th align="left" scope="row"><input name="aplicar_retencion_isr" type="checkbox" id="aplicar_retencion_isr" value="1" />
				Retenci&oacute;n de I.S.R.</th>
			<td>$
				<input name="retencion_isr" type="text" class="right bold red" id="retencion_isr" style="width:150px;" size="14" readonly="readonly" /></td>
		</tr>
		<tr class="linea_on">
			<th align="left" scope="row">Total</th>
			<td>$
				<input name="total" type="text" class="right bold blue font14" id="total" style="width:150px;" size="14" readonly="readonly" /></td>
		</tr>
		<tr class="linea_off">
			<th align="left" scope="row">Renta en efectivo</th>
			<td>$
			<input name="renta_efectivo" type="text" class="valid Focus numberPosFormat right blue" id="renta_efectivo" style="width:150px;" size="14" precision="2" /></td>
		</tr>
		<tr class="linea_on">
			<th align="left" scope="row">Gran total</th>
			<td>$
			<input name="gran_total" type="text" class="right bold blue font14" id="gran_total" style="width:150px;" size="14" readonly="readonly" /></td>
		</tr>
		<tr class="linea_off">
			<td colspan="2" scope="row">&nbsp;</td>
		</tr>
		<tr class="linea_off">
			<th align="left" scope="row">Observaciones</th>
			<td><textarea name="observaciones" cols="45" rows="5" class="valid toText toUpper cleanText" id="observaciones"></textarea></td>
		</tr>
	</table>
	<p>
		<input type="button" name="regresar" id="regresar" value="Regresar" />
		&nbsp;&nbsp;
		<input type="button" name="alta" id="alta" value="Alta" />
	</p>
</form>
