
<!-- START BLOCK : obtener_datos -->
<script type="text/javascript" language="JavaScript">
	function valida_registro() {
		document.form.submit();
	}
	
	function actualiza_compania(num_cia, nombre) {
		// Arreglo con los nombres de las materias primas
		cia = new Array();				// Materias primas
		<!-- START BLOCK : nombre_cia -->
		cia[{num_cia}] = '{nombre_cia}';
		<!-- END BLOCK : nombre_cia -->
				
		if (num_cia.value > 0) {
			if (cia[num_cia.value] == null) {
				alert("Compañía "+num_cia.value+" no es una rosticería");
				num_cia.value = "";
				nombre.value  = "";
				num_cia.focus();
			}
			else {
				nombre.value   = cia[num_cia.value];
			}
		}
		else if (num_cia.value == "") {
			num_cia.value = "";
			nombre.value  = "";
		}
	}
</script>
<link href="file:///C|/Documents%20and%20Settings/John%20Talbain/Escritorio/Lecaroz/styles/tablas.css" rel="stylesheet" type="text/css">



<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
<p class="title">Catálogo de Precios de Materias Primas para Rosticerias</p>
<form name="form" action="./ros_precios_cap.php" method="get">
<table class="tabla">
	<tr>
		<th class="tabla" align="center">Compañía</div></th>
	</tr>
	<tr>
		<td class="tabla" align="left">
<input name="num_cia" type="text" class="insert" id="num_cia" size="5" maxlength="5" onchange="actualiza_compania(this,form.nombre_cia);">			
<input name="nombre_cia" type="text" disabled class="nombre" size="50">	  </td>
	</tr>
	<tr>
	  <th class="tabla">Proveedor</th>
	  </tr>
	<tr>
	  <td class="tabla"><select name="num_proveedor" class="insert" id="num_proveedor">
	    <option value="13" selected>13 POLLOS GUERRA</option>
		<option value="482">482 CENTRAL DE POLLOS Y CARNES S.A. DE C.V.</option>
		<option value="1386">1386 EL RANCHERITO S.A. DE C.V.</option>
		</select></td>
	  </tr>
</table>    
<br><input type="button" name="enviar2" class="boton" value="Continuar" onclick='valida_registro()'>
</form>
</td>
</tr>
</table>
<script language="JavaScript" type="text/JavaScript">window.onload=document.form.num_cia.select();</script>

<!-- END BLOCK : obtener_datos -->


<!-- START BLOCK : captura -->
<script language="JavaScript" type="text/JavaScript">
function actualiza_materia(codmp, nombre) {
	// Arreglo con los nombres de las materias primas
	mp = new Array();				// Materias primas
	<!-- START BLOCK : nombre_mp -->
	mp[{codmp}] = '{nombre_mp}';
	<!-- END BLOCK : nombre_mp -->
			
	if (codmp.value > 0) {
		if (mp[codmp.value] == null) {
			alert("Materia Prima "+codmp.value+" no encontrada");
			codmp.value = "";
			nombre.value  = "";
			codmp.focus();
		}
		else {
			nombre.value   = mp[codmp.value];
		}
	}
	else if (codmp.value == "") {
		codmp.value = "";
		nombre.value  = "";
	}
}

	function valida_registro() 
	{
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
<tr>
<td align="center" valign="middle">
<form name="form" action="./insert_ros_precios_cap.php?tabla={tabla}" method="post">
<table class="tabla">
  <tr class="tabla">
    <th class="tabla"><font size="+1">Catálogo de Precios de Materias Primas</font></th>
    <th class="tabla"><font size="+1">Proveedor</font></th>
  </tr>
  <tr class="tabla">
    <td class="tabla"><font size="+1">{num_cia}&#8212;{nombre_cia}</font></td>
    <td class="tabla"><font size="+1">{num_pro}&#8212;{nombre_pro}</font></td>
  </tr>
</table>



<table class="tabla">
      <tr>
        <th class="tabla" align="center">Código Materia Prima</th>
        <th class="tabla" align="center">Precio compra </th>
        <th class="tabla" align="center">Precio venta </th>
        <th class="tabla" align="center">Nombre Alternativo </th>
      </tr>
	  <!-- START BLOCK : rows -->
      <tr>
        <td class="tabla" align="center"><input name="codmp{i}" type="text" class="insert" id="codmp{i}" size="5" maxlength="5" onkeydown="if(event.keyCode == 13){document.form.precio_compra{i}.select();}" onchange="actualiza_materia(this,form.nombre_mp{i})">
        <input name="nombre_mp{i}" type="text" disabled class="nombre" id="nombre_mp{i}" size="25">
        <input name="proveedor{i}" type="hidden" class="insert" id="proveedor{i}" value="{num_proveedor}" size="5" maxlength="5">
        <input name="num_cia{i}" type="hidden" class="insert" id="num_cia{i}"  value="{num_cia}" size="5" maxlength="5"></td>
        <td class="tabla" align="center"><input name="precio_compra{i}" type="text" class="insert" id="precio_compra{i}" size="15" onkeydown="if(event.keyCode == 13){document.form.precio_venta{i}.select();}"></td>
        <td class="tabla" align="center"><input name="precio_venta{i}" type="text" class="insert" id="precio_venta{i}" size="15" onkeydown="if(event.keyCode == 13){document.form.nombre{i}.select();}"></td>
        <td class="tabla" align="center"><input name="nombre{i}" type="text" id="nombre{i}" class="vinsert" size="40" maxlength="100" onkeydown="if(event.keyCode == 13){document.form.codmp{next}.select();}" /></td>
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
<!-- START BLOCK : captura -->



