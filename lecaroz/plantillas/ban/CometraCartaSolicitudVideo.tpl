<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Carta de solicitud de video a Cometra</title>

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
<script type="text/javascript" src="jscripts/mootools/Array.implement.js"></script>
<script type="text/javascript" src="jscripts/mootools/FormValidator.js"></script>
<script type="text/javascript" src="jscripts/mootools/FormStyles.js"></script>
<script type="text/javascript" src="jscripts/mootools/Popups.js"></script>
<script type="text/javascript" src="jscripts/ban/CometraCartaSolicitudVideo.js"></script>
<script language="JavaScript" type="text/javascript" src="menus/stm31.js"></script>
</head>

<body>
<div id="contenedor">
	<div id="titulo">Carta de solicitud de video a Cometra</div>
	<div id="captura" align="center">
		<form name="Datos" class="FormValidator FormStyles" id="Datos">
			<table class="tabla_captura">
				<tr class="linea_off">
					<th align="left" scope="row">Compa&ntilde;&iacute;a</th>
					<td><input name="num_cia" type="text" class="valid Focus toPosInt center" id="num_cia" size="3" />
					<input name="nombre_cia" type="text" disabled="disabled" id="nombre_cia" size="30" /></td>
				</tr>
				<tr class="linea_on">
					<th align="left" scope="row">Tipo</th>
					<td><input name="tipo" type="radio" id="radio" value="Faltante" checked="checked" />
						Faltante
						<input type="radio" name="tipo" id="radio2" value="Sobrante" />
						Sobrante
						<input type="radio" name="tipo" id="radio3" value="Falso" />
						Falso</td>
				</tr>
				<tr class="linea_off">
					<th align="left" scope="row">Importe</th>
					<td><input name="importe" type="text" class="valid Focus numberPosFormat right" precision="2" id="importe" size="10" /></td>
				</tr>
				<tr class="linea_on">
					<th align="left" scope="row">Comprobante</th>
					<td><input name="comprobante" type="text" class="valid Focus onlyNumbers" id="comprobante" size="10" /></td>
				</tr>
				<tr class="linea_off">
					<th align="left" scope="row">Fecha</th>
					<td><input name="fecha" type="text" class="valid Focus toDate center" id="fecha" size="10" maxlength="10" /></td>
				</tr>
				<tr class="linea_on">
					<th align="left" scope="row">Encargado</th>
					<td><input name="encargado" type="text" class="valid onlyText cleanText toUpper" id="encargado" size="60" maxlength="200" /></td>
				</tr>
			</table>
			<br />
			<table class="tabla_captura">
				<tr>
					<th scope="col">Documentos</th>
				</tr>
				<tr>
					<td align="center"><div id="documentos" style="margin:8px 8px;">No hay documentos<br />
						escaneados</div>
						<div>
							<input type="button" name="scan" id="scan" value="Escanear" />
						</div></td>
				</tr>
			</table>
			<p>
				<input type="button" name="generar" id="generar" value="Generar carta" />
			</p>
		</form>
		<iframe name="hidden_data" id="hidden_data" style="display:none;"></iframe>
	</div>
</div>
<script language="javascript" type="text/javascript" src="menus/{menucnt}"></script>
</body>
</html>