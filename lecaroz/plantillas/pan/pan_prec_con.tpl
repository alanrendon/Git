<!-- tabla catalogo_productos_proveedor -->
<script type="text/javascript" language="JavaScript">
	function valida_registro() {
		if(document.form.num_cia.value <= 0) {
			alert('Debe especificar una compañía');
			document.form.num_cia.select();
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

<!-- START BLOCK : obtener_datos -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
<p class="title">Consulta al catálogo de Control de la Producci&oacute;n </P>
<form name="form" method="get" action="./pan_prec_con.php" onKeyDown="if (event.keyCode == 13) document.form.enviar.focus();">
  <table class="tabla">
  <tr class="tabla">
    <th class="tabla">Número de Compa&ntilde;&iacute;a 
      <input name="num_cia" type="text" id="num_cia" size="5" maxlength="5" class="insert"></th>
  </tr>
</table>

  
  <p>
  <img src="./menus/insert.gif" align="middle">&nbsp;&nbsp;<input type="button" name="enviar" class="boton" value="Consultar" onclick='valida_registro()'>

  </p>
</form>
</td>
</tr>
</table>
<!-- END BLOCK : obtener_datos -->

<!-- START BLOCK : listado -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
<form name="form" method="post" action="./pan_prec_mod.php?tabla={tabla}" onKeyDown="if (event.keyCode == 13) document.form.enviar.focus();">
<table class="tabla">
	<tr class="tabla">
		<th class="tabla" align="center">CATÁLOGO DE CONTROL DE LA PRODUCCION</th>
	</tr>
	<tr>
		<td class="tabla" align="center"><strong><font size="+1">{num_cia}&#8212;{nom_cia}</font></strong>
		  <input name="num_cia" type="hidden" id="num_cia" value="{num_cia}">
	  <input name="cont" type="hidden" id="cont" value="{count}">	  </td>
	</tr>
	
</table>
<br>
<table class="tabla">
    <tr class="rtabla">
      <th class="tabla" align="center" colspan="2">C&oacute;digo Producto </th>
      <th class="tabla" align="center">Precio Raya </th>
      <th class="tabla" align="center">Porcentaje Raya </th>
      <th class="tabla" align="center">Precio de Venta</th>
      <th class="tabla" align="center">Número de orden</th>
      <th class="tabla" align="center">Modificar</th>
    </tr>

	<!-- START BLOCK : turnos -->
	<tr>
	  <th class="tabla" colspan="7">{turno}</th>
	</tr>

	<!-- START BLOCK : rows -->
    <tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
	  <th class="rtabla">{cod_producto} 
	    <input name="cod_producto{i}" type="hidden" id="cod_producto{i}" value="{cod_producto}">
	    <input name="id{i}" type="hidden" id="id{i}" value="{id}">
		<input name="cod_turno{i}" type="hidden" id="cod_turno{i}" value="{turno}">
	  </th>
	  <th class="vtabla">{nom_prod}</th>
      <td class="rtabla">{precio_raya1}
        <input name="precio_raya{i}" type="hidden" id="precio_raya{i}" value="{precio_raya}">
      </td>
      <td  class="rtabla">{porcentaje1}
        %
        <input name="porcentaje{i}" type="hidden" id="porcentaje{i}" value="{porcentaje}">
      </td>
      <td class="rtabla">{precio_venta1}
        <input name="precio_venta{i}" type="hidden" id="precio_venta{i}" value="{precio_venta}">
      </td>
	  <td class="tabla">{orden}<span class="rtabla">
	    <input name="orden{i}" type="hidden" id="orden{i}" value="{orden}">
	  </span></td>
	  
      <td class="tabla"><input type="checkbox" name="mod{i}" onClick="if (this.checked==true) document.form.modificar{i}.value=1; else if(this.checked==false)document.form.modificar{i}.value=0;"><input type="hidden" name="modificar{i}" value="0"></td>
    </tr>
	<!-- END BLOCK : rows -->
	<!-- END BLOCK : turnos -->

</table>
  
  <p>
<input type="button" name="enviar2" class="boton" value="Regresar" onclick='parent.history.back()'>&nbsp;&nbsp;
<input type="button" name="enviar2" class="boton" value="Modificar" onclick='document.form.submit();'>
</p>
</form>
</td>
</tr>
</table>
<!-- END BLOCK : listado -->

