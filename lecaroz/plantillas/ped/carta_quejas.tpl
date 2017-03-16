<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Documento sin t&iacute;tulo</title>
<link href="./styles/impresion.css" rel="stylesheet" type="text/css" />
<style type="text/css">
<!--
body,td,th {
	font-family: Arial, Helvetica, sans-serif;
}
body {
	margin-left: 0px;
	margin-top: 0px;
	margin-right: 0px;
	margin-bottom: 0px;
}
-->
</style></head>

<body>
<!-- START BLOCK : carta -->
<p align="center" style="font-size:16pt;font-weight:bold;">Oficinas Administrativas Mollendo<br />
<span style="font-size:6pt;">{clase}</span></p>
<p>&nbsp;</p>
<p align="right" style="font-weight:bold;">M&eacute;xico D.F., a {dia}  de {mes} de {anio} </p>
<p>&nbsp;</p>
<p>&nbsp;</p>
<p>A quien corresponda</p>
<p>&nbsp;</p>
<p>A continuaci&oacute;n se le presenta un informe completo de las quejas solicitadas:</p>
<table align="center" class="print">
  <tr>
    <th class="print" scope="col">Compa&ntilde;&iacute;a</th>
    <th class="print" scope="col">Fecha</th>
    <th class="print" scope="col">Quejoso</th>
    <th class="print" scope="col">Queja</th>
  </tr>
  <!-- START BLOCK : fila -->
  <tr>
    <td class="vprint">{num_cia} {nombre} </td>
    <td class="print">{fecha}</td>
    <td class="vprint">{quejoso}</td>
    <td class="vprint">{queja}</td>
  </tr>
  <!-- END BLOCK : fila -->
</table>
<p>&nbsp;</p>
<p align="center">Atentamente,</p>
<p>&nbsp;</p>
<p align="center" style="font-weight:bold;">________________________________________<br />
{admin}</p>
{salto}
<!-- END BLOCK : carta -->
</body>
</html>
