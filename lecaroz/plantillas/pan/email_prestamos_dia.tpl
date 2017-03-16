<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Prestamos</title>
<style type="text/css">
body, td, th {
	font-family: Arial, Helvetica, sans-serif;
}
#title {
	font-size: 12pt;
	font-weight: bold;
}
#nota {
	color: #C00;
	font-weight: bold;
}
#footer {
	font-size: 8pt;
	font-weight: bold;
}
</style>
</head>

<!-- THE CAKE IS A LIE -->
<body>
<p id="title">PRESTAMOS CON FECHA {fecha}</p>
<p><strong>{num_cia} {nombre_cia}</strong></p>
<p>Se han hecho los siguientes movimientos de prestamos a empleados:</p>
<table border="1" align="center">
	<tr>
		<th scope="col">Empleado</th>
		<th scope="col">Saldo</th>
		<th scope="col">Tipo</th>
		<th scope="col">Importe</th>
		<th scope="col">Resta</th>
	</tr>
	<!-- START BLOCK : row -->
	<tr>
		<td>{num_emp} {nombre_emp}</td>
		<td align="right">{saldo}</td>
		<td>{tipo}</td>
		<td align="right">{importe}</td>
		<td align="right">{resta}</td>
	</tr>
	<!-- END BLOCK : row -->
</table>
</body>
<!-- THE CAKE IS A LIE -->
</html>
