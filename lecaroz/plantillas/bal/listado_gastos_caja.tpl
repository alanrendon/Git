<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Documento sin t&iacute;tulo</title>
<link href="./styles/impresion.css" rel="stylesheet" type="text/css" />
</head>

<body>
<table width="100%">
  <tr>
    <td width="20%">&nbsp;</td>
    <td width="60%" align="center" class="print_encabezado">{oficina}</td>
    <td width="20%">&nbsp;</td>
  </tr>
  <tr>
    <td colspan="3" align="center" class="print_encabezado">Gastos de Caja<br />
    {dia} de {mes} de {anio} </td>
  </tr>
</table>
<br>
<table align="center" class="print">
  <!-- START BLOCK : cia -->
  <tr>
    <th colspan="5" class="vprint_total" scope="col">{num_cia} - {nombre} </th>
  </tr>
  <tr>
    <th class="print">Concepto</th>
    <th class="print">Comentario</th>
    <th class="print">Balance</th>
    <th class="print">Egreso</th>
    <th class="print">Ingreso</th>
  </tr>
  <!-- START BLOCK : gasto -->
  <tr>
    <td class="vprint" style="font-weight:bold;">{cod} {desc} </td>
    <td class="vprint">{comentario}</td>
    <td class="print">{bal}</td>
    <td class="rprint">{egreso}</td>
    <td class="rprint">{ingreso}</td>
  </tr>
  <!-- END BLOCK : gasto -->
  <tr>
    <th colspan="3" class="rprint">Totales</th>
    <th class="rprint_total" style="color:#C00;">{egresos}</th>
    <th class="rprint_total" style="color:#00C;">{ingresos}</th>
  </tr>
  <tr>
    <th colspan="3" class="rprint">Total Compa&ntilde;&iacute;a </th>
    <th colspan="2" class="print_total">{total}</th>
  </tr>
  <tr>
    <td colspan="5" class="print">&nbsp;</td>
  </tr>
  <!-- END BLOCK : cia -->
  <tr>
    <th colspan="3" class="rprint_total">Totales</th>
    <th class="rprint_total" style="color:#C00;font-size:14pt;">{egresos}</th>
    <th class="rprint_total" style="color:#00C;font-size:14pt;">{ingresos}</th>
  </tr>
  <tr>
    <th colspan="3" class="rprint_total">Total General </th>
    <th colspan="2" class="print_total" style="font-size:14pt;">{total}</th>
  </tr>
</table>
</body>
</html>
