<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Diferencia en Saldos Conciliados</title>

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
<script type="text/javascript" src="jscripts/mootools/FormValidator.js"></script>
<script type="text/javascript" src="jscripts/mootools/FormStyles.js"></script>
<script type="text/javascript" src="jscripts/mootools/Popups.js"></script>
<script type="text/javascript" src="jscripts/ban/DiferenciaSaldosConciliados.js"></script>
<script language="JavaScript" type="text/javascript" src="menus/stm31.js"></script>
</head>

<body>
<div id="contenedor">
	<div id="titulo">Diferencia en Saldos Conciliados</div>
	<div id="captura" align="center">
		<form action="" method="post" name="Datos" class="FormValidator FormStyles" id="Datos">
			<table class="tabla_captura">
				<tr class="linea_off">
					<th align="left" scope="row">Compa&ntilde;&iacute;a(s)</th>
					<td><input name="cias" type="text" class="valid toInterval" id="cias" size="40" /></td>
				</tr>
				<tr class="linea_on">
					<th align="left" scope="row">Banco</th>
					<td><input name="banco[]" type="checkbox" id="banco" value="1" checked="checked" />
					<img src="/lecaroz/imagenes/Banorte.png" width="186" height="32" /><br />
					<input name="banco[]" type="checkbox" id="banco" value="2" checked="checked" />
					<img src="/lecaroz/imagenes/Santander.png" width="244" height="32" /></td>
				</tr>
				<tr class="linea_off">
					<th align="left" scope="row">Incluir</th>
					<td><input name="sin_cuenta" type="checkbox" id="sin_cuenta" value="1" />
						Sin cuenta<br />
						<input name="sin_saldo_banco" type="checkbox" id="sin_saldo_banco" value="1" />
						Sin saldo del banco<br />
						<input name="sin_movimientos_pendientes" type="checkbox" id="sin_movimientos_pendientes" value="1" />
						Sin movimientos pendientes</td>
				</tr>
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