<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Reporte de efectivos</title>

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
<script type="text/javascript" src="jscripts/ban/EfectivosReporteMensual.js"></script>
<script language="JavaScript" type="text/javascript" src="menus/stm31.js"></script>
</head>

<body>
<div id="contenedor">
	<div id="titulo">Reporte de efectivos</div>
	<div id="captura" align="center">
		<form name="Datos" class="FormValidator FormStyles" id="Datos">
			<table class="tabla_captura">
				<tr class="linea_off">
					<th align="left" scope="row">Compa&ntilde;&iacute;a(s)</th>
					<td><input name="cias" type="text" class="valid toInterval" id="cias" size="40" /></td>
				</tr>
				<tr class="linea_on">
					<th align="left" scope="row">Omitir compa&ntilde;&iacute;a(s)</th>
					<td><input name="omitir_cias" type="text" class="valid toInterval" id="omitir_cias" size="40" /></td>
				</tr>
				<tr class="linea_off">
					<th align="left" scope="row">Administrador</th>
					<td><select name="admin" id="admin">
						<option value="" selected="selected"></option>
						<!-- START BLOCK : admin -->
						<option value="{value}">{text}</option>
						<!-- END BLOCK : admin -->
					</select></td>
				</tr>
				<tr class="linea_on">
					<th align="left" scope="row">Fecha de corte</th>
					<td><input name="fecha" type="text" class="valid Focus toDate center" id="fecha" value="{fecha}" size="10" maxlength="10" /></td>
				</tr>
				<tr class="linea_off">
					<th align="left" scope="row">Tipo</th>
					<td><input name="accion" type="radio" id="accion" value="reporteNormal" checked="checked" />
						Normal<br />
						<input type="radio" name="accion" id="accion" value="reporteAdministrador" />
						Administradores<br />
						<input type="radio" name="accion" id="accion" value="reporteMiniatura" />
						Miniatura<br />
						<input type="radio" name="accion" id="accion" value="reporteCompleto" />
						Completo<br />
						<input type="radio" name="accion" id="accion" value="reporteComparativo" />
						Comparativo de fin de mes<br />
						<input type="radio" name="accion" id="accion" value="reporteTotales" />
						Totales</td>
				</tr>
			</table>
			<p>
				<input type="button" name="reporte" id="reporte" value="Generar reporte" />
			</p>
		</form>

	</div>
</div>
<script language="javascript" type="text/javascript" src="menus/{menucnt}"></script>
</body>
</html>