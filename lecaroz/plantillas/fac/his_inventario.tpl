<!-- START BLOCK : obtener_compania -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
<p class="title">Captura de Historico de Inventario</p>
<form name="form" method="get" action="./his_inventario.php" onKeyDown="if (event.keyCode == 13) this.submit();">
<table class="tabla">
  <tr>
    <th class="vtabla">Compa&ntilde;&iacute;a</th>
    <td class="vtabla"><input name="compania" type="text" class="insert" id="compania" size="5" maxlength="5"></td>
    <th class="vtabla">Fecha</th>
    <td class="vtabla"><input name="fecha" type="text" class="insert" id="fecha" size="12" maxlength="12"></td>
  </tr>
</table>
</form>
</td>
</tr>
</table>
<!-- END BLOCK : obtener_compania -->

<!-- START BLOCK : captura -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
<p class="title">Captura de Historico de Inventario</p>
<script language="javascript" type="text/javascript">
	function actualiza_campos(codmp, nombre, unidad, existencia, fecha, fechain, fechaout, numcia) {
		// Arreglo con los nombres de las materias primas
		mp = new Array();				// Materias primas
		unidad_consumo = new Array();	// Unidad de consumo
		<!-- START BLOCK : nombre_mp -->
		mp[{codmp}] = '{nombre_mp}';
		unidad_consumo[{codmp}] = '{unidad}';
		<!-- END BLOCK : nombre_mp -->
				
		if (codmp.value > 0) {
			if (mp[codmp.value] == null) {
				alert("Código "+codmp.value+" no esta en el catálogo de materias primas");
				codmp.value      = "";
				nombre.value     = "";
				unidad.value     = "";
				existencia.value = "";
				fecha.value      = "";
				fechain.value    = "";
				fechaout.value   = "";
				numcia.value     = "";
				codmp.focus();
			}
			else {
				nombre.value   = mp[codmp.value];
				unidad.value   = unidad_consumo[codmp.value];
				fecha.value    = document.form.fecha.value;
				fechain.value  = document.form.fecha.value;
				fechaout.value = document.form.fecha.value;
				numcia.value   = document.form.compania.value;
			}
		}
		else if (codmp.value == "") {
			codmp.value      = "";
			nombre.value     = "";
			unidad.value     = "";
			existencia.value = "";
			fecha.value      = "";
			fechain.value    = "";
			fechaout.value   = "";
			numcia.value     = "";
		}
	}
	
	function valida_registro() {
		if (confirm("¿Son correctos los datos?"))
			document.form.submit();
		else
			document.form.codmp0.select();
	}
	
	function borrar() {
		if (confirm("Se borraran todos los datos del formulario capturado. ¿Desea continuar?"))
			document.form.reset();
		else
			document.form.codmp0.select();
	}
</script>
<form name="form" method="post" action="./his_inventario.php?ok=1">
<table class="tabla">
  <tr>
    <th class="vtabla">Compa&ntilde;&iacute;a</th>
    <td class="vtabla"><input name="compania" type="hidden" id="compania" value="{num_cia}">
      {num_cia} - {nombre_cia} </td>
    <th class="vtabla">Fecha</th>
    <td class="vtabla"><input name="fecha" type="hidden" id="fecha" value="{fecha}">
      {fecha}</td>
  </tr>
</table>
<br>
<table class="tabla">
  <tr>
    <th class="tabla" scope="col">C&oacute;digo</th>
    <th class="tabla" scope="col">Nombre de Materia Prima y Unidad </th>
    <th class="tabla" scope="col">Existencia</th>
  </tr>
  <!-- START BLOCK : fila -->
  <tr>
    <td class="tabla"><input name="codmp{i}" type="text" class="insert" id="codmp{i}" size="5" maxlength="5" onChange="actualiza_campos(form.codmp{i},form.nombre{i},form.unidad{i},form.existencia{i},form.fecha{i},form.fecha_entrada{i},form.fecha_salida{i},form.num_cia{i})" onKeyDown="if (event.keyCode == 13) form.existencia{i}.select();"></td>
    <td class="tabla"><input name="nombre{i}" type="text" class="vnombre" id="nombre{i}" size="30" readonly><input name="unidad{i}" type="text" class="vnombre" id="unidad{i}" size="10" readonly></td>
    <td class="tabla"><input name="existencia{i}" type="text" class="insert" id="existencia{i}" size="12" maxlength="12" onChange="var value=parseFloat(this.value); this.value=value.toFixed(2);" onKeyDown="if (event.keyCode == 13) form.codmp{next}.select();">
      <input name="num_cia{i}" type="hidden" id="num_cia{i}">
      <input name="fecha{i}" type="hidden" id="fecha{i}">
      <input name="fecha_entrada{i}" type="hidden" id="fecha_entrada{i}">
      <input name="fecha_salida{i}" type="hidden" id="fecha_salida{i}"></td>
  </tr>
  <!-- END BLOCK : fila -->
</table>

<p>
  <img src="./menus/delete.gif" width="16" height="16">
  <input type="button" class="boton" value="Borrar" onClick="borrar();">
&nbsp;&nbsp;&nbsp;&nbsp;
<img src="./menus/insert.gif" width="16" height="16">
<input name="enviar" type="button" class="boton" id="enviar" value="Capturar inventario" onClick="valida_registro();">
</p>
</form>
</td>
</tr>
</table>
<!-- END BLOCK : captura -->
