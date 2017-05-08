<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Remisiones de fruta</title>

<link href="/lecaroz/smarty/styles/layout.css" rel="stylesheet" type="text/css" />
<link href="/lecaroz/smarty/styles/tablas.css" rel="stylesheet" type="text/css" />
<link href="/lecaroz/styles/font-style.css" rel="stylesheet" type="text/css" />
<link href="/lecaroz/styles/FormValidator.css" rel="stylesheet" type="text/css" />
<link href="/lecaroz/styles/FormStyles.css" rel="stylesheet" type="text/css" />
<link href="/lecaroz/styles/Popups.css" rel="stylesheet" type="text/css" />
<link href="/lecaroz/styles/Tips.css" rel="stylesheet" type="text/css" />

<script type="text/javascript" src="jscripts/mootools/mootools-1.2-core.js"></script>
<script type="text/javascript" src="jscripts/mootools/mootools-1.2-more.js"></script>
<script type="text/javascript" src="jscripts/mootools/String.implement.js"></script>
<script type="text/javascript" src="jscripts/mootools/Number.implement.js"></script>
<script type="text/javascript" src="jscripts/mootools/Array.implement.js"></script>
<script type="text/javascript" src="jscripts/mootools/FormValidator.js"></script>
<script type="text/javascript" src="jscripts/mootools/FormStyles.js"></script>
<script type="text/javascript" src="jscripts/mootools/Popups.js"></script>
<script type="text/javascript" src="menus/stm31.js"></script>

<script type="text/javascript" src="jscripts/fac/FrutaCapturaRemisiones.js"></script>
</head>

<body>
<div id="contenedor">
	<div id="titulo">Remisiones de fruta</div>
	<div id="captura" align="center">
		<form name="Datos" class="FormValidator FormStyles" id="Datos">
			<p class="bold font12">Solo puede capturar remisiónes de los productos 179 FRUTA y 291 FRESA NATURAL</p>
			<table class="tabla_captura">
				<tr>
					<th scope="col">Compañía</th>
					<th scope="col">Producto</th>
					<th scope="col"> Proveedor</th>
					<th scope="col">Remisión</th>
					<th scope="col">Fecha</th>
					<th scope="col">Cantidad</th>
					<th scope="col">Precio</th>
					<th scope="col">Total</th>
				</tr>
				<tbody id="tbody">
					<tr class="linea_off">
						<td align="center" nowrap="nowrap"><input name="num_cia[]" type="text" class="valid Focus toPosInt center" id="num_cia" size="3" /><input name="nombre_cia[]" type="text" disabled="disabled" id="nombre_cia" size="30" /></td>
						<td align="center" nowrap="nowrap"><input name="codmp[]" type="text" class="valid Focus toPosInt center" id="codmp" size="3" /><input name="nombre_mp[]" type="text" disabled="disabled" id="nombre_mp" size="15" /></td>
						<td align="center"><select name="num_pro[]" id="num_pro">
						</select></td>
						<td align="center"><input name="num_rem[]" type="text" class="valid Focus onlyNumbersAndLetters toUpper" id="num_rem" size="10" /></td>
						<td align="center"><input name="fecha[]" type="text" class="valid Focus toDate center" id="fecha" size="10" maxlength="10" /></td>
						<td align="center"><input name="cantidad[]" type="text" class="valid Focus numberPosFormat right" precision="2" id="cantidad" size="8" /></td>
						<td align="center"><input name="precio[]" type="text" class="valid Focus numberPosFormat right" precision="2" id="precio" size="8" /></td>
						<td align="center"><input name="total[]" type="text" class="valid Focus numberPosFormat right" precision="2" id="total" size="10" /></td>
					</tr>
				</tbody>
			</table>
			<p>
				<input type="button" name="ingresar" id="ingresar" value="Ingresar remisiones" />
			</p>
		</form>
		<div id="result"></div>
	</div>
</div>
<script language="javascript" type="text/javascript" src="menus/{menucnt}"></script>
</body>
</html>
