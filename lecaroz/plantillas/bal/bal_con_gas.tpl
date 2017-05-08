<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Untitled Document</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="../../styles/tablas.css" rel="stylesheet" type="text/css" />
<link href="../../styles/pages.css" rel="stylesheet" type="text/css" />
</head>

<body>
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><table class="tabla">
  <tr>
    <th class="tabla" scope="col" style="font-size:12pt; ">{cod} {desc} </th>
  </tr>
</table>  <br />  <table class="tabla">
  <tr>
    <th class="tabla" scope="col">Fecha</th>
    <th class="tabla" scope="col">Concepto</th>
    <th class="tabla" scope="col">Folio</th>
    <th class="tabla" scope="col">Benficiario</th>
    <th class="tabla" scope="col">Importe</th>
  </tr>
  <!-- START BLOCK : fila -->
  <tr>
    <td class="tabla">{fecha}</td>
    <td class="vtabla">{concepto}</td>
    <td class="rtabla">{folio}</td>
    <td class="vtabla">{a_nombre}</td>
    <td class="rtabla">{importe}</td>
  </tr>
  <!-- END BLOCK : fila -->
  <tr>
    <th colspan="4" class="rtabla">Total</th>
    <th class="rtabla">{total}</th>
  </tr>
</table>

  <p>
    <input type="button" class="boton" value="Cerrar" onclick="self.close()" />
  </p></td>
</tr>
</table>
</body>
</html>
