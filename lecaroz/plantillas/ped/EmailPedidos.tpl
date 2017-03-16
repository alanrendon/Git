<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Pedidos</title>
<style type="text/css">
body,td,th {
	font-family: Arial, Helvetica, sans-serif;
}

table {
	border: solid 1px #000;
	border-collapse: collapse;
	empty-cells: show;
}

th, td {
	border: solid 1px #000;
}

th {
	background-color: #999;
}

#footer {
	font-size: 8pt;
	font-weight: bold;
}

.blue {
	color: #00C;
}

.green {
	color: #060;
}

.red {
	color: #C00;
}

.bold {
	font-weight: bold;
}

.underline {
	text-decoration: underline;
}

.urgente {
	background-color: #F90;
}

.font14 {
	font-size: 14pt;
}

</style>

</head>
<!-- THE CAKE IS A LIE -->
<body>
<p align="center"><strong>Oficinas Administrativas Mollendo S. de R.L. de C.V.</strong></p>
<p align="center"><strong>{num_pro} {nombre_pro}<br />
	{telefono1}</strong></p>
<p align="center"><strong>Pedido de materias primas al {dia} de {mes} de {anio} [Folio: {folio}]</strong></p>
<table align="center">
	<!-- START BLOCK : cia -->
	<tr>
  	<th colspan="6" align="left" class="font14 blue" scope="col">{num_cia} {nombre_cia}</th>
  	</tr>
  <tr>
    <th colspan="2" scope="col">Producto</th>
    <th colspan="2" scope="col">Pedido</th>
    <th colspan="2" scope="col">Entregar</th>
    </tr>
  <!-- START BLOCK : row -->
  <tr id="row"{urgente}>
    <td align="right" class="bold">{codmp}</td>
    <td class="bold">{nombre_mp}</td>
    <td align="right" class="blue bold">{pedido}</td>
    <td align="left" class="blue bold">{unidad}</td>
    <td align="right" class="green bold">{entregar}</td>
    <td class="green bold">{presentacion}</td>
    </tr>
  <!-- END BLOCK : row -->
  <tr>
  	<td colspan="6" align="right">&nbsp;</td>
  	</tr>
  <!-- END BLOCK : cia -->
</table>
<p align="center"><strong>NOTA:</strong> Los pedidos marcados con <span class="bold urgente underline">color</span> son para entrega <strong class="red">URGENTE</strong>.</p>
{anotaciones}
<p class="red"><strong>Favor de responder este email confirmando pedido. Gracias.</strong></p>
</body>
<!-- THE CAKE IS A LIE -->
</html>
</body>
</html>
