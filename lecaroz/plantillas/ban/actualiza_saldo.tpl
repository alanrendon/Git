<!-- START BLOCK : enviar_archivo -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr align="center" valign="middle">
<td>
<p class="title">Conciliaci&oacute;n Autom&aacute;tica </p>
<form name="form" enctype="multipart/form-data" method="post" action="./actualiza_saldo.php">
<input name="pantalla" type="hidden" value="1">
<table class="tabla">
<tr>
 <input name="MAX_FILE_SIZE" type="hidden" value="5242880">
 <th class="vtabla">Archivo de Movimientos Bancarios:</th>
 <td class="vtabla"><input name="userfile" type="file" class="vinsert" id="userfile" size="40" readonly="true"></td>
</tr>
</table>
<p>
	<input name="enviar" type="button" class="boton" id="enviar" value="Enviar" onClick="valida_registro()">
</p>
</form>
</td>
</tr>
</table>
<script language="javascript" type="text/javascript">
	function valida_registro() {
		document.form.submit();
	}
</script>
 <!-- END BLOCK : enviar_archivo -->
