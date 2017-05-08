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
<td align="center" valign="middle"><p class="title">Costo de Reparto</p>
  <form action="./bal_com_rep.php" method="get" name="form">
    <input name="tmp" type="hidden" id="tmp">
    <table class="tabla">
    <tr>
      <th class="vtabla" scope="row">Compa&ntilde;&iacute;a</th>
      <td class="vtabla"><input name="num_cia" type="text" class="insert" id="num_cia" onFocus="tmp.value=this.value" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) anio.select()" size="3" maxlength="3"></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Administrador</th>
      <td class="vtabla"><select name="admin" class="insert" id="admin">
        <option value=""></option>
		<option value="-1" style="color: #FF0000;">POR ADMINISTRADOR</option>
        <!-- START BLOCK : admin -->
		<option value="{id}">{nombre}</option>
		<!-- END BLOCK : admin -->
      </select></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Mes</th>
      <td class="vtabla"><select name="mes" class="insert" id="mes">
        <option value="1"{1}>ENERO</option>
        <option value="2"{2}>FEBRERO</option>
        <option value="3"{3}>MARZO</option>
        <option value="4"{4}>ABRIL</option>
        <option value="5"{5}>MAYO</option>
        <option value="6"{6}>JUNIO</option>
        <option value="7"{7}>JULIO</option>
        <option value="8"{8}>AGOSTO</option>
        <option value="9"{9}>SEPTIEMBRE</option>
        <option value="10"{10}>OCTUBRE</option>
        <option value="11"{11}>NOVIEMBRE</option>
        <option value="12"{12}>DICIEMBRE</option>
      </select></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">A&ntilde;o</th>
      <td class="vtabla"><input name="anio" type="text" class="insert" id="anio" onFocus="tmp.value=this.value" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) num_cia.select()" value="{anio}" size="4" maxlength="4"></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Desglosar mes </th>
      <td class="vtabla"><input name="des" type="checkbox" id="des" value="1">
        Si</td>
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
	if (form.anio.value < 2005) {
		alert("Debe especificar el año");
		form.anio.select();
		return false;
	}
	else
		form.submit();
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
    <td width="60%" class="print_encabezado" align="center">Costo de Reparto<br>
    al mes de {mes} de {anio} </td>
    <td width="20%">&nbsp;</td>
  </tr>
</table>
<br>
<table align="center" class="print">
  <tr>
    <th class="print" scope="col">Compa&ntilde;&iacute;a</th>
    <!-- START BLOCK : tmes -->
	<th class="print" scope="col">{mes}</th>
	<!-- END BLOCK : tmes -->
  </tr>
  <!-- START BLOCK : cia -->
  <tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
    <td class="vprint">{num_cia} {nombre} </td>
    <!-- START BLOCK : mes -->
	<td class="rprint">{dato}</td>
	<!-- END BLOCK : mes -->
  </tr>
  <!-- END BLOCK : cia -->
  <!-- START BLOCK : totales -->
  <tr>
    <th height="21" class="rprint">Totales</th>
    <!-- START BLOCK : total -->
	<th class="rprint_total">{total}</th>
	<!-- END BLOCK : total -->
  </tr>
  <!-- END BLOCK : totales -->
</table>
<p align="center" style="font-family:Arial, Helvetica, sans-serif; font-size: 8pt; ">Nota: Faltan gastos no identificados.</p>
<table align="center" class="print">
  <tr>
    <th class="print" scope="col">Cod.</th>
    <th class="print" scope="col">Descripci&oacute;n</th>
  </tr>
  <!-- START BLOCK : gasto -->
  <tr>
    <td class="rprint">{cod}</td>
    <td class="vprint">{desc}</td>
  </tr>
  <!-- END BLOCK : gasto -->
</table>
{salto}
<!-- END BLOCK : listado -->
<!-- START BLOCK : desglose -->
<table width="100%">
  <tr>
    <td>&nbsp;</td>
    <td class="print_encabezado" align="center">Oficinas Administrativas Mollendo S. de R.L. y C.V. </td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td width="20%">&nbsp;</td>
    <td width="60%" class="print_encabezado" align="center">Costo Desglosado de Reparto<br>
      del mes de {mes} de {anio} </td>
    <td width="20%">&nbsp;</td>
  </tr>
</table>
<br>
<table align="center" class="print">
  <tr>
    <th class="print" scope="row" style="font-size: 14pt;">{num_cia} {nombre} </th>
  </tr>
</table>
<br>
<table align="center" class="print">
  <tr>
    <th colspan="2" class="print" scope="col">Gastos</th>
    <!-- START BLOCK : tmes_des -->
	<th colspan="2" class="print" scope="col">{mes}</th>
	<!-- END BLOCK : tmes_des -->
  </tr>
  <tr>
    <th class="print" scope="col">C&oacute;digo</th>
    <th class="print" scope="col">Concepto</th>
    <!-- START BLOCK : tdatos -->
	<th class="print" scope="col">Importe</th>
    <th class="print" scope="col">%</th>
	<!-- END BLOCK : tdatos -->
  </tr>
  <!-- START BLOCK : gasto_des -->
  <tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
    <td class="rprint">{codgastos}</td>
    <td class="vprint">{descripcion}</td>
    <!-- START BLOCK : dato -->
	<td class="rprint">{importe}</td>
    <td class="rprint">{porc}</td>
	<!-- END BLOCK : dato -->
  </tr>
  <!-- END BLOCK : gasto_des -->
  <tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
    <td class="rprint">&nbsp;</td>
    <td class="vprint">DEVOLUCI&Oacute;N</td>
    <!-- START BLOCK : devolucion -->
	<td class="rprint">{devolucion}</td>
    <td class="rprint">{porc_dev}</td>
	<!-- END BLOCK : devolucion -->
  </tr>
  <tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
    <td class="rprint">&nbsp;</td>
    <td class="vprint">GANANCIA</td>
    <!-- START BLOCK : ganancia -->
	<td class="rprint">{ganancia}</td>
    <td class="rprint">{porc_gan}</td>
	<!-- END BLOCK : ganancia -->
  </tr>
  <tr>
    <th colspan="2" class="rprint">TOTAL GASTOS </th>
    <!-- START BLOCK : total_gastos -->
	<th colspan="2" class="rprint_total">{total_gastos}</th>
	<!-- END BLOCK : total_gastos -->
    </tr>
  <tr>
    <th colspan="2" class="rprint">REPARTO + PASTELES </th>
	<!-- START BLOCK : reparto -->
    <th colspan="2" class="rprint_total">{reparto}</th>
	<!-- END BLOCK : reparto -->
    </tr>
  <tr>
    <th colspan="2" class="rprint">PORCENTAJE</th>
    <!-- START BLOCK : porc -->
	<th colspan="2" class="rprint_total">{porc}%</th>
	<!-- END BLOCK : porc -->
    </tr>
</table>
<!-- END BLOCK : desglose -->
</body>
</html>
