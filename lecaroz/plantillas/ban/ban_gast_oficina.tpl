<script type="text/javascript" language="JavaScript">
	function valida_registro() {/*
		if(document.form.idcia.value <= 0) {
			alert('Debe especificar un numero para la compania');
			document.form.idcia.focus();
		}
		else {*/
			if (confirm("¿Son correctos los datos del formulario?"))
				document.form.submit();
			else
				document.form.idcia.focus();
	/*	}*/
	}
	
	function borrar() {
		if (confirm("¿Desea borrar el formulario?"))
			document.form.reset();
		else
			document.form.idcia.focus();
	}
</script>
<form name="form" action="./insert_ban_gast_oficina.php?tabla=companias" method="post"  onKeyDown="if (event.keyCode == 13) document.form.enviar.focus();">
  <table width="200" border="1" class="tabla">
    <tr class="vtabla">
      <th class="vtabla" scope="col">Compa&ntilde;&iacute;a</th>
      <th class="vtabla" scope="col">Concepto</th>
      <th class="vtabla" scope="col">Importe</th>
      <th class="vtabla" scope="col">Clave</th>
      <th class="vtabla" scope="col">Fecha</th>
    </tr>
	<!-- START BLOCK : rows -->
    <tr class="vtabla" onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');">
      <td onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');">
	  	<input name="num_cia{i}" type="text" class="insert" id="num_cia{i}" size="13" maxlength="5">
  	  </td>
      <td onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');">
	  <select name="concepto{i}" class="insert" id="concepto{i}">
	  	<!-- START BLOCK : codigo -->
		  <option value="{codgastos}" selected>{codgastos} - {descripcion}</option>
  	  	<!-- END BLOCK : codigo -->
	  </select></td>
      <td onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');">
	  	<input name="importe{i}" type="text" class="insert" id="importe{i}" size="15" maxlength="15">
	  </td>
      <td onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');">
      <select name="balance{i}" class="insert" id="balance{i}">
        <option value="true">S=Afecta a balances</option>
        <option value="false">N=No afecta a balances</option>
      </select>
	  </td>
      <td onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');">
	  	<input name="fecha{i}" type="text" class="insert" id="fecha{i}" value="{fecha}" size="10" maxlength="10">
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
