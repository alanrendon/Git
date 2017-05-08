<link href="../../styles/tablas.css" rel="stylesheet" type="text/css">
<link href="../../styles/pages.css" rel="stylesheet" type="text/css">
<!-- START BLOCK : obtener_datos -->

<script language="JavaScript" type="text/JavaScript">
function valida_registro(){
	if(document.form.tipo_con1.value==0 && document.form.num_local.value == ""){
		alert("Revise el Local");l
		document.form.num_local.select();
		return;
	}
	else
		document.form.submit();
		
}
</script>
<link href="/styles/impresion.css" rel="stylesheet" type="text/css">
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Consulta  de Locales 
  
</p>

<form name="form" action="./ren_local_con.php" method="get">
<input name="tipo_con1" type="hidden" value="1">
  <table class="tabla">
    <tr class="tabla">
      <th class="tabla" colspan="2">Consultar por</th>
    </tr>
    <tr class="tabla">
      <td class="vtabla">
        <label>
        <input type="radio" name="tipo_con" value="0" onChange="document.form.tipo_con1.value=0; num_local.style.visibility='visible'; num_arrendador.style.visibility='hidden'; num_cia.style.visibility='hidden'">
      Local</label>
        <input name="num_local" type="text" class="insert" id="num_local" size="3" maxlength="3" style="visibility: hidden ">
        <br>
		<label>
		<input type="radio" name="tipo_con" value="1" onChange="form.tipo_con1.value=1; num_local.style.visibility='hidden'; num_arrendador.style.visibility='visible'; num_cia.style.visibility='hidden';">
		Arrendador</label>
		<input name="num_arrendador" type="text" class="insert" size="3" maxlength="3" style="visibility:hidden " onChange="form.tipo_con1.value=2; ">
		<br>
		<label>
		<input type="radio" name="tipo_con" value="2" onChange="form.tipo_con1.value=2; num_local.style.visibility='hidden'; num_arrendador.style.visibility='hidden'; num_cia.style.visibility='visible';">
		Compañía a cargo</label>

		<input name="num_cia" type="text" size="3" maxlength="3" class="insert" style="visibility:hidden ">
		
      </td>
	 </tr>
	 <tr>
      <td class="tabla">
        <label>
        <input name="tipo_con" type="radio" value="3" checked onChange="form.tipo_con1.value=3; num_local.style.visibility='hidden'; num_arrendador.style.visibility='hidden'; num_cia.style.visibility='hidden';">
      <strong>Todos los locales</strong> </label>
      </td>
    </tr>
  </table>
  <p>
  <input name="enviar" onClick="valida_registro();" value="Enviar" class="boton" type="button">
  </p>
</form>  
</td>
</tr>
</table>

<!-- END BLOCK : obtener_datos -->

<!-- START BLOCK : por_local -->

<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Consulta  de Locales </p>
  <table class="tabla">
    <tr>
      <th class="vtabla" scope="row">N&uacute;mero de local </th>
      <td class="vtabla">{id}</td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Nombre</th>
      <td class="vtabla">{nombre_local}</td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Cuenta de predial </th>
      <td class="vtabla">{predial}</td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Metros</th>
      <td class="vtabla">{metros}</td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Metros cuadrados </th>
      <td class="vtabla">{m2}</td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Compa&ntilde;&iacute;a a cargo </th>
      <td class="vtabla">{num_cia} {nombre_cia}        </td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Arrendador</th>
      <td class="vtabla">{cod_arrendador} {nombre_arrendador}           </td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Direccion</th>
      <td class="vtabla">{direccion}</td>
    </tr>
	
    <tr>
      <th class="vtabla" scope="row">Bloque</th>
      <td class="vtabla">{bloque}</td>
    </tr>
  </table>  
  <p>
  <input class="boton" type="button" name="regresar" value="Regresar" onClick="document.location = './ren_local_con.php'">
  </p>
</td>
</tr>
</table>
<!-- END BLOCK : por_local -->

<!-- START BLOCK : todos -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="top">
<p class="print_encabezado"><strong>CONSULTA DE LOCALES</strong> </p>

<table class="print">
  <tr>
    <th class="print" colspan="2">LOCAL</th>
    <th class="print" colspan="2">COMPA&Ntilde;&Iacute;A</th>
    <th class="print" colspan="2">ARRENDADOR</th>
    <th class="print">DIRECCION</th>
    <th class="print">METROS</th>
    <th class="print">METROS CUADRADOS</th>
    <th class="print">CUENTA PREDIAL</th>
    <th class="print">BLOQUE</th>
  </tr>
  <!-- START BLOCK : locales -->
  <tr>
    <td class="print">{id}</td>
    <td class="vprint">{nombre_local}</td>
	<td class="print">{num_cia}</td>
    <td class="vprint">{nombre_cia}</td>
	<td class="print">{cod_arrendador}</td>
    <td class="vprint">{nombre_arrendador} </td>
    <td class="vprint">{direccion}</td>
    <td class="print">{metros}</td>
    <td class="print">{m2}</td>
    <td class="print">{predial}</td>
    <td class="print">{bloque}</td>
  </tr>
  <!-- END BLOCK : locales -->
</table>



</td>
</tr>
</table>

<!-- END BLOCK : todos -->

<!-- START BLOCK : por_cia -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="top">
<p class="print_encabezado"><strong>LOCALES A CARGO DE LA COMPAÑÍA<br> {num_cia} {nombre_cia}</strong><br>{direccion}</p>
<table class="print">
  <tr>
    <th class="print" colspan="2">LOCAL</th>
    <th class="print" colspan="2">ARRENDADOR</th>
    <th class="print">METROS</th>
    <th class="print">METROS CUADRADOS</th>
    <th class="print">CUENTA PREDIAL</th>
    <th class="print">BLOQUE</th>
  </tr>
  <!-- START BLOCK : locales_cia -->
  <tr>
    <td class="print">{id}</td>
    <td class="vprint">{nombre_local}</td>
    <td class="print">{cod_arrendador}</td>
    <td class="vprint">{nombre_arrendador} </td>
    <td class="print">{metros}</td>
    <td class="print">{m2}</td>
    <td class="print">{predial}</td>
    <td class="print">{bloque}</td>
  </tr>
  <!-- END BLOCK : locales_cia -->
</table><p class="print_encabezado">&nbsp;</p></td>
</tr>
</table>
<!-- END BLOCK : por_cia -->

<!-- START BLOCK : por_arrendador -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="top">
<p class="print_encabezado"><strong>LOCALES A CARGO DEL ARRENDADOR <br> {num_arrendador} - {nombre_arrendador}</strong></p>

<table class="print">
  <tr>
    <th class="print" colspan="2">LOCAL</th>
    <th class="print" colspan="2">COMPA&Ntilde;&Iacute;A</th>
    <th class="print">DIRECCION</th>
    <th class="print">METROS</th>
    <th class="print">METROS CUADRADOS</th>
    <th class="print">CUENTA PREDIAL</th>
    <th class="print">BLOQUE</th>
  </tr>
  <!-- START BLOCK : locales_arrendador -->
  <tr>
    <td class="print">{id}</td>
    <td class="vprint">{nombre_local}</td>
    <td class="print">{num_cia}</td>
    <td class="vprint">{nombre_cia}</td>
    <td class="vprint">{direccion}</td>
    <td class="print">{metros}</td>
    <td class="print">{m2}</td>
    <td class="print">{predial}</td>
    <td class="print">{bloque}</td>
  </tr>
  <!-- END BLOCK : locales_arrendador -->
</table>

</td>
</tr>
</table>
<!-- END BLOCK : por_arrendador -->
