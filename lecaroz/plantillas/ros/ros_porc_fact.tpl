<script type="text/javascript" language="JavaScript">
	function calcular_porcentaje(porcentaje1, porcentaje2) {
		var new_porcentaje;
		var por1 = parseFloat(porcentaje1.value);
		var por2 = parseFloat(porcentaje2.value);
		
		if (por1 > 0 && por1 <= 100) {
			new_porcentaje = 100 - por1;
			porcentaje1.value = por1.toFixed(2);
			porcentaje2.value = new_porcentaje.toFixed(2);
		}
		else if (por1 < 0 || por1 > 100) {
			alert("Porcentaje no puede ser menor a 0 o mayor a 100");
			porcentaje1.value = "";
			porcentaje2.value = "";
			porcentaje1.focus();
		}
		else if (por1 == "") {
			porcentaje1.value = "";
			porcentaje2.value = "";
		}
	}
	
	function valida_registro() {
		if (confirm("¿Son correctos los datos del formulario?"))
			document.form.submit();
		else
			document.form.num_cia.select();
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


<p class="title">Porcentajes de Facturas</p>
<form name="form" action="./ros_porc_fact.php?tabla={tabla}" method="post" onKeyDown="if (event.keyCode == 13) document.form.enviar.focus();">
<table class="tabla">
	<th class="tabla" align="center">Compa&ntilde;&iacute;a</div></th>
	<th class="tabla" align="center">Porcentaje 1(13) </div></th>
	<th class="tabla" align="center">Porcentaje 2 (795) </div></th>
	<!-- START BLOCK : rows -->
	  <tr>
        
        <td class="tabla" align="center">
          <input name="num_cia{i}" type="text" class="insert" id="num_cia{i}" size="15">
        </div></td>

        <td class="tabla" align="center">
          <input name="porcentaje_13{i}" type="text" class="insert" id="porcentaje_13{i}" size="15" onChange="calcular_porcentaje(this,form.porcentaje_795{i})">
        </div></td>
        
        <td class="tabla" align="center">
          <input name="porcentaje_795{i}" type="text" class="insert" id="porcentaje_795{i}" size="15" readonly>
        </div></td>
      </tr>
	<!-- END BLOCK : rows -->	  
</table>
	  <br><br>
      <p>
	  <img src="./menus/insert.gif" align="middle">&nbsp;&nbsp;<input type="button" name="enviar" class="boton" value="Capturar porcentajes" onclick='valida_registro()'>
	  <br><br>
	  <img src="./menus/delete.gif" align="middle">&nbsp;&nbsp;<input type="button" class="boton" value="Borrar formulario" onclick='borrar()'>
	  </p>
	 
</form>


</td>
</tr>
</table>