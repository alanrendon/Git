<!-- tabla catalogo_productos_proveedor -->
<script language="JavaScript" type="text/JavaScript">
function revisa()
{
if(parseFloat(document.form.temp.value) > 0) document.form.enviar2.disabled=false;
else 
document.form.enviar2.disabled=true; 
}
</script>
<link href="/styles/tablas.css" rel="stylesheet" type="text/css">
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
<p class="title">CONSULTA AL CAT&Aacute;LOGO DE SINDICATOS </P>
<form name="form" method="post" action="./ban_sind_mod.php?tabla={tabla}" onKeyDown="if (event.keyCode == 13) document.form.enviar.focus();">
  <br>
<table class="tabla">
    <tr class="rtabla">
      <th class="tabla" align="center">N&uacute;mero
        <input name="cont" type="hidden" id="cont" value="{count}">
        <input name="temp" type="hidden" id="temp" value="0"></th>
      <th class="tabla" align="center">Nombre del Sindicato </th>
      <th class="tabla" align="center">Modificar</th>
    </tr>
	<!-- START BLOCK : rows -->
    <tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
	  <th class="rtabla">{idsindicato} 
	    
	    <input name="idsindicato{i}" type="hidden" id="idsindicato{i}" value="{idsindicato}">
	  </th>
	
      <td class="vtabla"><strong>{nombre_sindicato}</strong>
        <input name="nombre_sindicato{i}" type="hidden" id="nombre_sindicato{i}" value="{nombre_sindicato}">
      </td>
      <td class="tabla"><input type="checkbox" name="mod{i}" onClick="if (this.checked==true){ document.form.modificar{i}.value=1; document.form.temp.value=parseFloat(document.form.temp.value) + 1 } else if(this.checked==false){document.form.modificar{i}.value=0;document.form.temp.value=parseFloat(document.form.temp.value) - 1};}" onChange="revisa();"><input type="hidden" name="modificar{i}" value="0"></td>
    </tr>
	<!-- END BLOCK : rows -->
</table>
  
  <p>

<input type="button" name="enviar2" class="boton" value="Modificar" onclick='document.form.submit();' disabled>
</p>
</form>
</td>
</tr>
</table>
<!-- END BLOCK : listado -->

