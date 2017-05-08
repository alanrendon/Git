<link href="../../styles/tablas.css" rel="stylesheet" type="text/css">
<link href="../../styles/pages.css" rel="stylesheet" type="text/css">
<!-- START BLOCK : obtener_datos -->
<script language="JavaScript" type="text/JavaScript">

function valida_registro(){
	if(document.form.anio.value=="" || document.form.anio.value < 0){
		alert("Revise el año de consulta");
		document.form.anio.select();
	}
	else
		document.form.submit();
}

</script>

<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Captura de recibos de renta</p>
  <form name="form" action="./ren_recibos_cap.php" method="get">
  <input name="temp" type="hidden">
  
  <table class="tabla">
  <tr class="tabla">
    <th class="tabla">Mes</th>
    <th class="tabla">A&ntilde;o</th>
  </tr>
  <tr class="tabla">
    <td class="tabla"><select name="mes" class="insert" id="mes">
	<!-- START BLOCK : mes -->
      <option value="{mes}" {selected}>{nombre_mes}</option>
	<!-- END BLOCK : mes -->
    </select></td>
    <td class="tabla"><input name="anio" type="text" class="insert" id="anio" value="{anio_actual}" size="4" maxlength="4"></td>
  </tr>
</table>
    <p>
    <input name="enviar" type="button" class="boton" id="enviar" onClick="valida_registro();" value="Siguiente">
  </p>
</form>  
<script language="JavaScript" type="text/JavaScript">
window.onload=document.form.anio.select();
</script>
  
</td>
</tr>
</table>
<!-- END BLOCK : obtener_datos -->

<!-- START BLOCK : captura -->

<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Captura de recibos de renta <br>
{nombre_mes} del {anio}</p>
<form name="form" action="./ren_recibos_cap1.php" method="post">
<input type="hidden" name="mes" value="{mes}">
<input type="hidden" name="anio" value="{anio}">
<input type="hidden" name="contador" value="{cont}">
<input name="temp" type="hidden" id="temp">
<table class="tabla">
  <tr class="tabla">
    <th colspan="2" class="tabla">ARRENDADOR</th>
    <th class="tabla">FOLIO DE INICIO </th>
  </tr>
  <!-- START BLOCK : arrendadores -->
  <tr class="tabla">
    <td class="vtabla">{cod_arrendador}</td>
    <td class="vtabla">{nombre_arrendador}<input name="cod_arrendador{i}" type="hidden" id="cod_arrendador{i}" value="{cod_arrendador}"></td>
    <td class="tabla"><input name="folios{i}" type="text" class="insert" id="folios{i}" value="{folio}" size="10" onKeyDown="if(event.keyCode==13) form.folios{next}.select();" onFocus="form.temp.value=this.value" onChange="valor=isInt(this,form.temp); if (valor==false) this.value='';"></td>
  </tr>
  <!-- END BLOCK : arrendadores -->
</table>




	<p>
	<input name="regresar" type="button" value="Regresar" onClick="document.location='./ren_recibos_cap.php'" class="boton">&nbsp;&nbsp;
	<input name="enviar" type="button" value="Siguiente" class="boton" onClick="document.form.submit();">
	</p>
</form>
<script language="JavaScript" type="text/JavaScript">
window.onload=document.form.folios0.select();
</script>

</td>
</tr>
</table>
<!-- END BLOCK : captura -->


