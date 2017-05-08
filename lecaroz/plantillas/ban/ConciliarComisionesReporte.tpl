<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Reporte de Comisiones Conciliadas</title>
<link href="styles/screen.css" rel="stylesheet" type="text/css" media="screen" />
<link href="styles/print.css" rel="stylesheet" type="text/css" media="print" />
<link href="styles/font-style.css" rel="stylesheet" type="text/css" />
<link href="../../styles/screen.css" rel="stylesheet" type="text/css" media="screen" />
<link href="../../styles/print.css" rel="stylesheet" type="text/css" media="print" />
<link href="../../styles/font-style.css" rel="stylesheet" type="text/css" />

<script type="text/javascript" src="jscripts/mootools/mootools-1.2-core.js"></script>
<script type="text/javascript" src="jscripts/ban/ConciliarComisionesReporte.js"></script>
</head>

<body>
<!-- START BLOCK : reporte -->
<table width="98%" align="center" class="encabezado">
  <tr>
    <td align="center">Reporte de Comisiones Conciliadas ({banco}) </td>
  </tr>
  <tr>
    <td align="center">{fecha}</td>
  </tr>
</table>
<br />
<table align="center" class="print">
  <!-- START BLOCK : cia -->
  <tr>
    <th colspan="2" align="left" class="print font12" scope="col">{num_cia} {nombre_cia} </th>
    <th align="left" class="print font12" scope="col">{cuenta}</th>
  </tr>
  <tr>
    <th class="print">Fecha</th>
    <th class="print">Concepto</th>
    <th class="print">Importe</th>
  </tr>
  <!-- START BLOCK : row -->
  <tr id="row">
    <td align="center" class="print">{fecha}</td>
    <td class="print">{concepto}</td>
    <td align="right" class="print">{importe}</td>
  </tr>
  <!-- END BLOCK : row -->
  <tr style="border-bottom:double 5px #000;">
    <th colspan="2" align="right" class="print font10">Total</th>
    <th align="right" class="print font10">{total}</th>
  </tr>
  <tr>
    <td colspan="3" class="print">&nbsp;</td>
  </tr>
  <!-- END BLOCK : cia -->
  <tr>
    <th colspan="2" align="right" class="print font12">Total de Comisiones </th>
    <th align="right" class="print font12">{total}</th>
  </tr>
</table>
{salto}
<!-- END BLOCK : reporte -->
<p align="center" class="noDisplay">
  <input name="cerrar" type="button" id="cerrar" value="Cerrar" />
</p>
</body>
</html>
