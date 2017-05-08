<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Documento sin t&iacute;tulo</title>
<link href="./styles/impresion.css" rel="stylesheet" type="text/css" />
</head>

<body>
<!-- START BLOCK : listado -->
<table width="100%">
  <tr>
    <td>&nbsp;</td>
    <td class="print_encabezado" align="center">Zapaterias Elite </td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td width="20%">&nbsp;</td>
    <td width="60%" class="print_encabezado" align="center">Facturas Pendientes{periodo}</td>
    <td width="20%">&nbsp;</td>
  </tr>
</table>
<br>
<table align="center" class="print">
  <tr>
    <th class="print" scope="col">Compa&ntilde;&iacute;a</th>
    <th class="print" scope="col">Facturas</th>
    <th class="print" scope="col">Vencidas</th>
    <th class="print" scope="col">Total</th>
  </tr>
  <!-- START BLOCK : fila -->
  <tr>
    <td class="vprint">{num_cia} {nombre}</td>
    <td class="rprint" style="color:#00C">{facturas}</td>
    <td class="rprint" style="color:#C00">{remisiones}</td>
    <td class="rprint">{total}</td>
  </tr>
  <!-- END BLOCK : fila -->
  <tr>
    <th class="rprint_total">Total</th>
    <th class="rprint_total" style="color:#00C">{facturas}</th>
    <th class="rprint_total" style="color:#C00">{remisiones}</th>
    <th class="rprint_total">{total}</th>
  </tr>
</table>
<!-- END BLOCK : listado -->
<!-- START BLOCK : cerrar -->
<script language="javascript" type="text/javascript">
<!--
function cerrar() {
	window.opener.alert('No hay resultados');
	
	self.close();
}

window.onload = cerrar();
-->
</script>
<!-- END BLOCK : cerrar -->
</body>
</html>
