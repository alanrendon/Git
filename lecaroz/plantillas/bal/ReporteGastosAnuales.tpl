<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Reporte de Gastos Anuales</title>
<link href="styles/screen.css" rel="stylesheet" type="text/css" media="screen" />
<link href="styles/print.css" rel="stylesheet" type="text/css" media="print" />
<link href="styles/font-style.css" rel="stylesheet" type="text/css" />
<link href="../../styles/screen.css" rel="stylesheet" type="text/css" media="screen" />
<link href="../../styles/print.css" rel="stylesheet" type="text/css" media="print" />
<link href="../../styles/font-style.css" rel="stylesheet" type="text/css" />

<script type="text/javascript" src="jscripts/mootools/mootools-1.2-core.js"></script>
<script type="text/javascript" src="jscripts/mootools/mootools-1.2-more.js"></script>
<script type="text/javascript" src="jscripts/mootools/extensiones.js"></script>
<script type="text/javascript" src="jscripts/bal/ReporteGastosAnuales.js"></script>

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
    <td align="center">Reporte de Gastos {anyo} </td>
    <td align="right" class="font8">&nbsp;</td>
  </tr>
</table>
<br />
<table width="99%" align="center" class="print">
  <!-- START BLOCK : tipo -->
  <tr>
    <th colspan="14" align="left" class="print font12" scope="col">GASTOS {tipo} {leyenda} </th>
  </tr>
  <tr>
    <th width="28%" colspan="2" class="print" scope="col">Concepto</th>
    <th width="6%" class="print" scope="col">Ene</th>
    <th width="6%" class="print" scope="col">Feb</th>
    <th width="6%" class="print" scope="col">Mar</th>
    <th width="6%" class="print" scope="col">Abr</th>
    <th width="6%" class="print" scope="col">May</th>
    <th width="6%" class="print" scope="col">Jun</th>
    <th width="6%" class="print" scope="col">Jul</th>
    <th width="6%" class="print" scope="col">Ago</th>
    <th width="6%" class="print" scope="col">Sep</th>
    <th width="6%" class="print" scope="col">Oct</th>
    <th width="6%" class="print" scope="col">Nov</th>
    <th width="6%" class="print" scope="col">Dic</th>
  </tr>
  <!-- START BLOCK : row -->
  <tr id="row">
    <td class="print">{cod}</td>
    <td class="print">{descripcion}</td>
    <td align="right" class="print blue">{1}</td>
    <td align="right" class="print red">{2}</td>
    <td align="right" class="print green">{3}</td>
    <td align="right" class="print purple">{4}</td>
    <td align="right" class="print orange">{5}</td>
    <td align="right" class="print blue">{6}</td>
    <td align="right" class="print red">{7}</td>
    <td align="right" class="print green">{8}</td>
    <td align="right" class="print purple">{9}</td>
    <td align="right" class="print orange">{10}</td>
    <td align="right" class="print blue">{11}</td>
    <td align="right" class="print red">{12}</td>
  </tr>
  <!-- END BLOCK : row -->
  <!-- START BLOCK : subtotales -->
  <tr>
    <th colspan="2" class="print font10">Subtotales</th>
    <th align="right" class="print font10">{1}</th>
    <th align="right" class="print font10">{2}</th>
    <th align="right" class="print font10">{3}</th>
    <th align="right" class="print font10">{4}</th>
    <th align="right" class="print font10">{5}</th>
    <th align="right" class="print font10">{6}</th>
    <th align="right" class="print font10">{7}</th>
    <th align="right" class="print font10">{8}</th>
    <th align="right" class="print font10">{9}</th>
    <th align="right" class="print font10">{10}</th>
    <th align="right" class="print font10">{11}</th>
    <th align="right" class="print font10">{12}</th>
  </tr>
  <tr>
    <td colspan="14" class="print">&nbsp;</td>
  </tr>
  <!-- END BLOCK : subtotales -->
  <!-- END BLOCK : tipo -->
  <!-- START BLOCK : totales -->
  <tr>
    <th colspan="2" class="print font10">Totales</th>
    <th align="right" class="print font10">{1}</th>
    <th align="right" class="print font10">{2}</th>
    <th align="right" class="print font10">{3}</th>
    <th align="right" class="print font10">{4}</th>
    <th align="right" class="print font10">{5}</th>
    <th align="right" class="print font10">{6}</th>
    <th align="right" class="print font10">{7}</th>
    <th align="right" class="print font10">{8}</th>
    <th align="right" class="print font10">{9}</th>
    <th align="right" class="print font10">{10}</th>
    <th align="right" class="print font10">{11}</th>
    <th align="right" class="print font10">{12}</th>
  </tr>
  <!-- END BLOCK : totales -->
</table>
{salto}
<!-- END BLOCK : reporte -->
<p align="center" class="noDisplay">
  <input name="cerrar" type="button" id="cerrar" value="Cerrar" />
</p>
</body>
</html>
