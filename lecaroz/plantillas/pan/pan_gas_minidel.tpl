<link href="/styles/tablas.css" rel="stylesheet" type="text/css">
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
<form name="form" method="post" action="./pan_gas_minidel.php">
<input name="bandera" type="hidden" id="bandera" value="{bandera}">


<input name="id" type="hidden" id="id" value="{id}">
<input name="codgastos" type="hidden" id="codgastos" value="{codgastos}">
<input name="num_cia" type="hidden" id="num_cia" value="{num_cia}">
<input name="importe" type="hidden" id="importe" value="{importe}">
<input name="fecha" type="hidden" id="fecha" value="{fecha}">
<p><strong><font size="+1">Justifique la eliminación</font></strong></p>
<p><strong><font size="+1">
  <input name="concepto" type="text" id="concepto" size="50" maxlength="49" class="vinsert">
</font></strong></p>
<p><strong><font face="Geneva, Arial, Helvetica, sans-serif">&iquest;Esta segura de borrar el gasto?</font></strong></p>
<p>
  <img src="./menus/delete.gif" width="16" height="16">
  <input name="" type="button" class="boton" onClick="self.close()" value="Cancelar">
&nbsp;&nbsp;&nbsp;
<img src="./menus/insert.gif" width="16" height="16">
<input name="Submit" type="button" class="boton" value="Aceptar" onClick="if(document.form.concepto.value==''){ alert('Introduzca la justificación'); document.form.concepto.select();} else document.form.submit();">
</p>
</form>
</td>
</tr>
</table>
<!-- END BLOCK : question -->