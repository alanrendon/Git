<!-- START BLOCK : datos -->
<link href="../../styles/tablas.css" rel="stylesheet" type="text/css">
<link href="../../styles/pages.css" rel="stylesheet" type="text/css">
<link href="../../styles/impresion.css" rel="stylesheet" type="text/css">
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
<p class="title">Vac&iacute;ado de Dep&oacute;sitos de COMETRA </p>
<form name="form" enctype="multipart/form-data" method="post" action="./ban_com_dep_v2.php">
  <table class="tabla">
    <tr>
      <input name="MAX_FILE_SIZE" type="hidden" value="1048576">
      <th class="vtabla">Archivo de dep&oacute;sitos de COMETRA:</th>
      <td class="vtabla"><input name="userfile" type="file" class="vinsert" id="userfile" size="40" readonly="true"></td>
    </tr>
    <tr>
      <th class="vtabla">Cuenta</th>
      <td class="vtabla"><select name="cuenta" class="insert" id="cuenta">
          <option value="1">BANORTE</option>
          <option value="2" selected>SANTANDER SERFIN</option>
      </select></td>
    </tr>
  </table>
  <p>
	<input name="Submit" type="submit" class="boton" value="Siguiente">
</p>
</form>
</td>
</tr>
</table>
<script language="javascript" type="text/javascript">
	window.onload = document.form.userfile.focus();
</script>
<!-- END BLOCK : datos -->
<!-- START BLOCK : listado -->
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

<table width="100%" align="center" class="print">
  <tr>
    <th width="5%" class="print" scope="row">Cia.</th>
    <th width="15%" class="print" scope="row">Cuenta</th>
    <th width="30%" class="print" scope="row">Nombre</th>
    <th width="25%" class="print">Codigo</th>
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
<script language="javascript" type="text/javascript">window.onload = window.print();</script>
<!-- END BLOCK : listado -->
<!-- START BLOCK : faltantes -->
<br style="page-break-after:always;">
<table width="100%">
  <tr>
    <td>&nbsp;</td>
    <td class="print_encabezado" align="center">Oficinas Administrativas Mollendo S. de R.L. y C.V. </td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td width="20%">&nbsp;</td>
    <td width="60%" class="print_encabezado" align="center">Faltantes Capturados el {dia} de {mes} de {anio} </td>
    <td width="20%">&nbsp;</td>
  </tr>
</table>
  <br>
<table width="50%" align="center" class="print">
    <!-- START BLOCK : cia_fal -->
	<tr>
      <th colspan="5" class="print" scope="col"><font size="+1" color="#000000">{num_cia} - {nombre_cia}</font> </th>
    </tr>
    <tr>
      <th width="10%" class="print"><font color="#000000">Fecha</font></th>
      <th width="20%" class="print"><font color="#000000">Dep&oacute;sito</font></th>
	  <th width="20%" class="print"><font color="#000000">Faltante</font></th>
      <th width="20%" class="print"><font color="#000000">Sobrante</font></th>
      <th width="30%" class="print"><font color="#000000">Descripci&oacute;n</font></th>
    </tr>
    <!-- START BLOCK : fila_fal -->
	<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
      <td class="print">{fecha}</td>
	  <td class="rprint">{deposito}</td>
      <td class="rprint"><font color="#0000FF">{faltante}</font></td>
      <td class="rprint"><font color="#FF0000">{sobrante}</font></td>
      <td class="vprint">{descripcion}</td>
    </tr>
	<!-- END BLOCK : fila_fal -->
    <!-- START BLOCK : totales -->
	<tr>
      <th class="rprint"><font color="#000000">Totales</font></th>
      <th class="rprint_total">{deposito}</th>
	  <th class="rprint_total">{faltante}</th>
      <th class="rprint_total">{sobrante}</th>
      <th class="print">&nbsp;</th>
    </tr>
	<tr>
	  <th colspan="2" class="rprint"><font color="#000000">Diferencia</font></th>
	  <th colspan="2" class="print_total">{diferencia}</th>
	  <th class="print">&nbsp;</th>
    </tr>
	<!-- END BLOCK : totales -->
    <tr>
      <td colspan="5">&nbsp;</td>
    </tr>
	<!-- END BLOCK : cia_fal -->
</table>
<br>
<table align="center" class="print">
  <tr>
    <th class="print" scope="col"><font size="+1" color="#000000">Faltantes</font></th>
    <th class="print" scope="col"><font size="+1" color="#000000">Sobrantes</font></th>
    <th class="print" scope="col"><font size="+1" color="#000000">Diferencia</font></th>
  </tr>
  <tr>
    <th class="print"><font size="+1" color="#000000">{faltantes}</font></th>
    <th class="print"><font size="+1" color="#000000">{sobrantes}</font></th>
    <th class="print"><font size="+1" color="#000000">{diferencia}</font></th>
  </tr>
</table>
<!-- END BLOCK : faltantes -->