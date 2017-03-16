<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Documento sin t&iacute;tulo</title>
<link href="./styles/tablas.css" rel="stylesheet" type="text/css" />
<link href="./styles/pages.css" rel="stylesheet" type="text/css" />
</head>

<body>
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Fichas de Dep&oacute;sito Pendientes</p>
<table align="center" class="tabla">
  <!-- START BLOCK : cia -->
  <tr>
    <th colspan="4" class="tabla" scope="col" style="font-size:12pt;">{num_cia} {nombre} </th>
  </tr>
  <tr>
    <th class="tabla">Fecha</th>
    <th class="tabla">Acreditado</th>
    <th class="tabla">Nombre</th>
    <th class="tabla">Importe</th>
  </tr>
  <!-- START BLOCK : fila -->
  <tr>
    <td class="tabla">{fecha}</td>
    <td class="vtabla">{acre}</td>
    <td class="vtabla">{nombre}</td>
    <td class="rtabla">{importe}</td>
  </tr>
  <!-- END BLOCK : fila -->
  <tr>
    <th colspan="3" class="rtabla">Total</th>
    <th class="rtabla">{total}</th>
  </tr>
  <tr>
    <td colspan="4" class="tabla">&nbsp;</td>
  </tr>
  <!-- END BLOCK : cia -->
  <tr>
    <th colspan="3" class="rtabla">Gran Total </th>
    <th class="rtabla">{gran_total}</th>
  </tr>
</table>
  <p>
    <input type="button" class="boton" value="Cerrar" onclick="self.close()" />
</p></td>
</tr>
</table>
</body>
</html>
