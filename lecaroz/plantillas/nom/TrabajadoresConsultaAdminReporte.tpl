<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Listado de Trabajadores</title>
<link href="styles/screen.css" rel="stylesheet" type="text/css" media="screen" />
<link href="styles/print.css" rel="stylesheet" type="text/css" media="print" />
<link href="styles/font-style.css" rel="stylesheet" type="text/css" />
<link href="/lecaroz/styles/screen.css" rel="stylesheet" type="text/css" media="screen" />
<link href="/lecaroz/styles/print.css" rel="stylesheet" type="text/css" media="print" />
<link href="/lecaroz/styles/font-style.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="jscripts/mootools/mootools-1.2-core.js"></script>
<script type="text/javascript" src="jscripts/nom/TrabajadoresConsultaAdminReporte.js"></script>
</head>

<body>
<!-- START BLOCK : reporte -->
<table width="98%" align="center" class="encabezado">
	<tr>
		<td width="15%" align="center">{num_cia}</td>
		<td width="70%" align="center">{nombre_cia}</td>
		<td width="15%" align="center">{num_cia}</td>
	</tr>
	<tr>
		<td colspan="3" align="center">Listado de Trabajadores al {fecha}{continuacion}</td>
	</tr>
</table>
<br />
<table width="98%" align="center" class="print">
	<tr>
		<th width="5%" class="print">#</th>
		<th width="35%" class="print">Nombre</th>
		<th width="15%" class="print">Puesto</th>
		<th width="15%" class="print">Turno</th>
		<th width="15%" class="print">Antigüedad</th>
		<th width="15%" class="print">Aguinaldo</th>
	</tr>
	<!-- START BLOCK : row -->
	<tr id="row">
		<td height="40" align="right" class="print">{num_emp}</td>
		<td nowrap="nowrap" class="print">{nombre_trabajador}</td>
		<td nowrap="nowrap" class="print">{puesto}</td>
		<td nowrap="nowrap" class="print">{turno}</td>
		<td align="center" nowrap="nowrap" class="print">{antiguedad}</td>
		<td align="right" class="print">{aguinaldo}</td>
	</tr>
	<!-- END BLOCK : row -->
	<!-- START BLOCK : trabajadores -->
	<tr>
		<th colspan="5" align="right" class="print">Número de trabajadores</th>
		<th align="right" class="print">{trabajadores}</th>
	</tr>
	<!-- END BLOCK : trabajadores -->
</table>
{salto} 
<!-- END BLOCK : reporte -->
<p align="center" class="noDisplay">
	<input name="cerrar" type="button" id="cerrar" value="Cerrar" />
</p>
</body>
</html>
