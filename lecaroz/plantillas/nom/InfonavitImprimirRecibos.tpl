<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Impresión de recibos de Infonavit</title>

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

<script type="text/javascript" src="jscripts/nom/InfonavitImprimirRecibos.js"></script>
</head>

<body>
<div id="contenedor">
	<div id="titulo">Impresión de recibos de Infonavit</div>
	<div id="captura" align="center">
		<form name="Datos" class="FormValidator FormStyles" id="Datos">
			<table class="tabla_captura">
				<tr class="linea_off">
					<th align="left" scope="row">Compañía(s)</th>
					<td><input name="cias" type="text" class="valid toInterval" id="cias" size="30" /></td>
				</tr>
				<tr class="linea_on">
					<th align="left" scope="row">Administrador</th>
					<td><select name="admin" id="admin">
						<option value="" selected="selected"></option>
						<!-- START BLOCK : admin -->
						<option value="{value}">{text}</option>
						<!-- END BLOCK : admin -->
					</select></td>
				</tr>
				<tr class="linea_off">
					<th align="left" scope="row">Folio(s)</th>
					<td><input name="folios" type="text" class="valid toInterval" id="folios" size="30" /></td>
				</tr>
				<tr class="linea_on">
					<th align="left" scope="row">Periodo de impresión</th>
					<td><input name="fecha1" type="text" class="valid Focus toDate center" id="fecha1" value="{fecha1}" size="10" maxlength="10" />
						al
						<input name="fecha2" type="text" class="valid Focus toDate center" id="fecha2" value="{fecha2}" size="10" maxlength="10" /></td>
				</tr>
				<tr class="linea_off">
					<th align="left" scope="row">Año</th>
					<td><input name="anio" type="text" class="valid Focus toPosInt center" id="anio" size="4" maxlength="4" /></td>
				</tr>
				<tr class="linea_on">
					<th align="left" scope="row">Mes</th>
					<td><select name="mes" id="mes">
						<option value="" selected="selected"></option>
						<option value="1">ENERO</option>
						<option value="2">FEBRERO</option>
						<option value="3">MARZO</option>
						<option value="4">ABRIL</option>
						<option value="5">MAYO</option>
						<option value="6">JUNIO</option>
						<option value="7">JULIO</option>
						<option value="8">AGOSTO</option>
						<option value="9">SEPTIEMBRE</option>
						<option value="10">OCTUBRE</option>
						<option value="11">NOVIEMBRE</option>
						<option value="12">DICIEMBRE</option>
					</select></td>
				</tr>
			</table>
			<p class="bold red">NOTA: Si no especifica ninguna condición se imprimiran o enviaran los últimos recibos generados.</p>
			<p>
				<input type="button" name="email" id="email" value="Enviar por correo electrónico" />
			&nbsp;&nbsp;
			<input type="button" name="imprimir" id="imprimir" value="Imprimir" />
			</p>
		</form>
	</div>
</div>
<script language="javascript" type="text/javascript" src="menus/{menucnt}"></script>
</body>
</html>
