
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
<p class="title">Modificación de Pesos por Compa&ntilde;&iacute;a </P>
<form name="form" method="post" action="./actualiza_ros_pesos.php?tabla={tabla}" onKeyDown="if (event.keyCode == 13) document.form.enviar.focus();">
<table class="tabla">
	<tr class="tabla">
		<th class="tabla" align="center">CATÁLOGO DE PESOS POR COMPAÑÍA</th>
	</tr>
	<tr>
		<td class="tabla" align="center"><strong><font size="+1">{num_cia}&#8212;{nom_cia}</font></strong>
	      <input name="num_cia" type="hidden" id="num_cia" value="{num_cia}">	  </td>
	</tr>
	
</table>
<br>
<table class="tabla">
    <tr class="rtabla">
      <th class="tabla" align="center" colspan="2">C&oacute;digo materia prima </th>
      <th class="tabla" align="center">Peso m&aacute;ximo</th>
      <th class="tabla" align="center">Peso m&iacute;nimo</th>
      <th class="tabla" align="center">Eliminar</th>
    </tr>
	<!-- START BLOCK : rows -->
    <tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
	  <th class="rtabla">{codmp} <input name="codmp{i}" type="hidden" value="{codmp}">
      <input name="id{i}" type="hidden" value="{id}"></th>
	  <th class="vtabla">{nom_mp}</th>
      <td class="tabla"><input name="peso_maximo{i}" type="text" class="insert" id="peso_maximo{i}" value="{peso_maximo}" size="10">      </td>
      <td  class="tabla"><input name="peso_minimo{i}" type="text" class="insert" id="peso_minimo{i}" value="{peso_minimo}" size="10">      </td>
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


