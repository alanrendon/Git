<!-- START BLOCK : datos -->
<form name="form" method="get" action="ros_cmd_con.php">
<table>
  <tr>
    <th scope="col" class="tabla">Compa&ntilde;&iacute;a</th>
    <th scope="col" class="tabla">Fecha Inicial (ddmmaa) </th>
    <th scope="col" class="tabla">Fecha Final (ddmmaa) </th>
  </tr>
  <tr>
    <td class="tabla"><input name="num_cia" type="text" class="insert" id="num_cia" size="5" maxlength="3"></td>
    <td class="tabla"><input name="fecha1" type="text" class="insert" id="fecha1" size="10" maxlength="10" onChange="actualiza_fecha(this)" value="{fecha1}"></td>
    <td class="tabla"><input name="fecha2" type="text" class="insert" id="fecha2" size="10" maxlength="10" onChange="actualiza_fecha(this)" value="{fecha2}"></td>
  </tr>
</table>
<p>
<input type="button" class="boton" name="enviar" value="Buscar" onClick="valida_registro">
</p>
</form>
<!-- END BLOCK : datos -->
<!-- START BLOCK : consulta -->
<p class="title">Compra Directa</p>
<table class="tabla">
      <tr>
        <th class="tabla" align="center">Compa&ntilde;&iacute;a</th>
        <th class="tabla" align="center">Fecha del<br>movimiento<br>(ddmmaaaa)</th>
        <th class="tabla" align="center">Fecha de pago<br>(autom&aacute;tico)</th>
      </tr>
      <tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
        <td class="tabla" align="center">
          {cd_num_cia} {cd_nombre_cia}
		</td>
        <td class="tabla" align="center">
			{cd_fecha_mov}
		</td>
        <td class="tabla"  align="center">{cd_fecha_pago}</td>
      </tr>
</table>
<br>
<table class="tabla" >
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
        <td class="tabla" align="center">
          {cd_codmp} {cd_nombre_mp}
        </td>
        <td class="tabla" align="center">
          {cd_cantidad}
        </td>
        <td class="tabla" align="center">
          {cd_kilos}
        </td>
        <td class="tabla" align="center">
          {cd_precio_unit}
        </td>
        <th class="tabla" align="center">{cd_total}</th>
        <th class="tabla" align="center">
			{aplica_gasto}
		</th>
	    <td class="tabla" align="center">{cd_num_proveedor} {cd_nombre_proveedor}</td>
	    <td class="tabla" align="center">{cd_numero_fact}</td>
	  </tr>
	  <!-- END BLOCK : fila -->
	  <tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
	    <td colspan="2"></td>
		<th  class="tabla"colspan="2">Total factura</th>
		<th class="tabla">{cd_total}</th>
	    <td class="tabla"></td>
	    <td class="tabla"></td>
	    <td class="tabla"></td>
	  </tr>
</table>
<!-- END BLOCK : consulta -->