<form method="post" name="Datos" class="FormValidator FormStyles" id="Datos">
	<table class="tabla_captura">
		<tr>
			<th colspan="4" align="left" scope="row"><img src="imagenes/info.png" width="16" height="16" /> Informaci&oacute;n general</th>
		</tr>
		<tr class="linea_off">
			<th align="left" scope="row">N&uacute;mero</th>
			<td colspan="3"><input name="num_proveedor" type="text" class="center" id="num_proveedor" value="{num_proveedor}" size="5" readonly="true" />
			-
			<input name="clave_seguridad" type="text" class="valid Focus toPosInt center" class="center" id="clave_seguridad" value="{clave_seguridad}" size="5" /></td>
		</tr>
		<tr class="linea_on">
			<th align="left" scope="row">Nombre</th>
			<td colspan="3"><input name="nombre" type="text" class="valid toText toUpper cleanText" id="nombre" value="{nombre}" size="50" maxlength="100" /></td>
		</tr>
		<tr class="linea_off">
			<th align="left" scope="row">R.F.C.</th>
			<td colspan="3"><input name="rfc" type="text" class="valid Focus toRFC toUpper cleanText" id="rfc" value="{rfc}" size="13" maxlength="13" /></td>
		</tr>
		<tr class="linea_off">
          <th align="left" scope="row">C.U.R.P.</th>
          <td colspan="3"><input name="curp" type="text" class="valid Focus toCURP toUpper cleanText" id="curp" value="{curp}" size="18" maxlength="18" /></td>
        </tr>
		<tr class="linea_on">
			<th align="left" scope="row">Tipo de persona </th>
			<td colspan="3"><input name="tipopersona" id="tipopersona_t" type="radio" value="TRUE"{tipopersona_t} />
				F&iacute;sica
				<input name="tipopersona" id="tipopersona_f" type="radio" value="FALSE"{tipopersona_f} />
				Moral</td>
		</tr>
		<tr class="linea_off">
			<th align="left" scope="row">Tipo de proveedor </th>
			<td colspan="3"><select name="idtipoproveedor" id="idtipoproveedor">
					<!-- START BLOCK : tipo_proveedor -->
					<option value="{id}"{selected}>{tipo}</option>
					<!-- END BLOCK : tipo_proveedor -->
				</select></td>
		</tr>
		<tr class="linea_on">
			<th align="left" scope="row">Calle</th>
			<td colspan="3"><input name="calle" type="text" class="valid toText toUpper cleanText" id="calle" value="{calle}" size="50" maxlength="200" />
				No. Ext.:
				<input name="no_exterior" type="text" class="valid toText toUpper cleanText" id="no_exterior" value="{no_exterior}" size="5" maxlength="20" />
				No. Int.:
				<input name="no_interior" type="text" class="valid toText toUpper cleanText" id="no_interior" value="{no_interior}" size="5" maxlength="20" /></td>
		</tr>
		<tr class="linea_off">
			<th align="left" scope="row">Colonia</th>
			<td colspan="3"><input name="colonia" type="text" class="valid toText toUpper cleanText" id="colonia" value="{colonia}" size="50" maxlength="200" /></td>
		</tr>
		<tr class="linea_on">
			<th align="left" scope="row">Localidad</th>
			<td colspan="3"><input name="localidad" type="text" class="valid toText toUpper cleanText" id="localidad" value="{localidad}" size="50" maxlength="200" /></td>
		</tr>
		<tr class="linea_off">
			<th align="left" scope="row">Referencia</th>
			<td colspan="3"><input name="referencia" type="text" class="valid toText toUpper cleanText" id="referencia" value="{referencia}" size="50" maxlength="200" /></td>
		</tr>
		<tr class="linea_on">
			<th align="left" scope="row">Delegaci&oacute;n/Municipio</th>
			<td colspan="3"><input name="municipio" type="text" class="valid toText toUpper cleanText" id="municipio" value="{municipio}" size="50" maxlength="200" /></td>
		</tr>
		<tr class="linea_off">
			<th align="left" scope="row">Estado</th>
			<td colspan="3"><input name="estado" type="text" class="valid toText toUpper cleanText" id="estado" value="{estado}" size="50" maxlength="200" /></td>
		</tr>
		<tr class="linea_on">
			<th align="left" scope="row">Pa&iacute;s</th>
			<td colspan="3"><select name="pais" id="pais">
					<!-- START BLOCK : pais -->
					<option value="{pais}"{selected}>{pais}</option>
					<!-- END BLOCK : pais -->
				</select></td>
		</tr>
		<tr class="linea_off">
			<th align="left" scope="row">C&oacute;digo postal </th>
			<td colspan="3"><input name="codigo_postal" type="text" class="valid onlyNumbers cleanText" id="codigo_postal" value="{codigo_postal}" size="5" maxlength="20" /></td>
		</tr>
		<tr class="linea_on">
			<th align="left" scope="row">Contacto</th>
			<td colspan="3"><input name="contacto" type="text" id="contacto" value="{contacto}" size="50" maxlength="100" /></td>
		</tr>
		<tr class="linea_off">
			<th align="left" scope="row">Tel&eacute;fono 1</th>
			<td colspan="3"><input name="telefono1" type="text" class="valid Focus toPhoneNumber" id="telefono1" value="{telefono1}" size="20" maxlength="20" /></td>
		</tr>
		<tr class="linea_on">
			<th align="left" scope="row">Tel&eacute;fono 2</th>
			<td colspan="3"><input name="telefono2" type="text" class="valid Focus toPhoneNumber" id="telefono2" value="{telefono2}" size="20" maxlength="20" /></td>
		</tr>
		<tr class="linea_off">
			<th align="left" scope="row">Fax</th>
			<td colspan="3"><input name="fax" type="text" class="valid Focus toPhoneNumber" id="fax" value="{fax}" size="20" maxlength="20" /></td>
		</tr>
		<tr class="linea_on">
			<th align="left" scope="row">Correo electr&oacute;nico 1</th>
			<td colspan="3"><input name="email1" type="text" class="valid Focus toEmail" id="email1" value="{email1}" size="50" maxlength="50" /></td>
		</tr>
		<tr class="linea_off">
			<th align="left" scope="row">Correo electr&oacute;nico 2</th>
			<td colspan="3"><input name="email2" type="text" class="valid Focus toEmail" id="email2" value="{email2}" size="50" maxlength="50" /></td>
		</tr>
		<tr class="linea_on">
			<th align="left" scope="row">Correo electr&oacute;nico 3</th>
			<td colspan="3"><input name="email3" type="text" class="valid Focus toEmail" id="email3" value="{email3}" size="50" maxlength="50" /></td>
		</tr>
		<tr class="linea_off">
			<th align="left" scope="row">Observaciones</th>
			<td colspan="3"><textarea name="observaciones" cols="50" rows="5" class="valid toText toUpper cleanText" id="observaciones">{observaciones}</textarea></td>
		</tr>
		<tr class="linea_on">
			<td colspan="4" align="left" scope="row">&nbsp;</td>
		</tr>
		<tr>
			<th colspan="4" align="left" scope="row"><img src="imagenes/info.png" width="16" height="16" /> Informaci&oacute;n de pago </th>
		</tr>
		<tr class="linea_off">
			<th align="left" scope="row">Tipo de documentaci&oacute;n </th>
			<td colspan="3"><input name="tipo_doc" type="radio" value="2"{tipo_doc_2} />
				Factura
				<input name="tipo_doc" type="radio" value="1"{tipo_doc_1} />
				Remisi&oacute;n</td>
		</tr>
		<tr class="linea_on">
			<th align="left" scope="row">Validar facturas </th>
			<td colspan="3"><input name="verfac" type="checkbox" id="verfac" value="TRUE"{verfac} />
				Si</td>
		</tr>
		<tr class="linea_off">
			<th align="left" scope="row">Restar a compras </th>
			<td colspan="3"><input name="restacompras" type="checkbox" id="restacompras" value="TRUE"{restacompras} />
				Si</td>
		</tr>
		<tr class="linea_on">
			<th align="left" scope="row">Prioridad</th>
			<td colspan="3"><input name="prioridad" type="radio" value="FALSE"{prioridad_f} />
				Baja
				<input name="prioridad" type="radio" value="TRUE"{prioridad_t} />
				Alta</td>
		</tr>
		<tr class="linea_off">
			<th align="left" scope="row">Forma de pago </th>
			<td colspan="3"><input name="idtipopago" type="radio" value="1"{idtipopago_1} />
				Cr&eacute;dito
				<input name="idtipopago" type="radio" value="2"{idtipopago_2} />
				Contado</td>
		</tr>
		<tr class="linea_on">
			<th align="left" scope="row">D&iacute;as de credito </th>
			<td colspan="3"><input name="diascredito" type="text" class="valid Focus toPosInt center" id="diascredito" value="{diascredito}" size="3" /></td>
		</tr>
		<tr class="linea_off">
			<th align="left" scope="row">Facturas por mes</th>
			<td colspan="3"><input name="facturas_por_mes" type="text" class="valid Focus toPosInt center" id="facturas_por_mes" value="{facturas_por_mes}" size="3" /></td>
		</tr>
		<tr class="linea_on">
			<th align="left" scope="row">Facturas por pago</th>
			<td colspan="3"><input name="facturas_por_pago" type="text" class="valid Focus toPosInt center" id="facturas_por_pago" value="{facturas_por_pago}" size="3" /></td>
		</tr>
		<tr class="linea_off">
			<th align="left" scope="row">Tipo de pago </th>
			<td colspan="3"><input name="trans" type="radio" value="FALSE"{trans_f} />
				Cheque
				<input name="trans" type="radio" value="TRUE"{trans_t} />
				Transferencia</td>
		</tr>
		<tr class="linea_on">
			<th align="left" scope="row">Para abono a cuenta </th>
			<td colspan="3"><input name="para_abono" type="checkbox" id="para_abono" value="1"{para_abono} />
				Si</td>
		</tr>
		<tr class="linea_off">
			<th align="left" scope="row">Banco </th>
			<td colspan="3"><select name="idbanco" id="idbanco">
					<option value=""></option>
					<!-- START BLOCK : banco -->
					<option value="{id}"{selected}>{nombre}</option>
					<!-- END BLOCK : banco -->
				</select></td>
		</tr>
		<tr class="linea_on">
			<th align="left" scope="row">Sucursal</th>
			<td colspan="3"><input name="sucursal" type="text" class="valid Focus onlyNumbers" id="sucursal" value="{sucursal}" size="4" maxlength="4" /></td>
		</tr>
		<tr class="linea_off">
			<th align="left" scope="row">Entidad</th>
			<td colspan="3"><select name="IdEntidad" id="IdEntidad">
					<option value=""></option>
					<!-- START BLOCK : entidad -->
					<option value="{id}"{selected}>{entidad}</option>
					<!-- END BLOCK : entidad -->
				</select></td>
		</tr>
		<tr class="linea_on">
			<th align="left" scope="row">Plaza Banxico </th>
			<td colspan="3"><input name="plaza_banxico" type="text" class="valid Focus onlyNumbers" id="plaza_banxico" value="{plaza_banxico}" size="5" maxlength="5" /></td>
		</tr>
		<tr class="linea_off">
			<th align="left" scope="row">Referencia bancaria para pago</th>
			<td colspan="3"><input name="referencia_bancaria" type="text" class="valid Focus toText" id="referencia_bancaria" value="{referencia_bancaria}" size="30" maxlength="10" /></td>
		</tr>
		<tr class="linea_on">
			<th align="left" scope="row">Cuenta (11 d&iacute;gitos) </th>
			<td colspan="3"><input name="cuenta" type="text" class="valid Focus onlyNumbers textClean" id="cuenta" value="{cuenta}" size="11" maxlength="11" /></td>
		</tr>
		<tr class="linea_off">
			<th align="left" scope="row">CLABE (18 d&iacute;gitos) </th>
			<td colspan="3"><input name="clabe" type="text" class="valid Focus onlyNumbers textClean" id="clabe" value="{clabe}" size="18" maxlength="18" /></td>
		</tr>
		<tr class="linea_on">
			<td colspan="4" align="left" scope="row">&nbsp;</td>
		</tr>
		<tr>
			<th colspan="4" align="left" scope="row"><img src="imagenes/info.png" width="16" height="16" /> Informaci&oacute;n para portal de pagos </th>
		</tr>
		<tr class="linea_off">
			<th align="left" scope="row">Contrase&ntilde;a</th>
			<td colspan="3"><input name="pass_site" type="text" class="valid Focus onlyNumbersAndLetters cleanText" id="pass_site" value="{pass_site}" size="10" maxlength="10" />
				<img src="imagenes/refresh.png" name="pass_reload" width="16" height="16" id="pass_reload" title="Generar contrase&ntilde;a aleatoria" /></td>
		</tr>
		<!-- START BLOCK : extra -->
		<tr class="linea_on">
			<td colspan="4" align="left" scope="row">&nbsp;</td>
		</tr>
		<tr>
			<th colspan="4" align="left" scope="row"><img src="imagenes/info.png" width="16" height="16" /> Informaci&oacute;n de contacto </th>
		</tr>
		<tr class="linea_off">
			<th align="left" scope="row">Contacto</th>
			<td colspan="3"><input name="contacto1" type="text" class="valid toText toUpper cleanText" id="contacto1" value="{contacto1}" size="50" maxlength="255" /></td>
		</tr>
		<tr class="linea_on">
			<th align="left" scope="row">Contacto</th>
			<td colspan="3"><input name="contacto2" type="text" class="valid toText toUpper cleanText" id="contacto2" value="{contacto2}" size="50" maxlength="255" /></td>
		</tr>
		<tr class="linea_off">
			<th align="left" scope="row">Contacto</th>
			<td colspan="3"><input name="contacto3" type="text" class="valid toText toUpper cleanText" id="contacto3" value="{contacto3}" size="50" maxlength="255" /></td>
		</tr>
		<tr class="linea_on">
			<th align="left" scope="row">Contacto</th>
			<td colspan="3"><input name="contacto4" type="text" class="valid toText toUpper cleanText" id="contacto4" value="{contacto4}" size="50" maxlength="255" /></td>
		</tr>
		<tr>
			<td colspan="4" align="left" scope="row">&nbsp;</td>
		</tr>
		<tr>
			<th colspan="4" align="left" scope="row"><img src="imagenes/info.png" width="16" height="16" /> Informaci&oacute;n de descuentos </th>
		</tr>
		<tr class="linea_off">
			<th align="left" scope="row">Descuento</th>
			<td><input name="desc1" type="text" class="valid Focus numberPosFormat right" id="desc1" value="{desc1}" size="5" precision="2" />
				%</td>
			<th align="left">Concepto</th>
			<td><input name="cod_desc1" type="text" class="valid Focus toPosInt center" id="cod_desc1" value="{cod_desc1}" size="3" />
				<input name="con_desc1" type="text" id="con_desc1" value="{con_desc1}" size="30" readonly="true" />
				<input name="tipo_desc1" type="text" disabled="disabled" id="tipo_desc1" value="{tipo_desc1}" size="10" /></td>
		</tr>
		<tr class="linea_on">
			<th align="left" scope="row">Descuento</th>
			<td><input name="desc2" type="text" class="valid Focus numberPosFormat right" id="desc2" value="{desc2}" size="5" precision="2" />
				%</td>
			<th align="left">Concepto</th>
			<td><input name="cod_desc2" type="text" class="valid Focus toPosInt center" id="cod_desc2" value="{cod_desc2}" size="3" />
				<input name="con_desc2" type="text" id="con_desc2" value="{con_desc2}" size="30" readonly="true" />
				<input name="tipo_desc2" type="text" disabled="disabled" id="tipo_desc2" value="{tipo_desc2}" size="10" /></td>
		</tr>
		<tr class="linea_off">
			<th align="left" scope="row">Descuento</th>
			<td><input name="desc3" type="text" class="valid Focus numberPosFormat right" id="desc3" value="{desc3}" size="5" precision="2" />
				%</td>
			<th align="left">Concepto</th>
			<td><input name="cod_desc3" type="text" class="valid Focus toPosInt center" id="cod_desc3" value="{cod_desc3}" size="3" />
				<input name="con_desc3" type="text" id="con_desc3" value="{cod_desc3}" size="30" readonly="true" />
				<input name="tipo_desc3" type="text" disabled="disabled" id="tipo_desc3" value="{tipo_desc3}" size="10" /></td>
		</tr>
		<tr class="linea_on">
			<th align="left" scope="row">Descuento</th>
			<td><input name="desc4" type="text" class="valid Focus numberPosFormat right" id="desc4" value="{desc4}" size="5" precision="2" />
				%</td>
			<th align="left">Concepto</th>
			<td><input name="cod_desc4" type="text" class="valid Focus toPosInt center" id="cod_desc4" value="{cod_desc4}" size="3" />
				<input name="con_desc4" type="text" id="con_desc4" value="{cod_desc4}" size="30" readonly="true" />
				<input name="tipo_desc4" type="text" disabled="disabled" id="tipo_desc4" value="{tipo_desc4}" size="10" /></td>
		</tr>
		<!-- END BLOCK : extra -->
	</table>
	<br />
	<p>
		<input name="regresar" type="button" id="regresar" value="Regresar">
		&nbsp;&nbsp;
		<input name="actualizar" type="button" class="boton" id="actualizar" value="Actualizar" />
	</p>
</form>
