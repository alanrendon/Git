<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Untitled Document</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../../styles/impresion.css" rel="stylesheet" type="text/css">
<link href="../../styles/tablas.css" rel="stylesheet" type="text/css">
<link href="../../styles/pages.css" rel="stylesheet" type="text/css">
</head>

<body>
<!-- START BLOCK : datos -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Promedios de Consumo </p>
  <form action="./fac_pro_con_v2.php" method="get" name="form">
  <input name="temp" type="hidden">
  <table class="tabla">
    <tr>
      <th class="vtabla">Compa&ntilde;&iacute;a</th>
      <td class="vtabla"><input name="num_cia" type="text" class="insert" id="num_cia" onFocus="temp.value=this.value" onChange="isInt(this,temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 40 || event.keyCode == 38) anio.select();" size="4" maxlength="4"></td>
    </tr>
    <tr>
      <th class="vtabla">Materia Prima </th>
      <td class="vtabla"><input name="codmp" type="text" class="insert" id="codmp" onFocus="temp.value=this.value" onChange="isInt(this,temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 40 || event.keyCode == 38) anio.select();" size="4" maxlength="4"></td>
    </tr>
    <tr>
      <th class="vtabla">A&ntilde;o</th>
      <td class="vtabla"><input name="anio" type="text" class="insert" id="anio" onFocus="temp.value=this.value" onChange="isInt(this,temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 40 || event.keyCode == 38) num_cia.select();" value="{anio}" size="4" maxlength="4"></td>
    </tr>
  </table>  
  <p>
    <input type="button" class="boton" value="Siguiente" onClick="valida_registro(form)">
  </p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
	function valida_registro(form) {
		if (form.num_cia.value <= 0) {
			alert("Debe especificar la compañía");
			form.num_cia.select();
			return false;
		}
		else if (form.codmp.value <= 0) {
			alert("Debe especificar la materia prima");
			form.codmp.select();
			return false;
		}
		else if (form.anio.value < 2000) {
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
<table width="100%" align="center">
  <tr>
    <td width="20%">&nbsp;</td>
    <td width="60%" class="print_encabezado" align="center">Consulta de Promedios de Consumo </td>
    <td width="20%">&nbsp;</td>
  </tr>
</table>
  <br>
  <table align="center" class="print">
    <tr>
      <th class="print" scope="col">Materia Prima </th>
      <th class="print" scope="col">Compa&ntilde;&iacute;a</th>
      <th class="print" scope="col">A&ntilde;o</th>
    </tr>
    <tr>
      <td class="print">{codmp} {nombre_mp} </td>
      <td class="print">{num_cia} {nombre_cia} </td>
      <td class="print">{anio}</td>
    </tr>
  </table>
  <br>
  <table width="70%" align="center" class="print">
    <tr>
      <th width="15%" class="print" scope="col">&nbsp;</th>
      <th width="15%" class="print" scope="col">{anio_ant}</th>
      <th width="15%" class="print" scope="col">{anio_act}</th>
      <th scope="col">&nbsp;</th>
      <th width="30%" scope="col">&nbsp;</th>
      <th width="20%" scope="col">&nbsp;</th>
    </tr>
    <tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
      <td class="vprint"><strong>Enero</strong></td>
      <td class="rprint"><strong>{1_ant}</strong></td>
      <td class="rprint"><strong>{1_act}</strong></td>
      <td>&nbsp;</td>
      <td><strong><font face="Arial, Helvetica, sans-serif" size="-1">Unidad de Consumo</font></strong></td>
      <td><strong><font face="Arial, Helvetica, sans-serif" size="-1">{unidad}</font></strong></td>
    </tr>
    <tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
      <td class="vprint"><strong>Febrero</strong></td>
      <td class="rprint"><strong>{2_ant}</strong></td>
      <td class="rprint"><strong>{2_act}</strong></td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
      <td class="vprint"><strong>Marzo</strong></td>
      <td class="rprint"><strong>{3_ant}</strong></td>
      <td class="rprint"><strong>{3_act}</strong></td>
      <td>&nbsp;</td>
      <td><strong><font face="Arial, Helvetica, sans-serif" size="-1">Tipo</font></strong></td>
      <td><strong><font face="Arial, Helvetica, sans-serif" size="-1">{tipo}</font></strong></td>
    </tr>
    <tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
      <td class="vprint"><strong>Abril</strong></td>
      <td class="rprint"><strong>{4_ant}</strong></td>
      <td class="rprint"><strong>{4_act}</strong></td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
      <td class="vprint"><strong>Mayo</strong></td>
      <td class="rprint"><strong>{5_ant}</strong></td>
      <td class="rprint"><strong>{5_act}</strong></td>
      <td>&nbsp;</td>
      <td><strong><font face="Arial, Helvetica, sans-serif" size="-1">Presentaci&oacute;n</font></strong></td>
      <td><strong><font face="Arial, Helvetica, sans-serif" size="-1">{presentacion}</font></strong></td>
    </tr>
    <tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
      <td class="vprint"><strong>Junio</strong></td>
      <td class="rprint"><strong>{6_ant}</strong></td>
      <td class="rprint"><strong>{6_act}</strong></td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
      <td class="vprint"><strong>Julio</strong></td>
      <td class="rprint"><strong>{7_ant}</strong></td>
      <td class="rprint"><strong>{7_act}</strong></td>
      <td>&nbsp;</td>
      <td><strong><font face="Arial, Helvetica, sans-serif" size="-1">Existencia</font></strong></td>
      <td><strong><font face="Arial, Helvetica, sans-serif" size="-1">{existencia}</font></strong></td>
    </tr>
    <tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
      <td class="vprint"><strong>Agosto</strong></td>
      <td class="rprint"><strong>{8_ant}</strong></td>
      <td class="rprint"><strong>{8_act}</strong></td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
      <td class="vprint"><strong>Septiembre</strong></td>
      <td class="rprint"><strong>{9_ant}</strong></td>
      <td class="rprint"><strong>{9_act}</strong></td>
      <td>&nbsp;</td>
      <td><strong><font face="Arial, Helvetica, sans-serif" size="-1">&iquest;Pedido autom&aacute;tico?</font></strong></td>
      <td><strong><font face="Arial, Helvetica, sans-serif" size="-1">{ped_aut}</font></strong></td>
    </tr>
    <tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
      <td class="vprint"><strong>Octubre</strong></td>
      <td class="rprint"><strong>{10_ant}</strong></td>
      <td class="rprint"><strong>{10_act}</strong></td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
      <td class="vprint"><strong>Noviembre</strong></td>
      <td class="rprint"><strong>{11_ant}</strong></td>
      <td class="rprint"><strong>{11_act}</strong></td>
      <td>&nbsp;</td>
      <td><strong><font face="Arial, Helvetica, sans-serif" size="-1">% para pedido autom&aacute;tico</font></strong></td>
      <td><strong><font face="Arial, Helvetica, sans-serif" size="-1">{por_ped}</font></strong></td>
    </tr>
    <tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
      <td class="vprint"><strong>Diciembre</strong></td>
      <td class="rprint"><strong>{12_ant}</strong></td>
      <td class="rprint"><strong>{12_act}</strong></td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
      <th class="vprint">Total</th>
      <th class="rprint_total">{total_ant}</th>
      <th class="rprint_total">{total_act}</th>
      <td>&nbsp;</td>
      <td><strong><font face="Arial, Helvetica, sans-serif" size="-1">No. de entregas</font></strong></td>
      <td><strong><font face="Arial, Helvetica, sans-serif" size="-1">{num_entregas}</font></strong></td>
    </tr>
    <tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
      <th class="vprint">Promedio</th>
      <th class="rprint_total">{prom_ant}</th>
      <th class="rprint_total">{prom_act}</th>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
  </table>
<!-- END BLOCK : listado -->
</body>
</html>
