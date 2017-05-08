<!-- START BLOCK : datos -->
<link href="/styles/tablas.css" rel="stylesheet" type="text/css">

<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">


<script type="text/javascript" language="javascript">
	function valida_registro() {
		if (document.form.num_cia.value <= 0) {
			alert("Debe especificar una compañía");
			document.form.num_cia.select();
			return false;
		}
		else if (document.form.fecha.value == null) {
			alert("Debe especificar la fecha de inicio");
			document.form.fecha.select();
			return false;
		}
		else
			document.form.submit();
	}
</script>
<p class="title">CONSULTA DE COMPRA DIRECTA POR DIA</p>
<form name="form" method="get" action="ros_cmd_con.php">
<table class="tabla">
  <tr class="tabla">
    <th scope="col" class="tabla">Compa&ntilde;&iacute;a</th>
    <th scope="col" class="tabla">Fecha <font size="-1">(ddmmaa)</font> </th>
    </tr>
  <tr class="tabla">
    <td class="tabla"><input name="num_cia" type="text" class="insert" id="num_cia" onChange="if (this.value == '') return;
else if (parseInt(this.value) > 0) {
var temp=parseInt(this.value); this.value=temp;}
else this.value='';" size="5" maxlength="3" onKeyDown="if(event.keyCode==13) document.form.fecha.select();"></td>
    <td class="tabla"><input name="fecha" type="text" class="insert" id="fecha" size="10" maxlength="10" onChange="actualiza_fecha(this);"  onKeyDown="if(event.keyCode==13) form.enviar.focus();"value="{fecha}"></td>
    </tr>
</table>
<p>
<input type="button" class="boton" name="enviar" value="Buscar" onClick="valida_registro()">
</p>
</form>
<script language="JavaScript" type="text/JavaScript">
window.onload=document.form.num_cia.select();
</script>

</td>
</tr>
</table>

<!-- END BLOCK : datos -->
<!-- START BLOCK : consulta -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">

<p class="title">Consulta de Compra Directa de Rosticer&iacute;as </p>
<table class="tabla">
      <tr>
        <th class="tabla" align="center">Compa&ntilde;&iacute;a</th>
        <th class="tabla" align="center">Fecha del<br>movimiento<br>(ddmmaaaa)</th>
        <th class="tabla" align="center">Fecha de pago<br>(autom&aacute;tico)</th>
      </tr>
      <tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
        <td class="tabla" align="center">
          {num_cia} {nombre_cia}
		</td>
        <td class="tabla" align="center">
			{fecha_mov}
		</td>
        <td class="tabla"  align="center">{fecha_pago}</td>
      </tr>
</table>
<br>
<table class="tabla">
      <tr>
        <th class="tabla" align="center">C&oacute;digo y nombre de materia prima  </th>
        <th class="tabla" align="center">Cantidad </th>
        <th class="tabla" align="center">Kilos </th>
        <th class="tabla" align="center">Precio<br>unitario</th>
        <th class="tabla" align="center">Total </th>
        <th class="tabla" align="center">Aplica a<br>Gastos </th>
        <th class="tabla" align="center">C&oacute;digo proveedor</th>
        <th class="tabla" align="center">N&uacute;mero de<br>
        documento</th>
      </tr>
      <!-- START BLOCK : fila -->
	  <tr  onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
        <td class="vtabla" align="center">
          {codmp} {nombre_mp}
        </td>
        <td class="tabla" align="center">
          {cantidad}
        </td>
        <td class="tabla" align="center">
          {kilos}
        </td>
        <td class="rtabla" align="center">
          {precio_unit}
        </td>
        <th class="tabla" align="center">{total}</th>
        <th class="tabla" align="center">
			{aplica_gasto}
		</th>
	    <td class="tabla" align="center">{num_proveedor} {nombre_proveedor}</td>
	    <td class="tabla" align="center">{numero_fact}</td>
	  </tr>
	  <!-- END BLOCK : fila -->
	  <tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
	    <td colspan="2"></td>
		<th  class="tabla"colspan="2">Total factura</th>
		<th class="rtabla">{total}</th>
	    <td class="tabla"></td>
	    <td class="tabla"></td>
	    <td class="tabla"></td>
	  </tr>
</table>
<p>

<input name="regresar" type="button" value="Regresar" onClick="parent.history.back();" class="boton">
</p>

</td>
</tr>
</table>

<!-- END BLOCK : consulta -->