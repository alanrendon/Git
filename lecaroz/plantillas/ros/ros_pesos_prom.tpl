<script type="text/javascript" language="JavaScript">
	function valida_registro() {
		if(document.form.num_cia.value <= 0) {
			alert('Debe especificar una compañia');
			document.form.num_cia.select();
		}
		else {
			if (confirm("¿Son correctos los datos del formulario?"))
				document.form.submit();
			else
				document.form.num_cia.select();
		}
	}

	function borrar() {
		if (confirm("¿Desea borrar el formulario?")) {
			document.form.reset();
			document.form.num_cia.select();
		}
		else
			document.form.num_cia.select();
	}
</script>
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">

<form name="form" action="./insert_ros_pesos_prom.php?tabla={tabla}" method="post" onKeyDown="if (event.keyCode == 13) document.form.enviar.focus();">
<table class="tabla">
      <tr>
        <th class="vtabla" align="center">
		Compa&ntilde;&iacute;a</div>
		</th>
        <td class="tabla" align="center">
          <input name="num_cia" type="text" class="insert" id="num_cia" size="10" maxlength="5">
        </div></td>
        <th class="vtabla" align="center">Proveedor</div></th>
        <td class="tabla" align="center">
		<input name="num_proveedor" type="text" class="insert" id="num_proveedor" value="13" size="10" maxlength="5">
		</td>
      </tr>

</table>
      <table class="tabla">
        <tr>
          <th class="tabla" align="center">C&oacute;digo materia prima </div></th>
          <th class="tabla" align="center">Peso m&aacute;ximo </div></th>
          <th class="tabla" align="center">Peso m&iacute;nimo </div></th>
        </tr>
		<!-- START BLOCK : rows -->
        <tr>
          <td class="tabla" align="center">
            <input name="codmp{i}" type="text" class="insert" id="codmp{i}" size="20" maxlength="5">
          </div></td>
          <td class="tabla" align="center">
            <input name="peso_max{i}" type="text" class="insert" id="peso_max{i}" maxlength="6">
          </div></td>
          <td class="tabla" align="center">
            <input name="peso_min{i}" type="text" class="insert" id="peso_min{i}" maxlength="6">
          </div></td>
        </tr>
		<!-- END BLOCK : rows -->
  </table>
	  <p>
	  <img src="./menus/insert.gif" align="middle">&nbsp;&nbsp;<input type="button" name="enviar" class="boton" value="Capturar" onclick='valida_registro()'>
	  <br><br>
	  <img src="./menus/delete.gif" align="middle">&nbsp;&nbsp;<input type="button" class="boton" value="Borrar formulario" onclick='borrar()'>
	  </p>
</form>
</td>
</tr>
</table>
