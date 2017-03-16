<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Estado de cuenta agrupado</title>
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
<script type="text/javascript" src="/lecaroz/jscripts/ban/EstadoCuentaAgrupado.js"></script>
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
	<div id="titulo">Estado de cuenta agrupado</div>
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
						<td class="bold">Compañía(s)</td>
						<td><input name="cias" type="text" class="validate toInterval" id="cias" size="40" /></td>
					</tr>
					<tr>
						<td class="bold">Banco</td>
						<td><select name="banco" id="banco" class="logo_banco">
								<option value="" class="logo_banco"></option>
								<option value="1" class="logo_banco logo_banco_1">BANORTE</option>
								<option value="2" class="logo_banco logo_banco_2">SANTANDER</option>
							</select></td>
					</tr>
					<!-- <tr>
						<td class="bold">Periodo</td>
						<td><input name="fecha1" type="text" class="validate focus toDate center" id="fecha1" value="{fecha1}" size="10" maxlength="10" />
							al
							<input name="fecha2" type="text" class="validate focus toDate center" id="fecha2" value="{fecha2}" size="10" maxlength="10" /></td>
					</tr> -->
					<tr>
						<td class="bold">Conciliado</td>
						<td><input name="conciliado1" type="text" class="validate focus toDate center" id="conciliado1" size="10" maxlength="10" />
							al
							<input name="conciliado2" type="text" class="validate focus toDate center" id="conciliado2" size="10" maxlength="10" /></td>
					</tr>
					<!-- <tr>
						<td class="bold">Opciones</td>
						<td>
							<input name="depositos" type="checkbox" id="depositos" value="1" checked="checked" />
							<span class="bold blue">Depositos</span><br />
							<input name="cargos" type="checkbox" id="cargos" value="1" checked="checked" />
							<span class="bold red">Cargos</span>
							<input name="pendientes" type="hidden" id="pendientes" value="1" />
							<input name="conciliados" type="hidden" id="conciliados" value="1" />
						</td>
					</tr>
					<tr>
						<td class="bold">Proveedor(es)</td>
						<td><input name="pros" type="text" class="validate toInterval" id="pros" size="40" /></td>
					</tr>
					<tr>
						<td class="bold">Folio(s)</td>
						<td><input name="folios" type="text" class="validate toInterval" id="folios" size="40" /></td>
					</tr>
					<tr>
						<td class="bold">Gasto(s)</td>
						<td><input name="gastos" type="text" class="validate toInterval" id="gastos" size="40" /></td>
					</tr>
					<tr>
						<td class="bold">Importe(s) o rango(s)</td>
						<td><input name="importes" type="text" class="validate toIntervalFloats" id="importes" size="40" /></td>
					</tr>
					<tr>
						<td class="bold">Código(s)</td>
						<td><select name="codigos[]" size="10" multiple="multiple" id="codigos" style="width:100%;">
							</select></td>
					</tr> -->
					<!-- <tr>
						<td class="bold">Concepto</td>
						<td><input name="concepto" type="text" class="validate toText cleanText toUpper" id="concepto" size="40" maxlength="1000" /></td>
					</tr> -->
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
