<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Cartas de adeudo a proveedores</title>
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
<script type="text/javascript" src="/lecaroz/jscripts/ban/CorreoAdeudoProveedores.js"></script>
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
#info-table {
	border-collapse: collapse;
	border: solid 1px #000;
	background-color: #fff;
}
#info-table td,
#info-table th {
	border: solid 1px #000;
}
#info-table th {
	background-color: #999;
}
</style>
</head>

<body>

<div id="contenedor">
	<div id="titulo">Cartas de adeudo a proveedores</div>
	<div id="captura" align="center">
		<form name="inicio_form" class="FormValidator" id="inicio_form">
			<table class="table">
				<thead>
					<tr>
						<th colspan="2" scope="col">&nbsp;</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td class="bold">Proveedor(es)</td>
						<td><input name="pros" type="text" class="validate toInterval" id="pros" size="40" /></td>
					</tr>
					<tr>
						<td class="bold">Administrador</td>
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
						<td class="bold">A&ntilde;o de cierre contable</td>
						<td>
							<input name="anio" type="text" class="validate focus toPosInt center" id="anio" size="4" maxlength="4" value="{anio}" />
						</td>
					</tr>
				</tbody>
				<tfoot>
					<tr>
						<td colspan="2">&nbsp;</td>
					</tr>
				</tfoot>
			</table>
			<p>
				<button type="button" id="generar_cartas">Generar cartas</button>
				<button type="button" id="enviar_correos">Enviar correos electr&oacute;nicos</button>
			</p>
		</form>
	</div>
</div>

<div id="cartas_wrapper" style="display:none; width:800px; height:600px;">
	<iframe id="cartas_frame" src="" style="width:100%; height:100%;"></iframe>
</div>

<script language="javascript" type="text/javascript" src="/lecaroz/menus/{menucnt}"></script>

</body>
</html>
