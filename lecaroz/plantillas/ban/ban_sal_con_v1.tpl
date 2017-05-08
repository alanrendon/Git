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
<!-- START BLOCK : datos -->
<script language="javascript" type="text/javascript">
	function valida_registro() {
		if (document.form.tipo.value == "cia" && document.form.num_cia.value <= 0) {
			alert("Debe especificar una compañía");
			document.form.num_cia.select();
			return false;
		}
		else
			document.form.submit();
	}
</script>
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
<p class="title">Listado de Saldos</p>
<form name="form" method="get" action="./ban_sal_con.php">
<table class="tabla">
  <tr>
    <th colspan="2" class="vtabla" scope="row">
      <input name="tipo" type="radio" onClick="form.num_cia.disabled=false;" value="cia" checked> 
      Por compa&ntilde;&iacute;a<br>
      <input name="tipo" type="radio" onClick="form.num_cia.disabled=true" value="todas"> 
      Todas las compa&ntilde;&iacute;as</th>
  </tr>
  <tr>
    <th class="vtabla" scope="row">Compa&ntilde;&iacute;a</th>
    <td class="vtabla"><input name="num_cia" type="text" class="insert" id="num_cia" size="3" maxlength="3"></td>
  </tr>
</table>
<p>
  <input name="enviar" type="button" class="boton" id="enviar" value="Siguiente" onClick="valida_registro()">
</p>
</form>
</td>
</tr>
</table>
<!-- END BLOCK : datos -->
<!-- START BLOCK : saldos_all -->
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
    <td width="60%" class="print_encabezado" align="center">Listado de Saldos <br>
      al {dia} de {mes} de {anio} </td>
    <td width="20%">&nbsp;</td>
  </tr>
</table>
  <br>
<table class="tabla">
  <tr>
    <th colspan="2" class="print" scope="col">N&uacute;mero y Cuenta </th>
    <th class="print" scope="col">Nombre</th>
    <th class="print" scope="col">Saldo en Bancos </th>
    <th class="print" scope="col">Saldo en Libros</th>
    <th class="print" scope="col">Pendientes</th>
    <th class="print" scope="col">Saldo Proveedores </th>
    <th class="print" scope="col">&Uacute;ltima factura </th>
    <th class="print" scope="col">Perdidas anteriores </th>
    <th class="print" scope="col">Devoluciones de IVA </th>
    <th class="print" scope="col">Promedio Efectivo </th>
    <th class="print" scope="col">Efectivo</th>
    <th class="print" scope="col">D&iacute;as</th>
  </tr>
  <!-- START BLOCK : fila -->
  <tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
    <td class="print">{num_cia}</td>
    <td class="print">{cuenta}</td>
    <td class="vprint">{nombre_cia}</td>
    <td class="rprint">{saldo_bancos}</td>
    <td class="rprint">{saldo_libros}</td>
    <td class="rprint">{pendientes}</td>
    <td class="rprint">{saldo_pro}</td>
    <td class="print">{ultima_fac}</td>
    <td class="rprint">{perdidas}</td>
    <td class="rprint">{devoluciones}</td>
    <td class="rprint">{pro_efectivo}</td>
    <td class="rprint">{efectivo}</td>
    <td class="print">{dias}</td>
  </tr>
  <!-- END BLOCK : fila -->
</table>
</td>
</tr>
</table>
<!-- END BLOCK : saldos_all -->
<!-- START BLOCK : saldo_cia -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="top">
<table width="100%">
  <tr>
    <td class="print_encabezado">Cia.: {num_cia} </td>
    <td class="print_encabezado" align="center">{nombre_cia}</td>
    <td class="print_encabezado" align="right">Cia.: {num_cia} </td>
  </tr>
  <tr>
    <td width="20%">&nbsp;</td>
    <td width="60%" class="print_encabezado" align="center">Listado de Saldos <br>
      al {dia} de {mes} de {anio} </td>
    <td width="20%">&nbsp;</td>
  </tr>
</table>
  <br>
<table class="tabla">
  <tr>
    <th class="print" scope="col">Saldo en Bancos </th>
    <th class="print" scope="col">Saldo en Libros</th>
    <th class="print" scope="col">Pendientes</th>
    <th class="print" scope="col">Saldo Proveedores </th>
    <th class="print" scope="col">&Uacute;ltima factura </th>
    <th class="print" scope="col">Perdidas anteriores </th>
    <th class="print" scope="col">Devoluciones de IVA </th>
    <th class="print" scope="col">Promedio Efectivo </th>
    <th class="print" scope="col">Efectivo</th>
    <th class="print" scope="col">D&iacute;as</th>
  </tr>
  <tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
    <td class="rprint">{saldo_bancos}</td>
    <td class="rprint">{saldo_libros}</td>
    <td class="rprint">{pendientes}</td>
    <td class="rprint">{saldo_pro}</td>
    <td class="print">{ultima_fac}</td>
    <td class="rprint">{perdidas}</td>
    <td class="rprint">{devoluciones}</td>
    <td class="rprint">{pro_efectivo}</td>
    <td class="rprint">{efectivo}</td>
    <td class="print">{dias}</td>
  </tr>
</table>
</td>
</tr>
</table>
<!-- END BLOCK : saldo_cia -->
</body>
</html>
