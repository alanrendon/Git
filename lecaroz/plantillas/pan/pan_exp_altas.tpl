<script type="text/javascript" language="JavaScript">
	function valida_registro() {
		if(document.form.campo0.value < 0) {
			alert('Debe especificar una compania');
			document.form.campo0.select();
		}
		else if (document.form.campo6.value==0) {
			alert('Debe especificar un numero para el expendio');
			document.form.campo6.select();
		}
		else if (document.form.campo3.value > 40) {
			alert('Porcentaje de ganancia debe ser menor al 40%');
			document.form.campo3.select();
		}
		else {
			if (confirm("¿Son correctos los datos del formulario?"))
				document.form.submit();
			else
				document.form.campo0.select();
		}
	}
	
	function borrar() {
		if (confirm("¿Desea borrar el formulario?"))
			document.form.reset();
		else
			document.form.campo0.select();
	}
</script>
<link href="../../styles/tablas.css" rel="stylesheet" type="text/css">

<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr align="center" valign="middle">
<td>
<p class="title">ALTA DE EXPENDIOS</P>
<form name="form" action="./alta_catalogos.php?tabla={tabla}" method="POST">
<table class="tabla" align="center">
	<tr>
		<th class="vtabla">N&uacute;mero de la compa&ntilde;&iacute;a</th>
		<td class="vtabla"><input name="campo0" type="text" class="insert" id="campo0" value="0" size="5" maxlength="5" onKeyDown="if (event.keyCode == 13) document.form.campo6.select();"></td>
	</tr>
	<tr>
		<th class="vtabla">N&uacute;mero del expendio</th>
		<td class="vtabla"><input name="campo6" type="text" class="insert" id="campo6" value="0" size="5" maxlength="5" onKeyDown="if (event.keyCode == 13) document.form.campo4.select();"></td>
	</tr>
	<tr>
		<th class="vtabla">N&uacute;mero de expendio en panader&iacute;a</th>
		<td class="vtabla"><input name="campo4" type="text" class="insert" id="campo4" onKeyDown="if (event.keyCode == 13) document.form.campo1.select();" value="0" size="5" maxlength="5" readonly="true"></td>
	</tr>
	<tr>
		<th class="vtabla">Nombre</th>
		<td class="vtabla"><input name="campo1" type="text" class="insert" id="campo1" size="20" maxlength="45" onKeyDown="if (event.keyCode == 13) document.form.campo3.select();"></td>
	</tr>
	<tr>
      <th class="vtabla">Tipo expendio</th>
	  <td class="vtabla">
		<select name="campo5" class="insert" id="campo5">
          <option value="{valuetipo}" selected>{idtipo} - {nametipo}</option>
		  <!-- START BLOCK : tipo -->
          <option value="{valuetipo}">{idtipo} - {nametipo}</option>
          <!-- END BLOCK : tipo -->
        </select></td>
	</tr>
	<tr>
		<th class="vtabla">Direcci&oacute;n</th>
		<td class="vtabla"><textarea name="campo2" cols="32" rows="4" wrap="VIRTUAL" class="insert" id="campo2"></textarea></td>
	</tr>
	<tr>
		<th class="vtabla">Porcentaje de ganancia</th>
		<td class="vtabla"><input name="campo3" type="text" class="insert" id="campo3" value="0" size="6" maxlength="6" {disabled} onKeyDown="if (event.keyCode == 13) document.form.campo7.select();"></td>
	</tr>
	<tr>
	  <th class="vtabla">Importe fijo </th>
	  <td class="vtabla"><input name="campo7" type="text" class="rinsert" id="campo7" size="10" maxlength="10" onKeyDown="if (event.keyCode == 13) document.form.campo12.select();"></td>
	  </tr>
	<tr>
	  <th class="vtabla">Total Fijo </th>
	  <td class="vtabla"><input name="campo8" type="radio" value="FALSE" checked>
	    No
	      <input name="campo8" type="radio" value="TRUE">
	      Si</td>
	  </tr>
	<tr>
	  <th class="vtabla">Expendio por Notas </th>
	  <td class="vtabla"><input name="campo9" type="radio" value="FALSE" checked>
	    No
	      <input name="campo9" type="radio" value="TRUE">
	      Si</td>
	  </tr>
	<tr>
	  <th class="vtabla">Autoriza Devoluci&oacute;n</th>
	  <td class="vtabla"><input name="campo10" type="radio" value="FALSE" checked>
	    No
	      <input name="campo10" type="radio" value="TRUE">
	      Si</td>
	  </tr>
	<tr>
	  <th class="vtabla">Agente de Ventas </th>
	  <td class="vtabla"><select name="campo11" class="insert" id="campo11">
	    <option value="" selected>-</option>
	    <!-- START BLOCK : agente -->
		<option value="{id}">{nombre}</option>
		<!-- END BLOCK : agente -->
	    </select></td>
	  </tr>
	<tr>
	  <th class="vtabla">Panader&iacute;a a la cual reparte </th>
	  <td class="vtabla"><input name="campo12" type="text" class="insert" id="campo12" onkeydown="if (event.keyCode==13)document.form.campo0.select()" value="0" size="3" maxlength="3" /></td>
	  </tr>
</table>
<p><img src="./menus/insert.gif" align="middle">&nbsp;
  <input name="enviar" type="button" class="boton" value="Alta de Expendio" onclick='valida_registro()'>&nbsp;&nbsp;&nbsp;&nbsp;<img src="./menus/delete.gif" align="middle">&nbsp;
  <input type="button" class="boton" value="Borrar formulario" onclick='borrar()'></p>
</form>
<script language="JavaScript" type="text/JavaScript">
window.onload=document.form.campo0.select();
</script>

</td>
</tr>
</table>