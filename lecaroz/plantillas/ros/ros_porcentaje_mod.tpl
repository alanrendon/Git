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
<link href="/styles/tablas.css" rel="stylesheet" type="text/css">

<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
<form name="form" method="post" action="./actualiza_ros_porcentaje.php?tabla={tabla}" onKeyDown="if (event.keyCode == 13) document.form.enviar.focus();">
 
<table class="tabla">
    <tr class="rtabla">
      <th class="tabla" align="center" colspan="2">Compa&ntilde;&iacute;a</th>
      <th class="tabla" align="center">Porcentaje facturacion</th>
      <th class="tabla" align="center">Porcentaje contado </th>
      <th class="tabla" align="center">Eliminar</th>
    </tr>
	<!-- START BLOCK : rows -->
    <tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
	  <th class="rtabla">{num_cia} 
	    <input name="num_cia{i}" type="hidden" id="num_cia{i}" value="{num_cia}">
      <input name="id{i}" type="hidden" value="{id}"></th>
	  <th class="vtabla">{nom_cia}</th>
      <td class="tabla"><span class="rtabla">
        <input name="porcentaje1{i}" type="text" id="porcentaje1{i}" value="{porcentaje1}" size="5" class="insert">
      </span> </td>
      <td  class="tabla"><span class="rtabla">
        <input name="porcentaje2{i}" type="text" id="porcentaje2{i}" value="{porcentaje2}" size="5" class="insert">
      </span> </td>
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

