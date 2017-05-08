<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Untitled Document</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="./styles/impresion.css" rel="stylesheet" type="text/css">
</head>

<body>
<!-- START BLOCK : hoja -->
<table width="100%">
  <tr>
    <td class="print_encabezado">Cia.: {num_cia} </td>
    <td class="print_encabezado" align="center">{nombre_cia}</td>
    <td class="print_encabezado" align="right">Cia.: {num_cia} </td>
  </tr>
  <tr>
    <td width="20%">&nbsp;</td>
    <td width="60%" class="print_encabezado" align="center">Listado de Trabajadores <br>
      al {dia} de {mes} de {anio} </td>
    <td width="20%">&nbsp;</td>
  </tr>
</table>
  <br>
<table width="100%" align="center" cellpadding="0" cellspacing="0" class="print">
    <tr>
      <th class="print" scope="col">No.</th>
      <th class="print" scope="col">Nombre</th>
      <th class="print" scope="col">Puesto</th>
      <th class="print" scope="col">Turno</th>
      <th class="print" scope="col">Status</th>
      <th class="print" scope="col">Antig&uuml;edad</th>
      <th class="print" scope="col">&Uacute;ltimo Aguinaldo</th>
      <th class="print" scope="col">Nuevo Aguinaldo </th>
    </tr>
    <!-- START BLOCK : fila -->
	<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
      <td class="print">{num_emp}</td>
      <td class="vprint">{nombre}</td>
      <td class="vprint">{puesto}</td>
      <td class="vprint">{turno}</td>
      <td class="print">{solo_aguinaldo}</td>
      <td class="vprint">{antiguedad}</td>
      <td class="rprint">{ultimo_aguinaldo}</td>
      <td class="rprint">{nuevo_aguinaldo}</td>
	</tr>
	<!-- END BLOCK : fila -->
</table>
<br>
<table align="center">
  <tr>
    <td bgcolor="#FF0000">&nbsp;&nbsp;&nbsp;</td>
    <td><font face="Geneva, Arial, Helvetica, sans-serif" size="-2">Aguinaldo por porcentaje</font> </td>
    <td bgcolor="#0000FF">&nbsp;&nbsp;&nbsp;</td>
    <td><font face="Geneva, Arial, Helvetica, sans-serif" size="-2">Aguinaldo calculado</font> </td>
    <td bgcolor="#00FF00">&nbsp;&nbsp;&nbsp;</td>
    <td><font face="Geneva, Arial, Helvetica, sans-serif" size="-2">Aguinaldo modificado</font> </td>
  </tr>
</table>
<br style="page-break-after:always;">
<!-- END BLOCK : hoja -->
<!-- START BLOCK : cerrar -->
<script language="javascript" type="text/javascript">window.onload = self.close()</script>
<!-- END BLOCK : cerrar -->
<!-- START BLOCK : reload -->
<script language="javascript" type="text/javascript">
	function recargar() {
		window.opener.document.location.reload();
		self.close();
	}
	
	window.onload = recargar();
</script>
<!-- END BLOCK : reload -->
<!-- START BLOCK : mensaje -->
<script language="javascript" type="text/javascript">window.onload = alert("Debe configurar la impresora para imprimir horizontalmente")</script>
<!-- END BLOCK : mensaje -->
</body>
</html>
