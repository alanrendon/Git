<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Alta de Trabajadores en la Lista Negra</title>
<link href="../../smarty/styles/layout.css" rel="stylesheet" type="text/css" />
<link href="../../smarty/styles/tablas.css" rel="stylesheet" type="text/css" />
<link href="../../styles/font-style.css" rel="stylesheet" type="text/css" />
<link href="../../styles/FormValidator.css" rel="stylesheet" type="text/css" />
<link href="../../styles/FormStyles.css" rel="stylesheet" type="text/css" />
<link href="smarty/styles/layout.css" rel="stylesheet" type="text/css" />
<link href="smarty/styles/tablas.css" rel="stylesheet" type="text/css" />
<link href="styles/font-style.css" rel="stylesheet" type="text/css" />
<link href="styles/FormValidator.css" rel="stylesheet" type="text/css" />
<link href="styles/FormStyles.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="jscripts/mootools/mootools-1.2-core.js"></script>
<script type="text/javascript" src="jscripts/mootools/mootools-1.2-more.js"></script>
<script type="text/javascript" src="jscripts/mootools/String.implement.js"></script>
<script type="text/javascript" src="jscripts/mootools/Number.implement.js"></script>
<script type="text/javascript" src="jscripts/mootools/FormValidator.js"></script>
<script type="text/javascript" src="jscripts/mootools/FormStyles.js"></script>
<script type="text/javascript" src="jscripts/nom/ListaNegraAlta.js"></script>
<script language="JavaScript" type="text/javascript" src="menus/stm31.js"></script>
</head>

<body>
<div id="contenedor">
	<div id="titulo"> Alta de Trabajadores en la Lista Negra </div>
	<div id="captura" align="center">
		<form name="Datos" class="FormValidator FormStyles" id="Datos">
			<table class="tabla_captura">
				<tr>
					<th scope="row">Nombre</th>
					<th scope="row">Ap. Paterno</th>
					<th scope="row">Ap. Materno</th>
					<th scope="row">Tipo baja</th>
					<th>Observaciones</th>
				</tr>
				<tbody id="TablaCaptura">
					<tr class="linea_off">
						<td valign="top" scope="row"><input name="nombre[]" type="text" class="valid onlyText cleanText toUpper" id="nombre" size="30" maxlength="200" /></td>
						<td valign="top" scope="row"><input name="ap_paterno[]" type="text" class="valid onlyText cleanText toUpper" id="ap_paterno" size="30" maxlength="200" /></td>
						<td valign="top" scope="row"><input name="ap_materno[]" type="text" class="valid onlyText cleanText toUpper" id="ap_materno" size="30" maxlength="200" /></td>
						<td valign="top" scope="row"><select name="tipo_baja[]" id="tipo_baja">
								<!-- START BLOCK : tipo_baja -->
								<option value="{value}">{text}</option>
								<!-- END BLOCK : tipo_baja -->
							</select></td>
						<td valign="top"><textarea name="observaciones[]" cols="50" rows="3" class="valid toText cleanText toUpper" id="observaciones"></textarea></td>
					</tr>
				</tbody>
			</table>
			<p>
				<input name="alta" type="button" id="alta" value="Alta"{disabled} />
			</p>
		</form>
	</div>
</div>
<script language="javascript" type="text/javascript" src="menus/{menucnt}"></script>
</body>
</html>