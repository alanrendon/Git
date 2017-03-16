<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Untitled Document</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../../styles/tablas.css" rel="stylesheet" type="text/css">
<link href="../../styles/pages.css" rel="stylesheet" type="text/css">
<link href="file:///C|/Documents%20and%20Settings/John%20Talbain/Escritorio/Lecaroz/styles/impresion.css" rel="stylesheet" type="text/css">
</head>

<body>
<!-- START BLOCK : datos -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Estimaci&oacute;n de Alza de Precios</p>
  <form action="./bal_est_alz.php" method="get" name="form">
    <input name="tmp" type="hidden" id="tmp">
    <table class="tabla">
    <tr>
      <th class="vtabla" scope="row">Compa&ntilde;&iacute;a</th>
      <td class="vtabla"><input name="num_cia" type="text" class="insert" id="num_cia" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) precio.select()" size="3" maxlength="3"></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Turnos</th>
      <td class="vtabla"><input name="cod_turno[]" type="checkbox" id="cod_turno" value="1">
        Frances de D&iacute;a <br>
        <input name="cod_turno[]" type="checkbox" id="cod_turno" value="2">
        Frances de Noche<br>
        <input name="cod_turno[]" type="checkbox" id="cod_turno" value="3">
        Biscochero<br>
        <input name="cod_turno[]" type="checkbox" id="cod_turno" value="4">
        Repostero<br>
        <input name="cod_turno[]" type="checkbox" id="cod_turno" value="3">
        Piconero<br>
        <input name="cod_turno[]" type="checkbox" id="cod_turno" value="9">
        Gelatinero</td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Precio</th>
      <td class="vtabla"><input name="precio" type="text" class="rinsert" id="precio" onFocus="tmp.value=this.value;this.select()" onChange="isFloat(this,4,tmp)" onKeyDown="if (event.keyCode == 13) anio.select()" value="0.10" size="5" maxlength="5"></td>
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
      <td class="vtabla"><input name="anio" type="text" class="insert" id="anio2" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) num_cia.select()" value="{anio}" size="4" maxlength="4"></td>
    </tr>
  </table>  <p>
    <input type="button" class="boton" value="Siguiente" onClick="validar(this.form)"> 
    </p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
<!--
function validar(form) {
	var turnos = 0;
	
	for (i = 0; i < form.cod_turno.length; i++) {
		turnos += form.cod_turno[i].checked ? 1 : 0;
	}
	
	if (turnos == 0) {
		alert("Debe seleciconar al menos un turno");
		return false;
	}
	else if (form.precio.value <= 0) {
		alert("Debe especificar el precio estimado");
		form.precio.select();
		return false;
	}
	else if (form.anio.value <= 2000) {
		alert("Debe especificar el año de consulta");
		form.anio.select();
		return false;
	}
	else {
		form.submit();
	}
}

window.onload = document.form.num_cia.select();
-->
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
    <td width="60%" class="print_encabezado" align="center">Estimaci&oacute;n de Alza de Precios </td>
    <td width="20%">&nbsp;</td>
  </tr>
</table>
<br>
<table align="center" class="print">
  <!-- START BLOCK : cia -->
  <tr>
    <th colspan="4" class="vprint" scope="col" style="font-size: 12pt;">{num_cia} - {nombre_cia} </th>
  </tr>
  <tr>
    <th class="print">Turno</th>
    <th class="print">Precio</th>
    <th class="print">Piezas</th>
    <th class="print">Total</th>
  </tr>
  <!-- START BLOCK : turno -->
  <tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
    <td class="vprint">{turno}</td>
    <td class="rprint">{precio}</td>
    <td class="rprint">{piezas}</td>
    <td class="rprint">{total}</td>
  </tr>
  <!-- END BLOCK : turno -->
  <tr>
    <th colspan="3" class="rprint">Total</th>
    <th class="rprint_total">{total}</th>
  </tr>
  <tr>
    <td colspan="4">&nbsp;</td>
  </tr>
  <!-- END BLOCK : cia -->
  <tr>
    <th colspan="3" class="rprint">Gran Total</th>
    <th class="rprint_total">{gran_total}</th>
  </tr>
</table>
<!-- END BLOCK : listado -->
</body>
</html>
