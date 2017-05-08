<!-- START BLOCK : cerrar -->
<script language="javascript" type="text/javascript">
	function cerrar() {
		window.opener.document.location.reload();
		self.close();
	}
	
	window.onload = cerrar();
</script>
<!-- END BLOCK : cerrar -->
<!-- START BLOCK : question -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
<form name="form" method="post" action="./ban_dot_minidel.php">
<input name="id" value="{id}" type="hidden">
<p><strong><font face="Geneva, Arial, Helvetica, sans-serif">&iquest;Esta seguro de borrar el dep&oacute;sito?</font></strong></p>
<p>
  <img src="./menus/delete.gif" width="16" height="16">
  <input name="" type="button" class="boton" onClick="self.close()" value="Cancelar">
&nbsp;&nbsp;&nbsp;
<img src="./menus/insert.gif" width="16" height="16">
<input name="Submit" type="submit" class="boton" value="Aceptar">
</p>
</form>
</td>
</tr>
</table>
<!-- END BLOCK : question -->