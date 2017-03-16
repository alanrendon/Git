<link href="../../styles/tablas.css" rel="stylesheet" type="text/css">
<link href="../../styles/pages.css" rel="stylesheet" type="text/css">
<link href="/styles/imp.css" rel="stylesheet" type="text/css">

<!-- START BLOCK : obtener_datos -->
<script language="JavaScript" type="text/JavaScript">
function valida(){
	if(document.form.anio.value =="" || document.form.anio.value < 2005){
		alert("Revise el año");
		document.form.anio.select();
	}
	else
		document.form.submit();
}
</script>

<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
<p class="title"> CONSULTA DE RECIBOS DE RENTA</p>

<form name="form" action="./ren_recibos_con.php" method="get">
<table class="tabla">
  <tr>
    <th class="tabla">Mes</th>
    <th class="tabla">A&ntilde;o</th>
  </tr>
  
  <tr>
    <td class="tabla">
	<select name="mes" class="insert" id="mes">
	<!-- START BLOCK : mes -->
      <option value="{mes}" {selected}>{nombre_mes}</option>
	<!-- END BLOCK : mes -->
    </select></td>
    <td class="tabla"><input name="anio" type="text" class="insert" id="anio" value="{anio_actual}" size="5" maxlength="4"></td>
  </tr>

</table>
<p>
<input type="button" class="boton" onClick="valida();" value="Enviar">
</p>
</form>

</td>
</tr>
</table>
<!-- END BLOCK : obtener_datos -->

<!-- START BLOCK : listado -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="top">
<p class="print2_encabezado"><strong>RECIBOS DE RENTA DE {mes} DEL {anio} <br>
LOCALES {bloque} </strong></p>
<table border="0" class="print2">
  <tr>
    <th class="print2">RECIBO</th>
    <th class="print2">RENTA</th>
    <th class="print2">AGUA</th>
    <th class="print2">MANTENIMIENTO</th>
    <th class="print2">I.V.A.</th>
    <th class="print2">I.S.R. RETENIDO </th>
    <th class="print2">I.V.A. RETENIDO </th>
    <th class="print2">NETO</th>
  </tr>
  <!-- START BLOCK : arrendador -->
  <tr>
	<th colspan="8" class="vprint2"><font size="3"><strong>{arrendador}</strong></font></th>  
  </tr>
  <!-- START BLOCK : recibo -->
  <tr>
    <td class="print2">&nbsp;</td>
    <td class="vprint2" colspan="3"><strong>{arrendatario}</strong></td>
    <td class="print2">&nbsp;</td>
    <td class="vprint2" colspan="3"><strong>{local}</strong></td>
  </tr>
  <tr>
    <td class="print2">{recibo}</td>
    <td class="print2">{renta}</td>
    <td class="print2">{agua}</td>
    <td class="print2">{mantenimiento}</td>
    <td class="print2">{iva}</td>
    <td class="print2">{isr_ret}</td>
    <td class="print2">{iva_ret}</td>
    <td class="print2">{neto}</td>
  </tr>
  <!-- END BLOCK : recibo -->
  <tr>
	<td colspan="7" class="rprint2" ><strong>TOTAL ARRENDADOR</strong></td>
	<td class="print2"><strong>{total_arrendador}</strong></td>
  </tr>
  <!-- END BLOCK : arrendador -->
  <tr>
    <td class="print2"><strong><font size="2">TOTAL <br>BLOQUE</font></strong></td>
    <td class="print2"><strong><font size="2">{total_renta}</font></strong></td>
    <td class="print2"><strong><font size="2">{total_agua}</font></strong></td>
    <td class="print2"><strong><font size="2">{total_mantenimiento}</font></strong></td>
    <td class="print2"><strong><font size="2">{total_iva}</font></strong></td>
    <td class="print2"><strong><font size="2">{total_isr_ret}</font></strong></td>
    <td class="print2"><strong><font size="2">{total_iva_ret}</font></strong></td>
    <td class="print2"><strong><font size="2">{total_neto}</font></strong></td>
  </tr>
</table></td>
</tr>
</table>
{salto_pagina}
<!-- END BLOCK : listado -->