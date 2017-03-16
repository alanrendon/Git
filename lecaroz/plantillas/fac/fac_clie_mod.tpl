<script type="text/javascript" language="JavaScript">
	
</script>
<link href="/styles/tablas.css" rel="stylesheet" type="text/css">
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">

<p class="title">MODIFICACI&Oacute;N AL CAT&Aacute;LOGO DE CLIENTES </P>
<form name="form" method="post" action="./actualiza_fac_clie.php?tabla={tabla}" >
  <br>
<table class="tabla">
    <tr class="rtabla">
      <th class="tabla" align="center">N&uacute;mero
        <input name="tem" type="hidden" id="tem"></th>
      <th class="tabla" align="center">Nombre</th>
      <th class="tabla" align="center">Direcci&oacute;n</th>
      <th class="tabla" align="center">R.F.C.</th>
      <th class="tabla" align="center">Eliminar</th>


    </tr>
	<!-- START BLOCK : rows -->
    <tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
	  <th class="rtabla">{id} 
	    
        <input name="id{i}" type="hidden" id="id{i}" value="{id}"></th>

      <td class="tabla"><span class="rtabla">
        <input name="nombre{i}" type="text" class="vinsert" id="nombre{i}" value="{nombre}" size="50" maxlength="50" onKeyDown="if (event.keyCode == 13) document.form.direccion{i}.select();">
      </span> </td>
      <td class="tabla"><span class="rtabla">
        <input name="direccion{i}" type="text" class="vinsert" id="direccion{i}" value="{direccion}" size="70" maxlength="70" onKeyDown="if (event.keyCode == 13) document.form.rfc{i}.select();">
      </span></td>
      <td class="tabla">
	  <input name="rfc{i}" type="text" class="vinsert" id="rfc{i}" value="{rfc}" size="13" maxlength="13" onKeyDown="if (event.keyCode == 13) document.form.nombre{next}.select();">	  </td>
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

