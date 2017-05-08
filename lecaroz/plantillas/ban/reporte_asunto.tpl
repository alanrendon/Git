<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Untitled Document</title>
<link href="./styles/impresion.css" rel="stylesheet" type="text/css" />
</head>

<body>
<table width="100%">
  <tr>
    <td width="20%">&nbsp;</td>
    <td width="60%" align="center" class="print_encabezado">Oficinas Administrativas Mollendo S. de R.L. y C.V. </td>
    <td width="20%">&nbsp;</td>
  </tr>
</table>
<br>
<table align="center" class="print">
  <tr>
    <th class="print" scope="col">Compa&ntilde;&iacute;a</th>
    <th class="print" scope="col">Folio</th>
    <th class="print" scope="col">Fecha</th>
    <th class="print" scope="col">Dirigida a</th>
    <th class="print" scope="col">Referencia</th>
  </tr>
  <tr>
    <td class="vprint" style="font-size:10pt;font-weight:bold;">{num_cia} {nombre}</td>
    <td class="print" style="font-size:10pt;font-weight:bold;">{folio}</td>
    <td class="print" style="font-size:10pt;font-weight:bold;">{fecha}</td>
    <td class="vprint" style="font-size:10pt;font-weight:bold;">{atencion}</td>
    <td class="vprint" style="font-size:10pt;font-weight:bold;">{referencia}</td>
  </tr>
</table>
<!-- START BLOCK : detalle -->
<br />
<table width="80%" align="center" class="print">
  <tr>
    <th class="print" scope="col">Fecha de<br />
    respuesta</th>
    <th class="print" scope="col">Dependencia</th>
    <th class="print" scope="col">Responsable</th>
    <th class="print" scope="col">Observaciones</th>
  </tr>
  <!-- START BLOCK : fila -->
  <tr>
    <td class="print">{fecha_respuesta}</td>
    <td class="vprint">{dependencia}</td>
    <td class="vprint">{responsable}</td>
    <td class="vprint">{observaciones}</td>
  </tr>
  <!-- END BLOCK : fila -->
</table>
<!-- END BLOCK : detalle -->
</body>
</html>
