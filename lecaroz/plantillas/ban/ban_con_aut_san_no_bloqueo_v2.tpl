<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Untitled Document</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../../styles/tablas.css" rel="stylesheet" type="text/css">
<link href="../../styles/pages.css" rel="stylesheet" type="text/css">
<link href="../../styles/impresion.css" rel="stylesheet" type="text/css">
</head>

<body>
<!-- START BLOCK : archivo -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Conciliaci&oacute;n Autom&aacute;tica<br>
    Santander Serfin </p>
  <form action="./ban_con_aut_san_no_bloqueo_v2.php" method="post" enctype="multipart/form-data" name="form" id="form">
    <table class="tabla">
      <tr>
        <th class="vtabla" scope="row">Archivo de movimientos</th>
        <td class="vtabla"><input name="userfile" type="file" class="insert" id="userfile" size="50"></td>
      </tr>
    </table>
    {mensaje}
    <p>
    <input type="button" class="boton" value="Siguiente >>" onClick="validar()"{disabled}>
  </form></p></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
<!--
var f = document.form;

function validar() {
	if (f.userfile.value == "" && confirm('No especifico el archivo de movimientos, ¿desea conciliar de todas maneras?')) {
		document.location = './ban_con_aut_san_no_bloqueo_v2.php?accion=codsob&hash=xxx';
		return false;
	}
	else if (confirm("¿Desea comenzar con la conciliación?"))
		f.submit();
	else
		f.userfile.focus();
}

window.onload = f.userfile.focus();
//-->
</script>
<!-- END BLOCK : archivo -->
<!-- START BLOCK : val_cod -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Conciliaci&oacute;n Autom&aacute;tica<br>
    Santander Serfin</p>
  <p><font face="Geneva, Arial, Helvetica, sans-serif">Los siguientes c&oacute;digos no se encuentran en el cat&aacute;logo de movimientos bancarios. No se proseguira con la conciliaci&oacute;n.</font></p>
  <table class="tabla">
    <tr>
      <th class="tabla" scope="col">C&oacute;digo</th>
      <th class="tabla" scope="col">Descripci&oacute;n</th>
      <th class="tabla" scope="col">Concepto</th>
      <th class="tabla" scope="col">Tipo</th>
      <th class="tabla" scope="col">Compa&ntilde;&iacute;a</th>
      <th class="tabla" scope="col">Cuenta</th>
      <th class="tabla" scope="col">Importe</th>
    </tr>
    <!-- START BLOCK : cod_banco -->
	<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
      <td class="tabla">{cod_banco}</td>
      <td class="vtabla">{descripcion}</td>
      <td class="vtabla">{concepto}</td>
      <td class="tabla">{tipo_mov}</td>
      <td class="vtabla">{num_cia} - {nombre_cia} </td>
	  <td class="tabla">{cuenta}</td>
	  <td class="rtabla">{importe}</td>
	</tr>
	<!-- END BLOCK : cod_banco -->
  </table>
  <p>
    <input type="button" class="boton" value="<< Regresar" onClick="document.location='./ban_cat_san_altas.php'">
  </p></td>
</tr>
</table>
<!-- END BLOCK : val_cod -->
<!-- START BLOCK : val_cuentas -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Conciliaci&oacute;n Autom&aacute;tica<br>
    Santander Serf&iacute;n</p>
  <p><font face="Geneva, Arial, Helvetica, sans-serif">Las siguientes cuentas no se encuentran en el cat&aacute;logo de compa&ntilde;&iacute;as.</font></p>
  <table class="tabla">
    <tr>
      <th class="tabla" scope="col">Cuenta</th>
      </tr>
    <!-- START BLOCK : cuenta -->
	<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
      <td class="tabla">{cuenta}</td>
      </tr>
	<!-- END BLOCK : cuenta -->
  </table>
  <p><font face="Geneva, Arial, Helvetica, sans-serif">&iquest;Desea proseguir con la conciliaci&oacute;n?</font></p>
<p>
  <input type="button" class="boton" value="Cancelar" onClick="document.location='./ban_con_aut_san_no_bloqueo_v2.php?accion=cancel&hash={hash}'">
  &nbsp;&nbsp;
  <input type="button" class="boton" value="Siguiente >>" onClick="document.location='./ban_con_aut_san_no_bloqueo_v2.php?accion=con&hash={hash}'">
</p></td>
</tr>
</table>
<!-- END BLOCK : val_cuentas -->
<!-- START BLOCK : imp -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Conciliaci&oacute;n Autom&aacute;tica<br>
    Santander Serf&iacute;n</p>
  <p style="font-family:Arial, Helvetica, sans-serif; font-weight:bold;">Impuestos Federales, IMSS e Infonavit</p>
  <table class="print">
    <tr>
      <th class="print" scope="col">Compa&ntilde;&iacute;a</th>
      <th class="print" scope="col">Cuenta</th>
      <th class="print" scope="col">Fecha</th>
      <th class="print" scope="col">Concepto</th>
      <th class="print" scope="col">Importe</th>
    </tr>
    <!-- START BLOCK : imp_rec -->
	<tr>
      <td class="vprint">{num_cia} {nombre} </td>
      <td class="print">{cuenta}</td>
      <td class="print">{fecha}</td>
      <td class="vprint">{concepto}</td>
      <td class="rprint">{importe}</td>
    </tr>
	<!-- END BLOCK : imp_rec -->
  </table>  <p>
    <input type="button" class="boton" value="Cancelar" onClick="document.location='./ban_con_aut_san_no_bloqueo_v2.php?accion=cancel&hash={hash}'">
&nbsp;&nbsp;
<input name="button" type="button" class="boton" onClick="document.location='./ban_con_aut_san_no_bloqueo_v2.php?accion=codsob&hash={hash}'" value="Siguiente >>">
</p></td>
</tr>
</table>
<!-- END BLOCK : imp -->
<!-- START BLOCK : cod_sob -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Conciliaci&oacute;n Autom&aacute;tica<br>
    Santander Serf&iacute;n</p>
  <form action="./ban_con_aut_san_no_bloqueo_v2.php?accion=con&hash={hash}" method="post" name="form"><table class="tabla">
    <tr>
      <th class="tabla" scope="col"><input type="checkbox" onClick="checkall()"></th>
      <th colspan="2" class="tabla" scope="col">Compa&ntilde;&iacute;a</th>
      <th class="tabla" scope="col">Cuenta</th>
      <th class="tabla" scope="col">Importe</th>
      <th class="tabla" scope="col">C&oacute;digo Archivo </th>
      <th class="tabla" scope="col">Concepto</th>
      <th class="tabla" scope="col">C&oacute;digo Sistema</th>
    </tr>
    <!-- START BLOCK : cod_sob_fila -->
	<tr>
	  <td class="rtabla"><input name="id{i}" type="checkbox" id="id{i}" value="{id}"></td>
      <td class="rtabla">{num_cia}</td>
      <td class="vtabla">{nombre}</td>
      <td class="tabla">{cuenta}</td>
      <td class="rtabla">{importe}</td>
      <td class="tabla">{cod}</td>
      <td class="vtabla">{concepto}</td>
      <td class="tabla"><select name="cod_mov[]" class="insert" id="cod_mov">
        <!-- START BLOCK : cod -->
		<option value="{cod}"{selected}>{cod} {nombre}</option>
		<!-- END BLOCK : cod -->
      </select></td>
    </tr>
	<!-- END BLOCK : cod_sob_fila -->
  </table>  <p>
    <input type="button" class="boton" value="Cancelar" onClick="document.location='./ban_con_aut_san_no_bloqueo_v2.php?accion=cancel&hash={hash}'">
&nbsp;&nbsp;
<input type="button" class="boton" value="Siguiente >>" onClick="validar()">
    </p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
<!--
var form = document.form;

function validar() {
	if (confirm("¿Son correctos los cambios?"))
		form.submit();
}

function checkall(check) {
	if (form.cod_mov.length == undefined)
		form.id0.checked = check.checked;
	else
		for (var i = 0; i < form.cod_mov.length; i++)
			form.eval("id" + i).checked = check.checked;
}
//-->
</script>
<!-- END BLOCK : cod_sob -->
<!-- START BLOCK : saldos -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Saldos Bajos</p>
  <table class="tabla">
    <tr>
      <th class="tabla" scope="col">Compa&ntilde;&iacute;a</th>
      <th class="tabla" scope="col">Saldo</th>
    </tr>
    <!-- START BLOCK : saldo -->
	<tr>
      <td class="vtabla"{styles}>{num_cia} {nombre} </td>
      <td class="rtabla"{styles}>{saldo}</td>
    </tr>
	<!-- END BLOCK : saldo -->
  </table>
  <p>
    <input type="button" class="boton" value="Continuar" onClick="document.location='./ban_con_aut_san_no_bloqueo_v2.php?accion=res'">
  </p>  <p>&nbsp; </p></td>
</tr>
</table>
<!-- END BLOCK : saldos -->
<!-- START BLOCK : palomeados -->
<table width="100%">
  <tr>
    <td>&nbsp;</td>
    <td class="print_encabezado" align="center">Oficinas Administrativas Mollendo S. de R.L. y C.V. </td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td width="20%">&nbsp;</td>
    <td width="60%" class="print_encabezado" align="center">Movimientos Palomeados Autom&aacute;ticamente<br>
    al d&iacute;a {dia} de {mes} del {anio} </td>
    <td width="20%">&nbsp;</td>
  </tr>
</table>
<br>
<table width="100%" align="center" class="print">
  <!-- START BLOCK : cia_pal -->
  <tr>
    <th class="print" scope="col">Cia.: {num_cia} </th>
    <th colspan="2" class="print" scope="col">Cuenta: {cuenta} </th>
    <th colspan="3" class="print" scope="col">{nombre_cia}</th>
  </tr>
  <tr>
    <th class="print">Fecha</th>
    <th class="print">Abono</th>
    <th class="print">Cargo</th>
    <th class="print">Folio</th>
    <th class="print">C&oacute;digo de Movimiento </th>
    <th class="print">Concepto</th>
  </tr>
  <!-- START BLOCK : fila_pal -->
  <tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
    <td class="print">{fecha}</td>
    <td class="rprint" style="color: #0000CC;">{abono}</td>
    <td class="rprint" style="color: #CC0000;">{cargo}</td>
    <td class="print">{folio}</td>
    <td class="vprint">{cod_mov} {descripcion}</td>
    <td class="vprint">{concepto}</td>
  </tr>
  <!-- END BLOCK : fila_pal -->
  <tr>
    <th class="print">Totales</th>
    <th class="rprint_total" style="color: #0000CC;">{abonos}</th>
    <th class="rprint_total" style="color: #CC0000;">{cargos}</th>
    <th colspan="3" class="print">&nbsp;</th>
  </tr>
  <tr>
    <td colspan="6">&nbsp;</td>
  </tr>
  <!-- END BLOCK : cia_pal -->
</table>
<br>
<table align="center" class="print">
  <tr>
    <th class="print" scope="col" style="font-size: 12pt; color: #0000CC;">Total de Abonos </th>
    <th class="print" scope="col" style="font-size: 12pt; color: #CC0000;">Total de Cargos </th>
  </tr>
  <tr>
    <th class="print" style="font-size: 14pt; color: #0000CC;">{total_abonos}</th>
    <th class="print" style="font-size: 14pt; color: #CC0000;">{total_cargos}</th>
  </tr>
</table>

<br style="page-break-after:always;">
<!-- END BLOCK : palomeados -->
<!-- START BLOCK : autorizados -->
<table width="100%">
  <tr>
    <td>&nbsp;</td>
    <td class="print_encabezado" align="center">Oficinas Administrativas Mollendo S. de R.L. y C.V. </td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td width="20%">&nbsp;</td>
    <td width="60%" class="print_encabezado" align="center">Movimientos Autorizados en el Palomeo<br>
    al d&iacute;a {dia} de {mes} del {anio} </td>
    <td width="20%">&nbsp;</td>
  </tr>
</table>
<br>
<table width="100%" align="center" class="print">
  <!-- START BLOCK : cia_aut -->
  <tr>
    <th class="print" scope="col">Cia.: {num_cia} </th>
    <th colspan="2" class="print" scope="col">Cuenta: {cuenta} </th>
    <th colspan="3" class="print" scope="col">{nombre_cia}</th>
  </tr>
  <tr>
    <th class="print">Fecha</th>
    <th class="print">Abono</th>
    <th class="print">Cargo</th>
    <th class="print">Folio</th>
    <th class="print">C&oacute;digo de Movimiento </th>
    <th class="print">Concepto</th>
  </tr>
  <!-- START BLOCK : fila_aut -->
  <tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
    <td class="print">{fecha}</td>
    <td class="rprint" style="color: #0000CC;">{abono}</td>
    <td class="rprint" style="color: #CC0000;">{cargo}</td>
    <td class="print">{folio}</td>
    <td class="vprint">{cod_mov} {descripcion}</td>
    <td class="vprint">{concepto}</td>
  </tr>
  <!-- END BLOCK : fila_aut -->
  <tr>
    <th class="print">Totales</th>
    <th class="rprint_total" style="color: #0000CC;">{abonos}</th>
    <th class="rprint_total" style="color: #CC0000;">{cargos}</th>
    <th colspan="3" class="print">&nbsp;</th>
  </tr>
  <tr>
    <td colspan="6">&nbsp;</td>
  </tr>
  <!-- END BLOCK : cia_aut -->
</table>
<br>
<table align="center" class="print">
  <tr>
    <th class="print" scope="col" style="font-size: 12pt; color: #0000CC;">Total de Abonos </th>
    <th class="print" scope="col" style="font-size: 12pt; color: #CC0000;">Total de Cargos </th>
  </tr>
  <tr>
    <th class="print" style="font-size: 14pt; color: #0000CC;">{total_abonos}</th>
    <th class="print" style="font-size: 14pt; color: #CC0000;">{total_cargos}</th>
  </tr>
</table>
<br style="page-break-after:always;">
<!-- END BLOCK : autorizados -->
<!-- START BLOCK : pendientes -->
<table width="100%">
  <tr>
    <td>&nbsp;</td>
    <td class="print_encabezado" align="center">Oficinas Administrativas Mollendo S. de R.L. y C.V. </td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td width="20%">&nbsp;</td>
    <td width="60%" class="print_encabezado" align="center">Movimientos Pendientes de Palomear <br>
    al d&iacute;a {dia} del {mes} al {anio} </td>
    <td width="20%">&nbsp;</td>
  </tr>
</table>
<br>
<table width="100%" align="center" class="print">
  <!-- START BLOCK : cia_pen -->
  <tr>
    <th class="print" scope="col">Cia.: {num_cia} </th>
    <th colspan="2" class="print" scope="col">Cuenta: {cuenta} </th>
    <th colspan="3" class="print" scope="col">{nombre_cia}</th>
  </tr>
  <tr>
    <th class="print">Fecha</th>
    <th class="print">Abono</th>
    <th class="print">Cargo</th>
    <th class="print">Folio</th>
    <th class="print">C&oacute;digo Bancario</th>
    <th class="print">Concepto</th>
  </tr>
  <!-- START BLOCK : fila_pen -->
  <tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
    <td class="print">{fecha}</td>
    <td class="rprint" style="color: #0000CC;">{abono}</td>
    <td class="rprint" style="color: #CC0000;">{cargo}</td>
    <td class="print">{folio}</td>
    <td class="print">{cod_banco}</td>
    <td class="vprint">{concepto}</td>
  </tr>
  <!-- END BLOCK : fila_pen -->
  <tr>
    <th class="print">Totales</th>
    <th class="rprint_total" style="color: #0000CC;">{abonos}</th>
    <th class="rprint_total" style="color: #CC0000;">{cargos}</th>
    <th colspan="3" class="print">&nbsp;</th>
  </tr>
  <tr>
    <th colspan="6">&nbsp;</th>
  </tr>
  <!-- END BLOCK : cia_pen -->
</table>
<br>
<table align="center" class="print">
  <tr>
    <th class="print" scope="col" style="font-size: 12pt; color: #0000CC;">Total de Abonos </th>
    <th class="print" scope="col" style="font-size: 12pt; color: #CC0000;">Total de Cargos </th>
  </tr>
  <tr>
    <th class="print" style="font-size: 14pt; color: #0000CC;">{total_abonos}</th>
    <th class="print" style="font-size: 14pt; color: #CC0000;">{total_cargos}</th>
  </tr>
</table>
<!-- END BLOCK : pendientes -->
</body>
</html>
