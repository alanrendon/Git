<!-- START BLOCK : enviar_archivo -->
<link href="file:///C|/Documents%20and%20Settings/John%20Talbain/Escritorio/Lecaroz/styles/tablas.css" rel="stylesheet" type="text/css">
<link href="file:///C|/Documents%20and%20Settings/John%20Talbain/Escritorio/Lecaroz/styles/pages.css" rel="stylesheet" type="text/css">
<link href="file:///C|/Documents%20and%20Settings/John%20Talbain/Escritorio/Lecaroz/styles/impresion.css" rel="stylesheet" type="text/css">

<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
<p class="title">Vac&iacute;ado de Dep&oacute;sitos de COMETRA </p>
<form name="form" enctype="multipart/form-data" method="post" action="">
<table class="tabla">
<tr>
 <input name="MAX_FILE_SIZE" type="hidden" value="1048576">
 <th class="vtabla">Archivo de dep&oacute;sitos de COMETRA:</th>
 <td class="vtabla"><input name="userfile" type="file" class="vinsert" id="userfile" size="40" readonly="true"></td>
</tr>
<tr>
  <th class="vtabla">Cuenta</th>
  <td class="vtabla"><select name="cuenta" class="insert" id="cuenta">
    <option value="1" selected>BANORTE</option>
    <option value="2">SANTANDER SERFIN</option>
  </select></td>
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

<!-- START BLOCK : listado -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="top">

<table width="100%">
  <tr>
    <td>&nbsp;</td>
    <td class="print_encabezado" align="center">Oficinas Administrativas MOLLENDO S. de R.L. y C.V. </td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td width="20%">&nbsp;</td>
    <td width="60%" class="print_encabezado" align="center">Dep&oacute;sitos COMETRA<br>
      al {dia} de {mes} de {anio} </td>
    <td width="20%">&nbsp;</td>
  </tr>
</table>
  <br>

<table width="100%" class="print">
  <tr>
    <th width="5%" class="print" scope="row">Cia.</th>
    <th width="15%" class="print" scope="row">Cuenta</th>
    <th width="35%" class="print" scope="row">Nombre</th>
    <th width="20%" class="print">Codigo</th>
    <th width="15%" class="print">Importe</th>
    <th width="10%" class="print">Fecha</th>
  </tr>
  <!-- START BLOCK : fila -->
  <tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
    <td class="print" scope="row">{num_cia}</td>
    <td class="print" scope="row">{cuenta}</td>
    <td class="vprint" scope="row">{nombre}</td>
    <td class="vprint">{cod_mov} {descripcion} </td>
    <td class="rprint">{importe}</td>
    <td class="rprint">{fecha}</td>
  </tr>
  <!-- END BLOCK : fila -->
  <tr>
    <th colspan="4" class="rprint" scope="row">Total</th>
    <th class="rprint_total">{total}</th>
	<th class="rprint" scope="row">&nbsp;</th>
  </tr>
  <tr>
    <td colspan="7" scope="row">&nbsp;</td>
    </tr>
</table>

<p>
  <input name="terminar" type="button" class="boton" id="terminar" value="Terminar" onClick="document.location = './ban_dep_cap.php'">
&nbsp;&nbsp;  
<input name="imprimir" type="button" class="boton" id="imprimir" value="Imprimir" onClick="window.print()">
</p></td>
</tr>
</table>
<script language="javascript" type="text/javascript">window.onload = window.print();</script>
<!-- END BLOCK : listado -->