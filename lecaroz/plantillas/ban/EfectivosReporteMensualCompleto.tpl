<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Reporte de efectivos mensuales</title>
<link href="/lecaroz/styles/screen.css" rel="stylesheet" type="text/css" media="screen" />
<link href="/lecaroz/styles/print.css" rel="stylesheet" type="text/css" media="print" />
<link href="/lecaroz/styles/font-style.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="jscripts/mootools/mootools-1.2-core.js"></script>
<script type="text/javascript" src="jscripts/mootools/Array.implement.js"></script>
<script type="text/javascript" src="jscripts/ban/EfectivosReporteMensualCompleto.js"></script>
<style id="screen-style" type="text/css" media="screen">
.hoja {
	width: 100%;
}

.reporte1 {
}

.tabla {
	width: 100%;
}

.celda {
	font-size: 12pt;
	border: 1px solid white;
}

.lcelda {
	border-left: 1px solid black;
}

.rcelda {
	border-right: 1px solid black;
}

.bcelda {
	border-bottom: 1px solid black;
}

.celda_normal {
	font-size: 12pt;
	border-top: none;
	border-bottom: none;
}

.cabecera {
	font-size: 12pt;
}

.columna1 {
	float: left;
}

.columna2 {
	float: left;
}
</style>
<style id="print-style" type="text/css" media="print">
body {
	margin-left: 15mm;
}

body, th, td {
	font-size: 8pt;
}

.hoja {
	width: 200mm;
	min-height: 260mm;
}

.reporte1 {
	margin-bottom: 4mm;
}

.reporte2 {
}

.tabla {
	width: 100%;
}

.celda {
	border: 1px solid white;
	padding: 0 2mm;
}

.lcelda {
	border-left: 1px solid black;
	padding: 0 2mm;
}

.rcelda {
	border-right: 1px solid black;
	padding: 0 2mm;
}

.bcelda {
	border-bottom: 1px solid black;
	padding: 0 2mm;
}

.celda_normal {
	border-top: none;
	border-bottom: none;
	padding: 0 2mm;
}

.cabecera {
	font-size: 10pt;
	padding: 0 2mm;
}

.columna1 {
	float: left;
	width: 118mm;
}

.columna2 {
	float: left;
	width: 79mm;
	margin-left: 2mm;
}
</style>
</head>

<body>
<!-- START BLOCK : hoja -->
<div class="hoja"> 
	<!-- START BLOCK : reporte -->
	<div class="reporte1">
		<table align="center" class="print tabla">
			<tr>
				<th colspan="7" class="print font14" scope="col"><span style="float:left;">{num_cia}</span><span style="float:right;">{num_cia}</span><br />
					{nombre}<br />
					{alias}<br />
					<span style="float:right;" class="font8">{periodo}</span></th>
			</tr>
			<tr>
				<th class="print cabecera">Día</th>
				<th class="print cabecera">Efectivo</th>
				<th class="print cabecera">Depósito</th>
				<th class="print cabecera">Mayoreo</th>
				<th class="print cabecera">Oficina</th>
				<th class="print cabecera">Diferencia</th>
				<th class="print cabecera">Total</th>
			</tr>
			<!-- START BLOCK : row -->
			<tr>
				<td align="center" class="celda lcelda bolder {bcelda}">{dia}</td>
				<td align="right" class="celda bolder green {bcelda}">{efectivo}</td>
				<td align="right" class="celda bolder blue {bcelda}">{deposito}</td>
				<td align="right" class="celda bolder blue {bcelda}">{mayoreo}</td>
				<td align="right" class="celda bolder yellow {bcelda}">{oficina}</td>
				<td align="right" class="celda bolder {color_diferencia} {bcelda}">{diferencia}</td>
				<td align="right" class="celda rcelda bolder blue {bcelda}">{total}</td>
			</tr>
			<!-- END BLOCK : row -->
			<tr>
				<th align="right" class="print cabecera">Tot.</th>
				<th align="right" class="print cabecera">{efectivo}</th>
				<th align="right" class="print cabecera blue">{deposito}</th>
				<th align="right" class="print cabecera blue">{mayoreo}</th>
				<th align="right" class="print cabecera yellow">{oficina}</th>
				<th align="right" class="print cabecera {color_diferencia}">{diferencia}</th>
				<th align="right" class="print cabecera blue">{total}</th>
			</tr>
			<tr>
				<th align="right" class="print cabecera">Prom.</th>
				<th align="right" class="print cabecera">{pefectivo}</th>
				<th align="right" class="print cabecera blue">{pdeposito}</th>
				<th align="right" class="print cabecera blue">{pmayoreo}</th>
				<th align="right" class="print cabecera yellow">{poficina}</th>
				<th align="right" class="print cabecera">&nbsp;</th>
				<th align="right" class="print cabecera blue">{ptotal}</th>
			</tr>
		</table>
	</div>
	<div id="reporte2" style="margin-top:20px;">
		<div class="columna1">
			<table width="100%" class="print">
				<tr>
					<td align="right" class="print cabecera bold" style="border-bottom:none;" scope="row">Porcentaje de depósitos / efectivo</td>
					<td align="right" class="print cabecera bold" style="border-bottom:none;">{porcentaje_depositos}</td>
				</tr>
				<tr>
					<td align="right" class="print cabecera bold" style="border-top:none; border-bottom:none;" scope="row">Porcentaje de oficina / efectivo</td>
					<td align="right" class="print cabecera bold" style="border-top:none; border-bottom:none;">{porcenatje_oficina}</td>
				</tr>
				<tr>
					<td align="right" class="print cabecera bold" style="border-top:none; border-bottom:none;" scope="row">Suma de porcentajes</td>
					<td align="right" class="print cabecera bold" style="border-top:none; border-bottom:none;">{suma_porcentajes}</td>
				</tr>
				<tr>
					<td align="right" class="print cabecera bold" style="border-top:none; border-bottom:none;" scope="row">Total de efectivo</td>
					<td align="right" class="print cabecera bold" style="border-top:none; border-bottom:none;">{efectivo}</td>
				</tr>
				<tr>
					<td align="right" class="print cabecera bold" style="border-top:none; border-bottom:none;" scope="row">Total de gastos</td>
					<td align="right" class="print cabecera bold" style="border-top:none; border-bottom:none;">{total_gastos}</td>
				</tr>
				<tr>
					<td align="right" class="print cabecera bold" style="border-top:none; border-bottom:none;" scope="row">Gastos pagados</td>
					<td align="right" class="print cabecera bold" style="border-top:none; border-bottom:none;">{total_egresos}</td>
				</tr>
				<tr>
					<td align="right" class="print cabecera bold" style="border-top:none; border-bottom:none;" scope="row">Gastos retirados</td>
					<td align="right" class="print cabecera bold" style="border-top:none; border-bottom:none;">{total_ingresos}</td>
				</tr>
			</table>
			<br />
			<table width="100%" class="print">
				<tr>
					<th colspan="4" class="print font20" style="letter-spacing:20pt;" scope="col">GASTOS</th>
				</tr>
				<tr>
					<th class="print celda_normal">#</th>
					<th class="print celda_normal">Concepto</th>
					<th class="print celda_normal">Ingreso</th>
					<th class="print celda_normal">Egreso</th>
				</tr>
				<tr>
					<td align="right" class="print celda_normal">{num}</td>
					<td class="print celda_normal">{concepto}</td>
					<td align="right" class="print celda_normal blue">{ingreso}</td>
					<td align="right" class="print celda_normal red">{egreso}</td>
				</tr>
				<tr>
					<td align="right" class="print celda_normal">{}</td>
					<td class="print celda_normal">&nbsp;</td>
					<td align="right" class="print celda_normal blue">&nbsp;</td>
					<td align="right" class="print celda_normal red">&nbsp;</td>
				</tr>
				<tr>
					<th colspan="2" align="right" class="print celda_normal">Subtotal..........$</th>
					<th align="right" class="print celda_normal blue">{ingresos}</th>
					<th align="right" class="print celda_normal red">{egresos}</th>
				</tr>
				<tr>
					<th colspan="2" align="right" class="print celda_normal">Gran total..........$</th>
					<th colspan="2" class="print celda_normal">{gran_total}</th>
				</tr>
			</table>
		</div>
		<div class="columna2">
			<table width="100%" class="print">
				<tr>
					<td class="print cabecera bold" style="border-top:none; border-bottom:none;" scope="row">{concepto1}</td>
					<td align="right" class="print cabecera bold" style="border-top:none; border-bottom:none;">{importe1}</td>
				</tr>
			</table>
		</div>
	</div>
	<!-- END BLOCK : reporte --> 
</div>
{salto} 
<!-- END BLOCK : hoja -->
<p align="center" class="noDisplay">
	<input name="cerrar" type="button" id="cerrar" value="Cerrar" />
</p>
</body>
</html>
