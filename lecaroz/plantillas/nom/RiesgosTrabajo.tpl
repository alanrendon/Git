<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Riesgos de trabajo</title>
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
<script type="text/javascript" src="/lecaroz/jscripts/nom/RiesgosTrabajo.js"></script>
<style type="text/css">
.icono {
	opacity: 0.6;
}
.icono:hover {
	opacity: 1;
	cursor: pointer;
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
</style>
</head>

<body>
<div id="contenedor">
	<div id="titulo">Riesgos de trabajo</div>
	<div id="captura" align="center">
	</div>
</div>
<div id="alta_incapacidad_wrapper" style="display:none;">
	<form action="" method="get" name="alta_incapacidad" class="FormValidator" id="alta_incapacidad">
		<table class="table">
			<thead>
				<tr>
					<th colspan="2" align="left" scope="col">&nbsp;</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td class="bold">Fecha de documento</td>
					<td><input name="fecha_nueva_incapacidad" type="text" class="validate focus toDate center" id="fecha_nueva_incapacidad" size="10" maxlength="10" /></td>
				</tr>
				<tr>
					<td class="bold">Inicio de incapacidad</td>
					<td><input name="fecha_inicio_nueva_incapacidad" type="text" class="validate focus toDate center" id="fecha_inicio_nueva_incapacidad" size="10" maxlength="10" /></td>
				</tr>
				<tr>
					<td class="bold">Días de incapacidad</td>
					<td><input name="dias_nueva_incapacidad" type="text" class="validate focus toPosInt right" id="dias_nueva_incapacidad" size="5" /></td>
				</tr>
				<tr>
					<td class="bold">Folio de incapacidad</td>
					<td><input name="folio_nueva_incapacidad" type="text" class="validate focus onlyNumbersAndLetters toUpper right" id="folio_nueva_incapacidad" size="8" maxlength="10" /></td>
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
