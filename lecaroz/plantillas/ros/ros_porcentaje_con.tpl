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

<!-- START BLOCK : listado -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
<p class="title">OFICINAS ADMINISTRATIVAS MOLLENDO S. DE R.L. DE C.V. </P>
<p class="title">Consulta al catálogo de Porcentajes de Facturas por Compa&ntilde;&iacute;a </P>

<form name="form" method="post" action="./ros_porcentaje_mod.php?tabla={tabla}" onKeyDown="if (event.keyCode == 13) document.form.enviar.focus();">
  <br>
<table class="tabla">
    <tr class="rtabla">
      <th class="tabla" align="center" colspan="2">Compa&ntilde;&iacute;a
       <input name="cont" type="hidden" id="cont" value="{count}"></th>
      <th class="tabla" align="center">Porcentaje facturacion </th>
      <th class="tabla" align="center">Porcentaje contado </th>
      <th class="tabla" align="center">Modificar</th>
    </tr>
	<!-- START BLOCK : rows -->
    <tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
	  <th class="rtabla">{num_cia} 
	    <input name="num_cia{i}" type="hidden" id="num_cia{i}" value="{num_cia}">
	  <input name="id{i}" type="hidden" value="{id}">
	  </th>
	  <th class="vtabla">{nom_cia}</th>
      <td class="rtabla">{porcentaje1}
        <input name="porcentaje1{i}" type="hidden" id="porcentaje1{i}" value="{porcentaje1}">
      </td>
      <td  class="rtabla">{porcentaje2}
        <input name="porcentaje2{i}" type="hidden" id="porcentaje2{i}" value="{porcentaje2}">
      </td>
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

