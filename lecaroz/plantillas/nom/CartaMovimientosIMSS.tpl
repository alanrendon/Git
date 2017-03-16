<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Catálogo de productos de materia prima</title>
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
<script type="text/javascript" src="menus/stm31.js"></script>
<script type="text/javascript" src="jscripts/nom/CartaMovimientosIMSS.js"></script>
</head>

<body>
<div id="contenedor">
	<div id="titulo">Cartas de movimientos para altas y bajas del IMSS</div>
	<div id="captura" align="center">
		<form name="Datos" class="FormValidator FormStyles" id="Datos">
			<table class="tabla_captura">
				<tr class="linea_off">
					<th align="left" scope="row">Movimiento</th>
					<td><input name="tipo" type="radio" value="alta" checked="checked" />
						Altas<br />
						<input type="radio" name="tipo" value="baja" />
						Bajas</td>
				</tr>
				<tr class="linea_on">
					<th align="left" scope="row">Contador</th>
					<td><select name="idcontador" id="idcontador">
						<!-- START BLOCK : contador -->
						<option value="{value}">{text}</option>
						<!-- END BLOCK : contador -->
					</select></td>
				</tr>
			</table>
			<p>
				<input type="button" name="limpiar_altas" id="limpiar_altas" value="Limpiar altas" />
				&nbsp;&nbsp;
				<input type="button" name="limpiar_bajas" id="limpiar_bajas" value="Limpiar bajas" />
			&nbsp;&nbsp;
			<input type="button" name="generar" id="generar" value="Generar carta" />
			</p>
		</form>
	</div>
</div>
<script language="javascript" type="text/javascript" src="menus/{menucnt}"></script>
</body>
</html>
