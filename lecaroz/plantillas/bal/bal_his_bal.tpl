<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Untitled Document</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="/lecaroz/styles/tablas.css" rel="stylesheet" type="text/css">
<link href="/lecaroz/styles/pages.css" rel="stylesheet" type="text/css">
</head>

<body>
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Hist&oacute;rico de Balance</p>
  <form action="./historico_bal_v2.php" method="get" name="form">
  <input name="temp" type="hidden">
  <table class="tabla">
    <tr>
      <th class="vtabla">Compa&ntilde;&iacute;a</th>
      <td class="vtabla"><input name="num_cia[]" type="text" class="insert" id="num_cia" onFocus="temp.value=this.value;this.select()" onChange="isInt(this,temp)" onKeyDown="if (event.keyCode==13)num_cia[1].select()" size="3" maxlength="3">
	  <input name="num_cia[]" type="text" class="insert" id="num_cia" onFocus="temp.value=this.value;this.select()" onChange="isInt(this,temp)" onKeyDown="if (event.keyCode==13)num_cia[2].select()" size="3" maxlength="3">
	  <input name="num_cia[]" type="text" class="insert" id="num_cia" onFocus="temp.value=this.value;this.select()" onChange="isInt(this,temp)" onKeyDown="if (event.keyCode==13)num_cia[3].select()" size="3" maxlength="3">
	  <input name="num_cia[]" type="text" class="insert" id="num_cia" onFocus="temp.value=this.value;this.select()" onChange="isInt(this,temp)" onKeyDown="if (event.keyCode==13)num_cia[4].select()" size="3" maxlength="3">
	  <input name="num_cia[]" type="text" class="insert" id="num_cia" onFocus="temp.value=this.value;this.select()" onChange="isInt(this,temp)" onKeyDown="if (event.keyCode==13)num_cia[5].select()" size="3" maxlength="3">
	  <input name="num_cia[]" type="text" class="insert" id="num_cia" onFocus="temp.value=this.value;this.select()" onChange="isInt(this,temp)" onKeyDown="if (event.keyCode==13)num_cia[6].select()" size="3" maxlength="3">
	  <input name="num_cia[]" type="text" class="insert" id="num_cia" onFocus="temp.value=this.value;this.select()" onChange="isInt(this,temp)" onKeyDown="if (event.keyCode==13)num_cia[7].select()" size="3" maxlength="3">
	  <input name="num_cia[]" type="text" class="insert" id="num_cia" onFocus="temp.value=this.value;this.select()" onChange="isInt(this,temp)" onKeyDown="if (event.keyCode==13)num_cia[8].select()" size="3" maxlength="3">
	  <input name="num_cia[]" type="text" class="insert" id="num_cia" onFocus="temp.value=this.value;this.select()" onChange="isInt(this,temp)" onKeyDown="if (event.keyCode==13)num_cia[9].select()" size="3" maxlength="3">
	  <input name="num_cia[]" type="text" class="insert" id="num_cia" onFocus="temp.value=this.value;this.select()" onChange="isInt(this,temp)" onKeyDown="if (event.keyCode==13)anio.select()" size="3" maxlength="3"></td>
      <th class="vtabla">A&ntilde;o</th>
      <td class="vtabla">
        <input name="anio" type="text" class="insert" id="anio" onFocus="temp.value=this.value" onChange="isInt(this,temp)" onKeyDown="if (event.keyCode==13)num_cia[0].select();" value="{anio}" size="4" maxlength="4">
        &nbsp;<input name="agrupar_totales" type="checkbox" id="agrupar_totales" value="1">Solo totales
      </td>
    </tr>
    <tr>
      <th class="vtabla">Administrador</th>
      <td colspan="3" class="vtabla"><select name="idadmin" class="insert" id="idadmin">
        <option value=""></option>
		<!-- START BLOCK : admin -->
        <option value="{id}">{admin}</option>
		<!-- END BLOCK : admin -->
      </select>
      </td>
      </tr>
    <tr>
      <th colspan="4" class="vtabla">
        <input name="rango" type="radio" value="" checked>
        Todos&nbsp;&nbsp;&nbsp;
        <input name="rango" type="radio" value="pan">
        Panader&iacute;as&nbsp;&nbsp;&nbsp;
        <input name="rango" type="radio" value="ros">
        Rosticer&iacute;as</th>
      </tr>
  </table>
  <p>
    <input type="button" class="boton" value="Siguiente" onClick="valida_registro(form)">
    </p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
	function valida_registro(form) {
		if (form.anio.value <= 2000) {
			alert("Debe especificar el año");
			form.anio.select();
			return false;
		}
		else {
			window.open("","historico_bal","toolbar=no,location=no,directories=no,status=no,menubar=yes,scrollbars=yes,resizable=no,width=1024,height=768");
			form.target = "historico_bal";
			form.submit();
		}
	}

	window.onload = document.form.num_cia[0].select();
</script>
</body>
</html>
