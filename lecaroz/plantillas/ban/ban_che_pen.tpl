<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Untitled Document</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="./styles/tablas.css" rel="stylesheet" type="text/css">
<link href="./styles/pages.css" rel="stylesheet" type="text/css">
<link href="./styles/impresion.css" rel="stylesheet" type="text/css">
</head>

<body>
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
<table class="print">
  <tr>
    <th class="print" scope="col">Compa&ntilde;&iacute;a</th>
  </tr>
  <tr>
    <th class="print"><font size="+1">{num_cia} - {nombre_cia}</font></th>
  </tr>
</table>
<br><table class="print">
  <tr>
    <th class="print" scope="col">Fecha</th>
    <th class="print" scope="col">Beneficiario</th>
    <th class="print" scope="col">Folio</th>
    <th class="print" scope="col">Importe</th>
    </tr>
  <!-- START BLOCK : fila -->
  <tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
    <td class="print">{fecha}</td>
    <td class="vprint">{beneficiario}</td>
    <td class="print">{folio}</td>
    <td class="rprint">{importe}</td>
    </tr>
	<!-- END BLOCK : fila -->
  <tr>
    <th colspan="3" class="rprint">Total</th>
    <th class="rprint_total">{total}</th>
    </tr>
</table>

  <p>
    <input type="button" class="boton" value="Cerrar" onClick="self.close()">
  </p></td>
</tr>
</table>
</body>
</html>
