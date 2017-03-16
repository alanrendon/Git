<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Reporte de Cheques, Transferencias y Otros Pagos</title>
<link href="styles/screen.css" rel="stylesheet" type="text/css" media="screen" />
<link href="styles/print.css" rel="stylesheet" type="text/css" media="print" />
<link href="styles/font-style.css" rel="stylesheet" type="text/css" />
<link href="styles/Tips.css" rel="stylesheet" type="text/css" />
<link href="../../styles/screen.css" rel="stylesheet" type="text/css" media="screen" />
<link href="../../styles/print.css" rel="stylesheet" type="text/css" media="print" />
<link href="../../styles/font-style.css" rel="stylesheet" type="text/css" />

<script type="text/javascript" src="jscripts/mootools/mootools-1.2-core.js"></script>
<script type="text/javascript" src="jscripts/mootools/mootools-1.2-more.js"></script>
<script type="text/javascript" src="jscripts/ban/ReporteCheques.js"></script>
</head>

<body>
<!-- START BLOCK : reporte -->
<table width="98%" align="center" class="encabezado">
  <tr>
    <td align="center">{empresa}</td>
  </tr>
  <tr>
    <td align="center">Reporte de Cheques, Transferencias y Otros Pagos </td>
  </tr>
</table>
<br />
<table align="center" class="print">
  <!-- START BLOCK : cia -->
  <tr>
    <th colspan="10" align="left" class="print font14" scope="col">{num_cia} {nombre_cia} </th>
  </tr>
  <!-- START BLOCK : banco -->
  <tr>
    <th colspan="5" align="left" class="print font12">{banco}</th>
    <th colspan="5" align="left" class="print font12">{cuenta}</th>
  </tr>
  <tr>
    <th class="print">Folio</th>
    <th class="print">Fecha</th>
    <th colspan="2" class="print">Beneficiario</th>
    <th class="print">Concepto</th>
    <th colspan="2" class="print">Gasto</th>
    <th class="print">Cobrado</th>
    <th class="print">Cancelado</th>
    <th class="print">Importe</th>
  </tr>
  <!-- START BLOCK : row -->
  <tr id="row">
    <td align="right" class="print{color}">{folio}</td>
    <td align="center" class="print blue">{fecha}</td>
    <td align="right" class="print">{num_pro}</td>
    <td class="print">{beneficiario}</td>
    <td class="print">{concepto}</td>
    <td align="right" class="print">{cod}</td>
    <td class="print">{gasto} </td>
    <td align="center" class="print green">{cobrado}</td>
    <td align="center" class="print red">{cancelado}</td>
    <td align="right" class="print{color_importe}">{importe}</td>
  </tr>
  <!-- END BLOCK : row -->
  <tr style="border-bottom:double 5px #000;">
    <th colspan="9" align="right" class="print font10">Total Cuenta </th>
    <th align="right" class="print font10">{total}</th>
  </tr>
  <!-- END BLOCK : banco -->
  <tr>
    <th colspan="9" align="right" class="print font10">Total Compa&ntilde;&iacute;a </th>
    <th align="right" class="print font10">{total}</th>
  </tr>
  <tr>
    <td colspan="10" class="print">&nbsp;</td>
  </tr>
  <!-- END BLOCK : cia -->
  <tr>
    <th colspan="9" align="right" class="print font12">Total General </th>
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
