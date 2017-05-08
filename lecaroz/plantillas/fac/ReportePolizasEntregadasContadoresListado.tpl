<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Reporte de p&oacute;lizas entregadas a contadores</title>
<link href="styles/screen.css" rel="stylesheet" type="text/css" media="screen" />
<link href="styles/print.css" rel="stylesheet" type="text/css" media="print" />
<link href="styles/font-style.css" rel="stylesheet" type="text/css" />
<link href="/lecaroz/styles/screen.css" rel="stylesheet" type="text/css" media="screen" />
<link href="/lecaroz/styles/print.css" rel="stylesheet" type="text/css" media="print" />
<link href="/lecaroz/styles/font-style.css" rel="stylesheet" type="text/css" />
<link href="/lecaroz/styles/Tips.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="/lecaroz/jscripts/mootools1.4/mootools-core-1.4.5.js"></script>
<script type="text/javascript" src="/lecaroz/jscripts/mootools1.4/mootools-more-1.4.0.1.js"></script>
<script type="text/javascript" src="jscripts/fac/ReportePolizasEntregadasContadoresListado.js"></script>
<style type="text/css">
#info_tip {
	border-collapse: collapse;
	border: solid 1px #000;
	background-color: #fff;
}

#info_tip td,
#info_tip th {
	border: solid 1px #000;
}

#info_tip th {
	background-color: #999;
}
</style>
</head>

<body>
<!-- START BLOCK : reporte -->
<table width="98%" align="center" class="encabezado">
	<tr>
		<td width="20%" align="center">&nbsp;</td>
		<td width="60%" align="center">Reporte de p&oacute;lizas entregadas a contadores</td>
		<td width="20%" align="center">Folio: {folio}</td>
	</tr>
	<tr>
		<td width="20%" align="center">&nbsp;</td>
		<td width="60%" align="center">No. polizas: {polizas}, No. facturas: {facturas}</td>
		<td width="20%" align="center">&nbsp;</td>
	</tr>
</table>
<br />
<table align="center" class="print">
	<thead>
		<tr>
			<th class="print">Compa&ntilde;&iacute;a</th>
			<th class="print">Banco</th>
			<th class="print">Folio</th>
			<th class="print">Fecha<br />cheque</th>
			<th class="print">Beneficiario</th>
			<th class="print">Factura</th>
			<th class="print">Fecha<br />factura</th>
			<th class="print">Gasto</th>
			<th class="print">Importe</th>
		</tr>
	</thead>
	<!-- START BLOCK : poliza -->
	<!-- START BLOCK : factura -->
	<tr>
		<td class="print">{num_cia} {nombre_cia}</td>
		<td class="print">{banco}</td>
		<td class="print right">{folio}</td>
		<td class="print center">{fecha}</td>
		<td class="print">{num_pro} {nombre_pro}</td>
		<td class="print right">{num_fact}</td>
		<td class="print center">{fecha_fact}</td>
		<td class="print">{gasto} {nombre_gasto}</td>
		<td class="print right">{importe}</td>
	</tr>
	<!-- END BLOCK : factura -->
	<!-- START BLOCK : total -->
	<tr>
		<th class="print right" colspan="8">Total</th>
		<th class="print right">{total}</th>
	</tr>
	<!-- END BLOCK : total -->
	<tr>
		<td class="print" colspan="9">&nbsp;</td>
	</tr>
	<!-- END BLOCK : poliza -->
</table>
{salto}
<!-- END BLOCK : reporte -->
<p align="center" class="noDisplay">
	<input name="cerrar" type="button" id="cerrar" value="Cerrar" />
</p>
</body>
</html>
