<script type="text/javascript" language="JavaScript">
	function valida_registro() {
		if(document.form.campo0.value <= 0) {
			alert('Debe especificar un codigo para la materia prima');
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
<form name="form" method="post" action="alta_catalogos.php?tabla={tabla}">
<table class="tabla">
    <tr>
      <th class="vtabla">C&oacute;digo de materia prima</th>
      <td class="vtabla"><input name="campo0" type="text" class="insert" id="campo0" value="{id}"></td>
    </tr>
    <tr>
      <th class="vtabla">Nombre Materia Prima </th>
      <td><input name="campo1" type="text" class="insert" id="campo1"></td>
    </tr>
    <tr>
      <th class="vtabla">Unidad de Consumo </th>
      <td class="vtabla">
        <select name="campo2" class="insert" id="campo2">
          <option value="{valueunidad}" selected>{idunidad} - {nameunidad}</option>
	  <!-- START BLOCK : unidad -->
          <option value="{valueunidad}">{idunidad} - {nameunidad}</option>
          <!-- END BLOCK : unidad -->
        </select>	  </td>
    </tr>
    <tr>
      <th class="vtabla">Tipo de Materia Prima </th>
	   <td class="vtabla">
        <select name="campo3" class="insert" id="campo3">
          <option value="{valuetipo}" selected>{idtipo} - {nametipo}</option>
	  <!-- START BLOCK : tipo -->
          <option value="{valuetipo}">{idtipo} - {nametipo}</option>
          <!-- END BLOCK : tipo -->
        </select>	  </td>
    </tr>
    <tr>
      <th class="vtabla">Materia prima controlada</th>
      <td class="vtabla"><p>
        <label>
        <input name="campo4" type="radio" value="FALSE" checked>
  No</label>

        <label>
        <input type="radio" name="campo4" value="TRUE">
  Si</label>
        <br>
      </p></td>
    </tr>
    <tr>
      <th class="vtabla">Presentaci&oacute;n</th>
      <td class="vtabla">
	    <select name="campo5" class="insert" id="campo5">
          <option value="{valuepresentacion}" selected>{idpresentacion} - {namepresentacion}</option>
	  <!-- START BLOCK : presentacion -->
          <option value="{valuepresentacion}">{idpresentacion} - {namepresentacion}</option>
          <!-- END BLOCK : presentacion -->
        </select>      </td>
    </tr>
    <tr>
      <th class="vtabla">Proceso autom&aacute;tico de pedidos</th>
      <td class="vtabla"><p>
        <label>
        <input name="campo6" type="radio" value="FALSE">
  No</label>
        
        <label>
        <input name="campo6" type="radio" value="TRUE" checked>
  Si</label>
        <br>
      </p></td>
    </tr>
    <tr>
      <th class="vtabla">% de incremento al promedio </th>
      <td class="vtabla"><input name="campo7" type="text" class="insert" id="campo7"></td>
    </tr>
    <tr>
      <th class="vtabla">N&uacute;mero de entregas para el pedido de fin de mes </th>
      <td><input name="campo8" type="text" class="insert" id="campo8">
        <input name="campo10" type="hidden" id="campo10" value="0"></td>
    </tr>
    <tr>
      <th class="vtabla">Tipo</th>
      <td><input name="campo9" type="radio" value="TRUE" checked="checked" />
        Panader&iacute;a
          <input name="campo9" type="radio" value="FALSE" />
          Rosticer&iacute;a</td>
    </tr>
    <tr>
      <th class="vtabla">Producto con existencia </th>
      <td><input name="campo11" type="radio" value="FALSE" checked="checked" />
        Si
          <input name="campo11" type="radio" value="TRUE" />
          No</td>
    </tr>
</table>
<p>
    <img src="./menus/insert.gif" align="middle">&nbsp;&nbsp;<input type="button" class="boton" value="Alta de Materia Prima" onclick='valida_registro()'><br><br>
    <img src="./menus/delete.gif" align="middle">&nbsp;&nbsp;<input type="button" class="boton" value="Borrar formulario" onclick='borrar()'>
</p>
</form>
</td>
</tr>
</table>