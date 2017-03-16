<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Pedidos</title>
<link href="/lecaroz/styles/screen.css" rel="stylesheet" type="text/css" media="screen" />
<link href="/lecaroz/styles/print.css" rel="stylesheet" type="text/css" media="print" />
<link href="/lecaroz/styles/font-style.css" rel="stylesheet" type="text/css" />

<script type="text/javascript" src="jscripts/mootools/mootools-1.2-core.js"></script>
<script type="text/javascript" src="jscripts/mootools/extensiones.js"></script>
<script type="text/javascript" src="jscripts/ped/ReportePedidos.js"></script>

</head>

<body>
<!-- START BLOCK : reporte -->
<table width="98%" align="center" class="encabezado">
  <tr>
    <td width="15%" class="font14">{num_pro}</td>
    <td width="70%" align="center" class="font14">{nombre_pro}</td>
    <td width="15%" align="right" class="font14">{num_pro}</td>
  </tr>
  <tr>
  	<td class="font14">&nbsp;</td>
  	<td align="center">{telefono1}</td>
  	<td align="right" class="font14">&nbsp;</td>
  	</tr>
  <tr>
    <td class="font8">&nbsp;</td>
    <td align="center">Pedidos al {dia} de {mes} de {anio}</td>
    <td align="right" class="font8 bold">FOLIO: {folio}</td>
  </tr>
</table>
<br />
<table width="80%" align="center" class="print">
  <!-- START BLOCK : cia -->
  <tr>
  	<th colspan="6" align="left" class="print font12" scope="col">{num_cia} {nombre_cia}</th>
  	</tr>
  <tr>
    <th colspan="2" class="print" scope="col">Producto</th>
    <th colspan="2" class="print" scope="col">Pedido</th>
    <th colspan="2" class="print" scope="col">Entregar</th>
    </tr>
  <!-- START BLOCK : row -->
  <tr id="row">
    <td align="right" class="print">{codmp}</td>
    <td class="print">{nombre_mp}</td>
    <td align="right" class="print blue">{pedido}</td>
    <td align="left" class="print blue">{unidad}</td>
    <td align="right" class="print {urgente}">{entregar}</td>
    <td class="print {urgente}">{presentacion}</td>
    </tr>
  <!-- END BLOCK : row -->
  <tr>
  	<td colspan="6" align="right" class="print">&nbsp;</td>
  	</tr>
  <!-- END BLOCK : cia -->
</table>
<p align="center"><strong>NOTA:</strong> Los productos <span class="underline"><strong>subrayados</strong></span> son para entrega <strong class="red">URGENTE</strong>.</p>
{anotaciones}
<p><strong>
	SE&Ntilde;OR PROVEEDOR LE PEDIMOS VERIFIQUE BIEN NUESTRO PEDIDO Y CORROBORE QUE LOS DATOS SEAN CONGRUENTES CON LOS PEDIDOS DE MESES ANTERIORES, ESTO ES CON EL FIN DE EVITAR ALGUNA DEVOLUCION A LA HORA DE LA ENTREGA Y EN SU DEFECTO EN SU PAGO. SIN MAS POR EL MOMENTO Y AGRADECIENDO SUS ATENCIONES.
</strong></p>
{salto}
<!-- END BLOCK : reporte -->
<p align="center" class="noDisplay">
  <input name="cerrar" type="button" id="cerrar" value="Cerrar" />
</p>
</body>
</html>
