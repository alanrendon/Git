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
<script type="text/javascript" src="jscripts/pan/ReportePruebaPan.js"></script>

<style>
.NombreCia {
	font-weight: bold;
	margin-top: 10px;
}

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
</style>
</head>

<body>
<!-- START BLOCK : reporte1 -->
<div class="Reporte1">
  <div class="NombreCia" align="center">{num_cia} {nombre_cia} </div>
  <div class="NombreReporte" align="center">RENDIMIENTOS DE HARINA AL {dia}  DE {mes} DE {anio} </div>
  <div class="Datos">
  	<table width="98%" align="center" class="print">
  		<tr>
  			<th colspan="5" class="print" scope="col">{turno_1}</th>
  			<th width="30" class="print" scope="col">&nbsp;</th>
  			<th colspan="5" class="print" scope="col">{turno_2}</th>
  			</tr>
  		<tr>
  			<th class="print">Día</th>
  			<th class="print">Consumo</th>
  			<th class="print">Producción</th>
  			<th class="print">Raya</th>
  			<th class="print">Rendimiento</th>
  			<th class="print">&nbsp;</th>
  			<th class="print">Día</th>
  			<th class="print">Consumo</th>
  			<th class="print">Producción</th>
  			<th class="print">Raya</th>
  			<th class="print">Rendimiento</th>
  			</tr>
  		<!-- START BLOCK : row1 -->
		<tr>
  			<td align="right" class="print">{dia_1}</td>
  			<td align="right" class="print red">{consumo_1}</td>
  			<td align="right" class="print blue">{produccion_1}</td>
  			<td align="right" class="print red">{raya_1}</td>
  			<td align="right" class="print green">{rendimiento_1}</td>
  			<td class="print">&nbsp;</td>
  			<td align="right" class="print">{dia_2}</td>
  			<td align="right" class="print red">{consumo_2}</td>
  			<td align="right" class="print blue">{produccion_2}</td>
  			<td align="right" class="print red">{raya_2}</td>
  			<td align="right" class="print green">{rendimiento_2}</td>
  			</tr>
		<!-- END BLOCK : row1 -->
  		<tr>
  			<th align="right" class="print">Total</th>
  			<th align="right" class="print">{consumo_1}</th>
  			<th align="right" class="print">{produccion_1}</th>
  			<th align="right" class="print">{raya_1}</th>
  			<th align="right" class="print">{rendimiento_1}</th>
  			<th class="print">&nbsp;</th>
  			<th align="right" class="print">Total</th>
  			<th align="right" class="print"> {consumo_2}</th>
  			<th align="right" class="print">{produccion_2}</th>
  			<th align="right" class="print">{raya_2}</th>
  			<th align="right" class="print">{rendimiento_2}</th>
  			</tr>
  		</table>
  </div>
</div>
<!--{salto}-->
<!-- END BLOCK : reporte1 -->
<!-- START BLOCK : reporte2 -->
<div class="Reporte2">
  <div class="NombreReporte bold" align="center">RENDIMIENTOS DE HARINA {mes} DE {anio} <br />
  	{turno}
  </div>
  <div class="Datos">
  	<table width="98%" align="center" class="print">
  		<tr>
  			<th class="print" scope="col">Compa&ntilde;&iacute;a</th>
  			<th class="print" scope="col">D&iacute;a</th>
			<th class="print" scope="col">Consumo</th>
			<th class="print" scope="col">Producción</th>
  			<th class="print" scope="col">Raya</th>
  			<th class="print" scope="col">Rendimiento</th>
  			</tr>
  		<!-- START BLOCK : row2 -->
		<tr>
  			<td class="print">{num_cia} {nombre_cia}</td>
  			<td align="center" class="print">{dia}</td>
			<td align="right" class="print red">{consumo}</td>
			<td align="right" class="print blue">{produccion}</td>
  			<td align="right" class="print red">{raya}</td>
  			<td align="right" class="print green">{rendimiento}</td>
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
  	<table width="98%" align="center" class="print">
  		<tr>
  			<th class="print" scope="col">&nbsp;</th>
  			<th colspan="2" class="print" scope="col">FRANCES DE DIA</th>
  			<th colspan="2" class="print" scope="col">FRANCES DE NOCHE</th>
  			<th colspan="2" class="print" scope="col">BIZCOCHERO</th>
  			<th colspan="2" class="print" scope="col">REPOSTERO</th>
  			<th class="print" scope="col">&nbsp;</th>
  			</tr>
  		<tr>
  			<th class="print">Día</th>
  			<th class="print">Consumo</th>
  			<th class="print">Rendimiento</th>
  			<th class="print">Consumo</th>
  			<th class="print">Rendimiento</th>
  			<th class="print">Consumo</th>
  			<th class="print">Rendimiento</th>
  			<th class="print">Consumo</th>
  			<th class="print">Rendimiento</th>
  			<th class="print">Efectivo</th>
  			</tr>
  		<!-- START BLOCK : row3 -->
		<tr>
  			<td align="right" class="print">{dia}</td>
  			<td align="right" class="print red">{consumo_1}</td>
  			<td align="right" class="print green">{rendimiento_1}</td>
  			<td align="right" class="print red">{consumo_2}</td>
  			<td align="right" class="print green">{rendimiento_2}</td>
  			<td align="right" class="print red">{consumo_3}</td>
  			<td align="right" class="print green">{rendimiento_3}</td>
  			<td align="right" class="print red">{consumo_4}</td>
  			<td align="right" class="print green">{rendimiento_4}</td>
  			<td align="right" class="print blue">{efectivo}</td>
  			</tr>
		<!-- END BLOCK : row3 -->
  		<tr>
  			<th class="print">&nbsp;</th>
  			<th align="right" class="print">{consumo_1}</th>
  			<th align="right" class="print">{rendimiento_1}</th>
  			<th align="right" class="print">{consumo_2}</th>
  			<th align="right" class="print">{rendimiento_2}</th>
  			<th align="right" class="print">{consumo_3}</th>
  			<th align="right" class="print">{rendimiento_3}</th>
  			<th align="right" class="print">{consumo_4}</th>
  			<th align="right" class="print">{rendimiento_4}</th>
  			<th align="right" class="print">{efectivo}</th>
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
