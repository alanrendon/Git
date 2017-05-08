<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Alta de trabajador</title>

<link href="/lecaroz/smarty/styles/layout.css" rel="stylesheet" type="text/css" />
<link href="/lecaroz/smarty/styles/tablas.css" rel="stylesheet" type="text/css" />
<link href="/lecaroz/styles/font-style.css" rel="stylesheet" type="text/css" />
<link href="/lecaroz/styles/FormValidator.css" rel="stylesheet" type="text/css" />
<link href="/lecaroz/styles/FormStyles.css" rel="stylesheet" type="text/css" />
<link href="/lecaroz/styles/Popups.css" rel="stylesheet" type="text/css" />

<script type="text/javascript" src="jscripts/mootools/mootools-1.2-core.js"></script>
<script type="text/javascript" src="jscripts/mootools/mootools-1.2-more.js"></script>
<script type="text/javascript" src="jscripts/mootools/String.implement.js"></script>
<script type="text/javascript" src="jscripts/mootools/Number.implement.js"></script>
<script type="text/javascript" src="jscripts/mootools/FormValidator.js"></script>
<script type="text/javascript" src="jscripts/mootools/FormStyles.js"></script>
<script type="text/javascript" src="jscripts/mootools/Popups.js"></script>
<script type="text/javascript" src="menus/stm31.js"></script>

<script type="text/javascript" src="jscripts/nom/TrabajadoresAlta.js"></script>
</head>

<body>
<div id="contenedor">
	<div id="titulo">Alta de trabajador</div>
	<div id="captura" align="center">
		<form name="Datos" class="FormValidator FormStyles" id="Datos">
			<table class="tabla_captura">
				<tr class="linea_off">
					<th align="left" scope="row">Compa&ntilde;&iacute;a</th>
					<td><input name="num_cia" type="text" class="valid Focus toPosInt center" id="num_cia" size="3" />
					<input name="nombre_cia" type="text" disabled="disabled" id="nombre_cia" size="40" /></td>
				</tr>
				<tr class="linea_on">
					<th align="left" scope="row">Labora en</th>
					<td><input name="num_cia_emp" type="text" class="valid Focus toPosInt center" id="num_cia_emp" size="3" />
					<input name="nombre_cia_emp" type="text" disabled="disabled" id="nombre_cia_emp" size="40" /></td>
				</tr>
				<tr>
					<td colspan="2" align="left" scope="row">&nbsp;</td>
				</tr>
				<tr>
					<th colspan="2" align="left" scope="row"><img src="/lecaroz/imagenes/info.png" width="16" height="16" /> Informaci&oacute;n general</th>
				</tr>
				<tr class="linea_off">
					<th align="left" scope="row">Nombre</th>
					<td><input name="nombre" type="text" class="valid onlyText cleanText toUpper" id="nombre" size="20" maxlength="50" /></td>
				</tr>
				<tr class="linea_on">
					<th align="left" scope="row">Apellido paterno</th>
					<td><input name="ap_paterno" type="text" class="valid onlyText cleanText toUpper" id="ap_paterno" size="20" maxlength="50" /></td>
				</tr>
				<tr class="linea_off">
					<th align="left" scope="row">Apellido materno</th>
					<td><input name="ap_materno" type="text" class="valid onlyText cleanText toUpper" id="ap_materno" size="20" maxlength="50" /></td>
				</tr>
				<tr class="linea_on">
					<th align="left" scope="row">R.F.C.</th>
					<td><input name="rfc" type="text" class="valid toRFCopcional toUpper" id="rfc" size="13" maxlength="13" /></td>
				</tr>
				<tr class="linea_on">
					<th align="left" class="linea_off" scope="row">C.U.R.P.</th>
					<td class="linea_off">
						<input name="curp" type="text" class="valid toCURP toUpper" id="curp" size="18" maxlength="18" />
					</td>
				</tr>
				<tr class="linea_on">
					<th align="left" scope="row">Fecha de nacimiento</th>
					<td>
						<input name="fecha_nac" type="text" class="valid Focus toDate center" id="fecha_nac" size="10" maxlength="10" />
					</td>
				</tr>
				<tr class="linea_off">
					<th align="left" scope="row">Lugar de nacimiento</th>
					<td>
						<input name="lugar_nac" type="text" class="valid toText cleanText toUpper" id="lugar_nac" size="20" maxlength="25" />
					</td>
				</tr>
				<tr class="linea_on">
					<th align="left" scope="row">Sexo</th>
					<td>
						<input name="sexo" type="radio" id="sexo" value="FALSE" checked="checked" />
						Hombre
						<input type="radio" name="sexo" id="sexo" value="TRUE" />
						Mujer</td>
				</tr>
				<tr>
					<th colspan="2" align="left" scope="row"><img src="/lecaroz/imagenes/info.png" width="16" height="16" /> Domicilio y contacto</th>
				</tr>
				<tr class="linea_off">
					<th align="left" scope="row">Calle y n&uacute;mero</th>
					<td><input name="calle" type="text" class="valid toText cleanText toUpper" id="calle" size="40" maxlength="50" /></td>
				</tr>
				<tr class="linea_on">
					<th align="left" scope="row">Colonia</th>
					<td><input name="colonia" type="text" class="valid toText cleanText toUpper" id="colonia" size="40" maxlength="40" /></td>
				</tr>
				<tr class="linea_off">
					<th align="left" scope="row">Delegaci&oacute;n o municipio</th>
					<td><input name="del_mun" type="text" class="valid toText cleanText toUpper" id="del_mun" size="40" maxlength="40" /></td>
				</tr>
				<tr class="linea_on">
					<th align="left" scope="row">Entidad</th>
					<td><input name="entidad" type="text" class="valid toText cleanText toUpper" id="entidad" size="40" maxlength="30" /></td>
				</tr>
				<tr class="linea_off">
					<th align="left" scope="row">C&oacute;digo postal</th>
					<td><input name="cod_postal" type="text" class="valid onlyNumbers" id="cod_postal" size="5" maxlength="5" /></td>
				</tr>
				<tr class="linea_on">
					<th align="left" scope="row">Tel&eacute;fono de casa</th>
					<td><input name="telefono_casa" type="text" class="valid toPhoneNumber" id="telefono_casa" size="20" maxlength="20" /></td>
				</tr>
				<tr class="linea_off">
					<th align="left" scope="row">Tel&eacute;fono movil</th>
					<td><input name="telefono_movil" type="text" class="valid toPhoneNumber" id="telefono_movil" size="20" maxlength="20" /></td>
				</tr>
				<tr class="linea_on">
					<th align="left" scope="row">Correo electr&oacute;nico</th>
					<td><input name="email" type="text" class="valid toEmail" id="email" size="40" maxlength="200" /></td>
				</tr>
				<tr>
					<td colspan="2" align="left" scope="row">&nbsp;</td>
				</tr>
				<tr>
					<th colspan="2" align="left" scope="row"><img src="/lecaroz/imagenes/info.png" width="16" height="16" /> Informaci&oacute;n laboral</th>
				</tr>
				<tr class="linea_off">
					<th align="left" scope="row">Fecha de ingreso</th>
					<td><input name="fecha_alta" type="text" class="valid Focus toDate center" id="fecha_alta" value="{fecha}" size="10" maxlength="10" /></td>
				</tr>
				<tr class="linea_on">
					<th align="left" scope="row">Puesto</th>
					<td><select name="cod_puestos" id="cod_puestos">
					</select></td>
				</tr>
				<tr class="linea_off">
					<th align="left" scope="row">Turno</th>
					<td><select name="cod_turno" id="cod_turno">
					</select></td>
				</tr>
				<tr class="linea_on">
					<th align="left" scope="row">Horario</th>
					<td><select name="cod_horario" id="cod_horario">
					</select></td>
				</tr>
				<tr class="linea_off">
					<th align="left" scope="row">Salario diario</th>
					<td><input name="salario" type="text" class="valid Focus numberPosFormat right" precision="2" id="salario" size="10" /></td>
				</tr>
				<tr class="linea_on">
					<th align="left" scope="row">Salario diario integrado</th>
					<td><input name="salario_integrado" type="text" class="valid Focus numberPosFormat right" precision="2" id="salario_integrado" size="10" /></td>
				</tr>
				<tr class="linea_off">
					<th align="left" scope="row">Fecha de alta en I.M.S.S.</th>
					<td><input name="fecha_alta_imss" type="text" class="valid Focus toDate center" id="fecha_alta_imss" size="10" maxlength="10" /></td>
				</tr>
				<tr class="linea_on">
					<th align="left" scope="row">Trabajador permanente</th>
					<td><input name="no_baja" type="checkbox" id="no_baja" value="TRUE" />
						Si</td>
				</tr>
				<tr class="linea_off">
					<th align="left" scope="row">N&uacute;mero de seguro social</th>
					<td><input name="num_afiliacion" type="text" class="valid onlyNumbers" id="num_afiliacion" size="11" maxlength="11" /></td>
				</tr>
				<tr class="linea_on">
					<th align="left" scope="row">Cr&eacute;dito Infonavit</th>
					<td><input name="credito_infonavit" type="checkbox" id="credito_infonavit" value="TRUE" />
					Si</td>
				</tr>
				<tr class="linea_off">
					<th align="left" scope="row">N&uacute;mero de cr&eacute;dito Infonavit</th>
					<td><input name="no_infonavit" type="text" class="valid onlyNumbers" id="no_infonavit" size="11" maxlength="10" /></td>
				</tr>
				<tr class="linea_on">
					<th align="left" scope="row">Recibe aguinaldo</th>
					<td><input name="solo_aguinaldo" type="checkbox" id="solo_aguinaldo" value="TRUE" />
						Si</td>
				</tr>
				<tr class="linea_off">
					<th align="left" scope="row">Tipo de c&aacute;lculo para aguinaldo</th>
					<td><select name="tipo" id="tipo">
						<option value="0">NORMAL</option>
						<option value="1">A 1 A&Ntilde;O</option>
						<option value="2">A 3 MESES</option>
					</select></td>
				</tr>
				<tr class="linea_on">
					<th align="left" scope="row">Último aguinaldo</th>
					<td><input name="aguinaldo" type="text" class="valid Focus numberPosFormat right" precision="2" id="aguinaldo" size="10" /></td>
				</tr>
				<tr class="linea_off">
					<th align="left" scope="row">Observaciones</th>
					<td><textarea name="observaciones" cols="45" rows="5" class="valid toText toUpper" id="observaciones"></textarea></td>
				</tr>
				<tr>
					<td colspan="2" align="left" scope="row">&nbsp;</td>
				</tr>
				<tr>
					<th colspan="2" align="left" scope="row"><img src="../../imagenes/info.png" width="16" height="16" /> Otros</th>
				</tr>
				<tr class="linea_off">
					<th align="left" scope="row">Fecha de entrega de uniforme</th>
					<td><input name="uniforme" type="text" class="valid Focus toDate center" id="uniforme" size="10" maxlength="10" /></td>
				</tr>
				<tr class="linea_on">
					<th align="left" scope="row">Talla</th>
					<td><select id="talla" class="insert" name="talla">
						<option selected="" value=""></option>
						<option value="1">CHICA</option>
						<option value="2">MEDIANA</option>
						<option value="3">GRANDE</option>
						<option value="4">EXTRA GRANDE</option>
					</select></td>
				</tr>
				<tr class="linea_off">
					<th align="left" scope="row">Dep&oacute;sito por bata</th>
					<td><input name="control_bata" type="checkbox" id="control_bata" value="TRUE" />
					Si</td>
				</tr>
				<tr class="linea_on">
					<th align="left" scope="row">Monto de dep&oacute;sito</th>
					<td><input name="deposito_bata" type="text" class="valid Focus numberPosFormat right" precision="2" id="deposito_bata" size="10" /></td>
				</tr>
			</table>
			<p>
				<input type="button" name="borrar" id="borrar" value="Borrar informaci&oacute;n" />
				&nbsp;&nbsp;
				<input type="button" name="alta" id="alta" value="Alta de trabajador" />
			</p>
		</form>
	</div>
</div>
<script language="javascript" type="text/javascript" src="menus/{menucnt}"></script>
</body>
</html>
