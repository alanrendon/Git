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
<form name="form" method="post" action="./actualiza_pan_prec.php?tabla={tabla}" onKeyDown="if (event.keyCode == 13) document.form.enviar.focus();">
<table class="tabla">
	<tr class="tabla">
		<th class="tabla" align="center">CATÁLOGO DE CONTROL DE LA PRODUCCI&Oacute;N</th>
	</tr>
	<tr>
		<td class="tabla" align="center"><strong><font size="+1">{num_cia}&#8212;{nom_cia}</font></strong>
	      <input name="num_cia" type="hidden" id="num_cia" value="{num_cia}">	  </td>
	</tr>
	
</table>
<br>
<table class="tabla">
    <tr class="rtabla">
      <th class="tabla" align="center" colspan="2">C&oacute;digo Produto </th>
      <th class="tabla" align="center">Turno</th>
      <th class="tabla" align="center">Precio Raya</th>	  
      <th class="tabla" align="center">Porcentaja Raya </th>
      <th class="tabla" align="center">Precio Venta </th>
      <th class="tabla" align="center">N&uacute;mero de orden </th>
      <th class="tabla" align="center">Eliminar</th>
    </tr>
	<!-- START BLOCK : rows -->
    <tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
	  <th class="rtabla">{cod_producto} 
	    <input name="cod_producto{i}" type="hidden" id="cod_producto{i}" value="{cod_producto}">
      <input name="id{i}" type="hidden" value="{id}"></th>
	  <th class="vtabla">{nom_producto}</th>
      <th class="vtabla">{turno}</th>
	  <td class="tabla"><input name="precio_raya{i}" type="text" class="insert" id="precio_raya{i}" value="{precio_raya}" size="10">      </td>
      <td  class="tabla"><input name="porc_raya{i}" type="text" class="insert" id="porc_raya{i}" value="{porc_raya}" size="10">      </td>
      <td class="tabla"><input name="precio_venta{i}" type="text" class="insert" id="precio_venta{i}" value="{precio_venta}" size="10">      </td>
      <td class="tabla"><input name="orden{i}" type="text" class="insert" id="orden{i}" value="{orden}" size="10">      </td>
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