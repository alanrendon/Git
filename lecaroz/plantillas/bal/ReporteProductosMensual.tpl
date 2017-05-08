<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Reporte de surtido de productos mensual</title>
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
<script type="text/javascript" src="/lecaroz/jscripts/bal/ReporteProductosMensual.js"></script>
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
	<div id="titulo">Reporte de surtido de productos mensual</div>
	<div id="captura" align="center">
		<form name="inicio" class="FormValidator" id="inicio">
			<table class="table">
				<thead>
					<tr>
						<th colspan="2" scope="col">&nbsp;</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td class="bold">Compa&ntilde;&iacute;a(s)</td>
						<td><input name="cias" type="text" class="validate toInterval" id="cias" size="40" /></td>
					</tr>
					<tr>
						<td class="bold">Administrador</td>
						<td>
							<select name="admin" id="admin">
								<option value="" selected="selected"></option>
								<!-- START BLOCK : admin -->
								<option value="{value}">{text}</option>
								<!-- END BLOCK : admin -->
							</select>
						</td>
					</tr>
					<tr>
						<td class="bold">Producto</td>
						<td><input name="codmp" type="text" class="validate focus toPosInt center" id="codmp" size="4" /><span id="producto"></span></td>
					</tr>
					<tr>
						<td class="bold">Proveedor(es)</td>
						<td><input name="pros" type="text" class="validate toInterval" id="pros" size="40" /></td>
					</tr>
					<tr>
						<td class="bold">A&ntilde;o</td>
						<td><input name="anio" type="text" class="validate focus toPosInt center" id="anio" size="4" value="{anio}" /></td>
					</tr>
					<tr>
						<td class="bold">Mes</td>
						<td>
							<select name="mes" id="mes">
								<option value="1"{1}>ENERO</option>
								<option value="2"{2}>FEBRERO</option>
								<option value="3"{3}>MARZO</option>
								<option value="4"{4}>ABRIL</option>
								<option value="5"{5}>MAYO</option>
								<option value="6"{6}>JUNIO</option>
								<option value="7"{7}>JULIO</option>
								<option value="8"{8}>AGOSTO</option>
								<option value="9"{9}>SEPTIEMBRE</option>
								<option value="10"{10}>OCTUBRE</option>
								<option value="11"{11}>NOVIEMBRE</option>
								<option value="12"{12}>DICIEMBRE</option>
							</select>
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
				<input type="button" name="consultar" id="consultar" value="Consultar" />
			</p>
		</form>
	</div>
</div>
<script language="javascript" type="text/javascript" src="menus/{menucnt}"></script>
</body>
</html>
