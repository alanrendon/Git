<!-- tabla catalogo_productos_proveedor -->
<script type="text/javascript" language="JavaScript">
	function valida_registro() {
		if(document.form.num_proveedor.value <= 0) {
			alert('Debe especificar un proveedor');
			document.form.num_proveedor.select();
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
<form name="form" method="post" action="./fac_dmp1_altas.php?tabla={tabla}" onkeydown="if (event.keyCode == 13) document.form.enviar.focus();">
<table class="tabla">
  <tr>
    <th class="tabla" scope="row">N&uacute;mero de proveedor </th>
    <td><input name="num_proveedor" type="text" class="insert" id="num_proveedor" size="10" maxlength="4"></td>
  </tr>
</table>
<table class="tabla">
    <tr>
      <th class="tabla" align="center">C&oacute;digo materia prima </th>
      <th class="tabla" align="center">Presentaci&oacute;n</th>
      <th class="tabla" align="center">Contenido</th>
      <th class="tabla" align="center">Precio</th>
      <th class="tabla" align="center">Descuento 1 </th>
      <th class="tabla" align="center">Descuento 2 </th>
      <th class="tabla" align="center">Descuento 3 </th>
      <th class="tabla" align="center">I.V.A.</th>
      <th class="tabla" align="center">IEPS</th>
    </tr>
	<!-- START BLOCK : rows -->
    <tr>
	  <td class="tabla" align="center">
        <input name="codmp{i}" type="text" class="insert" id="codmp{i}" maxlength="4">
      </td>
	  <td class="tabla" align="center"><select name="presentacion{i}" id="presentacion{i}" class="insert">
	  	<!-- START BLOCK : presentacion -->
		<option value="{value}">{text}</option>
		<!-- END BLOCK : presentacion -->
  	</select></td>
      <td class="tabla" align="center">
        <input name="contenido{i}" type="text" class="insert" id="contenido{i}" size="9" maxlength="7">
      </td>
      <td  class="tabla" align="center">
        <input name="precio{i}" type="text" class="insert" id="precio{i}" size="9" maxlength="9">
      </td>
      <td class="tabla" align="center">
        <input name="desc1{i}" type="text" class="insert" id="desc1{i}" size="9" maxlength="6">
      </td>
      <td class="tabla" align="center">
        <input name="desc2{i}" type="text" class="insert" id="desc2{i}" size="9" maxlength="6">
      </td>
      <td class="tabla" align="center">
        <input name="desc3{i}" type="text" class="insert" id="desc3{i}" size="9" maxlength="6">
      </td>
      <td class="tabla" align="center">
        <input name="iva{i}" type="text" class="insert" id="iva{i}" value="16.0" size="9" maxlength="4">
      </td>
      <td class="tabla" align="center">
        <input name="ieps{i}" type="text" class="insert" id="ieps{i}" size="9" maxlength="6">
      </td>
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