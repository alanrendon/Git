<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Consulta de ventas por día</title>
<link href="/lecaroz/smarty/styles/layout.css" rel="stylesheet" type="text/css" />
<link href="/lecaroz/smarty/styles/tablas.css" rel="stylesheet" type="text/css" />
<link href="/lecaroz/styles/font-style.css" rel="stylesheet" type="text/css" />
<link href="/lecaroz/styles/FormValidator.css" rel="stylesheet" type="text/css" />
<link href="/lecaroz/styles/FormStyles.css" rel="stylesheet" type="text/css" />
<link href="/lecaroz/styles/Popups.css" rel="stylesheet" type="text/css" />
<link href="styles/calendar.css" rel="stylesheet" type="text/css" />

<script type="text/javascript" src="jscripts/mootools/mootools-1.2-core.js"></script>
<script type="text/javascript" src="jscripts/mootools/mootools-1.2-more.js"></script>
<script type="text/javascript" src="jscripts/mootools/String.implement.js"></script>
<script type="text/javascript" src="jscripts/mootools/Number.implement.js"></script>
<script type="text/javascript" src="jscripts/mootools/FormValidator.js"></script>
<script type="text/javascript" src="jscripts/mootools/FormStyles.js"></script>
<script type="text/javascript" src="jscripts/mootools/Calendar.js"></script>
<script type="text/javascript" src="jscripts/mootools/Popups.js"></script>
<script type="text/javascript" src="jscripts/pan/VentasConsultaDia.js"></script>
<script language="JavaScript" type="text/javascript" src="menus/stm31.js"></script>
</head>

<body>
<div id="contenedor">
	<div id="titulo">Consulta de ventas por día</div>
	<div id="captura" align="center">
		<!-- START BLOCK : normal -->
		<form action="" method="post" name="Datos" class="FormValidator FormStyles" id="Datos">
			<table class="tabla_captura">
				<tr class="linea_off">
					<th align="left" scope="row">Compañía(s)</th>
					<td><input name="cias" type="text" class="valid toInterval" id="cias" size="30" /></td>
				</tr>
				<tr class="linea_on">
					<th align="left" scope="row">Administrador</th>
					<td><select name="admin" id="admin">
						<option value="" selected="selected"></option>
						<!-- START BLOCK : admin_1 -->
						<option value="{value}">{text}</option>
						<!-- END BLOCK : admin_1 -->
					</select></td>
				</tr>
				<tr class="linea_off">
					<th align="left" scope="row">Dia</th>
					<td><input name="fecha" type="text" class="valid Focus toDate center" id="fecha" value="{fecha}" size="10" maxlength="10" /></td>
				</tr>
			</table>
			<p>
				<input type="button" name="reporte" id="reporte" value="Reporte" />
			</p>
		</form>
		<!-- END BLOCK : normal -->
		<!-- START BLOCK : ipad -->
		<form action="" method="post" name="Datos" class="FormValidator FormStyles" id="Datos">
			<table class="tabla_captura">
				<tr class="linea_off">
					<th align="left" scope="row">Compañía</th>
					<td><select name="cias" id="cias">
						<option value="" selected="selected"></option>
						<!-- START BLOCK : cia -->
						<option value="{value}">{value} {text}</option>
						<!-- END BLOCK : cia -->
					</select></td>
				</tr>
				<tr class="linea_on">
					<th align="left" scope="row">Administrador</th>
					<td><select name="admin" id="admin">
						<option value="" selected="selected"></option>
						<!-- START BLOCK : admin_2 -->
						<option value="{value}">{text}</option>
						<!-- END BLOCK : admin_2 -->
					</select></td>
				</tr>
				<tr class="linea_off">
					<th align="left" scope="row">Dia</th>
					<td><input name="fecha" type="text" class="valid Focus toDate center" id="fecha" value="{fecha}" size="10" maxlength="10" /></td>
				</tr>
			</table>
			<p>
				<input type="button" name="reporte" id="reporte" value="Reporte" />
			</p>
		</form>
		<!-- END BLOCK : ipad -->
	</div>
</div>
<script language="javascript" type="text/javascript" src="menus/{menucnt}"></script>
</body>
</html>