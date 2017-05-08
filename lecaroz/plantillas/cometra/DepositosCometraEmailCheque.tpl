<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title></title>
<style type="text/css">
body, td, th {
	font-family: Arial, Helvetica, sans-serif;
}

table {
	border: solid 1px #000;
	border-collapse: collapse;
}

td, th {
	border: solid 1px #000;
}
</style>
</head>
<body>
<p style="font-size:18pt;"><strong>{num_cia} {nombre_cia}</strong></p>
<p style="font-size:18pt;">CON ATENCION PARA <strong>{encargado}</strong></p>
<p style="font-size:18pt;">LE RECORDAMOS QUE CUANDO RECIBA UN CHEQUE<strong></strong> LO TENDRA QUE DESGLOSAR EN EL COMPROBANTE Y EN EL SISTEMA DE CAPTURA.</p>
<p style="font-size:18pt;">USTED ENVIO</p>
<table>
	<tr>
		<th scope="col">Compa&ntilde;&iacute;a</th>
		<th scope="col">Tipo</th>
		<th scope="col">Fecha</th>
		<th scope="col">Concepto</th>
		<th scope="col">Importe</th>
	</tr>
	<!-- START BLOCK : row1 -->
	<tr>
		<td>{num_cia} {nombre_cia}</td>
		<td>{cod_mov} {descripcion}</td>
		<td align="center">{fecha}</td>
		<td>{concepto}</td>
		<td align="right">{importe}</td>
	</tr>
	<!-- END BLOCK : row1 -->
	<tr>
		<th colspan="4" align="right">Total</th>
		<th align="right">{total1}</th>
	</tr>
</table>
<p style="font-size:18pt;">Y DEBIA SER</p>
<table>
	<tr>
		<th scope="col">Compa&ntilde;&iacute;a</th>
		<th scope="col">Tipo</th>
		<th scope="col">Fecha</th>
		<th scope="col">Concepto</th>
		<th scope="col">Importe</th>
	</tr>
	<!-- START BLOCK : row2 -->
	<tr>
		<td>{num_cia} {nombre_cia}</td>
		<td>{cod_mov} {descripcion}</td>
		<td align="center">{fecha}</td>
		<td>{concepto}</td>
		<td align="right">{importe}</td>
	</tr>
	<!-- END BLOCK : row2 -->
	<tr>
		<th colspan="4" align="right">Total</th>
		<th align="right">{total2}</th>
	</tr>
</table>
<p style="font-size:18pt;">&nbsp;</p>
<p style="font-size:18pt;">ATENTAMENTE</p>
<p style="font-size:18pt;">LIC. MIGUEL ANGEL REBUELTA DIEZ</p>
<hr />
<p style="font-size:8pt; font-weight:bold;">Favor de no responder  a este correo electr&oacute;nico, este buz&oacute;n no se supervisa y no recibira respuesta.</p>
</body>
</html>