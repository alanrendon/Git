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
<td align="center" valign="middle"><p class="title">Existencias en Materia Prima</p>
  <form action="./bal_exc_fis.php" method="get" name="form">
    <input name="temp" type="hidden" id="temp">
    <table class="tabla">
    <tr>
      <th class="vtabla" scope="row">Compa&ntilde;&iacute;a</th>
      <td class="vtabla"><input name="num_cia" type="text" class="insert" id="num_cia" onFocus="temp.value=this.value" onChange="isInt(this,temp)" onKeyDown="if (event.keyCode == 13) codmp.select()" size="3" maxlength="3"></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">C&oacute;digo de Producto </th>
      <td class="vtabla"><input name="codmp" type="text" class="insert" id="codmp" onFocus="temp.value=this.value" onChange="isInt(this,temp)" onKeyDown="if (event.keyCode == 13) anio.select()" size="3" maxlength="4"></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Mes</th>
      <td class="vtabla"><select name="mes" class="insert" id="mes">
        <option value="1" {1}>ENERO</option>
        <option value="2" {2}>FEBRERO</option>
        <option value="3" {3}>MARZO</option>
        <option value="4" {4}>ABRIL</option>
        <option value="5" {5}>MAYO</option>
        <option value="6" {6}>JUNIO</option>
        <option value="7" {7}>JULIO</option>
        <option value="8" {8}>AGOSTO</option>
        <option value="9" {9}>SEPTIEMBRE</option>
        <option value="10" {10}>OCTUBRE</option>
        <option value="11" {11}>NOVIEMBRE</option>
        <option value="12" {12}>DICIEMBRE</option>
      </select></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">A&ntilde;o</th>
      <td class="vtabla"><input name="anio" type="text" class="insert" id="anio" onFocus="temp.value=this.value" onChange="isInt(this,temp)" onKeyDown="if (event.keyCode == 13) num_cia.select()" value="{anio}" size="4" maxlength="4"></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Tipo</th>
      <td class="vtabla"><input name="tipo" type="radio" value="listado" checked>
        Listado
          <input name="tipo" type="radio" value="archivo">
          Archivo</td>
    </tr>
  </table>  <p>
    <input type="button" class="boton" onClick="validar(this.form)" value="Siguiente">
    </p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
<!--
function validar(form) {
	if (form.anio.value <= 0) {
		alert("Debe especificar el año");
		form.anio.select();
		return false;
	}
	else if (form.num_cia.value > 300) {
		alert("Solo se permiten panaderias");
		form.num_cia.select();
		return false;
	}
	else {
		form.submit();
	}
}

window.onload = document.form.num_cia.select();
-->
</script>
<!-- END BLOCK : datos -->
<!-- START BLOCK : listado -->
<table width="100%">
  <tr>
    <td class="print_encabezado">Cia.: {num_cia}</td>
    <td class="print_encabezado" align="center">{nombre_cia}</td>
    <td class="rprint_encabezado">Cia.: {num_cia} </td>
  </tr>
  <tr>
    <td width="20%">&nbsp;</td>
    <td width="60%" class="print_encabezado" align="center">Existencias F&iacute;sicas en Materia Prima <br>
    correspondientes al mes de {mes} de {anio} </td>
    <td width="20%">&nbsp;</td>
  </tr>
</table>
<br>
<table width="50%" align="center" class="print">
  <tr>
    <th colspan="2" class="print" scope="col">Producto</th>
    <th class="print" scope="col">Costo Unitario </th>
    <th class="print" scope="col">Existencia F&iacute;sica</th>
    <th class="print" scope="col">Costo Total </th>
  </tr>
  <!-- START BLOCK : fila -->
  <tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
    <td width="5%" class="rprint">{codmp}</td>
    <td width="35%" class="vprint">{nombre}</td>
    <td width="20%" class="rprint">{costo}</td>
    <td width="20%" class="rprint">{existencia}</td>
    <td width="20%" class="rprint">{total}</td>
  </tr>
  <!-- END BLOCK : fila -->
  <!-- START BLOCK : total -->
  <tr>
    <th colspan="4" class="rprint">Costo Inventario </th>
    <th class="rprint_total">{total}</th>
  </tr>
  <!-- END BLOCK : total -->
</table>
{salto}
<!-- END BLOCK : listado -->
</body>
</html>
