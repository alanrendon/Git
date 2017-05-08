<form name="alta_cia" class="FormValidator" id="alta_cia">
	<table class="table">
		<thead>
			<tr>
				<th colspan="2">&nbsp;</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td class="bold">Compa&ntilde;&iacute;a</td>
				<td>
					<input name="num_cia" type="text" class="validate focus toPosInt" id="num_cia" size="3" value="{num_cia}" />
				</td>
			</tr>
			<tr>
				<td class="bold">Nombre</td>
				<td>
					<input name="nombre" type="text" class="validate toText cleanText toUpper" id="nombre" size="40" maxlength="100" value="{nombre}" />
				</td>
			</tr>
			<tr>
				<td class="bold">Alias</td>
				<td>
					<input name="nombre_corto" type="text" class="validate toText cleanText toUpper" id="nombre_corto" size="40" maxlength="25" value="{nombre_corto}" />
				</td>
			</tr>
			<tr>
				<td class="bold">Tipo</td>
				<td>
					<select name="tipo_cia" id="tipo_cia">
						<!-- START BLOCK : tipo_cia -->
						<option value="{value}"{selected}>{text}</option>
						<!-- END BLOCK : tipo_cia -->
					</select>
				</td>
			</tr>
			<tr>
				<td class="bold">Representada por</td>
				<td>
					<input name="num_proveedor" type="text" class="validate focus toPosInt right" id="num_proveedor" size="3" value="{num_proveedor}" />
					<input name="nombre_proveedor" type="text" id="nombre_proveedor" size="40" value="{nombre_proveedor}" disabled="disabled" />
				</td>
			</tr>
			<tr>
				<td colspan="2">&nbsp;</td>
			</tr>
			<tr>
				<th colspan="2" class="left">
					<img src="/lecaroz/iconos/info.png" width="16" height="16" />
					Homoclaves
				</th>
			</tr>
			<tr>
				<td class="bold">Primaria</td>
				<td>
					<input name="num_cia_primaria" type="text" class="validate focus toPosInt right" id="num_cia_primaria" size="3" value="{num_cia_primaria}" />
					<input name="nombre_cia_primaria" type="text" id="nombre_cia_primaria" size="40" value="{nombre_cia_primaria}" disabled="disabled" />
				</td>
			</tr>
			<tr>
				<td class="bold">Saldos</td>
				<td>
					<input name="num_cia_saldos" type="text" class="validate focus toPosInt right" id="num_cia_saldos" size="3" value="{num_cia_saldos}" />
					<input name="nombre_cia_saldos" type="text" id="nombre_cia_saldos" size="40" value="{nombre_cia_saldos}" disabled="disabled" />
				</td>
			</tr>
			<tr>
				<td class="bold">Aguinaldos</td>
				<td>
					<input name="cia_aguinaldos" type="text" class="validate focus toPosInt right" id="cia_aguinaldos" size="3" value="{cia_aguinaldos}" />
					<input name="nombre_cia_aguinaldos" type="text" id="nombre_cia_aguinaldos" size="40" value="{nombre_cia_aguinaldos}" disabled="disabled" />
				</td>
			</tr>
			<tr>
				<td class="bold">Esta rosticer&iacute;a (en caso de serlo)<br />pertence a:</td>
				<td>
					<input name="num_cia_ros" type="text" class="validate focus toPosInt right" id="num_cia_ros" size="3" value="{num_cia_ros}" />
					<input name="nombre_cia_ros" type="text" id="nombre_cia_ros" size="40" value="{nombre_cia_ros}" disabled="disabled" />
				</td>
			</tr>
			<tr>
				<td class="bold">Matriz fiscal</td>
				<td>
					<input name="cia_fiscal_matriz" type="text" class="validate focus toPosInt right" id="cia_fiscal_matriz" size="3" value="{cia_fiscal_matriz}" />
					<input name="nombre_cia_fiscal_matriz" type="text" id="nombre_cia_fiscal_matriz" size="40" value="{nombre_cia_fiscal_matriz}" disabled="disabled" />
				</td>
			</tr>
			<tr>
				<td colspan="2">&nbsp;</td>
			</tr>
			<tr>
				<th colspan="2" class="left">
					<img src="/lecaroz/iconos/info.png" width="16" height="16" />
					Informaci&oacute;n fiscal
				</th>
			</tr>
			<tr>
				<td class="bold">Razon social</td>
				<td>
					<input name="razon_social" type="text" class="validate toText cleanText toUpper" id="razon_social" size="40" maxlength="200" value="{razon_social}" />
				</td>
			</tr>
			<tr>
				<td class="bold">R.F.C.</td>
				<td>
					<input name="rfc" type="text" class="validate toRFC toUpper" id="rfc" size="13" maxlength="20" value="{rfc}" />
				</td>
			</tr>
			<tr>
				<td class="bold">Tipo de persona</td>
				<td>
					<input name="persona_fis_moral" type="radio" id="persona_fis_moral_f" value="FALSE" checked="checked" />
					Moral
					<input name="persona_fis_moral" type="radio" id="persona_fis_moral_t" value="TRUE" />
					F&iacute;sica
				</td>
			</tr>
			<tr>
				<td class="bold">R&eacute;gimen fiscal</td>
				<td>
					<input name="regimen_fiscal" type="text" class="validate toText cleanText toUpper" id="regimen_fiscal" size="40" maxlength="200" value="{regimen_fiscal}" />
				</td>
			</tr>
			<tr>
				<td class="bold">Aplicar I.V.A.</td>
				<td>
					<input name="aplica_iva" type="radio" id="aplica_iva_f" value="FALSE" checked="checked" />
					No
					<input name="aplica_iva" type="radio" id="aplica_iva_t" value="TRUE" />
					Si
				</td>
			</tr>
			<tr>
				<td class="bold">Logotipo para comprobantes</td>
				<td>
					<select name="logo_cfd" id="logo_cfd" class="logo_cfd_select">
						<!-- START BLOCK : logo -->
						<option value="{value}" class="logo_cfd_option" style="background-image: url(imagenes/logos_cfds/{imagen});">{text}</option>
						<!-- END BLOCK : logo -->
					</select>
				</td>
			</tr>
			<tr>
				<td colspan="2">&nbsp;</td>
			</tr>
			<tr>
				<th colspan="2" class="left">
					<img src="/lecaroz/iconos/info.png" width="16" height="16" />
					Domicilio fiscal
				</th>
			</tr>
			<tr>
				<td class="bold">Calle</td>
				<td>
					<input name="calle" type="text" class="validate toText toUpper cleanText" id="calle" size="35" maxlength="100" value="{calle}" />
					No. Ext.:
					<input name="no_exterior" type="text" class="validate toText toUpper cleanText" id="no_exterior" size="5" maxlength="20" value="{no_exterior}" />
					No. Int.:
					<input name="no_interior" type="text" class="validate toText toUpper cleanText" id="no_interior" size="5" maxlength="20" value="{no_interior}" />
				</td>
			</tr>
			<tr>
				<td class="bold">Colonia</td>
				<td>
					<input name="colonia" type="text" class="validate toText toUpper cleanText" id="colonia" size="40" maxlength="100" value="{colonia}" />
				</td>
			</tr>
			<tr>
				<td class="bold">Localidad</td>
				<td>
					<input name="localidad" type="text" class="validate toText toUpper cleanText" id="localidad" size="40" maxlength="100" value="{localidad}" />
				</td>
			</tr>
			<tr>
				<td class="bold">Referencia</td>
				<td>
					<input name="referencia" type="text" class="validate toText toUpper cleanText" id="referencia" size="40" maxlength="100" value="{referencia}" />
				</td>
			</tr>
			<tr>
				<td class="bold">Delegaci&oacute;n/Municipio</td>
				<td>
					<input name="municipio" type="text" class="validate toText toUpper cleanText" id="municipio" size="40" maxlength="100" value="{municipio}" />
				</td>
			</tr>
			<tr>
				<td class="bold">Estado</td>
				<td>
					<select name="estado" id="estado">
						<!-- START BLOCK : estado -->
						<option value="{estado}"{selected}>{estado}</option>
						<!-- END BLOCK : estado -->
					</select>
				</td>
			</tr>
			<tr>
				<td class="bold">Pa&iacute;s</td>
				<td>
					<select name="pais" id="pais">
						<!-- START BLOCK : pais -->
						<option value="{pais}"{selected}>{pais}</option>
						<!-- END BLOCK : pais -->
					</select>
				</td>
			</tr>
			<tr>
				<td class="bold">C&oacute;digo postal </td>
				<td>
					<input name="codigo_postal" type="text" class="validate onlyNumbers" id="codigo_postal" size="5" maxlength="20" value="{codigo_postal}" />
				</td>
			</tr>
			<tr>
				<td colspan="2">&nbsp;</td>
			</tr>
			<tr>
				<th colspan="2" class="left">
					<img src="/lecaroz/iconos/info.png" width="16" height="16" />
					Informaci&oacute;n general
				</th>
			</tr>
			<tr>
				<td class="bold">Tel&eacute;fono</td>
				<td>
					<input name="telefono" type="text" class="validate focus toPhoneNumber" id="telefono" size="40" maxlength="15" value="{telefono}" />
				</td>
			</tr>
			<tr>
				<td class="bold">Correo electr&oacute;nico</td>
				<td>
					<input name="email" type="text" class="validate focus toEmail" id="email" size="40" maxlength="50" value="{email}" />
				</td>
			</tr>
			<tr>
				<td class="bold">Clave I.M.S.S.</td>
				<td>
					<input name="no_imss" type="text" class="validate focus onlyNumbersAndLetters" id="no_imss" size="25" maxlength="25" value="{no_imss}" />
				</td>
			</tr>
			<tr>
				<td class="bold">Clave Infonavit</td>
				<td>
					<input name="no_infonavit" type="text" class="validate focus onlyNumbers" id="no_infonavit" size="15" maxlength="15" value="{no_infonavit}" />
				</td>
			</tr>
			<tr>
				<td class="bold">Delegaci&oacute;n I.M.S.S.</td>
				<td>
					<select name="iddelimss" id="iddelimss">
						<!-- START BLOCK : iddelimss -->
						<option value="{value}"{selected}>{text}</option>
						<!-- END BLOCK : iddelimss -->
					</select>
				</td>
			</tr>
			<tr>
				<td class="bold">Sub-delegaci&oacute;n I.M.S.S.</td>
				<td>
					<select name="idsubdelimss" id="idsubdelimss">
						<!-- START BLOCK : idsubdelimss -->
						<option value="{value}"{selected}>{text}</option>
						<!-- END BLOCK : idsubdelimss -->
					</select>
				</td>
			</tr>
			<tr>
				<td class="bold">N&uacute;mero de cortes al d&iacute;a</td>
				<td>
					<select name="cortes_caja" id="cortes_caja">
						<!-- START BLOCK : cortes -->
						<option value="{cortes}"{selected}>{cortes}</option>
						<!-- END BLOCK : cortes -->
					</select>
				</td>
			</tr>
			<tr>
				<td class="bold">¿Cuenta con medidor de agua?</td>
				<td>
					<input name="med_agua" type="radio" id="med_agua_t" value="TRUE" checked="checked" />
					Si
					<input name="med_agua" type="radio" id="med_agua_f" value="FALSE" />
					No
				</td>
			</tr>
			<tr>
				<td class="bold">No. de cliente de gasolina</td>
				<td>
					<input name="cod_gasolina" type="text" class="validate focus toPosInt" id="cod_gasolina" size="20" value="{cod_gasolina}" />
				</td>
			</tr>
			<tr>
				<td class="bold">Num. de servicio de luz</td>
				<td>
					<input name="no_cta_cia_luz" type="text" class="validate focus onlyNumbers" id="no_cta_cia_luz" size="12" maxlength="25" value="{no_cta_cia_luz}" />
					<span class="font8">
						(<input name="luz_esp" type="checkbox" id="luz_esp" value="1"{luz_esp} /> Especial)
					</span>
				</td>
			</tr>
			<tr>
				<td class="bold">Periodo de pago de luz</td>
				<td>
					<input name="periodo_pago_luz" type="radio" id="periodo_pago_luz_2" value="2" checked="checked" />
					Bimestral
					<input name="periodo_pago_luz" type="radio" id="periodo_pago_luz_1" value="1" />
					Mensual
				</td>
			</tr>
			<tr>
				<td class="bold">Mes/Bimestre de vencimiento de luz</td>
				<td>
					<input name="bim_par_imp_luz" type="radio" id="bim_par_imp_luz_1" value="1" checked="checked" />
					Impar
					<input name="bim_par_imp_luz" type="radio" id="bim_par_imp_luz_0" value="0" />
					Par
				</td>
			</tr>
			<tr>
				<td class="bold">D&iacute;a de vencimiento de luz</td>
				<td>
					<select name="dia_ven_luz" id="dia_ven_luz">
						<!-- START BLOCK : dia_ven_luz -->
						<option value="{dia}"{selected}>{dia}</option>
						<!-- END BLOCK : dia_ven_luz -->
					</select>
				</td>
			</tr>
			<tr>
				<td class="bold">Administrador</td>
				<td>
					<select name="idadministrador" id="idadministrador">
						<!-- START BLOCK : idadministrador -->
						<option value="{value}"{selected}>{text}</option>
						<!-- END BLOCK : idadministrador -->
					</select>
				</td>
			</tr>
			<tr>
				<td class="bold">Operador(a)</td>
				<td>
					<select name="idoperadora" id="idoperadora">
						<!-- START BLOCK : idoperadora -->
						<option value="{value}"{selected}>{text}</option>
						<!-- END BLOCK : idoperadora -->
					</select>
				</td>
			</tr>
			<tr>
				<td class="bold">Contador</td>
				<td>
					<select name="idcontador" id="idcontador">
						<!-- START BLOCK : idcontador -->
						<option value="{value}"{selected}>{text}</option>
						<!-- END BLOCK : idcontador -->
					</select>
				</td>
			</tr>
			<tr>
				<td class="bold">Auditor</td>
				<td>
					<select name="idauditor" id="idauditor">
						<!-- START BLOCK : idauditor -->
						<option value="{value}"{selected}>{text}</option>
						<!-- END BLOCK : idauditor -->
					</select>
				</td>
			</tr>
			<tr>
				<td class="bold">Aseguradora</td>
				<td>
					<select name="idaseguradora" id="idaseguradora">
						<!-- START BLOCK : idaseguradora -->
						<option value="{value}"{selected}>{text}</option>
						<!-- END BLOCK : idaseguradora -->
					</select>
				</td>
			</tr>
			<tr>
				<td class="bold">Sindicato</td>
				<td>
					<select name="idsindicato" id="idsindicato">
						<!-- START BLOCK : idsindicato -->
						<option value="{value}"{selected}>{text}</option>
						<!-- END BLOCK : idsindicato -->
					</select>
				</td>
			</tr>
			<tr>
				<td class="bold">Sub-cuenta de deudores</td>
				<td>
					<input name="sub_cuenta_deudores" type="text" class="validate focus toPosInt" id="sub_cuenta_deudores" size="13" maxlength="13" value="{sub_cuenta_deudores}" />
				</td>
			</tr>
			<tr>
				<td colspan="2">&nbsp;</td>
			</tr>
			<tr>
				<th colspan="2" class="left">
					<img src="/lecaroz/iconos/info.png" width="16" height="16" />
					Informaci&oacute;n bancaria
				</th>
			</tr>
			<tr>
				<td class="bold">
					<img src="/lecaroz/imagenes/Banorte16x16.png" width="16" height="16" />
					CLABE Banorte
				</td>
				<td>
					<input name="clabe_banco" type="text" class="validate focus onlyNumbers" id="clabe_banco" size="3" maxlength="3" value="{clabe_banco}" />
					<input name="clabe_plaza" type="text" class="validate focus onlyNumbers" id="clabe_plaza" size="3" maxlength="3" value="{clabe_plaza}" />
					<input name="clabe_cuenta" type="text" class="validate focus onlyNumbers" id="clabe_cuenta" size="11" maxlength="11" value="{clabe_cuenta}" />
					<input name="clabe_identificador" type="text" class="validate focus onlyNumbers" id="clabe_identificador" size="1" maxlength="1" value="{clabe_identificador}" />
				</td>
			</tr>
			<tr>
				<td class="bold">
					<img src="/lecaroz/imagenes/Santander16x16.png" width="16" height="16" />
					CLABE Santander
				</td>
				<td>
					<input name="clabe_banco2" type="text" class="validate focus onlyNumbers" id="clabe_banco2" size="3" maxlength="3" value="{clabe_banco2}" />
					<input name="clabe_plaza2" type="text" class="validate focus onlyNumbers" id="clabe_plaza2" size="3" maxlength="3" value="{clabe_plaza2}" />
					<input name="clabe_cuenta2" type="text" class="validate focus onlyNumbers" id="clabe_cuenta2" size="11" maxlength="11" value="{clabe_cuenta2}" />
					<input name="clabe_identificador2" type="text" class="validate focus onlyNumbers" id="clabe_identificador2" size="1" maxlength="1" value="{clabe_identificador2}" />
				</td>
			</tr>
			<tr>
				<td class="bold">Aviso de saldo bajo</td>
				<td>
					<input name="aviso_saldo" type="radio" id="aviso_saldo_t" value="TRUE" checked="checked" />
					Si
					<input name="aviso_saldo" type="radio" id="aviso_saldo_f" value="FALSE" />
					No
				</td>
			</tr>
			<tr>
				<td class="bold">Num. de cliente Cometra</td>
				<td>
					<input name="cliente_cometra" type="text" class="validate focus toPosInt" id="cliente_cometra" size="12" maxlength="16" value="{cliente_cometra}" />
				</td>
			</tr>
			<tr>
				<td class="bold">Turno Cometra</td>
				<td>
					<input name="turno_cometra" type="radio" id="turno_cometra_1" value="1" checked="checked" />
					Matutino
					<input name="turno_cometra" type="radio" id="turno_cometra_2" value="2" />
					Vespertino
				</td>
			</tr>
			<tr>
				<td class="bold">Referencia para pagos</td>
				<td>
					<input name="ref" type="text" class="validate focus onlyNumbersAndLetters toUpper" id="ref" size="20" maxlength="20" value="{ref}" />
				</td>
			</tr>
			<tr>
				<td colspan="2">&nbsp;</td>
			</tr>
			<tr>
				<th colspan="2" class="left">
					<img src="/lecaroz/iconos/info.png" width="16" height="16" />
					Informaci&oacute;n para balances
				</th>
			</tr>
			<tr>
				<td class="bold">% BG <input name="por_bg" type="text" class="validate focus numberPosFormat right" precision="2" id="por_bg" size="6" maxlength="6" value="{por_bg}" /></td>
				<td class="bold">% Efectivo <input name="por_efectivo" type="text" class="validate focus numberPosFormat right" precision="2" id="por_efectivo" size="6" maxlength="6" value="{por_efectivo}" /></td>
			</tr>
			<tr>
				<td class="bold">% J. IGNACIO <input name="por_bg_1" type="text" class="validate focus numberPosFormat right" precision="2" id="por_bg_1" size="6" maxlength="6" value="{por_bg_1}" /></td>
				<td class="bold">% Efectivo 1 <input name="por_efectivo_1" type="text" class="validate focus numberPosFormat right" precision="2" id="por_efectivo_1" size="6" maxlength="6" value="{por_efectivo_1}" /></td>
			</tr>
			<tr>
				<td class="bold">% J. MIGUEL <input name="por_bg_2" type="text" class="validate focus numberPosFormat right" precision="2" id="por_bg_2" size="6" maxlength="6" value="{por_bg_2}" /></td>
				<td class="bold">% Efectivo 2 <input name="por_efectivo_2" type="text" class="validate focus numberPosFormat right" precision="2" id="por_efectivo_2" size="6" maxlength="6" value="{por_efectivo_2}" /></td>
			</tr>
			<tr>
				<td class="bold">% I&Ntilde;AKI <input name="por_bg_3" type="text" class="validate focus numberPosFormat right" precision="2" id="por_bg_3" size="6" maxlength="6" value="{por_bg_3}" /></td>
				<td class="bold">% Efectivo 3 <input name="por_efectivo_3" type="text" class="validate focus numberPosFormat right" precision="2" id="por_efectivo_3" size="6" maxlength="6" value="{por_efectivo_3}" /></td>
			</tr>
			<tr>
				<td class="bold">% BG 4 <input name="por_bg_4" type="text" class="validate focus numberPosFormat right" precision="2" id="por_bg_4" size="6" maxlength="6" value="{por_bg_4}" /></td>
				<td class="bold">% Efectivo 4 <input name="por_efectivo_4" type="text" class="validate focus numberPosFormat right" precision="2" id="por_efectivo_4" size="6" maxlength="6" value="{por_efectivo_4}" /></td>
			</tr>
		</tbody>
		<tfoot>
			<tr>
				<td colspan="2">&nbsp;</td>
			</tr>
		</tfoot>
	</table>
	<p>
		<input type="button" name="cancelar" id="cancelar" value="Cancelar" />
		&nbsp;&nbsp;
		<input type="button" name="alta" id="alta" value="Alta" />
	</p>
</form>
