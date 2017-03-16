<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252" />
<title>Prueba de Pan</title>
<link href="../../styles/screen.css" rel="stylesheet" type="text/css" media="screen" />
<link href="../../styles/print.css" rel="stylesheet" type="text/css" media="print" />
<link href="../../styles/font-style.css" rel="stylesheet" type="text/css" />
<link href="styles/screen.css" rel="stylesheet" type="text/css" media="screen" />
<link href="styles/print.css" rel="stylesheet" type="text/css" media="print" />
<link href="styles/font-style.css" rel="stylesheet" type="text/css" />
<link href="/lecaroz/styles/Tips.css" rel="stylesheet" type="text/css" />

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
	height: 205mm;
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
  <div class="NombreReporte" align="center">PRUEBA DE PAN AL {dia}  DE {mes} DE {anio} </div>
  <div class="Datos">
    <table width="98%" align="center">
      <tr>
        <th colspan="2" scope="col">D&iacute;a</th>
        <th scope="col">Producci&oacute;n</th>
        <th scope="col">Pan<br />
        Comprado</th>
        <th scope="col">Sobrante<br />
        Ayer</th>
        <th scope="col">Total<br />
        de Pan</th>
        <th scope="col">Venta en<br />
        Puerta</th>
        <th scope="col">Reparto</th>
        <th scope="col">Pan<br />
        Devuelto</th>
        <th scope="col">Descuento y<br />
        Degustaci&oacute;n</th>
        <th scope="col">Sobrante<br />
        de Hoy </th>
        <th scope="col">Existencia<br />
        F&iacute;sica</th>
        <th scope="col">Diferencia</th>
      </tr>
      <!-- START BLOCK : row1 -->
	  <tr id="row">
        <td align="center">{dial}</td>
        <td align="center"> {dian}</td>
        <td align="right" class="bold">{produccion}</td>
        <td align="right">{pan_comprado}</td>
        <td align="right">{sobrante_ayer}</td>
        <td align="right">{total_pan}</td>
        <td align="right" class="blue">{venta_puerta}</td>
        <td align="right" class="orange">{reparto}</td>
        <td align="right">{devuelto}</td>
        <td align="right">{descuento}</td>
        <td align="right">{sobrante_hoy}</td>
        <td align="right">{pan_contado}</td>
        <td align="right" class="bold">{diferencia}</td>
      </tr>
	  <!-- END BLOCK : row1 -->
      <tr>
        <th colspan="2">&nbsp;</th>
        <th align="right">{produccion}</th>
        <th align="right">{pan_comprado}</th>
        <th align="right">{sobrante_ayer}</th>
        <th align="right">{total_pan}</th>
        <th align="right" class="blue">{venta_puerta}</th>
        <th align="right">{reparto}</th>
        <th align="right">{pdevuelto}{devuelto}</th>
        <th align="right">{descuento}</th>
        <th align="right">{sobrante_hoy}</th>
        <th align="right">{pan_contado}</th>
        <th align="right">{diferencia}</th>
      </tr>
      <tr>
        <th colspan="2">&nbsp;</th>
        <th colspan="2" align="right">Efectivo/Producci&oacute;n:</th>
        <th>{efectivo_produccion}</th>
        <th colspan="2" align="right">Prom. Diario Faltante:</th>
        <th align="right">{pfaltante}{promedio_faltante}</th>
        <th>&nbsp;</th>
        <th>&nbsp;</th>
        <th colspan="2" align="right">%Diferencia/Producci&oacute;n:</th>
        <th align="right">{diferencia_produccion}{tipo_diferencia}</th>
      </tr>
    </table>
  </div>
</div>
<!--{salto}-->
<!-- END BLOCK : reporte1 -->
<!-- START BLOCK : reporte2 -->
<div class="Reporte1">
  <div class="NombreCia" align="center">{num_cia} {nombre_cia} </div>
  <div class="NombreReporte" align="center">PRUEBA DE PAN AL {dia}  DE {mes} DE {anio} </div>
  <div class="Datos">
    <table width="98%" align="center">
      <tr>
        <th colspan="2" scope="col">D&iacute;a</th>
        <th scope="col">Producci&oacute;n</th>
        <th scope="col">Pan<br />
        Comprado</th>
        <th scope="col">Sobrante<br />
        Ayer</th>
        <th scope="col">Total<br />
        de Pan</th>
        <th scope="col">Venta en<br />
        Puerta</th>
        <th scope="col">Pasteles</th>
        <th scope="col">Reparto</th>
        <th scope="col">Pan<br />
        Devuelto</th>
        <th scope="col">Descuento y<br />
        Degustaci&oacute;n</th>
        <th scope="col">Sobrante<br />
        de Hoy </th>
        <th scope="col">Existencia<br />
        F&iacute;sica</th>
        <th scope="col">Diferencia</th>
      </tr>
      <!-- START BLOCK : row2 -->
	  <tr id="row">
        <td align="center">{dial}</td>
        <td align="center"> {dian}</td>
        <td align="right" class="bold">{produccion}</td>
        <td align="right">{pan_comprado}</td>
        <td align="right">{sobrante_ayer}</td>
        <td align="right">{total_pan}</td>
        <td align="right" class="blue">{venta_puerta}</td>
        <td align="right" class="purple">{pasteles}</td>
        <td align="right" class="orange">{reparto}</td>
        <td align="right">{devuelto}</td>
        <td align="right">{descuento}</td>
        <td align="right">{sobrante_hoy}</td>
        <td align="right">{pan_contado}</td>
        <td align="right" class="bold">{diferencia}</td>
      </tr>
	  <!-- END BLOCK : row2 -->
      <tr>
        <th colspan="2">&nbsp;</th>
        <th align="right">{produccion}</th>
        <th align="right">{pan_comprado}</th>
        <th align="right">{sobrante_ayer}</th>
        <th align="right">{total_pan}</th>
        <th align="right" class="blue">{venta_puerta}</th>
        <th align="right">{pasteles}</th>
        <th align="right">{reparto}</th>
        <th align="right">{pdevuelto}{devuelto}</th>
        <th align="right">{descuento}</th>
        <th align="right">{sobrante_hoy}</th>
        <th align="right">{pan_contado}</th>
        <th align="right">{diferencia}</th>
      </tr>
      <tr>
        <th colspan="2">&nbsp;</th>
        <th colspan="2" align="right">Efectivo/Producci&oacute;n:</th>
        <th>{efectivo_produccion}</th>
        <th colspan="3" align="right">Prom. Diario Faltante:</th>
        <th align="right">{pfaltante}{promedio_faltante}</th>
        <th>&nbsp;</th>
        <th>&nbsp;</th>
        <th colspan="2" align="right">%Diferencia/Producci&oacute;n:</th>
        <th align="right">{diferencia_produccion}{tipo_diferencia}</th>
      </tr>
    </table>
  </div>
</div>
<!--{salto}-->
<!-- END BLOCK : reporte2 -->
<!-- START BLOCK : reporte3 -->
<div class="Reporte2">
  <div class="NombreReporte font14 bold" align="center">PRUEBA DE PAN {mes} DE {anio} </div>
  <div class="Datos">
  	<table width="98%" align="center">
  		<tr>
  			<th class="font12" scope="col">Compa&ntilde;&iacute;a</th>
  			<th class="font12" scope="col">D&iacute;a</th>
			<th class="font12" scope="col">Diferencia</th>
			<th class="font12" scope="col">Prom. Diario<br />
  				Faltante</th>
  			<th class="font12" scope="col">% Dif. /<br />
  				Pro.</th>
  			<th class="font12" scope="col">Producci&oacute;n</th>
  			<th class="font12" scope="col">Pan<br />
  				Comprado</th>
  			<th class="font12" scope="col">Venta en<br />
  				Puerta</th>
  			<th class="font12" scope="col">Reparto</th>
  			<th class="font12" scope="col">Pan<br />
  				Devuelto</th>
  			<th class="font12" scope="col">Descuento y<br />
  				Degustaci&oacute;n</th>
  			<th class="font12" scope="col">Efe. /<br />
  				Pro.</th>
  			</tr>
  		<!-- START BLOCK : row3 -->
		<tr id="row">
  			<td class="font12 bold">{num_cia} {nombre_cia}</td>
  			<td align="center" class="font12 bold">{dia}</td>
			<td align="right" class="font12 bold {color_diferencia}">{diferencia}</td>
			<td align="right" class="font12 bold {color_promedio_faltante}">{pfaltante}{promedio_faltante}</td>
  			<td align="right" class="font12 bold {color_diferencia_produccion}">{diferencia_produccion}</td>
  			<td align="right" class="font12 bold">{produccion}</td>
  			<td align="right" class="font12">{pan_comprado}</td>
  			<td align="right" class="font12 bold">{venta_puerta}</td>
  			<td align="right" class="font12">{reparto}</td>
  			<td align="right" class="font12">{pdevuelto}{devuelto}</td>
  			<td align="right" class="font12">{descuento}</td>
  			<td align="right" class="font12 bold">{efectivo_produccion}</td>
  			</tr>
			<!-- END BLOCK : row3 -->
  		</table>
  </div>
</div>
{salto}
<!-- END BLOCK : reporte3 -->
<p align="center" class="noDisplay">
  <input name="cerrar" type="button" id="cerrar" value="Cerrar" />
</p>
</body>
</html>
