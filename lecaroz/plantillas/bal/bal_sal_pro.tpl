<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Untitled Document</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../../styles/tablas.css" rel="stylesheet" type="text/css">
<link href="../../styles/impresion.css" rel="stylesheet" type="text/css">
</head>

<body>
<!-- START BLOCK : datos -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
<p class="title">Saldos de Proveedores</p>
<form action="./bal_sal_pro.php" method="get" name="form"><table class="tabla">
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
  <input type="button" class="boton" value="Siguiente" onClick="valida_registro(form)">
</p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
	function valida_registro(form) {
		if (form.anio.value <= 2000) {
			alert("Debe especificar el año");
			form.anio.select();
			return false;
		}
		else
			form.submit();
	}
	
	window.onload = document.form.anio.select();
</script>
<!-- END BLOCK : datos -->

<!-- START BLOCK : listado -->

<!-- START BLOCK : hoja -->
<table width="100%" align="center">
  <tr>
    <td>&nbsp;</td>
    <td class="print_encabezado" align="center">Oficinas Administrativas MOLLENDO S. de R.L. y C.V. </td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td width="20%">&nbsp;</td>
    <td width="60%" class="print_encabezado" align="center">Saldos de Proveedores 
      al {dia} de {mes} de {anio} </td>
    <td width="20%">&nbsp;</td>
  </tr>
</table>
  <br>
  <table width="80%" align="center" cellpadding="0" cellspacing="0" class="print">
    <tr>
      <th colspan="2" class="print" scope="col">N&uacute;mero y Nombre </th>
      <th class="print" scope="col">Saldo</th>
      <th class="print" scope="col">Pagos</th>
      <th class="print" scope="col">Compras</th>
      <th class="print" scope="col">Inventario</th>
      <th class="print" scope="col">Bancos</th>
    </tr>
    <!-- START BLOCK : fila -->
	<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
      <td width="5%" class="print">{num_cia}</td>
      <td width="20%" class="vprint">{nombre_cia}</td>
      <td width="15%" class="rprint">{saldo}</td>
      <td width="15%" class="rprint">{pagos}</td>
      <td width="15%" class="rprint">{compras}</td>
      <td width="15%" class="rprint">{inventario}</td>
      <td width="15%" class="rprint">{bancos}</td>
    </tr>
    <tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
      <td class="print">&nbsp;</td>
      <td class="print">&nbsp;</td>
      <td class="rprint">{prom_saldo}</td>
      <td class="rprint">{prom_pagos}</td>
      <td class="rprint">{prom_compras}</td>
      <td class="print">&nbsp;</td>
      <td class="print">&nbsp;</td>
    </tr>
	<!-- END BLOCK : fila -->
</table>
<br style="page-break-after:always;">
<!-- END BLOCK : hoja -->
<table width="100%" align="center">
  <tr>
    <td>&nbsp;</td>
    <td class="print_encabezado" align="center">Oficinas Administrativas MOLLENDO S. de R.L. y C.V. </td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td width="20%">&nbsp;</td>
    <td width="60%" class="print_encabezado" align="center">Saldos de Proveedores 
      al {dia} de {mes} de {anio} </td>
    <td width="20%">&nbsp;</td>
  </tr>
</table>
  <br>
  <table width="80%" align="center" cellpadding="0" cellspacing="0" class="print">
    <tr>
      <th rowspan="3" class="print" scope="col">TOTALES</th>
      <th class="print" scope="col">Saldo</th>
      <th class="print" scope="col">Pagos</th>
      <th class="print" scope="col">Compras</th>
      <th class="print" scope="col">Inventario</th>
      <th class="print" scope="col">Bancos</th>
    </tr>
	<tr>
      <td class="rprint">{saldo}</td>
      <td class="rprint">{pagos}</td>
      <td class="rprint">{compras}</td>
      <td class="rprint">{inventario}</td>
      <td class="rprint">{bancos}</td>
    </tr>
    <tr>
      <td class="rprint">{prom_saldo}</td>
      <td class="rprint">{prom_pagos}</td>
      <td class="rprint">{prom_compras}</td>
      <td class="print">&nbsp;</td>
      <td class="print">&nbsp;</td>
    </tr>
</table>
  <br>
  <br>
  <br>
  <table align="center" cellpadding="0" cellspacing="0">
    <tr>
      <td colspan="3" align="center"><strong><font face="Geneva, Arial, Helvetica, sans-serif">Resumen</font></strong></td>
    </tr>
    <tr>
      <td colspan="3" align="center">&nbsp;</td>
    </tr>
    <tr>
      <td align="right"><strong><font face="Geneva, Arial, Helvetica, sans-serif">Saldo Total de Proveedores </font></strong></td>
      <td align="right">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
      <td align="right"><strong><font face="Geneva, Arial, Helvetica, sans-serif">{saldo}</font></strong></td>
    </tr>
    <tr>
      <td align="right"><strong><font face="Geneva, Arial, Helvetica, sans-serif">Menos Saldo Total de Inventarios </font></strong></td>
      <td align="right">&nbsp;</td>
      <td align="right"><strong><font face="Geneva, Arial, Helvetica, sans-serif">{inventario}</font></strong></td>
    </tr>
    <tr>
      <td align="right"><strong><font face="Geneva, Arial, Helvetica, sans-serif">Igual a Diferencia</font> </strong></td>
      <td align="right">&nbsp;</td>
      <td align="right"><strong><font face="Geneva, Arial, Helvetica, sans-serif">{diferencia}</font></strong></td>
    </tr>
    <tr>
      <td align="right">&nbsp;</td>
      <td align="right">&nbsp;</td>
      <td align="right">&nbsp;</td>
    </tr>
    <tr>
      <td align="right"><strong><font face="Geneva, Arial, Helvetica, sans-serif">Saldo Total de Bancos</font></strong></td>
      <td align="right">&nbsp;</td>
      <td align="right"><strong><font face="Geneva, Arial, Helvetica, sans-serif">{bancos}</font></strong></td>
    </tr>
    <tr>
      <td align="right"><strong><font face="Geneva, Arial, Helvetica, sans-serif">Menos Diferencia </font></strong></td>
      <td align="right">&nbsp;</td>
      <td align="right"><strong><font face="Geneva, Arial, Helvetica, sans-serif">{menos}</font></strong></td>
    </tr>
    <tr>
      <td align="right"><strong><font face="Geneva, Arial, Helvetica, sans-serif">Igual a </font></strong></td>
      <td align="right">&nbsp;</td>
      <td align="right"><strong><font face="Geneva, Arial, Helvetica, sans-serif">{igual}</font></strong></td>
    </tr>
  </table>
<!-- END BLOCK : listado -->
</body>
</html>
