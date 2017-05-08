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
<p class="title">CONSULTA AL CAT&Aacute;LOGO DE PUESTOS </p>
<form name="form" method="post" action="./fac_pue_mod.php?tabla={tabla}" onkeydown="if (event.keyCode == 13) document.form.enviar.focus();">
  <br>
<table class="tabla">
    <tr class="rtabla">
      <th class="tabla" align="center">C&oacute;digo
        <input name="cont" type="hidden" id="cont" value="{count}">
        <input name="temp" type="hidden" id="temp" value="0"></th>
      <th class="tabla" align="center">Descripci&oacute;n del puesto </th>
      <th class="tabla" align="center">Sueldo</th>
	  <th class="tabla" align="center">Modificar</th>
    </tr>
	<!-- START BLOCK : rows -->
    <tr onmouseover="overTR(this,'#ACD2DD');" onmouseout="outTR(this,'');">
	  <th class="rtabla">{cod_puestos} 
	    
	    <input name="cod_puestos{i}" type="hidden" id="cod_puestos{i}" value="{cod_puestos}">
	  </th>
	
      <td class="vtabla"><strong>{descripcion}</strong>
        <input name="descripcion{i}" type="hidden" id="descripcion{i}" value="{descripcion}">
      </td>
      <td class="rtabla">{sueldo1}<span class="rtabla">
        <input name="sueldo{i}" type="hidden" id="sueldo{i}" value="{sueldo}">
      </span></td>
      <td class="tabla"><input type="checkbox" name="mod{i}" onclick="if (this.checked==true){ document.form.modificar{i}.value=1; document.form.temp.value=parseFloat(document.form.temp.value) + 1 } else if(this.checked==false){document.form.modificar{i}.value=0;document.form.temp.value=parseFloat(document.form.temp.value) - 1;}" onchange="revisa();"><input type="hidden" name="modificar{i}" value="0"></td>
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

