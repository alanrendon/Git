<form name="Datos" class="FormValidator FormStyles" id="Datos">
	<table class="tabla_captura">
		<tr class="linea_off">
			<th align="left" scope="row">Compañía(s)</th>
			<td><input name="cias" type="text" class="valid toInterval" id="cias" size="30" /></td>
		</tr>
		<tr class="linea_on">
			<th align="left" scope="row">Trabajador(es)</th>
			<td><input name="trabajadores" type="text" class="valid toInterval" id="trabajadores" size="30" /></td>
		</tr>
		<t class="linea_off"r>
			<th align="left" scope="row">Nombre(s)</th>
			<td><input name="nombre" type="text" class="valid onlyText cleanText toUpper" id="nombre" size="30" /></td>
		</tr>
		<tr class="linea_on">
			<th align="left" scope="row">Ap. paterno</th>
			<td><input name="ap_paterno" type="text" class="valid onlyText cleanText toUpper" id="ap_paterno" size="30" /></td>
		</tr>
		<tr class="linea_off">
			<th align="left" scope="row">Ap. materno</th>
			<td><input name="ap_materno" type="text" class="valid onlyText cleanText toUpper" id="ap_materno" size="30" /></td>
		</tr>
		<tr class="linea_on">
			<th align="left" scope="row">R.F.C.</th>
			<td><input name="rfc" type="text" class="valid toRFC toUpper" id="rfc" size="13" maxlength="13" /></td>
		</tr>
		<tr class="linea_off">
			<th align="left" scope="row">Puesto</th>
			<td><select name="puesto" id="puesto">
				<option value=""></option>
				<!-- START BLOCK : puesto -->
				<option value="{value}" class="{color}">{text}</option>
				<!-- END BLOCK : puesto -->
			</select></td>
		</tr>
		<tr class="linea_on">
			<th align="left" scope="row">Turno</th>
			<td><select name="turno" id="turno">
				<option value=""></option>
				<!-- START BLOCK : turno -->
				<option value="{value}" class="{color}">{text}</option>
				<!-- END BLOCK : turno -->
			</select></td>
		</tr>
		<tr class="linea_off">
			<th align="left" scope="row">Incluir</th>
			<td>
				<input name="aguinaldo" type="checkbox" id="aguinaldo" value="1" checked="checked" />
				Con aguinaldo<br />
				<input name="no_aguinaldo" type="checkbox" id="no_aguinaldo" value="1" checked="checked" />
				Sin aguinaldo<br />
				<input name="afiliados" type="checkbox" id="afiliados" value="1" checked="checked" />
				Afiliados<br />
				<input name="no_afiliados" type="checkbox" id="no_afiliados" value="1" checked="checked" />
				No afiliados<br />
				<input name="bajas" type="checkbox" id="bajas" value="1" />
				Dados de baja en el/los último(s)
				<input name="meses_baja" type="text" class="valid Focus toPosInt center" id="meses_baja" size="2" />
				mes(es)
				<hr>
				<strong>Chequeos:</strong><br>
				(<input name="con_idempleado" type="checkbox" id="con_idempleado" value="1" checked="checked" />Con,
				<input name="sin_idempleado" type="checkbox" id="sin_idempleado" value="1" checked="checked" />Sin) ID de empleado<br>
				<input name="con_chequeos" type="checkbox" id="con_chequeos" value="1" checked="checked" />
				Con chequeos<br />
				<input name="chequeos_atrasados" type="checkbox" id="chequeos_atrasados" value="1" checked="checked" />
				&Uacute;ltimo chequeo menor a 20 d&iacute;as<br />
				<input name="sin_chequeos" type="checkbox" id="sin_chequeos" value="1" checked="checked" />
				Sin chequeos
				<hr>
				<strong>Documentos:</strong><br>
				(<input name="con_doc_acta_nacimiento" type="checkbox" id="con_doc_acta_nacimiento" value="1" checked="checked" />Con,
				<input name="sin_doc_acta_nacimiento" type="checkbox" id="sin_doc_acta_nacimiento" value="1" checked="checked" />Sin) Acta de nacimiento<br />
				(<input name="con_doc_comprobante_domicilio" type="checkbox" id="con_doc_comprobante_domicilio" value="1" checked="checked" />Con,
				<input name="sin_doc_comprobante_domicilio" type="checkbox" id="sin_doc_comprobante_domicilio" value="1" checked="checked" />Sin) Comprobante de domicilio<br />
				(<input name="con_doc_rfc" type="checkbox" id="con_doc_rfc" value="1" checked="checked" />Con,
				<input name="sin_doc_rfc" type="checkbox" id="sin_doc_rfc" value="1" checked="checked" />Sin) R.F.C.<br />
				(<input name="con_doc_curp" type="checkbox" id="con_doc_curp" value="1" checked="checked" />Con,
				<input name="sin_doc_curp" type="checkbox" id="sin_doc_curp" value="1" checked="checked" />Sin) C.U.R.P.<br />
				(<input name="con_doc_ife" type="checkbox" id="con_doc_ife" value="1" checked="checked" />Con,
				<input name="sin_doc_ife" type="checkbox" id="sin_doc_ife" value="1" checked="checked" />Sin) Credencial de elector<br />
				(<input name="con_doc_num_seguro_social" type="checkbox" id="con_doc_num_seguro_social" value="1" checked="checked" />Con,
				<input name="sin_doc_num_seguro_social" type="checkbox" id="sin_doc_num_seguro_social" value="1" checked="checked" />Sin) Número de seguro social<br />
				(<input name="con_doc_solicitud_trabajo" type="checkbox" id="con_doc_solicitud_trabajo" value="1" checked="checked" />Con,
				<input name="sin_doc_solicitud_trabajo" type="checkbox" id="sin_doc_solicitud_trabajo" value="1" checked="checked" />Sin) Solicitud de empleo<br />
				(<input name="con_doc_comprobante_estudios" type="checkbox" id="con_doc_comprobante_estudios" value="1" checked="checked" />Con,
				<input name="sin_doc_comprobante_estudios" type="checkbox" id="sin_doc_comprobante_estudios" value="1" checked="checked" />Sin) Comprobante de estudios<br />
				(<input name="con_doc_referencias" type="checkbox" id="con_doc_referencias" value="1" checked="checked" />Con,
				<input name="sin_doc_referencias" type="checkbox" id="sin_doc_referencias" value="1" checked="checked" />Sin) Referencias<br />
				(<input name="con_doc_no_antecedentes_penales" type="checkbox" id="con_doc_no_antecedentes_penales" value="1" checked="checked" />Con,
				<input name="sin_doc_no_antecedentes_penales" type="checkbox" id="sin_doc_no_antecedentes_penales" value="1" checked="checked" />Sin) Carta de no antecedentes penales<br />
				(<input name="con_doc_licencia_manejo" type="checkbox" id="con_doc_licencia_manejo" value="1" checked="checked" />Con,
				<input name="sin_doc_licencia_manejo" type="checkbox" id="sin_doc_licencia_manejo" value="1" checked="checked" />Sin) Licencia de manejo<br />
				(<input name="con_doc_no_adeudo_infonavit" type="checkbox" id="con_doc_no_adeudo_infonavit" value="1" checked="checked" />Con,
				<input name="sin_doc_no_adeudo_infonavit" type="checkbox" id="sin_doc_no_adeudo_infonavit" value="1" checked="checked" />Sin) Carta de no adeudo a Infonavit
			</td>
		</tr>
		<tr class="linea_on">
			<th align="left" scope="row">Ordenar por</th>
			<td><select name="orden[]" id="orden">
				<option value="puestos.orden, puestos.sueldo, ct.cod_puestos" selected="selected">PUESTO</option>
				<option value="turnos.orden_turno">TURNO</option>
				<option value="ct.ap_paterno, ct.ap_materno, ct.nombre">NOMBRE DE TRABAJADOR</option>
				<option value="ct.num_emp">NUMERO DE TRABAJADOR</option>
			</select>
				<br />
				<select name="orden[]" id="orden">
					<option value=""></option>
					<option value="puestos.orden, puestos.sueldo, ct.cod_puestos">PUESTO</option>
					<option value="turnos.orden_turno" selected="selected">TURNO</option>
					<option value="ct.ap_paterno, ct.ap_materno, ct.nombre">NOMBRE DE TRABAJADOR</option>
					<option value="ct.num_emp">NUMERO DE TRABAJADOR</option>
				</select>
				<br />
				<select name="orden[]" id="orden">
					<option value=""></option>
					<option value="puestos.orden, puestos.sueldo, ct.cod_puestos">PUESTO</option>
					<option value="turnos.orden_turno">TURNO</option>
					<option value="ct.ap_paterno, ct.ap_materno, ct.nombre" selected="selected">NOMBRE DE TRABAJADOR</option>
					<option value="ct.num_emp">NUMERO DE TRABAJADOR</option>
				</select>
				<br />
				<select name="orden[]" id="orden">
					<option value="" selected="selected"></option>
					<option value="puestos.orden, puestos.sueldo, ct.cod_puestos">PUESTO</option>
					<option value="turnos.orden_turno">TURNO</option>
					<option value="ct.ap_paterno, ct.ap_materno, ct.nombre">NOMBRE DE TRABAJADOR</option>
					<option value="ct.num_emp">NUMERO DE TRABAJADOR</option>
				</select></td>
		</tr>
	</table>
	<p>
		<input type="button" name="listado" id="listado" value="Listado de trabajadores"{disabled} />
&nbsp;&nbsp;
<input type="button" name="repetidos" id="repetidos" value="Trabajadores repetidos"{disabled} />
&nbsp;&nbsp;
<input type="button" name="similares" id="similares" value="Trabajadores similares" />
&nbsp;&nbsp;
		<input type="button" name="buscar" id="buscar" value="Buscar trabajadores"{disabled} />
	</p>
</form>
