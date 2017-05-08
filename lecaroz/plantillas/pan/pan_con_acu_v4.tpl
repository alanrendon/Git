<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Untitled Document</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="/styles/tablas.css" rel="stylesheet" type="text/css">
<link href="/styles/impresion.css" rel="stylesheet" type="text/css">
<link href="/styles/pages.css" rel="stylesheet" type="text/css">
</head>

<body>
<!-- START BLOCK : datos -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Consumos Acumulados en Valores </p>
  <form action="./pan_con_acu_v4.php" method="get" name="form">
    <input type="hidden" name="temp" id="temp">
    <table class="tabla">
      <tr>
        <th class="vtabla" scope="col">Compa&ntilde;&iacute;a</th>
        <th class="vtabla" scope="col"><input name="num_cia" type="text" class="insert" id="num_cia" onFocus="temp.value=this.value" onChange="isInt(this,temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 38 || event.keyCode == 40) fecha.select();" size="3" maxlength="3"></th>
      </tr>
      <tr>
        <th class="vtabla">Fecha <font size="-2">(ddmmaa)</font> </th>
        <th class="vtabla"><input name="fecha" type="text" class="insert" id="fecha" onChange="actualiza_fecha(this)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 38 || event.keyCode == 40) num_cia.select();" value="{fecha}" size="10" maxlength="10"></th>
      </tr>
    </table>
    <p>
    <input type="button" class="boton" value="Siguiente" onClick="valida_registro(form)">
  </p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
	function valida_registro(form) {
		/*if (form.num_cia.value <= 0) {
			alert("Debe especificar la compañía");
			form.num_cia.select();
			return false;
		}
		else*/ if (form.fecha.value == "") {
			alert("Debe especificar la fecha");
			form.fecha.select();
			return false;
		}
		else
			form.submit();
	}
	
	window.onload = document.form.num_cia.select();
</script>
<!-- END BLOCK : datos -->
<!-- START BLOCK : listado -->
<table width="100%">
  <tr>
    <td class="print_encabezado">Cia.: {num_cia}</td>
    <td class="print_encabezado" align="center">{nombre_cia}<br>
      ({nombre_corto})</td>
    <td class="print_encabezado" align="right">Cia.: {num_cia} </td>
  </tr>
  <tr>
    <td width="20%">&nbsp;</td>
    <td width="60%" class="print_encabezado" align="center">Consumos Acumulados en Valores
      al d&iacute;a {dia} de {mes} de {anio} </td>
    <td width="20%">&nbsp;</td>
  </tr>
</table>
  <br>
  <table width="100%" cellpadding="0" cellspacing="0" class="print">
    <tr>
      <th colspan="2" class="print" scope="col">Materia Prima</th>
      <th class="print" scope="col">Costo <br>
        Unitario </th>
      <th class="print" scope="col">FD</th>
      <th class="print" scope="col">FN</th>
      <th class="print" scope="col">BD</th>
      <th class="print" scope="col">REP</th>
      <th class="print" scope="col">PIC</th>
      <th class="print" scope="col">GEL</th>
      <th class="print" scope="col">DESP</th>
      <th class="print" scope="col">Consumo <br>
      Total </th>
    </tr>
    <!-- START BLOCK : fila -->
	<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
      <td width="5%" class="vprint">{codmp}</td>
      <td width="21%" class="vprint">{nombre}</td>
      <td width="8%" class="rprint">{precio_unidad}</td>
      <td width="8%" class="rprint" style="color:#0000CC">{1}</td>
      <td width="8%" class="rprint" style="color:#000099">{2}</td>
      <td width="8%" class="rprint" style="color:#006666">{3}</td>
      <td width="8%" class="rprint" style="color:#990000">{4}</td>
      <td width="8%" class="rprint" style="color:#CC3300">{8}</td>
      <td width="8%" class="rprint" style="color:#663300">{9}</td>
      <td width="8%" class="rprint" style="color:#CC0099">{10}</td>
      <td width="10%" class="rprint"><strong>{consumo}</strong></td>
    </tr>
    <!-- END BLOCK : fila -->
	<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
	  <th colspan="3" class="vprint">Consumo</th>
	  <th class="rprint">{1_consumo}</th>
	  <th class="rprint">{2_consumo}</th>
	  <th class="rprint">{3_consumo}</th>
	  <th class="rprint">{4_consumo}</th>
	  <th class="rprint">{8_consumo}</th>
	  <th class="rprint">{9_consumo}</th>
	  <th class="rprint">{10_consumo}</th>
	  <th class="rprint">{total_consumo}</th>
    </tr>
    <tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
	  <td colspan="11" class="vprint">&nbsp;</td>
    </tr>
    <!-- START BLOCK : fila_nc -->
	<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
      <td width="5%" class="vprint">{codmp}</td>
      <td width="21%" class="vprint">{nombre}</td>
      <td width="8%" class="rprint">{precio_unidad}</td>
      <td width="8%" class="rprint" style="color:#0000CC">{1}</td>
      <td width="8%" class="rprint" style="color:#000099">{2}</td>
      <td width="8%" class="rprint" style="color:#006666">{3}</td>
      <td width="8%" class="rprint" style="color:#990000">{4}</td>
      <td width="8%" class="rprint" style="color:#CC3300">{8}</td>
      <td width="8%" class="rprint" style="color:#663300">{9}</td>
      <td width="8%" class="rprint" style="color:#CC0099">{10}</td>
      <td width="10%" class="rprint">{consumo}</td>
    </tr>
    <!-- END BLOCK : fila_nc -->
	<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
	  <th colspan="3" class="vprint">No controlados</th>
	  <th class="rprint">{1_no_control}</th>
	  <th class="rprint">{2_no_control}</th>
	  <th class="rprint">{3_no_control}</th>
	  <th class="rprint">{4_no_control}</th>
	  <th class="rprint">{8_no_control}</th>
	  <th class="rprint">{9_no_control}</th>
	  <th class="rprint">{10_no_control}</th>
	  <th class="rprint">{total_no_control}</th>
    </tr>
	<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
	  <td colspan="11" class="vprint">&nbsp;</td>
    </tr>
	<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
	  <th colspan="3" class="vprint">+ Mercancias </th>
	  <td class="rprint" style="color:#0000CC">{1_mercancias}</td>
	  <td class="rprint" style="color:#000099">{2_mercancias}</td>
	  <td class="rprint" style="color:#006666">{3_mercancias}</td>
	  <td class="rprint" style="color:#990000">{4_mercancias}</td>
	  <td class="rprint" style="color:#CC3300">{8_mercancias}</td>
	  <td class="rprint" style="color:#663300">{9_mercancias}</td>
	  <td class="rprint" style="color:#CC0099">{10_mercancias}</td>
	  <td class="rprint"><strong>{mercancias}</strong></td>
    </tr>
	<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
	  <th colspan="3" class="vprint">Consumo Total </th>
	  <td class="rprint" style="color:#0000CC">{1_consumo_total}</td>
	  <td class="rprint" style="color:#000099">{2_consumo_total}</td>
	  <td class="rprint" style="color:#006666">{3_consumo_total}</td>
	  <td class="rprint" style="color:#990000">{4_consumo_total}</td>
	  <td class="rprint" style="color:#CC3300">{8_consumo_total}</td>
	  <td class="rprint" style="color:#663300">{9_consumo_total}</td>
	  <td class="rprint" style="color:#CC0099">{10_consumo_total}</td>
	  <td class="rprint"><strong>{consumo_total}</strong></td>
    </tr>
	<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
	  <th colspan="3" class="vprint">Producci&oacute;n</th>
	  <td class="rprint" style="color:#0000CC">{1_produccion}</td>
	  <td class="rprint" style="color:#000099">{2_produccion}</td>
	  <td class="rprint" style="color:#006666">{3_produccion}</td>
	  <td class="rprint" style="color:#990000">{4_produccion}</td>
	  <td class="rprint" style="color:#CC3300">{8_produccion}</td>
	  <td class="rprint" style="color:#663300">{9_produccion}</td>
	  <td class="rprint">&nbsp;</td>
	  <td class="rprint"><strong>{total_produccion}</strong></td>
    </tr>
	<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
	  <th colspan="3" class="vprint">Consumo / Prod. </th>
	  <td class="rprint" style="color:#0000CC">{1_con_pro}</td>
	  <td class="rprint" style="color:#000099">{2_con_pro}</td>
	  <td class="rprint" style="color:#006666">{3_con_pro}</td>
	  <td class="rprint" style="color:#990000">{4_con_pro}</td>
	  <td class="rprint" style="color:#CC3300">{8_con_pro}</td>
	  <td class="rprint" style="color:#663300">{9_con_pro}</td>
	  <td class="rprint">&nbsp;</td>
	  <td class="rprint"><strong>{con_pro}</strong></td>
    </tr>
	<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
	  <th colspan="3" class="vprint">MP / (Prod. - Gel.) (Balance) </th>
	  <td colspan="8" class="rprint"><strong>{mp_pro}</strong></td>
    </tr>
  </table>
<br style="page-break-after:always;">
<!-- END BLOCK : listado -->
</body>
</html>
