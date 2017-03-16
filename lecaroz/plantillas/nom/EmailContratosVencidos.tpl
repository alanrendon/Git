<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title></title>
<style type="text/css">
body, td, th {
	font-family: Arial, Helvetica, sans-serif;
}
#title {
	font-size: 12pt;
	font-weight: bold;
}
#cia {
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
#lista {
	border-collapse: collapse;
	border: solid 1px #000;
}
#lista th, #lista td {
	border: solid 1px #000;
}
#lista th {
	background-color: #333;
	color: #FFF;
}
</style>
</head>

<!-- THE CAKE IS A LIE -->
<body>
<p id="title">CONTRATOS DE TRABAJADORES VENCIDOS</p>
<p id="cia">{num_cia} {nombre_cia}</p>
<p>Sr(ta). Encargado(a), le recordamos que es muy importante solicite a los siguientes trabajadores <strong style="color:red;">presentarse urgentemente</strong> en el &aacute;rea de <strong>Recursos Humanos</strong> ubicado en el <strong>Centro de Capacitaci&oacute;n Coyoac&aacute;n, Allende no. 5, Col. del Carmen, Coyoac&aacute;n Centro Hist&oacute;rico, D.F.,</strong> para <strong>renovaci&oacute;n de contrato</strong>:</p>
<table align="center" id="lista">
	<thead>
		<tr>
			<th scope="col">#</th>
			<th scope="col">Nombre del trabajador</th>
			<th scope="col">Vencimiento de<br />
			contrato</th>
		</tr>
	</thead>
	<tbody>
		<!-- START BLOCK : row -->
		<tr>
			<td align="right">{num_emp}</td>
			<td>{nombre}</td>
			<td align="center">{fecha}</td>
		</tr>
		<!-- END BLOCK : row -->
	</tbody>
</table>
<hr />
<p id="footer">Favor de no responder a este correo electr&oacute;nico ya que es generado autom&aacute;ticamente por el sistema y no recibir&aacute; respuesta. Para mayor informaci&oacute;n por favor comunicarse al Centro de Capacitaci&oacute;n Coyoac&aacute;n a los teléfonos 5658 5207 o 5276 6580.</p>
</body>
<!-- THE CAKE IS A LIE -->
</html>
