<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Rentas pagadas</title>
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
<script type="text/javascript" src="jscripts/ren/RentasPagadas.js"></script>
<script language="JavaScript" type="text/javascript" src="menus/stm31.js"></script>
</head>

<body>
<div id="contenedor">
	<div id="titulo">Rentas pagadas</div>
	<div id="captura" align="center">
		<form name="Datos" class="FormValidator FormStyles" id="Datos">
			<table class="tabla_captura">
				<tr class="linea_off">
					<th align="left" scope="row">Arrendado(es)</th>
					<td><input name="arrendadores" type="text" class="valid toInterval" id="arrendadores" size="30" /></td>
				</tr>
				<tr class="linea_on">
					<th align="left" scope="row">Arrendatario(s)</th>
					<td><input name="arrendatarios" type="text" class="valid toInterval" id="arrendatarios" size="30" /></td>
				</tr>
				<tr class="linea_off">
					<th align="left" scope="row">Categor&iacute;a</th>
					<td>
						<select name="categoria" id="categoria">
							<option value=""></option>
							<option value="1">CATEGORIA 1</option>
							<option value="2">CATEGORIA 2</option>
							<option value="3">CATEGORIA 3</option>
							<option value="4">CATEGORIA 4</option>
							<option value="5">CATEGORIA 5</option>
							<option value="6">CATEGORIA 6</option>
							<option value="7">CATEGORIA 7</option>
							<option value="8">CATEGORIA 8</option>
							<option value="9">CATEGORIA 9</option>
							<option value="10">CATEGORIA 10</option>
						</select>
					</td>
				</tr>
				<tr class="linea_on">
					<th align="left" scope="row">Año</th>
					<td><input name="anio" type="text" class="valid toPosInt center" id="anio" value="{anio}" size="4" maxlength="4" /></td>
				</tr>
				<tr class="linea_off">
					<th align="left" scope="row">Solo pendientes</th>
					<td><input name="solo_pendientes" type="checkbox" id="solo_pendientes" value="1" />
						Si</td>
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
