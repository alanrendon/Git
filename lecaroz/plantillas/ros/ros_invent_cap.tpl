<script type="text/javascript" language="JavaScript">
	function valida_registro() 
	{
		if(document.form.num_cia.value <= 0) {
			alert('Debe especificar una compa�ia');
			document.form.num_cia.select();
		}
		else if (confirm("�Son correctos los datos del formulario?"))
				document.form.submit();
			 else
				document.form.num_cia.select();
												
	}

	function borrar() {
		if (confirm("�Desea borrar el formulario?")) {
			document.form.reset();
			document.form.num_cia.select();
		}
		else
			document.form.num_cia.select();
	}
</script>
<!-- tabla inventarios -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
<form name="form" action="./insert_ros_invent_cap.php?tabla={tabla}" method="post" onKeyDown="if (event.keyCode == 13) document.form.enviar.focus();">
<table class="tabla">
	<tr>
		<th class="vtabla" align="center">Compa&ntilde;&iacute;a</th>
		<td class="vtabla" align="center">
		<input name="num_cia" type="text" class="insert" id="num_cia" size="10" maxlength="5">
		</div></td>
	</tr>
</table>
     <table class="tabla">
      <tr>
        <th class="tabla" align="center">C&oacute;digo</div></th>
        <th class="tabla" align="center">Existencia</div></th>
      </tr>
	  <!-- START BLOCK : rows -->
      <tr>
        <td class="tabla" align="center">
          <input name="codmp{i}" type="text" class="insert" id="codmp{i}" maxlength="5">
        </div></td>
        <td class="tabla" align="center">
          <input name="existencia{i}" type="text" class="insert" id="existencia{i}" maxlength="5">
        </div></td>
      </tr>
	  <!-- END BLOCK : rows -->
    </table>    
    <p>
	<img src="./menus/insert.gif" align="middle">&nbsp;&nbsp;<input type="button" name="enviar" class="boton" value="Capturar" onClick='valida_registro()'>
	<br><br>
	<img src="./menus/delete.gif" align="middle">&nbsp;&nbsp;<input type="button" class="boton" value="Borrar formulario" onClick='borrar()'>
	</p>
</form>

</td>
</tr>
</table>