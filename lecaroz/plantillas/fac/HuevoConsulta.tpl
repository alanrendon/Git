<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Consulta de remisiones de huevo</title>

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

<script type="text/javascript" src="jscripts/fac/HuevoConsulta.js"></script>
</head>

<body>
<div id="contenedor">
	<div id="titulo">Consulta de remisiones de huevo</div>
	<div id="captura" align="center">
		<form action="" method="post" name="Datos" class="FormValidator FormStyles" id="Datos">
			<table class="tabla_captura">
				<tr class="linea_off">
					<th align="left" scope="row">Compañía(s)</th>
					<td><input name="cias" type="text" class="valid toInterval" id="cias" size="30" /></td>
				</tr>
				<tr class="linea_on">
					<th align="left" scope="row">Proveedor</th>
					<td><select name="num_pro" id="num_pro">
						<option value=""></option>
						<!-- START BLOCK : pro -->
						<option value="{value}">{text}</option>
						<!-- END BLOCK : pro -->
					</select></td>
				</tr>
				<tr class="linea_off">
					<th align="left" scope="row">Periodo</th>
					<td><input name="fecha1" type="text" class="valid Focus toDate center" id="fecha1" value="{fecha1}" size="10" maxlength="10" />
						al
						<input name="fecha2" type="text" class="valid Focus toDate center" id="fecha2" value="{fecha2}" size="10" maxlength="10" /></td>
				</tr>
				<tr class="linea_on">
					<th align="left" scope="row">Remisión</th>
					<td><input name="num_rem" type="text" class="valid onlyNumbersAndLetters" id="num_rem" size="30" /></td>
				</tr>
				<tr class="linea_off">
					<th align="left" scope="row">Factura</th>
					<td><input name="num_fact" type="text" class="valid onlyNumbersAndLetters" id="num_fact" size="30" /></td>
				</tr>
				<tr class="linea_on">
					<th align="left" scope="row">Incluir</th>
					<td><input name="pendientes" type="checkbox" id="pendientes" value="1" checked="checked" />
						Pendientes<br />
						<input name="asociadas" type="checkbox" id="asociadas" value="1" checked="checked" />
						Asociadas</td>
				</tr>
				<tr class="linea_off">
					<th align="left" scope="row">Ordenar por</th>
					<td><input name="orden" type="radio" id="radio" value="cia" checked="checked" />
						Compañía<br />
						<input type="radio" name="orden" id="radio2" value="pro" />
						Proveedor</td>
				</tr>
			</table>
			<p>
				<input type="button" name="consultar" id="consultar" value="Consultar" />
			</p>
		</form>
	</div>
</div>
<script language="javascript" type="text/javascript" src="menus/{menucnt}"></script>
</body>
</html>
