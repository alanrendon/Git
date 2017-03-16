<!-- tabla control_produccion -->
<script type="text/javascript" language="JavaScript">
	function actualiza_gasto(codgasto, nombre) {
		// Arreglo con los nombres de los gastos
		gas = new Array();
		<!-- START BLOCK : nombre_gasto -->
		gas[{codgasto}] = '{nombre_gasto}';
		<!-- END BLOCK : nombre_gasto -->
		
		if (parseInt(codgasto.value) > 0) {
			if (gas[parseInt(codgasto.value)] == null) {
				alert("Código "+parseInt(codgasto.value)+" no esta en el catálogo de gastos");
				codgasto.value = "";
				nombre.value  = "";
				codgasto.focus();
				return false;
			}
			else {
				codgasto.value = parseFloat(codgasto.value);
				nombre.value  = gas[parseInt(codgasto.value)];
				return;
			}
		}
		else if (codgasto.value == "") {
			codgasto.value = "";
			nombre.value  = "";
			return false;
		}
	}
	
	function valida_registro() {
		if(document.form.tipo_res.value <= 0) {
			alert('Debe especificar codigo');
			document.form.tipo_res.select();
		}
		else if(document.form.descripcion == "") {
			alert('Debe especificar la descripcion');
			document.form.descripcion.select();
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
<link href="/styles/tablas.css" rel="stylesheet" type="text/css">
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
<p class="title">CAPTURA AL CATALOGO DE RESERVAS</P>
<form name="form" action="./insert_cat_reservas.php?tabla={tabla}" method="post" onKeyDown="if (event.keyCode == 13) return false">
<input name="temp" type="hidden">
<table class="tabla">
    <tr>
      <th class="vtabla">Tipo de  Reserva</th>
      <td class="vtabla"><input name="tipo_res" type="text" class="insert" id="tipo_res" onFocus="form.temp.value=this.value" onChange="isInt(this,form.temp);" onKeyDown="if (event.keyCode == 13 || event.keyCode == 40) descripcion.select()" value="{tipo_res}" size="4" maxlength="4"></td>
    </tr>
    <tr>
      <th class="vtabla">Descripci&oacute;n</th>
      <td class="vtabla"><input name="descripcion" type="text" class="vinsert" id="descripcion" onKeyDown="if (event.keyCode == 13 || event.keyCode == 40) codgastos.select();
else if (event.keyCode == 38) tipo_res.select();" size="30" maxlength="30">
	  </td>
    </tr>
    <tr>
      <th class="vtabla">C&oacute;digo de Gasto </th>
      <td class="vtabla"><input name="codgastos" type="text" class="insert" id="codgastos" onFocus="temp.value=this.value" onChange="if (isInt(this,temp)) actualiza_gasto(this,nombre_gasto)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 40) tipo_res.select();
else if (event.keyCode == 38) descripcion.select();" size="4" maxlength="4">
        <input name="nombre_gasto" type="text" class="vnombre" id="nombre_gasto" size="30" maxlength="30"></td>
    </tr>
  </table>
  <p>
  <img src="./menus/insert.gif" align="middle">&nbsp;&nbsp;<input type="button" name="enviar" class="boton" value="Capturar" onclick='valida_registro()'>
  &nbsp;
  <img src="./menus/delete.gif" align="middle">&nbsp;&nbsp;<input type="button" class="boton" value="Borrar formulario" onclick='borrar()'>
  </p>
 
</form>
<script language="javascript" type="text/javascript">window.onload = document.form.tipo_res.select();</script>
</td>
</tr>
</table>