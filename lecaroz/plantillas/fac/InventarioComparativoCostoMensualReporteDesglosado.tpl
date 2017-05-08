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
	<!-- START BLOCK : cia -->
	<tr>
		<th colspan="8" align="left" class="print font12" scope="col">{num_cia} {nombre_cia}</th>
	</tr>
	<tr>
		<th class="print" scope="col">Producto</th>
		<th class="print" scope="col">Consumo</th>
		<th class="print" scope="col">Precio</th>
		<th class="print" scope="col">Costo del mes</th>
		<th class="print" scope="col">Precio</th>
		<th class="print" scope="col">Costo probable</th>
		<th class="print" scope="col">% Dif.</th>
		<th class="print" scope="col">Dif.</th>
	</tr>
	<!-- START BLOCK : row -->
	<tr id="row">
		<td align="left" class="print">{codmp} {nombre_mp}</td>
		<td align="right" class="print orange">{consumo}</td>
		<td align="right" class="print green">{precio1}</td>
		<td align="right" class="print green">{costo1}</td>
		<td align="right" class="print {color}">{precio2}</td>
		<td align="right" class="print {color}">{costo2}</td>
		<td align="right" class="print {color}">{por_dif}</td>
		<td align="right" class="print {color}">{dif}</td>
	</tr>
	<!-- END BLOCK : row -->
	<tr>
		<th align="right" class="print" colspan="2">Consumos</th>
		<th align="right" class="print">{consumos1}</th>
		<th align="right" class="print">&nbsp;</th>
		<th align="right" class="print">{consumos2}</th>
		<th align="right" class="print">&nbsp;</th>
		<th align="right" class="print">{por_dif}</th>
		<th align="right" class="print">{dif}</th>
	</tr>
	<tr>
		<th align="right" class="print" colspan="2">Mercancias</th>
		<th align="right" class="print">{mercancias1}</th>
		<th class="print">&nbsp;</th>
		<th align="right" class="print">{mercancias2}</th>
		<th class="print" colspan="3">&nbsp;</th>
	</tr>
	<tr>
		<th align="right" class="print" colspan="2">Total</th>
		<th align="right" class="print">{total1}</th>
		<th align="right" class="print">&nbsp;</th>
		<th align="right" class="print">{total2}</th>
		<th class="print" colspan="3">&nbsp;</th>
	</tr>
	<tr>
		<th align="right" class="print" colspan="2">Producci&oacute;n</th>
		<th align="right" class="print">{produccion1}</th>
		<th class="print">&nbsp;</th>
		<th align="right" class="print">{produccion2}</th>
		<th class="print" colspan="3">&nbsp;</th>
	</tr>
	<tr>
		<th align="right" class="print" colspan="2">M. Prima / Producción</th>
		<th align="right" class="print">{mp_pro1}</th>
		<th class="print">&nbsp;</th>
		<th align="right" class="print">{mp_pro2}</th>
		<th align="right" class="print" colspan="2">Diferencia</th>
		<th align="right" class="print">{mp_pro_dif}</th>
	</tr>
	<tr>
		<td colspan="7" align="left" class="print">&nbsp;</td>
	</tr>
	<!-- END BLOCK : cia -->
</table>
<p class="center font10 bold blue">NOTA: los costos probables subrayados son solo informativos.</p>
<p align="center" class="noDisplay">
  <input name="cerrar" type="button" id="cerrar" value="Cerrar" />
</p>
</body>
</html>
