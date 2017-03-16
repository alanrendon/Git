<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Documento sin t&iacute;tulo</title>
<link href="./styles/impresion.css" rel="stylesheet" type="text/css" />
</head>

<body>
<!-- START BLOCK : operadora -->
<table align="center" class="print">
  <tr>
    <th colspan="5" class="print" scope="col" style="font-size:12pt;">{operadora}</th>
  </tr>
  <!-- START BLOCK : cia -->
  <tr>
    <th colspan="5" class="vprint" style="font-size:10pt;">{num_cia} {nombre_cia} </th>
  </tr>
  <!-- START BLOCK : fecha -->
  <tr>
    <th colspan="5" class="vprint">{fecha}</th>
  </tr>
  <tr>
    <th class="print">Producto</th>
    <th class="print">Turno</th>
    <th class="print">Promedio</th>
    <th class="print">Consumo</th>
    <th class="print">% Excedente </th>
  </tr>
  <!-- START BLOCK : producto -->
  <tr{style}>
    <td class="vprint">{codmp} {producto} </td>
    <td class="print">{turno}</td>
    <td class="rprint">{promedio}</td>
    <td class="rprint">{consumo}</td>
    <td class="rprint">{diferencia}</td>
  </tr>
  <!-- END BLOCK : producto -->
  <tr>
    <td colspan="5" class="vprint">&nbsp;</td>
  </tr>
  <!-- END BLOCK : fecha -->
  <!-- END BLOCK : cia -->
</table>
{salto}
<!-- END BLOCK : operadora -->
</body>
</html>
