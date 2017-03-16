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
<td align="center" valign="middle"><p class="title">Consulta de Vencimiento de Locales </p>
  <form action="./ren_loc_ven.php" method="get" name="form">
    <input name="tmp" type="hidden" id="tmp">
    <table class="tabla">
    <tr>
      <th class="vtabla" scope="row">Arrendador</th>
      <td class="vtabla"><input name="arr" type="text" class="insert" id="arr" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) local.select()" size="4"></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Local</th>
      <td class="vtabla"><input name="local" type="text" class="insert" id="local" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) arr.select()" size="4"></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Criterio</th>
      <td class="vtabla"><input name="criterio" type="radio" value="1" checked>
        Antig&uuml;edad<br>
        <input name="criterio" type="radio" value="2">
        Vencidas<br>
        <input name="criterio" type="radio" value="3" onClick="meses.select()">
        Contratos vencidos 
        (
        <input name="meses" type="text" class="insert" id="meses" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this.tmp)" onKeyDown="if (event.keyCode == 13) this.blur()" size="2">
        meses)</td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Tipo de Local </th>
      <td class="vtabla"><input name="tipo" type="radio" value="0" checked>
        Todos<br>
        <input name="tipo" type="radio" value="1">
        Propios<br>
        <input name="tipo" type="radio" value="2">
        Ajenos</td>
    </tr>
  </table><p>
    <input type="button" class="boton" value="Siguiente" onClick="validar()">
  </p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
<!--
var f = document.form;

function validar() {
	f.submit();
}

window.onload = f.arr.select();
//-->
</script>
<!-- END BLOCK : datos -->
<!-- START BLOCK : listado -->
<table width="100%">
  <tr>
    <td>&nbsp;</td>
    <td class="print_encabezado" align="center">Oficinas Administrativas Mollendo S. de R.L. y C.V. </td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td width="20%">&nbsp;</td>
    <td width="60%" class="print_encabezado" align="center">{titulo}</td>
    <td width="20%">&nbsp;</td>
  </tr>
</table>
<br>
<table align="center" class="print">
  <tr>
    <th class="print" scope="col">Local</th>
    <th class="print" scope="col">Inmobiliaria</th>
    <th class="print" scope="col">Renta</th>
    <th class="print" scope="col">Fecha de Vencimiento </th>
  </tr>
  <!-- START BLOCK : fila -->
  <tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
    <td class="vprint">{num} {nombre} </td>
    <td class="vprint"><!--{cod} -->{arr}</td>
    <td class="rprint">{renta}</td>
    <td class="print">{fecha_ven}</td>
  </tr>
  <!-- END BLOCK : fila -->
</table>
<!-- END BLOCK : listado -->
</body>
</html>
