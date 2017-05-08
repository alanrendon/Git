<script type="text/javascript" language="JavaScript">
function valida_registro() 
	{
				if(document.form.codigo.value <= 0)
				{
						alert('Debe especificar un código');
						document.form.codigo.select();
				}
				else 
				{
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
<!-- tabla salida_mp -->
     <table class="tabla">
      <tr>
        <th class="vtabla">Compa&ntilde;&iacute;a</th>
        <td  class="vtabla"><input name="textfield" type="text" class="insert"></td>
      </tr>
      <tr>
        <th class="vtabla">C&oacute;digo de la materia prima</th>
        <td  class="vtabla"><input name="textfield2" type="text" class="insert"></td>
      </tr>
      <tr>
        <th class="vtabla">Fecha del movimiento </th>
        <td class="vtabla"><input name="textfield3" type="text" class="insert"></td>
      </tr>
      <tr>
        <th class="vtabla">Concepto</th>
        <td class="vtabla"><input name="textfield4" type="text" class="insert"></td>
      </tr>
      <tr>
        <th class="vtabla">Unidades</th>
        <td class="vtabla"><input name="textfield5" type="text" class="insert"></td>
      </tr>
      <tr>
        <th class="vtabla">Costo Unitario</th>
        <td class="vtabla"><input name="textfield6" type="text" class="insert"></td>
      </tr>
      <tr>
        <th class="vtabla">Costo total </th>
        <td class="vtabla"><input name="textfield7" type="text" class="insert"></td>
      </tr>
    </table>
	<p>
	  <img src="./menus/insert.gif" align="middle">&nbsp;&nbsp;<input type="button" name="enviar" class="boton" value="Capturar" onclick='valida_registro()'>
	  <br><br>
	  <img src="./menus/delete.gif" align="middle">&nbsp;&nbsp;<input type="button" class="boton" value="Borrar formulario" onclick='borrar()'>
	  </p>
      <p>&nbsp;</p>
<p>***se quita el campo que dice numero documento en la tabla*** <br>
  </p>
      