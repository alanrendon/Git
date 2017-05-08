<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Documento sin título</title>
<link href="/lecaroz/styles/font-style.css" rel="stylesheet" type="text/css" />
<link href="/lecaroz/styles/screen.css" rel="stylesheet" type="text/css" media="screen" />
<link href="/lecaroz/styles/print.css" rel="stylesheet" type="text/css" media="print" />

<script type="text/javascript" src="jscripts/mootools/mootools-1.2-core.js"></script>
<script type="text/javascript" src="jscripts/fac/InventarioComparativoCostoMensualReporte.js"></script>
</head>

<body>
<table width="98%" align="center" class="encabezado">
  <tr>
    <td width="15%" class="bold font12">&nbsp;</td>
    <td width="70%" align="center" class="bold font12">Comparativo de costo de inventario por mes<br />
    	{mes1} de {anio1} contra {mes2} de {anio2}</td>
    <td width="15%" align="right" class="bold font12">&nbsp;</td>
  </tr>
</table>
<br />
<table align="center" class="print">
	<tr>
		<th class="print" scope="col">Compañía</th>
		<th class="print" scope="col">Costo del mes</th>
		<th class="print" scope="col">Costo probable</th>
		<th class="print" scope="col">% Dif.</th>
		<th class="print" scope="col">Mercancias</th>
		<th class="print" scope="col">Producci&oacute;n</th>
		<th class="print" scope="col">% Dif.</th>
	</tr>
	<!-- START BLOCK : row -->
	<tr id="row">
		<td align="left" class="print">{num_cia} {nombre_cia}</td>
		<td align="right" class="print green">{costo1}</td>
		<td align="right" class="print {color}">{costo2}</td>
		<td align="right" class="print {color}">{dif}</td>
		<td align="right" class="print purple">{mercancias}</td>
		<td align="right" class="print orange">{produccion}</td>
		<td align="right" class="print {color_mp_pro}">{mp_pro_dif}</td>
	</tr>
	<!-- START BLOCK : totales -->
	<tr>
		<th class="print" align="right">Totales</th>
		<th class="print" align="right">{costo1}</th>
		<th class="print" align="right">{costo2}</th>
		<th class="print" align="right">{dif}</th>
		<th class="print" align="right">{mercancias}</th>
		<th class="print" align="right">{produccion}</th>
		<th class="print" align="right">{mp_pro_dif}</th>
	</tr>
	<tr>
		<td colspan="7">&nbsp;</td>
	</tr>
	<!-- END BLOCK : totales -->
	<!-- END BLOCK : row -->
</table>
<p align="center" class="noDisplay">
  <input name="cerrar" type="button" id="cerrar" value="Cerrar" />
</p>
</body>
</html>
