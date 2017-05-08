<link href="../../styles/tablas.css" rel="stylesheet" type="text/css">
<link href="../../styles/pages.css" rel="stylesheet" type="text/css">

<!-- START BLOCK : obtener_arrendador -->
<script language="JavaScript" type="text/JavaScript">
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

function valida_registro(){
	if(document.form.arrendador.value==""){
		alert("Debe espedificar un número de arrendador");
		document.form.arrendador.select();
	}
	else{
		document.form.submit();
	}
}
</script>
<table width="100%" height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">

<p class="title">Modificación de Arrendadores</p>
<form name="form" method="get" action="./ren_arrendador_mod.php">
<input name="temp" type="hidden" value="">
<table class="tabla">
  <tr class="tabla">
    <th class="tabla">N&uacute;mero de arrendador </th>
    <th class="tabla">Nombre de arrendador </th>
  </tr>
  <tr class="tabla">
    <td class="tabla"><input name="arrendador" type="text" class="insert" id="arrendador" size="5" onChange="valor=isInt(this,form.temp); if (valor==false) this.value=''; actualiza_arrendador(this,form.nombre_arrendador);" onKeyDown="if(event.keyCode==13 || event.keyCode==9) form.nombre_arrendador.focus();"></td>
    <td class="tabla"><input name="nombre_arrendador" type="text" class="vnombre" id="nombre_arrendador" size="70" readonly></td>
  </tr>
</table>
<p>
<input name="enviar" type="button" class="boton" id="enviar" onClick="valida_registro();" value="Siguiente">
</p>
</form>

<script language="JavaScript" type="text/JavaScript">
window.onload=document.form.arrendador.select();
</script>
</td>
</tr>
</table>

<!-- END BLOCK : obtener_arrendador -->


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
</script>


<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Modificaci&oacute;n  de Arrendadores</p>
  <form name="form" action="./ren_arrendador_mod.php" method="post">
  <table class="tabla">
    <tr>
      <th class="vtabla" scope="row">C&oacute;digo</th>
      <td class="vtabla"><input name="cod_arrendador" type="text" class="insert" id="cod_arrendador" onKeyDown="if(event.keyCode==13) document.form.nombre.select();" value="{id}" size="5" maxlength="5" readonly></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Nombre</th>
      <td class="vtabla"><input name="nombre" type="text" class="vinsert" id="nombre" onKeyDown="if (event.keyCode == 13) representante.select()" value="{nombre}" size="50"></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Representado por </th>
      <td class="vtabla"><input name="representante" type="text" class="vinsert" id="representante" onKeyDown="if (event.keyCode == 13) num_acta.select()" value="{representante}" size="50"></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Tipo</th>
      <td class="vtabla"><input name="tipo_persona" type="radio" value="0" {checked1}>
        F&iacute;sica&nbsp;&nbsp;&nbsp;
        <input name="tipo_persona" type="radio" value="1" {checked2}>
        Moral</td>
    </tr>
	<tr>
		<td colspan="2" class="tabla"><strong>DATOS DEL ACTA CONSTITUTIVA</strong></td>		
	</tr>
    <tr>
      <th class="vtabla" scope="row">N&uacute;mero de Acta </th>
      <td class="vtabla"><input name="num_acta" type="text" class="vinsert" id="num_acta" onKeyDown="if (event.keyCode == 13) ent_fed.select()" value="{acta}" size="20"></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Notario</th>
      <td class="vtabla">
	  <select name="notario" class="insert">
	  <!-- START BLOCK : notario -->
	  <option value="{cod_notario}" {selected}>{cod_notario} - {nombre_notario}</option>
	  <!-- END BLOCK : notario -->
      </select></td>
    </tr>
    <tr>
      <th class="vtabla">Entidad Federativa </th>
      <td class="vtabla"><input name="ent_fed" type="text" class="vinsert" id="ent_fed" onKeyDown="if (event.keyCode == 13) nombre.select()" value="{entidad}" size="30" maxlength="30"></td>
    </tr>
  </table>  
  <p>
	<input type="button" class="boton" value="Regresar" onClick="document.location= './ren_arrendador_mod.php'">&nbsp;&nbsp;&nbsp;
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