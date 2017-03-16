<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Remisiones de huevo</title>

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

<script type="text/javascript" src="jscripts/fac/HuevoCapturaRemisiones.js"></script>
</head>

<body>
<div id="contenedor">
	<div id="titulo">Remisiones de huevo</div>
	<div id="captura" align="center">
		<form name="Datos" class="FormValidator FormStyles" id="Datos">
			<table class="tabla_captura">
				<tr class="linea_off">
					<th align="left" scope="row">Compañía</th>
					<td><input name="num_cia" type="text" class="valid Focus toPosInt center" id="num_cia" size="3" />
					<input name="nombre_cia" type="text" disabled="disabled" id="nombre_cia" size="40" /></td>
				</tr>
				<tr class="linea_on">
					<th align="left" scope="row">Fecha de remisión</th>
					<td><input name="fecha" type="text" class="valid Focus toDate center" id="fecha" size="10" maxlength="10" /></td>
				</tr>
				<tr class="linea_off">
					<th align="left" scope="row">Fecha del precio</th>
					<td><input name="fecha_precio" type="text" class="valid Focus toDate center" id="fecha_precio" size="10" maxlength="10" /></td>
				</tr>
				<tr class="linea_on">
					<th align="left" scope="row">Proveedor</th>
					<td><select name="num_pro" id="num_pro">
					</select></td>
				</tr>
				<tr class="linea_off">
					<th align="left" scope="row">Remisión</th>
					<td><input name="num_rem" type="text" class="valid Focus onlyNumbersAndLetters toUpper" id="num_rem" size="12" /></td>
				</tr>
				<tr class="linea_on">
					<td colspan="2" align="left" scope="row">&nbsp;</td>
				</tr>
				<tr class="linea_off">
					<th align="left" scope="row">Cantidad de cajas</th>
					<td><input name="cajas" type="text" class="valid Focus toPosInt right green" id="cajas" size="12" /></td>
				</tr>
				<tr class="linea_on">
					<td colspan="2" align="left" scope="row">&nbsp;</td>
				</tr>
				<tr class="linea_off">
					<th align="left" scope="row">Peso bruto (remisión)(kg)</th>
					<td><input name="peso_bruto_remision" type="text" class="valid Focus numberPosFormat right" precision="2" id="peso_bruto_remision" size="12" /></td>
				</tr>
				<tr class="linea_on">
					<th align="left" scope="row">Peso bruto (pesadas)(kg)</th>
					<td><input name="peso_bruto" type="text" class="right blue" id="peso_bruto" size="12" readonly="readonly" /></td>
				</tr>
				<tr class="linea_off">
					<td colspan="2" align="left" scope="row">&nbsp;</td>
				</tr>
				<tr class="linea_on">
					<th align="left" scope="row">Tara (kg)</th>
					<td><input name="tara" type="text" class="right red" id="tara" size="12" readonly="readonly" /></td>
				</tr>
				<tr class="linea_off">
					<th align="left" scope="row">Peso neto (kg)</th>
					<td><input name="peso_neto" type="text" class="right blue bold" id="peso_neto" size="12" readonly="readonly" /></td>
				</tr>
				<tr class="linea_on">
					<th align="left" scope="row">Precio por kilo</th>
					<td><input name="precio" type="text" class="right green bold" id="precio" size="12" readonly="readonly" /></td>
				</tr>
				<tr class="linea_off">
					<th align="left" scope="row">Total</th>
					<td><input name="total" type="text" class="right blue bold font12" id="total" size="12" readonly="readonly" /></td>
				</tr>
			</table>
			<br />
			<table class="tabla_captura">
				<tr>
					<th scope="col">Pesadas (kg)</th>
				</tr>
				<tbody id="tabla_pesadas">
					<tr>
						<td align="center"><input name="pesada[]" type="text" class="valid Focus numberPosFormat right" precision="2" id="pesada" size="12" /></td>
					</tr>
				</tbody>
				<tr>
					<th><input name="pesadas" type="text" class="right bold font12" id="pesadas" size="12" readonly="readonly" /></th>
				</tr>
			</table>
			<p>
				<input type="button" name="ingresar" id="ingresar" value="Ingresar remisión" />
			</p>
		</form>
		<div id="result"></div>
	</div>
</div>
<script language="javascript" type="text/javascript" src="menus/{menucnt}"></script>
</body>
</html>
