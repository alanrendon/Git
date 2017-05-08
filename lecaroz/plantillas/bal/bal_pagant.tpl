<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Untitled Document</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../../styles/tablas.css" rel="stylesheet" type="text/css">
<link href="../../styles/pages.css" rel="stylesheet" type="text/css">
</head>

<body>
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Alta de Pagos Anticipados</p>
  <form action="./bal_pagant_altas.php" method="post" name="form"><table class="tabla">
    <tr>
      <th class="vtabla" scope="col">&nbsp;</th>
      <th colspan="2" class="tabla" scope="col">Inicia</th>
      <th colspan="2" class="tabla" scope="col">Termina</th>
	  <th class="tabla" scope="col">&nbsp;</th>
      </tr>
    <tr>
      <th class="tabla" scope="col">Compa&ntilde;&iacute;a</th>
      <th class="tabla" scope="col">Mes</th>
      <th class="tabla" scope="col">A&ntilde;o</th>
      <th class="tabla" scope="col">Mes</th>
      <th class="tabla" scope="col">A&ntilde;o</th>
      <th class="tabla" scope="col">Concepto</th>
    </tr>
    <tr>
      <td class="tabla"><input name="num_cia[]" type="text" class="insert" id="num_cia[]" size="3" maxlength="3">
        <input name="nombre_cia[]" type="text" class="vnombre" id="nombre_cia[]" size="20"></td>
      <td class="tabla"><input name="mes1[]" type="text" class="insert" id="mes1[]" value="{mes}" size="2" maxlength="2"></td>
      <td class="tabla"><input name="anio1[]" type="text" class="insert" id="anio1[]" value="{anio}" size="4" maxlength="4"></td>
      <td class="tabla"><input name="mes2[]" type="text" class="insert" id="mes2[]" value="{mes}" size="2" maxlength="2"></td>
      <td class="tabla"><input name="anio2[]" type="text" class="insert" id="anio2[]" value="{anio}" size="4" maxlength="4"></td>
      <td class="tabla"><input name="concepto[]" type="text" class="vinsert" id="concepto[]" size="30" maxlength="100"></td>
    </tr>
  </table>  <p>
    <input type="button" value="Siguiente"> 
    </p></form></td>
</tr>
</table>
</body>
</html>
