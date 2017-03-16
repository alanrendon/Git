<script type="text/javascript" language="JavaScript">
	function valida_registro() {
		if(document.form.num_cia.value <= 0) {
			alert('Debe especificar una compañía');
			document.form.num_cia.select();
		}
		else if(document.form.codmp.value <= 0) {
			alert('Debe especificar una materia prima');
			document.form.codmp.focus();
		}
		else {
				if (confirm("¿Son correctos los datos?"))
					document.form.submit();
				else
					document.form.num_cia.select();
		}
	}
	
	function borrar() {
		if (confirm("¿Desea borrar el formulario?"))
			document.form.reset();
		else
			document.form.num_cia.select();
	}
</script>
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr align="center" valign="middle">
<td>
<p class="title">Control de Av&iacute;o</p>
<form action="./pan_avi_altas.php?tabla={tabla}" method="post" name="form" onKeyDown="if (event.keyCode == 13) document.form.enviar.focus();">
<table class="tabla">
  <tr>
    <th class="tabla" scope="col">Compa&ntilde;&iacute;a</th>
    <th class="tabla" scope="col">C&oacute;digo de Materia Prima </th>
  </tr>
  <tr>
    <td class="tabla">
    <input name="num_cia" type="text" class="insert" id="num_cia" size="5" maxlength="5">    </td>
    <td class="tabla">
      <select name="codmp" class="insert" id="codmp">
        <option value="" selected>---------------------</option>
        <!-- START BLOCK : mp -->
        <option value="{codmp}">{codmp} - {nombre}</option>
        <!-- END BLOCK : mp -->
      </select>
	</td>
	<tr>
      <th class="tabla" colspan="2">Turno</th>
	</tr>
	<tr>
	<td class="vtabla" colspan="2">
	  <label><input name="cod_turno0" type="checkbox" id="cod_turno0" value="1">
	  Francesero de día</label><br>
	  <label><input name="cod_turno1" type="checkbox" id="cod_turno1" value="2">
	  Francesero de noche</label><br>
	  <label><input name="cod_turno2" type="checkbox" id="cod_turno2" value="3">
	  Bizcochero de día</label><br>
	  <label><input name="cod_turno3" type="checkbox" id="cod_turno3" value="4">
	  Repostero de día</label><br>
	  <label><input name="cod_turno4" type="checkbox" id="cod_turno4" value="8">
	  Piconero</label><br>
	  <label><input name="cod_turno5" type="checkbox" id="cod_turno5" value="9">
	  Gelatinero</label><br>
    <label><input name="cod_turno6" type="checkbox" id="cod_turno6" value="10">
    Despacho</label></td>
  </tr>
	<tr>
	  <td class="vtabla" colspan="2">N&uacute;mero de orden: 
	    <input name="num_orden" type="text" id="num_orden" class="insert" size="3" maxlength="3"></td>
	  </tr>
</table>
<p>
    <img src="./menus/delete.gif" align="middle">&nbsp;&nbsp;<input type="button" class="boton" value="Borrar formulario" onclick='borrar()'>
    <img src="./menus/insert.gif" align="middle">&nbsp;&nbsp;<input type="button" class="boton" name="enviar" value="Alta de Av&iacute;o" onclick="valida_registro()">
</p>
</form>
</td>
</tr>
</table>