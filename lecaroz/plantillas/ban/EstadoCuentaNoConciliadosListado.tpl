<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Depósitos de renta</title>
<link href="styles/screen.css" rel="stylesheet" type="text/css" media="screen" />
<link href="styles/print.css" rel="stylesheet" type="text/css" media="print" />
<link href="styles/font-style.css" rel="stylesheet" type="text/css" />
<link href="/lecaroz/styles/screen.css" rel="stylesheet" type="text/css" media="screen" />
<link href="/lecaroz/styles/print.css" rel="stylesheet" type="text/css" media="print" />
<link href="/lecaroz/styles/font-style.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="jscripts/mootools/mootools-1.2-core.js"></script>
<script type="text/javascript" src="jscripts/ban/RentasDepositosReporte.js"></script>
</head>

<body>
<!-- START BLOCK : reporte -->
<table width="98%" align="center" class="encabezado">
	<tr>
		<td align="center">Oficinas Administrativas Mollendo, S. de R.L. de C.V.</td>
	</tr>
	<tr>
		<td align="center">Movimientos no concilados</td>
	</tr>
</table>
<br />
<table align="center" class="print">
	<tbody>
		<!-- START BLOCK : cia -->
		<tr>
			<th colspan="8" align="left" class="print font10" scope="col">{num_cia} {nombre_cia}{cuentas}</th>
		</tr>
		<tr>
			<th class="print">Banco</th>
			<th class="print">Fecha</th>
			<th class="print">Deposito</th>
			<th class="print">Cargo</th>
			<th class="print">Folio</th>
			<th class="print">Beneficiario</th>
			<th class="print">Concepto</th>
			<th class="print">Código</th>
		</tr>
		<!-- START BLOCK : row -->
		<tr>
			<td align="center" class="print"><img src="/lecaroz/imagenes/{banco}16x16.png" width="16" height="16" /></td>
			<td align="center" class="print green">{fecha}</td>
			<td align="right" class="print blue">{deposito}</td>
			<td align="right" class="print red">{cargo}</td>
			<td align="right" class="print">{folio}</td>
			<td class="print">{beneficiario}</td>
			<td class="print">{concepto}</td>
			<td class="print">{codigo}</td>
		</tr>
		<!-- END BLOCK : row -->
		<tr>
			<th colspan="2" align="right" class="print">Total</th>
			<th align="right" class="print"><span class="blue font10">{depositos}</span></th>
			<th align="right" class="print"><span class="red font10">{cargos}</span></th>
			<th colspan="4" align="right" class="print">&nbsp;</th>
		</tr>
		<tr>
			<td colspan="8" align="center" class="print">&nbsp;</td>
		</tr>
		<!-- END BLOCK : cia -->
	</tbody>
</table>
{salto} 
<!-- END BLOCK : reporte -->
<p align="center" class="noDisplay">
	<input name="cerrar" type="button" id="cerrar" value="Cerrar" />
</p>
</body>
</html>
