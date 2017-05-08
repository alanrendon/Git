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
<td align="center" valign="middle"><p class="title">Cambio de Estatus de Revisiones Vehiculares </p>
  <form action="./doc_cam_rev_cap.php" method="get" name="form">
  <input name="temp" type="hidden">
  <table class="tabla">
    <tr>
      <th class="vtabla" scope="row">No. Camioneta </th>
      <td class="vtabla"><input name="idcamioneta" type="text" class="insert" id="idcamioneta" onFocus="temp.value=this.value" onChange="isInt(this,temp)" onKeyDown="if (event.keyCode == 13) num_cia.select()" size="4" maxlength="4"></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Compa&ntilde;&iacute;a</th>
      <td class="vtabla"><input name="num_cia" type="text" class="insert" id="num_cia" onFocus="temp.value=this.value" onChange="isInt(this,temp)" onKeyDown="if (event.keyCode == 13) anio.select()" size="3" maxlength="3"></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">A&ntilde;o</th>
      <td class="vtabla"><input name="anio" type="text" class="insert" id="anio" onFocus="temp.value=this.value" onChange="isInt(this,temp)" onKeyDown="if (event.keyCode == 13) idcamioneta.select()" value="{anio}" size="4" maxlength="4"></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Color</th>
      <td class="vtabla"><span class="vtabla"><select name="color" class="insert" id="color">
        <option selected>TODAS</option>
        <option value="1">AMARILLO</option>
        <option value="2">ROSA</option>
        <option value="3">ROJO</option>
        <option value="4">VERDE</option>
        <option value="5">AZUL</option>
      </select></span></td>
    </tr>
  </table>  
  <p>
    <input type="button" class="boton" value="Siguiente" onClick="validar(this.form)">
  </p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
	function validar(form) {
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
<!-- START BLOCK : listado -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Cambio de Estatus de Revisiones Vehiculares</p>
  <table class="tabla">
    <tr>
      <th class="tabla" scope="col">A&ntilde;o</th>
    </tr>
    <tr>
      <th class="tabla"><font size="+1">{anio}</font></th>
    </tr>
  </table><br><form action="./doc_cam_rev_cap.php" method="post" name="form">
  <input name="anio" type="hidden" value="{anio}">
  <table class="tabla">
    <tr>
      <th class="tabla" scope="col">No.</th>
      <th class="tabla" scope="col">Modelo</th>
      <th class="tabla" scope="col">A&ntilde;o</th>
      <th class="tabla" scope="col">Placas</th>
      <th class="tabla" scope="col">Color</th>
      <th class="tabla" scope="col">Compa&ntilde;&iacute;a</th>
      <th class="tabla" scope="col">1&ordf; Verificaci&oacute;n </th>
      <th class="tabla" scope="col">2&ordf; Verificaci&oacute;n </th>
      <th class="tabla" scope="col">Revista</th>
      <th class="tabla" scope="col">Tenencia</th>
    </tr>
    <!-- START BLOCK : fila -->
	<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
      <td class="tabla"><input name="idcamioneta[{i}]" type="hidden" id="id" value="{id}">
        {id}</td>
      <td class="vtabla">{modelo}</td>
      <td class="tabla">{anio}</td>
      <td class="tabla">{placas}</td>
      <td class="tabla">{color}</td>
      <td class="vtabla">{num_cia} - {nombre_cia} </td>
      <td class="vtabla"><input name="ver1_ok[{i}]" type="checkbox" id="ver1_ok" onClick="if (this.checked) ver1_rec{I}.disabled=false; else ver1_rec{I}.disabled=true;" value="1" {ver1_ok}>
        Hecha
        <input name="ver1_rec[{i}]" type="checkbox" id="ver1_rec" value="1" {ver1_rec_dis} {ver1_rec}>
        Recibo<br>
        Observaciones<br>
        <textarea name="ver1_obs[{i}]" cols="15" rows="2" wrap="VIRTUAL" class="insert" id="ver1_obs">{ver1_obs}</textarea></td>
      <td class="vtabla"><input name="ver2_ok[{i}]" type="checkbox" id="ver2_ok" onClick="if (this.checked) ver2_rec{I}.disabled=false; else ver2_rec{I}.disabled=true;" value="1" {ver2_ok}>
        Hecha
        <input name="ver2_rec[{i}]" type="checkbox" id="ver2_rec" value="1" {ver2_rec_dis} {ver2_rec}>
        Recibo<br>
        Observaciones<br>
        <textarea name="ver2_obs[{i}]" cols="15" rows="2" wrap="VIRTUAL" class="insert" id="ver2_obs">{ver2_obs}</textarea></td>
      <td class="vtabla"><input name="rev_ok[{i}]" type="checkbox" id="rev_ok" onClick="if (this.checked) rev_rec{I}.disabled=false; else rev_rec{I}.disabled=true;" value="1" {rev_ok}>
        Hecha
        <input name="rev_rec[{i}]" type="checkbox" id="rev_rec" value="1" {rev_rec_dis} {rev_rec}>
        Recibo<br>
        Observaciones<br>
        <textarea name="rev_obs[{i}]" cols="15" rows="2" wrap="VIRTUAL" class="insert" id="rev_obs">{rev_obs}</textarea></td>
      <td class="vtabla"><input name="ten_ok[{i}]" type="checkbox" id="ten_ok" onClick="if (this.checked) ten_rec{I}.disabled=false; else ten_rec{I}.disabled=true;" value="1" {ten_ok}>
        Hecha
        <input name="ten_rec[{i}]" type="checkbox" id="ten_rec" value="1" {ten_rec_dis} {ten_rec}>
        Recibo<br>
        Observaciones<br>
        <textarea name="ten_obs[{i}]" cols="15" rows="2" wrap="VIRTUAL" class="insert" id="ten_obs">{ten_obs}</textarea></td>
    </tr>
	<!-- END BLOCK : fila -->
  </table>  
  <p>
    <input type="button" class="boton" value="Regresar" onClick="document.location='./doc_cam_rev_cap.php'">
&nbsp;&nbsp;    
<input type="button" class="boton" value="Siguiente" onClick="validar(this.form)"> 
    </p>
  </form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
	function validar(form) {
		if (confirm("¿Son correctos los datos?"))
			form.submit();
		else
			return false
	}
</script>
<!-- END BLOCK : listado -->
</body>
</html>
