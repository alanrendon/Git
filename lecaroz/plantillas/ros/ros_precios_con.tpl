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
<link href="/styles/tablas.css" rel="stylesheet" type="text/css">

<!-- START BLOCK : obtener_datos -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">


<p class="title">Consulta al catálogo de Precios por Compa&ntilde;&iacute;a </P>
<form name="form" method="get" action="./ros_precios_con.php" onKeyDown="if (event.keyCode == 13) document.form.enviar.focus();">
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
<script language="javascript" type="text/javascript">
window.onload = document.form.num_cia.select();
</script>
<!-- END BLOCK : obtener_datos -->

<!-- START BLOCK : listado -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">

<p class="title">OFICINAS ADMINISTRATIVAS MOLLENDO S. DE R.L. DE C.V. </P>
<p class="title">Consulta al catálogo de Precios por Compa&ntilde;&iacute;a </P>
<form name="form" method="post" action="./ros_precios_mod.php?tabla={tabla}" onKeyDown="if (event.keyCode == 13) document.form.enviar.focus();">
<table class="tabla">
	<tr class="tabla">
		<th class="tabla" align="center">Precios de Materias Primas</th>
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
      <th class="tabla" align="center" colspan="2">C&oacute;digo materia prima </th>
      <th class="tabla" align="center">Proveedor</th>
      <th class="tabla" align="center">Nombre Alternativo </th>
      <th class="tabla" align="center">Precio Compra</th>
      <th class="tabla" align="center">Precio Venta</th>
      <th class="tabla" align="center">Modificar</th>
    </tr>
	<!-- START BLOCK : rows -->
    <tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
	  <th class="rtabla">{codmp} 
	  <input name="codmp{i}" type="hidden" value="{codmp}">
	  <input name="id{i}" type="hidden" value="{id}">	  </th>
	  <th class="vtabla">{nom_mp}</th>
      <td class="vtabla">{num_pro} {nombre_pro} </td>
      <td class="vtabla">{nombre_alt}
        <input name="nombre_alt{i}" type="hidden" id="nombre_alt{i}" value="{nombre_alt}" /></td>
      <td class="rtabla">{precio_compra1}
        <input name="precio_compra{i}" type="hidden" id="precio_compra{i}" value="{precio_compra}">      </td>
      <td  class="rtabla">{precio_venta1}
        <input name="precio_venta{i}" type="hidden" id="precio_venta{i}" value="{precio_venta}">      </td>
      <td class="tabla"><input type="checkbox" name="mod{i}" onClick="if (this.checked==true) document.form.modificar{i}.value=1; else if(this.checked==false)document.form.modificar{i}.value=0;"><input type="hidden" name="modificar{i}" value="0"></td>
    </tr>
	<!-- END BLOCK : rows -->
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

