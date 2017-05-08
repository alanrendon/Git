<!-- START BLOCK : obtener_datos -->
<script language="javascript" type="text/javascript">
function valida(){
	if (document.form.num_cia.value<=0 || document.form.num_cia.value=="" || document.form.num_fac.value=="")
	{
		alert("Verifique los campos por favor");
		document.form.num_cia.select();		
	}
	else document.form.submit();
}


</script>
<link href="/styles/tablas.css" rel="stylesheet" type="text/css">

<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
<p class="title">Cancelaci&oacute;n de Facturas de Proveedores </P>
<form name="form" action="./fac_prov_can.php" method="get" onKeyDown="if (event.keyCode == 13) form.enviar.focus();">
  <table class="tabla">
    <tr>
      <th class="vtabla">Compañía</th>
      <td class="vtabla">
        <input class="insert" name="num_cia" type="text" id="num_cia" size="10" maxlength="10" onFocus="form.temp.value=this.value" onChange="valor=isInt(this,form.temp); if (valor==false) this.select();" onKeyDown="if (event.keyCode == 13) document.form.proveedor.select();">
      </td>
      <th class="vtabla">Proveedor</th>
      <td class="vtabla"><input class="insert" name="proveedor" type="text" id="proveedor" size="10" maxlength="10" onFocus="form.temp.value=this.value" onChange="valor=isInt(this,form.temp); if (valor==false) this.select();" onKeyDown="if (event.keyCode == 13) document.form.num_fac.select();"></td>
      <th class="vtabla">N&uacute;mero de factura</th>
      <td class="vtabla"><input class="insert" name="num_fac" type="text" id="num_fac" size="15" maxlength="15" onKeyDown="if (event.keyCode == 13) document.form.enviar2.focus();"></td>
    </tr>
  </table>
  <p>
    <input class="boton" name="enviar2" type="button" value="Consultar" onClick='valida();'>
  </p>
</form>
</td>
</tr>
</table>
<script language="javascript" type="text/javascript">window.onload = document.form.num_cia.select();</script>
<!-- END BLOCK : obtener_datos -->

<!-- START BLOCK : factura -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
<p class="title">Cancelaci&oacute;n de Facturas de Proveedores </P>
<form name="form" action="./actualiza_prov_can.php" method="post">
  <table class="tabla">
    <tr>
      <th class="tabla" align="center">Compa&ntilde;&iacute;a</th>
      <th class="tabla" align="center">Proveedor</th>
      <th class="tabla" align="center">N&uacute;mero Factura </th>
      <th class="tabla" align="center">Fecha movimiento </th>
      <th class="tabla" align="center">Total de la Factura </th>
    </tr>

    <tr>
      <td class="tabla" align="center">

<font size="+1">
<strong>{numero_cia}&#8212;{nombre_cia}</strong>
</font>
<input name="num_cia" type="hidden" value="{numero_cia}">
<input name="codgastos" type="hidden" id="codgastos" value="{codgastos}"></td>
      <td class="tabla" align="center">

<font size="+1">
{num_proveedor}&#8212;{nom_proveedor}
<input name="num_proveedor" type="hidden" id="num_proveedor" value="{num_proveedor}">
</font>

</td>
      <td class="tabla" align="center">

	<font size="+1">
	{num_factura}<font size="+1">
	<input name="num_fac" type="hidden" id="num_fac" value="{num_factura}">
	</font>	</font>      
</td>
      <td class="tabla" align="center">
<font size="+1">{fecha_mov}</font> <font size="+1"><font size="+1">
<input name="fecha_mov" type="hidden" id="fecha_mov" value="{fecha_mov}">
</font></font> </td>
      <td class="tabla" align="center"><font size="+1">{total}</font> <font size="+1"><font size="+1"> </font></font></td>
    </tr>

  </table>
  <br>
  
  <input type="button" name="enviar" class="boton" value="Regresar" onclick='parent.history.back()'>
  <input name="cancelar" type="button" class="boton" id="cancelar" onclick="if(confirm('¿Estas segura de cancelar esta factura?')) document.form.submit(); else return false;" value="CANCELAR FACTURA">
  <br>
   
</form>
</td>
</tr>
</table>
<!-- END BLOCK : factura -->