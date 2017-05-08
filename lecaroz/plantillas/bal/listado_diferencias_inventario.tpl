<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Diferencias de Inventario</title>
<link href="../../styles/impresion.css" rel="stylesheet" type="text/css" />
<link href="./styles/impresion.css" rel="stylesheet" type="text/css" />
</head>

<body>
<!-- START BLOCK : listado -->
<table width="100%">
  <tr>
    <td width="20%" class="print_encabezado">{num_cia}</td>
    <td width="60%" align="center" class="print_encabezado">{nombre}</td>
    <td width="20%" align="right" class="print_encabezado">{num_cia}</td>
  </tr>
  <tr>
    <td colspan="3" align="center" class="print_encabezado">Diferencias de Inventario{continuacion}<br />{mes} {anio} </td>
  </tr>
</table>
<br>
<table align="center" class="print">
  <tr>
    <th rowspan="2" class="print" scope="col">Producto</th>
    <th rowspan="2" class="print" scope="col">Precio<br />
    Unitario</th>
    <th rowspan="2" class="print" scope="col">Existencia<br />
    Sistema</th>
    <th rowspan="2" class="print" scope="col">Existencia<br />
      F&iacute;sica</th>
    <th colspan="2" class="print" scope="col">Faltantes</th>
    <th colspan="2" class="print" scope="col">Sobrantes</th>
  </tr>
  <tr>
    <th class="print" scope="col">Cantidad</th>
    <th class="print" scope="col">Costo</th>
    <th class="print" scope="col">Cantidad</th>
    <th class="print" scope="col">Costo</th>
  </tr>
  <!-- START BLOCK : fila -->
  <tr>
    <td class="vprint">{codmp} {producto} </td>
    <td class="rprint">{precio_unidad}</td>
    <td class="rprint" style="color:#FF6600">{sistema}</td>
    <td class="rprint" style="color:#6600CC">{fisica}</td>
    <td class="rprint" style="color:#C00;">{ufaltantes}</td>
    <td class="rprint" style="color:#C00;">{vfaltantes}</td>
    <td class="rprint" style="color:#00C;">{usobrantes}</td>
    <td class="rprint" style="color:#00C;">{vsobrantes}</td>
  </tr>
  <!-- END BLOCK : fila -->
  <!-- START BLOCK : totales -->
  <tr>
    <th colspan="4" class="rprint_total">Totales</th>
    <th colspan="2" class="print_total" style="color:#C00;">{faltantes}</th>
    <th colspan="2" class="print_total" style="color:#00C;">{sobrantes}</th>
  </tr>
  <tr>
    <th colspan="4" class="rprint_total">Total General </th>
    <th colspan="4" class="print_total">{total}</th>
  </tr>
  <!-- END BLOCK : totales -->
  <!-- START BLOCK : tanque -->
  <tr>
    <th colspan="4" class="rprint_total">Capacidad de tanque de gas {tanque} total y al 90%</th>
    <th colspan="2" class="print_total">{capacidad}</th>
    <th colspan="2" class="print_total">{capacidad_90}</th>
  </tr>
  <!-- END BLOCK : tanque -->
  <!-- START BLOCK : observaciones -->
  <tr>
    <th colspan="8" class="print_total">Observaciones</th>
  </tr>
  <tr>
    <td colspan="8" class="vprint">{observaciones}</td>
  </tr>
  <!-- END BLOCK : observaciones -->
</table>
{salto}
<!-- END BLOCK : listado -->
</body>
</html>
