<script type="text/javascript" language="JavaScript">
	function valida_registro() {
		if(document.form.campo0.value <= 0) {
			alert('Debe especificar un código para el gasto');
			document.form.campo0.select();
		}
		else {
			if (confirm("¿Son correctos los datos del formulario?"))
				document.form.submit();
			else
				document.form.campo0.select();
		}
	}
	
	function borrar() {
		if (confirm("¿Desea borrar el formulario?")) {
			document.form.reset();
			document.form.campo0.select();
		}
		else
			document.form.campo0.select();
	}
</script>
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
<p class="title">Alta de Gastos</p>
<form action="./alta_catalogos.php?tabla={tabla}" method="post" name="form" id="form" onKeyDown="if (event.keyCode == 13) document.form.enviar.focus();">
  <input name="campo5" type="hidden" id="campo5" value="2" />
  <table class="tabla">
    <tr>
      <th class="vtabla">C&oacute;digo de gasto </th>
      <td class="vtabla"><input name="campo0" type="text" class="insert" id="campo0" size="5" maxlength="5" value={id}></td>
    </tr>
    <tr>
      <th class="vtabla">Descripci&oacute;n</th>
      <td class="vtabla"><input name="campo1" type="text" class="insert" id="campo1" size="50" maxlength="50"></td>
    </tr>
    <tr>
      <th class="vtabla">C&oacute;digo estado de resultados </th>
      <td class="vtabla"><select name="campo2" class="insert" id="campo2" >
        <option value="{value}" selected>{num} - {name}</option>
        <!-- START BLOCK : codigo -->
        <option value="{value}">{num} - {name}</option>
		<!-- END BLOCK : codigo -->
      </select></td>
    </tr>
    <tr>
      <th class="vtabla">Tipo de gasto </th>
      <td class="vtabla"><input name="campo3" type="radio" value="0" checked>
      &nbsp;Variable&nbsp;&nbsp;<input name="campo3" type="radio" value="1">
      &nbsp;Fijo</td>
    </tr>
    <tr>
      <th class="vtabla">Aplicaci&oacute;n del gasto</th>
      <td class="vtabla"><input name="campo4" type="radio" value="FALSE" checked>
        Panader&iacute;a
          <input name="campo4" type="radio" value="TRUE">
          Reparto</td>
    </tr>
  </table>
  <p>
    <img src="./menus/insert.gif" align="middle">&nbsp;&nbsp;<input type="button" name="enviar" class="boton" value="Alta de Gasto" onclick="valida_registro()"><br><br>
    <img src="./menus/delete.gif" align="middle">&nbsp;&nbsp;<input type="button" class="boton" value="Borrar formulario" onclick='borrar()'>
  </p>
</form>
</td>
</tr>
</table>
</body>
</html>
