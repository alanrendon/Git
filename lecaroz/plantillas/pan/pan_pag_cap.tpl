<!-- tabla control pago menu panaderia -->
<script type="text/javascript" language="JavaScript">
	function valida_registro() {
		if(document.form.num_cia{1}.value <= 0) {
			alert('Debe especificar una compañia');
			document.form.num_cia{1}.select();
		}
			else {
				if (confirm("¿Son correctos los datos del formulario?"))
					document.form.submit();
				else
					document.form.num_cia{1}.select();
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
<form name="form" method="post" action="./insert_pan_pag_cap.php?tabla={tabla}" onKeyDown="if (event.keyCode == 13) document.form.enviar.focus();">
  <table class="tabla">
    <tr>
      <th class="vtabla">Compañ&iacute;a</th>
      <th class="vtabla">N&uacute;mero de Trabajador</th>
      <th class="vtabla">Importe</th>
      <th class="vtabla">Fecha</th>
    </tr>
	<!-- START BLOCK : rows -->    
	<tr>
      <td class="vtabla" onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');">
	  	<input name="num_cia{i}" type="text" class="insert" id="num_cia{i}" maxlength="5"></th>
      <td class="vtabla" onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');"><div align="center">
        <input name="numcontrolprestamo{i}" type="text" class="insert" id="numcontrolprestamo{i}" maxlength="6"></td>
      <td class="vtabla" onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');">
	  	<input name="importe{i}" type="text" class="insert" id="importe2" maxlength="10"></td>
      <td class="vtabla" onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');">
	  	<input name="fecha{i}" type="text" class="insert" id="fecha{i}" maxlength="10"></td>
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