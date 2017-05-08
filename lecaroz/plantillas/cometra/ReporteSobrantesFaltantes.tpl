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
		<td align="center">Reporte de sobrantes, faltantes y falsos<br /></td>
	</tr>
</table>
<br />
<table align="center" class="print">
	<!-- START BLOCK : tipo -->
	<tr>
		<th colspan="6" class="print font12" scope="col">{tipo}</th>
	</tr>
	<tr>
		<th class="print" scope="col">Compañía</th>
		<th class="print" scope="col">Cuenta</th>
		<th class="print" scope="col">Fecha</th>
		<th class="print" scope="col">Concepto</th>
		<th class="print" scope="col">Comprobante</th>
		<th class="print" scope="col">Importe</th>
	</tr>
	<!-- START BLOCK : row -->
	<tr>
		<td class="print">{num_cia} {nombre_cia}</td>
		<td align="center" class="print">{cuenta}</td>
		<td align="center" class="print">{fecha}</td>
		<td class="print">{concepto}</td>
		<td align="center" class="print">{comprobante}</td>
		<td align="right" class="print">{importe}</td>
	</tr>
	<!-- END BLOCK : row -->
	<tr>
		<th colspan="5" align="right" class="print font12">Total</th>
		<th align="right" class="print font12">{total}</th>
	</tr>
	<tr>
		<td colspan="6" class="print">&nbsp;</td>
	</tr>
	<!-- END BLOCK : tipo -->
</table>
{salto} 
<!-- END BLOCK : reporte -->
<p align="center" class="noDisplay">
	<input name="cerrar" type="button" id="cerrar" value="Cerrar" />
</p>
</body>
</html>
