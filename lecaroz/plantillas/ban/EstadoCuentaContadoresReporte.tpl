<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Estado de Cuenta</title>
<link href="/lecaroz/styles/screen.css" rel="stylesheet" type="text/css" media="screen" />
<link href="/lecaroz/styles/print.css" rel="stylesheet" type="text/css" media="print" />
<link href="/lecaroz/styles/font-style.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="jscripts/mootools/mootools-1.2-core.js"></script>
<script type="text/javascript" src="jscripts/mootools/mootools-1.2-more.js"></script>
<script type="text/javascript" src="jscripts/mootools/extensiones.js"></script>
<script type="text/javascript" src="jscripts/ban/ReporteEstadoCuenta.js"></script>
<style type="text/css" media="screen">
.Tip {
	background: #FF9;
	border: solid 1px #000;
	padding: 3px 5px;
}
.tip-title {
	font-weight: bold;
	font-size: 8pt;
	border-bottom: solid 2px #FC0;
	padding: 0 5px 3px 5px;
	margin-bottom: 3px;
}
.tip-text {
	font-weight: bold;
	font-size: 8pt;
	padding: 0 5px;
}
.info-table {
  border-collapse: collapse;
  border: solid 1px #000;
  background-color: #fff;
}
.info-table td,
.info-table th {
  border: solid 1px #000;
}
.info-table th {
  background-color: #999;
}
</style>

<!--<style type="text/css" media="print">
.Tip, .tip-title, .tip-text {
	display: none;
}
</style>-->

</head>

<body>
<!-- START BLOCK : reporte -->
<table width="98%" align="center" class="encabezado">
	<tr>
		<td width="15%" class="font14">{num_cia}</td>
		<td width="70%" align="center" class="font14">{nombre_cia}</td>
		<td width="15%" align="right" class="font14">{num_cia}</td>
	</tr>
	<tr>
		<td class="font8">{fecha}</td>
		<td align="center">Estado de Cuenta </td>
		<td align="right" class="font8">{hora}</td>
	</tr>
</table>
<br />
<!-- START BLOCK : saldos_ini -->
<table width="100%" style="border-collapse:collapse;">
	<tr>
		<th colspan="3" align="left" class="print font12">Saldo Inicial </th>
	</tr>
	<tr>
		<th class="print font10">Banco</th>
		<th class="print font10">Cuenta</th>
		<th class="print font10">Saldo Banco </th>
	</tr>
	<!-- START BLOCK : banco_ini -->
	<tr>
		<td align="left" class="print bold font14"><img src="imagenes/{logo_banco}" width="16" height="16" /> {banco}</td>
		<td align="center" class="print bold font14">{cuenta}</td>
		<td align="right" class="print bold font14 blue">{saldo_banco}</td>
	</tr>
	<!-- END BLOCK : banco_ini -->
	<!-- START BLOCK : total_ini -->
	<tr>
		<th colspan="2" align="right" class="print font10">Total Inicial </th>
		<th align="right" class="print font14 blue">{saldo_banco}</th>
	</tr>
	<!-- END BLOCK : total_ini -->
</table>
<br />
<!-- END BLOCK : saldos_ini -->
<table width="100%" align="center" class="print">
	<!-- START BLOCK : cuentas -->
	<!-- START BLOCK : cuenta -->
	<tr>
		<th colspan="7" align="left" class="print font14">{banco}</th>
		<th colspan="3" class="print font14">{cuenta}</th>
	</tr>
	<!-- END BLOCK : cuenta -->
	<tr>
		<td colspan="10" class="print">&nbsp;</td>
	</tr>
	<!-- END BLOCK : cuentas -->
	<tr>
		<!-- START BLOCK : th_banco -->
		<th class="print">Banco</th>
		<!-- END BLOCK : th_banco -->
		<th colspan="2" class="print">Fecha</th>
		<th class="print">Conciliado</th>
		<th class="print">Depositos</th>
		<th class="print">Cargo</th>
		<th class="print">Folio</th>
		<th class="print">Beneficiario</th>
		<th class="print">Concepto</th>
		<th class="print">C&oacute;digo</th>
	</tr>
	<!-- START BLOCK : row -->
	<tr id="row">
		<!-- START BLOCK : td_banco -->
		<td align="center" class="print"><img src="imagenes/{logo_banco}" alt="{nombre_banco}" width="16" height="16" longdesc="{nombre_banco}" title="{nombre_banco}" /></td>
		<!-- END BLOCK : td_banco -->
		<td colspan="2" align="center" class="print">{fecha}</td>
		<td align="center" class="print orange"><a class="info" data-tooltip="{info}">{conciliado}</a></td>
		<td align="right" class="print blue">{deposito}</td>
		<td align="right" class="print red">{cargo}</td>
		<td align="center" class="print">{folio}</td>
		<td class="print">{beneficiario}</td>
		<td class="print">{concepto}</td>
		<td class="print">{codigo}</td>
	</tr>
	<!-- END BLOCK : row -->
	<!-- START BLOCK : totales -->
	<tr>
		<th colspan="4" align="right" class="print font10">Totales</th>
		<th align="right" class="print font10 blue">{depositos}</th>
		<th align="right" class="print font10 red">{cargos}</th>
		<th colspan="4" class="print">&nbsp;</th>
	</tr>
	<!-- END BLOCK : totales -->
</table>
<!-- START BLOCK : saldos_fin -->
<br />
<table width="100%" style="border-collapse:collapse;">
	<tr>
		<th colspan="3" align="left" class="print font12">Saldo al Corte </th>
	</tr>
	<tr>
		<th class="print font10">Banco</th>
		<th class="print font10">Cuenta</th>
		<th class="print font10">Saldo Banco </th>
	</tr>
	<!-- START BLOCK : banco_fin -->
	<tr>
		<td align="left" class="print bold font14"><img src="imagenes/{logo_banco}" width="16" height="16" /> {banco}</td>
		<td align="center" class="print bold font14">{cuenta}</td>
		<td align="right" class="print bold font14 blue">{saldo_banco}</td>
	</tr>
	<!-- END BLOCK : banco_fin -->
	<!-- START BLOCK : total_fin -->
	<tr>
		<th colspan="2" align="right" class="print font10">Total al Corte </th>
		<th align="right" class="print font14 blue">{saldo_banco}</th>
	</tr>
	<!-- END BLOCK : total_fin -->
</table>
<!-- END BLOCK : saldos_fin -->
{salto}
<!-- END BLOCK : reporte -->
<p align="center" class="noDisplay">
	<input name="cerrar" type="button" id="cerrar" value="Cerrar" />
</p>
</body>
</html>
