<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Porcentajes de venta de sucursales</title>
<link href="/lecaroz/smarty/styles/layout.css" rel="stylesheet" type="text/css" />
<link href="/lecaroz/styles/table_layout.css" rel="stylesheet" type="text/css" />
<link href="/lecaroz/styles/font-style.css" rel="stylesheet" type="text/css" />
<link href="/lecaroz/styles/FormValidator2.0.css" rel="stylesheet" type="text/css" />
<link href="/lecaroz/styles/Tips.css" rel="stylesheet" type="text/css" />
<link href="/lecaroz/styles/mbox/mBoxCore.css" rel="stylesheet" type="text/css" />
<link href="/lecaroz/styles/mbox/mBoxModal.css" rel="stylesheet" type="text/css" />
<link href="/lecaroz/styles/mbox/mBoxTooltip.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="menus/stm31.js"></script>
<script type="text/javascript" src="/lecaroz/jscripts/mootools1.4/mootools-core-1.4.5.js"></script>
<script type="text/javascript" src="/lecaroz/jscripts/mootools1.4/mootools-more-1.4.0.1.js"></script>
<script type="text/javascript" src="/lecaroz/jscripts/mootools1.4/string.implement.js"></script>
<script type="text/javascript" src="/lecaroz/jscripts/mootools1.4/number.implement.js"></script>
<script type="text/javascript" src="/lecaroz/jscripts/mootools1.4/array.implement.js"></script>
<script type="text/javascript" src="/lecaroz/jscripts/mbox/mBox.Core.js"></script>
<script type="text/javascript" src="/lecaroz/jscripts/mbox/mBox.Modal.js"></script>
<script type="text/javascript" src="/lecaroz/jscripts/mbox/mBox.Modal.Confirm.js"></script>
<script type="text/javascript" src="/lecaroz/jscripts/mbox/mBox.Tooltip.js"></script>
<script type="text/javascript" src="/lecaroz/jscripts/mootools1.4/Request.File.js"></script>
<script type="text/javascript" src="/lecaroz/jscripts/mootools1.4/FormValidator.js"></script>
<script type="text/javascript" src="/lecaroz/jscripts/ban/PorcentajesVentasSucursales.js"></script>
<style type="text/css">
.icono {
	opacity: 0.6;
}
.icono:hover {
	opacity: 1;
	cursor: pointer;
}
</style>
</head>

<body>

<div id="contenedor">
	<div id="titulo">Porcentajes de venta de sucursales</div><div id="captura" align="center"></div>
</div>

<div id="alta_matriz_wrapper" style="display:none;">
	<form name="alta_matriz_form" class="FormValidator" id="alta_matriz_form">
		<table class="table">
			<thead>
				<tr>
					<th colspan="2">&nbsp;</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td align="left">Matriz</td>
					<td>
						<input name="matriz" type="text" class="validate focus toPosInt center" id="matriz" size="3" />
						<input name="nombre_matriz" type="text" id="nombre_matriz" size="40" disabled="disabled" />
					</td>
				</tr>
			</tbody>
			<tfoot>
				<tr>
					<td colspan="2">&nbsp;</td>
				</tr>
			</tfoot>
		</table>
	</form>
</div>

<div id="alta_sucursal_wrapper" style="display:none;">
	<form name="alta_sucursal_form" class="FormValidator" id="alta_sucursal_form">
		<table class="table">
			<thead>
				<tr>
					<th colspan="2">&nbsp;</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td align="left">Sucursal</td>
					<td>
						<input name="sucursal" type="text" class="validate focus toPosInt center" id="sucursal" size="3" />
						<input name="nombre_sucursal" type="text" id="nombre_sucursal" size="40" disabled="disabled" />
					</td>
				</tr>
				<tr>
					<td align="left">Porcentaje</td>
					<td>
						<input name="porcentaje_alta" type="text" class="validate focus numberPosFormat right" precision="2" id="porcentaje_alta" size="7" />
					</td>
				</tr>
			</tbody>
			<tfoot>
				<tr>
					<td colspan="2">&nbsp;</td>
				</tr>
			</tfoot>
		</table>
	</form>
</div>

<div id="mod_sucursal_wrapper" style="display:none;">
	<form name="mod_sucursal_form" class="FormValidator" id="mod_sucursal_form">
		<table class="table">
			<thead>
				<tr>
					<th colspan="2">&nbsp;</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td align="left">Sucursal</td>
					<td id="nombre_sucursal_mod">&nbsp;</td>
				</tr>
				<tr>
					<td align="left">Porcentaje</td>
					<td>
						<input name="porcentaje_mod" type="text" class="validate focus numberPosFormat right" precision="2" id="porcentaje_mod" size="7" />
					</td>
				</tr>
			</tbody>
			<tfoot>
				<tr>
					<td colspan="2">&nbsp;</td>
				</tr>
			</tfoot>
		</table>
	</form>
</div>

<script language="javascript" type="text/javascript" src="menus/{menucnt}"></script>

</body>
</html>
