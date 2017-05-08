<!-- START BLOCK : cerrar -->
<script language="javascript" type="text/javascript">
	function cerrar() {
		window.opener.document.location.reload();
		self.close();
	}
	
	window.onload = cerrar();
</script>
<!-- END BLOCK : cerrar -->

<!-- START BLOCK : modificar -->
<script language="javascript" type="text/javascript">
	function actualiza_mp(codmp, nombre, precio, precio_nuevo) {
		// Arreglo con los nombres de las materias primas
		mp = new Array();				// Materias primas
		precio_mp = new Array();		// Precios por materia prima
		<!-- START BLOCK : nombre_mp -->
		mp[{codmp}]     = '{nombre_mp}';
		precio_mp[{codmp}] = {precio_actual};
		<!-- END BLOCK : nombre_mp -->
				
		if (parseInt(codmp.value) > 0) {
			if (mp[parseInt(codmp.value)] == null) {
				alert("Código "+parseInt(codmp.value)+" no esta en el catálogo de materias primas para rosticerías");
				codmp.value = temp_codmp.value;
				codmp.focus();
				return false;
			}
			else {
				codmp.value = parseInt(codmp.value);
				nombre.value = mp[parseInt(codmp.value)];
				if (precio_mp[parseInt(codmp.value)] > 0) {
					precio.value = precio_mp[parseInt(codmp.value)].toFixed(2);
					precio_nuevo.value = precio_mp[parseInt(codmp.value)].toFixed(2);
					return;
				}
				else
					precio.value = precio_mp[parseInt(codmp.value)];
			}
		}
		else if (codmp.value == "") {
			codmp.value = "";
			nombre.value = "";
			precio.value = "";
			precio_nuevo.value = "";
			codmp.focus();
			return false;
		}
	}
	
	function valida_registro() {
		document.form.submit();
	}
</script>
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
<p class="title">Precios de Venta </p>
<table class="tabla">
  <tr>
    <th class="vtabla" scope="row">Compa&ntilde;&iacute;a</th>
    <td class="vtabla"><strong>{num_cia} - {nombre_cia} </strong></td>
  </tr>
</table>
<br>
<form name="form" method="post" action="./ros_prv_minimod.php?tabla={tabla}">
<input name="temp" type="hidden">
<table class="tabla">
  <tr>
    <th class="tabla" scope="col">C&oacute;digo</th>
    <th class="tabla" scope="col">Descripci&oacute;n</th>
    <th class="tabla" scope="col">Precio Actual</th>
    <th class="tabla" scope="col">Precio Nuevo </th>
  </tr>
  <!-- START BLOCK : fila -->
  <tr>
    <td class="tabla"><input name="codmp{i}" type="text" class="insert" id="codmp{i}" onFocus="form.temp.value=this.value" onChange="if (isInt(this,form.temp)) actualiza_mp(this,form.nombre{i},form.precio_actual{i},form.precio_compra{i})" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) form.precio_venta{i}.select();
else if (event.keyCode == 37) form.precio_venta{back}.select();
else if (event.keyCode == 38) form.codmp{back}.select();
else if (event.keyCode == 40) form.codmp{next}.select();" size="4" maxlength="4"></td>
    <td class="tabla"><input name="nombre{i}" type="text" disabled="true" class="vnombre" id="nombre{i}" size="20"></td>
    <td class="tabla"><input name="precio_actual{i}" type="text" disabled="true" class="rnombre" id="precio_actual{i}" size="10" maxlength="10"></td>
    <td class="tabla"><input name="precio_venta{i}" type="text" class="rinsert" id="precio_venta{i}" onFocus="form.temp.value=this.value" onChange="if (isFloat(this,2,form.temp)) return true;" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) form.codmp{next}.select();
else if (event.keyCode == 37) form.codmp{i}.select();
else if (event.keyCode == 38) form.precio_venta{back}.select();
else if (event.keyCode == 40) form.precio_venta{next}.select();" size="10" maxlength="10"></td>
  </tr>
  <!-- END BLOCK : fila -->
</table>
<p><input name="" type="button" class="boton" onClick="self.close();" value="Cerrar Ventana">
&nbsp;&nbsp;<input name="enviar" type="button" class="boton" value="Siguiente" onClick="valida_registro()">
</p>
</form>
</td>
</tr>
</table>
<!-- END BLOCK : modificar -->
