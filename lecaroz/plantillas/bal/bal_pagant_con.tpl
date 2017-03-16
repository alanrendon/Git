<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Untitled Document</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../../styles/pages.css" rel="stylesheet" type="text/css">
<link href="../../styles/tablas.css" rel="stylesheet" type="text/css">
</head>

<body>
<!-- START BLOCK : datos -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Consulta de Pagos Anticipados</p>
  <form action="./bal_pagant_con.php" method="get" name="form" onKeyDown="if (event.keyCode == 13) return false">
  <input name="temp" type="hidden">
  <table class="tabla">
    <tr>
      <th class="vtabla" scope="row">Compa&ntilde;&iacute;a</th>
      <td class="vtabla"><input name="num_cia" type="text" class="insert" id="num_cia" onFocus="temp.value=this.value" onChange="isInt(this,temp)" size="3" maxlength="3"></td>
    </tr>
  </table>  <p>
    <input type="button" class="boton" value="Siguiente" onClick="this.form.submit()">
  </p></form></td>
</tr>
</table>
<!-- END BLOCK : datos -->
<!-- START BLOCK : listado -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Consulta de Pagos Anticipados</p>
  <table class="tabla">
    <!-- START BLOCK : cia -->
	<tr>
      <th colspan="6" class="tabla" scope="col">{num_cia} - {nombre_cia} </th>
      </tr>
    <tr>
      <th class="tabla" scope="col">Inicio</th>
      <th class="tabla" scope="col">Termino</th>
      <th class="tabla" scope="col">Concepto</th>
      <th class="tabla" scope="col">Importe</th>
      <th class="tabla" scope="col">Resta por anticipar</th>
      <th class="tabla" scope="col">Meses<br>restantes</th>
    </tr>
    <!-- START BLOCK : fila -->
	<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
      <td class="tabla" style="font-weight:{activo};">{fecha_ini}</td>
      <td class="tabla" style="font-weight:{activo};">{fecha_fin}</td>
      <td class="vtabla" style="font-weight:{activo};">{concepto}</td>
      <td class="rtabla" style="font-weight:{activo};">{importe}</td>
      <td class="rtabla" style="font-weight:{activo};">{acumulado}</td>
      <td class="rtabla" style="font-weight:{activo};">{meses_restantes}</td>
    </tr>
	<!-- END BLOCK : fila -->
    <!-- START BLOCK : total -->
	<tr>
      <th colspan="3" class="rtabla">&nbsp;</th>
      <th class="rtabla">{total}</th>
      <th colspan="2" class="rtabla">&nbsp;</th>
    </tr>
	<!-- END BLOCK : total -->
    <tr>
      <td colspan="6">&nbsp;</td>
      </tr>
	  <!-- END BLOCK : cia -->
  </table>  <p>
    <input type="button" class="boton" value="Regresar" onClick="document.location='bal_pagant_altas.php'">
  </p></td>
</tr>
</table>
<!-- END BLOCK : listado -->
</body>
</html>
