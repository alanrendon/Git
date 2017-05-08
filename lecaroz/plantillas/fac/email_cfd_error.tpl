<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Error al generar el comprobante fiscal</title>
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

caption {
	font-weight: bold;
	text-align: left;
	background-color: transparent;
}

li {
	font-size: 8pt;
}

#cia {
	font-size: 12pt;
	font-weight: bold;
}

#error {
	font-size: 12pt;
	font-weight: bold;
	color: #C00;
}

#accion {
	font-size: 18pt;
	font-weight: bold;
	text-decoration: underline;
}

#footer {
	font-size: 8pt;
	font-weight: bold;
}

.mark {
	background-color: #FF6;
}

</style>
</head>
<!-- THE CAKE IS A LIE -->
<body>
<p id="cia">{num_cia} {nombre_cia}<br />
	{rfc_cia}</p>
<p>Error al generar el comprobante fiscal, el sistema reporto lo siguiente:</p>
<p id="error">{codigo} {descripcion}</p>
<p>Para los datos capturados:</p>
<table align="center">
	<caption>
		<li> Los elementos marcados con * son obligatorios
		<li> Los elementos marcados en amarillo presentan alguna anomalia o no estan presente en la informaci&oacute;n
		<li> En el caso de las facturas de venta del d&iacute;a, del domicilio el &uacute;nico dato obligatorio es el &quot;Pa&iacute;s&quot;
	</caption>
	<tr>
		<th colspan="2" align="left">Datos del cliente</th>
	</tr>
	<tr>
		<th align="left">* Cliente</th>
		<td {mark_101}>{nombre_cliente}</td>
	</tr>
	<tr>
		<th align="left">* R.F.C.</th>
		<td {mark_102}{mark_103}>{rfc_cliente}</td>
	</tr>
	<tr>
		<th align="left">* Calle</th>
		<td {mark_104}>{calle}</td>
	</tr>
	<tr>
		<th align="left">No. exterior</th>
		<td>{no_exterior}</td>
	</tr>
	<tr>
		<th align="left">No. interior</th>
		<td>{no_interior}</td>
	</tr>
	<tr>
		<th align="left">* Colonia</th>
		<td {mark_105}>{colonia}</td>
	</tr>
	<tr>
		<th align="left">Localidad</th>
		<td>{localidad}</td>
	</tr>
	<tr>
		<th align="left">Referencia</th>
		<td>{referencia}</td>
	</tr>
	<tr>
		<th align="left">* Delegaci&oacute;n o municipio</th>
		<td {mark_106}>{municipio}</td>
	</tr>
	<tr>
		<th align="left">* Estado</th>
		<td {mark_107}>{estado}</td>
	</tr>
	<tr>
		<th align="left">* Pa&iacute;s</th>
		<td {mark_108}>{pais}</td>
	</tr>
	<tr>
		<th align="left">* C&oacute;digo postal</th>
		<td {mark_109}>{codigo_postal}</td>
	</tr>
</table>
<br />
<table align="center">
	<tr>
		<th colspan="5" align="left" scope="col">Datos de factura</th>
	</tr>
	<tr>
		<th>* Descripci&oacute;n</th>
		<th>* Cantidad</th>
		<th>* Precio</th>
		<th>* Unidad</th>
		<th>* Importe</th>
	</tr>
	<!-- START BLOCK : detalle -->
	<tr>
		<td>{descripcion}</td>
		<td align="right">{cantidad}</td>
		<td align="right">{precio}</td>
		<td>{unidad}</td>
		<td align="right"{mark_importe}>{importe}</td>
	</tr>
	<!-- END BLOCK : detalle -->
	<tr>
		<th colspan="4" align="right">Subtotal</th>
		<th align="right"{mark_subtotal}>{subtotal}</th>
	</tr>
	<tr>
		<th colspan="4" align="right">I.V.A.</th>
		<th align="right"{mark_iva}>{iva}</th>
	</tr>
	<tr>
		<th colspan="4" align="right">Total</th>
		<th align="right"{mark_total}>{total}</th>
	</tr>
</table>
<p id="accion">{accion}</p>
<hr />
<p id="footer">Favor de no responder a este correo electr&oacute;nico, este buz&oacute;n no se supervisa y no recibira respuesta. Si necesita informaci&oacute;n adicional escriba al correo <a href="mailto:{email_info}">{email_info}</a> y con gusto le atenderemos.</p>
</body>
<!-- THE CAKE IS A LIE -->
</html>
