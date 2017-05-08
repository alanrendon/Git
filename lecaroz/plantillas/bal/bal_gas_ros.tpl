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
<td align="center" valign="middle"><p class="title">Generar Gastos de Oficina (Pollos)</p>
  <form action="./bal_gas_ros.php" method="get" name="form">
  <input name="temp" type="hidden">
  <table class="tabla">
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
      <th class="vtabla">A&ntilde;o</th>
      <td class="vtabla"><input name="anio" type="text" class="insert" id="anio" onFocus="temp.value=this.value" onChange="isInt(this)" onKeyDown="if (event.keyCode == 13) sig.focus()" value="{anio}" size="4" maxlength="4"></td>
    </tr>
  </table>  <p>
    <input name="sig" type="button" class="boton" id="sig" onClick="validar(this.form)" value="Siguiente"> 
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
		else if (confirm("¿Desea generar los datos para los Gastos de Oficinas?")) {
			form.submit();
		}
		else {
			return false;
		}
	}
	
	window.onload = document.form.anio.select();
</script>
<!-- END BLOCK : datos -->
<!-- START BLOCK : listado -->
<table width="100%" align="center">
  <tr>
    <td>&nbsp;</td>
    <td class="print_encabezado" align="center">Oficinas Administrativas Mollendo S. de R.L. y C.V. </td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td width="20%">&nbsp;</td>
    <td width="60%" class="print_encabezado" align="center">Gastos de Oficina<br>
      del 1 al {dia} de {mes} del {anio} </td>
    <td width="20%">&nbsp;</td>
  </tr>
</table>
  <br>
  <table align="center" class="print">
    <tr>
      <th colspan="2" class="print" scope="col">Compa&ntilde;&iacute;a</th>
      <th class="print" scope="col">Concepto</th>
      <th class="print" scope="col">Balance</th>
      <th class="print" scope="col">Fecha</th>
	  <th class="print" scope="col">Egreso</th>
      <th class="print" scope="col">Ingreso</th>
    </tr>
    <!-- START BLOCK : fila -->
	<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
      <td class="print">{num_cia}</td>
      <td class="vprint">{nombre_cia}</td>
      <td class="vprint">{concepto}</td>
      <td class="print">{balance}</td>
      <td class="print">{fecha}</td>
	  <td class="print">{egreso}</td>
      <td class="print">&nbsp;</td>
    </tr>
    <tr>
      <th colspan="5" class="rprint">Total de Gastos </th>
      <th class="rprint_total">{egreso}</th>
      <th class="rprint_total">0.00</th>
    </tr>
    <tr>
      <th colspan="5" class="rprint">Total de la Compa&ntilde;&iacute;a </th>
      <th colspan="2" class="print_total">{total}</th>
    </tr>
    <tr>
      <td colspan="7">&nbsp;</td>
    </tr>
	<!-- END BLOCK : fila -->
    <tr>
      <th colspan="5" class="rprint">Gran Total </th>
      <th class="rprint_total">{egreso}</th>
      <th class="rprint_total">0.00</th>
    </tr>
    <tr>
      <th colspan="5" class="rprint">Neto</th>
      <th colspan="2" class="print_total">{neto}</th>
    </tr>
  </table>
<p align="center">
  <input type="button" class="boton" value="Regresar" onClick="document.location='./bal_gas_ros.php'">
  &nbsp;&nbsp;
  <input type="button" class="boton" value="Imprimir" onClick="window.print()">
</p>
<!-- END BLOCK : listado -->
</body>
</html>
