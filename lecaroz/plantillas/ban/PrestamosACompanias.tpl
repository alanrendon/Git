<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Prestamos a Compañías</title>

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

<script type="text/javascript" src="jscripts/ban/PrestamosACompanias.js"></script>
</head>

<body>
<div id="contenedor">
	<div id="titulo">Prestamos a Compañías</div>
	<div id="captura" align="center">
		<form action="" method="get" name="Datos" class="FormValidator FormStyles" id="Datos">
			<table class="tabla_captura">
				<tr>
					<th scope="col">Compañía</th>
					<th scope="col">Fecha</th>
					<th scope="col">Importe</th>
				</tr>
				<tr>
					<td align="center"><input name="num_cia[]" type="text" class="valid Focus toPosInt center" id="num_cia" size="3" />
					<input name="nombre_cia" type="text" disabled="disabled" id="nombre_cia[]" size="30" /></td>
					<td align="center"><input name="fecha[]" type="text" class="valid Focus toDate center" id="fecha" size="10" maxlength="10" /></td>
					<td align="center"><input name="importe[]" type="text" class="valid Focus numberPosFormat right" precision="2" id="importe" size="10" /></td>
				</tr>
			</table>
			<p>
				<input type="submit" name="registrar" id="registrar" value="Registrar" />
			</p>
		</form>
		&nbsp;
	</div>
</div>
<script language="javascript" type="text/javascript" src="menus/{menucnt}"></script>
</body>
</html>
