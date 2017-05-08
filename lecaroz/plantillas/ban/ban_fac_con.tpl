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
<td align="center" valign="middle"><p class="title">Consulta de Facturas de Pan </p>
  <form action="./ban_fac_con.php" method="get" name="form">
  <input name="temp" type="hidden">
  <table class="tabla">
    <tr>
      <th class="vtabla" scope="row">Compa&ntilde;&iacute;a</th>
      <td class="vtabla"><input name="num_cia" type="text" class="insert" id="num_cia" onFocus="temp.value=this.value" onChange="isInt(this,temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 40 || event.keyCode == 38) anio.select();" size="3" maxlength="3"></td>
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
      <td class="vtabla"><input name="anio" type="text" class="insert" id="anio" onFocus="temp.value=this.value" onChange="isInt(this,temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 40 || event.keyCode == 38) num_cia.select();" value="{anio}" size="4" maxlength="4"></td>
    </tr>
  </table>  <p>
    <input type="button" class="boton" value="Siguiente" onClick="valida_registro(form)">
  </p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
	function valida_registro(form) {
		if (form.anio.value <= 0) {
			alert("Debe especificar el año");
			form.anio.select();
			return false;
		}
		else
			form.submit();
	}
	
	window.onload = document.form.num_cia.select();
</script>
<!-- END BLOCK : datos -->
<!-- START BLOCK : cia -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Consulta de Facturas de Pan</p>
  <table class="tabla">
  <tr>
    <th class="tabla" scope="col">Compa&ntilde;&iacute;a</th>
    <th class="tabla" scope="col">Mes</th>
    <th class="tabla" scope="col">A&ntilde;o</th>
  </tr>
  <tr>
    <td class="tabla">{num_cia} {nombre_cia} </td>
    <td class="tabla">{mes}</td>
    <td class="tabla">{anio}</td>
  </tr>
</table>

  <br>  <table class="tabla">
    <tr>
      <th class="tabla" scope="col">D&iacute;a</th>
      <th class="tabla" scope="col">Ventas</th>
      <th class="tabla" scope="col">Facturadas</th>
      <th class="tabla" scope="col">Pendientes</th>
      </tr>
    <!-- START BLOCK : dia -->
	<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
      <td class="tabla">{dia}</td>
      <td class="rtabla">{ventas}</td>
      <td class="rtabla">{facturas}</td>
      <td class="rtabla">{pendientes}</td>
      </tr>
	  <!-- END BLOCK : dia -->
    <tr>
      <th class="tabla">Total</th>
      <th class="rtabla">{ventas}</th>
      <th class="rtabla">{facturas}</th>
      <th class="rtabla">{pendientes}</th>
      </tr>
  </table>  <p>
    <input type="button" class="boton" value="Regresar" onClick="document.location='./ban_fac_con.php'">
  </p></td>
</tr>
</table>
<!-- END BLOCK : cia -->
<!-- START BLOCK : all -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Consulta de Facturas de Pan</p>
  <table class="tabla">
    <tr>
      <th class="tabla" scope="col">Mes</th>
      <th class="tabla" scope="col">A&ntilde;o</th>
    </tr>
    <tr>
      <td class="tabla">{mes}</td>
      <td class="tabla">{anio}</td>
    </tr>
  </table>  
  <br>
  <table class="tabla">
    <tr>
      <th colspan="2" class="tabla" scope="col">Compa&ntilde;&iacute;a</th>
      <th class="tabla" scope="col">Ventas</th>
      <th class="tabla" scope="col">Facturadas</th>
      <th class="tabla" scope="col">Pendientes</th>
    </tr>
    <!-- START BLOCK : fila -->
	<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
      <td class="tabla">{num_cia}</td>
      <td class="vtabla">{nombre_cia}</td>
      <td class="rtabla">{ventas}</td>
      <td class="rtabla">{facturas}</td>
      <td class="rtabla">{pendientes}</td>
    </tr>
	<!-- END BLOCK : fila -->
  </table>  <p>
    <input type="button" class="boton" value="Regresar" onClick="document.location='./ban_fac_con.php'"> 
    </p></td>
</tr>
</table>
<!-- END BLOCK : all -->
</body>
</html>
