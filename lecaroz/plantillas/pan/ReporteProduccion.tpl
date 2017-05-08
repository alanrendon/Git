<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Reporte de Producci&oacute;n</title>
<link href="styles/screen.css" rel="stylesheet" type="text/css" media="screen" />
<link href="styles/print.css" rel="stylesheet" type="text/css" media="print" />
<link href="styles/font-style.css" rel="stylesheet" type="text/css" />
<link href="../../styles/screen.css" rel="stylesheet" type="text/css" media="screen" />
<link href="../../styles/print.css" rel="stylesheet" type="text/css" media="print" />
<link href="../../styles/font-style.css" rel="stylesheet" type="text/css" />

<script type="text/javascript" src="jscripts/mootools/mootools-1.2-core.js"></script>
<script type="text/javascript" src="jscripts/mootools/mootools-1.2-more.js"></script>
<script type="text/javascript" src="jscripts/mootools/extensiones.js"></script>
<script type="text/javascript" src="jscripts/pan/ReporteProduccion.js"></script>

<style type="text/css" media="screen">
.Tip {
	background: #FF9;
	border: solid 1px #000;
	padding: 3px 5px;
}

.tip-title {
	font-weight: bold;
	font-size: 8pt;
	border-bottom: solid 2px #FC0;
	padding: 0 5px 3px 5px;
	margin-bottom: 3px;
}

.tip-text {
	font-weight: bold;
	font-size: 8pt;
	padding: 0 5px;
}
</style>

<style type="text/css">
@media all {
	div.saltopagina {
		display: none;
	}
	
	br.saltopagina {
		display: none;
	}
}
   
@media print {
	div.saltopagina { 
		display: block; 
		page-break-before: always;
	}
	
	br.saltopagina { 
		display: inline; 
		page-break-after: always;
	}
}
</style>

</head>

<body>
<!-- START BLOCK : reporte -->
<table width="98%" align="center" class="encabezado">
  <tr>
    <td width="15%" class="font14">{num_cia}</td>
    <td width="70%" align="center" class="font14">{nombre_cia}</td>
    <td width="15%" align="right" class="font14">{num_cia}</td>
  </tr>
  <tr>
    <td class="font8">&nbsp;</td>
    <td align="center">Reporte de Producci&oacute;n<br />
    {dia} de {mes} de {anio} </td>
    <td align="right" class="font8">&nbsp;</td>
  </tr>
</table>
<br />
<table width="98%" align="center" class="print">
  <!-- START BLOCK : turno -->
  <tr>
    <th colspan="7" align="left" class="print font12" scope="col">{turno}</th>
  </tr>
  <tr>
    <th colspan="2" class="print" scope="col">Producto</th>
    <th class="print" scope="col">Piezas</th>
    <th class="print" scope="col">Precio de Raya </th>
    <th class="print" scope="col">Importe Raya</th>
    <th class="print" scope="col">Precio de Venta </th>
    <th class="print" scope="col">Producci&oacute;n</th>
  </tr>
  <!-- START BLOCK : producto -->
  <tr id="row">
    <td class="print">{cod}</td>
    <td class="print">{producto}</td>
    <td align="right" class="print green">{piezas}</td>
    <td align="right" class="print red">{precio_raya}</td>
    <td align="right" class="print red">{raya}</td>
    <td align="right" class="print blue">{precio_venta}</td>
    <td align="right" class="print blue">{produccion}</td>
  </tr>
  <!-- END BLOCK : producto -->
  <tr>
    <th colspan="4" align="right" class="print font12">Raya Ganada </th>
    <th align="right" class="print font12 red">{raya_ganada}</th>
    <th align="right" class="print font12">Producci&oacute;n Total </th>
    <th align="right" class="print font12 blue">{produccion}</th>
  </tr>
  <tr>
    <th colspan="4" align="right" class="print font12">Raya Pagada </th>
    <th align="right" class="print font12 red">{raya_pagada}</th>
    <th colspan="2" align="right" class="print font12">&nbsp;</th>
  </tr>
  <tr>
    <td colspan="7" class="print">&nbsp;</td>
  </tr>
  <!-- END BLOCK : turno -->
</table>
<br />
<table align="center" class="print">
  <tr>
    <th align="left" class="print font14" scope="col">Raya Ganada </th>
    <td align="right" class="print font14 bold red" scope="col">{raya_ganada}</td>
  </tr>
  <tr>
    <th align="left" class="print font14">Raya Pagada </th>
    <td align="right" class="print font14 bold red">{raya_pagada}</td>
  </tr>
  <tr>
    <th align="left" class="print font14">Producci&oacute;n</th>
    <td align="right" class="print font14 bold blue">{produccion}</td>
  </tr>
</table>

{salto}
<!-- END BLOCK : reporte -->
<p align="center" class="noDisplay">
  <input name="cerrar" type="button" id="cerrar" value="Cerrar" />
</p>
</body>
</html>
