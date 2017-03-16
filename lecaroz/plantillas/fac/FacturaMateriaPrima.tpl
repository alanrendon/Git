<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Factura de Materia Prima</title>

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
<script type="text/javascript" src="menus/stm31.js"></script>

<script type="text/javascript" src="jscripts/fac/FacturaMateriaPrima.js"></script>
</head>

<body>
<div id="contenedor">
	<div id="titulo">Factura de Materia Prima</div>
	<div id="captura" align="center">
		&nbsp;
		<form action="" method="post" name="Datos" class="FormValidator FormStyles" id="Datos">
			<table class="tabla_captura">
				<tr class="linea_off">
					<th align="left" scope="row">Compañía</th>
					<td><input name="num_cia" type="text" class="valid Focus toPosInt center" id="num_cia" size="3" />
							<input name="nombre_cia" type="text" disabled="disabled" id="nombre_cia" size="40" /></td>
				</tr>
				<tr class="linea_on">
					<th align="left" scope="row">Proveedor</th>
					<td><input name="num_pro" type="text" class="valid Focus toPosInt center" id="num_pro" size="3" />
							<input name="nombre_pro" type="text" disabled="disabled" id="nombre_pro" size="40" /></td>
				</tr>
				<tr class="linea_off">
					<th align="left" scope="row">Factura</th>
					<td><input name="num_fact" type="text" class="valid Focus onlyNumbersAndLetters cleanText toUpper" id="num_fact" size="20" /></td>
				</tr>
				<tr class="linea_on">
					<th align="left" scope="row">Fecha</th>
					<td><input name="fecha" type="text" class="valid Focus toDate center" id="fecha" size="10" maxlength="10" /></td>
				</tr>
				<tr class="linea_off">
					<th align="left" scope="row"><input name="aclaracion" type="checkbox" id="aclaracion" value="1" />
					Aclaración</th>
					<td><textarea name="observaciones" cols="45" rows="5" disabled="disabled" class="valid toText cleanText toUpper" id="observaciones" style="width:98%;"></textarea></td>
				</tr>
			</table>
		<br />
		<table class="tabla_captura">
				<tr>
					<th scope="row">Cantidad</th>
					<th>Producto</th>
					<th>Contenido</th>
					<th>Unidad</th>
					<th>Precio</th>
					<th>Desc. 1</th>
					<th>Desc. 2</th>
					<th>Desc. 3</th>
					<th>I.E.P.S</th>
					<th>I.V.A.</th>
					<th>Importe</th>
					<th>Regalado</th>
				</tr>
				<tbody id="productos">
				</tbody>
				<tr>
					<th colspan="10" align="right" scope="row">Subtotal</th>
					<th><input name="subtotal" type="text" class="right bold font12" id="subtotal" value="0.00" size="10" readonly="readonly" /></th>
					<th>&nbsp;</th>
				</tr>
				<tr>
					<th colspan="10" align="right" scope="row">I.V.A.</th>
					<th><input name="iva_total" type="text" class="right bold font12" id="iva_total" value="0.00" size="10" readonly="readonly" /></th>
					<th>&nbsp;</th>
				</tr>
				<tr>
					<th colspan="10" align="right" scope="row">Total</th>
					<th><input name="total" type="text" class="right bold font12" id="total" value="0.00" size="10" readonly="readonly" /></th>
					<th>&nbsp;</th>
				</tr>
			</table>
			<p>
				<input type="button" name="ingresar" id="ingresar" value="Ingresar factura" />
			</p>
		</form>
	</div>
</div>
<script language="javascript" type="text/javascript" src="menus/{menucnt}"></script>
</body>
</html>
