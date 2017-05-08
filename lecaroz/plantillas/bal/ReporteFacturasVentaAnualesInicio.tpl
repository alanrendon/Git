<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Reporte de facturas de venta anuales</title>
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
<script type="text/javascript" src="/lecaroz/jscripts/bal/ReporteFacturasVentaAnualesInicio.js"></script>
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
	<div id="titulo">Reporte de facturas de venta anuales</div>
	<div id="captura" align="center">
		<form name="Datos" class="FormValidator" id="inicio">
			<table class="table">
				<thead>
					<tr>
						<th colspan="2" align="left" class="bold" scope="row">&nbsp;</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td align="left" class="bold" scope="row">Compañía(s)</td>
						<td><input name="cias" type="text" class="validate toInterval" id="cias" size="40" /></td>
					</tr>
					<tr>
						<td align="left" class="bold" scope="row">Administrador</td>
						<td>
							<select name="admin" id="admin">
								<option value=""></option>
								<!-- START BLOCK : admin -->
								<option value="{value}">{text}</option>
								<!-- END BLOCK : admin -->
							</select>
						</td>
					</tr>
					<tr>
						<td align="left" class="bold" scope="row">RFC</td>
						<td><input name="rfc" type="text" class="validate toRFC toUpper" id="rfc" size="13" /></td>
					</tr>
					<tr>
						<td align="left" class="bold" scope="row">Año</td>
						<td><input name="anio" type="text" class="validate focus toPosInt center" id="anio" value="{anio}" size="4" maxlength="4" /></td>
					</tr>
				</tbody>
				<tfoot>
					<tr>
						<td colspan="2" align="left" class="bold" scope="row">&nbsp;</td>
					</tr>
				</tfoot>
			</table>
			<p>
				<!--<input type="button" name="exportar" id="exportar" value="Exportar" />
				&nbsp;&nbsp;-->
				<input name="reporte" type="button" id="reporte" value="Reporte" />
			</p>
		</form>
	</div>
</div>
<script language="javascript" type="text/javascript" src="menus/{menucnt}"></script>
</body>
</html>
