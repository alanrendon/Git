<!-- START BLOCK : cerrar -->
<script language="javascript" type="text/javascript">
	function cerrar() {
		window.opener.document.location.reload();
		self.close();
	}
	
	window.onload = cerrar();
</script>
<!-- END BLOCK : cerrar -->

<!-- START BLOCK : modificar -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
<p class="title">Modificaci&oacute;n de movimientos autorizados </p>
<form name="form" method="post" action="./ban_mau_minimod.php?tabla={tabla}">
<input name="temp" type="hidden">
<input name="id" type="hidden" value="{id}">
<table class="tabla">
  <tr>
    <th class="vtabla" scope="row">C&oacute;digo de movimiento </th>
    <td class="vtabla"><select name="cod_mov" class="insert" id="cod_mov">
      <!-- START BLOCK : cod_mov -->
	  <option value="{id}" {selected}>{id} - {nombre}</option>
	  <!-- END BLOCK : cod_mov -->
    </select></td>
  </tr>
  <tr>
    <th class="vtabla" scope="row">Importe autorizado </th>
    <td class="vtabla"><input name="importe" type="text" class="rinsert" id="importe" onFocus="form.temp.value=this.value" onChange="isFloat(this,2,form.temp)" value="{importe}" size="10" maxlength="10"></td>
  </tr>
</table>
<p>
  <input type="button" class="boton" value="Cerrar ventana" onClick="self.close()">
&nbsp;&nbsp;&nbsp;  
<input type="button" class="boton" value="Modificar" onClick="valida_registro()">
</p>
</form>
</td>
</tr>
</table>
</form>
</td>
</tr>
</table>
<script language="javascript" type="text/javascript">
	function valida_registro() {
		if (document.form.importe.value <= 0) {
			alert("Debe especificar el importe");
			document.form.importe.select();
			return false;
		}
		else if (document.form.cod_mov.value <= 0) {
			alert("Debe especificar el c�digo del movimiento");
			document.form.cod_mov.select();
			return false;
		}
		else {
			if (confirm("�Son correctos los datos?"))
				document.form.submit();
			else
				return false;
		}
	}
	
	window.onload = document.form.importe.select();
</script>
<!-- END BLOCK : modificar -->