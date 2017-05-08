<form name="Datos" class="FormValidator FormStyles" id="Datos">
	<table class="tabla_captura">
		<tr class="linea_off">
			<th align="left">Compa&ntilde;&iacute;a</th>
			<td><input name="num_cia" type="text" class="valid Focus toPosInt center" id="num_cia" value="{num_cia}" size="3" />
			<input name="nombre_cia" type="text" disabled="disabled" id="nombre_cia" size="40" /></td>
		</tr>
		<tr class="linea_on">
			<th align="left">Labora en</th>
			<td><input name="num_cia_emp" type="text" class="valid Focus toPosInt center" id="num_cia_emp" size="3" />
			<input name="nombre_cia_emp" type="text" disabled="disabled" id="nombre_cia_emp" size="40" /></td>
		</tr>
		<tr>
			<td colspan="2" align="left">&nbsp;</td>
		</tr>
		<tr>
			<th colspan="2" align="left"><img src="/lecaroz/imagenes/info.png" width="16" height="16" /> Informaci&oacute;n general</th>
		</tr>
		<tr class="linea_off">
			<th align="left">Nombre</th>
			<td><input name="nombre" type="text" class="valid onlyText cleanText toUpper" id="nombre" size="20" maxlength="50" /></td>
		</tr>
		<tr class="linea_on">
			<th align="left">Apellido paterno</th>
			<td><input name="ap_paterno" type="text" class="valid onlyText cleanText toUpper" id="ap_paterno" size="20" maxlength="50" /></td>
		</tr>
		<tr class="linea_off">
			<th align="left">Apellido materno</th>
			<td><input name="ap_materno" type="text" class="valid onlyText cleanText toUpper" id="ap_materno" size="20" maxlength="50" /></td>
		</tr>
		<tr class="linea_on">
			<th align="left">R.F.C.</th>
			<td><input name="rfc" type="text" class="valid toRFCopcional toUpper" id="rfc" size="13" maxlength="13" /></td>
		</tr>
		<tr class="linea_off">
			<th align="left">C.U.R.P.</th>
			<td>
				<input name="curp" type="text" class="valid toCURP toUpper" id="curp" size="18" maxlength="18" />
			</td>
		</tr>
		<tr class="linea_on">
			<th align="left">Fecha de nacimiento</th>
			<td><input name="fecha_nac" type="text" class="valid Focus toDate center" id="fecha_nac" size="10" maxlength="10" /></td>
		</tr>
		<tr class="linea_off">
			<th align="left">Lugar de nacimiento</th>
			<td><input name="lugar_nac" type="text" class="valid toText cleanText toUpper" id="lugar_nac" size="20" maxlength="25" /></td>
		</tr>
		<tr class="linea_on">
			<th align="left">Sexo</th>
			<td><input name="sexo" type="radio" id="sexo" value="FALSE" checked="checked" />
				Hombre
				<input type="radio" name="sexo" id="sexo" value="TRUE" />
				Mujer</td>
		</tr>
		<tr>
			<th colspan="2" align="left"><img src="/lecaroz/imagenes/info.png" width="16" height="16" /> Domicilio y contacto</th>
		</tr>
		<tr class="linea_off">
			<th align="left">Calle y n&uacute;mero</th>
			<td><input name="calle" type="text" class="valid toText cleanText toUpper" id="calle" size="40" maxlength="50" /></td>
		</tr>
		<tr class="linea_on">
			<th align="left">Colonia</th>
			<td><input name="colonia" type="text" class="valid toText cleanText toUpper" id="colonia" size="40" maxlength="40" /></td>
		</tr>
		<tr class="linea_off">
			<th align="left">Delegaci&oacute;n o municipio</th>
			<td><input name="del_mun" type="text" class="valid toText cleanText toUpper" id="del_mun" size="40" maxlength="40" /></td>
		</tr>
		<tr class="linea_on">
			<th align="left">Entidad</th>
			<td><input name="entidad" type="text" class="valid toText cleanText toUpper" id="entidad" size="40" maxlength="30" /></td>
		</tr>
		<tr class="linea_off">
			<th align="left">C&oacute;digo postal</th>
			<td><input name="cod_postal" type="text" class="valid onlyNumbers" id="cod_postal" size="5" maxlength="5" /></td>
		</tr>
		<tr class="linea_on">
			<th align="left">Tel&eacute;fono de casa</th>
			<td><input name="telefono_casa" type="text" class="valid toPhoneNumber" id="telefono_casa" size="20" maxlength="20" /></td>
		</tr>
		<tr class="linea_off">
			<th align="left">Tel&eacute;fono movil</th>
			<td><input name="telefono_movil" type="text" class="valid toPhoneNumber" id="telefono_movil" size="20" maxlength="20" /></td>
		</tr>
		<tr class="linea_on">
			<th align="left">Correo electr&oacute;nico</th>
			<td><input name="email" type="text" class="valid toEmail" id="email" size="40" maxlength="200" /></td>
		</tr>
		<tr>
			<td colspan="2" align="left">&nbsp;</td>
		</tr>
		<tr>
			<th colspan="2" align="left"><img src="/lecaroz/imagenes/info.png" width="16" height="16" /> Informaci&oacute;n laboral</th>
		</tr>
		<tr class="linea_off">
			<th align="left">Fecha de ingreso</th>
			<td><input name="fecha_alta" type="text" class="valid Focus toDate center" id="fecha_alta" value="{fecha}" size="10" maxlength="10" /></td>
		</tr>
		<tr class="linea_on">
			<th align="left">Puesto</th>
			<td><select name="cod_puestos" id="cod_puestos">
			</select></td>
		</tr>
		<tr class="linea_off">
			<th align="left">Turno</th>
			<td><select name="cod_turno" id="cod_turno">
			</select></td>
		</tr>
		<tr class="linea_on">
			<th align="left">Horario</th>
			<td><select name="cod_horario" id="cod_horario">
			</select></td>
		</tr>
		<tr class="linea_off">
			<th align="left">Salario diario</th>
			<td><input name="salario" type="text" class="valid Focus numberPosFormat right" precision="2" id="salario" size="10" /></td>
		</tr>
		<tr class="linea_on">
			<th align="left">Salario diario integrado</th>
			<td><input name="salario_integrado" type="text" class="valid Focus numberPosFormat right" precision="2" id="salario_integrado" size="10" /></td>
		</tr>
		<tr class="linea_off">
			<th align="left">Fecha de alta en I.M.S.S.</th>
			<td><input name="fecha_alta_imss" type="text" class="valid Focus toDate center" id="fecha_alta_imss" size="10" maxlength="10" /></td>
		</tr>
		<tr class="linea_on">
			<th align="left">Trabajador permanente</th>
			<td><input name="no_baja" type="checkbox" id="no_baja" value="TRUE" />
				Si</td>
		</tr>
		<tr class="linea_off">
			<th align="left">N&uacute;mero de seguro social</th>
			<td><input name="num_afiliacion" type="text" class="valid onlyNumbers" id="num_afiliacion" size="11" maxlength="11" /></td>
		</tr>
		<tr class="linea_on">
			<th align="left">Cr&eacute;dito Infonavit</th>
			<td><input name="credito_infonavit" type="checkbox" id="credito_infonavit" value="TRUE" />
			Si</td>
		</tr>
		<tr class="linea_off">
			<th align="left">N&uacute;mero de cr&eacute;dito Infonavit</th>
			<td><input name="no_infonavit" type="text" class="valid onlyNumbers" id="no_infonavit" size="11" maxlength="10" /></td>
		</tr>
		<tr class="linea_on">
			<th align="left">Recibe aguinaldo</th>
			<td><input name="solo_aguinaldo" type="checkbox" id="solo_aguinaldo" value="TRUE" />
				Si</td>
		</tr>
		<tr class="linea_off">
			<th align="left">Tipo de c&aacute;lculo para aguinaldo</th>
			<td><select name="tipo" id="tipo">
				<option value="0">NORMAL</option>
				<option value="1">A 1 A&Ntilde;O</option>
				<option value="2">A 3 MESES</option>
			</select></td>
		</tr>
		<tr class="linea_on">
			<th align="left">Último aguinaldo</th>
			<td><input name="aguinaldo" type="text" class="valid Focus numberPosFormat right" precision="2" id="aguinaldo" size="10" /></td>
		</tr>
		<tr class="linea_on">
			<th align="left">Fecha de vencimiento de licencia de manejo</th>
			<td><input name="fecha_vencimiento_licencia_manejo" type="text" class="valid Focus toDate center" id="fecha_vencimiento_licencia_manejo" value="" size="10" maxlength="10" /></td>
		</tr>
		<tr class="linea_off">
			<th align="left">Observaciones</th>
			<td><textarea name="observaciones" cols="45" rows="5" class="valid toText toUpper" id="observaciones"></textarea></td>
		</tr>
		<tr>
			<td colspan="2" align="left">&nbsp;</td>
		</tr>
		<tr>
			<th colspan="2" align="left"><img src="/lecaroz/imagenes/info.png" width="16" height="16" /> Otros</th>
		</tr>
		<tr class="linea_off">
			<th align="left">Fecha de entrega de uniforme</th>
			<td><input name="uniforme" type="text" class="valid Focus toDate center" id="uniforme" size="10" maxlength="10" /></td>
		</tr>
		<tr class="linea_on">
			<th align="left">Talla</th>
			<td><select id="talla" class="insert" name="talla">
				<option selected="" value=""></option>
				<option value="1">CHICA</option>
				<option value="2">MEDIANA</option>
				<option value="3">GRANDE</option>
				<option value="4">EXTRA GRANDE</option>
			</select></td>
		</tr>
		<tr class="linea_off">
			<th align="left">Dep&oacute;sito por bata</th>
			<td><input name="control_bata" type="checkbox" id="control_bata" value="TRUE" />
			Si</td>
		</tr>
		<tr class="linea_on">
			<th align="left">Monto de dep&oacute;sito</th>
			<td><input name="deposito_bata" type="text" class="valid Focus numberPosFormat right" precision="2" id="deposito_bata" size="10" /></td>
		</tr>
		<tr>
			<td colspan="2" align="left">&nbsp;</td>
		</tr>
		<tr>
			<th colspan="2" align="left"><img src="/lecaroz/imagenes/info.png" width="16" height="16" /> Checador</th>
		</tr>
		<tr class="linea_off">
			<th align="left">ID de empleado en checador</th>
			<td><input name="idempleado" type="text" class="valid Focus toPosInt center" id="idempleado" size="6" /></td>
		</tr>
		<tr class="linea_on">
			<th align="left">Nombre de empleado en checador</th>
			<td><input name="nombre_checador" type="text" id="nombre_checador" size="40" disabled="" /></td>
		</tr>
		<tr class="linea_off">
			<th align="left">Huellas</th>
			<td><input name="num_huellas" type="text" id="num_huellas" size="5" disabled="" /></td>
		</tr>
	</table>
	<p>
		<input type="button" name="cancelar" id="cancelar" value="Cancelar" />
		&nbsp;&nbsp;
		<input type="button" name="alta" id="alta" value="Alta de trabajador" />
	</p>
</form>
