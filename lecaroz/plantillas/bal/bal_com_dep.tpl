<!-- START BLOCK : enviar_archivo -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
<p class="title">Vac&iacute;ado de Dep&oacute;sitos de COMETRA </p>
<form name="form" enctype="multipart/form-data" method="post" action="">
<table class="tabla">
<tr>
 <input name="MAX_FILE_SIZE" type="hidden" value="1048576">
 <th class="vtabla">Archivo de depositos de COMETRA:</th>
 <td class="vtabla"><input name="userfile" type="file" class="vinsert" id="userfile" size="40"></td>
</tr>
</table>
<p>
	<input name="enviar" type="submit" class="boton" id="enviar" value="Enviar">
</p>
</form>
</td>
</tr>
</table>
 <!-- END BLOCK : enviar_archivo -->

<!-- START BLOCK : mensaje -->
<p>{mensaje}</p>
<input type="button" value="Regresar" onClick="parent.history.back()">
<!-- END BLOCK : mensaje -->

<!-- START BLOCK : listado -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="top">
<table class="tabla">
  <tr>
    <th class="tabla" colspan="2" scope="col">N&uacute;mero y Nombre de Compa&ntilde;&iacute;a </th>
    <th class="tabla" scope="col">Fecha</th>
    <th class="tabla" scope="col">C&oacute;digo</th>
    <th class="tabla" scope="col">Importe</th>
  </tr>
  <!-- START BLOCK : fila -->
  <tr>
    <td class="vtabla">{num_cia}</td>
    <td class="vtabla">{nombre_cia}</td>
    <td class="tabla">{fecha}</td>
    <td class="tabla">{codigo}</td>
    <td class="tabla"><b>{importe}</b></td>
  </tr>
  <!-- END BLOCK : fila -->
</table>

</td>
</tr>
</table>
<!-- END BLOCK : listado -->