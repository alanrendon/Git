<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Untitled Document</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../../styles/tablas.css" rel="stylesheet" type="text/css">
<link href="../../styles/pages.css" rel="stylesheet" type="text/css">
<link href="../../styles/impresion.css" rel="stylesheet" type="text/css">
</head>

<body>
<!-- START BLOCK : datos -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Pagos no Conciliados</p>
  <form action="./ban_pag_nco.php" method="get" name="form">
    <input name="tmp" type="hidden" id="tmp">
    <table class="tabla">
    <tr>
      <th class="vtabla" scope="row">Compa&ntilde;&iacute;a</th>
      <td class="vtabla"><input name="num_cia" type="text" class="insert" id="num_cia" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) num_pro.select()" value="{num_cia}" size="3"></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Proveedor</th>
      <td class="vtabla"><input name="num_pro" type="text" class="insert" id="num_pro" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) fecha.select()" value="{num_pro}" size="3"></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Fecha de Corte </th>
      <td class="vtabla"><input name="fecha" type="text" class="insert" id="fecha" onFocus="tmp.value=this.value;this.select()" onChange="inputDateFormat(this)" onKeyDown="if (event.keyCode == 13) num_cia.select()" value="{fecha}" size="10" maxlength="10"></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Buscar</th>
      <td class="vtabla"><input name="buscar" type="radio" value="0" checked>
        Buscar<br>
        <input name="buscar" type="radio" value="5">
        Cheques<br>
        <input name="buscar" type="radio" value="41">
        Transferencias</td>
    </tr>
  </table>  <p>
    <input type="button" class="boton" value="Siguiente">
  </p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
<!--
var f = document.form;

window.onload = function () { f.num_cia.select(); showAlert = true; };
//-->
</script>
<!-- END BLOCK : datos -->
<!-- START BLOCK : listado -->

<!-- END BLOCK : listado -->
</body>
</html>
