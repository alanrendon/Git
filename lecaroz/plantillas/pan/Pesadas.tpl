<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Pesadas</title>

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

<script type="text/javascript" src="jscripts/pan/Pesadas.js"></script>
</head>

<body>
<div id="contenedor">
	<div id="titulo">Pesadas</div>
	<div id="captura" align="center">
		<form name="Datos" class="FormValidator FormStyles" id="Datos">
			<table class="tabla_captura">
				<tr>
					<th scope="col">Compañía</th>
					<th scope="col">F.D.</th>
					<th scope="col">F.N.</th>
					<th scope="col">Biz.</th>
					<th scope="col">Rep.</th>
				</tr>
				<!-- START BLOCK : row -->
				<tr class="linea_{color}" id="row">
					<td><input name="num_cia[]" type="hidden" id="num_cia" value="{num_cia}" />
					{num_cia} {nombre_cia}</td>
					<td align="center"><input name="pesada_1[]" type="text" class="valid Focus numberPosFormat right" precision="2" id="pesada_1" value="{pesada_1}" size="8" /></td>
					<td align="center"><input name="pesada_2[]" type="text" class="valid Focus numberPosFormat right" precision="2" id="pesada_2" value="{pesada_2}" size="8" /></td>
					<td align="center"><input name="pesada_3[]" type="text" class="valid Focus numberPosFormat right" precision="2" id="pesada_3" value="{pesada_3}" size="8" /></td>
					<td align="center"><input name="pesada_4[]" type="text" class="valid Focus numberPosFormat right" precision="2" id="pesada_4" value="{pesada_4}" size="8" /></td>
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
