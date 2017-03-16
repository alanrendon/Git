<link href="../../styles/tablas.css" rel="stylesheet" type="text/css">
<link href="../../styles/pages.css" rel="stylesheet" type="text/css">
<!-- START BLOCK : obtener_datos -->

<script language="JavaScript" type="text/JavaScript">
function valida_registro(){
	if(document.form.tipo_con1.value==0 && document.form.cod_arrendador.value == ""){
		alert("Revise el Arrendador");
		document.form.tipo_con1.select();
		return;
	}
	else
		document.form.submit();
		
}
</script>

<link href="/styles/impresion.css" rel="stylesheet" type="text/css">
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Consulta  de Arrendadores</p>

<form name="form" action="./ren_arrendador_con.php" method="get">
<input name="tipo_con1" type="hidden" value="1">
<input name="temp" type="hidden">
<table class="tabla">
  <tr class="tabla">
    <th class="tabla" colspan="2">Consultar por</th>
  </tr>
  <tr class="tabla">
    <td class="tabla">
        <label>
        <input type="radio" name="tipo_con" value="0" onChange="document.form.tipo_con1.value=0">
  Arrendador</label> <input name="cod_arrendador" type="text" size="5" maxlength="3" class="insert">
	</td>
    <td class="tabla">        <label>
        <input name="tipo_con" type="radio" value="1" checked onChange="document.form.tipo_con1.value=1">
  Todos los arrendadores</label>
</td>
    </tr>
</table>
<p>
<input type="button" class="boton" name="enviar" value="Enviar" onClick="valida_registro();">
</p>
</form>
</td>
</tr>
</table>



<!-- END BLOCK : obtener_datos -->

<!-- START BLOCK : por_arrendador -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Consulta  de Arrendadores</p>
  <table class="tabla">
    <tr>
      <th class="vtabla" scope="row">C&oacute;digo</th>
      <td class="vtabla">{id}</td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Nombre</th>
      <td class="vtabla">{nombre}</td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Representado por </th>
      <td class="vtabla">{representante}</td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Tipo</th>
      <td class="vtabla">{tipo}</td>
    </tr>
	<tr>
		<td colspan="2" class="tabla"><strong>DATOS DEL ACTA CONSTITUTIVA</strong></td>		
	</tr>
    <tr>
      <th class="vtabla" scope="row">N&uacute;mero de Acta </th>
      <td class="vtabla">{acta}</td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Notario</th>
      <td class="vtabla">{notario}	  </td>
    </tr>
    <tr>
      <th class="vtabla">Entidad Federativa </th>
      <td class="vtabla">{entidad}</td>
    </tr>
  </table>  
  <p>
  <input type="button" name="regresar" value="Regresar" class="boton" onClick="document.location = './ren_arrendador_con.php'">
  </p>
</td>
</tr>
</table>
<!-- END BLOCK : por_arrendador -->

<!-- START BLOCK : todos -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="top">
<p class="print_encabezado"><strong>CONSULTA DE ARRENDADORES </strong></p>

<table class="print">
  <tr>
    <th class="print" colspan="2">ARRENDADOR</th>
    <th class="print">REPRESENTANTE</th>
    <th class="print">TIPO PERSONA </th>
    <th class="print">NUMERO DE ACTA</th>
    <th class="print">NOTARIO</th>
    <th class="print">ENTIDAD FEDERATIVA </th>
  </tr>
  <!-- START BLOCK : arrendadores -->
  <tr>
    <th class="print">{id}</th>
    <td class="vprint">{nombre}</td>
    <td class="vprint">{representante}</td>
    <td class="print">{tipo_persona}</td>
    <td class="print">{acta}</td>
    <td class="vprint">{notario}</td>
    <td class="print">{entidad}</td>
  </tr>
  <!-- END BLOCK : arrendadores -->
</table>


</td>
</tr>
</table>
<!-- END BLOCK : todos -->


