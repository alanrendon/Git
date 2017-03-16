<form name="Datos" class="FormValidator FormStyles" id="Datos">
	<input name="id" type="hidden" id="id" value="{id}" />
	<input name="pendiente_alta" type="hidden" id="pendiente_alta" value="{pendiente_alta}" />
	<input name="pendiente_baja" type="hidden" id="pendiente_baja" value="{pendiente_baja}" />
	<table class="tabla_captura">
		<tr class="linea_off">
			<th align="left" scope="row">Compa&ntilde;&iacute;a</th>
			<td><input name="num_cia" type="text" class="valid Focus toPosInt center" id="num_cia" value="{num_cia}" size="3"{readonly} />
			<input name="nombre_cia" type="text" disabled="disabled" id="nombre_cia" value="{nombre_cia}" size="40" /></td>
		</tr>
		<tr class="linea_on">
			<th align="left" scope="row">Labora en</th>
			<td><input name="num_cia_emp" type="text" class="valid Focus toPosInt center" id="num_cia_emp" value="{num_cia_emp}" size="3" />
			<input name="nombre_cia_emp" type="text" disabled="disabled" id="nombre_cia_emp" value="{nombre_cia_emp}" size="40" /></td>
		</tr>
		<!-- START BLOCK : saldo -->
		<tr class="linea_off">
			<td colspan="2" align="left" scope="row" class="red bold">El empleado tiene un saldo pendiente de {saldo} y no puede ser cambiado de compañía.</td>
		</tr>
		<!-- END BLOCK : saldo -->
		<tr>
			<td colspan="2" align="left" scope="row">&nbsp;</td>
		</tr>
		<tr>
			<th colspan="2" align="left" scope="row"><img src="/lecaroz/imagenes/info.png" width="16" height="16" /> Informaci&oacute;n general</th>
		</tr>
		<tr class="linea_off">
			<th align="left" scope="row">Nombre</th>
			<td><input name="nombre" type="text" class="valid onlyText cleanText toUpper" id="nombre" value="{nombre}" size="20" maxlength="50" /></td>
		</tr>
		<tr class="linea_on">
			<th align="left" scope="row">Apellido paterno</th>
			<td><input name="ap_paterno" type="text" class="valid onlyText cleanText toUpper" id="ap_paterno" value="{ap_paterno}" size="20" maxlength="50" /></td>
		</tr>
		<tr class="linea_off">
			<th align="left" scope="row">Apellido materno</th>
			<td><input name="ap_materno" type="text" class="valid onlyText cleanText toUpper" id="ap_materno" value="{ap_materno}" size="20" maxlength="50" /></td>
		</tr>
		<tr class="linea_on">
			<th align="left" scope="row">R.F.C.</th>
			<td><input name="rfc" type="text" class="valid toRFCopcional toUpper" id="rfc" value="{rfc}" size="13" maxlength="13" /></td>
		</tr>
		<tr class="linea_off">
			<th align="left" scope="row">C.U.R.P.</th>
			<td>
				<input name="curp" type="text" class="valid toCURP toUpper" id="curp" value="{curp}" size="18" maxlength="18" />
			</td>
		</tr>
		<tr class="linea_on">
			<th align="left" scope="row">Fecha de nacimiento</th>
			<td><input name="fecha_nac" type="text" class="valid Focus toDate center" id="fecha_nac" value="{fecha_nac}" size="10" maxlength="10" /></td>
		</tr>
		<tr class="linea_off">
			<th align="left" scope="row">Lugar de nacimiento</th>
			<td><input name="lugar_nac" type="text" class="valid toText cleanText toUpper" id="lugar_nac" value="{lugar_nac}" size="20" maxlength="25" /></td>
		</tr>
		<tr class="linea_on">
			<th align="left" scope="row">Sexo</th>
			<td><input name="sexo" type="radio" id="sexo" value="FALSE"{sexo_f} />
				Hombre
				<input type="radio" name="sexo" id="sexo" value="TRUE"{sexo_t} />
				Mujer</td>
		</tr>
		<tr>
			<th colspan="2" align="left" scope="row"><img src="/lecaroz/imagenes/info.png" width="16" height="16" /> Domicilio y contacto</th>
		</tr>
		<tr class="linea_off">
			<th align="left" scope="row">Calle y n&uacute;mero</th>
			<td><input name="calle" type="text" class="valid toText cleanText toUpper" id="calle" value="{calle}" size="40" maxlength="50" /></td>
		</tr>
		<tr class="linea_on">
			<th align="left" scope="row">Colonia</th>
			<td><input name="colonia" type="text" class="valid toText cleanText toUpper" id="colonia" value="{colonia}" size="40" maxlength="40" /></td>
		</tr>
		<tr class="linea_off">
			<th align="left" scope="row">Delegaci&oacute;n o municipio</th>
			<td><input name="del_mun" type="text" class="valid toText cleanText toUpper" id="del_mun" value="{del_mun}" size="40" maxlength="40" /></td>
		</tr>
		<tr class="linea_on">
			<th align="left" scope="row">Entidad</th>
			<td><input name="entidad" type="text" class="valid toText cleanText toUpper" id="entidad" value="{entidad}" size="40" maxlength="30" /></td>
		</tr>
		<tr class="linea_off">
			<th align="left" scope="row">C&oacute;digo postal</th>
			<td><input name="cod_postal" type="text" class="valid onlyNumbers" id="cod_postal" value="{cod_postal}" size="5" maxlength="5" /></td>
		</tr>
		<tr class="linea_on">
			<th align="left" scope="row">Tel&eacute;fono de casa</th>
			<td><input name="telefono_casa" type="text" class="valid toPhoneNumber" id="telefono_casa" value="{telefono_casa}" size="20" maxlength="20" /></td>
		</tr>
		<tr class="linea_off">
			<th align="left" scope="row">Tel&eacute;fono movil</th>
			<td><input name="telefono_movil" type="text" class="valid toPhoneNumber" id="telefono_movil" value="{telefono_movil}" size="20" maxlength="20" /></td>
		</tr>
		<tr class="linea_on">
			<th align="left" scope="row">Correo electr&oacute;nico</th>
			<td><input name="email" type="text" class="valid toEmail" id="email" value="{email}" size="40" maxlength="200" /></td>
		</tr>
		<tr>
			<td colspan="2" align="left" scope="row">&nbsp;</td>
		</tr>
		<tr>
			<th colspan="2" align="left" scope="row"><img src="/lecaroz/imagenes/info.png" width="16" height="16" /> Informaci&oacute;n laboral</th>
		</tr>
		<tr class="linea_off">
			<th align="left" scope="row">Fecha de ingreso</th>
			<td><input name="fecha_alta" type="text" class="valid Focus toDate center" id="fecha_alta" value="{fecha_alta}" size="10" maxlength="10" /></td>
		</tr>
		<tr class="linea_on">
			<th align="left" scope="row">Puesto</th>
			<td><select name="cod_puestos" id="cod_puestos">
				<!-- START BLOCK : puesto -->
				<option value="{value}"{selected}>{text}</option>
				<!-- END BLOCK : puesto -->
			</select></td>
		</tr>
		<tr class="linea_off">
			<th align="left" scope="row">Turno</th>
			<td><select name="cod_turno" id="cod_turno">
				<!-- START BLOCK : turno -->
				<option value="{value}"{selected}>{text}</option>
				<!-- END BLOCK : turno -->
			</select></td>
		</tr>
		<tr class="linea_on">
			<th align="left" scope="row">Horario</th>
			<td><select name="cod_horario" id="cod_horario">
				<!-- START BLOCK : horario -->
				<option value="{value}"{selected}>{text}</option>
				<!-- END BLOCK : horario -->
			</select></td>
		</tr>
		<tr class="linea_off">
			<th align="left" scope="row">Salario diario</th>
			<td><input name="salario" type="text" class="valid Focus numberPosFormat right" id="salario" value="{salario}" size="10" precision="2" /></td>
		</tr>
		<tr class="linea_on">
			<th align="left" scope="row">Salario diario integrado</th>
			<td><input name="salario_integrado" type="text" class="valid Focus numberPosFormat right" id="salario_integrado" value="{salario_integrado}" size="10" precision="2" /></td>
		</tr>
		<tr class="linea_off">
			<th align="left" scope="row">Fecha de alta en I.M.S.S.</th>
			<td><input name="fecha_alta_imss" type="text" class="valid Focus toDate center" id="fecha_alta_imss" value="{fecha_alta_imss}" size="10" maxlength="10" /></td>
		</tr>
		<tr class="linea_on">
			<th align="left" scope="row">Trabajador permanente</th>
			<td><input name="no_baja" type="checkbox" id="no_baja" value="TRUE"{no_baja} />
				Si</td>
		</tr>
		<tr class="linea_off">
			<th align="left" scope="row">N&uacute;mero de seguro social</th>
			<td><input name="num_afiliacion" type="text" class="valid onlyNumbers" id="num_afiliacion" value="{num_afiliacion}" size="11" maxlength="11" /></td>
		</tr>
		<tr class="linea_on">
			<th align="left" scope="row">Cr&eacute;dito Infonavit</th>
			<td><input name="credito_infonavit" type="checkbox" id="credito_infonavit" value="TRUE"{credito_infonavit} />
			Si</td>
		</tr>
		<tr class="linea_off">
			<th align="left" scope="row">N&uacute;mero de cr&eacute;dito Infonavit</th>
			<td><input name="no_infonavit" type="text" class="valid onlyNumbers" id="no_infonavit" value="{no_infonavit}" size="11" maxlength="10" /></td>
		</tr>
		<tr class="linea_on">
			<th align="left" scope="row">Recibe aguinaldo</th>
			<td><input name="solo_aguinaldo" type="checkbox" id="solo_aguinaldo" value="TRUE"{solo_aguinaldo} />
				Si</td>
		</tr>
		<tr class="linea_off">
			<th align="left" scope="row">Tipo de c&aacute;lculo para aguinaldo</th>
			<td><select name="tipo" id="tipo">
				<option value="0"{tipo_0}>NORMAL</option>
				<option value="1"{tipo_1}>A 1 A&Ntilde;O</option>
				<option value="2"{tipo_2}>A 3 MESES</option>
			</select></td>
		</tr>
		<tr class="linea_on">
			<th align="left" scope="row">Fecha de vencimiento de licencia de manejo</th>
			<td><input name="fecha_vencimiento_licencia_manejo" type="text" class="valid Focus toDate center" id="fecha_vencimiento_licencia_manejo" value="{fecha_vencimiento_licencia_manejo}" size="10" maxlength="10" /></td>
		</tr>
		<tr class="linea_off">
			<th align="left" scope="row">Observaciones</th>
			<td><textarea name="observaciones" cols="45" rows="5" class="valid toText toUpper" id="observaciones">{observaciones}</textarea></td>
		</tr>
		<tr>
			<td colspan="2" align="left" scope="row">&nbsp;</td>
		</tr>
		<tr>
			<th colspan="2" align="left" scope="row"><img src="../../imagenes/info.png" width="16" height="16" /> Otros</th>
		</tr>
		<tr class="linea_off">
			<th align="left" scope="row">Fecha de entrega de uniforme</th>
			<td><input name="uniforme" type="text" class="valid Focus toDate center" id="uniforme" value="{uniforme}" size="10" maxlength="10" /></td>
		</tr>
		<tr class="linea_on">
			<th align="left" scope="row">Talla</th>
			<td><select id="talla" class="insert" name="talla">
				<option value=""{talla_}></option>
				<option value="1"{talla_1}>CHICA</option>
				<option value="2"{talla_2}>MEDIANA</option>
				<option value="3"{talla_3}>GRANDE</option>
				<option value="4"{talla_4}>EXTRA GRANDE</option>
			</select></td>
		</tr>
		<tr class="linea_off">
			<th align="left" scope="row">Dep&oacute;sito por bata</th>
			<td><input name="control_bata" type="checkbox" id="control_bata" value="TRUE"{control_bata} />
			Si</td>
		</tr>
		<tr class="linea_on">
			<th align="left" scope="row">Monto de dep&oacute;sito</th>
			<td><input name="deposito_bata" type="text" class="valid Focus numberPosFormat right" id="deposito_bata" value="{deposito_bata}" size="10" precision="2" /></td>
		</tr>
		<tr>
			<td colspan="2" align="left">&nbsp;</td>
		</tr>
		<tr>
			<th colspan="2" align="left"><img src="/lecaroz/imagenes/info.png" width="16" height="16" /> Checador</th>
		</tr>
		<tr class="linea_off">
			<th align="left">ID de empleado en checador</th>
			<td><input name="idempleado" type="text" class="valid Focus toPosInt center" id="idempleado" size="6" value="{idempleado}" /></td>
		</tr>
		<tr class="linea_on">
			<th align="left">Nombre de empleado en checador</th>
			<td><input name="nombre_checador" type="text" id="nombre_checador" size="40" disabled="" value="{nombre_checador}" /></td>
		</tr>
		<tr class="linea_off">
			<th align="left">Huellas</th>
			<td><input name="num_huellas" type="text" id="num_huellas" size="5" disabled="" value="{num_huellas}" /></td>
		</tr>
	</table>
	<p>
		<input type="button" name="cancelar" id="cancelar" value="Cancelar" />
		&nbsp;&nbsp;
		<input type="button" name="actualizar" id="actualizar" value="Actualizar datos del trabajador" />
	</p>
</form>
