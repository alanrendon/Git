<!-- tabla horarios menu provedores y facturas -->
<script type="text/javascript" language="JavaScript">
	function valida_registro() {
		if(document.form.campo0.value <= 0) {
			alert('Debe especificar un número de código');
			document.form.campo0.focus();
		}
		else {
			if (confirm("¿Son correctos los datos del formulario?"))
				document.form.submit();
			else
				document.form.campo0.focus();
		}
	}
	
	function borrar() {
		if (confirm("¿Desea borrar el formulario?"))
			document.form.reset();
		else
			document.form.campo0.focus();
	}
</script>
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
<form name="form" method="post" action="./insercion.php?tabla=catalogohorarios">
<table class="tabla">
    <tr>
      <th class="vtabla">C&oacute;digo</th>
      <td class="vtabla" onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');"><input name="campo0" type="text" size="5" maxlength="5" class="insert"></td>
    </tr>
    <tr>
      <th class="vtabla">Descripci&oacute;n</th>
      <td class="vtabla" onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');"><input name="campo1" type="text" size="10" maxlength="10" class="insert"></td>
    </tr>
    <tr>
      <th class="vtabla">Hora Entrada </th>
      <td class="vtabla" onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');"><input name="campo2" type="text" size="10" maxlength="10" class="insert"></td>
    </tr>
    <tr>
      <th class="vtabla">Hora Salida </th>
      <td class="vtabla" onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');"><input name="campo3" type="text" size="10" maxlength="10" class="insert"></td>
    </tr>
  </table>
  <p>
    <img src="./menus/insert.gif" align="middle">&nbsp;&nbsp;<input type="button" class="boton" value="Alta de Horario" onclick='valida_registro()'><br><br>
    <img src="./menus/delete.gif" align="middle">&nbsp;&nbsp;<input type="button" class="boton" value="Borrar formulario" onclick='borrar()'>
  </p>
</form>
</td>
</tr>
</table>
