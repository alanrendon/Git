<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Reporte de bajas de trabajadores</title>
<link href="/lecaroz/smarty/styles/layout.css" rel="stylesheet" type="text/css" />
<link href="/lecaroz/styles/table_layout.css" rel="stylesheet" type="text/css" />
<link href="/lecaroz/styles/font-style.css" rel="stylesheet" type="text/css" />
<link href="/lecaroz/styles/FormValidator2.0.css" rel="stylesheet" type="text/css" />
<link href="/lecaroz/styles/Tips.css" rel="stylesheet" type="text/css" />
<link href="/lecaroz/styles/mbox/mBoxCore.css" rel="stylesheet" type="text/css" />
<link href="/lecaroz/styles/mbox/mBoxModal.css" rel="stylesheet" type="text/css" />
<link href="/lecaroz/styles/mbox/mBoxTooltip.css" rel="stylesheet" type="text/css" />
<link href="/lecaroz/styles/calendar.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="menus/stm31.js"></script>
<script type="text/javascript" src="/lecaroz/jscripts/mootools1.4/mootools-core-1.4.5-compat.js"></script>
<script type="text/javascript" src="/lecaroz/jscripts/mootools1.4/mootools-more-1.4.0.1.js"></script>
<script type="text/javascript" src="/lecaroz/jscripts/mootools1.4/string.implement.js"></script>
<script type="text/javascript" src="/lecaroz/jscripts/mootools1.4/number.implement.js"></script>
<script type="text/javascript" src="/lecaroz/jscripts/mootools1.4/array.implement.js"></script>
<script type="text/javascript" src="/lecaroz/jscripts/mbox/mBox.Core.js"></script>
<script type="text/javascript" src="/lecaroz/jscripts/mbox/mBox.Modal.js"></script>
<script type="text/javascript" src="/lecaroz/jscripts/mbox/mBox.Modal.Confirm.js"></script>
<script type="text/javascript" src="/lecaroz/jscripts/mbox/mBox.Tooltip.js"></script>
<script type="text/javascript" src="/lecaroz/jscripts/mootools1.4/FormValidator.js"></script>
<script type="text/javascript" src="/lecaroz/jscripts/mootools1.4/Calendar.js"></script>
<script type="text/javascript" src="/lecaroz/jscripts/nom/TrabajadoresBajasReporte.js"></script>
</head>

<body>
<div id="contenedor">
	<div id="titulo">Reporte de bajas de trabajadores</div>
	<div id="captura" align="center">
		<form name="inicio" class="FormValidator" id="inicio">
			<table class="table">
				<thead>
					<tr>
						<th colspan="2" align="left" scope="row">&nbsp;</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td class="bold" scope="row">Compa&ntilde;&iacute;a(s)</td>
						<td><input name="cias" type="text" class="validate toInterval" id="cias" size="30" /></td>
					</tr>
					<tr>
						<td class="bold" scope="row">Administrador</td>
						<td><select name="admin" id="admin">
								<option value=""></option>
								<!-- START BLOCK : admin -->
								<option value="{value}">{text}</option>
								<!-- END BLOCK : admin -->
							</select></td>
					</tr>
					<tr>
						<td class="bold" scope="row">Periodo de baja</td>
						<td>
							<input name="fecha1" type="text" class="validate focus toDate center" id="fecha1" value="{fecha1}" size="10" maxlength="10" />
							al
							<input name="fecha2" type="text" class="validate focus toDate center" id="fecha2" value="{fecha2}" size="10" maxlength="10" />
						</td>
					</tr>
					<tr>
						<td class="bold" scope="row">Tipos</td>
						<td>
							<input name="sin_definir" type="checkbox" id="sin_definir" value="NULL" checked="checked" /><span class="orange">SIN DEFINIR</span>
							<!-- START BLOCK : tipo -->
							<br /><input name="tipo[]" type="checkbox" id="tipo" value="{tipo}" checked="checked" />{descripcion}
							<!-- END BLOCK : tipo -->
						</td>
					</tr>
				</tbody>
				<tfoot>
					<tr>
						<th colspan="2" align="left" scope="row">&nbsp;</th>
					</tr>
				</tfoot>
			</table>
			<p>
				<input name="consultar" type="button" class="boton" id="consultar" value="Generar reporte impreso" />
			</p>
		</form>
	</div>
</div>
<script language="javascript" type="text/javascript" src="/lecaroz/menus/{menucnt}"></script>
</body>
</html>