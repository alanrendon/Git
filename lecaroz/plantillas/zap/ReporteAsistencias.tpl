<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Reporte de Asistencias</title>
<link href="styles/screen.css" rel="stylesheet" type="text/css" media="screen" />
<link href="styles/print.css" rel="stylesheet" type="text/css" media="print" />
<link href="styles/font-style.css" rel="stylesheet" type="text/css" />
<link href="../../styles/screen.css" rel="stylesheet" type="text/css" media="screen" />
<link href="../../styles/print.css" rel="stylesheet" type="text/css" media="print" />
<link href="../../styles/font-style.css" rel="stylesheet" type="text/css" />

<style type="text/css">
<!--
.Color1 {color: #00C}
.Color2 {color: #C00}
.Color3 {color: #0C0}
.Color4 {color: #F90}
.Color5 {color: #60C}
-->
</style>

<script type="text/javascript" src="jscripts/mootools/mootools-1.2-core.js"></script>
<script type="text/javascript" src="jscripts/zap/ReporteAsistencias.js"></script>
</head>

<body>
<!-- START BLOCK : reporte -->
<table width="98%" class="encabezado">
  <tr>
    <td width="10%">{num_cia}</td>
    <td width="80%" align="center">{nombre_cia}</td>
    <td width="10%" align="right">{num_cia}</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td align="center">Reporte de Asistencias<br />
    del &quot;{fecha1} &quot; al &quot;{fecha2}&quot; </td>
    <td>&nbsp;</td>
  </tr>
</table>
<br />
<table align="center" class="print">
  <tr>
    <th colspan="2" class="print" scope="col">Empleado</th>
    <!-- START BLOCK : th -->
	<th class="print" scope="col">{dia_mes}<br />{dia_semana}</th>
	<!-- END BLOCK : th -->
    <th class="print" style="color:#00C;" scope="col">A</th>
    <th class="print" style="color:#C00;" scope="col">F</th>
    <th class="print" style="color:#0C0;" scope="col">I</th>
    <th class="print" style="color:#F90;" scope="col">D</th>
    <th class="print" style="color:#60C;" scope="col">V</th>
    <th class="print" scope="col">T</th>
  </tr>
  <!-- START BLOCK : row -->
  <tr id="row">
    <td class="print">{num_emp}</td>
    <td class="print">{nombre_emp}</td>
    <!-- START BLOCK : td -->
	<td align="center" class="print Color{color}">{status}</td>
	<!-- END BLOCK : td -->
    <td align="center" class="print Color1">{1}</td>
    <td align="center" class="print Color2">{2}</td>
    <td align="center" class="print Color3">{3}</td>
    <td align="center" class="print Color4">{4}</td>
    <td align="center" class="print Color5">{5}</td>
    <td align="center" class="print bold">{T}</td>
  </tr>
  <!-- END BLOCK : row -->
  <!-- START BLOCK : foot -->
  <tr>
    <th colspan="{span}" class="print">&nbsp;</th>
    <th class="print" style="color:#00C;">{1}</th>
    <th class="print" style="color:#C00;">{2}</th>
    <th class="print" style="color:#0C0;">{3}</th>
    <th class="print" style="color:#F90;">{4}</th>
    <th class="print" style="color:#60C;">{5}</th>
    <th class="print">{T}</th>
  </tr>
  <!-- END BLOCK : foot -->
</table>
<!-- START BLOCK : leyenda -->
<br>
<table align="center" class="print">
  <tr class="linea_off">
	<td align="center" class="print bold Color1" scope="row">A</td>
	<td class="print">Asistencia</td>
  </tr>
  <tr class="linea_on">
	<td align="center" class="print bold Color2" scope="row">F</td>
	<td class="print">Falta</td>
  </tr>
  <tr class="linea_off">
	<td align="center" class="print bold Color3" scope="row">I</td>
	<td class="print">Incapacidad</td>
  </tr>
  <tr class="linea_on">
	<td align="center" class="print bold Color4" scope="row">D</td>
	<td class="print">Descanso</td>
  </tr>
  <tr class="linea_off">
	<td align="center" class="print bold Color5" scope="row">V</td>
	<td class="print">Vacaciones</td>
  </tr>
</table>
<!-- END BLOCK : leyenda -->
{salto}
<!-- END BLOCK : reporte -->
<p align="center" class="noDisplay">
  <input name="cerrar" type="button" id="cerrar" value="Cerrar" />
</p>
</body>
</html>
