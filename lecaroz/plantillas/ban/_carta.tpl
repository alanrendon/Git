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

#folio {
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
	margin-top: 40mm;
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
	margin-top: 15mm;
	min-height: 70mm;
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
<!-- START BLOCK : hoja -->
<div id="hoja">
  <div id="folio">
    {folio}
  </div>
  <div id="titulo">
    Oficinas Administrativas Mollendo, S. de R.L. y C.V.
  </div>
    <div id="fecha">
	M&eacute;xico, D.F. a {dia} de {mes} de {anyo}
  </div>
  <div id="empresa">
    {empresa}
  </div>
  <div id="atencion">
    At'n: {atencion}
  </div>
  <div id="presente">
    Presente:
  </div>
  <div id="referencia">
    Ref: {referencia}
  </div>
  <p id="cuerpo">
    {cuerpo}
  </p>
  <p id="despedida">
    Sin m&aacute;s por el momento y agradeciendo de antemano tus finas atenciones, te reitero las seguridades de mi atenta y distinguida consideraci&oacute;n.</p>
  <div id="atentamente">
    Atentamente
  </div>
  <div id="remitente">
    JESUS M. ZUBIZARRETA CEBERIO</div>
  <div id="piedepagina">
    <table width="100%" align="center" style="border-collapse:collapse;">
      <tr>
        <td style="font-size:6pt;"><div align="center">LIC. CRISTIAN E. GONZALEZ R.</div></td>
        <td style="font-size:6pt;"><div align="center">RECIBE</div></td>
        <td style="font-size:6pt;">{fecha}</td>
      </tr>
    </table>
  </div>
</div>
<!-- END BLOCK : hoja -->
</body>
</html>
