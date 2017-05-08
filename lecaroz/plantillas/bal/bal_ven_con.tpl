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
<td align="center" valign="middle"><p class="title">Ventas Globales</p>
  <form action="./bal_ven_con.php" method="get" name="form">
    <input name="temp" type="hidden" id="temp">
    <table class="tabla">
    <tr>
      <th class="vtabla" scope="row">Compa&ntilde;&iacute;a</th>
      <td class="vtabla"><input name="num_cia" type="text" class="insert" id="num_cia" onFocus="temp.value=this.value" onChange="isInt(this,temp)" onKeyDown="if (event.keyCode == 13) anio.select()" size="3" maxlength="3"></td>
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
  </table>  <p>
    <input type="button" class="boton" value="Siguiente" onClick="validar(this.form)">
  </p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
<!--
function validar(form) {
	if (form.anio.value < 2004) {
		alert("Debe especificar el año");
		form.anio.select();
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
    <td>&nbsp;</td>
    <td class="print_encabezado" align="center">Oficinas Administrativas Mollendo S. de R.L. y C.V. </td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td width="20%">&nbsp;</td>
    <td width="60%" class="print_encabezado" align="center">Ventas Globales al Mes de {mes} del {anio} </td>
    <td width="20%">&nbsp;</td>
  </tr>
</table>
<br>
<table align="center" class="print">
  <tr>
    <th colspan="2" class="print" scope="col">Compa&ntilde;&iacute;a</th>
    <th class="print" scope="col">&nbsp;</th>
    <!-- START BLOCK : title_mes -->
	<th class="print" scope="col">{mes}</th>
	<!-- END BLOCK : title_mes -->
  </tr>
  <!-- START BLOCK : cia -->
  <tr>
    <td colspan="2" rowspan="5" nowrap class="vprint"><strong>{num_cia} {nombre_cia}</strong></td>
    <td class="vprint"><strong>Venta</strong></td>
    <!-- START BLOCK : venta -->
	<td class="rprint"><strong>{venta}</strong></td>
	<!-- END BLOCK : venta -->
  </tr>
  <tr>
    <td class="vprint"><strong>Pastel</strong></td>
    <!-- START BLOCK : pastel -->
	<td class="rprint">{pastel}</td>
	<!-- END BLOCK : pastel -->
  </tr>
  <tr>
    <td nowrap class="vprint"><strong>V. Puerta </strong></td>
    <!-- START BLOCK : vpuerta -->
	<td class="rprint">{vpuerta}</td>
	<!-- END BLOCK : vpuerta -->
  </tr>
  <tr>
    <td class="vprint"><strong>Kilos Entregados</strong></td>
    <!-- START BLOCK : kilos_entregados -->
	<td class="rprint">{kilos}</td>
	<!-- END BLOCK : kilos_entregados -->
  </tr>
  <tr>
    <td class="vprint"><strong>Kilos Pedidos</strong></td>
    <!-- START BLOCK : kilos_pedidos -->
	<td class="rprint">{kilos}</td>
	<!-- END BLOCK : kilos_pedidos -->
  </tr>
  <!-- END BLOCK : cia -->
</table>
<!-- START BLOCK : salto -->
<br style="page-break-after:always;">
<!-- END BLOCK : salto -->
<!-- END BLOCK : listado -->
</body>
</html>
