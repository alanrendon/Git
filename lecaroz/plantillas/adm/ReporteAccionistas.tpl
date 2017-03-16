<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252" />
<title>Reporte de Accionistas</title>
<link href="../../styles/screen.css" rel="stylesheet" type="text/css" media="screen" />
<link href="../../styles/print.css" rel="stylesheet" type="text/css" media="print" />
<link href="../../styles/font-style.css" rel="stylesheet" type="text/css" />
<link href="styles/screen.css" rel="stylesheet" type="text/css" media="screen" />
<link href="styles/print.css" rel="stylesheet" type="text/css" media="print" />
<link href="styles/font-style.css" rel="stylesheet" type="text/css" />

<script type="text/javascript" src="jscripts/mootools/mootools-1.2-core.js"></script>
<script type="text/javascript" src="jscripts/mootools/mootools-1.2-more.js"></script>
<script type="text/javascript" src="jscripts/adm/ReporteAccionistas.js"></script>
</head>

<body>
<!-- START BLOCK : reporte -->
<table width="98%" align="center" class="print">
  <tr>
    <th width="28%" class="print" scope="col">Compa&ntilde;&iacute;a</th>
    <th width="9%" class="print" scope="col">Total</th>
    <th width="9%" class="print" scope="col">{accionista0}</th>
    <th width="9%" class="print" scope="col">{accionista1}</th>
    <th width="9%" class="print" scope="col">{accionista2}</th>
    <th width="9%" class="print" scope="col">{accionista3}</th>
    <th width="9%" class="print" scope="col">{accionista4}</th>
    <th width="9%" class="print" scope="col">{accionista5}</th>
    <th width="9%" class="print" scope="col">{accionista6}</th>
  </tr>
  <!-- START BLOCK : fila -->
  <tr id="row">
    <td align="left" class="print bold">{num_cia} {nombre}</td>
    <td align="right" class="print bold{color}">{total}</td>
    <td align="right" class="print{color0}">{valor0}</td>
    <td align="right" class="print{color1}">{valor1}</td>
    <td align="right" class="print{color2}">{valor2}</td>
    <td align="right" class="print{color3}">{valor3}</td>
    <td align="right" class="print{color4}">{valor4}</td>
    <td align="right" class="print{color5}">{valor5}</td>
    <td align="right" class="print{color6}">{valor6}</td>
  </tr>
  <!-- END BLOCK : fila -->
  <!-- START BLOCK : totales -->
  <tr>
    <th align="right" class="print">Total</th>
    <th align="right" class="print">{total}</th>
    <th align="right" class="print">{total0}</th>
    <th align="right" class="print">{total1}</th>
    <th align="right" class="print">{total2}</th>
    <th align="right" class="print">{total3}</th>
    <th align="right" class="print">{total4}</th>
    <th align="right" class="print">{total5}</th>
    <th align="right" class="print">{total6}</th>
  </tr>
  <!-- END BLOCK : totales -->
</table>
<br style="page-break-after:always;">
<!-- END BLOCK : reporte -->
<p align="center" class="noDisplay">
  <input name="cerrar" type="button" id="cerrar" value="Cerrar" />
</p>
</body>
</html>
