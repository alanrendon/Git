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
<!-- START BLOCK : cia -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
<p class="title">Control de Avío</p>
<form action="./pan_avi_altas.php" method="get" name="form">
<input name="temp" type="hidden">
<table class="tabla">
  <tr>
    <th class="vtabla" scope="row">Compa&ntilde;&iacute;a</th>
    <td class="vtabla"><input name="num_cia" type="text" class="insert" id="num_cia" onFocus="form.temp.value=this.value" onChange="isInt(this,form.temp)" size="3" maxlength="3"></td>
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
		else
			document.form.submit();
	}
	
	window.onload = document.form.num_cia.select();
</script>
<!-- END BLOCK : cia -->

<!-- START BLOCK : altas -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Control de Av&iacute;o</p>
  <form action="./pan_avi_altas.php" method="post" name="form">
  <input name="numfilas" type="hidden" value="{numfilas}">
  <input name="temp" type="hidden">
  {cap}
  <table class="tabla">
    <tr>
      <th class="tabla" scope="col">Compa&ntilde;&iacute;a</th>
    </tr>
    <tr>
      <td class="tabla"><input name="num_cia" type="hidden" id="num_cia" value="{num_cia}">
        <strong>{num_cia} - {nombre_cia}</strong> </td>
    </tr>
  </table>  
  <br>
  <table class="tabla">
    <tr>
      <th class="tabla" scope="col">Cod.</th>
      <th class="tabla" scope="col">Nombre</th>
      <th class="tabla" scope="col">Num. orden </th>
      <th class="tabla" scope="col">FD</th>
      <th class="tabla" scope="col">FN</th>
      <th class="tabla" scope="col">BD</th>
      <th class="tabla" scope="col">Rep</th>
      <th class="tabla" scope="col">Pic</th>
      <th class="tabla" scope="col">Gel</th>
      <th class="tabla" scope="col">Des</th>
    </tr>
    <!-- START BLOCK : fila -->
	<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
      <td class="tabla"><input name="codmp{i}" type="hidden" id="codmp{i}" value="{codmp}">
        {codmp}</td>
      <td class="vtabla">{nombre}</td>
      <td class="tabla"><input name="num_orden{i}" type="text" class="insert" id="num_orden{i}" onFocus="form.temp.value=this.value" onChange="isInt(this,form.temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 40) form.num_orden{next}.select();
else if (event.keyCode == 38) form.num_orden{back}.select();" value="{num_orden}" size="3" maxlength="3"></td>
      <td class="tabla"><input name="fd{i}" type="checkbox" id="fd{i}" value="TRUE" {fd_checked}></td>
      <td class="tabla"><input name="fn{i}" type="checkbox" id="fn{i}" value="TRUE" {fn_checked}></td>
      <td class="tabla"><input name="bd{i}" type="checkbox" id="bd{i}" value="TRUE" {bd_checked}></td>
      <td class="tabla"><input name="rep{i}" type="checkbox" id="rep{i}" value="TRUE" {rep_checked}></td>
      <td class="tabla"><input name="pic{i}" type="checkbox" id="pic{i}" value="TRUE" {pic_checked}></td>
      <td class="tabla"><input name="gel{i}" type="checkbox" id="gel{i}" value="TRUE" {gel_checked}></td>
      <td class="tabla"><input name="des{i}" type="checkbox" id="des{i}" value="TRUE" {des_checked}></td>
    </tr>
	<!-- END BLOCK : fila -->
  </table>  <p>
    <input type="button" class="boton" value="Regresar" onClick="history.back()">
&nbsp;&nbsp;    
<input type="button" class="boton" value="Siguiente" onClick="valida_registro(form)">
  </p>
  </form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
	function valida_registro(form) {
		if (confirm("¿Son correctso los datos?"))
			form.submit();
		else
			form.num_orden0.select();
	}
	
	window.onload = document.form.num_orden0.select();
</script>
<!-- END BLOCK : altas -->
</body>
</html>
