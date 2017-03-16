<!-- catalogo delegaciones del imss  -->
<script type="text/javascript" language="JavaScript">
	function valida_registro() {
		if(document.form.campo0.value <= 0) {
			alert('Debe especificar un codigo para el contador');
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
<form name="form" action="./alta_catalogos.php?tabla={tabla}" method="post" onKeyDown="if (event.keyCode == 13) document.form.enviar.focus();">
<table class="tabla">
  <tr>
    <th class="vtabla">C&oacute;digo de Delegaci&oacute;n del IMSS</th>
    <td class="vtabla"  onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');"><input name="campo0" type="text" class="insert" id="campo0" size="5" maxlength="5" value="{id}"></td>
  </tr>
  <tr>
    <th class="vtabla">Descripci&oacute;n</th>
    <td class="vtabla"  onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');"><textarea name="campo1" cols="34" rows="3" wrap="VIRTUAL" class="insert"></textarea></td>
  </tr>
</table>
  <p>
    <img src="./menus/insert.gif" align="middle">&nbsp;&nbsp;<input type="button" name="enviar" class="boton" value="Alta de Delegaci&oacute;n" onclick='valida_registro()'>
    <br><br>
    <img src="./menus/delete.gif" align="middle">&nbsp;&nbsp;<input type="button" class="boton" value="Borrar formulario" onclick='borrar()'>
  </p>
  </form>

