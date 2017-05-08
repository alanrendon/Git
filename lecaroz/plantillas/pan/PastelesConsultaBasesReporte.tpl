<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Pedidos</title>
<link href="/lecaroz/styles/screen.css" rel="stylesheet" type="text/css" media="screen" />
<link href="/lecaroz/styles/print.css" rel="stylesheet" type="text/css" media="print" />
<link href="/lecaroz/styles/font-style.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="jscripts/mootools/mootools-1.2-core.js"></script>
<script type="text/javascript" src="jscripts/mootools/extensiones.js"></script>
<script type="text/javascript" src="jscripts/pan/PastelesConsultaEntregasReporte.js"></script>
<style type="text/css">
.cancelada {
	background-color: #e6b8b7;
}
</style>
</head>

<body>
<!-- START BLOCK : reporte -->
<table width="98%" align="center" class="encabezado">
	<tr>
		<td align="center">Reporte de devoluciones de base de pastel</td>
	</tr>
</table>
<br />
<table align="center" class="print">
	<!-- START BLOCK : cia -->
	<tr>
		<th colspan="2" align="left" class="print font12" scope="col">{num_cia} {nombre_cia}</th>
	</tr>
	<tr>
		<td colspan="2" class="print bold font10">&nbsp;</td>
	</tr>
	<!-- START BLOCK : dia -->
	<tr>
		<th colspan="2" align="left" class="print font10">{dia_semana} {dia} de {mes} de {anio}</th>
	</tr>
	<tr>
		<th class="print">Remisi&oacute;n</th>
		<th class="print">Importe</th>
	</tr>
	<!-- START BLOCK : row -->
	<tr {cancelada}>
		<td align="right" class="print">{remision}</td>
		<td align="right" class="print green">{importe}</td>
	</tr>
	<!-- END BLOCK : row -->
	<tr>
		<th align="right" class="print font10">Totales</th>
		<th align="right" class="print font10">{total}</th>
	</tr>
	<tr>
		<td colspan="2" class="print">&nbsp;</td>
	</tr>
	<!-- END BLOCK : dia -->
	<!-- END BLOCK : cia -->
</table>
{salto} 
<!-- END BLOCK : reporte -->
<p align="center" class="noDisplay">
	<input name="cerrar" type="button" id="cerrar" value="Cerrar" />
</p>
</body>
</html>
