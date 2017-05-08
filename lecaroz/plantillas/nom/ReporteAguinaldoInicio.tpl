<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Reporte de N&oacute;mina</title>

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

<script type="text/javascript" src="jscripts/nom/ReporteAguinaldoInicio.js"></script>
</head>

<body>
<div id="contenedor">
	<div id="titulo">Reporte de N&oacute;mina</div>
	<div id="captura" align="center">
		<form action="ReporteAguinaldo.php" method="post" enctype="multipart/form-data" name="Datos" class="FormValidator FormStyles" id="Datos">
			<table class="tabla_captura">
				<tr class="linea_off">
					<th align="left" scope="row">Compa&ntilde;&iacute;a</th>
					<td><input name="num_cia" type="text" class="valid Focus toPosInt right" id="num_cia" size="3" />
					<input name="nombre_cia" type="text" disabled="disabled" id="nombre_cia" size="40" /></td>
				</tr>
				<tr class="linea_on">
					<th align="left" scope="row">Año</th>
					<td><input name="anio" type="text" class="valid Focus toPosInt center" id="anio" value="{anio}" size="4" maxlength="4" /></td>
				</tr>
				<tr class="linea_off">
					<th align="left" scope="row">Archivo 1</th>
					<td><input name="archivo1" type="file" id="archivo1" style="width:98%;" /></td>
				</tr>
				<tr class="linea_on">
					<th align="left" scope="row">Archivo 2</th>
					<td><input name="archivo2" type="file" id="archivo2" style="width:98%;" /></td>
				</tr>
				<tr class="linea_off">
					<th align="left" scope="row">Archivo 3</th>
					<td><input name="archivo3" type="file" id="archivo3" /></td>
				</tr>
			</table>
			<p>
				<input type="button" name="siguiente" id="siguiente" value="Siguiente" />
			</p>
		</form>&nbsp;
	</div>
</div>
<script language="javascript" type="text/javascript" src="menus/{menucnt}"></script>
</body>
</html>
