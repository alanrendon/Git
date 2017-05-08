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
	function revisa()
	{
		if(parseFloat(document.form.temp.value) > 0) document.form.enviar2.disabled=false;
		else document.form.enviar2.disabled=true; 
	}

	
</script>
<link href="/styles/tablas.css" rel="stylesheet" type="text/css">


<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
<p class="title">Usuarios con permiso de reportar efectivos </P>


<form name="form" method="post" action="./adm_efe_con.php">
<input name="temp" type="hidden" id="temp" value="0">
  <br>
<table class="tabla">
    <tr class="rtabla">
      <th class="tabla" align="center"><input name="cont" type="hidden" id="cont" value="{count}">
        Nombre </th>
      <th class="tabla" align="center">Eliminar</th>
    </tr>
	<!-- START BLOCK : rows -->
    <tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
	  <td class="vtabla"><strong>{nombre}</strong>
        <span class="rtabla">
        <input name="id{i}" type="hidden" id="id{i}" value="{id}">
        </span> </td>
      <td class="tabla"><input type="checkbox" name="mod{i}" onClick="if (this.checked==true){ document.form.modificar{i}.value=1; document.form.temp.value=parseFloat(document.form.temp.value) + 1 } else if(this.checked==false){document.form.modificar{i}.value=0; document.form.temp.value=parseFloat(document.form.temp.value) - 1};}" onChange="revisa();"><input type="hidden" name="modificar{i}" value="0"></td>
    </tr>
	<!-- END BLOCK : rows -->
</table>
  
  <p>

<input type="button" name="enviar2" class="boton" value="Eliminar" onclick='document.form.submit();' disabled>
</p>
</form>

</td>
</tr>
</table>


