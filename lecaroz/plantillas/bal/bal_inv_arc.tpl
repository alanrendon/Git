<!-- START BLOCK : enviar_archivo -->
<link href="../../styles/tablas.css" rel="stylesheet" type="text/css">
<link href="../../styles/pages.css" rel="stylesheet" type="text/css">
<link href="../../styles/impresion.css" rel="stylesheet" type="text/css">

<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
<p class="title">Vac&iacute;ado de Archivo PALM </p>
<form name="form" enctype="multipart/form-data" method="post" action="./bal_inv_arc.php">
<table class="tabla">
<tr>
 <input name="MAX_FILE_SIZE" type="hidden" value="1048576">
 <th class="vtabla">Archivo PALM:</th>
 <td class="vtabla"><input name="userfile" type="file" class="vinsert" id="userfile" size="40" readonly="true"></td>
</tr>
<tr>
  <th class="vtabla">Mes</th>
  <td class="vtabla"><select name="mes" class="insert" id="mes">
    <option value="1" {1}>ENERO</option>
    <option value="2" {2}>FEBRERO</option>
    <option value="3" {3}>MARZO</option>
    <option value="4" {4}>ABRIL</option>
    <option value="5" {5}>MAYO</option>
    <option value="6" {6}>JUNIO</option>
    <option value="7" {7}>JULIO</option>
    <option value="8" {8}>AGOSTO</option>
    <option value="9" {9}>SEPTIEMBRE</option>
    <option value="10" {10}>OCTUBRE</option>
    <option value="11" {11}>NOVIEMBRE</option>
    <option value="12" {12}>DICIEMBRE</option>
  </select></td>
</tr>
<tr>
  <th class="vtabla">A&ntilde;o</th>
  <td class="vtabla"><input name="anio" type="text" class="insert" id="anio" value="{anio}" size="4" maxlength="4"></td>
</tr>
</table>
<p>
	<input name="enviar" type="submit" class="boton" id="enviar" value="Enviar">
</p>
</form>
</td>
</tr>
</table>
<script language="javascript" type="text/javascript">
	window.onload = document.form.userfile.focus();
</script>
 <!-- END BLOCK : enviar_archivo -->

<!-- START BLOCK : mensaje -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
<p>{mensaje}</p>
<input type="button" value="Regresar" onClick="parent.history.back()">
</td>
</tr>
</table>
<!-- END BLOCK : mensaje -->

