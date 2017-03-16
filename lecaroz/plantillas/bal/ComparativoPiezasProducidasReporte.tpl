<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Comparativo de piezas producidas</title>
<link href="/lecaroz/styles/screen.css" rel="stylesheet" type="text/css" media="screen" />
<link href="/lecaroz/styles/print.css" rel="stylesheet" type="text/css" media="print" />
<link href="/lecaroz/styles/font-style.css" rel="stylesheet" type="text/css" />

<script type="text/javascript" src="jscripts/mootools/mootools-1.2-core.js"></script>
<script type="text/javascript" src="jscripts/mootools/mootools-1.2-more.js"></script>
<script type="text/javascript" src="jscripts/mootools/extensiones.js"></script>
<script type="text/javascript" src="jscripts/bal/ComparativoPiezasProducidasReporte.js"></script>

<style type="text/css" media="screen">
.NombreReporte {
	font-size: 12pt;
	font-weight: bold;
	margin-bottom: 20px;
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
.NombreReporte {
	font-size: 12pt;
	font-weight: bold;
	margin-bottom: 20px;
}

table {
	border-collapse: collapse;
	border: #000 solid 1px;
}

th {
	font-size: 12pt;
	border: #000 solid 1px;
}

td {
	font-size: 12pt;
	border: #000 solid 1px;
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
<div class="Reporte">
	<div class="NombreReporte" align="center">Comparativo de piezas producidas del {anio}</div>
	<div class="Datos">
		<table width="80%" align="center">
			<tr>
				<th rowspan="2" scope="col">Compañía</th>
				<th colspan="2" scope="col">{mes}</th>
				<th rowspan="2" scope="col">Diferencia</th>
			</tr>
			<tr>
				<th>{anio_1}</th>
				<th>{anio_2}</th>
			</tr>
			<!-- START BLOCK : row -->
			<tr id="row">
				<td>{num_cia} {nombre_cia}</td>
				<td align="right" class="blue">{piezas_1}</td>
				<td align="right" class="green">{piezas_2}</td>
				<td align="right" class="{color}">{diferencia}</td>
			</tr>
			<!-- END BLOCK : row -->
			<!-- START BLOCK : totales -->
			<tr>
				<th align="right">Totales</th>
				<th align="right" class="blue">{total_1}</th>
				<th align="right" class="green">{total_2}</th>
				<th align="right" class="{color}">{diferencia}</th>
			</tr>
			<!-- END BLOCK : totales -->
		</table>
	</div>
</div>
<br style="page-break-after:always;" />
<!-- END BLOCK : reporte -->
<p align="center" class="noDisplay">
  <input name="cerrar" type="button" id="cerrar" value="Cerrar" />
</p>
</body>
</html>
