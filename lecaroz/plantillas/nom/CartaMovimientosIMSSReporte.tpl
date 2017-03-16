<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Listado de Trabajadores</title>
<link href="/lecaroz/styles/screen.css" rel="stylesheet" type="text/css" media="screen" />
<link href="/lecaroz/styles/print.css" rel="stylesheet" type="text/css" media="print" />
<link href="/lecaroz/styles/font-style.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="jscripts/mootools/mootools-1.2-core.js"></script>
<script type="text/javascript" src="jscripts/nom/TrabajadoresConsultaAdminReporte.js"></script>
</head>

<body>
<!-- START BLOCK : reporte -->
<p class="center font14"><strong>Oficinas Administrativas Mollendo S. de R.L. de C.V.</strong></p>
<p>&nbsp;</p>
<p class="right"><strong>México, D.F., a {dia} de {mes} de {anio}</strong></p>
<p>&nbsp;</p>
<p><strong>C.P. {contador}<br />
Presente</strong></p>
<p>&nbsp;</p>
<p class="justify">Por medio de la presente me permito saludarle y a su vez recordarle se den de <strong>{tipo}</strong> en el I.M.S.S. los trabajadores que a continuación se listan:</p>
<table width="98%" align="center">
	<!-- START BLOCK : row -->
	<tr>
		<td width="50%">{nombre_trabajador}</td>
		<td width="50%">{nombre_cia}</td>
	</tr>
	<tr>
		<td colspan="2">&nbsp;</td>
	</tr>
	<!-- END BLOCK : row -->
</table>
<p>Quedo a sus órdenes.</p>
<p>&nbsp;</p>
<p>&nbsp;</p>
<p class="center"><strong>_________________________________<br />
	Firma
</strong></p>
<p><!-- END BLOCK : reporte --></p>
<p align="center" class="noDisplay">
	<input name="cerrar" type="button" id="cerrar" value="Cerrar" />
</p>
</body>
</html>
