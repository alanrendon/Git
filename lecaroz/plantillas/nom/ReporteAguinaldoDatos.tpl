<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Reporte de Aguinaldos</title>

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

<script type="text/javascript" src="jscripts/nom/ReporteAguinaldoDatos.js"></script>
</head>

<body>
<div id="contenedor">
	<div id="titulo">Reporte de Aguinaldos</div>
	<div id="captura" align="center">
		<table class="tabla_captura">
			<tr class="linea_off">
				<th align="left" scope="row">Compa&ntilde;&iacute;a</th>
				<td class="bold font12">{num_cia} {nombre_cia}</td>
			</tr>
			<tr class="linea_on">
				<th align="left" scope="row">R.F.C.</th>
				<td class="bold font12">{rfc_cia}</td>
			</tr>
			<tr class="linea_off">
				<th align="left" scope="row">I.M.S.S.</th>
				<td class="bold font12">{no_imss}</td>
			</tr>
			<tr class="linea_on">
				<th align="left" scope="row">Año</th>
				<td class="bold font12">{anio}</td>
			</tr>
			<tr class="linea_off">
				<th align="left" scope="row">Empleados</th>
				<td class="bold font12">{empleados}</td>
			</tr>
		</table>
		<br />
		<table class="tabla_captura">
			<tr>
				<th rowspan="2" scope="col">No.</th>
				<th rowspan="2" scope="col">Nombre del empleado</th>
				<th rowspan="2" scope="col">Puesto</th>
				<th rowspan="2" nowrap="nowrap" scope="col">Fecha de<br />
				ingreso</th>
				<th rowspan="2" scope="col">CURP</th>
				<th rowspan="2" scope="col">Afiliaci&oacute;n<br />
				I.M.S.S.</th>
				<th rowspan="2" scope="col">Horario</th>
				<th colspan="7" scope="col">Percepciones</th>
				<th colspan="6" scope="col">Deducciones</th>
				<th rowspan="2" nowrap="nowrap" scope="col">Neto a<br />
				Pagar</th>
			</tr>
			<tr>
				<th scope="col">S.D.</th>
				<th scope="col">S.D.I.</th>
				<th scope="col">Sueldo</th>
				<th scope="col">P.D.</th>
				<th scope="col">V</th>
				<th scope="col">P.V.</th>
				<th scope="col">Total</th>
				<th scope="col">I.S.R.</th>
				<th nowrap="nowrap" scope="col">Subsidio<br />
				al empleo</th>
				<th scope="col">Cr&eacute;dito<br />
				Infonavit</th>
				<th scope="col">Pensi&oacute;n<br />
				alimen.</th>
				<th scope="col">I.M.S.S.</th>
				<th scope="col">Total</th>
			</tr>
			<!-- START BLOCK : row -->
			<tr class="linea_{row_color}" id="row"{error}>
				<td align="right">{clave}</td>
				<td nowrap="nowrap">{nombre}</td>
				<td nowrap="nowrap">{puesto}</td>
				<td>{fecha_alta}</td>
				<td>{curp}</td>
				<td>{num_afiliacion}</td>
				<td align="center" nowrap="nowrap">{horario}</td>
				<td align="right" class="blue">{salario_diario}</td>
				<td align="right" class="blue">{salario_integrado}</td>
				<td align="right" class="blue">{sueldo_semanal}</td>
				<td align="right" class="blue">{prima_dominical}</td>
				<td align="center" class="blue">{vacaciones}</td>
				<td align="right" class="blue">{prima_vacacional}</td>
				<td align="right" class="blue bold">{total_percepciones}</td>
				<td align="right" class="red">{isr}</td>
				<td align="right" class="red">{subsidio_al_empleo}</td>
				<td align="right" class="red">{credito_infonavit}</td>
				<td align="right" class="red">{pension_alimenticia}</td>
				<td align="right" class="red">{imss}</td>
				<td align="right" class="red bold">{total_deducciones}</td>
				<td align="right" class="green bold">{total}</td>
			</tr>
			<!-- END BLOCK : row -->
			<tr>
				<th colspan="7" align="right">Total</th>
				<th align="right" class="blue">{salario_diario}</th>
				<th align="right" class="blue">{salario_integrado}</th>
				<th align="right" class="blue">{sueldo_semanal}</th>
				<th align="right" class="blue">{prima_dominical}</th>
				<th align="right" class="blue">&nbsp;</th>
				<th align="right" class="blue">{prima_vacacional}</th>
				<th align="right" class="blue">{total_percepciones}</th>
				<th align="right" class="red">{isr}</th>
				<th align="right" class="red">{subsidio_al_empleo}</th>
				<th align="right" class="red">{credito_infonavit}</th>
				<th align="right" class="red">{pension_alimenticia}</th>
				<th align="right" class="red">{imss}</th>
				<th align="right" class="red">{total_deducciones}</th>
				<th align="right">{total}</th>
			</tr>
		</table>
		&nbsp;
		<input type="button" name="cancelar" id="cancelar" value="Cancelar" />
	&nbsp;&nbsp;
	<input type="button" name="registrar" id="registrar" value="Registrar datos"{disabled} />
	</div>
</div>
<script language="javascript" type="text/javascript" src="menus/{menucnt}"></script>
</body>
</html>
