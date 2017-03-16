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

function disponibilidad(indice){
	arreglo = new Array();
	var contador;
	<!-- START BLOCK : ocupados -->
	arreglo[{i}]='{cod_arrendador}';
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
<td align="center" valign="middle"><p class="title">Alta de Arrendadores</p>
<form name="form" action="./ren_arrendador_alta.php" method="get">
  <input name="temp" type="hidden">
  <input name="temp1" type="hidden">
  <table class="tabla">
    <tr>
      <th class="vtabla" scope="row">C&oacute;digo</th>
      <td class="vtabla">
	  <input name="cod_arrendador" type="text" class="insert" id="cod_arrendador" onFocus="temp1.value=this.value;" onChange="disponibilidad(this);" onKeyDown="if(event.keyCode==13) document.form.nombre.select();" value="{id}" size="5" maxlength="5">
	  </td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Nombre</th>
      <td class="vtabla"><input name="nombre" type="text" class="vinsert" id="nombre" onKeyDown="if (event.keyCode == 13) representante.select()" size="50"></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Representado por </th>
      <td class="vtabla"><input name="representante" type="text" class="vinsert" id="representante" onKeyDown="if (event.keyCode == 13) num_acta.select()" size="50"></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Tipo</th>
      <td class="vtabla"><input name="tipo_persona" type="radio" value="0">
        F&iacute;sica&nbsp;&nbsp;&nbsp;
        <input name="tipo_persona" type="radio" value="1" checked>
        Moral</td>
    </tr>
	<tr>
		<td colspan="2" class="tabla"><strong>DATOS DEL ACTA CONSTITUTIVA</strong></td>		
	</tr>
    <tr>
      <th class="vtabla" scope="row">N&uacute;mero de Acta </th>
      <td class="vtabla"><input name="num_acta" type="text" class="vinsert" id="num_acta" onKeyDown="if (event.keyCode == 13) ent_fed.select()" size="20"></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Notario</th>
      <td class="vtabla">
	  <select name="notario" class="insert">
	  <!-- START BLOCK : notario -->
	  <option value="{cod_notario}">{cod_notario} - {nombre_notario}</option>
	  <!-- END BLOCK : notario -->
      </select></td>
    </tr>
    <tr>
      <th class="vtabla">Entidad Federativa </th>
      <td class="vtabla"><input name="ent_fed" type="text" class="vinsert" id="ent_fed" onKeyDown="if (event.keyCode == 13) nombre.select()" size="30" maxlength="30"></td>
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