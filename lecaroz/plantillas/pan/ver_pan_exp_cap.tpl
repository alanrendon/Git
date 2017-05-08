<!-- START BLOCK : hoja -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
<form name="form" action="./hojadiaria.php?tabla={tabla}" method="post" onKeyDown="if (event.keyCode == 13) document.form.enviar.focus();">
<table class="tabla" align="center">
    <tr align="center">
      <th class="vtabla">Compa&ntilde;&iacute;a</th>
      <td class="vtabla"><font size="+1">{compania} &#8212; {nombre_cia}</font></th>
      <th class="vtabla">Fecha</th>
      <td class="vtabla"><font size="+1">{fecha}</font></td>
    </tr>
  </table>
  <br>
  <table class="tabla" align="center">
	<tr>
	  <th class="tabla" align="center">Rezago anterior </th>
      <th class="tabla" align="center" colspan="2">C&oacute;digo y nombre de expendio </th>
      <th align="center" class="tabla">Partidas</th>
      <th class="tabla" align="center">Devuelto</th>
      <th class="tabla" align="center">Total</th>
      <th class="tabla" align="center">Abono </th>
      <th class="tabla" align="center">Rezago</th>
    </tr>
    <!-- START BLOCK : rows -->
	<input type="hidden" name="num_cia{i}" value="{num_cia}">
	<input type="hidden" name="fecha{i}" value="{fecha}">
	<input name="num_expendio{i}" type="hidden" value="{num_expendio}">
	<input name="pan_p_venta{i}" type="hidden"value="{pan_p_venta}">
	<input name="devolucion{i}" type="hidden" value="{devolucion}">
	<input name="pan_p_expendio{i}" type="hidden" value="{pan_p_expendio}">
	<input name="abono{i}" type="hidden" value="{abono}">
	<input name="rezago{i}" type="hidden" value="{rezago}">
	<input name="rezago_anterior{i}" type="hidden" value="{rezago_anterior}">
	<input name="nombre_expendio{i}" type="hidden" value="{nombre_expendio}">
	<input name="porc_ganancia{i}" type="hidden" value="{porc_ganancia}">
	<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
	  <th class="tabla" align="center">
        <font size="+1">{rezagoant_for}</font>
      </th>
      <td align="center" class="tabla">
          <b>{num_expendio}</b>
      </td>
	  <td class="vtabla" align="left">
	      <b>{nombre_expendio}</b>
	  </td>
	  <td class="tabla" align="center">
	  <!-- START BLOCK : pan_p_venta_ok -->
        {pan_p_venta_for}
 	  <!-- END BLOCK : pan_p_venta_ok -->
	  <!-- START BLOCK : pan_p_venta_error -->
        <font color="#FF0000"><b>{pan_p_venta_for}</b></font>
 	  <!-- END BLOCK : pan_p_venta_error -->
      </td>
	  
      <td class="tabla" align="center">
		<!-- START BLOCK : devolucion_ok -->
		{devolucion_for}
		<!-- END BLOCK : devolucion_ok -->
		<!-- START BLOCK : devolucion_error -->
		 <font color="#FF0000"><b>{devolucion_for}</b></font>
		<!-- END BLOCK : devolucion_error -->
	  </td>
      <td class="tabla" align="center">
          <!-- START BLOCK : pan_p_expendio_ok -->
		  {pan_p_expendio_for}
		  <!-- END BLOCK : pan_p_expendio_ok -->
		  <!-- START BLOCK : pan_p_expendio_error -->
		  <font color="#FF0000"><b>{pan_p_expendio_for}</b></font>
		  <!-- END BLOCK : pan_p_expendio_error -->
      </td>
      <td class="tabla" align="center">
        <!-- START BLOCK : abono_ok -->
		{abono_for}
		<!-- END BLOCK : abono_ok -->
		<!-- START BLOCK : abono_error -->
		<font color="#FF0000"><b>{abono_for}</b></font>
		<!-- END BLOCK : abono_error -->
      </td>
      <th class="tabla" align="center">
	  <!-- START BLOCK : rezago_ok -->
        <font size="+1">{rezago_for}</font>
	  <!-- END BLOCK : rezago_ok -->
	  <!-- START BLOCK : rezago_error -->
	  	<font size="+1" color="#FF0000">{rezago_for}</font>
	  <!-- END BLOCK : rezago_error -->
      </th>
    </tr>
	<!-- END BLOCK : rows -->
    <!-- START BLOCK : totales -->
	<tr>
	  <th class="tabla" colspan="3" align="center"><b>Totales</b></th>
      <th class="tabla" align="center">
          <font size="+1">{pan_p_venta}</font>
      </th>
      <th class="tabla" align="center"> <font size="+1">{devolucion}</font> </th>
      <th class="tabla" align="center">
          <font size="+1">{pan_p_expendio}</font>
      </th>
      <th class="tabla" align="center"><input name="fecha" type="hidden" value="{fecha}"><input name="num_cia" type="hidden" value="{num_cia}"><input name="abono" type="hidden" value="{post_abono}">
        <font size="+1">{abono}</font>
      </th>
      <th class="tabla" align="center">
		<font size="+1">{rezago}</font>
      </th>
    </tr>
	<!-- END BLOCK : totales -->
  </table>
  <p><input type="button" class="boton" value='<<Regresar' onclick="document.location='./pan_exp_cap.php?compania={num_cia}'">&nbsp;&nbsp;
  <!-- START BLOCK : enviar -->
  <input type="button" class="boton" name="enviar" value="Capturar movimientos" onclick="if(confirm('¿Capturar datos?')) document.form.submit(); else return false;">
  <!-- END BLOCK : enviar -->
  </p>
</form>
</td>
</tr>
</table>
<!-- END BLOCK : hoja -->