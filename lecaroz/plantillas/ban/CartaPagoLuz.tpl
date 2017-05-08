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
  <div id="num_cia">
    {num_cia}
  </div>
  <div id="titulo">
    {nombre_cia}</div>
    <div id="fecha">
	M&eacute;xico, D.F. a {dia} de {mes} de {anio}
  </div>
  <div id="empresa">Comisión Federal de Electricidad </div>
  <p id="cuerpo">
  Por medio de la presente me permito saludarle y a la vez solicitarle la <strong>factura fiscal</strong> del contrato no. <strong>{num_servicio}</strong> del mes de <strong>{mes_rec}</strong> de <strong>{anio_rec}</strong>. Este mes fue pagado con el cheque <strong>{folio}</strong> de <strong>{banco}</strong> con un importe de <strong>{importe}</strong>  y cobrado el día <strong>{fecha}</strong>. Agredecido de antemano y esperando su pronta respuesta.</p>
  <div id="atentamente">
    Atentamente
  </div>
  <div id="remitente">{nombre_cia}</div>
</div>
{salto}
<!-- END BLOCK : carta -->
</body>
</html>
