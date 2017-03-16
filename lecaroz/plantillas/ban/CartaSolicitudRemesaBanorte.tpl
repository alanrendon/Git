<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Carta</title>

<style type="text/css">
<!--
body {
	margin-left: 0mm;
	margin-top: 0mm;
	margin-right: 0mm;
	margin-bottom: 0mm;
}

#hoja {
	width: 170mm;
	min-height: 250mm;
	margin-top: 10mm;
	margin-left: 20mm;
	font-family: Arial, Helvetica, sans-serif;
	font-size: 12pt;
}

#num_cia {
	font-size: 8pt;
	float: right;
}

#titulo {
	font-variant: small-caps;
	font-size: 14pt;
	text-align: center;
}

#fecha {
	text-align: right;
	margin: 20mm 0;
}

#empresa {
	font-weight: bold;
}

#atencion {
	font-weight: bold;
	margin-top: 20mm;
}

#presente {
	font-weight: bold;
}

#referencia {
	margin-top: 5mm;
	text-align: right;
	font-size: 10pt;
}

#cuerpo {
	margin-top: 15mm;
	text-align: justify;
}

#despedida {
	margin-top: 5mm;
	text-align: justify;
}

#atentamente {
	margin-top: 15mm;
	text-align: center;
}

#remitente {
	margin-top: 20mm;
	text-align: center;
	font-weight: bold;
}

#piedepagina {
	margin-top: 5mm;
}
-->
</style>

</head>

<body>
<!-- START BLOCK : carta -->
<div id="hoja">
    <div id="fecha">
	M&eacute;xico, D.F. a {dia} de {mes} de {anio}
  </div>
    <div id="titulo">
    Carta Solicitud de Remesa
  </div>
  <div id="atencion">
    At'n: Moises</div>
  <div id="presente">
    Gerente Caja General
  </div>
  <div id="empresa">Banco Mercantil del Norte, S.A.</div>
  <p id="cuerpo">
  Por este medio autorizamos a Banco Mercantil del Norte, S.A. a realizar el cargos a las siguientes cuentas:</p>
	<table border="1" align="center" style="border-collapse:collapse;border:1px solid Black;">
		<tr>
			<th scope="col">Cuenta</th>
			<th scope="col">Compañía</th>
			<th scope="col">Referencia <br />
			Interna</th>
			<th scope="col">Importe</th>
		</tr>
		<!-- START BLOCK : cuenta -->
		<tr>
			<td>{cuenta}</td>
			<td>{nombre_cia}</td>
			<td align="center">{folio}</td>
			<td>{importe}</td>
		</tr>
		<!-- END BLOCK : cuenta -->
		<tr>
			<td colspan="3" align="right">Total</td>
			<td>{total}</td>
		</tr>
	</table>
  <p>&nbsp; </p>
  <p>En las denominaciones de $1,000.00 y $500.00, así mismo solicito que este efectivo sea entregado al Sr. Miguel Ángel Rebuelta Diez en el domicilio ubicado en Callejón de Cuitlahuac No. 160, Col. Lorenzo Boturini, Delegación V. Carranza para lo cual deberá mostrar una identificación vigente al momento de la entrega.  </p>
  <p>Sin más por el momento y agradeciendo sus atenciones, quedamos como siempre. </p>
  <div id="atentamente">
    Atentamente
  </div>
  <div id="remitente">
    {firma}
  </div>
</div>
{salto}
<!-- END BLOCK : carta -->
</body>
</html>
