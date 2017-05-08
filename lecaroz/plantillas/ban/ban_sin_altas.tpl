<!-- catalogo sindicatos -->
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
<link href="/styles/tablas.css" rel="stylesheet" type="text/css">

<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
<p class="title">ALTA AL CATÁLOGO DE SINDICATOS</p>

<form name="form" action="./alta_catalogos.php?tabla={tabla}" method="post" onKeyDown="if (event.keyCode == 13) document.form.enviar.focus();">
<table class="tabla">
  <tr>
    <th class="vtabla">C&oacute;digo de Sindicato</th>
    <td class="vtabla"  onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');"><input name="campo0" type="text" class="insert" id="campo0" size="5" maxlength="5" value="{id}"></td>
  </tr>
  <tr>
    <th class="vtabla">Descripci&oacute;n</th>
    <td class="vtabla"  onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');"><input name="campo1" type="text" class="insert" id="campo1" size="50" maxlength="70"></td>
  </tr>
</table>
  <p>
    <img src="./menus/insert.gif" align="middle">&nbsp;&nbsp;<input type="button" name="enviar" class="boton" value="Alta de Sindicato" onclick='valida_registro()'>
    <br><br>
    <img src="./menus/delete.gif" align="middle">&nbsp;&nbsp;<input type="button" class="boton" value="Borrar formulario" onclick='borrar()'>
  </p>
  </form>
</td>
</tr>
</table>
