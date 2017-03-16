<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Importes para separar de depósitos (días festivos)</title>

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

<script type="text/javascript" src="jscripts/cometra/ImportesSeparacionFestivos.js"></script>
</head>

<body>
<div id="contenedor">
	<div id="titulo">Importes para separar de depósitos (días festivos)</div>
	<div id="captura" align="center">
		<form action="" method="post" name="Datos" class="FormValidator FormStyles" id="Datos">
			<table class="tabla_captura">
				<tr>
					<th scope="col">Día festivo</th>
				</tr>
				<tr>
					<td align="center"><select name="dia_festivo" id="dia_festivo">
						<!-- START BLOCK : dia_festivo -->
						<option value="{value}">{text}</option>
						<!-- END BLOCK : dia_festivo -->
					</select></td>
				</tr>
			</table>
			<br />
			<table class="tabla_captura">
				<tr>
					<th scope="col">Compañía</th>
					<th scope="col">Importe</th>
					<th scope="col">%</th>
					<th scope="col">Promedio</th>
					<th scope="col">Días</th>
				</tr>
				<!-- START BLOCK : row -->
				<tr class="linea_{row_color}">
					<td><input name="num_cia[]" type="hidden" id="num_cia" value="{num_cia}" />
					{num_cia} {nombre_cia}</td>
					<td align="center"><input name="importe[]" type="text" class="valid Focus numberPosFormat right blue" id="importe" value="{importe}" size="10" precision="2" /></td>
					<td align="center"><input name="porcentaje[]" type="text" class="valid Focus numberPosFormat right red" id="porcentaje" value="{porcentaje}" size="5" precision="2" /></td>
					<td align="right" class="blue">{promedio}</td>
					<td align="right">{dias}</td>
				</tr>
				<!-- END BLOCK : row -->
			</table>
			<p>
				<input type="button" name="actualizar" id="actualizar" value="Actualizar" />
			</p>
		</form>
	</div>
</div>
<script language="javascript" type="text/javascript" src="menus/{menucnt}"></script>
</body>
</html>
