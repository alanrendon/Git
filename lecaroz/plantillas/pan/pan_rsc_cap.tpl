<script type="text/javascript" language="JavaScript">
	function valida_registro() {
		
			if (confirm("¿Son correctos los datos del formulario?"))
				document.form.submit();
			else
				document.form.num_cia1.focus();
		
	}
	
	function borrar() {
		if (confirm("¿Desea borrar el formulario?"))
			document.cia.reset();
		else
			document.cia.campo0.focus();
	}
</script>
<link href="../../styles/tablas.css" rel="stylesheet" type="text/css">

<link href="styles/tablas.css" rel="stylesheet" type="text/css">
<form action="insert_pan_rsc_cap.php?tabla={tabla}" method="post" name="form" id="form">
  <table width="200" border="1">
    <tr>
      <th class="tabla" scope="col">Compa&ntilde;ia</th>
      <th class="tabla" scope="col">Remisi&oacute;n</th>
      <th class="tabla" scope="col">Descripci&oacute;n</th>
    </tr>
	<!-- START BLOCK : rows  -->
    <tr>
      <td><input name="num_cia{i}" type="text" class="insert" id="num_cia{i}"></td>
      <td><input name="num_remi{i}" type="text" class="insert" id="num_remi{i}"></td>
      <td><input name="concepto{i}" type="text" class="insert" id="concepto{i}"></td>
    </tr>
	<!-- END BLOCK : rows  -->
  </table>
  <p>
    <img src="./menus/insert.gif" align="middle">&nbsp;&nbsp;<input type="button" name="enviar" class="boton" value="Capturar datos" onclick='valida_registro()'>
    <br><br>
    <img src="./menus/delete.gif" align="middle">&nbsp;&nbsp;<input type="button" class="boton" value="Borrar formulario" onclick='borrar()'>
</p>

</form>
