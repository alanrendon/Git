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
<td align="center" valign="middle"><p class="title">Reporte de Comparativo de Gas </p>
  <form action="./bal_com_gas.php" method="get" name="form"><table class="tabla">
    <tr>
      <th class="vtabla" scope="col">Compa&ntilde;&iacute;a</th>
      <td class="vtabla" scope="col"><input name="num_cia" type="text" class="insert" id="num_cia" size="3" maxlength="3"></td>
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
  </table>  <p>
    <input name="button" type="button" class="boton" value="Siguiente" onClick="validar(form)">
</p>
  </form></td>
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
    <td width="60%" class="print_encabezado" align="center">Concentrado del Mes de {mes} de {anio} </td>
    <td width="20%">&nbsp;</td>
  </tr>
</table>
  <br>


<table align="center" class="print">
  <tr>
    <th colspan="2" class="print" scope="col">N&uacute;mero y Nombre</th>
    <th class="print" scope="col">Gas en<br>
      Pesos </th>
    <th class="print" scope="col">Faltante<br>
    de Pan </th>
    <th class="print" scope="col">%FP /<br>
      Prod.</th>
    <th class="print" scope="col">Rezago</th>
    <th class="print" scope="col">Producci&oacute;n</th>
    <th class="print" scope="col">Gas en<br>
      Litros </th>
    <th class="print" scope="col">% Gas /<br>
      Prod.</th>
    <th class="print" scope="col">Sueldo<br>
    Empleados</th>
    <th class="print" scope="col">% Sdo /<br>
      Prod.</th>
    <th class="print" scope="col">Sueldo<br>
    Encargados</th>
    <th class="print" scope="col">M. Prima<br>
      Utilizada </th>
    <th class="print" scope="col">% MP / <br>
    Prod.</th>
    <th class="print" scope="col">Mano<br>
    de Obra </th>
    <th class="print" scope="col">% de MO /<br>
      Prod.</th>
    <th class="print" scope="col">Panaderos</th>
    <th class="print" scope="col">Gastos de<br>
    Fabricaci&oacute;n</th>
    <th class="print" scope="col">Total de<br>
      Gastos </th>
    <th class="print" scope="col">Ventas</th>
    <th class="print" scope="col">Efectivo</th>
    <th class="print" scope="col">% MP /<br>
      Ventas </th>
    <th class="print" scope="col">% UT /<br>
      Ventas </th>
    <th class="print" scope="col">% Gastos /<br>
      Ventas </th>
    <th class="print" scope="col">% Total </th>
  </tr>
  <!-- START BLOCK : fila -->
  <tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
    <td class="rprint">{num_cia}</td>
    <td class="vprint">{nombre}</td>
    <td class="rprint">{gas_pesos}</td>
    <td class="rprint">{faltante_pan}</td>
    <td class="rprint">{fp_pro}</td>
    <td class="rprint">{rezago}</td>
    <td class="rprint">{pro}</td>
    <td class="rprint">{gas_litros}</td>
    <td class="rprint">{gas_pro}</td>
    <td class="rprint">{sueldo_emp}</td>
    <td class="rprint">{sdo_pro}</td>
    <td class="rprint">{sueldo_enc}</td>
    <td class="rprint">{mp_utilizada}</td>
    <td class="rprint">{mp_pro}</td>
    <td class="rprint">{mano_obra}</td>
    <td class="rprint">{mo_pro}</td>
    <td class="rprint">{panaderos}</td>
    <td class="rprint">{gastos_fab}</td>
    <td class="rprint">{total_gastos}</td>
    <td class="rprint">{ventas}</td>
    <td class="rprint">{efectivo}</td>
    <td class="rprint">{mp_vtas}</td>
    <td class="rprint">{ut_vtas}</td>
    <td class="rprint">{gastos_vtas}</td>
    <td class="rprint">{total}</td>
  </tr>
  <!-- END BLOCK : fila -->
  <tr>
    <th colspan="2" class="print">Totales</th>
    <th class="rprint_total">{gas_pesos}</th>
    <th class="rprint_total">{faltante_pan}</th>
    <th class="rprint_total">{fp_pro}</th>
    <th class="rprint_total">{rezago}</th>
    <th class="rprint_total">{pro}</th>
    <th class="rprint_total">{gas_litros}</th>
    <th class="rprint_total">{gas_pro}</th>
    <th class="rprint_total">{sueldo_emp}</th>
    <th class="rprint_total">{sdo_pro}</th>
    <th class="rprint_total">{sueldo_enc}</th>
    <th class="rprint_total">{mp_utilizada}</th>
    <th class="rprint_total">{p_pro}</th>
    <th class="rprint_total">{mano_obra}</th>
    <th class="rprint_total">{mo_pro}</th>
    <th class="rprint_total">{panaderos}</th>
    <th class="rprint_total">{gastos_fab}</th>
    <th class="rprint_total">{total_gastos}</th>
    <th class="rprint_total">{ventas}</th>
    <th class="rprint_total">{efectivo}</th>
    <th class="rprint_total">{mp_vtas}</th>
    <th class="rprint_total">{ut_vtas}</th>
    <th class="rprint_total">{gastos_vtas}</th>
    <th class="rprint_total">{total}</th>
  </tr>
</table>


<!-- END BLOCK : listado -->
</body>
</html>
