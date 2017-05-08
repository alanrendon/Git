<link href="/styles/tablas.css" rel="stylesheet" type="text/css">
<script type="text/javascript" language="JavaScript">
	function valida_registro() {
		if(document.form.num_proveedor.value <= 0) {
			alert('Debe especificar un proveedor');
			document.form.num_proveedor.select();
		}
		else {
				document.form.submit();
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
<p class="title">MODIFICACIÓN DE TANQUES DE GAS</p>
<form name="form" method="post" action="./actualiza_fac_gas.php?tabla={tabla}">
<table class="tabla">
	<tr class="tabla">
		<th class="tabla" align="center">COMPA&Ntilde;&Iacute;A</th>
	</tr>
	<tr>
		<td class="tabla" align="center"><strong><font size="+1">{num_cia}&#8212;{nom_cia}</font></strong>
		  <input name="num_cia" type="hidden" id="num_cia" value="{num_cia}">
		  <input name="temp" type="hidden" id="temp"></td>
	</tr>
	
</table>
<br>
<table class="tabla">
    <tr class="rtabla">
      <th class="tabla" align="center">N&uacute;mero de Tanque</th>
      <th class="tabla" align="center">Nombre</th>
      <th class="tabla" align="center">Contenido</th>
      <th class="tabla" align="center">Eliminar</th>
    </tr>
	<!-- START BLOCK : rows -->
    <tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
	  <th class="tabla">{num_tanque} 
        <input name="id{i}" type="hidden" value="{id}"></th>
	 <td class="tabla"><input name="nombre{i}" type="text" class="insert" id="nombre{i}" value="{nombre}" size="20" maxlength="200" onChange="this.value=this.value.toUpperCase()"></td>
      <td class="tabla"><input name="capacidad{i}" type="text" class="insert" id="capacidad{i}" value="{capacidad}" size="10" onFocus="form.temp.value=this.value" onChange="valor=isFloat(this,2,form.temp);"</td>
      <td class="tabla"><input type="checkbox" name="mod{i}" onClick="if (this.checked==true) document.form.eliminar{i}.value=1; else if(this.checked==false)document.form.eliminar{i}.value=0;"><input type="hidden" name="eliminar{i}" value="0"></td>
    </tr>
	<!-- END BLOCK : rows -->
	<!-- START BLOCK : contador -->
		<input name="cont" type="hidden" value="{cont}">
	<!-- END BLOCK : contador -->
</table>
  
  <p>
<input type="button" name="enviar2" class="boton" value="Regresar" onclick='parent.history.back()'>&nbsp;&nbsp;
<input type="button" name="enviar2" class="boton" value="Modificar" onclick='document.form.submit();'>
</p>
</form>
</td>
</tr>
</table>