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
<!-- START BLOCK : captura -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Captura de Cheques</p>
  <form action="./ban_cap_che.php" method="post" name="form">
    <input name="tmp" type="hidden" id="tmp">
    <table class="tabla">
      <tr>
        <th class="tabla" scope="col">Cuenta</th>
      </tr>
      <tr>
        <td class="tabla"><select name="cuenta" class="insert" id="cuenta">
          <option value="1">BANORTE</option>
          <option value="2" selected>SANTANDER</option>
        </select></td>
      </tr>
    </table>
    <br>
    <table class="tabla">
    <tr>
      <th class="tabla" scope="col">Compa&ntilde;&iacute;a</th>
      <th class="tabla" scope="col">Proveedor</th>
      <th class="tabla" scope="col">Fecha</th>
      <th class="tabla" scope="col">Folio</th>
      <th class="tabla" scope="col">Concepto</th>
      <th class="tabla" scope="col">Codigo</th>
      <th class="tabla" scope="col">Importe</th>
      </tr>
    <!-- START BLOCK : fila -->
	<tr>
      <td class="tabla"><input name="num_cia[]" type="text" class="insert" id="num_cia" onFocus="tmp.value=this.value;this.select()" onChange="if (isInt(this,tmp)) cambiaCia({i})" onKeyDown="if (event.keyCode == 13) num_pro[{i}].select()" value="{num_cia}" size="3" maxlength="3">
        <input name="nombre_cia[]" type="text" class="vnombre" id="nombre_cia" value="{nombre_cia}" size="20" readonly="true"></td>
      <td class="tabla"><input name="num_pro[]" type="text" class="insert" id="num_pro" onFocus="tmp.value=this.value;this.select()" onChange="if (isInt(this,tmp)) cambiaPro({i})" onKeyDown="if (event.keyCode == 13) fecha[{i}].select()" value="{num_pro}" size="3" maxlength="4">
        <input name="nombre_pro[]" type="text" class="vnombre" id="nombre_pro" value="{nombre_pro}" size="20"></td>
      <td class="tabla"><input name="fecha[]" type="text" class="insert" id="fecha" onChange="actualiza_fecha(this)" onKeyDown="if (event.keyCode == 13) folio[{i}].select()" value="{fecha}" size="10" maxlength="10"></td>
      <td class="tabla"><input name="folio[]" type="text" class="insert" id="folio" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) concepto[{i}].select()" value="{folio}" size="5"></td>
      <td class="tabla"><input name="concepto[]" type="text" class="vinsert" id="concepto" onKeyDown="if (event.keyCode == 13) codgastos[{i}].select()" value="{concepto}" size="30" maxlength="255"></td>
      <td class="tabla"><input name="codgastos[]" type="text" class="insert" id="codgastos" onFocus="tmp.value=this.value;this.select()" onChange="if (isInt(this,tmp)) cambiaGas({i})" onKeyDown="if (event.keyCode == 13) importe[{i}].select()" value="{codgastos}" size="3">
        <input name="nombre_gas[]" type="text" class="vnombre" id="nombre_gas" value="{nombre_gas}" size="20"></td>
      <td class="tabla"><input name="importe[]" type="text" class="rinsert" id="importe" onFocus="tmp.value=this.value;this.select()" onChange="input_format(this,2,true)" onKeyDown="if (event.keyCode == 13) num_fact0[{i}].select()" value="{importe}" size="10"></td>
    </tr>
	<tr>
	  
	  <th class="tabla">Facturas</th>
	  <td colspan="6" class="vtabla"><input name="num_fact0[]" type="text" class="insert" id="num_fact0" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) num_fact1[{i}].select()" value="{num_fact}" size="8">
	    <input name="num_fact1[]" type="text" class="insert" id="num_fact1" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) num_fact2[{i}].select()" value="{num_fact}" size="8">
	    <input name="num_fact2[]" type="text" class="insert" id="num_fact2" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) num_fact3[{i}].select()" value="{num_fact}" size="8">
	    <input name="num_fact3[]" type="text" class="insert" id="num_fact3" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) num_fact4[{i}].select()" value="{num_fact}" size="8">
	    <input name="num_fact4[]" type="text" class="insert" id="num_fact4" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) num_fact5[{i}].select()" value="{num_fact}" size="8">
	    <input name="num_fact5[]" type="text" class="insert" id="num_fact5" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) num_fact6[{i}].select()" value="{num_fact}" size="8">
	    <input name="num_fact6[]" type="text" class="insert" id="num_fact6" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) num_fact7[{i}].select()" value="{num_fact}" size="8">
	    <input name="num_fact7[]" type="text" class="insert" id="num_fact7" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) num_fact8[{i}].select()" value="{num_fact}" size="8">
	    <input name="num_fact8[]" type="text" class="insert" id="num_fact8" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) num_fact9[{i}].select()" value="{num_fact}" size="8">
	    <input name="num_fact9[]" type="text" class="insert" id="num_fact9" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) num_cia[{next}].select()" value="{num_fact}" size="8"></td>
	  </tr>
	<tr>
	  <td colspan="7" class="tabla">&nbsp;</td>
	  </tr>
	<!-- END BLOCK : fila -->
  </table>  
    <p>
    <input type="button" class="boton" value="Siguiente" onClick="validar()">
  </p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
<!--
var f = document.form, cia = new Array(), pro = new Array(), gas = new Array();
<!-- START BLOCK : cia -->
cia[{num_cia}] = "{nombre}";
<!-- END BLOCK : cia -->
<!-- START BLOCK : pro -->
pro[{num_pro}] = "{nombre}";
<!-- END BLOCK : pro -->
<!-- START BLOCK : gas -->
gas[{cod}] = "{nombre}";
<!-- END BLOCK : gas -->

function cambiaCia(i) {
	if (f.num_cia.value == "" || f.num_cia.value == "0") {
		f.num_cia[i].value = "";
		f.nombre_cia[i].value = "";
	}
	else if (cia[f.num_cia[i].value] != null)
		f.nombre_cia[i].value = cia[f.num_cia[i].value];
	else {
		alert("La compañía no se encuentra en el catálogo");
		f.num_cia[i].value = f.tmp.value;
	}
}

function cambiaPro(i) {
	if (f.num_pro.value == "" || f.num_pro.value == "0") {
		f.num_pro[i].value = "";
		f.nombre_pro[i].value = "";
	}
	else if (pro[f.num_pro[i].value] != null)
		f.nombre_pro[i].value = pro[f.num_pro[i].value];
	else {
		alert("El proveedor no se encuentra en el catálogo");
		f.num_pro[i].value = f.tmp.value;
	}
}

function cambiaGas(i) {
	if (f.codgastos.value == "" || f.codgastos.value == "0") {
		f.codgastos[i].value = "";
		f.nombre_gas[i].value = "";
	}
	else if (gas[f.codgastos[i].value] != null)
		f.nombre_gas[i].value = gas[f.codgastos[i].value];
	else {
		alert("El proveedor no se encuentra en el catálogo");
		f.codgastos[i].value = f.tmp.value;
	}
}

function validar() {
	if (confirm("¿Son correctos los datos?"))
		f.submit();
	else
		return false;
}

window.onload = f.num_cia[0].select();
-->
</script>
<!-- END BLOCK : captura -->
<!-- START BLOCK : errores -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Captura de Cheques </p>
  <table class="tabla">
    <tr>
      <th class="tabla" scope="col">Compa&ntilde;&iacute;a</th>
      <th class="tabla" scope="col">Folio</th>
      <th class="tabla" scope="col">Beneficiario</th>
      <th class="tabla" scope="col">Importe</th>
    </tr>
    <!-- START BLOCK : error -->
	<tr>
      <td class="vtabla">{num_cia} {nombre_cia} </td>
      <td class="tabla">{folio}</td>
      <td class="vtabla">{num_pro} {nombre_pro}</td>
      <td class="rtabla">{importe}</td>
    </tr>
	<!-- END BLOCK : error -->
  </table>  <p>
    <input type="button" class="boton" value="Siguiente" onClick="document.location='ban_cap_che.php'">
  </p></td>
</tr>
</table>
<!-- END BLOCK : errores -->
</body>
</html>
