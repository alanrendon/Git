<p class="title">Cancelaci&oacute;n de Facturas de Rosticerias</p>
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
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
<form name="form" action="./ros_fac_can.php" method="get" onkeydown="if (event.keyCode == 13) form.enviar.focus();">
  <table class="tabla">
    <tr>
      <th class="vtabla">Compañía</th>
      <td class="vtabla">
        <input class="insert" name="num_cia" type="text" id="num_cia" size="10" maxlength="10" onfocus="form.temp.value=this.value" onchange="valor=isInt(this,form.temp); if (valor==false) this.select();" onkeydown="if (event.keyCode == 13) document.form.num_fac.select();">      </td>
      <th class="vtabla">Proveedor</th>
      <td class="vtabla"><select name="num_pro" class="insert" id="num_pro">
        <!-- <option value="13" selected="selected">13 POLLOS GUERRA</option>
        <option value="482">482 CENTRAL DE POLLOS Y CARNES S.A. DE C.V.</option>
        <option value="1386">1386 EL RANCHERITO S.A. DE C.V.</option> -->
        <!-- START BLOCK : pro -->
        <option value="{value}">{value} {text}</option>
        <!-- END BLOCK : pro -->
		</select></td>
      <th class="vtabla">N&uacute;mero de factura </th>
      <td class="vtabla"><input class="insert" name="num_fac" type="text" id="num_fac" size="10" maxlength="50" onchange="this.value=this.value.replace(/[^a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ]/g,'');this.value=this.value.toUpperCase();"></td>
    </tr>
  </table>
  <p>
    <input class="boton" name="enviar2" type="button" value="Consultar" onclick='valida();'>
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
<form name="form" action="./actualiza_ros_fac.php" method="post" onkeydown="if (event.keyCode == 13) form.enviar.focus();">
  <table class="tabla">
    <tr>
      <th class="tabla" align="center">Compa&ntilde;&iacute;a</th>
      <th class="tabla" align="center">Proveedor</th>
      <th class="tabla" align="center">N&uacute;mero Factura </th>
      <th class="tabla" align="center">Fecha movimiento </th>
      <th class="tabla" align="center">Fecha de pago </th>
    </tr>

    <tr>
      <td class="tabla" align="center">

<font size="+1">
<strong>{numero_cia}&#8212;{nombre_cia}</strong>
</font>
</td>
      <td class="tabla" align="center">

<font size="+1">
{num_proveedor}&#8212;{nom_proveedor}
</font>

</td>
      <td class="tabla" align="center">

	<font size="+1">
	{num_factura}
	</font>
</td>
      <td class="tabla" align="center">
<font size="+1">{fecha_mov}</font>      </td>
      <td class="tabla" align="center">
<font size="+1">
      {fecha_pago}
</font>	  </td>
    </tr>

  </table>
  <br>
  <table class="tabla">
    <tr>
      <th width="306" align="center" class="tabla">C&oacute;digo de Materia Primas</th>
      <th width="112" align="center" class="tabla">Cantidad</th>
      <th width="89" align="center" class="tabla">Kilos</th>
      <th width="101" align="center" class="tabla">Precio unitario </th>
      <th width="88" align="center" class="tabla">Total</th>
    </tr>
    <!-- START BLOCK : rows -->
     <tr onmouseover="overTR(this,'#ACD2DD');" onmouseout="outTR(this,'');">
		  <input name="num_cia{var}" type="hidden" value="{num_cia}">
		  <input name="codmp{var}" type="hidden" value="{codmp}">
		  <input name="num_pro{var}" type="hidden" value="{num_pro}">
		  <input name="num_fac{var}" type="hidden" value="{num_factura}">
		  <input name="fecha_mov{var}" type="hidden" value="{fecha_mov}">
		  <input name="fecha_pago{var}" type="hidden" value="{fecha_pago}">
		  <input name="cantidad{var}" type="hidden" value="{cantidad}">
		  <input name="kilos{var}" type="hidden" value="{kilos}">
		  <input name="precio{var}" type="hidden" value="{precio}">
		  <input name="total{var}" type="hidden" value="{total}">
		  <td class="vtabla" align="left">
			<strong>{codmp}&#8212;{nom_mp}</strong>
		  </td>
		  <td class="tabla" align="center">
			<strong> {cantidad1}</strong>
		  </td>
		  <td class="tabla" align="center">
			<strong>{kilos1}</strong>
		  </td>
		  <td class="tabla" align="center">
			<strong> {precio1}</strong>
		  </td>
		  <th class="tabla" align="center">
			<strong>{total1}</strong>
		  </th>
    </tr>
    <!-- END BLOCK : rows -->

<!-- START BLOCK : totales -->

  <th class="tabla" colspan="4" align="center"><b>Total</b></th>
      <th class="tabla" align="center">
        <font size="+2">{total}</font></th>
    <!-- END BLOCK : totales -->
  </table><br>

  <input type="button" name="enviar" class="boton" value="Regresar" onclick='parent.history.back()'>
  <input name="cancelar" type="button" class="boton" id="cancelar" onclick="if(confirm('¿Estas segura de cancelar esta factura?')) document.form.submit(); else return false;" value="CANCELAR FACTURA">
  <br>

</form>
</td>
</tr>
</table>
<!-- END BLOCK : factura -->
