<!-- START BLOCK : buscar -->
<script type="text/javascript" language="JavaScript">
	function valida_registro() {
		if(document.form.compania.value <= 0) {
			alert('Debe especificar una compañía');
			document.form.compania.select();
		}
		else if(document.form.expendio.value <= 0) {
			alert('Debe especificar la fecha');
			document.form.expendio.select();
		}
		else {
				document.form.submit();
		}
	}
</script>
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr align="center" valign="middle">
<td>

<p class="title" align="center">Modificación de Expendios</p>
<form name="form" action="./pan_exp_mod.php" method="get">
<table class="tabla" align="center">
    <tr align="center">
      <th class="vtabla">Compa&ntilde;&iacute;a</th>
      <td class="vtabla"><input name="compania" type="text" class="insert" size="5" maxlength="3" onKeyDown="if(event.keyCode==13) document.form.expendio.select();">
      </th>
      <th class="vtabla">Expendio</th>
      <td class="vtabla"><input name="expendio" type="text" class="insert" size="5" maxlength="3" onKeyDown="if(event.keyCode==13) document.form.compania.select();"></td>
    </tr>
</table>
<p><input name="enviar" type="button" id="enviar" value="Enviar" onClick="valida_registro();" class="boton"></p>
</form>
<script language="JavaScript" type="text/JavaScript">
window.onload=document.form.compania.select();
</script>

</td>
</tr>
</table>
<!-- END BLOCK : buscar -->

<!-- START BLOCK : modificacion -->
<script type="text/javascript" language="JavaScript">
	function valida_registro() {
		if(document.form.num_cia.value <= 0) {
			alert('Debe especificar una compania');
			document.form.num_cia.select();
		}
		else if (document.form.num_expendio.value==0) {
			alert('Debe especificar un numero para el expendio');
			document.form.num_expendio.select();
		}
				/*else if (document.form.num_referencia.value==0) {
			alert('Debe especificar un numero para el expendio');
			document.form.num_referencia.select();
		}*/
		else if (document.form.porciento_ganancia.value > 40) {
			alert('Porcentaje de ganancia debe ser menor al 40%');
			document.form.porciento_ganancia.select();
		}
		else {
			if (confirm("¿Son correctos los datos del formulario?"))
				document.form.submit();
			else
				document.form.num_expendio.select();
		}
	}
	
	function borrar() {
		if (confirm("¿Desea borrar el formulario?"))
			document.form.reset();
		else
			document.form.num_expendio.select();
	}
</script>
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr align="center" valign="middle">
<td>

<p class="title" align="center">Modificación de Expendios</p>
<form name="form" action="./modificacion_catalogo.php?tabla={tabla}" method="POST">
<table class="tabla" align="center">
	<tr>
		<th class="vtabla">N&uacute;mero de la compa&ntilde;&iacute;a</th>
		<td class="vtabla"><input name="num_cia" type="text" class="vinsert" id="num_cia" value="{num_cia}" size="5" maxlength="5" readonly></td>
	</tr>
	<tr>
		<th class="vtabla">N&uacute;mero del expendio</th>
		<td class="vtabla"><input name="num_expendio" type="text" class="vinsert" id="num_expendio" value="{num_expendio}" size="5" maxlength="5" onKeyDown="if (event.keyCode == 13) document.form.num_referencia.select();" readonly></td>
	</tr>
	<tr>
		<th class="vtabla">N&uacute;mero de expendio en panader&iacute;a</th>
		<td class="vtabla"><input name="num_referencia" type="text" class="vinsert" id="num_referencia" value="{num_referencia}" size="5" maxlength="5"  onKeyDown="if (event.keyCode == 13) document.form.nombre.select();"></td>
	</tr>
	<tr>
		<th class="vtabla">Nombre</th>
		<td class="vtabla"><input name="nombre" type="text" class="vinsert" id="nombre" onchange="idagven.selectedIndex=0"  onKeyDown="if (event.keyCode == 13) document.form.porciento_ganancia.select();" value="{nombre}" size="20" maxlength="45"></td>
	</tr>
	<tr>
      <th class="vtabla">Tipo expendio</th>
	  <td class="vtabla">
		<select name="tipo_expendio" class="insert" id="tipo_expendio">
		  <!-- START BLOCK : tipo -->
          <option value="{valuetipo}">{valuetipo} - {nametipo}</option>
          <!-- END BLOCK : tipo -->
	      <!-- START BLOCK : tipo_selected -->
          <option value="{valuetipo}" selected>{valuetipo} - {nametipo}</option>
		  <!-- END BLOCK : tipo_selected -->
        </select></td>
	</tr>
	<tr>
		<th class="vtabla">Direcci&oacute;n</th>
		<td class="vtabla"><textarea name="direccion" cols="32" rows="4" wrap="VIRTUAL" class="insert" id="direccion"  onKeyDown="if (event.keyCode == 13) document.form.porciento_ganancia.select();">{direccion}</textarea></td>
	</tr>
	<tr>
		<th class="vtabla">Porcentaje de ganancia</th>
		<td class="vtabla"><input name="porciento_ganancia" type="text" class="vinsert" id="porciento_ganancia" value="{porciento_ganancia}" size="6" maxlength="6" {readonly}  onKeyDown="if (event.keyCode == 13) document.form.importe_fijo.select();"></td>
	</tr>
	<tr>
	  <th class="vtabla">Importe Fijo </th>
	  <td class="vtabla"><input name="importe_fijo" type="text" class="vinsert" id="importe_fijo" value="{importe_fijo}" size="10" maxlength="10"  onKeyDown="if (event.keyCode == 13) document.form.num_cia_exp.select();"></td>
	  </tr>
	<tr>
	  <th class="vtabla">Total Fijo </th>
	  <td class="vtabla"><input name="total_fijo" type="radio" value="FALSE" {checked_false}>
	    No
	      <input name="total_fijo" type="radio" value="TRUE" {checked_true}>
	      Si</td>
	  </tr>
	<tr>
	  <th class="vtabla">Expendio por Notas </th>
	  <td class="vtabla"><input name="notas" type="radio" value="FALSE"{nota_f}>
	    No
	      <input name="notas" type="radio" value="TRUE"{nota_t}>
	      Si</td>
	  </tr>
	<tr>
	  <th class="vtabla">Autoriza Devoluci&oacute;n</th>
	  <td class="vtabla"><input name="aut_dev" type="radio" value="FALSE"{dev_f}>
	    No
	      <input name="aut_dev" type="radio" value="TRUE"{dev_t}>
	      Si</td>
	  </tr>
	<tr>
	  <th class="vtabla">Agente de Ventas </th>
	  <td class="vtabla"><select name="idagven" id="idagven" class="insert">
	    <option value="">-</option>
	    <!-- START BLOCK : agente -->
		<option value="{id}"{selected}>{nombre}</option>
		<!-- END BLOCK : agente -->
	    </select></td>
	  </tr>
	<tr>
	  <th class="vtabla">Panader&iacute;a a la cual reparte</th>
	  <td class="vtabla"><input name="num_cia_exp" type="text" id="num_cia_exp" class="vinsert" onkeydown="if(event.keyCode==13)document.form.num_referencia.select()" value="{num_cia_exp}" size="3" maxlength="3" /></td>
	  </tr>
</table>
<p align="center"><img src="./menus/delete.gif" align="middle">&nbsp;<input name="button" type="button" class="boton" onClick='document.location="./pan_exp_mod.php"' value="Regresar">
&nbsp;&nbsp;&nbsp;&nbsp;<img src="./menus/insert.gif" align="middle">&nbsp;<input name="enviar" type="button" class="boton" value="Modificar Expendio" onclick='valida_registro()'>
</p>
</form>
<script language="JavaScript" type="text/JavaScript">
window.onload=document.form.num_referencia.select();
</script>

</td>
</tr>
</table>
<!-- END BLOCK : modificacion -->