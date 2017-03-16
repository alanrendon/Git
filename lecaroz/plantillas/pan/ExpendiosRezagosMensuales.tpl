<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Rezagos mensuales de expendios</title>
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
<script type="text/javascript" src="jscripts/pan/ExpendiosRezagosMensuales.js"></script>
<script language="JavaScript" type="text/javascript" src="menus/stm31.js"></script>
</head>

<body>
<div id="contenedor">
	<div id="titulo">Rezagos mensuales de expendios</div>
	<div id="captura" align="center">
		<form name="Datos" class="FormValidator FormStyles" id="Datos">
			<table class="tabla_captura">
				<tr class="linea_off">
					<th align="left" scope="row">Compañía(s)</th>
					<td><input name="cias" type="text" class="valid toInterval" id="cias" size="40" /></td>
				</tr>
				<tr class="linea_on">
					<th align="left" scope="row">Administrador</th>
					<td><select name="admin" id="admin">
						<option value=""></option>
						<!-- START BLOCK : admin -->
						<option value="{value}">{text}</option>
						<!-- END BLOCK : admin -->
					</select></td>
				</tr>
				<tr class="linea_off">
					<th align="left" scope="row">Periodo</th>
					<td><select name="mes1" id="mes1">
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
					<input name="anio1" type="text" class="valid Focus toInt center" id="anio1" value="{anio}" size="4" maxlength="4" />
					al
					<select name="mes2" id="mes2">
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
					<input name="anio2" type="text" class="valid Focus toInt center" id="anio2" value="{anio}" size="4" maxlength="4" /></td>
				</tr>
			</table>
			<p>
				<input type="button" name="exportar" id="exportar" value="Exportar" />
				&nbsp;&nbsp;
				<input name="reporte" type="button" id="reporte" value="Reporte" />
			</p>
		</form>
	</div>
</div>
<script language="javascript" type="text/javascript" src="menus/{menucnt}"></script>
</body>
</html>