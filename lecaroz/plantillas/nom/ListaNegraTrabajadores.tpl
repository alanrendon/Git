<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Alta de Trabajadores en la Lista Negra</title>
<link href="/lecaroz/smarty/styles/layout.css" rel="stylesheet" type="text/css" />
<link href="/lecaroz/smarty/styles/tablas.css" rel="stylesheet" type="text/css" />
<link href="/lecaroz/styles/font-style.css" rel="stylesheet" type="text/css" />
<link href="/lecaroz/styles/FormValidator.css" rel="stylesheet" type="text/css" />
<link href="/lecaroz/styles/FormStyles.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="jscripts/mootools/mootools-1.2-core.js"></script>
<script type="text/javascript" src="jscripts/mootools/mootools-1.2-more.js"></script>
<script type="text/javascript" src="jscripts/mootools/String.implement.js"></script>
<script type="text/javascript" src="jscripts/mootools/Number.implement.js"></script>
<script type="text/javascript" src="jscripts/mootools/FormValidator.js"></script>
<script type="text/javascript" src="jscripts/mootools/FormStyles.js"></script>
<script type="text/javascript" src="jscripts/nom/ListaNegraTrabajadores.js"></script>
<script language="JavaScript" type="text/javascript" src="menus/stm31.js"></script>
<style type="text/css">
.hide {
	display: none;
}

.show {
	display: table-cell;
}
</style>
</head>

<body>
<div id="contenedor">
	<div id="titulo"> {titulo}</div>
	<div id="captura" align="center">
		<table class="tabla_captura">
			<tr>
				<th>Fecha</th>
				<th>Ap. Paterno</th>
				<th>Ap. Materno</th>
				<th>Nombre</th>
				<th>Tipo baja <img src="/lecaroz/iconos/arrow_{dir}_blue_round.png" alt="{dir}" name="display" width="16" height="16" id="display" /></th>
				<th class="{display}">Observaciones</th>
			</tr>
			<!-- START BLOCK : row -->
			<tr class="linea_{color}">
				<td align="center">{fecha}</td>
				<td>{ap_paterno}</td>
				<td>{ap_materno}</td>
				<td>{nombre}</td>
				<td>{tipo_baja}</td>
				<td class="{display}">{observaciones}</td>
			</tr>
			<!-- END BLOCK : row -->
		</table>
		<p>
			<input type="button" name="enviar" id="enviar" value="Enviar a panader&iacute;as" />
		</p>
	</div>
</div>
<script language="javascript" type="text/javascript" src="menus/{menucnt}"></script>
</body>
</html>