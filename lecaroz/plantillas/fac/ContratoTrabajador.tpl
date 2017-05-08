<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Contratos de Trabajo</title>

<link href="/lecaroz/smarty/styles/layout.css" rel="stylesheet" type="text/css" />
<link href="/lecaroz/smarty/styles/tablas.css" rel="stylesheet" type="text/css" />
<link href="/lecaroz/styles/font-style.css" rel="stylesheet" type="text/css" />
<link href="/lecaroz/styles/FormValidator.css" rel="stylesheet" type="text/css" />
<link href="/lecaroz/styles/FormStyles.css" rel="stylesheet" type="text/css" />
<link href="/lecaroz/styles/calendar.css" rel="stylesheet" type="text/css" />
<link href="/lecaroz/styles/Popups.css" rel="stylesheet" type="text/css" />
<link href="/lecaroz/styles/Tips.css" rel="stylesheet" type="text/css" />

<script type="text/javascript" src="jscripts/mootools/mootools-1.2-core.js"></script>
<script type="text/javascript" src="jscripts/mootools/mootools-1.2-more.js"></script>
<script type="text/javascript" src="jscripts/mootools/String.implement.js"></script>
<script type="text/javascript" src="jscripts/mootools/Number.implement.js"></script>
<script type="text/javascript" src="jscripts/mootools/Array.implement.js"></script>
<script type="text/javascript" src="jscripts/mootools/FormValidator.js"></script>
<script type="text/javascript" src="jscripts/mootools/FormStyles.js"></script>
<script type="text/javascript" src="jscripts/mootools/Popups.js"></script>
<script type="text/javascript" src="jscripts/mootools/Calendar.js"></script>
<script type="text/javascript" src="jscripts/fac/ContratoTrabajador.js"></script>
<script language="JavaScript" type="text/javascript" src="menus/stm31.js"></script>

</head>

<body>
<div id="contenedor">
	<div id="titulo">Contratos de Trabajo </div>
	<div id="captura" align="center">
		<form name="Datos" class="FormValidator FormStyles" id="Datos">
			<table class="tabla_captura">
				<tr class="linea_on">
					<th align="left" scope="row">Compa&ntilde;&iacute;a</th>
					<td><input name="num_cia" type="text" class="valid Focus toPosInt center" id="num_cia" size="3" />
					<input name="nombre_cia" type="text" disabled="disabled" id="nombre_cia" size="45" /></td>
				</tr>
				<tr>
					<th align="left" scope="row">Empleados</th>
					<td><select name="empleado" id="empleado" style="width:98%;">
						<option value=""></option>
					</select>					</td>
				</tr>
				<tr>
					<th align="left" scope="row">Segunda compa&ntilde;&iacute;a</th>
					<td><input name="num_cia_sec" type="text" class="valid Focus toPosFloat center" id="num_cia_sec" size="3" />
						<input name="nombre_cia_sec" type="text" disabled="disabled" id="nombre_cia_sec" size="45" /></td>
				</tr>
				<tr class="linea_on">
					<th align="left" scope="row">Tipo de contrato </th>
					<td><input name="tipo" type="radio" value="1" checked="checked" />
						Tiempo determinado<br />
						<input name="tipo" type="radio" value="2" />
						Tiempo indeterminado </td>
				</tr>
				<tr>
					<td colspan="2" align="left" scope="row">&nbsp;</td>
				</tr>
				<tr>
					<th colspan="2" align="left" scope="row"><img src="/lecaroz/iconos/info.png" width="16" height="16" /> Datos del trabajador </th>
				</tr>
				<tr class="linea_off" id="nombre_row">
					<th align="left" scope="row">Nombre(s)</th>
					<td><input name="nombre" type="text" class="valid onlyText cleanText toUpper" id="nombre" size="50" maxlength="50" /></td>
					</tr>
				<tr class="linea_on">
					<th align="left" scope="row">Apellido paterno </th>
					<td><input name="ap_paterno" type="text" class="valid onlyText cleanText toUpper" id="ap_paterno" size="50" maxlength="50" /></td>
				</tr>
				<tr class="linea_off">
					<th align="left" scope="row">Apellido materno </th>
					<td><input name="ap_materno" type="text" class="valid onlyText cleanText toUpper" id="ap_materno" size="50" maxlength="50" /></td>
				</tr>
				<tr class="linea_on">
					<th align="left" scope="row">R.F.C.</th>
					<td><input name="rfc" type="text" class="valid Focus toRFCopcional toUpper" id="rfc" size="14" maxlength="13" /></td>
				</tr>
				<tr class="linea_off">
					<th align="left" scope="row">C.U.R.P.</th>
					<td><input name="curp" type="text" class="valid Focus toCURP toUpper" id="curp" size="18" maxlength="18" /></td>
					</tr>
				<tr class="linea_on">
					<th align="left" scope="row">Sexo</th>
					<td><input name="sexo" type="radio" value="FALSE" checked="checked" />
						Masculino
							<br />
						<input name="sexo" type="radio" value="TRUE" />
						Femenino</td>
				</tr>
				<tr class="linea_off">
					<th align="left" scope="row">Fecha de nacimiento </th>
					<td><input name="fecha_nacimiento" type="text" class="valid Focus toDate center" id="fecha_nacimiento" size="10" maxlength="10" /></td>
				</tr>
				<tr class="linea_on">
					<th align="left" scope="row">Estado civil </th>
					<td><input name="estado_civil" type="radio" value="1" checked="checked" />
						Soltero(a)<br />
						<input name="estado_civil" type="radio" value="2" />
						Casado(a)<br />
						<input name="estado_civil" type="radio" value="3" />
						Viudo(a)<br />
						<input name="estado_civil" type="radio" value="4" />
						Separado(a)<br />
						<input name="estado_civil" type="radio" value="5" />
						Divorciado(a)<br />
						<input name="estado_civil" type="radio" value="6" />
						Union libre</td>
				</tr>
				<tr class="linea_off">
					<th align="left" scope="row">Calle y n&uacute;mero</th>
					<td><input name="calle" type="text" class="valid toText toUpper cleanText" id="calle" size="50" maxlength="200" /></td>
				</tr>
				<tr class="linea_on">
					<th align="left" scope="row">Colonia</th>
					<td><input name="colonia" type="text" class="valid toText toUpper cleanText" id="colonia" size="50" maxlength="200" /></td>
				</tr>
				<tr class="linea_off">
					<th align="left" scope="row">Delegaci&oacute;n/Municipio</th>
					<td><input name="municipio" type="text" class="valid toText toUpper cleanText" id="municipio" size="50" maxlength="200" /></td>
				</tr>
				<tr class="linea_on">
					<th align="left" scope="row">Estado</th>
					<td><input name="estado" type="text" class="valid toText toUpper cleanText" id="estado" size="50" maxlength="200" /></td>
				</tr>
				<tr class="linea_off">
					<th align="left" scope="row">C&oacute;digo postal </th>
					<td><input name="codigo_postal" type="text" class="valid onlyNumbers cleanText" id="codigo_postal" size="5" maxlength="20" /></td>
				</tr>
				<tr class="linea_on">
					<th align="left" scope="row">Correo electr&oacute;nico</th>
					<td><input name="email" type="text" class="valid toEmail toLower" id="email" size="50" maxlength="200" /></td>
				</tr>
				<tr class="linea_on">
					<td colspan="2" align="left" scope="row">&nbsp;</td>
					</tr>
				<tr class="linea_off">
					<th colspan="2" align="left" scope="row"><img src="/lecaroz/iconos/info.png" width="16" height="16" /> Datos laborales</th>
					</tr>
				<tr class="linea_on">
					<th align="left" scope="row">Labora en</th>
					<td><select name="num_cia_emp" id="num_cia_emp">
					</select></td>
				</tr>
				<tr class="linea_off">
							<th align="left" scope="row">Puesto</th>
							<td><select name="puesto" id="puesto">
							</select></td>
						</tr>
				<tr class="linea_on">
					<th align="left" scope="row">Turno</th>
					<td><select name="turno" id="turno">
					</select></td>
				</tr>
				<tr class="linea_off">
					<th align="left" scope="row">Fecha de inicio </th>
					<td><input name="fecha_inicio" type="text" class="valid Focus toDate center" id="fecha_inicio" size="10" maxlength="10" /></td>
				</tr>
				<tr class="linea_on">
					<th align="left" scope="row">Fecha de termino </th>
					<td><input name="fecha_termino" type="text" class="valid Focus toDate center" id="fecha_termino" size="10" maxlength="10" /></td>
				</tr>
				<tr class="linea_off">
					<th align="left" scope="row">Hora de inicio</th>
					<td><input name="hora_inicio" type="text" class="valid Focus toTime center" id="hora_inicio" size="5" maxlength="5" /></td>
				</tr>
				<tr class="linea_on">
					<th align="left" scope="row">Hora de termino</th>
					<td><input name="hora_termino" type="text" class="valid Focus toTime center" id="hora_termino" size="5" maxlength="5" /></td>
				</tr>
				<tr class="linea_off">
					<th align="left" scope="row">Salario</th>
					<td>$
					<input name="salario" type="text" class="valid Focus numberPosFormat right" precision="2" id="salario" size="10" /></td>
				</tr>
				<tr class="linea_on">
					<th align="left" scope="row">Documentos presentados</th>
					<td>
						<input name="doc_acta_nacimiento" type="checkbox" id="doc_acta_nacimiento" value="1" />
						Acta de nacimiento<br />
						<input name="doc_comprobante_domicilio" type="checkbox" id="doc_comprobante_domicilio" value="1" />
						Comprobante de domicilio<br />
						<input name="doc_curp" type="checkbox" id="doc_curp" value="1" />
						CURP<br />						<input name="doc_ife" type="checkbox" id="doc_ife" value="1" />
						Credencial del IFE<br />
						<input name="doc_num_seguro_social" type="checkbox" id="doc_num_seguro_social" value="1" />
						N&uacute;mero de seguro social<br />
						<input name="doc_solicitud_trabajo" type="checkbox" id="doc_solicitud_trabajo" value="1" />
						Solicitud de trabajo<br />
						<input name="doc_comprobante_estudios" type="checkbox" id="doc_comprobante_estudios" value="1" />
						Comprobante de estudios<br />
						<input name="doc_referencias" type="checkbox" id="doc_referencias" value="1" />
						Referencias laborales y/o cartas de recomendaci&oacute;n<br />
						<input name="doc_no_antecedentes_penales" type="checkbox" id="doc_no_antecedentes_penales" value="1" />
						Carta de no antencentes penales<br />
						<input name="doc_licencia_manejo" type="checkbox" id="doc_licencia_manejo" value="1" />
						Licencia de manejo <input name="fecha_vencimiento_licencia_manejo" type="text" id="fecha_vencimiento_licencia_manejo" class="valid Focus toDate center" size="10" maxlength="10" value="" readonly="readonly" /><br />
						<input name="doc_rfc" type="checkbox" id="doc_rfc" value="1" />
						R.F.C.<br />
						<input name="doc_no_adeudo_infonavit" type="checkbox" id="doc_no_adeudo_infonavit" value="1" />
						Carta de no adeudo a Infonavit
					</td>
				</tr>
				<tr class="linea_off">
					<th align="left" scope="row">Firma de contrato</th>
					<td><input name="firma_contrato" type="checkbox" id="firma_contrato" value="1" />
						Si</td>
					</tr>
			</table>
			<br />
			<p>
				<input name="generar" type="button" class="boton" id="generar" value="Generar contrato" />
			&nbsp;&nbsp;
			<input type="button" name="actualizar" id="actualizar" value="Actualizar informaci&oacute;n de empleado" />
			&nbsp;&nbsp;
			<input name="alta" type="button" disabled="disabled" id="alta" value="Alta de empleado" />
			</p>
		</form>
	</div>
</div>
<script language="javascript" type="text/javascript" src="menus/{menucnt}"></script>
</body>
</html>
