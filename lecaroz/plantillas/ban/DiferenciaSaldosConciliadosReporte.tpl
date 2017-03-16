<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Diferencia de Saldos Conciliados</title>
<link href="/lecaroz/styles/screen.css" rel="stylesheet" type="text/css" media="screen" />
<link href="/lecaroz/styles/print.css" rel="stylesheet" type="text/css" media="print" />
<link href="/lecaroz/styles/font-style.css" rel="stylesheet" type="text/css" />

<script type="text/javascript" src="jscripts/mootools/mootools-1.2-core.js"></script>
<script type="text/javascript" src="jscripts/fac/FacturasElectronicasConsultaReporte.js"></script>
</head>

<body>
<!-- START BLOCK : reporte -->
<table width="98%" align="center" class="encabezado">
  <tr>
    <td align="center">DIFERENCIA DE SALDOS CONCILIADOS ({banco})<br />
    	{dia_escrito} {dia} DE {mes_escrito} DE {anio}, {hora}</td>
  </tr>
</table>
<br />
<table width="95%%" align="center" class="print">
	<tr>
		<th class="print" scope="col">#</th>
		<th class="print" scope="col">Compa&ntilde;&iacute;a</th>
		<th class="print" scope="col">Cuenta</th>
		<th class="print" scope="col">Saldo en<br />
		sistema</th>
		<th class="print" scope="col">Dep&oacute;sitos<br />
			pendientes</th>
		<th class="print" scope="col">Cargos<br />
		pendientes</th>
		<th class="print" scope="col">Saldo<br />
		total</th>
		<th class="print" scope="col">Saldo en<br />
			Banco</th>
		<th class="print" scope="col">Diferencia</th>
	</tr>
	<!-- START BLOCK : row -->
	<tr id="row">
		<td align="right" class="print">{num_cia}</td>
		<td class="print">{nombre_cia}</td>
		<td align="center" class="print">{cuenta}</td>
		<td align="right" class="print blue bold">{saldo_sistema}</td>
		<td align="right" class="print blue">{depositos_pendientes}</td>
		<td align="right" class="print red">{cargos_pendientes}</td>
		<td align="right" class="print bold {color_saldo_total}">{saldo_total}</td>
		<td align="right" class="print green bold">{saldo_banco}</td>
		<td align="right" class="print bold {color_diferencia}">{diferencia}</td>
	</tr>
	<!-- END BLOCK : row -->
	<tr>
		<th colspan="3" align="right" class="print">Totales</th>
		<th align="right" class="print blue">{saldo_sistema}</th>
		<th align="right" class="print blue">{depositos_pendientes}</th>
		<th align="right" class="print red">{cargos_pendientes}</th>
		<th align="right" class="print">{saldo_total}</th>
		<th align="right" class="print green">{saldo_banco}</th>
		<th align="right" class="print">{diferencia}</th>
	</tr>
</table>
<br class="page-break" />
<!-- END BLOCK : reporte -->
<p align="center" class="noDisplay">
	<input name="cerrar" type="button" id="cerrar" value="Cerrar" />
</p>
</body>
</html>
