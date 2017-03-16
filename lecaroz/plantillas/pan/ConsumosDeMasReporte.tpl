<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Consumos de más</title>
<link href="/lecaroz/styles/screen.css" rel="stylesheet" type="text/css" media="screen" />
<link href="/lecaroz/styles/print.css" rel="stylesheet" type="text/css" media="print" />
<link href="/lecaroz/styles/font-style.css" rel="stylesheet" type="text/css" />

<script type="text/javascript" src="jscripts/mootools/mootools-1.2-core.js"></script>
<script type="text/javascript" src="jscripts/mootools/mootools-1.2-more.js"></script>
<script type="text/javascript" src="jscripts/mootools/extensiones.js"></script>
<script type="text/javascript" src="jscripts/pan/ConsumosDeMasReporte.js"></script>

<style type="text/css" media="screen">
.Reporte1, .Reporte2 {
	height: auto;
}

table {
	border-collapse: collapse;
}

th {
	font-size: 10pt;
	border: solid 1px #000;
	background-color: #73A8B7;
}

td {
	font-size: 10pt;
	border: solid 1px #000;
}

td.bold {
	font-weight: bold;
}

td.blue {
	color: #00C;
}

th.blue {
	color: #00C;
}
</style>

<style type="text/css" media="print">
.Reporte1 {
	height: 130mm;
	border-bottom: dashed 1px #999;
	margin-bottom: 5mm;
}

.Reporte2 {
	height: 270mm;
}

table {
	border-collapse: collapse;
}

th {
	font-size: 7pt;
}

td {
	font-size: 7pt;
}

td.bold {
	font-weight: bold;
}

td.blue {
	color: #00C;
}

th.blue {
	color: #00C;
}
</style>

</head>

<body>
<!-- START BLOCK : reporte -->
<div class="Reporte1">
	<div class="NombreCia" align="center">{num_cia} {nombre_cia} </div>
	<div class="NombreReporte" align="center">CONSUMOS DE MAS AL {dia} DE {mes} DE {anio}</div>
	<div class="Datos">
		<table width="99%" align="center">
			<tr>
				<th rowspan="2" scope="col">Producto</th>
				<th colspan="4" scope="col">Frances de día</th>
				<th colspan="4" scope="col">Frances de noche</th>
				<th colspan="4" scope="col">Bizcochero</th>
				<th colspan="4" scope="col">Repostero</th>
				<th colspan="4" scope="col">Piconero</th>
			</tr>
			<tr>
				<th>Aut.</th>
				<th>Con.</th>
				<th>Exc.</th>
				<th>Costo</th>
				<th>Aut.</th>
				<th>Con.</th>
				<th>Exc.</th>
				<th>Costo</th>
				<th>Aut.</th>
				<th>Con.</th>
				<th>Exc.</th>
				<th>Costo</th>
				<th>Aut.</th>
				<th>Con.</th>
				<th>Exc.</th>
				<th>Costo</th>
				<th>Aut.</th>
				<th>Con.</th>
				<th>Exc.</th>
				<th>Costo</th>
			</tr>
			<!-- START BLOCK : row -->
			<tr id="row">
				<td>{codmp} {nombre_mp}</td>
				<td align="right" class="green">{aut1}</td>
				<td align="right" class="blue">{con1}</td>
				<td align="right" class="red">{dif1}</td>
				<td align="right" class="red">{costo1}</td>
				<td align="right" class="green">{aut2}</td>
				<td align="right" class="blue">{con2}</td>
				<td align="right" class="red">{dif2}</td>
				<td align="right" class="red">{costo2}</td>
				<td align="right" class="green">{aut3}</td>
				<td align="right" class="blue">{con3}</td>
				<td align="right" class="red">{dif3}</td>
				<td align="right" class="red">{costo3}</td>
				<td align="right" class="green">{aut4}</td>
				<td align="right" class="blue">{con4}</td>
				<td align="right" class="red">{dif4}</td>
				<td align="right" class="red">{costo4}</td>
				<td align="right" class="green">{aut8}</td>
				<td align="right" class="blue">{con8}</td>
				<td align="right" class="red">{dif8}</td>
				<td align="right" class="red">{costo8}</td>
			</tr>
			<!-- END BLOCK : row -->
			<tr>
				<th colspan="4" align="right">Costo de excedente</th>
				<th align="right">{costo1}</th>
				<th colspan="3" align="right">&nbsp;</th>
				<th align="right">{costo2}</th>
				<th colspan="3" align="right">&nbsp;</th>
				<th align="right">{costo3}</th>
				<th colspan="3" align="right">&nbsp;</th>
				<th align="right">{costo4}</th>
				<th colspan="3" align="right">&nbsp;</th>
				<th align="right">{costo8}</th>
			</tr>
			<tr>
				<th colspan="4" align="right">Costo del mes</th>
				<th align="right">{mes1}</th>
				<th colspan="3" align="right">&nbsp;</th>
				<th align="right">{mes2}</th>
				<th colspan="3" align="right">&nbsp;</th>
				<th align="right">{mes3}</th>
				<th colspan="3" align="right">&nbsp;</th>
				<th align="right">{mes4}</th>
				<th colspan="3" align="right">&nbsp;</th>
				<th align="right">{mes8}</th>
			</tr>
			<tr>
				<th colspan="4" align="right">Costo total</th>
				<th colspan="17" align="left">{total}</th>
			</tr>
		</table>
	</div>
</div>
<!--{salto}-->
<!-- END BLOCK : reporte -->
<p align="center" class="noDisplay">
  <input name="cerrar" type="button" id="cerrar" value="Cerrar" />
</p>
</body>
</html>
