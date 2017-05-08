<link href="../../styles/tablas.css" rel="stylesheet" type="text/css">
<link href="../../styles/pages.css" rel="stylesheet" type="text/css">
<!-- START BLOCK : obtener_datos -->

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
	arrendador = new Array();				// Materias primas
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

function disponibilidad(indice){
	arreglo = new Array();
	var contador;
	<!-- START BLOCK : ocupados -->
	arreglo[{i}]='{cod_local}';
	<!-- END BLOCK : ocupados -->
	for(i=0;i<arreglo.length;i++){
		if(parseInt(indice.value)==arreglo[i]){
			alert("El código ya esta ocupado");
			indice.value=document.form.temp1.value;
			document.form.nombre.select();
			return;
		}
	}
	return;
}
</script>


<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Alta de Locales </p>
  <form name="form" action="./ren_local_alta.php" method="get">
  <input name="temp" type="hidden">
  <input name="temp1" type="hidden">
  <table class="tabla">
    <tr>
      <th class="vtabla" scope="row">N&uacute;mero de local </th>
      <td class="vtabla"><input name="cod_local" type="text" class="insert" id="cod_local" onFocus="temp1.value=this.value;"onChange="disponibilidad(this);" onKeyDown="if(event.keyCode==13) document.form.nombre.select();" value="{id}" size="5" maxlength="5"></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Nombre</th>
      <td class="vtabla"><input name="nombre" type="text" class="vinsert" id="nombre" onKeyDown="if (event.keyCode == 13) cta_predial.select()" size="45" maxlength="45"></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Cuenta de predial </th>
      <td class="vtabla"><input name="cta_predial" type="text" class="vinsert" id="cta_predial" onKeyDown="if (event.keyCode == 13) metros.select()" size="20" maxlength="30"></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Metros</th>
      <td class="vtabla"><input name="metros" type="text" size="5" class="vinsert" onKeyDown="if(event.keyCode== 13) metros_cuadrados.select();" onChange="valor=isFloat(this,2,form.temp); if (valor==false) this.select(); "></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Metros cuadrados </th>
      <td class="vtabla"><input name="metros_cuadrados" type="text" class="vinsert" id="metros_cuadrados" onKeyDown="if (event.keyCode == 13) num_cia.select()" size="5" onChange="valor=isFloat(this,2,form.temp); if (valor==false) this.select(); "></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Compa&ntilde;&iacute;a a cargo </th>
      <td class="vtabla">
	  <input name="num_cia" type="text" class="vinsert" id="num_cia" onKeyDown="if(event.keyCode==13) document.form.cod_arrendador.select();" size="5" maxlength="5" onChange="valor=isInt(this,form.temp); if (valor==false) this.select(); actualiza_compania(this,form.nombre_cia)">
        <input name="nombre_cia" type="text" class="vnombre" id="nombre_cia" size="40" readonly></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Arrendador</th>
      <td class="vtabla"><input name="cod_arrendador" type="text" class="vinsert" id="cod_arrendador" onKeyDown="if (event.keyCode == 13) locales.select()" size="5" onChange="valor=isInt(this,form.temp); if (valor==false) this.select(); actualiza_arrendador(this,form.nombre_arrendador);">
        <input name="nombre_arrendador" type="text" class="vnombre" id="nombre_arrendador" size="40" readonly></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Bloque</th>
      <td class="vtabla"><select name="bloque" size="1" class="insert" id="bloque">
        <option value="1">1 - Internos</option>
        <option value="2">2 - Externos</option>
      </select></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">N&uacute;mero de locales </th>
      <td class="vtabla"><input name="locales" type="text" class="insert" id="locales" value="1" size="5" onChange="valor=isInt(this,form.temp); if (valor==false) this.value='';" onKeyDown="if (event.keyCode == 13) nombre.select();"></td>
    </tr>
  </table>  
  <p>
    <input type="button" class="boton" value="Alta" onClick="valida_registro();">
  </p>
</form>  
<script language="JavaScript" type="text/JavaScript">
window.onload=document.form.nombre.select();
</script>
  
</td>
</tr>
</table>
<!-- END BLOCK : obtener_datos -->