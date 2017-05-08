<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252" />
<title>Rendimi&iexcl;entos de Harina</title>
<link href="/lecaroz/styles/screen.css" rel="stylesheet" type="text/css" media="screen" />
<link href="/lecaroz/styles/print.css" rel="stylesheet" type="text/css" media="print" />
<link href="/lecaroz/styles/font-style.css" rel="stylesheet" type="text/css" />

<script type="text/javascript" src="jscripts/mootools/mootools-1.2-core.js"></script>
<script type="text/javascript" src="jscripts/mootools/mootools-1.2-more.js"></script>
<script type="text/javascript" src="jscripts/mootools/extensiones.js"></script>
<script type="text/javascript" src="jscripts/pan/ConsumosDeMasReporte.js"></script>

<style>
.NombreReporte {
	font-weight: bold;
	margin-bottom: 10px;
}

.bgWhite {
	background-color: #FFF;
}

.bgGray {
	background-color: #CCC;
}
td.blue {	color: #00C;
}
th.blue {	color: #00C;
}
</style>
</head>

<body>
<!-- START BLOCK : reporte -->
<div class="Reporte1">
	<div class="NombreReporte" align="center">Comparativo de piezas producidas del {anio}</div>
	<div class="Datos">
		<table width="80%" align="center" class="print">
			<tr>
				<th rowspan="2" class="print" scope="col">Compa&ntilde;&iacute;a</th>
				<th colspan="2" class="print" scope="col">{mes}</th>
				<th rowspan="2" class="print" scope="col">Diferencia</th>
			</tr>
			<tr>
				<th class="print">{anio_1}</th>
				<th class="print">{anio_2}</th>
			</tr>
			<!-- START BLOCK : row -->
			<tr id="row">
				<td class="print">{num_cia} {nombre_cia}</td>
				<td align="right" class="print blue">{piezas_1}</td>
				<td align="right" class="print red">{piezas_2}</td>
				<td align="right" class="print {color}">{diferencia}</td>
			</tr>
			<!-- END BLOCK : row -->
			<!-- START BLOCK : totales -->
			<tr>
				<th align="right" class="print">Totales</th>
				<th align="right" class="print blue">{total_1}</th>
				<th align="right" class="print red">{total_2}</th>
				<th align="right" class="print {color}">{diferencia}</th>
			</tr>
			<!-- END BLOCK : totales -->
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
