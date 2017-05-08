<!-- START BLOCK : obtener_datos -->
<script language="javascript" type="text/javascript">
function valida(){
	if (document.form.num_cia.value<=0 || document.form.num_cia.value=="" || document.form.num_fac.value=="" || document.form.num_fac.value<=0 || document.form.proveedor.value=="" || document.form.proveedor.value<=0)
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
<p class="title">Cancelaci&oacute;n de Facturas de Gas </P>
<form name="form" action="./fac_gas_can.php" method="get" onKeyDown="if (event.keyCode == 13) form.enviar.focus();">
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
<script language="javascript" type="text/javascript">window.onload = document.form.num_cia.select();</script>
</td>
</tr>
</table>

<!-- END BLOCK : obtener_datos -->

<!-- START BLOCK : factura -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
<p class="title">Cancelaci&oacute;n de Facturas de Gas </P>
<form name="form" action="./actualiza_gas_fac.php" method="post" onKeyDown="if (event.keyCode == 13) form.enviar.focus();">
  <table class="tabla">
    <tr>
      <th class="tabla" align="center">Compa&ntilde;&iacute;a</th>
      <th class="tabla" align="center">Proveedor</th>
      <th class="tabla" align="center">N&uacute;mero Factura </th>
      <th class="tabla" align="center">Fecha movimiento </th>
      <th class="tabla" align="center">Total de la Factura</th>
    </tr>
    <tr>
      <td class="tabla" align="center">
			<font size="+1"><strong>{numero_cia}&#8212;{nombre_cia}</strong></font>
			<input name="num_cia" type="hidden" value="{numero_cia}">
			<input name="contador" type="hidden" id="contador" value="{cont}">
	</td>
      <td class="tabla" align="center">
		<font size="+1">
		{num_proveedor}&#8212;{nom_proveedor}
		<input name="num_proveedor" type="hidden" id="num_proveedor" value="{num_proveedor}">
		</font>
	</td>
      <td class="tabla" align="center">
		<font size="+1">{num_factura}</font><input name="num_fac" type="hidden" id="num_fac" value="{num_factura}">
	</td>
      <td class="tabla" align="center">
		<font size="+1">{fecha_mov}</font> <font size="+1"><font size="+1">
		<input name="fecha_mov" type="hidden" id="fecha_mov" value="{fecha_mov}">
		</font></font> 
	 </td>
      <td align="center" class="tabla"><font size="+1"><strong>{total_fac}</strong></font></td>
    </tr>
  </table>
  <br>

  <table class="tabla">
    <tr>
      <th class="tabla">No. tanque </th>
      <th class="tabla">Capacidad</th>
      <th class="tabla">Precio/litro</th>
      <th class="tabla">IVA</th>
      <th class="tabla">Litros</th>
      <th class="tabla">% inicial</th>
      <th class="tabla">% final</th>
      <th class="tabla">Total</th>
    </tr>
    <tr>
 <!-- START BLOCK : tanques -->
      <th class="tabla">
	  <input name="num_tanque{i}" type="hidden" class="insert" value="{num_tanque}" size="5">
      {num_tanque}
	  </th>
      <th class="tabla">{capacidad}</th>
      <td class="tabla">{fprecio_unit} 
	  </td>
      <td class="tabla">{iva}</td>
      <td class="tabla">{litros}<font size="+1">
        <input name="litros{i}" type="hidden" id="litros{i}" value="{litros}" size="10">
      </font></td>
      <td class="tabla">{porc_ini}</td>
      <td class="tabla">{porc_fin}</td>
      <th class="tabla">{total}</th>
    </tr>
  </table>
  <br>
<!-- END BLOCK : tanques -->
  <input type="button" name="enviar" class="boton" value="Regresar" onclick='parent.history.back()'>
  <input name="cancelar" type="button" class="boton" id="cancelar" onclick="if(confirm('¿Estas segura de cancelar esta factura?')) document.form.submit(); else return false;" value="CANCELAR FACTURA">
</form>
</td>
</tr>
</table>
<!-- END BLOCK : factura -->