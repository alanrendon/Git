<script type="text/javascript" language="JavaScript">
	function valida_registro() {
		if(document.form.num_sindicato.value <= 0) {
			alert('Debe especificar un sindicato');
			document.form.num_sindicato.select();
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
<p class="title">MODIFICACI&Oacute;N AL CAT&Aacute;LOGO DE SINDICATOS </P>
<form name="form" method="post" action="./actualiza_ban_sind.php?tabla={tabla}" onKeyDown="if (event.keyCode == 13) document.form.enviar.focus();">
  <br>
<table class="tabla">
    <tr class="rtabla">
      <th class="tabla" align="center">N&uacute;mero</th>
      <th class="tabla" align="center">Nombre Sindicato </th>
      <th class="tabla" align="center">Eliminar</th>
    </tr>
	<!-- START BLOCK : rows -->
    <tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
	  <th class="rtabla">{idsindicato} 
	    
        <input name="idsindicato{i}" type="hidden" id="idsindicato{i}" value="{idsindicato}"></th>

      <td class="tabla"><span class="rtabla">
        <input name="nombre_sindicato{i}" type="text" class="insert" id="nombre_sindicato{i}" value="{nombre_sindicato}" size="50">
      </span> </td>
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

