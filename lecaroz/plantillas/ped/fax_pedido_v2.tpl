<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Untitled Document</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<style type="text/css">
<!--
.table_header {
	border-bottom: 1px solid Black;
}
.style4 {font-family: Arial, Helvetica, sans-serif; font-size: x-small; }
.style7 {font-family: Arial, Helvetica, sans-serif; font-weight: bold; font-size: x-small; }
.style10 {font-family: Arial, Helvetica, sans-serif; font-weight: bold; font-size: medium; }
-->
</style>
</head>

<body>
<!-- START BLOCK : fax -->
<table width="100%">
  <tr>
    <td>&nbsp;</td>
    <td align="center"><span class="style10">Oficinas Administrativas Mollendo S. de R.L. y C.V.</span></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td width="20%">&nbsp;</td>
    <td width="60%" align="center"><span class="style10">Pedido de Materias Primas <br>
    del {dia} de {mes} de {anio} </span></td>
    <td width="20%">&nbsp;</td>
  </tr>
</table>
  <br>
  <table width="400" cellspacing="4">
    <tr>
      <td valign="top"><span class="style7">{num_proveedor}</span></td>
  <td valign="top"><span class="style7">{nombre_proveedor}<br>
        {direccion}
            <br>
            R.F.C. {rfc}<br>
            {telefono}</span></td>
    </tr>
</table>
  <br>
  <table cellspacing="4">
    <tr>
      <th colspan="2" class="table_header" scope="col"><span class="style4">C&oacute;d. y Descripci&oacute;n del Producto </span></th>
      <th class="table_header" scope="col"><span class="style4">Unidad</span></th>
      <th class="table_header" scope="col"><span class="style4">Contenido</span></th>
      <th class="style4" scope="col">Entregar a:</th>
    </tr>
    <!-- START BLOCK : bloque -->
	<tr valign="bottom">
	  <td colspan="4" align="right">&nbsp;</td>
	  <!-- START BLOCK : nombre_cia -->
	  <td align="center" class="style4">{cia}</td>
	  <!-- END BLOCK : nombre_cia -->
    </tr>
	<!-- START BLOCK : fila -->
	<tr valign="bottom">
      <td align="right"><span class="style4">{codmp}</span></td>
      <td align="left"><span class="style4">{nombre}</span></td>
      <td align="center"><span class="style4">{unidad}</span></td>
      <td align="center"><span class="style4">{contenido}</span></td>
      <!-- START BLOCK : cia -->
	  <td align="center"><span class="style4">{entrega}</span></td>
	  <!-- END BLOCK : cia -->
    </tr>
	<!-- END BLOCK : fila -->
	<!-- END BLOCK : bloque -->
</table>
  <br style="page-break-after:always;">
  <!-- END BLOCK : fax -->
  <!-- START BLOCK : cerrar -->
  <script language="javascript">window.onload=self.close()</script>
  <!-- END BLOCK : cerrar -->
</body>
</html>
