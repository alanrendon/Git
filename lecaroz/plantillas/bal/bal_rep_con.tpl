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
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Reporte Consolidado</p>
  <form action="./bal_rep_con.php" method="get" name="form">
    <table class="tabla">
    <tr>
      <th class="vtabla" scope="row">Compa&ntilde;&iacute;a</th>
      <td class="vtabla"><input name="num_cia" type="text" class="insert" id="num_cia" size="3" maxlength="3"></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Mes</th>
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
      <th class="vtabla" scope="row">A&ntilde;o</th>
      <td class="vtabla"><input name="anio" type="text" class="insert" id="anio" value="{anio}" size="4" maxlength="4"></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Para</th>
      <td class="vtabla"><input name="tipo" type="radio" value="1" checked>
        Panader&iacute;as
          <input name="tipo" type="radio" value="2">
          Rosticer&iacute;as</td>
    </tr>
  </table>
    <p>
    <input type="button" class="boton" value="Siguiente" onClick="validar(this.form)">
  </p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
	function validar(form) {
		if (form.anio.value <= 0) {
			alert("Debe especificar el año");
			form.anio.select();
			return false;
		}
		else
			form.submit();
	}
</script>
<!-- END BLOCK : datos -->
<!-- START BLOCK : listado -->
<table width="100%">
  <tr>
    <td>&nbsp;</td>
    <td class="print_encabezado" align="center">Oficinas Administrativas Mollendo S. de R.L. y C.V. </td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td width="20%">&nbsp;</td>
    <td width="60%" class="print_encabezado" align="center">Reporte Consolidado<br>
      del mes de {mes} de {anio} </td>
    <td width="20%">&nbsp;</td>
  </tr>
</table>
  <br>
  <table width="100%" class="print">
    <tr>
      <th class="print" scope="col">No.</th>
      <th class="print" scope="col">Nombre</th>
      <th class="print" scope="col">Venta en puerta</th>
      <th class="print" scope="col">Venta reparto</th>
      <th class="print" scope="col">Producci&oacute;n</th>
      <th class="print" scope="col">Gas</th>
      <th class="print" scope="col">Gastos de Operaci&oacute;n </th>
      <th class="print" scope="col">Gastos Generales </th>
      <th class="print" scope="col">M.P. Utilizada </th>
      <th class="print" scope="col">Sueldo a Empleados </th>
      <th class="print" scope="col">Utilidad Neta</th>
      <th class="print" scope="col">Compa&ntilde;&iacute;a</th>
    </tr>
    <!-- START BLOCK : fila -->
	<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
      <td class="print"><font color="#0000FF">{num_cia}</font></td>
      <td class="vprint"><font color="#0000FF">{nombre_cia}</font></td>
      <td class="rprint"><font color="#00609C">{venta_puerta}</font></td>
      <td class="rprint"><font color="#0000DC">{venta_reparto}</font></td>
      <td class="rprint"><font color="#006600">{produccion}</font></td>
      <td class="rprint"><font color="#996600">{gas}</font></td>
      <td class="rprint"><font color="#006699">{gastos_op}</font></td>
      <td class="rprint"><font color="#006699">{gastos_gral}</font></td>
      <td class="rprint"><font color="#006699">{mp_utilizada}</font></td>
      <td class="rprint"><font color="#FF6600">{sueldo_emp}</font></td>
      <td class="rprint">{utilidad_neta}</td>
      <td class="vprint"><font color="#0000FF">{num_cia} {nombre_cia}</font></td>
    </tr>
	<!-- END BLOCK : fila -->
  </table>
<!-- END BLOCK : listado -->
<!-- START BLOCK : listado_pollos -->
<table width="100%">
  <tr>
    <td>&nbsp;</td>
    <td class="print_encabezado" align="center">Oficinas Administrativas Mollendo S. de R.L. y C.V. </td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td width="20%">&nbsp;</td>
    <td width="60%" class="print_encabezado" align="center">Reporte Consolidado<br>
      del mes de {mes} de {anio} </td>
    <td width="20%">&nbsp;</td>
  </tr>
</table>
  <br>
  <table width="100%" class="print">
    <tr>
      <th class="print" scope="col">No.</th>
      <th class="print" scope="col">Nombre</th>
      <th class="print" scope="col">Ventas</th>
      <th class="print" scope="col">Gas</th>
      <th class="print" scope="col">Gastos de Operaci&oacute;n </th>
      <th class="print" scope="col">Gastos Generales </th>
      <th class="print" scope="col">M.P. Utilizada </th>
      <th class="print" scope="col">Sueldo a Empleados </th>
      <th class="print" scope="col">Utilidad Neta</th>
    </tr>
    <!-- START BLOCK : fila_pollos -->
	<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
      <td class="print"><font color="#0000FF">{num_cia}</font></td>
      <td class="vprint"><font color="#0000FF">{nombre_cia}</font></td>
      <td class="rprint"><font color="#006600">{ventas}</font></td>
      <td class="rprint"><font color="#996600">{gas}</font></td>
      <td class="rprint"><font color="#006699">{gastos_op}</font></td>
      <td class="rprint"><font color="#006699">{gastos_gral}</font></td>
      <td class="rprint"><font color="#006699">{mp_utilizada}</font></td>
      <td class="rprint"><font color="#FF6600">{sueldo_emp}</font></td>
      <td class="rprint">{utilidad_neta}</td>
    </tr>
	<!-- END BLOCK : fila_pollos -->
  </table>
<!-- END BLOCK : listado_pollos -->
</body>
</html>
