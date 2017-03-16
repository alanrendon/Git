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

#titulo {
	font-variant: small-caps;
	font-size: 14pt;
	text-align: center;
}

#fecha {
	text-align: right;
	margin-top: 20mm;
}

#empresa {
	margin-top: 20mm;
	text-transform: uppercase;
	font-weight: bold;
}

#atencion {
	text-transform: uppercase;
}

#presente {
}

#referencia {
	margin-top: 5mm;
	text-align: right;
	font-size: 10pt;
}

#cuerpo {
	margin-top: 10mm;
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
	text-transform: uppercase;
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
  <div id="titulo">
    Oficinas Administrativas Mollendo, S. de R.L. y C.V.
  </div>
    <div id="fecha">
	M&eacute;xico, D.F. a {dia} de {mes} de {anyo}
  </div>
  <div id="empresa">{admin}</div>
  <p id="cuerpo">Por medio de la presente le entrego los certificados de verificación de las unidades que les toca la <strong>{num}ª revisión</strong>. Tambien solicito que los certificados anteriores sean entregados lo antes posible para corroborar que la verificación ya haya sido realizada y poder reclamar con anticipación el vencimiento de la misma.</p>
  <p style="text-align:justify;">A su vez pedimos su apoyo para que la persona que lleve la unidad a verificar <strong>pida factura</strong> del trámite realizado a nombre de la panadería.</p>
  <p style="text-align:justify;">Le solicitamos también nos informe de los vehículos que no tienen placas o tarjeta de circulación y que los números de la tarjeta y la razón social son los que corresponden a esa unidad.</p>
  <table width="98%" align="center">
    <tr>
      <th colspan="5" align="left" scope="col">{num}ª Revisión </th>
    </tr>
	<!-- START BLOCK : revision -->
	<tr>
      <td>{num_camioneta}</td>
      <td>{nombre_cia}</td>
      <td>{placas}</td>
      <td>{modelo}</td>
      <td>{recibo}</td>
    </tr>
	<!-- END BLOCK : revision -->
  </table>
  <br />
  <table width="98%" align="center">
    <tr>
      <th colspan="5" align="left" scope="col">Recibos Pendientes </th>
    </tr>
	<!-- START BLOCK : pendiente -->
    <tr>
      <td>{num_camioneta}</td>
      <td>{nombre_cia}</td>
      <td>{placas}</td>
      <td>{modelo}</td>
      <td>{recibo}</td>
    </tr>
	<!-- END BLOCK : pendiente -->
  </table>
  <p style="text-align:justify;">Le recordamos que los vehículos que tienen la leyenda <strong>N/RECIBO</strong>  no tienen el certificado y por consiguiente no sabemos el estatus de verificación del mismo. <strong>Esto no es inconveniente para verificar la unidad en tiempo</strong>, solo debe solicitar una copia del semestre anterior en el verificentro donde se realizo el trámite. </p>
  <div id="atentamente">
    Atentamente
  </div>
  <div id="remitente">
    JESUS M. ZUBIZARRETA CEBERIO
  </div>
</div>
{salto}
<!-- END BLOCK : carta -->
</body>
</html>
