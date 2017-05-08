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
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Aplicar Pagos Fijos</p>
  <form action="./ban_gen_che_fij.php" method="get" name="form">
    <input name="tmp" type="hidden" id="tmp">
    <table class="tabla">
    <tr>
      <th class="vtabla" scope="row">Cuenta</th>
      <td class="vtabla"><select name="cuenta" class="insert" id="cuenta">
        <option value="1">BANORTE</option>
        <option value="2" selected>SANTANDER</option>
      </select></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Con fecha de</th>
      <td class="vtabla"><input name="fecha" type="text" class="insert" id="fecha" onChange="actualiza_fecha(this)" onKeyDown="if (event.keyCode == 13) num_cia[0].select()" value="{fecha}" size="10" maxlength="10"></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Compa&ntilde;&iacute;as espec&iacute;ficos </th>
      <td class="vtabla"><input name="num_cia[]" type="text" class="insert" id="num_cia" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) num_cia[1].select()" size="3">
	  <input name="num_cia[]" type="text" class="insert" id="num_cia" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) num_cia[2].select()" size="3">
	  <input name="num_cia[]" type="text" class="insert" id="num_cia" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) num_cia[3].select()" size="3">
	  <input name="num_cia[]" type="text" class="insert" id="num_cia" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) num_cia[4].select()" size="3">
	  <input name="num_cia[]" type="text" class="insert" id="num_cia" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) num_cia[5].select()" size="3">
	  <input name="num_cia[]" type="text" class="insert" id="num_cia" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) num_cia[6].select()" size="3">
	  <input name="num_cia[]" type="text" class="insert" id="num_cia" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) num_cia[7].select()" size="3">
	  <input name="num_cia[]" type="text" class="insert" id="num_cia" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) num_cia[8].select()" size="3">
	  <input name="num_cia[]" type="text" class="insert" id="num_cia" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) num_cia[9].select()" size="3">
	  <input name="num_cia[]" type="text" class="insert" id="num_cia" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) num_cia[10].select()" size="3"><br>
	  <input name="num_cia[]" type="text" class="insert" id="num_cia" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) num_cia[11].select()" size="3">
	  <input name="num_cia[]" type="text" class="insert" id="num_cia" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) num_cia[12].select()" size="3">
	  <input name="num_cia[]" type="text" class="insert" id="num_cia" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) num_cia[13].select()" size="3">
	  <input name="num_cia[]" type="text" class="insert" id="num_cia" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) num_cia[14].select()" size="3">
	  <input name="num_cia[]" type="text" class="insert" id="num_cia" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) num_cia[15].select()" size="3">
	  <input name="num_cia[]" type="text" class="insert" id="num_cia" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) num_cia[16].select()" size="3">
	  <input name="num_cia[]" type="text" class="insert" id="num_cia" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) num_cia1[7].select()" size="3">
	  <input name="num_cia[]" type="text" class="insert" id="num_cia" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) num_cia[18].select()" size="3">
	  <input name="num_cia[]" type="text" class="insert" id="num_cia" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) num_cia[19].select()" size="3">
	  <input name="num_cia[]" type="text" class="insert" id="num_cia" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) num_pro[0].select()" size="3"></td>
    </tr>
	<tr>
      <th class="vtabla" scope="row">Proveedores espec&iacute;ficos </th>
      <td class="vtabla"><input name="num_pro[]" type="text" class="insert" id="num_pro" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) num_pro[1].select()" size="3">
	  <input name="num_pro[]" type="text" class="insert" id="num_pro" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) num_pro[2].select()" size="3">
	  <input name="num_pro[]" type="text" class="insert" id="num_pro" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) num_pro[3].select()" size="3">
	  <input name="num_pro[]" type="text" class="insert" id="num_pro" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) num_pro[4].select()" size="3">
	  <input name="num_pro[]" type="text" class="insert" id="num_pro" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) num_pro[5].select()" size="3">
	  <input name="num_pro[]" type="text" class="insert" id="num_pro" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) num_pro[6].select()" size="3">
	  <input name="num_pro[]" type="text" class="insert" id="num_pro" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) num_pro[7].select()" size="3">
	  <input name="num_pro[]" type="text" class="insert" id="num_pro" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) num_pro[8].select()" size="3">
	  <input name="num_pro[]" type="text" class="insert" id="num_pro" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) num_pro[9].select()" size="3">
	  <input name="num_pro[]" type="text" class="insert" id="num_pro" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) num_pro[10].select()" size="3"><br>
	  <input name="num_pro[]" type="text" class="insert" id="num_pro" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) num_pro[11].select()" size="3">
	  <input name="num_pro[]" type="text" class="insert" id="num_pro" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) num_pro[12].select()" size="3">
	  <input name="num_pro[]" type="text" class="insert" id="num_pro" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) num_pro[13].select()" size="3">
	  <input name="num_pro[]" type="text" class="insert" id="num_pro" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) num_pro[14].select()" size="3">
	  <input name="num_pro[]" type="text" class="insert" id="num_pro" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) num_pro[15].select()" size="3">
	  <input name="num_pro[]" type="text" class="insert" id="num_pro" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) num_pro[16].select()" size="3">
	  <input name="num_pro[]" type="text" class="insert" id="num_pro" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) num_pro[17].select()" size="3">
	  <input name="num_pro[]" type="text" class="insert" id="num_pro" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) num_pro[18].select()" size="3">
	  <input name="num_pro[]" type="text" class="insert" id="num_pro" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) num_pro[19].select()" size="3">
	  <input name="num_pro[]" type="text" class="insert" id="num_pro" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) no_pro[0].select()" size="3"></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Omitir proveedores </th>
      <td class="vtabla"><input name="no_pro[]" type="text" class="insert" id="no_pro" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) no_pro[1].select()" size="3">
	  <input name="no_pro[]" type="text" class="insert" id="no_pro" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) no_pro[2].select()" size="3">
	  <input name="no_pro[]" type="text" class="insert" id="no_pro" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) no_pro[3].select()" size="3">
	  <input name="no_pro[]" type="text" class="insert" id="no_pro" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) no_pro[4].select()" size="3">
	  <input name="no_pro[]" type="text" class="insert" id="no_pro" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) no_pro[5].select()" size="3">
	  <input name="no_pro[]" type="text" class="insert" id="no_pro" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) no_pro[6].select()" size="3">
	  <input name="no_pro[]" type="text" class="insert" id="no_pro" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) no_pro[7].select()" size="3">
	  <input name="no_pro[]" type="text" class="insert" id="no_pro" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) no_pro[8].select()" size="3">
	  <input name="no_pro[]" type="text" class="insert" id="no_pro" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) no_pro[9].select()" size="3">
	  <input name="no_pro[]" type="text" class="insert" id="no_pro" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) no_pro[10].select()" size="3"><br>
	  <input name="no_pro[]" type="text" class="insert" id="no_pro" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) no_pro[11].select()" size="3">
	  <input name="no_pro[]" type="text" class="insert" id="no_pro" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) no_pro[12].select()" size="3">
	  <input name="no_pro[]" type="text" class="insert" id="no_pro" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) no_pro[13].select()" size="3">
	  <input name="no_pro[]" type="text" class="insert" id="no_pro" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) no_pro[14].select()" size="3">
	  <input name="no_pro[]" type="text" class="insert" id="no_pro" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) no_pro[15].select()" size="3">
	  <input name="no_pro[]" type="text" class="insert" id="no_pro" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) no_pro[16].select()" size="3">
	  <input name="no_pro[]" type="text" class="insert" id="no_pro" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) no_pro[17].select()" size="3">
	  <input name="no_pro[]" type="text" class="insert" id="no_pro" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) no_pro[18].select()" size="3">
	  <input name="no_pro[]" type="text" class="insert" id="no_pro" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) no_pro[19].select()" size="3">
	  <input name="no_pro[]" type="text" class="insert" id="no_pro" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) no_cia[0].select()" size="3"></td>
    </tr>
	 <tr>
      <th class="vtabla" scope="row">Omitir compa&ntilde;&iacute;as </th>
      <td class="vtabla"><input name="no_cia[]" type="text" class="insert" id="no_cia" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) no_cia[1].select()" size="3">
	  <input name="no_cia[]" type="text" class="insert" id="no_cia" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) no_cia[2].select()" size="3">
	  <input name="no_cia[]" type="text" class="insert" id="no_cia" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) no_cia[3].select()" size="3">
	  <input name="no_cia[]" type="text" class="insert" id="no_cia" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) no_cia[4].select()" size="3">
	  <input name="no_cia[]" type="text" class="insert" id="no_cia" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) no_cia[5].select()" size="3">
	  <input name="no_cia[]" type="text" class="insert" id="no_cia" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) no_cia[6].select()" size="3">
	  <input name="no_cia[]" type="text" class="insert" id="no_cia" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) no_cia[7].select()" size="3">
	  <input name="no_cia[]" type="text" class="insert" id="no_cia" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) no_cia[8].select()" size="3">
	  <input name="no_cia[]" type="text" class="insert" id="no_cia" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) no_cia[9].select()" size="3">
	  <input name="no_cia[]" type="text" class="insert" id="no_cia" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) no_cia[10].select()" size="3"><br>
	  <input name="no_cia[]" type="text" class="insert" id="no_cia" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) no_cia[11].select()" size="3">
	  <input name="no_cia[]" type="text" class="insert" id="no_cia" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) no_cia[12].select()" size="3">
	  <input name="no_cia[]" type="text" class="insert" id="no_cia" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) no_cia[13].select()" size="3">
	  <input name="no_cia[]" type="text" class="insert" id="no_cia" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) no_cia[14].select()" size="3">
	  <input name="no_cia[]" type="text" class="insert" id="no_cia" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) no_cia[15].select()" size="3">
	  <input name="no_cia[]" type="text" class="insert" id="no_cia" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) no_cia[16].select()" size="3">
	  <input name="no_cia[]" type="text" class="insert" id="no_cia" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) no_cia[17].select()" size="3">
	  <input name="no_cia[]" type="text" class="insert" id="no_cia" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) no_cia[18].select()" size="3">
	  <input name="no_cia[]" type="text" class="insert" id="no_cia" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) no_cia[19].select()" size="3">
	  <input name="no_cia[]" type="text" class="insert" id="no_cia" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) fecha.select()" size="3"></td>
    </tr>
  </table>  
  <p>
    <input type="button" class="boton" value="Siguiente" onClick="validar(this.form)">
  </p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
<!--
function validar(form) {
	if (form.fecha.value.length < 8) {
		alert("Debe especificar la fecha");
		form.fecha.select();
		return false;
	}
	else if (confirm("¿Desea generar los pagos?")) {
		form.submit();
	}
	else {
		form.fecha.select();
		return false;
	}
}

window.onload = document.form.fecha.select();
-->
</script>
</body>
</html>
