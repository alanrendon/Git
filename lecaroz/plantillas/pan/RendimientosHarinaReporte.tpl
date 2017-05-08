<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Rendimientos de Harina</title>
<link href="/lecaroz/styles/screen.css" rel="stylesheet" type="text/css" media="screen" />
<link href="/lecaroz/styles/print.css" rel="stylesheet" type="text/css" media="print" />
<link href="/lecaroz/styles/font-style.css" rel="stylesheet" type="text/css" />

<script type="text/javascript" src="jscripts/mootools/mootools-1.2-core.js"></script>
<script type="text/javascript" src="jscripts/mootools/mootools-1.2-more.js"></script>
<script type="text/javascript" src="jscripts/mootools/extensiones.js"></script>
<script type="text/javascript" src="jscripts/pan/ReportePruebaPan.js"></script>

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
<!-- START BLOCK : reporte1 -->
<div class="Reporte1">
  <div class="NombreCia" align="center">{num_cia} {nombre_cia} </div>
  <div class="NombreReporte" align="center">RENDIMIENTOS DE HARINA AL {dia}  DE {mes} DE {anio} </div>
  <div class="Datos">
  	<table width="98%" align="center">
  		<tr>
  			<th colspan="5" scope="col">{turno_1}</th>
  			<th width="30" scope="col">&nbsp;</th>
  			<th colspan="5" scope="col">{turno_2}</th>
  			</tr>
  		<tr>
  			<th>Día</th>
  			<th>Consumo</th>
  			<th>Producción</th>
  			<th>Raya</th>
  			<th>Rendimiento</th>
  			<th>&nbsp;</th>
  			<th>Día</th>
  			<th>Consumo</th>
  			<th>Producción</th>
  			<th>Raya</th>
  			<th>Rendimiento</th>
  			</tr>
  		<!-- START BLOCK : row1 -->
		<tr>
  			<td align="right">{dia_1}</td>
  			<td align="right" class="red">{consumo_1}</td>
  			<td align="right" class="blue">{produccion_1}</td>
  			<td align="right" class="red">{raya_1}</td>
  			<td align="right" class="green">{rendimiento_1}</td>
  			<td>&nbsp;</td>
  			<td align="right">{dia_2}</td>
  			<td align="right" class="red">{consumo_2}</td>
  			<td align="right" class="blue">{produccion_2}</td>
  			<td align="right" class="red">{raya_2}</td>
  			<td align="right" class="green">{rendimiento_2}</td>
  			</tr>
		<!-- END BLOCK : row1 -->
  		<tr>
  			<th align="right">Total</th>
  			<th align="right">{consumo_1}</th>
  			<th align="right">{produccion_1}</th>
  			<th align="right">{raya_1}</th>
  			<th align="right">{rendimiento_1}</th>
  			<th>&nbsp;</th>
  			<th align="right">Total</th>
  			<th align="right"> {consumo_2}</th>
  			<th align="right">{produccion_2}</th>
  			<th align="right">{raya_2}</th>
  			<th align="right">{rendimiento_2}</th>
  			</tr>
  		</table>
  </div>
</div>
<!--{salto}-->
<!-- END BLOCK : reporte1 -->
<!-- START BLOCK : reporte2 -->
<div class="Reporte2">
  <div class="NombreReporte font14 bold" align="center">RENDIMIENTOS DE HARINA {mes} DE {anio} <br />
  	{turno}
  </div>
  <div class="Datos">
  	<table width="98%" align="center">
  		<tr>
  			<th class="font12" scope="col">Compa&ntilde;&iacute;a</th>
  			<th class="font12" scope="col">D&iacute;a</th>
			<th class="font12" scope="col">Consumo</th>
			<th class="font12" scope="col">Producción</th>
  			<th class="font12" scope="col">Raya</th>
  			<th class="font12" scope="col">Rendimiento</th>
  			</tr>
  		<!-- START BLOCK : row2 -->
		<tr>
  			<td class="font12 bold">{num_cia} {nombre_cia}</td>
  			<td align="center" class="font12 bold">{dia}</td>
			<td align="right" class="font12 bold red">{consumo}</td>
			<td align="right" class="font12 bold blue">{produccion}</td>
  			<td align="right" class="font12 bold red">{raya}</td>
  			<td align="right" class="font12 bold green">{rendimiento}</td>
  			</tr>
			<!-- END BLOCK : row2 -->
  		</table>
  </div>
</div>
{salto}
<!-- END BLOCK : reporte2 -->
<!-- START BLOCK : reporte3 -->
<div class="Reporte1">
  <div class="NombreCia" align="center">{num_cia} {nombre_cia} </div>
  <div class="NombreReporte" align="center">RENDIMIENTOS DE HARINA AL {dia}  DE {mes} DE {anio} </div>
  <div class="Datos">
  	<table width="98%" align="center">
  		<tr>
  			<th scope="col">&nbsp;</th>
  			<th colspan="2" scope="col">FRANCES DE DIA</th>
  			<th colspan="2" scope="col">FRANCES DE NOCHE</th>
  			<th colspan="2" scope="col">BIZCOCHERO</th>
  			<th colspan="2" scope="col">REPOSTERO</th>
  			<th scope="col">&nbsp;</th>
  			</tr>
  		<tr>
  			<th>Día</th>
  			<th>Consumo</th>
  			<th>Rendimiento</th>
  			<th>Consumo</th>
  			<th>Rendimiento</th>
  			<th>Consumo</th>
  			<th>Rendimiento</th>
  			<th>Consumo</th>
  			<th>Rendimiento</th>
  			<th>Efectivo</th>
  			</tr>
  		<!-- START BLOCK : row3 -->
		<tr>
  			<td align="right">{dia}</td>
  			<td align="right" class="red">{consumo_1}</td>
  			<td align="right" class="green">{rendimiento_1}</td>
  			<td align="right" class="red">{consumo_2}</td>
  			<td align="right" class="green">{rendimiento_2}</td>
  			<td align="right" class="red">{consumo_3}</td>
  			<td align="right" class="green">{rendimiento_3}</td>
  			<td align="right" class="red">{consumo_4}</td>
  			<td align="right" class="green">{rendimiento_4}</td>
  			<td align="right" class="blue">{efectivo}</td>
  			</tr>
		<!-- END BLOCK : row3 -->
  		<tr>
  			<th>&nbsp;</th>
  			<th align="right">{consumo_1}</th>
  			<th align="right">{rendimiento_1}</th>
  			<th align="right">{consumo_2}</th>
  			<th align="right">{rendimiento_2}</th>
  			<th align="right">{consumo_3}</th>
  			<th align="right">{rendimiento_3}</th>
  			<th align="right">{consumo_4}</th>
  			<th align="right">{rendimiento_4}</th>
  			<th align="right">{efectivo}</th>
  			</tr>
  		</table>
  </div>
</div>
<!--{salto}-->
<!-- END BLOCK : reporte3 -->
<p align="center" class="noDisplay">
  <input name="cerrar" type="button" id="cerrar" value="Cerrar" />
</p>
</body>
</html>
