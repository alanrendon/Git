<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Impresion de Etiquetas para Compa&ntilde;&iacute;as</title>

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
<script type="text/javascript" src="jscripts/fac/ImpresionEtiquetasCompanias.js"></script>
<script language="JavaScript" type="text/javascript" src="menus/stm31.js"></script>

</head>

<body>
<div id="contenedor">
	<div id="titulo">Impresi&oacute;n de Etiquetas para Compa&ntilde;&iacute;as</div>
	<div id="captura" align="center">
		<form action="" method="post" name="Datos" class="FormValidator FormStyles" id="Datos">
			<table class="tabla_captura">
				<tr class="linea_off">
					<th align="left" scope="row">Compa&ntilde;&iacute;a(s)</th>
					<td><input name="cias" type="text" class="valid toInterval" id="cias" size="40" /></td>
				</tr>
				<tr class="linea_on">
					<th align="left" scope="row">Tipo</th>
					<td><input name="campo" type="radio" id="campo" value="nombre_corto" checked="checked" />
						Nombre corto<br />
						<input type="radio" name="campo" id="campo" value="nombre" />
						Nombre completo</td>
				</tr>
				<tr class="linea_off">
					<th align="left" scope="row">Desde etiqueta</th>
					<td><input name="etiqueta" type="text" class="valid Focus toPosInt center" id="etiqueta" size="5" /></td>
				</tr>
				<tr class="linea_on">
					<th align="left" scope="row">N&uacute;mero de copias</th>
					<td><input name="copias" type="text" class="valid Focus toPosInt center" id="copias" size="3" /></td>
				</tr>
				<tr class="linea_off">
					<th align="left" scope="row">Intercalar copias</th>
					<td><input name="intercalar" type="checkbox" id="intercalar" value="1" />
						S&iacute;</td>
				</tr>
			</table>
			<p class="red bold">NOTA: las etiquetas deben ser tama&ntilde;o Avery #5160 (6.7 x 2.5 cm)</p>
			<p>
				<input name="buscar" type="button" id="buscar" value="Buscar Compa&ntilde;&iacute;as" />
			</p>
		</form>
		<div id="companias">
			
		</div>
	</div>
</div>
<script language="javascript" type="text/javascript" src="menus/{menucnt}"></script>
</body>
</html>