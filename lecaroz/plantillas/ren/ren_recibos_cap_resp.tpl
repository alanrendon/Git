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
    <td class="tabla"><input name="anio" type="text" class="insert" id="anio" value="{anio_actual}" size="5"></td>
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
  <input name="mes" type="hidden" value="{mes}">
  <input name="anio" type="hidden" value="{anio}">
  <input name="contador" type="hidden" value="{cont}">
  <input name="temp" type="hidden">
  
<table class="tabla">
  <tr>
    <th class="tabla">COMPA&Ntilde;&Iacute;A</th>
    <th class="tabla">ARRENDATARIO</th>
    <th class="tabla">RECIBO</th>
  </tr>
  <!-- START BLOCK : bloque -->
  <tr>
	<th class="tabla" colspan="3">{bloque}</th>  
  </tr>
  <!-- START BLOCK : arrendatarios -->
  <tr>
    <td class="vtabla">{nombre_cia}</td>
    <td class="vtabla">{nombre_arrendatario}
	<input name="arrendatario{i}" type="hidden" value="{arrendatario}">
	<input name="nombre_arrendatario{i}" type="hidden" value="{nombre_arrendatario}">
	<input name="nombre_cia{i}" type="hidden" value="{nombre_cia}">
	<input name="local{i}" type="hidden" value="{local}">
	<input name="bloque{i}" type="hidden" value="{bloque1}">
	<input name="arrendador{i}" type="hidden" value="{arrendador}">
	
	</td>
    <td class="tabla"><input name="recibo{i}" type="text" class="vinsert" id="recibo{i}" value="{recibo}" size="10" onChange="valor=isInt(this,form.temp); if (valor==false) this.select();" onKeyDown="if(event.keyCode==13) document.form.recibo{next}.select();"></td>
  </tr>
  <!-- END BLOCK : arrendatarios -->
  <!-- END BLOCK : bloque -->
</table>

<p>
<input name="regresar" type="button" value="Regresar" onClick="document.location='./ren_recibos_cap.php'" class="boton">&nbsp;&nbsp;
<input name="enviar" type="button" value="Siguiente" class="boton" onClick="document.form.submit();">
</p>
</form>
<script language="JavaScript" type="text/JavaScript">
window.onload=document.form.recibo0.select();
</script>

</td>
</tr>
</table>
<!-- END BLOCK : captura -->


