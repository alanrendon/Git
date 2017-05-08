<!-- tabla catalogo_productos_proveedor -->
<link href="/styles/tablas.css" rel="stylesheet" type="text/css">

<!-- START BLOCK : listado -->
<script language="JavaScript" type="text/JavaScript">
function revisa()
{
if(parseFloat(document.form.temp.value) > 0) document.form.enviar2.disabled=false;
else 
document.form.enviar2.disabled=true; 
}
</script>

<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
<p class="title">Consulta al catálogo de Notarios </P>

<form name="form" method="post" action="./ban_notario_mod.php?tabla={tabla}" onKeyDown="if (event.keyCode == 13) document.form.enviar.focus();">
  <br>
<table class="tabla">
    <tr class="rtabla">
      <th class="tabla" align="center">N&uacute;mero
        <input name="cont" type="hidden" id="cont" value="{count}">
        <input name="temp" type="hidden" id="temp" value="0"></th>
      <th class="tabla" align="center">Nombre del Notario(a) </th>
      <th class="tabla" align="center">Notario público número</th>
      <th class="tabla" align="center">Modificar</th>
    </tr>
	<!-- START BLOCK : rows -->
    <tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
	  <th class="rtabla">{cod_notario} 
	    
	    <input name="cod_notario{i}" type="hidden" id="cod_notario{i}" value="{cod_notario}">	  </th>
	
      <td class="vtabla"><strong>{nombre_notario}</strong>
        <input name="nombre_notario{i}" type="hidden" id="nombre_notario{i}" value="{nombre_notario}">
      </td>
      <td class="tabla"><span class="rtabla">
        <strong>{num_notario}</strong>        
        <input name="num_notario{i}" type="hidden" id="num_notario{i}" value="{num_notario}">
      </span></td>
      <td class="tabla"><input type="checkbox" name="mod{i}" onClick="if (this.checked==true){ document.form.modificar{i}.value=1; document.form.temp.value=parseFloat(document.form.temp.value) + 1 } else if(this.checked==false){document.form.modificar{i}.value=0;document.form.temp.value=parseFloat(document.form.temp.value) - 1};}" onChange="revisa();"><input type="hidden" name="modificar{i}" value="0" ></td>
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

