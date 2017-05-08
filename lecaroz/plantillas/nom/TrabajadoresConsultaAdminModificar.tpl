<form name="Datos" class="FormValidator FormStyles" id="Datos">
	<input name="id" type="hidden" id="id" value="{id}" />
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
			<th align="left" scope="row">Fecha de nacimiento</th>
			<td><input name="fecha_nac" type="text" class="valid Focus toDate center" id="fecha_nac" value="{fecha_nac}" size="10" maxlength="10" /></td>
		</tr>
		<tr class="linea_on">
			<th align="left" scope="row">Sexo</th>
			<td><input name="sexo" type="radio" id="sexo" value="FALSE"{sexo_f} />
				Hombre
				<input type="radio" name="sexo" id="sexo" value="TRUE"{sexo_t} />
				Mujer</td>
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
			<th align="left" scope="row">Fecha de alta en I.M.S.S.</th>
			<td><input name="fecha_alta_imss" type="text" class="valid Focus toDate center" id="fecha_alta_imss" value="{fecha_alta_imss}" size="10" maxlength="10" /></td>
		</tr>
		<tr class="linea_off">
			<th align="left" scope="row">Trabajador permanente</th>
			<td><input name="no_baja" type="checkbox" id="no_baja" value="TRUE"{no_baja} />
				Si</td>
		</tr>
		<tr class="linea_on">
			<th align="left" scope="row">N&uacute;mero de seguro social</th>
			<td><input name="num_afiliacion" type="text" class="valid onlyNumbers" id="num_afiliacion" value="{num_afiliacion}" size="11" maxlength="11" /></td>
		</tr>
		<tr class="linea_off">
			<th align="left" scope="row">Recibe aguinaldo</th>
			<td><input name="solo_aguinaldo" type="checkbox" id="solo_aguinaldo" value="TRUE"{solo_aguinaldo} />
				Si</td>
		</tr>
		<tr class="linea_on">
			<th align="left" scope="row">Tipo de c&aacute;lculo para aguinaldo</th>
			<td><select name="tipo" id="tipo">
				<option value="0"{tipo_0}>NORMAL</option>
				<option value="1"{tipo_1}>A 1 A&Ntilde;O</option>
				<option value="2"{tipo_2}>A 3 MESES</option>
			</select></td>
		</tr>
		<tr class="linea_off">
			<th align="left" scope="row">Observaciones</th>
			<td><textarea name="observaciones" cols="45" rows="5" class="valid toText toUpper" id="observaciones">{observaciones}</textarea></td>
		</tr>
		<tr class="linea_on">
			<th align="left" scope="row">Empleado especial</th>
			<td><input name="empleado_especial" type="checkbox" id="empleado_especial" value="1"{empleado_especial} />
				Si</td>
		</tr>
		<tr class="linea_off">
			<th align="left" scope="row">Baja para recursos humanos</th>
			<td><input name="baja_rh" type="checkbox" id="baja_rh" value="1"{baja_rh} />
				Si</td>
		</tr>
	</table>
	<p>
		<input type="button" name="cancelar" id="cancelar" value="Cancelar" />
		&nbsp;&nbsp;
		<input type="button" name="actualizar" id="actualizar" value="Actualizar datos del trabajador" />
	</p>
</form>
