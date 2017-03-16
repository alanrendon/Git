<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Pedidos</title>

<style type="text/css">
body,td,th {
	font-family: Arial, Helvetica, sans-serif;
}
</style>
</head>

<body>
<table width="98%" align="center" class="encabezado">
  <tr>
  	<td width="15%"><strong>{num_pro}</strong></td>
  	<td width="70%" align="center"><strong>{nombre_pro}<br />
		<span style="font-size:10pt;">{telefono}</span></strong></td>
  	<td width="15%" align="right"><strong>{num_pro}</strong></td>
  	</tr>
  <tr>
  	<td>&nbsp;</td>
  	<td align="center"><strong>Pedidos al {dia} de {mes} de {anio}</strong></td>
  	<td align="right">&nbsp;</td>
  	</tr>
</table>
<br />
<table border="1" align="center" class="print">
  <!-- START BLOCK : cia -->
  <tr>
  	<th colspan="5" align="left" class="print font12" scope="col">{num_cia} {nombre_cia}</th>
  	</tr>
  <tr>
    <th colspan="2" class="print" scope="col">Producto</th>
    <th class="print" scope="col">Pedido</th>
    <th class="print" scope="col">Unidad</th>
    <th class="print" scope="col">Entregar</th>
    </tr>
  <!-- START BLOCK : pro -->
  <tr id="row">
    <td align="right" class="print">{codmp}</td>
    <td class="print">{nombre_mp}</td>
    <td align="right" class="print">{pedido}</td>
    <td class="print">{unidad}</td>
    <td align="right" class="print blue">{pedido_pro}</td>
    </tr>
  <!-- END BLOCK : pro -->
  <tr>
  	<td colspan="5" align="right" class="print">&nbsp;</td>
  	</tr>
  <!-- END BLOCK : cia -->
</table>
<p>SE&Ntilde;OR PROVEEDOR LE PEDIMOS VERIFIQUE BIEN NUESTRO PEDIDO Y CORROBORE QUE LOS DATOS SEAN CONGRUENTES CON LOS PEDIDOS DE MESES ANTERIORES, ESTO ES CON EL FIN DE EVITAR ALGUNA DEVOLUCION A LA HORA DE LA ENTREGA Y EN SU DEFECTO EN SU PAGO. SIN MAS POR EL MOMENTO Y AGRADECIENDO SUS ATENCIONES.</p>
<p align="center" class="noDisplay">
	<input name="cerrar" type="button" id="cerrar" value="Cerrar" />
</p>
</body>
</html>
