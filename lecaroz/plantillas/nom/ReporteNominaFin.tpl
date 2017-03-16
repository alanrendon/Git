<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Reporte de N&oacute;mina</title>

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
<script type="text/javascript" src="menus/stm31.js"></script>

<script type="text/javascript" src="jscripts/nom/ReporteNominaFin.js"></script>
</head>

<body>
<div id="contenedor">
	<div id="titulo">Reporte de N&oacute;mina</div>
	<div id="captura" align="center">
		<table class="tabla_captura">
			<tr class="linea_off">
				<th align="left" scope="row">Folio</th>
				<td class="bold font12">{folio}
				<input name="folio" type="hidden" id="folio" value="{folio}" /></td>
			</tr>
			<tr class="linea_on">
				<th align="left" scope="row">Compa&ntilde;&iacute;a</th>
				<td class="bold font12">{num_cia} {nombre_cia}</td>
			</tr>
			<tr class="linea_off">
				<th align="left" scope="row">R.F.C.</th>
				<td class="bold font12">{rfc_cia}</td>
			</tr>
			<tr class="linea_on">
				<th align="left" scope="row">I.M.S.S.</th>
				<td class="bold font12">{no_imss}</td>
			</tr>
			<tr class="linea_off">
				<th align="left" scope="row">Semana</th>
				<td class="bold font12">{semana}</td>
			</tr>
			<tr class="linea_on">
				<th align="left" scope="row">Periodo</th>
				<td class="bold font12">{fecha1} al {fecha2}</td>
			</tr>
		</table>
		<p>
			<input type="button" name="terminar" id="terminar" value="Terminar" />
		&nbsp;&nbsp;
		<input type="button" name="reporte" id="reporte" value="Reporte" />
		</p>
	</div>
</div>
<script language="javascript" type="text/javascript" src="menus/{menucnt}"></script>
</body>
</html>
