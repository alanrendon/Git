<!-- START BLOCK : obtener_datos -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
<p class="title">Consumo de Av&iacute;os</p>
<form action="./pan_avi_cap.php" method="get" name="form" id="form" onKeyPress="if (event.keyCode == 13) return false">
<input name="temp" type="hidden">
<table class="tabla">
  <tr>
    <th class="vtabla">Compa&ntilde;&iacute;a</th>
    <td class="vtabla"><input name="num_cia" type="text" class="insert" id="num_cia" size="3" maxlength="3" onKeyDown="if (event.keyCode == 13) valida_registro(this)"></td>
  </tr>
</table>
<p>
<input name="next" type="button" class="boton" id="next" onClick="valida_registro(num_cia)" value="Siguiente">
</p>
</form>
</td>
</tr>
</table>
<script language="javascript" type="text/javascript">
	function valida_registro(num_cia) {
		// Arreglo con los nombres de las materias primas
		cia = new Array();
		<!-- START BLOCK : nombre_cia -->
		cia[{num_cia}] = '{nombre_cia}';
		<!-- END BLOCK : nombre_cia -->
		
		if (parseInt(num_cia.value) > 0) {
			if (cia[parseInt(num_cia.value)] == null) {
				alert("Compañía "+parseInt(num_cia.value)+" no es tuya");
				num_cia.value = "";
				num_cia.select();
				return false;
			}
			else {
				document.form.submit();
				return;
			}
		}
		else if (num_cia.value == "") {
			num_cia.value = "";
			return false;
		}
	}
	
	window.onload = document.form.num_cia.select();
</script>
<!-- END BLOCK : obtener_datos -->

<!-- START BLOCK : hoja -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="top">
<p class="title">Consumo de Av&iacute;os</p>

  <table class="tabla">
  <tr>
    <th class="vtabla" scope="col">Compa&ntilde;&iacute;a</th>
    <td class="vtabla" scope="col">
      <font size="+1"><strong>{num_cia} - {nombre_cia}</strong></font> </td>
    <th class="vtabla" scope="col">Fecha</th>
    <td class="vtabla" scope="col">
      <font size="+1"><strong>{fecha}</strong></font></td>
  </tr>
</table>
  <br>
  <table width="100%" class="tabla">
    <tr>
      <th width="87" height="36" class="tabla" scope="col">Producto</th>
      <th width="83" class="tabla" scope="col">Existencia<br>
        anterior </th>
      <th width="85" class="tabla" scope="col">Entrada</th>
      <th width="85" class="tabla" scope="col">FD</th>
      <th width="85" class="tabla" scope="col">FN</th>
      <th width="85" class="tabla" scope="col">BD</th>
      <th width="85" class="tabla" scope="col">Repostero</th>
      <th width="85" class="tabla" scope="col">Piconero</th>
      <th width="85" class="tabla" scope="col">Gelatinero</th>
      <th width="85" class="tabla" scope="col">Despacho</th>
      <th class="tabla" scope="col">Existencia<br>
        final</th>
    </tr>
	<tr>
      <td height="400" colspan="11" scope="row">
	  <iframe src="pan_avi_table.php?num_cia={num_cia}" name="avi_cap" width="100%" marginwidth="0" height="100%" marginheight="0" align="top" scrolling="auto" frameborder="0"></iframe>
	  </td>
     </tr>
  </table>
  
  <input type="button" class="boton" value="Regresar">
  <table class="tabla">
    <tr>
      <th class="tabla"><input type="button" class="boton" onClick="document.location='pan_avi_altas.php?num_cia={num_cia}&cap=1'" value="Control de Avío"></th>
      </tr>
  </table>
  </td>
</tr>
</table>
<!-- END BLOCK : hoja -->
