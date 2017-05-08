<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Untitled Document</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="file:///C|/Documents%20and%20Settings/John%20Talbain/Escritorio/Lecaroz/styles/tablas.css" rel="stylesheet" type="text/css">
<link href="file:///C|/Documents%20and%20Settings/John%20Talbain/Escritorio/Lecaroz/styles/impresion.css" rel="stylesheet" type="text/css">
<link href="file:///C|/Documents%20and%20Settings/John%20Talbain/Escritorio/Lecaroz/styles/pages.css" rel="stylesheet" type="text/css">
</head>

<body>
<!-- START BLOCK : datos -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Entradas de Producto por Proveedor </p>
  <form action="./fac_pro_pro.php" method="get" name="form">
  <input name="temp" type="hidden">
  <table class="tabla">
    <tr>
      <th class="vtabla">Producto</th>
      <td class="vtabla"><input name="codmp" type="text" class="insert" id="codmp" onFocus="temp.value=this.value" onChange="isInt(this,temp)" onKeyDown="if (event.keyCode == 13) fecha1.select();" size="4" maxlength="4"></td>
    </tr>
    <tr>
    <tr>
      <th class="vtabla">Fecha</th>
      <td class="vtabla"><input name="fecha1" type="text" class="insert" id="fecha1" onChange="actualiza_fecha(this)" onKeyDown="if (event.keyCode == 13) fecha2.select()" size="10" maxlength="10">
        al
          <input name="fecha2" type="text" class="insert" id="fecha2" onChange="actualiza_fecha(this)" onKeyDown="if (event.keyCode == 13) anio.select();" size="10" maxlength="10"></td>
    </tr>
	<th class="vtabla">Mes</th>
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
      <th class="vtabla">A&ntilde;o</th>
      <td class="vtabla"><input name="anio" type="text" class="insert" id="anio" onFocus="temp.value=this.value" onChange="isInt(this,temp)" onKeyDown="if (event.keyCode == 13) codmp.select();" value="{anio}" size="4" maxlength="4"></td>
    </tr>
  </table>  
  <p>
    <input type="button" class="boton" value="Siguiente" onClick="valida_registro(form)">
  </p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
	function valida_registro(form) {
		if (form.codmp.value <= 0) {
			alert("Debe especificar el código de materia prima");
			form.codmp.select();
			return false;
		}
		else if (form.anio.value < 2000) {
			alert("Debe especificar el año");
			form.anio.select();
			return false;
		}
		else
			form.submit();
	}
	
	window.onload = document.form.codmp.select();
</script>
<!-- END BLOCK : datos -->

<!-- START BLOCK : listado -->
<table width="100%" align="center">
  <tr>
    <td width="20%">&nbsp;</td>
    <td width="60%" class="print_encabezado" align="center">Entradas Mensuales de {nombre_mp} por Proveedor<br>{intervalo}</td>
    <td width="20%">&nbsp;</td>
  </tr>
</table>
  <br>
  <table align="center" cellpadding="0" cellspacing="0" class="print">
    <tr>
      <th colspan="2" class="print" scope="col">Compa&ntilde;&iacute;a</th>
      <th class="print" scope="col">Total</th>
      <!-- START BLOCK : proveedor -->
	  <th class="print" scope="col">{proveedor}</th>
	  <!-- END BLOCK : proveedor -->
    </tr>
    <!-- START BLOCK : fila -->
	<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
      <td class="vprint">{num_cia}</td>
      <td class="vprint">{nombre_cia}</td>
      <td class="rprint">{total}</td>
      <!-- START BLOCK : entrada -->
	  <td class="rprint">{entrada}</td>
	  <!-- END BLOCK : entrada -->
    </tr>
	<!-- END BLOCK : fila -->
  <tr>
  	<th colspan="2" class="rprint">Total</th>
	<th class="rprint">{total}</th>
	<!-- START BLOCK : total_pro -->
	<th class="rprint">{total}</th>
	<!-- END BLOCK : total_pro -->
	</tr>
</table>
<!--<br style="page-break-after:always;">-->

  <!-- END BLOCK : listado -->
</body>
</html>
