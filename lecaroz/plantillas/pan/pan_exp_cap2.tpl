<!-- START BLOCK : error_pexp -->
<p class="error">Expendio no.{exp}: 'Pan p/expendio' debe ser menor a 'Pan p/venta'.</p>
<!-- END BLOCK : error_pexp -->
<!-- START BLOCK : error_dev -->
<p class="error">Expendio no.{exp}: 'Devoluci&oacute;n' debe ser menor a 'Abono'.</p>
<!-- END BLOCK : error_dev -->

<!-- START BLOCK : hoja -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr align="center" valign="middle">
<td>

<form name="form" action="./hojadiaria.php?tabla={tabla}" method="post" onKeyDown="if (event.keyCode == 13) document.form.enviar.focus();">
<table class="tabla">
    <tr align="center">
      <th class="vtabla">Compa&ntilde;&iacute;a</th>
      <td class="vtabla"><font size="+1">{compania} - {nombre_cia}</font></th>
      <th class="vtabla">Fecha</th>
      <td class="vtabla"><font size="+1">{fecha}</font></td>
    </tr>
  </table>
  <br>
  <table class="tabla">
	<tr>
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
	<tr>
      <td align="center" class="tabla">
          <input name="num_expendio{i}" type="hidden" value="{num_expendio}"><b>{num_expendio}</b>
      </td>
	  <td class="vtabla" align="left">
	      <b>{nombre_expendio}</b>
	  </td>
      <td class="tabla" align="center">
          <input name="pan_p_venta{i}" type="hidden"value="{pan_p_venta}">{pan_p_venta}
      </td>
      <td class="tabla" align="center">
		<input name="devolucion{i}" type="hidden" value="{devolucion}">{devolucion}
	  </td>
      <td class="tabla" align="center">
          <input name="pan_p_expendio{i}" type="hidden" value="{pan_p_expendio}">{pan_p_expendio}
      </td>
      <td class="tabla" align="center">
        <input name="abono{i}" type="hidden" value="{abono}">{abono}
      </td>
      <th class="tabla" align="center">
        <input name="rezago{i}" type="hidden" value="{rezago}"><font size="+1">{rezago}</font>
      </th>
    </tr>
	<!-- END BLOCK : rows -->
    <!-- START BLOCK : totales -->
	<tr>
      <td align="center" class="tabla">&nbsp;
      </td>
	  <th class="tabla" align="center"><b>Totales</b>
	  </th>
      <th class="tabla" align="center">
          <font size="+1">{pan_p_venta}</font>
      </th>
      <th class="tabla" align="center"> <font size="+1">{devolucion}</font> </th>
      <th class="tabla" align="center">
          <font size="+1">{pan_p_expendio}</font>
      </th>
      <th class="tabla" align="center">
        <font size="+1">{abono}</font>
      </th>
      <th class="tabla" align="center">
        <font size="+1">{rezago}</font>
      </th>
    </tr>
	<!-- END BLOCK : totales -->
  </table>
  <p><input type="button" class="boton" value='<<Regresar' onclick='parent.history.back()'>&nbsp;&nbsp;
  <!-- START BLOCK : enviar -->
  <input type="button" class="boton" name="enviar" value="Capturar movimientos" onclick="if(confirm('¿Capturar datos?')) document.form.submit(); else return false;">
  <!-- END BLOCK : enviar -->
  </p>
</form>  
</td>
</tr>
</table>
<!-- END BLOCK : hoja -->