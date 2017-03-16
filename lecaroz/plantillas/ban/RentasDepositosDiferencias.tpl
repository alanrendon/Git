<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Captura de diferencias de depósitos de renta</title>
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
<script type="text/javascript" src="jscripts/ban/RentasDepositosDiferencias.js"></script>
<script language="JavaScript" type="text/javascript" src="menus/stm31.js"></script>
</head>

<body>
<div id="contenedor">
	<div id="titulo">Captura de diferencias de depósitos de renta</div>
	<div id="captura" align="center">
		<form name="Datos" class="FormValidator FormStyles" id="Datos">
			<table class="tabla_captura">
				<tr>
					<th scope="col">Banco</th>
				</tr>
				<tr>
					<td height="34" align="center"><select name="banco" id="banco" style="font-size:16pt;">
							<option value="1">BANORTE</option>
							<option value="2">SANTANDER</option>
					</select></td>
				</tr>
			</table>
			<br />
			<table class="tabla_captura">
				<tr>
					<th scope="col">Compañía</th>
					<th scope="col">Fecha</th>
					<th scope="col">Arrendatario</th>
					<th scope="col">Año</th>
					<th scope="col">Mes</th>
					<th scope="col">Importe</th>
				</tr>
				<tbody id="filas">
				</tbody>
				<tr>
					<th colspan="5" align="right">Total</th>
					<th><input name="total" type="text" disabled="disabled" class="font12 bold right" id="total" value="0.00" size="10" /></th>
				</tr>
			</table>
			<p>
				<input type="button" name="cancelar" id="cancelar" value="Cancelar" />
				&nbsp;&nbsp;
				<input type="button" name="registrar" id="registrar" value="Registrar depósitos" />
			</p>
		</form>
	</div>
</div>
<script language="javascript" type="text/javascript" src="menus/{menucnt}"></script>
</body>
</html>