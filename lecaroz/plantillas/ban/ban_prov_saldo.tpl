<link href="/styles/impresion.css" rel="stylesheet" type="text/css">
<link href="/styles/tablas.css" rel="stylesheet" type="text/css">

<!-- START BLOCK : obtener_datos -->
<script language="JavaScript" type="text/JavaScript">
function valida()
{
	document.form.submit();
}
</script>

<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">

<p class="title">Saldo de Proveedores</p>
<form action="./ban_prov_saldo.php" method="get" name="form">
<table class="tabla">
  <tr class="tabla">
    <th class="tabla"colspan="2">REVISAR POR</th>
  </tr>

  <tr class="tabla">
    <td class="vtabla">
      <label><input name="tipo_con" type="radio" onChange="document.form.temp.value=0" value="0" checked>
      Por proveedor &nbsp;</label>
      <input name="proveedor" type="text" class="insert" id="proveedor" onKeyDown="if(event.keyCode==13) form.enviar.focus();" size="5">      <br>
    </td>
    <td class="vtabla">	      <label><input type="radio" name="tipo_con" value="1" onChange="document.form.temp.value=2">
      Por compa&ntilde;&iacute;a </label> 
          <input name="cia" type="text" class="insert" id="cia" size="5" onKeyDown="if(event.keyCode==13) form.enviar.focus();"><br>          <label></label>          	</td>
  </tr>
</table>
<p>
  <input type="button" name="enviar" value="Listado" onClick="valida();" class="boton">
</p>
</form>
<script language="JavaScript" type="text/JavaScript">
window.onload=document.form.fecha_inicial.select();
</script>

</td>
</tr>
</table>
<!-- END BLOCK : obtener_datos -->

<!-- START BLOCK : proveedor -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="top">

<table width="80%" class="print">
  <tr class="print">
    <th colspan="6" class="print"><strong>{num_proveedor}&nbsp;{nom_proveedor}</strong></th>
  </tr>
  <!-- START BLOCK : cia -->
  <tr class="print">
    <th class="vprint" colspan="6">{num_cia}&nbsp;{nombre_cia}</th>
  </tr>
  <tr class="print">
    <th  class="print" width="20%">N&uacute;mero de factura </th>
    <th class="print" width="30%">Descripci&oacute;n</th>
    <th class="print" width="10%">Fecha movimiento </th>
    <th class="print" width="10%">Fecha vencimiento </th>
    <th class="print" width="10%">Gasto</th>
    <th class="print" width="20%">Importe</th>
  </tr>
  <!-- START BLOCK : rows -->
  <tr class="print">
    <td class="print" width="20%">{num_fact}</td>
    <td class="vprint" width="30%">{descripcion}</td>
    <td class="print" width="10%">{fecha_mov}</td>
    <td class="print" width="10%">{fecha_pago}</td>
    <td class="print" width="10%">{codgastos}</td>
    <td class="rprint" width="20%">{importe}</td>
  </tr>
  <!-- END BLOCK : rows -->
  
  <!-- START BLOCK : total_cia -->
  <tr class="print">
  	<td class="print" colspan="5"> Total compañía</td>
	<td class="rprint"><strong> {total_cia}</strong></td>
  </tr>
  <!-- END BLOCK : total_cia -->
  
  
  <!-- END BLOCK : cia -->
  <tr class="print">
    <th class="print" colspan="5">TOTAL PROVEEDOR </th>
    <th class="print">{total_proveedor}</th>
  </tr>
</table>
</td>
</tr>
</table>
<br style="page-break-after:always;">
<!-- END BLOCK : proveedor -->


<!-- START BLOCK : compania -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="top">

<table width="80%" class="print">
  <tr class="print">
    <th colspan="6" class="print"><strong>{num_cia}&nbsp;{nom_compania}</strong></th>
  </tr>
  <!-- START BLOCK : prov -->
  <tr class="print">
    <th class="vprint" colspan="6">{num_proveedor}&nbsp;{nombre_proveedor}</th>
  </tr>
  <tr class="print">
    <th class="print" width="20%">N&uacute;mero de factura </th>
    <th class="print" width="30%">Descripci&oacute;n</th>
    <th class="print" width="10%">Fecha movimiento </th>
    <th class="print" width="10%">Fecha vencimiento </th>
    <th class="print" width="10%">Gasto</th>
    <th class="print" width="20$">Importe</th>
  </tr>
  <!-- START BLOCK : rows1 -->
  <tr class="print">
    <td class="print" width="20%">{num_fact}</td>
    <td class="vprint" width="30%">{descripcion}</td>
    <td class="print" width="10%">{fecha_mov}</td>
    <td class="print" width="10%">{fecha_pago}</td>
    <td class="print" width="10%">{codgastos}</td>
    <td class="rprint" width="20%">{importe}</td>
  </tr>
  <!-- END BLOCK : rows1 -->
  
  <!-- START BLOCK : total_prov -->
  <tr class="print">
  	<td class="print" colspan="5"> Total proveedor </td>
	<td class="rprint"><strong> {total_proveedor}</strong></td>
  </tr>
  <!-- END BLOCK : total_prov -->
  
  
  <!-- END BLOCK : prov -->
  <tr class="print">
    <th class="print" colspan="5">TOTAL COMPA&Ntilde;&Iacute;A</th>
    <th class="print">{total_cia}</th>
  </tr>
</table>
</td>
</tr>
</table>
<br style="page-break-after:always;">
<!-- END BLOCK : compania -->


