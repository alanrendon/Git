<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Documento sin t&iacute;tulo</title>
<link href="../../styles/pages.css" rel="stylesheet" type="text/css" />
<link href="../../styles/impresion.css" rel="stylesheet" type="text/css" />
</head>

<body>
<table align="center" class="print">
  <tr>
    <th class="print" scope="col">Proveedor</th>
    <th class="print" scope="col">Factura</th>
    <th class="print" scope="col">Fecha</th>
    <th class="print" scope="col">Total</th>
    <th class="print" scope="col">Copia</th>
	<th class="print" scope="col">Validadas</th>
  </tr>
  <!-- START BLOCK : fila -->
  <tr>
    <td class="vprint">{num_pro} {nombre} </td>
    <td class="print">{num_fact}</td>
    <td class="print">{fecha}</td>
    <td class="rprint">{total}</td>
    <td class="print" style="color:#CC0000">{cop}</td>
	<td class="print" style="color:#0000CC">{por}</td>
  </tr>
  <!-- END BLOCK : fila -->
  <tr>
    <th colspan="3" class="rprint">Total</th>
    <th class="rprint_total">{total}</th>
    <th colspan="2" class="rprint_total">&nbsp;</th>
  </tr>
</table>
<p align="center">
  <input type="button" class="boton" value="Cerrar" onclick="self.close()" />
</p>
</body>
</html>
