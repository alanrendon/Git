<link href="/styles/tablas.css" rel="stylesheet" type="text/css">

<!-- START BLOCK : obtener_datos -->
<script language="JavaScript" type="text/JavaScript">
function valida()
{
/*
if(document.form.cia.value=="" || document.form.cia.value < 0){
	alert("Por favor revise la compañía");
	document.form.cia.select();
}
else if(document.form.proveedor.value=="" || document.form.proveedor.value < 0){
	alert("Por favor revise el proveedor");
	document.form.proveedor.select();
}
*/
if(document.form.anio.value=="" || document.form.anio.value < 0){
	alert("Revise el año");
	document.form.anio.select();
}
else 
	document.form.submit();
}
</script>

<link href="/styles/impresion.css" rel="stylesheet" type="text/css">
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">

<p class="title">CONSULTA DE FACTURAS</p>
	<form action="./fac_conta_con.php" method="get" name="form">
	<input name="temp" type="hidden">
	<table border="1" class="tabla">
	  <tr class="tabla">
		<th scope="row" colspan="2" class="tabla"> Mes 
		  <select name="mes" size="1" class="insert">
        <!-- START BLOCK : mes -->
	    <option value="{num_mes}" {checked}>{nom_mes}</option>
        <!-- END BLOCK : mes -->
		</select>
		  del 
		  <input name="anio" type="text" class="insert" id="cia3" value="{anio_actual}" size="5">
		 </th>
	  </tr>
	  <tr class="tabla">
	    <td scope="row" colspan="2" class="tabla">Tipo de Proveedor </td>
	    </tr>
	  <tr class="tabla">
	    <td scope="row" colspan="2" class="vtabla"><p>
	      <label>
	      <input type="radio" name="tipo_prov" value="0">
  Avío</label>
	      <br>
	      <label>
	      <input type="radio" name="tipo_prov" value="2">
  Empaque</label>
	      <br>
	      <label>
	      <input type="radio" name="tipo_prov" value="1">
  Varios</label>
	      <br>
	      <label>
	      <input name="tipo_prov" type="radio" value="3" checked>
  Todos</label>
	      <br>
	      </p></td>
	    </tr>
	  <tr class="tabla">
	    <td scope="row" colspan="2" class="tabla">Compa&ntilde;&iacute;a
	      <input name="cia" type="text" id="cia" size="5" maxlength="3" class="insert" onFocus="form.temp.value=this.value" onChange="valor=isInt(this,form.temp); if (valor==false) this.select();"></td>
	    </tr>
		
	</table>
	<p>
	<input type="button" name="enviar" class="boton" value="Continuar" onclick='valida()'>
	</p>
	</form>
	<script language="JavaScript" type="text/JavaScript">window.onload=document.form.cia.select();</script>
</td>
</tr>
</table>
	<script language="JavaScript" type="text/JavaScript">window.onload=form.cia.select()</script>
<!-- END BLOCK : obtener_datos -->


<!-- START BLOCK : listado -->
<!-- START BLOCK : compania -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="top">
<p class="encabezado">OFICINAS ADMINISTRATIVAS MOLLENDO S. DE R.L. DE C.V.<br>
{num_cia}&nbsp;{nombre_cia}<br>
FACTURAS DE PROVEEDORES {tipo_prov} DE {mes} DEL {anio}
</p>
<table class="print">
  <tr class="print">
    <th class="print"><strong><font size="2">Fecha</font></strong></th>
    <th class="print"><strong><font size="2">Factura</font></strong></th>
    <th class="print"><strong><font size="2">Sub-Total</font></strong></th>
    <th class="print"><strong><font size="2">I.V.A.</font></strong></th>
    <th class="print"><strong><font size="2">I.V.A. Retenido</font></strong> </th>
    <th class="print"><strong><font size="2">I.E.P.S.</font></strong></th>
    <th class="print"><strong><font size="2">I.S.R. Retenido</font></strong> </th>
    <th class="print"><strong><font size="2">Cheque</font></strong></th>
    <th class="print"><strong><font size="2">Fecha conciliación</font></strong></th>
    <th class="print"><strong><font size="2">Total</font></strong></th>
  </tr>
<!-- START BLOCK : rows -->  
<!-- START BLOCK : proveedor -->
  <tr>
    <th colspan="10" class="vprint"><strong>{nom_proveedor}&nbsp;R.F.C.:{rfc}</strong></th>
  </tr>
<!-- END BLOCK : proveedor -->
  <tr class="print">
    <td class="print">{fecha}</td>
    <td class="print">{factura}</td>
    <td class="rprint">{sub_total}</td>
    <td class="rprint">{iva}</td>
    <td class="rprint">{iva_ret}</td>
    <td class="rprint">{ieps}</td>
    <td class="rprint">{isr_ret}</td>
    <td class="rprint">{num_cheque}</td>
    <td class="rprint">{fecha_con}</td>
    <td class="rprint">{total}</td>
  </tr>

<!-- START BLOCK : total_proveedor -->
  <tr class="print">
    <td class="print" colspan="2">Total Proveedor</td>
    <td class="rprint">{proveedor_sub_total}</td>
    <td class="rprint">{proveedor_iva}</td>
    <td class="rprint">{proveedor_iva_ret}</td>
    <td class="rprint"></td>
    <td class="rprint">{proveedor_isr_ret}</td>
    <td class="rprint"></td>
    <td class="rprint"></td>
    <td class="rprint">{proveedor_total}</td>
  </tr>
<!-- END BLOCK : total_proveedor -->
<!-- END BLOCK : rows --> 

<!-- START BLOCK : total_compania -->  
  <tr class="print">
    <td class="print" colspan="2">Total compañía</td>
    <td class="rprint">&nbsp;</td>
    <td class="rprint">&nbsp;</td>
    <td class="rprint">&nbsp;</td>
    <td class="rprint">&nbsp;</td>
    <td class="rprint">&nbsp;</td>
    <td class="rprint">&nbsp;</td>
    <td class="rprint">&nbsp;</td>
    <td class="rprint">&nbsp;</td>
  </tr>
<!-- END BLOCK : total_compania -->  
</table>
</td>
</tr>
</table>
<!-- END BLOCK : compania -->

<!-- END BLOCK : listado -->
