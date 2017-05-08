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
<td align="center" valign="middle"><p class="title">Relaci&oacute;n de Pasteles Entregados</p>
  <form action="./pan_rel_con_v2.php" method="get" name="form">
    <input name="temp" type="hidden" id="temp">
    <table class="tabla">
    <tr>
      <th class="vtabla" scope="row">Compa&ntilde;&iacute;a</th>
      <td class="vtabla"><input name="num_cia" type="text" class="insert" id="num_cia" onFocus="temp.value=this.value" onChange="isInt(this,temp)" onKeyDown="if (event.keyCode == 13) fecha.select()" size="3" maxlength="3"></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row"><input name="tipo" type="radio" value="dia" checked>
        Fecha</th>
      <td class="vtabla"><input name="fecha" type="text" class="insert" id="fecha" onChange="actualiza_fecha(this)" onKeyDown="if (event.keyCode == 13) anio.select()" value="{fecha}" size="10" maxlength="10"></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row"><input name="tipo" type="radio" value="mes">
        Mes</th>
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
      </select>
        del 
        <input name="anio" type="text" class="insert" id="anio" onFocus="temp.value=this.value" onChange="isInt(this,temp)" onKeyDown="if (event.keyCode == 13) num_cia.select()" value="{anio}" size="4" maxlength="4"></td>
    </tr>
  </table>  
  <p>
    <input type="button" class="boton" value="Siguiente" onClick="validar(this.form)"> 
    </p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
<!--
function validar(form) {
	if (form.tipo[0].checked) {
		if (form.fecha.value.length < 8) {
			alert("Debe especificar la fecha");
			form.fecha.select();
			return false;
		}
		else {
			form.submit();
		}
	}
	else if (form.tipo[1].checked) {
		if (form.anio.value.length < 4) {
			alert("Debe especificar el año");
			form.anio.select();
			return false;
		}
		else {
			form.submit();
		}
	}
}

window.onload = document.form.num_cia.select();
-->
</script>
<!-- END BLOCK : datos -->
<!-- START BLOCK : listado_mes -->
<table width="100%">
  <tr>
    <td class="print_encabezado">Cia.: {num_cia}</td>
    <td class="print_encabezado" align="center">{nombre_cia}</td>
    <td class="rprint_encabezado">Cia.: {num_cia} </td>
  </tr>
  <tr>
    <td width="20%">&nbsp;</td>
    <td width="60%" class="print_encabezado" align="center">Relaci&oacute;n de Kilos de Pastel Entregados<br>
      el mes de {mes} de {anio} </td>
    <td width="20%">&nbsp;</td>
  </tr>
</table>
<br>
<table align="center" class="print">
  <tr>
    <th class="print" scope="col">D&iacute;a</th>
    <th class="print" scope="col">Kilos Entregados </th>
    <th class="print" scope="col">Kilos Producci&oacute;n </th>
    <th class="print" scope="col">Facturas Liquidadas </th>
    <th class="print" scope="col">Importe Facturas </th>
  </tr>
  <!-- START BLOCK : dia -->
  <tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
    <td class="print">{dia}</td>
    <td class="rprint">{k_ent}</td>
    <td class="rprint">{k_pro}</td>
    <td class="rprint">{num_fac}</td>
    <td class="rprint">{importe}</td>
  </tr>
  <!-- END BLOCK : dia -->
  <tr>
    <th class="print">Totales</th>
    <th class="print_total">{k_ent}</th>
    <th class="print_total">{k_pro}</th>
    <th class="print_total">&nbsp;</th>
    <th class="print_total">{importe}</th>
  </tr>
</table>
{salto}
<!-- END BLOCK : listado_mes -->
<!-- START BLOCK : listado_dia -->
<table width="100%">
  <tr>
    <td class="print_encabezado">Cia.: {num_cia}</td>
    <td class="print_encabezado" align="center">{nombre_cia}</td>
    <td class="rprint_encabezado">Cia.: {num_cia} </td>
  </tr>
  <tr>
    <td width="20%">&nbsp;</td>
    <td width="60%" class="print_encabezado" align="center">Relaci&oacute;n de Kilos de Pastel Entregados<br>
      al {dia} de {mes} de {anio} </td>
    <td width="20%">&nbsp;</td>
  </tr>
</table>
<br>
<table align="center" class="print">
  <tr>
    <th class="print" scope="col">Factura</th>
    <th class="print" scope="col">Kilos</th>
    <th class="print" scope="col">Importe</th>
  </tr>
  <!-- START BLOCK : fac -->
  <tr>
    <td class="print">{factura}</td>
    <td class="rprint">{kilos}</td>
    <td class="rprint">{importe}</td>
  </tr>
  <!-- END BLOCK : fac -->
  <tr>
    <th class="print">Totales</th>
    <th class="rprint_total">{kilos}</th>
    <th class="rprint_total">{total}</th>
  </tr>
  <tr>
    <th class="print">Kilos<br>
    Producci&oacute;n</th>
    <th class="rprint_total">{k_pro}</th>
    <th class="print">&nbsp;</th>
  </tr>
</table>
{salto}
<!-- END BLOCK : listado_dia -->
</body>
</html>