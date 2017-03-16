<link href="../../styles/tablas.css" rel="stylesheet" type="text/css">
<link href="../../styles/pages.css" rel="stylesheet" type="text/css">


<!-- START BLOCK : obtener_local -->
<script language="JavaScript" type="text/JavaScript">
function actualiza_local(codigo, nombre) {
	// Arreglo con los nombres de los locales
	local = new Array();				
	<!-- START BLOCK : nombre_local -->
	local[{cod_local}] = '{nombre_local}';
	<!-- END BLOCK : nombre_local -->
			
	if (codigo.value > 0) {
		if (local[codigo.value] == null) {
			alert("Código "+codigo.value+" no esta en el catálogo de locales");
			codigo.value = "";
			nombre.value  = "";
			codigo.select();
		}
		else {
			nombre.value = local[codigo.value];
		}
	}
	else if (codigo.value == "") {
		codigo.value = "";
		nombre.value  = "";
	}
}


function valida_registro(){
	if(document.form.local.value==""){
		alert("Debe espedificar un número de local");
		document.form.local.select();
	}
	else{
		document.form.submit();
	}
}

</script>



<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Modificaci&oacute;n  de Locales </p>
<form name="form" action="./ren_local_mod.php" method="get">
<table class="tabla">
  <tr class="tabla">
    <th class="tabla">N&uacute;mero de local </th>
    <th class="tabla">Nombre del local </th>
  </tr>
  <tr class="tabla">
    <td class="tabla"><input name="local" type="text" class="insert" id="local" size="5" onChange="valor=isInt(this,form.temp); if (valor==false) this.value=''; actualiza_local(this,form.nombre_local);" onKeyDown="if(event.keyCode==13 || event.keyCode==9) form.nombre_local.focus();"></td>
    <td class="tabla"><input name="nombre_local" type="text" class="vnombre" id="nombre_local" size="70" readonly></td>
  </tr>
</table>
<p>
<input name="enviar" type="button" class="boton" id="enviar" onClick="valida_registro();" value="Siguiente">

</p>
</form>
<script language="JavaScript" type="text/JavaScript">
window.onload=document.form.local.select();
</script>

</td>
</tr>
</table>
<!-- END BLOCK : obtener_local -->


<!-- START BLOCK : modificar_datos -->
<script language="JavaScript" type="text/JavaScript">
function valida_registro(){
	if(document.form.nombre.value==""){
		alert("Revise el nombre");
		form.nombre.select();
		return;
	}
	else
		document.form.submit();
		
}
function actualiza_compania(num_cia, nombre) {
	// Arreglo con los nombres de las materias primas
	cia = new Array();				// Materias primas
	<!-- START BLOCK : nombre_cia -->
	cia[{num_cia}] = '{nombre_cia}';
	<!-- END BLOCK : nombre_cia -->
			
	if (num_cia.value > 0) {
		if (cia[num_cia.value] == null) {
			alert("Compañía "+num_cia.value+" no esta en el catálogo de compañías");
			num_cia.value = "";
			nombre.value  = "";
			num_cia.focus();
		}
		else {
			nombre.value = cia[num_cia.value];
		}
	}
	else if (num_cia.value == "") {
		num_cia.value = "";
		nombre.value  = "";
	}
}

function actualiza_arrendador(codigo, nombre) {
	// Arreglo con los nombres de las materias primas
	arrendador = new Array();
	<!-- START BLOCK : nombre_arrendador -->
	arrendador[{cod_arrendador}] = '{nombre_arrendador}';
	<!-- END BLOCK : nombre_arrendador -->
			
	if (codigo.value > 0) {
		if (arrendador[codigo.value] == null) {
			alert("Código "+codigo.value+" no esta en el catálogo de arrendadores");
			codigo.value = "";
			nombre.value  = "";
			codigo.select();
		}
		else {
			nombre.value = arrendador[codigo.value];
		}
	}
	else if (codigo.value == "") {
		codigo.value = "";
		nombre.value  = "";
	}
}

</script>


<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Modificaci&oacute;n  de Locales </p>
  <form name="form" action="./ren_local_mod.php" method="post">
  <input name="temp" type="hidden">
  <table class="tabla">
    <tr>
      <th class="vtabla" scope="row">N&uacute;mero de local </th>
      <td class="vtabla"><input name="cod_local" type="text" class="insert" id="cod_local" onKeyDown="if(event.keyCode==13) document.form.nombre.select();" value="{cod}" size="5" maxlength="5" readonly>
        <input name="id" type="hidden" class="insert" id="id" onKeyDown="if(event.keyCode==13) document.form.nombre.select();" value="{id}" size="5" maxlength="5" readonly></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Nombre</th>
      <td class="vtabla"><input name="nombre" type="text" class="vinsert" id="nombre" onKeyDown="if (event.keyCode == 13) cta_predial.select()" value="{nombre}" size="45" maxlength="45"></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Cuenta de predial </th>
      <td class="vtabla"><input name="cta_predial" type="text" class="vinsert" id="cta_predial" onKeyDown="if (event.keyCode == 13) metros.select()" value="{predial}" size="20" maxlength="30"></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Metros</th>
      <td class="vtabla"><input name="metros" type="text" class="vinsert" onChange="valor=isFloat(this,2,form.temp); if (valor==false) this.select(); " onKeyDown="if(event.keyCode== 13) metros_cuadrados.select();" value="{metros}" size="5"></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Metros cuadrados </th>
      <td class="vtabla"><input name="metros_cuadrados" type="text" class="vinsert" id="metros_cuadrados" onChange="valor=isFloat(this,2,form.temp); if (valor==false) this.select(); " onKeyDown="if (event.keyCode == 13) num_cia.select()" value="{m2}" size="5"></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Compa&ntilde;&iacute;a a cargo </th>
      <td class="vtabla">
	  <input name="num_cia" type="text" class="vinsert" id="num_cia" onChange="valor=isInt(this,form.temp); if (valor==false) this.select(); actualiza_compania(this,form.nombre_cia)" onKeyDown="if(event.keyCode==13) document.form.cod_arrendador.select();" value="{num_cia}" size="5" maxlength="5">
        <input name="nombre_cia" type="text" class="vnombre" id="nombre_cia" value="{nombre_cia}" size="40" readonly></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Arrendador</th>
      <td class="vtabla"><input name="cod_arrendador" type="text" class="vinsert" id="cod_arrendador" onChange="valor=isInt(this,form.temp); if (valor==false) this.select(); actualiza_arrendador(this,form.nombre_arrendador);" onKeyDown="if (event.keyCode == 13) nombre.select()" value="{arrendador}" size="5">
        <input name="nombre_arrendador" type="text" class="vnombre" id="nombre_arrendador" value="{nombre_arrendador}" size="40" readonly></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Bloque</th>
      <td class="vtabla"><select name="bloque" size="1" class="insert" id="bloque">
        <option value="1" {selected1}>1 - Propios</option>
        <option value="2" {selected2}>2 - Ajenos</option>
      </select></td>
    </tr>
<!--
    <tr>
      <th class="vtabla" scope="row">Maneja varios locales</th>
      <td class="vtabla"><p>
        <label>
        <input name="varios" type="radio" value="false" checked>
  No</label>
        <label>
        <input type="radio" name="varios" value="true">
  Si</label>
        <br>
      </p></td>
    </tr>
-->
  </table>  
  <p>
    <input type="button" class="boton" value="Modificar" onClick="valida_registro();">
  </p>
</form>  
<script language="JavaScript" type="text/JavaScript">
window.onload=document.form.nombre.select();
</script>
</td>
</tr>
</table>
<!-- END BLOCK : modificar_datos -->