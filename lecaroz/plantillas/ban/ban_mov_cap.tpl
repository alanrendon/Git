<!-- START BLOCK : cerrar -->
<p>
  <input type="button" class="boton" onClick="window.opener.document.location = './ban_conciliacion.php';self.close();" value="Cerrar">
</p>
<script language="javascript" type="text/javascript">
	function cerrar() {
		window.opener.document.location = './ban_conciliacion.php';
		self.close();
	}
	
	window.onload = cerrar();
</script>
<!-- END BLOCK : cerrar -->
<!-- START BLOCK : captura -->
<script type="text/javascript" language="JavaScript">
	function valida_registro() {
		if (confirm("¿Son correctos los datos del formulario?"))
			document.form.submit();
		else
			return false;
	}
</script>
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">

<p class="title">Otros movimientos </p>
<form action="./ban_mov_cap.php?tabla={tabla}" method="post" name="form">
<input name="temp" type="hidden">
<table class="tabla">
   <tr>
      <th class="tabla" scope="col">C&oacute;digo Movimiento</th>
      <th class="tabla" scope="col">Importe</th>
      <th class="tabla" scope="col">Concepto</th>
    </tr>
	<!-- START BLOCK : fila -->
    <tr>
      <td class="tabla" onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');">
	  	<select name="cod_mov{i}" class="insert" id="cod_mov{i}">
		<!-- START BLOCK : mov -->
			<option value="{id}" {selected}>{id} - {descripcion}</option>
		<!-- END BLOCK : mov -->
      </select>	  </td>
      <td class="tabla" onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');">
  	  <input name="importe{i}" type="text" class="rinsert" id="importe{i}" onFocus="form.temp.value=this.value" onChange="isFloat(this,2,form.temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) form.concepto{i}.select();
else if (event.keyCode == 37) form.fecha_mov{i}.select();
else if (event.keyCode == 38) form.importe{back}.select();
else if (event.keyCode == 40) form.importe{next}.select();" value="{importe}" size="12" maxlength="12"></td>
      <td class="tabla" onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');">
  	  <input name="concepto{i}" type="text" class="vinsert" id="concepto{i}" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) form.importe{next}.select();
else if (event.keyCode == 37) form.importe{i}.select();
else if (event.keyCode == 38) form.concepto{back}.select();
else if (event.keyCode == 40) form.concepto{next}.select();" value="{concepto}" size="50" maxlength="50"></td>
    </tr>
	<!-- END BLOCK : fila -->
  </table>
  <p> 
      <input type="button" value="Cerrar ventana" class="boton" onClick="self.close()">
&nbsp;&nbsp;      
<input name="enviar" type="button" class="boton" onClick='valida_registro()' value="Siguiente">
  </p>
</form>

</td>
</tr>
</table>
<!-- END BLOCK : captura -->