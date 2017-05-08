<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Facturas Pendientes de Pago</title>

<link href="/lecaroz/styles/screen.css" rel="stylesheet" type="text/css" media="screen" />
<link href="/lecaroz/styles/print.css" rel="stylesheet" type="text/css" media="print" />
<link href="/lecaroz/styles/font-style.css" rel="stylesheet" type="text/css" />

<script type="text/javascript" src="jscripts/mootools/mootools-1.2-core.js"></script>
<script type="text/javascript" src="jscripts/bal/EstimacionAlzaPreciosProduccionReporte.js"></script>
</head>

<body>
<!-- START BLOCK : reporte -->
<table width="98%" align="center" class="encabezado">
  <tr>
  	<td width="10%" align="left">{num_cia}</td>
  	<td width="80%" align="center">{nombre_cia}<br />
  		Estimaci&oacute;n de alza de precios de producci&oacute;n<br />
  		{mes} de {anio}</td>
  	<td width="10%" align="right">{num_cia}</td>
  	</tr>
</table>
<br />
<table align="center" class="print">
	<!-- START BLOCK : turno -->
	<tr>
		<th colspan="6" align="left" class="print" scope="col">{turno}</th>
	</tr>
	<tr>
		<th class="print">Producto</th>
		<th class="print">Piezas</th>
		<th class="print">Precio</th>
		<th class="print">Produccion</th>
		<th class="print">Precio<br />
		estimado</th>
		<th class="print">Producci&oacute;n<br />
			estimada</th>
	</tr>
	<!-- START BLOCK : row -->
	<tr id="row">
		<td class="print">{cod_producto} {producto}</td>
		<td align="right" class="print blue">{piezas}</td>
		<td align="right" class="print blue">{precio}</td>
		<td align="right" class="print blue">{produccion}</td>
		<td align="right" class="print green">{precio_estimado}</td>
		<td align="right" class="print green">{produccion_estimada}</td>
	</tr>
	<!-- END BLOCK : row -->
	<tr>
		<td colspan="6" class="print">&nbsp;</td>
	</tr>
	<!-- END BLOCK : turno -->
</table>
{salto} 
<!-- END BLOCK : reporte -->
<p align="center" class="noDisplay">
  <input name="cerrar" type="button" id="cerrar" value="Cerrar" />
</p>
</body>
</html>
