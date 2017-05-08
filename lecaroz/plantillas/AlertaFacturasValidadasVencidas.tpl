<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Facturas validadas vencidas</title>
<link href="/lecaroz/styles/screen.css" rel="stylesheet" type="text/css" media="screen" />
<link href="/lecaroz/styles/print.css" rel="stylesheet" type="text/css" media="print" />
<link href="/lecaroz/styles/font-style.css" rel="stylesheet" type="text/css" />
<link href="/lecaroz/styles/Popups.css" rel="stylesheet" type="text/css" />
<link href="/lecaroz/styles/FormValidator.css" rel="stylesheet" type="text/css" />
<link href="/lecaroz/styles/FormStyles.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="jscripts/mootools/mootools-1.2-core.js"></script>
<script type="text/javascript" src="jscripts/mootools/mootools-1.2-more.js"></script>
<script type="text/javascript" src="jscripts/mootools/String.implement.js"></script>
<script type="text/javascript" src="jscripts/mootools/Number.implement.js"></script>
<script type="text/javascript" src="jscripts/mootools/Array.implement.js"></script>
<script type="text/javascript" src="jscripts/mootools/FormValidator.js"></script>
<script type="text/javascript" src="jscripts/mootools/FormStyles.js"></script>
<script type="text/javascript" src="jscripts/mootools/Popups.js"></script>
<script type="text/javascript" src="jscripts/AlertaFacturasValidadasVencidas.js"></script>
<script language="JavaScript" type="text/javascript" src="menus/stm31.js"></script>
</head>

<body>
<p class="bold font14 center">Facturas validadas vencidas</p>
<form method="get" name="Datos" class="FormValidator FormStyles" id="Datos">
	<table align="center" class="print" id="empleados">
		<tr>
			<th class="print">Compañía</th>
			<th class="print">Proveedor</th>
			<th class="print">Factura</th>
			<th class="print">Alta</th>
		</tr>
		<!-- START BLOCK : row -->
		<tr>
			<td class="print">{num_cia} {nombre_cia}</td>
			<td class="print">{num_pro} {nombre_pro}</td>
			<td class="print">{num_fact}</td>
			<td align="center" class="print red">{fecha_alta}</td>
		</tr>
		<!-- END BLOCK : row -->
	</table>
	<p class="noDisplay center">
		<input type="button" name="cerrar" id="cerrar" value="Cerrar" />
	</p>
</form>
</body>
</html>
