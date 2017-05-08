<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Untitled Document</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="./styles/impresion.css" rel="stylesheet" type="text/css">
</head>

<body>
<!-- START BLOCK : cerrar -->
<script language="javascript" type="text/javascript">
	window.onload = self.close();
</script>
<!-- END BLOCK : cerrar -->
<!-- START BLOCK : hoja -->
<table width="100%">
  <tr>
    <td class="print_encabezado">{num_cia}</td>
    <td class="print_encabezado" align="center">{nombre_cia}</td>
    <td class="rprint_encabezado">{num_cia}</td>
  </tr>
  <tr>
    <td width="20%">&nbsp;</td>
    <td width="60%" class="print_encabezado" align="center">Listado de Trabajadores </td>
    <td width="20%">&nbsp;</td>
  </tr>
</table>
  <br>
  <table width="98%" align="center" class="print">
    <tr>
      <th width="5%" class="print" scope="col">No.</th>
      <th width="35%" class="print" scope="col">Nombre</th>
      <th width="15%" class="print" scope="col">Puesto</th>
      <th width="15%" class="print" scope="col">Turno</th>
      <th width="15%" class="print" scope="col">Antig&uuml;edad</th>
      <th width="15%" class="print" scope="col">Ultimo Aguinaldo</th>
    </tr>
    <!-- START BLOCK : fila -->
	<tr>
	  <td height="40" class="vprint">{num_emp}</td>
      <td class="vprint">{nombre}</td>
      <td class="vprint">{puesto}</td>
      <td class="vprint">{turno}</td>
      <td class="print"><strong>{asterisco}</strong></td>
      <td class="vprint">&nbsp;</td>
    </tr>
	<!-- END BLOCK : fila -->
	<!-- START BLOCK : total -->
	<tr>
	  <th colspan="5" class="rprint_total">Total de Trabajadores </th>
      <th class="rprint_total">{num_trabajadores}</th>
    </tr>
	<!-- END BLOCK : total -->
</table>
<br style="page-break-after:always;">
<!-- END BLOCK : hoja -->
</body>
</html>
