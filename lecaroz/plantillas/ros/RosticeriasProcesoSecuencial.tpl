<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Rosticer&iacute;as - Proceso secuencial</title>
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
<script type="text/javascript" src="/lecaroz/jscripts/ros/RosticeriasProcesoSecuencial.js"></script>
<style type="text/css">
.icono {
	opacity: 0.6;
}
.icono:hover {
	opacity: 1;
	cursor: pointer;
}
.show {
	display: block;
}
.hide {
	display: none;
}
</style>
</head>

<body>
<div id="contenedor">
	<div id="titulo">Rosticer&iacute;as - Proceso secuencial</div><div id="captura" align="center"></div>
</div>

<div id="agregar_precio_compra_wrapper" style="display:none;">
	<form action="" method="post" name="agregar_precio_compra_form" class="FormValidator" id="agregar_precio_compra_form">
		<table align="center" class="table">
			<thead>
				<tr>
					<th colspan="2" scope="row">&nbsp;</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>Producto</td>
					<td>
						<input name="nuevo_codmp_compra" type="text" id="nuevo_codmp_compra" class="validate focus toPosInt right" value="" size="3" />
						<input name="nuevo_nombre_mp_compra" type="text" id="nuevo_nombre_mp_compra" value="" disabled="disabled" size="30" />
					</td>
				</tr>
				<tr>
					<td>Proveedor</td>
					<td>
						<input name="nuevo_num_pro_compra" type="text" id="nuevo_num_pro_compra" class="validate focus toPosInt right" value="" size="3" />
						<input name="nuevo_nombre_pro_compra" type="text" id="nuevo_nombre_pro_compra" value="" disabled="disabled" size="30" />
					</td>
				</tr>
				<tr>
					<td>Precio de compra</td>
					<td><input name="nuevo_precio_compra" type="text" id="nuevo_precio_compra" class="validate focus numberPosFormat right" precision="2" value="" size="10" /></td>
				</tr>
			</tbody>
			<tfoot>
				<tr>
					<td colspan="2" scope="row">&nbsp;</td>
				</tr>
			</tfoot>
		</table>
	</form>
</div>

<div id="modificar_precio_venta_wrapper" style="display:none;">
	<form action="" method="post" name="modificar_precio_venta_form" class="FormValidator" id="modificar_precio_venta_form">
		<table align="center" class="table">
			<thead>
				<tr>
					<th colspan="2" scope="row">&nbsp;</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>Producto</td>
					<td id="nombre_producto_venta"></td>
				</tr>
				<tr>
					<td>Precio de venta</td>
					<td>
						<input name="id_precio_venta" type="hidden" id="id_precio_venta" value="" size="10" />
						<input name="index_precio_venta" type="hidden" id="index_precio_venta" value="" size="10" />
						<input name="precio_venta" type="text" id="precio_venta" class="validate focus numberPosFormat right" precision="2" value="" size="10" />
					</td>
				</tr>
			</tbody>
			<tfoot>
				<tr>
					<td colspan="2" scope="row">&nbsp;</td>
				</tr>
			</tfoot>
		</table>
	</form>
</div>

<div id="cantidad_gas_wrapper" style="display:none;">
	<form action="" method="post" name="cantidad_gas_form" class="FormValidator" id="cantidad_gas_form">
		<input type="hidden" name="row_gasto" id="row_gasto" value="" />
		<table align="center" class="table">
			<thead>
				<tr>
					<th colspan="2" scope="row">&nbsp;</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td class="bold" scope="row">Cantidad de gas comprado</td>
					<td>
						<input name="cantidad_gas_input" type="text" class="validate focus numberPosFormat right" precision="2" id="cantidad_gas_input" size="10" />
					</td>
				</tr>
			</tbody>
			<tfoot>
				<tr>
					<td colspan="2" scope="row">&nbsp;</td>
				</tr>
			</tfoot>
		</table>
	</form>
</div>

<script language="javascript" type="text/javascript" src="/lecaroz/menus/{menucnt}"></script>

</body>
</html>
