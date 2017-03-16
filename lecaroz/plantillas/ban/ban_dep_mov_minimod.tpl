<!-- START BLOCK : cerrar -->
<script language="javascript" type="text/javascript">
	function cerrar() {
		window.opener.opener.document.location = "./ban_mov_pen.php#{num_cia}";
		window.opener.close();
		window.opener.opener.document.location.reload();
		self.close();
	}
	
	window.onload = cerrar();
</script>
<!-- END BLOCK : cerrar -->
<!-- START BLOCK : modificar -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Modificar Dep&oacute;sito</p>
  <form name="form" method="post">
  <input name="id" type="hidden" value="{id}">
  <input name="num_cia" type="hidden" value="{num_cia}">
  <input name="fecha_con" type="hidden" value="{fecha_con}">
  <input name="importe" type="hidden" id="importe" value="{importe}"> 
  <input name="cod_banco" type="hidden" id="cod_banco" value="{cod_banco}"> 
  <table class="tabla">
    <tr>
      <th class="tabla" scope="col">Fecha</th>
      <th class="tabla" scope="col">Codigo de movimiento</th>
      <th class="tabla" scope="col">Concepto</th>
      <th class="tabla" scope="col">Importe</th>
      </tr>
    <tr>
      <td class="tabla"><input name="fecha" type="text" class="insert" id="fecha" onChange="actualiza_fecha(this)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39 || event.keyCode == 37) form.concepto.select();" value="{fecha}" size="10" maxlength="10"></td>
      <td class="tabla"><select name="cod_mov" class="insert" id="cod_mov">
        <!-- START BLOCK : cod_mov -->
		<option value="{cod_mov}" {selected}>{cod_mov} - {descripcion}</option>
		<!-- END BLOCK : cod_mov -->
      </select></td>
      <td class="tabla"><input name="concepto" type="text" class="vinsert" id="concepto" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39 || event.keyCode == 37) form.fecha.select();" value="{concepto}" size="50" maxlength="200"></td>
      <td class="rtabla"><strong>{fimporte}</strong></td>
      </tr>
  </table>  
  <p>
    <input name="Button" type="button" class="boton" value="Cancelar" onClick="self.close()">
&nbsp;&nbsp;    
<input name="Submit2" type="button" class="boton" value="Modificar" onClick="valida_registro()">
  </p>
  </form>
  </td>
</tr>
</table>
<script language="javascript" type="text/javascript">
	function valida_registro() {
		if (document.form.fecha.value == "") {
			alert("Debe especificar la fecha del depósito");
			document.form.fecha.select();
			return false;
		}
		else if (document.form.concepto.value == "") {
			alert("Debe poner el concepto del importe");
			document.form.concepto.select();
			return false;
		}
		else {
			if (confirm("¿Desea modificar el depósilto y conciliarlo?"))
				document.form.submit();
			else {
				document.form.fecha.select();
			}
		}
	}
	
	window.onload = document.form.fecha.select();
</script>
<!-- END BLOCK : modificar -->
