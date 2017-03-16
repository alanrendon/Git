<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Carta</title>

<style>
body {
	margin-left: 0mm;
	margin-top: 0mm;
	margin-right: 0mm;
	margin-bottom: 0mm;
}

.hoja_carta {
	width: 160mm;
	height: 250mm;
	margin-top: 10mm;
	margin-left: 25mm;
	font: 11pt Arial, Helvetica, sans-serif;
	overflow: hidden;
}

.titulo {
	font-variant: small-caps;
	font-size: 14pt;
	text-align: center;
}

.fecha {
	margin-top: 15mm;
	font-weight: bold;
	text-align: right;
}

.contacto {
	margin-top: 15mm;
	font-weight: bold;
}

.cuerpo {
	margin-top: 10mm;
	text-align: justify;
}

.firma {
	margin-top: 15mm;
	text-align: center;
	font-weight: bold;
}
</style>
</head>

<body>
<!-- START BLOCK : carta -->
<div class="hoja_carta">
  <div class="titulo">
    {_inmobiliaria}
  </div>
  <div class="fecha">
    M&eacute;xico, D.F. a {dia} de {mes} de {anio}
  </div>
  <div class="contacto">
    {arrendatario}<br />
    Presente
  </div>
  <div class="cuerpo">
    <p>Derivado del contrato de arrendamiento suscrito entre <strong>{arrendatario} EN SU CAR&Aacute;CTER DE ARRENDATARIO Y POR LA OTRA PARTE {inmobiliaria} COMO ARRENDADOR</strong>, con fecha <strong>{fecha_inicio}</strong> por el inmueble ubicado en <strong>{direccion}</strong>.</p>
    <p>Y derivado de la <strong>CLAUSULA {clausula} PARRAFO {parrafo}</strong> del contrato, referente al incremento anual por aniversario, se procede aplicar lo ah&iacute; dispuesto de acuerdo a la variaci&oacute;n resultante del <strong>INDICE NACIONAL DE PRECIOS AL CONSUMIDOR (INPC)</strong> por el periodo a continuaci&oacute;n descrito:</p>
    <p><strong>PERIODO DE CALCULO {fecha_inicio_ant} AL {fecha_termino_ant} = {por_incremento}%</strong></p>
    <table width="80%" style="border-collapse:collapse;">
      <tr>
        <td><strong>RENTA ANTERIOR</strong></td>
        <td align="right"><strong>{renta_anterior}</strong></td>
      </tr>
      <tr>
        <td><strong>PORCENTAJE DE INCREMENTO</strong></td>
        <td align="right"><strong>{por_incremento}</strong></td>
      </tr>
      <tr style="border-bottom:solid 1px #000;">
        <td><strong>IMPORTE DE INCREMENTO</strong></td>
        <td align="right"><strong>{importe_incremento}</strong></td>
      </tr>
      <tr>
        <td><strong>NUEVA RENTA</strong></td>
        <td align="right"><strong>{nueva_renta}</strong></td>
      </tr>
    </table>
    <p><strong>VIGENCIA NUEVA RENTA DEL {nueva_fecha_inicio} AL {nueva_fecha_termino}</strong></p>
    <p>Suscrito por las partes de conformidad el presente documento formara parte integrante del contrato de referencia.</p>
  </div>
  <div class="firma">
    <table width="100%">

      <tr>
        <td width="50%" align="center" valign="top"><p>Atentamente</p>
        <p>&nbsp;</p>
        <p>__________________________<br />
        {inmobiliaria}</p></td>
        <td width="50%" align="center" valign="top"><p>De conformidad</p>
        <p>&nbsp;</p>
        <p>__________________________<br />
        {arrendatario}</p>
        </td>
      </tr>
    </table>
  </div>
</div>
<!-- END BLOCK : carta -->
</body>
</html>
