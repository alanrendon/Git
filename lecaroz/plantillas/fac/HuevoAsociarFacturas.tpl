<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Asociar remisiones de huevo con facturas</title>

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
<script type="text/javascript" src="jscripts/mootools/FormValidator.js"></script>
<script type="text/javascript" src="jscripts/mootools/FormStyles.js"></script>
<script type="text/javascript" src="jscripts/mootools/Popups.js"></script>
<script type="text/javascript" src="menus/stm31.js"></script>

<script type="text/javascript" src="jscripts/fac/HuevoAsociarFacturas.js"></script>
</head>

<body>
<div id="contenedor">
	<div id="titulo">Asociar remisiones de huevo con facturas</div>
	<div id="captura" align="center">
		<form name="Datos" class="FormValidator FormStyles" id="Datos">
			<p class="bold font12">Para asociar una remisión ingrese el número de factura, para borrarla deje el número de factura en blanco.</p>
			<table class="tabla_captura">
				<tr>
					<th scope="col">Remisión</th>
					<th scope="col">Proveedor(es)</th>
					<th scope="col">Compañía</th>
					<th scope="col">Fecha</th>
					<th scope="col">Cajas</th>
					<th scope="col">Peso Neto (kg)</th>
					<th scope="col">Precio por kilo</th>
					<th scope="col">Total</th>
					<th scope="col">Factura</th>
				</tr>
				<tbody id="tbody">
					<tr class="linea_off">
						<td align="center"><input name="num_rem[]" type="text" class="valid Focus onlyNumbersAndLetters toUpper" id="num_rem" size="12" /></td>
						<td align="center"><select name="num_pro[]" id="num_pro">
						</select></td>
						<td align="center"><input name="cia[]" type="text" disabled="disabled" id="cia" size="30" /></td>
						<td align="center"><input name="fecha[]" type="text" disabled="disabled" class="center" id="fecha" size="10" maxlength="10" /></td>
						<td align="center"><input name="cajas[]" type="text" disabled="disabled" class="right" id="cajas" size="8" /></td>
						<td align="center"><input name="peso_neto[]" type="text" disabled="disabled" class="right" id="peso_neto" size="12" /></td>
						<td align="center"><input name="precio[]" type="text" disabled="disabled" class="right" id="precio" size="12" /></td>
						<td align="center"><input name="total[]" type="text" disabled="disabled" class="right" id="total" size="12" /></td>
						<td align="center"><input name="num_fact[]" type="text" class="valid Focus onlyNumbersAndLetters toUpper" id="num_fact" size="12" /></td>
				</tr>
				</tbody>
			</table>
			<p>
				<input type="button" name="cancelar" id="cancelar" value="Cancelar" />
				&nbsp;
				<input type="button" name="asociar" id="asociar" value="Asociar remisiones con facturas" />
			</p>
		</form>
		<div id="result"></div>
	</div>
</div>
<script language="javascript" type="text/javascript" src="menus/{menucnt}"></script>
</body>
</html>
