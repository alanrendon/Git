<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Consulta de cheques</title>
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
<script type="text/javascript" src="/lecaroz/jscripts/mootools1.4/FormValidator.js"></script>
<script type="text/javascript" src="/lecaroz/jscripts/ban/ChequesConsulta.js"></script>
<style type="text/css">
.icono {
	opacity: 0.6;
}
.icono:hover {
	opacity: 1;
	cursor: pointer;
}
.icono_disabled {
	opacity: 0.4;
}
.logo_banco {
	padding: 0px 0px 0px 18px;
	background-repeat: no-repeat;
}
.logo_banco_1 {
	background-image: url(imagenes/Banorte16x16.png);
}
.logo_banco_2 {
	background-image: url(imagenes/Santander16x16.png);
}
tr.cancelado > td {
	background-color: #FFC6C6;
}
</style>
</head>

<body>
<div id="contenedor">
	<div id="titulo">Consulta de cheques</div>
	<div id="captura" align="center"></div>
</div>
<div id="cancelar_wrapper" style="display:none;">
	<form action="" method="post" name="cancelar_form" class="FormValidator" id="cancelar_form">
		<input name="devolver_facturas" type="hidden" id="devolver_facturas" value="1" />
		<p>Opciones de cancelaci&oacute;n:</p>
		<ul>
			<li>Fecha de cancelaci&oacute;n <input name="fecha_cancelacion" type="input" id="fecha_cancelacion" class="validate focus toDate center" size="10" maxlength="10" value="{fecha_cancelacion}" /></li>
			<!-- <li><input name="devolver_facturas" type="checkbox" id="devolver_facturas" value="1" checked="checked" /> Devolver facturas a pasivo</li> -->
			<li><input name="inversa" type="checkbox" id="inversa" value="1" checked="checked" /> Realizar inversa de cheque conciliado</li>
		</ul>
		<p>¿Desea cancelar el cheque con las opciones seleccionadas?</p>
	</form>
</div>
<div id="cancelar_seleccion_wrapper" style="display:none;">
	<form action="" method="post" name="cancelar_seleccion_form" class="FormValidator" id="cancelar_seleccion_form">
		<input name="devolver_facturas_seleccion" type="hidden" id="devolver_facturas_seleccion" value="1" />
		<p>Opciones de cancelaci&oacute;n:</p>
		<ul>
			<li>Fecha de cancelaci&oacute;n <input name="fecha_cancelacion_seleccion" type="input" id="fecha_cancelacion_seleccion" class="validate focus toDate center" size="10" maxlength="10" value="{fecha_cancelacion}" /></li>
			<!-- <li><input name="devolver_facturas_seleccion" type="checkbox" id="devolver_facturas_seleccion" value="1" checked="checked" /> Devolver facturas a pasivo</li> -->
			<li><input name="inversa_seleccion" type="checkbox" id="inversa_seleccion" value="1" checked="checked" /> Realizar inversa de cheques conciliados</li>
		</ul>
		<p>¿Desea cancelar los cheques marcados con las opciones seleccionadas?</p>
	</form>
</div>
<div id="cambiar_fecha_seleccion_wrapper" style="display:none;">
	<form action="" method="post" name="cambiar_fecha_seleccion_form" class="FormValidator" id="cancelar_form">
		<ul>
			<li>Nueva fecha <input name="nueva_fecha_seleccion" type="input" id="nueva_fecha_seleccion" class="validate focus toDate center" size="10" maxlength="10" value="{nueva_fecha}" /></li>
		</ul>
		<p>¿Desea cambiar la fecha de los cheques seleccionados?</p>
	</form>
</div>
<script>var fecha_cancelacion = '{fecha_cancelacion}';</script>
<script language="javascript" type="text/javascript" src="menus/{menucnt}"></script>
</body>
</html>
