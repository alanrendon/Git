<link href="../../styles/tablas.css" rel="stylesheet" type="text/css">
<link href="../../styles/pages.css" rel="stylesheet" type="text/css">

<!-- START BLOCK : revision -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Recibos de renta <br>
{nombre_mes} del {anio}</p>

  <form name="form" action="./ren_recibos_cap1.php" method="post">
  <input name="mes" type="hidden" value="{mes}">
  <input name="anio" type="hidden" value="{anio}">
  <input name="registros" type="hidden" id="registros" value="{cont}">
  
<table class="tabla">
  <tr>
    <th class="tabla">COMPA&Ntilde;&Iacute;A</th>
    <th class="tabla">ARRENDATARIO</th>
    <th class="tabla">RECIBO</th>
    <th class="tabla">RENTA</th>
    <th class="tabla">AGUA</th>
    <th class="tabla">MANTENIMIENTO</th>
    <th class="tabla">I.V.A.</th>
    <th class="tabla">I.S.R. RET.</th>
    <th class="tabla">I.V.A. RET. </th>
    <th class="tabla">NETO</th>
  </tr>
  <!-- START BLOCK : bloque -->
  <tr>
	<th class="tabla" colspan="10">{bloque}</th>  
  </tr>
  <!-- START BLOCK : arrendatarios -->
  <tr>
    <td class="vtabla">{nombre_cia}</td>
    <td class="vtabla"><font color="#{color}">{nombre_arrendatario}</font>
      <input name="arrendatario{i}" type="hidden" value="{arrendatario}">
	  <input name="arrendador{i}" type="hidden" value="{arrendador}">
	  <input name="bloque{i}" type="hidden" id="bloque{i}" value="{bloque1}">
	</td>
    <td class="tabla"><input name="recibo{i}" type="hidden" class="vinsert" id="recibo{i}" value="{recibo}" size="10">
    <font color="#{color}">{recibo}</font></td>
    <td class="tabla"><input name="renta{i}" type="hidden" class="vinsert" id="renta{i}" value="{renta}" size="10">
    {renta1}</td>
    <td class="tabla"><input name="agua{i}" type="hidden" class="vinsert" id="agua{i}" value="{agua}" size="10">
    {agua1}</td>
    <td class="tabla"><input name="mantenimiento{i}" type="hidden" class="vinsert" id="mantenimiento{i}" value="{mantenimiento}" size="10">
    {mantenimiento1}</td>
    <td class="tabla"><input name="iva{i}" type="hidden" class="vinsert" id="iva{i}" value="{iva}" size="10">
	{iva1}</td>
    <td class="tabla"><input name="isr_ret{i}" type="hidden" class="vinsert" id="isr_ret{i}" value="{isr_ret}" size="10">
    {isr_ret1}</td>
    <td class="tabla"><input name="iva_ret{i}" type="hidden" class="vinsert" id="iva_ret{i}" value="{iva_ret}" size="10">
    {iva_ret1}</td>
    <td class="tabla"><input name="neto{i}" type="hidden" class="vinsert" id="neto{i}" value="{neto}" size="10">
    {neto1}</td>

  </tr>
  <!-- END BLOCK : arrendatarios -->
  <!-- END BLOCK : bloque -->
</table>

<p>
<input name="regresar" type="button" value="Regresar" onClick="document.location='./ren_recibos_cap.php?mes={mes}&anio={anio}'" class="boton">&nbsp;&nbsp;
<input name="enviar" type="button" value="Siguiente" class="boton" {disabled} onClick="document.form.submit();">
</p>
</form>
<script language="JavaScript" type="text/JavaScript">
window.onload=document.form.recibo0.select();
</script>

</td>
</tr>
</table>
<!-- END BLOCK : revision -->


