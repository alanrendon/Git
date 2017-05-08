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
<!-- START BLOCK : datos -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Captura de Dep&oacute;sitos </p>
  <form action="./ban_dep_alt.php" method="get" name="form">
  <input name="temp" type="hidden">
  <table class="tabla">
    <tr>
      <th class="vtabla" scope="row">Compa&ntilde;&iacute;a</th>
      <td class="vtabla"><input name="num_cia" type="text" class="insert" id="num_cia" onFocus="form.temp.value=this.value" onChange="isInt(this,form.temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 38 || event.keyCode == 40) form.anio.select();" size="3" maxlength="3"></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Cuenta</th>
      <td class="vtabla"><select name="cuenta" class="insert" id="cuenta">
        <option value="1" selected>BANORTE</option>
        <option value="2">SANTANDER SERFIN</option>
      </select></td>
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
      <td class="vtabla"><input name="anio" type="text" class="insert" id="anio" onFocus="form.temp.value=this.value" onChange="isInt(this,form.temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 38 || event.keyCode == 40) form.num_cia.select();" value="{anio}" size="4" maxlength="4"></td>
    </tr>
  </table>  
  <p>
    <input type="button" class="boton" value="Siguiente" onClick="valida_registro(form)">
  </p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
	function valida_registro(form) {
		if (form.num_cia.value <= 0) {
			alert("Debe especificar la compañía");
			form.num_cia.select();
			return false;
		}
		else if (form.anio.value < 2004) {
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
<!-- START BLOCK : captura -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Captura de Dep&oacute;sitos Alternativos </p>
  <form action="./ban_dep_alt.php" method="post" name="form">
  <input name="num_cia" type="hidden" value="{num_cia}">
  <input name="num_dias" type="hidden" value="{num_dias}">
  <input name="temp" type="hidden">
  {con}
  <table class="tabla">
    <tr>
      <th class="tabla" scope="col">Compa&ntilde;&iacute;a</th>
      <th class="tabla" scope="col">Mes</th>
      <th class="tabla" scope="col">A&ntilde;o</th>
    </tr>
    <tr>
      <td class="tabla"><strong>{num_cia} - {nombre_cia} </strong></td>
      <td class="tabla"><strong>{mes}</strong></td>
      <td class="tabla"><strong>{anio}</strong></td>
    </tr>
  </table>  
  <br>
  <table class="tabla">
    <tr>
      <th class="tabla" scope="col">D&iacute;a</th>
      <th class="tabla" scope="col">Dep&oacute;sito 1</th>
      <th class="tabla" scope="col">Dep&oacute;sito 2 </th>
    </tr>
    <!-- START BLOCK : fila -->
	<tr>
      <td class="tabla"><input name="fecha{i}" type="hidden" id="fecha{i}" value="{fecha}">
        {dia}</td>
      <td class="tabla"><input name="dep1_{i}" type="text" class="rinsert" id="dep1_{i}" onFocus="form.temp.value=this.value" onChange="isFloat(this,2,form.temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) form.dep2_{i}.select();
else if (event.keyCode == 37) form.dep2_{back}.select();
else if (event.keyCode == 38) form.dep1_{back}.select();
else if (event.keyCode == 40) form.dep1_{next}.select();" value="{dep1}" size="10" maxlength="10"></td>
      <td class="tabla"><input name="dep2_{i}" type="text" class="rinsert" id="dep2_{i}" onFocus="form.temp.value=this.value" onChange="isFloat(this,2,form.temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) form.dep1_{next}.select();
else if (event.keyCode == 37) form.dep1_{i}.select();
else if (event.keyCode == 38) form.dep2_{back}.select();
else if (event.keyCode == 40) form.dep2_{next}.select();" value="{dep2}" size="10" maxlength="10"></td>
    </tr>
	<!-- END BLOCK : fila -->
  </table>
  <!-- START BLOCK : normal -->
  <p>
    <input name="Button" type="button" class="boton" value="Cancelar" onClick="document.location = './ban_dep_alt.php'">
    &nbsp;&nbsp;
    <input name="Button" type="button" class="boton" value="Capturar" onClick="valida_registro(form)">
  </p>
  <!-- END BLOCK : normal -->
  <!-- START BLOCK : con -->
  <p>
    <input name="Button" type="button" class="boton" value="Cancelar" onClick="document.location = './ban_con_dep_v2.php'">
    &nbsp;&nbsp;
    <input name="Button" type="button" class="boton" value="Capturar" onClick="valida_registro(form)">
  </p>
  <!-- END BLOCK : con -->
  </form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
	function valida_registro(form) {
		form.submit();
	}
	window.onload=document.form.dep1_0.select();
</script>
<!-- END BLOCK : captura -->
</body>
</html>
