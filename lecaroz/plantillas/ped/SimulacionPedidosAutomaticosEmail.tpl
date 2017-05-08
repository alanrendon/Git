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
  	<td><strong>{num_pro}</strong></td>
  	<td align="center"><strong>{nombre_pro}<br />
		<span style="font-size:10pt;">{telefono}</span></strong></td>
  	<td align="right"><strong>{num_pro}</strong></td>
  	</tr>
  <tr>
    <td width="15%"><strong>{codmp}</strong></td>
    <td width="70%" align="center"><strong>{nombre_mp}</strong></td>
    <td width="15%" align="right"><strong>{codmp}</strong></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td align="center"><strong>Pedidos al {dia} de {mes} de {anio}</strong></td>
    <td align="right">&nbsp;</td>
  </tr>
</table>
<br />
<table align="center" class="print">
  <tr>
    <th colspan="2" scope="col">Compa&ntilde;&iacute;a</th>
    <th scope="col">Pedido</th>
    <th scope="col">Unidad</th>
    <th scope="col">Entregar</th>
    </tr>
  <!-- START BLOCK : row -->
  <tr id="row">
    <td align="right">{num_cia}</td>
    <td>{nombre_cia}</td>
    <td align="right">{pedido}</td>
    <td>{unidad}</td>
    <td align="right">{pedido_pro}</td>
    </tr>
  <!-- END BLOCK : row -->
</table>
<p>SE&Ntilde;OR PROVEEDOR LE PEDIMOS VERIFIQUE BIEN NUESTRO PEDIDO Y CORROBORE QUE LOS DATOS SEAN CONGRUENTES CON LOS PEDIDOS DE MESES ANTERIORES, ESTO ES CON EL FIN DE EVITAR ALGUNA DEVOLUCION A LA HORA DE LA ENTREGA Y EN SU DEFECTO EN SU PAGO. SIN MAS POR EL MOMENTO Y AGRADECIENDO SUS ATENCIONES.</p>
<p align="center" class="noDisplay">
	<input name="cerrar" type="button" id="cerrar" value="Cerrar" />
</p>
</body>
</html>
