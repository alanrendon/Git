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
    <td width="60%" class="print_encabezado" align="center">Productos pendientes de ingresar a inventario <br>
      al {dia} de {mes} de {anio} </td>
    <td width="20%">&nbsp;</td>
  </tr>
</table>
  <br>
  <table align="center" class="print">
    <!-- START BLOCK : cia -->
	<tr>
      <th colspan="5" class="print" scope="col">{num_cia} - {nombre_cia} </th>
    </tr>
    <tr>
      <th class="print" scope="col">Fecha</th>
      <th class="print" scope="col">Proveedor</th>
      <th class="print" scope="col">Producto</th>
      <th class="print" scope="col">Cantidad</th>
      <th class="print" scope="col">Importe</th>
    </tr>
    <!-- START BLOCK : fila -->
	<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
      <td class="print">{fecha}</td>
      <td class="vprint">{proveedor}</td>
      <td class="vprint">{producto}</td>
      <td class="print">{cantidad}</td>
      <td class="rprint">{importe}</td>
    </tr>
	  <!-- END BLOCK : fila -->
	<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
	  <td colspan="5">&nbsp;</td>
    </tr>
	  <!-- END BLOCK : cia -->
	  <!-- START BLOCK : no_result -->
	  <tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
	  <th colspan="5" class="print">No hay resultados </th>
	  </tr>
	  <!-- END BLOCK : no_result -->
</table>
</body>
</html>
