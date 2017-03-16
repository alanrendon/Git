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

<form name="form" action="./ren_recibos_con2.php" method="get">
<input type="hidden" name="temp">
<table class="tabla">
  <tr>
  <th class="tabla">MES</th>
    <td class="tabla">	<select name="mes" class="insert" id="mes">
	<!-- START BLOCK : mes -->
      <option value="{mes}" {selected}>{nombre_mes}</option>
	<!-- END BLOCK : mes -->
    </select></td>
	<th class="tabla">AÑO</th>
    <td class="tabla"><input name="anio" type="text" class="insert" id="anio" value="{anio_actual}" size="5" maxlength="4"></td>
  </tr>

  <tr>
    <td colspan="4" class="tabla"><strong>Tipo de consulta: </strong></td>
  </tr>
  <tr>
    <td class="vtabla" colspan="4">
	  <p>
	    <label>
	    <input type="radio" name="tipo_con" value="0" onChange="form.arrendatario.style.visibility='hidden'; form.recibo.style.visibility='hidden';form.arrendador.style.visibility='visible'; form.arrendador.select();">
  Por arrendador</label>&nbsp;
  		<input name="arrendador" type="text" size="5" class="insert" style="visibility:hidden " onFocus="form.temp.value=this.value" onChange="valor=isInt(this,form.temp); if (valor==false) this.value='';" onKeyDown="if(event.keyCode==13) form.anio.select();">
	    <br>
	    <label>
	    <input type="radio" name="tipo_con" value="1" onChange="form.arrendador.style.visibility='hidden'; form.recibo.style.visibility='hidden'; form.arrendatario.style.visibility='visible'; form.arrendatario.select();">
  Por arrendatario</label>&nbsp;
		<input name="arrendatario" type="text" class="insert" id="arrendatario" size="5" style="visibility:hidden" onFocus="form.temp.value=this.value" onChange="valor=isInt(this,form.temp); if (valor==false) this.value='';" onKeyDown="if(event.keyCode==13) form.anio.select();">
		<br>
	    <label>
	    <input type="radio" name="tipo_con" value="2" onChange="form.arrendador.style.visibility='hidden'; form.arrendatario.style.visibility='hidden'; form.recibo.style.visibility='visible'; form.recibo.select();">
  Por recibo</label>&nbsp;
  		<input name="recibo" type="text" class="insert" size="5" style="visibility:hidden " onFocus="form.temp.value=this.value" onChange="valor=isInt(this,form.temp); if (valor==false) this.value='';" onKeyDown="if(event.keyCode==13) form.anio.select();"> 
		<br>
	    <label>
	    <input name="tipo_con" type="radio" value="3" checked onChange="form.arrendador.style.visibility='hidden'; form.arrendatario.style.visibility='hidden'; form.recibo.style.visibility='hidden'">
  TODO</label>
		
	    </p>
	</td>
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
	<th colspan="8" class="vprint2"><font size="3"><strong>{num_arrendador} {arrendador}</strong></font></th>  
  </tr>
  <!-- START BLOCK : recibo -->
  <tr>
    <td class="print2">&nbsp;</td>
    <td class="vprint2" colspan="3"><strong>{num_arrendatario} {arrendatario}</strong></td>
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
  <!-- START BLOCK : concepto_todos -->
  <tr>
	<td class="print2"></td>
	<td class="vprint2" colspan="7">{concepto}</td>	  
  </tr>
  <!-- END BLOCK : concepto_todos -->
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

<!-- START BLOCK : list_arrendador -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="top">
<p class="print2_encabezado"><strong>RECIBOS DE RENTA DE {mes} DEL {anio} <br>
 <font size="+2">{num_arrendador} {arrendador}</font> </strong></p>

<table class="print2">
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
  <!-- START BLOCK : recibo2 -->
  <tr>
    <td class="vprint2">{tipo_local}</td>
    <td class="vprint2" colspan="3"><strong>{num_arrendatario} {arrendatario}</strong></td>
    <td class="print2"></td>
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
  <!-- START BLOCK : concepto_arrendador -->
  <tr>
	<td class="print2"></td>
	<td class="vprint2" colspan="7">{concepto}</td>	  
  </tr>
  <!-- END BLOCK : concepto_arrendador -->
  
  <!-- END BLOCK : recibo2 -->
  <tr>
    <td class="print2"><strong><font size="2">TOTAL</font></strong></td>
    <td class="print2"><strong><font size="2">{total_renta}</font></strong></td>
    <td class="print2"><strong><font size="2">{total_agua}</font></strong></td>
    <td class="print2"><strong><font size="2">{total_mantenimiento}</font></strong></td>
    <td class="print2"><strong><font size="2">{total_iva}</font></strong></td>
    <td class="print2"><strong><font size="2">{total_isr_ret}</font></strong></td>
    <td class="print2"><strong><font size="2">{total_iva_ret}</font></strong></td>
    <td class="print2"><strong><font size="2">{total_neto}</font></strong></td>
  </tr>
</table>
</td>
</tr>
</table>

<!-- END BLOCK : list_arrendador -->

<!-- START BLOCK : list_arrendatario -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="top">
<p class="print2_encabezado"><strong>RECIBOS DE RENTA <br>
 <font size="+1">{num_arrendatario} {arrendatario}</font> </strong></p>
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
  <!-- START BLOCK : bloque_arrendador -->
  <tr>
	<th colspan="8" class="vprint2"><font size="3"><strong>{num_arrendador} {arrendador}</strong></font></th>  
  </tr>
  <!-- START BLOCK : recibo_arrendatario -->
  <tr>
    <td class="print2">&nbsp;</td>
    <td class="vprint2" colspan="3"><strong>{local}</strong></td>
    <td class="print2">{fecha}</td>
    <td class="vprint2" colspan="3"><strong>{tipo_local}</strong></td>
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
  <!-- START BLOCK : concepto_arrendatario -->
  <tr>
	<td class="print2"></td>
	<td class="vprint2" colspan="7">{concepto}</td>	  
  </tr>
  <!-- END BLOCK : concepto_arrendatario -->
  <!-- END BLOCK : recibo_arrendatario -->
  <!-- END BLOCK : bloque_arrendador -->
  
</table>


</td>
</tr>
</table>

<!-- END BLOCK : list_arrendatario -->

<!-- START BLOCK : list_recibo -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="top">
<p class="print2_encabezado"><strong>RECIBOS DE RENTA <br>
 <font size="+1">RECIBO NUMERO {num_recibo}</font> </strong></p>

<table border="0" class="print2">
  <tr>
    <th class="print2">LOCAL</th>
    <th class="print2">RENTA</th>
    <th class="print2">AGUA</th>
    <th class="print2">MANTENIMIENTO</th>
    <th class="print2">I.V.A.</th>
    <th class="print2">I.S.R. RETENIDO </th>
    <th class="print2">I.V.A. RETENIDO </th>
    <th class="print2">NETO</th>
  </tr>
  <!-- START BLOCK : arrendador3 -->
  <tr>
	<th colspan="8" class="vprint2"><font size="3"><strong>{num_arrendador} {arrendador}</strong></font></th>  
  </tr>
  <tr>
    <td class="print2">{fecha}</td>
    <td class="vprint2" colspan="3"><strong>{num_arrendatario} {arrendatario}</strong></td>
    <td class="print2">&nbsp;</td>
    <td class="vprint2" colspan="3"><strong>{local}</strong></td>
  </tr>
  <tr>
    <td class="print2">{tipo_local}</td>
    <td class="print2">{renta}</td>
    <td class="print2">{agua}</td>
    <td class="print2">{mantenimiento}</td>
    <td class="print2">{iva}</td>
    <td class="print2">{isr_ret}</td>
    <td class="print2">{iva_ret}</td>
    <td class="print2">{neto}</td>
  </tr>
  <!-- START BLOCK : concepto_recibo -->
  <tr>
	<td class="print2"></td>
	<td class="vprint2" colspan="7">{concepto}</td>	  
  </tr>
  <!-- END BLOCK : concepto_recibo -->
  
  <!-- END BLOCK : arrendador3 -->
</table>

</td>
</tr>
</table>
<!-- END BLOCK : list_recibo -->