<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Untitled Document</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../../styles/tablas.css" rel="stylesheet" type="text/css">
<link href="../../styles/pages.css" rel="stylesheet" type="text/css">
<link href="../../styles/impresion.css" rel="stylesheet" type="text/css">
</head>

<body>
<table width="100%">
  <tr>
    <td>&nbsp;</td>
    <td class="print_encabezado" align="center">Oficinas Administrativas Mollendo S. de R.L. y C.V. </td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td width="20%">&nbsp;</td>
    <td width="60%" class="print_encabezado" align="center">Trasferencias Electr&oacute;nicas Pendientes de Procesar</td>
    <td width="20%">&nbsp;</td>
  </tr>
</table>
<br>
<table align="center" class="print">
  <!-- START BLOCK : pro -->
  <tr>
    <th colspan="6" class="print" scope="col">{num_pro} {nombre} </th>
  </tr>
  <tr>
    <th class="print" scope="col">Compa&ntilde;&iacute;a</th>
    <th class="print" scope="col">Fecha</th>
    <th class="print" scope="col">Folio</th>
    <th class="print" scope="col">Concepto</th>
    <th class="print" scope="col">Facturas</th>
    <th class="print" scope="col">Importe</th>
  </tr>
  <!-- START BLOCK : fila -->
  <tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
    <td class="vprint">{num_cia} {nombre} </td>
    <td class="print">{fecha}</td>
    <td class="print">{folio}</td>
    <td class="vprint">{concepto}</td>
    <td class="vprint">{facturas}</td>
    <td class="rprint">{importe}</td>
  </tr>
  <!-- END BLOCK : fila -->
  <tr>
    <th colspan="5" class="rprint">Total</th>
    <th class="rprint_total">{total}</th>
  </tr>
  <tr>
    <th colspan="6">&nbsp;</th>
  </tr>
  <!-- END BLOCK : pro -->
  <!-- START BLOCK : total -->
  <tr>
    <th colspan="5" class="rprint">Gran Total </th>
    <th class="rprint_total">{gran_total}</th>
  </tr>
  <!-- END BLOCK : total -->
  <!-- START BLOCK : no_result -->
  <tr>
    <th colspan="6" class="print_total">No hay transferencias pendientes </th>
  </tr>
  <!-- END BLOCK : no_result -->
</table>
<p align="center">
  <input type="button" class="boton" value="Regresar" onClick="document.location='./ban_gen_tra.php'">
</p>
</body>
</html>
