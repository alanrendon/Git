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
<div class="Reporte">
  <div class="NombreCia" align="center">{num_cia} {nombre_cia} </div>
  <div class="NombreReporte" align="center">PRUEBA DE PAN AL {dia}  DE {mes} DE {anio} </div>
  <div class="Datos">
    <table width="98%" align="center" class="print">
      <tr>
        <th colspan="2" class="print font10" scope="col">D&iacute;a</th>
        <th class="print font10" scope="col">Producci&oacute;n</th>
        <th class="print font10" scope="col">Pan<br />
        Comprado</th>
        <th class="print font10" scope="col">Sobrante<br />
        Ayer</th>
        <th class="print font10" scope="col">Total<br />
        de Pan</th>
        <th class="print font10" scope="col">Venta en<br />
        Puerta</th>
        <th class="print font10" scope="col">Reparto</th>
        <th class="print font10" scope="col">Pan<br />
        Devuelto</th>
        <th class="print font10" scope="col">Descuento y<br />
        Degustaci&oacute;n</th>
        <th class="print font10" scope="col">Sobrante<br />
        de Hoy </th>
        <th class="print font10" scope="col">Existencia<br />
        F&iacute;sica</th>
        <th class="print font10" scope="col">Diferencia</th>
      </tr>
      <!-- START BLOCK : row1 -->
	  <tr class="{bgcolor}">
        <td class="print font10">{dial}</td>
        <td class="print font10"> {dian}</td>
        <td align="right" class="print font10 bold">{produccion}</td>
        <td align="right" class="print font10">{pan_comprado}</td>
        <td align="right" class="print font10">{sobrante_ayer}</td>
        <td align="right" class="print font10">{total_pan}</td>
        <td align="right" class="print font10 blue">{venta_puerta}</td>
        <td align="right" class="print font10 orange">{reparto}</td>
        <td align="right" class="print font10">{devuelto}</td>
        <td align="right" class="print font10">{descuento}</td>
        <td align="right" class="print font10">{sobrante_hoy}</td>
        <td align="right" class="print font10">{pan_contado}</td>
        <td align="right" class="print bold font10 {color_diferencia}">{diferencia}</td>
      </tr>
	  <!-- END BLOCK : row1 -->
      <tr>
        <th colspan="2" class="print">&nbsp;</th>
        <th align="right" class="print font10">{produccion}</th>
        <th align="right" class="print font10">{pan_comprado}</th>
        <th align="right" class="print font10">{sobrante_ayer}</th>
        <th align="right" class="print font10">{total_pan}</th>
        <th align="right" class="print font10 blue">{venta_puerta}</th>
        <th align="right" class="print font10">{reparto}</th>
        <th align="right" class="print font10">{devuelto}</th>
        <th align="right" class="print font10">{descuento}</th>
        <th align="right" class="print font10">{sobrante_hoy}</th>
        <th align="right" class="print font10">{pan_contado}</th>
        <th align="right" class="print font10">{diferencia}</th>
      </tr>
      <tr>
        <th colspan="2" class="print font10">&nbsp;</th>
        <th colspan="2" align="right" class="print font10">Efectivo/Producci&oacute;n:</th>
        <th class="print font10">{efectivo_produccion}</th>
        <th colspan="2" align="right" class="print font10">Prom. Diario Faltante:</th>
        <th class="print font10" align="right">{pfaltante}{promedio_faltante}</th>
        <th class="print font10">&nbsp;</th>
        <th class="print font10">&nbsp;</th>
        <th colspan="2" align="right" class="print font10">%Diferencia/Producci&oacute;n:</th>
        <th class="print font10" align="right">{diferencia_produccion}{tipo_diferencia}</th>
      </tr>
    </table>
  </div>
</div>
<!-- END BLOCK : reporte1 -->
<!-- START BLOCK : reporte2 -->
<div class="Reporte">
  <div class="NombreCia" align="center">{num_cia} {nombre_cia} </div>
  <div class="NombreReporte" align="center">PRUEBA DE PAN AL {dia}  DE {mes} DE {anio} </div>
  <div class="Datos">
    <table width="98%" align="center" class="print">
      <tr>
        <th colspan="2" class="print font10" scope="col">D&iacute;a</th>
        <th class="print font10" scope="col">Producci&oacute;n</th>
        <th class="print font10" scope="col">Pan<br />
        Comprado</th>
        <th class="print font10" scope="col">Sobrante<br />
        Ayer</th>
        <th class="print font10" scope="col">Total<br />
        de Pan</th>
        <th class="print font10" scope="col">Venta en<br />
        Puerta</th>
        <th class="print font10" scope="col">Pasteles</th>
        <th class="print font10" scope="col">Reparto</th>
        <th class="print font10" scope="col">Pan<br />
        Devuelto</th>
        <th class="print font10" scope="col">Descuento y<br />
        Degustaci&oacute;n</th>
        <th class="print font10" scope="col">Sobrante<br />
        de Hoy </th>
        <th class="print font10" scope="col">Existencia<br />
        F&iacute;sica</th>
        <th class="print font10" scope="col">Diferencia</th>
      </tr>
      <!-- START BLOCK : row2 -->
	  <tr class="{bgcolor}">
        <td class="print font10">{dial}</td>
        <td class="print font10"> {dian}</td>
        <td align="right" class="print font10 bold">{produccion}</td>
        <td align="right" class="print font10">{pan_comprado}</td>
        <td align="right" class="print font10">{sobrante_ayer}</td>
        <td align="right" class="print font10">{total_pan}</td>
        <td align="right" class="print font10 blue">{venta_puerta}</td>
        <td align="right" class="print font10 purple">{pasteles}</td>
        <td align="right" class="print font10 orange">{reparto}</td>
        <td align="right" class="print font10">{devuelto}</td>
        <td align="right" class="print font10">{descuento}</td>
        <td align="right" class="print font10">{sobrante_hoy}</td>
        <td align="right" class="print font10">{pan_contado}</td>
        <td align="right" class="print bold font10 {color_diferencia}">{diferencia}</td>
      </tr>
	  <!-- END BLOCK : row2 -->
      <tr>
        <th colspan="2" class="print">&nbsp;</th>
        <th align="right" class="print font10">{produccion}</th>
        <th align="right" class="print font10">{pan_comprado}</th>
        <th align="right" class="print font10">{sobrante_ayer}</th>
        <th align="right" class="print font10">{total_pan}</th>
        <th align="right" class="print font10 blue">{venta_puerta}</th>
        <th align="right" class="print font10">{pasteles}</th>
        <th align="right" class="print font10">{reparto}</th>
        <th align="right" class="print font10">{devuelto}</th>
        <th align="right" class="print font10">{descuento}</th>
        <th align="right" class="print font10">{sobrante_hoy}</th>
        <th align="right" class="print font10">{pan_contado}</th>
        <th align="right" class="print font10">{diferencia}</th>
      </tr>
      <tr>
        <th colspan="2" class="print font10">&nbsp;</th>
        <th colspan="2" align="right" class="print font10">Efectivo/Producci&oacute;n:</th>
        <th class="print font10">{efectivo_produccion}</th>
        <th colspan="3" align="right" class="print font10">Prom. Diario Faltante:</th>
        <th class="print font10" align="right">{pfaltante}{promedio_faltante}</th>
        <th class="print font10">&nbsp;</th>
        <th class="print font10">&nbsp;</th>
        <th colspan="2" align="right" class="print font10">%Diferencia/Producci&oacute;n:</th>
        <th class="print font10" align="right">{diferencia_produccion}{tipo_diferencia}</th>
      </tr>
    </table>
  </div>
</div>
<!-- END BLOCK : reporte2 -->
<!-- START BLOCK : reporte3 -->
<div class="Reporte">
  <div class="NombreCia" align="center">{num_cia} {nombre_cia} </div>
  <div class="NombreReporte" align="center">PRUEBA DE PAN {mes} DE {anio} </div>
  <div class="Datos">
  	<table width="98%" align="center" class="print">
  		<tr>
  			<th class="print font10" scope="col">Compa&ntilde;&iacute;a</th>
  			<th class="print font10" scope="col">D&iacute;a</th>
			<th class="print font10" scope="col">Diferencia</th>
			<th class="print font10" scope="col">Prom. Diario<br />
  				Faltante</th>
  			<th class="print font10" scope="col">% Dif. /<br />
  				Pro.</th>
  			<th class="print font10" scope="col">Producci&oacute;n</th>
  			<th class="print font10" scope="col">Pan<br />
  				Comprado</th>
  			<th class="print font10" scope="col">Venta en<br />
  				Puerta</th>
  			<th class="print font10" scope="col">Reparto</th>
  			<th class="print font10" scope="col">Pan<br />
  				Devuelto</th>
  			<th class="print font10" scope="col">Descuento y<br />
  				Degustaci&oacute;n</th>
  			<th class="print font10" scope="col">Efe. /<br />
  				Pro.</th>
  			</tr>
  		<!-- START BLOCK : row3 -->
		<tr class="{bgcolor}">
  			<td class="print font10 bold">{num_cia} {nombre_cia}</td>
  			<td align="center" class="print font10 bold">{dia}</td>
			<td align="right font10" class="print font10 bold {color_diferencia}">{diferencia}</td>
			<td align="right font10" class="print font10 bold {color_promedio_faltante}">{pfaltante}{promedio_faltante}</td>
  			<td align="right font10" class="print font10 bold {color_diferencia_produccion}">{diferencia_produccion}</td>
  			<td align="right font10" class="print font10">{produccion}</td>
  			<td align="right font10" class="print font10">{pan_comprado}</td>
  			<td align="right font10" class="print font10">{venta_puerta}</td>
  			<td align="right font10" class="print font10">{reparto}</td>
  			<td align="right font10" class="print font10">{devuelto}</td>
  			<td align="right font10" class="print font10">{descuento}</td>
  			<td align="right font10" class="print font10 bold">{efectivo_produccion}</td>
  			</tr>
			<!-- END BLOCK : row3 -->
  		</table>
  </div>
</div>
<!-- END BLOCK : reporte3 -->
<p align="center" class="noDisplay">
  <input name="cerrar" type="button" id="cerrar" value="Cerrar" />
</p>
</body>
</html>
