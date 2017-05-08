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
    <td colspan="3" align="center" class="print_encabezado">Dispersi&oacute;n de Gastos de Caja {fecha} <br />
    {descripcion}, {tipo}{bal} </td>
  </tr>
</table>
<br>
<table align="center" class="print">
  <tr>
    <th class="print" scope="col">Compa&ntilde;ia</th>
    <th class="print" scope="col">Importe</th>
  </tr>
  <!-- START BLOCK : fila -->
  <tr>
    <td class="vprint">{num_cia} {nombre} </td>
    <td class="rprint">{importe}</td>
  </tr>
  <!-- END BLOCK : fila -->
  <tr>
    <th class="rprint">Total</th>
    <th class="rprint_total">{total}</th>
  </tr>
</table>
</body>
</html>
