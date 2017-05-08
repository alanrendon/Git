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
<td align="center" valign="middle"><p class="title">Consulta de Efectivos Desglosados </p>
  <form action="./pan_efm_con_v2.php" method="get" name="form">
    <input name="tmp" type="hidden" id="tmp">
    <table class="tabla">
    <tr>
      <th class="vtabla" scope="col">Compa&ntilde;&iacute;a</th>
      <td class="vtabla" scope="col"><input name="num_cia" type="text" class="insert" id="num_cia" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) fecha1.select()" size="3"></td>
    </tr>
    <tr>
      <th class="vtabla">Periodo</th>
      <td class="vtabla"><input name="fecha1" type="text" class="insert" id="fecha1" onChange="actualiza_fecha(this)" onKeyDown="if (event.keyCode == 13) fecha2.select()" value="{fecha1}" size="10" maxlength="10">
        al
          <input name="fecha2" type="text" class="insert" id="fecha2" onChange="actualiza_fecha(this)" onKeyDown="if (event.keyCode == 13) num_cia.select()" value="{fecha2}" size="10" maxlength="10"></td>
    </tr>
  </table>  <p>
    <input type="button" class="boton" value="Siguiente" onClick="validar()">
  </p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
<!--
var f = document.form;

function validar() {
	if (f.fecha1.length < 8) {
		alert("Debe especificar el periodo o el dia a consultar");
		f.fecha1.select();
		return false;
	}
	else
		f.submit();
}

window.onload = f.num_cia.select();
//-->
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
    <td width="60%" class="print_encabezado" align="center">Efectivos Desglosados<br>
      {periodo} </td>
    <td width="20%">&nbsp;</td>
  </tr>
</table>
<br>
<table align="center" class="print">
  <tr>
    <th rowspan="2" class="print" scope="col">Compa&ntilde;&iacute;a</th>
    <th colspan="2" class="print" scope="col">AM</th>
    <th colspan="2" class="print" scope="col">PM</th>
    <th rowspan="2" class="print" scope="col">Pastel</th>
    <th rowspan="2" class="print" scope="col">Venta en<br>
      Puerta</th>
    <th rowspan="2" class="print" scope="col">Pastillaje</th>
    <th rowspan="2" class="print" scope="col">Otros</th>
    <!-- START BLOCK : hbloque -->
	<th rowspan="2" class="print" scope="col">Clientes</th>
	<th rowspan="2" class="print" scope="col">Corte<br>
    Pan</th>
    <th rowspan="2" class="print" scope="col">Corte<br>
    Pastel</th>
	<!-- END BLOCK : hbloque -->
    <th rowspan="2" class="print" scope="col">Descuento<br>
    de Pastel</th>
  </tr>
  <tr>
    <th class="print">Total</th>
    <th class="print">Error</th>
    <th class="print">Total</th>
    <th class="print">Error</th>
  </tr>
  <!-- START BLOCK : fila -->
  <tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
    <td class="vprint">{num_cia} {nombre} </td>
    <td class="rprint">{am}</td>
    <td class="rprint" style="color:#CC0000;">{am_error}</td>
    <td class="rprint">{pm}</td>
    <td class="rprint" style="color:#CC0000;">{pm_error}</td>
    <td class="rprint">{pastel}</td>
    <td class="rprint" style="color:#0000CC; font-weight:bold;">{venta_pta}</td>
    <td class="rprint">{pastillaje}</td>
    <td class="rprint">{otros}</td>
    <!-- START BLOCK : bloque -->
	<td class="rprint">{ctes}</td>
	<td class="print">{corte1}</td>
    <td class="print">{corte2}</td>
	<!-- END BLOCK : bloque -->
    <td class="rprint">{desc_pastel}</td>
  </tr>
  <!-- END BLOCK : fila -->
</table>
<!-- END BLOCK : listado -->
</body>
</html>
