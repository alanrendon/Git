<script type="text/javascript" language="JavaScript">
function valida_registro() 
	{
			if(document.form.num_cia.value <= 0)
			{
				alert('Debe especificar una compañia');
				document.form.num_cia.select();
			}
			else if(document.form.codigo.value <= 0)
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

<!-- tabla movimiento_gastos -->
<form name="form" action="./inser_pan_pro_altas.php?tabla={tabla}" method="post" onKeyDown="if (event.keyCode == 13) document.form.enviar.focus();">
<table class="tabla">
      <tr>
        <th class="tabla" align="center">C&oacute;digo materia prima </th>
        <th class="tabla" align="center">Concepto</th>
        <th class="tabla" align="center">Total</th>
      </tr>
	  <!-- START BLOCK : rows -->
      <tr>
	    <td  align="center" class="tabla">
            <input name="textfield5{i}" type="text" class="insert" id="textfield5{i}" size="15">          </td>
        <td align="center" class="tabla">
            <input name="textfield8{i}" type="text" class="insert" id="textfield8{i}" size="15">          </td>
        <td><div align="center"></div></td>
	  </tr>
	  <!-- END BLOCK : rows -->
  </table>    
    <p>
  <img src="./menus/insert.gif" align="middle">&nbsp;&nbsp;<input type="button" name="enviar" class="boton" value="Capturar" onclick='valida_registro()'>
  <br><br>
  <img src="./menus/delete.gif" align="middle">&nbsp;&nbsp;<input type="button" class="boton" value="Borrar formulario" onclick='borrar()'>
  </p>

</form>