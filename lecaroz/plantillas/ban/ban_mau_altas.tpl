<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
<p class="title">Alta de movimientos autorizados </p>
<form name="form" method="post" action="./ban_mau_altas.php?tabla={tabla}">
<input name="temp" type="hidden">
<table class="tabla">
  <tr>
    <th class="vtabla" scope="row">C&oacute;digo de movimiento </th>
    <td class="vtabla"><select name="cod_mov" class="insert" id="cod_mov">
      <!-- START BLOCK : cod_mov -->
	  <option value="{id}">{id} - {nombre}</option>
	  <!-- END BLOCK : cod_mov -->
    </select></td>
  </tr>
  <tr>
    <th class="vtabla" scope="row">Importe autorizado </th>
    <td class="vtabla"><input name="importe" type="text" class="rinsert" id="importe" onFocus="form.temp.value=this.value" onChange="isFloat(this,2,form.temp)" size="10" maxlength="10"></td>
  </tr>
</table>
<p>
  <input type="button" class="boton" value="Alta" onClick="valida_registro()">
</p>
</form>
</td>
</tr>
</table>
</form>
<script language="javascript" type="text/javascript">
	function valida_registro() {
		if (document.form.importe.value <= 0) {
			alert("Debe especificar el importe");
			document.form.importe.select();
			return false;
		}
		else if (document.form.cod_mov.value <= 0) {
			alert("Debe especificar el código del movimiento");
			document.form.cod_mov.select();
			return false;
		}
		else {
			if (confirm("¿Son correctos los datos?"))
				document.form.submit();
			else
				return false;
		}
	}
	
	window.onload = document.form.importe.select();
</script>
