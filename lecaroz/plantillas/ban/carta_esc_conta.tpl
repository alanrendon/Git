<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Documento sin t&iacute;tulo</title>
<link href="./styles/impresion.css" rel="stylesheet" type="text/css" />
<style type="text/css">
<!--
body,td,th {
	font-family: Arial, Helvetica, sans-serif;
}
-->
</style></head>

<body>
<!-- START BLOCK : carta -->
<p align="right"><strong>MEXICO D.F. A {dia} DE {mes} DE {anio} </strong></p>
<p>&nbsp;</p>
<p><strong>&nbsp;</strong></p>
<p><strong>&nbsp;{contador}<br />
PRESENTE</strong></p>
<p>&nbsp;</p>
<p>POR MEDIO DE ESTE CONDUCTO HAGO ENTREGA DE LOS  ESTADOS DE CUENTA QUE SE DETALLAN A CONTINUACION:</p>
<table align="center" class="print">
  <tr>
    <th class="print" scope="col">Compa&ntilde;&iacute;a</th>
    <th class="print" scope="col">Banco</th>
    <th class="print" scope="col">Cuenta</th>
    <th class="print" scope="col">Mes</th>
    <th class="print" scope="col">A&ntilde;o</th>
  </tr>
  <!-- START BLOCK : fila -->
  <tr>
    <td class="vprint">{nombre}</td>
    <td class="vprint">{banco}</td>
    <td class="print">{cuenta}</td>
    <td class="print">{mes}</td>
    <td class="print">{anio}</td>
  </tr>
  <!-- END BLOCK : fila -->
</table>
{salto}
<!-- END BLOCK : carta -->
<!-- START BLOCK : cerrar -->
<script language="javascript" type="text/javascript">
<!--
function cerrar() {
	alert('No hay estados de cuenta pendientes de imprimir');
	self.close();
}

window.onload = cerrar();
//-->
</script>
<!-- END BLOCK : cerrar -->
</body>
</html>
