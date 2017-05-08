<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Untitled Document</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="./styles/impresion.css" rel="stylesheet" type="text/css">
</head>

<body>
<p align="center"><strong><font face="Geneva, Arial, Helvetica, sans-serif" size="+2">Listado de Camionetas</font></strong></p>
<table width="100%" align="center" class="print">
  <tr>
    <th class="print" scope="col">No.</th>
    <th class="print" scope="col">Modelo</th>
    <th class="print" scope="col">A&ntilde;o</th>
    <th class="print" scope="col">Placas</th>
    <th class="print" scope="col">Color</th>
    <th class="print" scope="col">Compa&ntilde;&iacute;a</th>
    <th class="print" scope="col">Propietario</th>
    <th class="print" scope="col">Usuario</th>
    <th class="print" scope="col">No. Serie </th>
    <th class="print" scope="col">No. Motor </th>
  </tr>
  <!-- START BLOCK : fila -->
  <tr>
    <td class="print">{id}</td>
    <td class="vprint">{modelo}</td>
    <td class="print">{anio}</td>
    <td class="print">{placas}</td>
    <td class="print"><img src="{color}"></td>
    <td class="vprint">{num_cia} - {nombre_cia} </td>
    <td class="vprint">{propietario}</td>
    <td class="vprint">{usuario}</td>
    <td class="vprint">{num_serie}</td>
    <td class="vprint">{num_motor}</td>
  </tr>
  <!-- END BLOCK : fila -->
</table>
</body>
</html>
