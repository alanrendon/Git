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
<p class="title">CONSULTA DE NOTARIOS </p>
<form name="form" method="post" action="./actualiza_ban_notario.php?tabla={tabla}">
  <br>
<table class="tabla">
    <tr class="rtabla">
      <th class="tabla" align="center">N&uacute;mero</th>
      <th class="tabla" align="center">Nombre notario(a) </th>
      <th class="tabla" align="center">Notario público número</th>
      <th class="tabla" align="center">Eliminar</th>
    </tr>
	<!-- START BLOCK : rows -->
    <tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
	  <th class="rtabla">{cod_notario} 
	    
        <input name="cod_notario{i}" type="hidden" id="cod_notario{i}" value="{cod_notario}"></th>

      <td class="tabla"><span class="rtabla">
        <input name="nombre_notario{i}" type="text" class="vinsert" id="nombre_notario{i}" value="{nombre_notario}" size="50" onKeyDown="if(event.keyCode==13) form.nombre_notario{next}.select();">
      </span> </td>
	  <td class="tabla"><input name="num_notario{i}" type="text" class="insert" id="num_notario{i}" value="{num_notario}" size="5"></td>
      <td class="tabla"><input type="checkbox" name="mod{i}" onClick="if (this.checked==true) document.form.eliminar{i}.value=1; else if(this.checked==false)document.form.eliminar{i}.value=0;"><input type="hidden" name="eliminar{i}" value="0"></td>
      
    </tr>
	<!-- END BLOCK : rows -->
	<!-- START BLOCK : notario -->
		<input name="cont" type="hidden" value="{cont}">
	<!-- END BLOCK : notario -->
</table>
  
  <p>
<input type="button" name="enviar2" class="boton" value="Regresar" onclick='parent.history.back()'>&nbsp;&nbsp;
<input type="button" name="enviar2" class="boton" value="Modificar" onclick='document.form.submit();'>
</p>
</form>
<script language="JavaScript" type="text/JavaScript">
window.onload=document.form.nombre_notario0.select();
</script>

</td>
</tr>
</table>
