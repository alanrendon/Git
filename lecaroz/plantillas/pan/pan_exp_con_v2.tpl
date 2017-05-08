<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Untitled Document</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../../styles/impresion.css" rel="stylesheet" type="text/css">
<link href="../../styles/tablas.css" rel="stylesheet" type="text/css">
<link href="../../styles/pages.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="jscripts/mootools/mootools-1.2-core.js"></script>
</head>

<body>
<!-- START BLOCK : datos1 -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Listados de Expendios </p>
  <form action="./pan_exp_con_v2.php" method="get" name="form">
  <input name="temp" type="hidden">
  <table class="tabla">
  <tr>
    <th class="vtabla" scope="row">Compa&ntilde;&iacute;a</th>
    <td class="vtabla"><input name="num_cia" type="text" class="insert" id="num_cia" onFocus="temp.value=this.value" onChange="isInt(this,temp)" onKeyDown="if (event.keyCode == 13) fecha1.select()" size="3" maxlength="3"></td>
  </tr>
  <tr>
  	<th class="vtabla" scope="row">Administrador</th>
  	<td class="vtabla"><select name="admin" class="insert" id="admin">
  		<option value=""></option>
  		<!-- START BLOCK : admin -->
		<option value="{value}">{text}</option>
		<!-- END BLOCK : admin -->
  		</select></td>
  	</tr>
  <tr>
    <th class="vtabla" scope="row">Fecha</th>
    <td class="vtabla"><input name="fecha1" type="text" class="insert" id="fecha1" onChange="actualiza_fecha(this)" onKeyDown="if (event.keyCode == 13) fecha2.select()" value="{fecha1}" size="10" maxlength="10">
      a
        <input name="fecha2" type="text" class="insert" id="fecha2" onChange="actualiza_fecha(this)" onKeyDown="if (event.keyCode == 13) num_cia.select()" value="{fecha2}" size="10" maxlength="10"></td>
  </tr>
  <tr>
    <th class="vtabla" scope="row">Tipo</th>
    <td class="vtabla"><input name="tipo" type="radio" value="1" checked>
      Desglosado
        <input name="tipo" type="radio" value="2">
        Totales
        <input type="radio" name="tipo" value="3">
        Rezagos </td>
  </tr>
  <tr>
    <th class="vtabla" scope="row">Agente de Ventas </th>
    <td class="vtabla"><select name="idagven" class="insert" id="idagven">
      <option value="">-</option>
      <!-- START BLOCK : idagven -->
	  <option value="{id}">{nombre}</option>
	  <!-- END BLOCK : idagven -->
    </select></td>
  </tr>
  <tr>
    <th class="vtabla" scope="row">Con aumento de rezago </th>
    <td class="vtabla"><input name="aumento" type="checkbox" id="aumento" value="1">
      Si</td>
  </tr>
</table>

  <p>
    <input type="button" class="boton" value="Siguiente" onClick="validar(form)">
  </p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
	function validar(form) {
		if (form.num_cia.value <= 0 && form.idagven.value <= 0 && !form.tipo[2].checked) {
			alert("Debe especificar el número de compañía o seleccionar un agente de ventas");
			form.num_cia.select();
			return false;
		}
		else if (form.tipo[0].checked && form.fecha1.value.length < 8) {
			alert("Debe especificar al menos la fecha inicial");
			form.fecha1.select();
			return false;
		}
		else if (form.tipo[1].checked && form.fecha1.value.length < 8 && form.fecha2.value.length < 8) {
			alert("Debe especificar fecha de inicio y fecha final");
			form.fecha1.select();
			return false;
		}
		else if (form.tipo[1].checked && form.num_cia.value <= 0) {
			alert("Debe especificar el número de compañía");
			form.num_cia.select();
			return false;
		}
		else
			form.submit()
	}
	
	window.onload = document.form.num_cia.select();
</script>
<!-- END BLOCK : datos1 -->
<!-- START BLOCK : datos2 -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Listados de Expendios </p>
  <form action="./pan_exp_con_v2.php" method="get" name="form">
  <input name="temp" type="hidden">
  <input name="tipo" type="hidden" value="1">
  <input name="idagven" type="hidden" value="1">
  <table class="tabla">
  <tr>
    <th class="vtabla" scope="row">Fecha</th>
    <td class="vtabla"><input name="fecha1" type="text" class="insert" id="fecha1" onChange="actualiza_fecha(this)" onKeyDown="if (event.keyCode == 13) fecha2.select()" value="{fecha1}" size="10" maxlength="10">
      a
        <input name="fecha2" type="text" class="insert" id="fecha2" onChange="actualiza_fecha(this)" onKeyDown="if (event.keyCode == 13) fecha1.select()" value="{fecha2}" size="10" maxlength="10"></td>
  </tr>
  <tr>
    <th class="vtabla" scope="row">Con aumento de rezago </th>
    <td class="vtabla"><input name="aumento" type="checkbox" id="aumento" value="1">
      Si</td>
  </tr>
</table>

  <p>
    <input type="button" class="boton" value="Siguiente" onClick="validar(form)">
  </p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
	function validar(form) {
		if (form.fecha1.value.length < 8) {
			alert("Debe especificar al menos la fecha inicial");
			form.fecha1.select();
			return false;
		}
		else
			form.submit()
	}
	
	window.onload = document.form.fecha1.select();
</script>
<!-- END BLOCK : datos2 -->
<!-- START BLOCK : saldos -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="top">
<table width="100%">
  <tr>
    <td class="print_encabezado">Cia.: {num_cia} </td>
    <td class="print_encabezado" align="center">{nombre_cia}</td>
    <td class="print_encabezado" align="right">Cia.: {num_cia} </td>
  </tr>
  <tr>
    <td width="20%">&nbsp;</td>
    <td width="60%" class="print_encabezado" align="center">Saldos de los Expendios <br>
      del {dia1} de {mes1} de {anio1} al {dia2} de {mes2} de {anio2} </td>
    <td width="20%">&nbsp;</td>
  </tr>
</table>
  <br>
  <table class="print">
    <tr>
      <th colspan="2" class="print" scope="row">N&uacute;mero y nombre del expendio </th>
      <th class="print">Saldo anterior </th>
      <th class="print">Precio Venta </th>
      <th class="print">Precio Expendio </th>
      <th class="print">Diferencia</th>
      <th class="print">%</th>
      <th class="print">Abono</th>
      <th class="print">Devoluci&oacute;n</th>
      <th class="print">Saldo Actual</th>
      <th class="print">Dif. Saldo </th>
      <th class="print">Prom</th>
      <th class="print">D&iacute;as</th>
    </tr>
    <!-- START BLOCK : fila -->
	<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
      <td class="print" scope="row">{num_exp}</td>
      <td class="vprint">{nombre_exp}</td>
      <td class="rprint">{saldo_anterior}</td>
      <td class="rprint">{precio_venta}</td>
      <td class="rprint">{precio_exp}</td>
      <td class="rprint">{diferencia}</td>
      <td class="rprint">{porc}</td>
      <td class="rprint">{abono}</td>
      <td class="rprint">{devolucion}</td>
      <td class="rprint">{saldo_actual}</td>
      <td class="rprint">{dif_saldo}</td>
	  <td class="rprint">{prom}</td>
	  <td class="rprint">{dias}</td>
	</tr>
	<!-- END BLOCK : fila -->
    <tr>
      <th colspan="2" class="print" scope="row">Totales del d&iacute;a </th>
      <th class="rprint_total">{saldo_anterior}</th>
      <th class="rprint_total">{precio_venta}</th>
      <th class="rprint_total">{precio_exp}</th>
      <th class="rprint_total">{diferencia}</th>
      <th class="rprint_total">{porc}</th>
      <th class="rprint_total">{abono}</th>
      <th class="rprint_total">{devolucion}</th>
      <th class="rprint_total">{saldo_actual}</th>
      <th class="rprint_total">&nbsp;</th>
      <th class="rprint_total">&nbsp;</th>
      <th class="rprint_total">&nbsp;</th>
    </tr>
  </table>
</td>
</tr>
</table>
<!-- END BLOCK : saldos -->
<!-- START BLOCK : movimientos -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="top">
<table width="100%">
  <tr>
    <td class="print_encabezado">Cia.: {num_cia} </td>
    <td class="print_encabezado" align="center">{nombre_cia}</td>
    <td class="print_encabezado" align="right">Cia.: {num_cia}</td>
  </tr>
  <tr>
    <td width="20%">&nbsp;</td>
    <td width="60%" class="print_encabezado" align="center">Movimientos de Expendios <br>
      al {dia} de {mes} de {anio} </td>
    <td width="20%">&nbsp;</td>
  </tr>
</table>
  <br>
  <table class="print">
    <tr>
      <th class="print" scope="col">Dia</th>
      <th class="print" scope="col">Precio Venta </th>
      <th class="print" scope="col">Precio Expendio </th>
      <th class="print" scope="col">Diferencia</th>
      <th class="print" scope="col">%</th>
      <th class="print" scope="col">Abono</th>
    </tr>
    <!-- START BLOCK : dia -->
	<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
      <td class="print">{dia}</td>
      <td class="rprint">{precio_venta}</td>
      <td class="rprint">{precio_expendio}</td>
      <td class="rprint">{diferencia}</td>
      <td class="rprint">{porc}</td>
      <td class="rprint">{abono}</td>
    </tr>
	<!-- END BLOCK : dia -->
    <tr>
      <th class="print">Totales</th>
      <th class="rprint_total">{precio_venta}</th>
      <th class="rprint_total">{precio_expendio}</th>
      <th class="rprint_total">{diferencia}</th>
      <th class="rprint_total">{porc}</th>
      <th class="rprint_total">{abono}</th>
    </tr>
    <tr>
      <th class="print">Promedios</th>
      <th class="rprint_total">{prom_venta}</th>
      <th class="rprint_total">{prom_expendio}</th>
      <th class="rprint_total">{prom_diferencia}</th>
      <th class="rprint_total">{prom_porc}</th>
      <th class="rprint_total">{prom_abono}</th>
    </tr>
  </table>
</td>
</tr>
</table>
<!-- END BLOCK : movimientos -->
<!-- START BLOCK : agentes -->
<script language="javascript">
function detalleRezago(num_cia, num_exp, nombre_exp, fecha1, fecha2, dias) {
	var url = 'ExpendioDetalleRezago.php',
		param = '?accion=reporte&num_cia=' + num_cia + '&num_exp=' + num_exp + '&nombre_exp=' + nombre_exp + '&fecha1=' + fecha1 + '&fecha2=' + fecha2 + '&dias=' + dias,
		opt = 'toolbar=no,location=no,directories=no,status=no,menubar=yes,scrollbars=yes,resizable=yes,width=1024,height=768',
		win;
	
	win = window.open(url + param, 'reporte_detalle_rezago', opt);
	
	win.focus();
}
</script>
<table width="100%">
  <tr>
    <td>&nbsp;</td>
    <td class="print_encabezado" align="center">Oficinas Administrativas Mollendo S. de R.L. y C.V. </td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td width="20%">&nbsp;</td>
    <td width="60%" class="print_encabezado" align="center">Movimientos de Expendios <br>
      del {dia1} de {mes1} de {anio1} al {dia2} de {mes2} de {anio2} </td>
    <td width="20%">&nbsp;</td>
  </tr>
</table>
<br>
<table align="center" class="print">
    <tr>
      <th class="print" scope="row">Compa&ntilde;&iacute;a</th>
      <th class="print" scope="row">Expendio</th>
      <th class="print">Saldo anterior </th>
      <th class="print">Precio Venta </th>
      <th class="print">Precio Expendio </th>
      <th class="print">Diferencia</th>
      <th class="print">%</th>
      <th class="print">Abono</th>
      <th class="print">Devoluci&oacute;n</th>
      <th class="print">Saldo Actual</th>
      <th class="print">Dif. Saldo </th>
      <th class="print">Prom</th>
      <th class="print">D&iacute;as</th>
    </tr>
	<!-- START BLOCK : cia_ag -->
    <!-- START BLOCK : fila_ag -->
	<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
	  <td class="vprint" scope="row">{num_cia} {nombre} </td>
      <td class="vprint" scope="row">{num_exp} {nombre_exp}</td>
      <td class="rprint">{saldo_anterior}</td>
      <td class="rprint">{precio_venta}</td>
      <td class="rprint">{precio_exp}</td>
      <td class="rprint">{diferencia}</td>
      <td class="rprint">{porc}</td>
      <td class="rprint">{abono}</td>
      <td class="rprint">{devolucion}</td>
      <td class="rprint">{saldo_actual}</td>
      <td class="rprint">{dif_saldo}</td>
	  <td class="rprint">{prom}</td>
	  <td class="rprint">{dias}</td>
	</tr>
	<!-- END BLOCK : fila_ag -->
	<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
	  <th colspan="2" class="rprint" scope="row">Totales Cia. </th>
	  <th class="rprint">{saldo_anterior}</th>
	  <th class="rprint">{precio_venta}</th>
	  <th class="rprint">{precio_exp}</th>
	  <th class="rprint">{diferencia}</th>
	  <th class="rprint">{porc}</th>
	  <th class="rprint">{abono}</th>
	  <th class="rprint">{devolucion}</th>
	  <th class="rprint">{saldo_actual}</th>
	  <th colspan="3" class="rprint">&nbsp;</th>
  </tr>
	<!-- END BLOCK : cia_ag -->
    <tr>
      <th colspan="2" class="rprint" scope="row">Totales del d&iacute;a </th>
      <th class="rprint_total">{saldo_anterior}</th>
      <th class="rprint_total">{precio_venta}</th>
      <th class="rprint_total">{precio_exp}</th>
      <th class="rprint_total">{diferencia}</th>
      <th class="rprint_total">{porc}</th>
      <th class="rprint_total">{abono}</th>
      <th class="rprint_total">{devolucion}</th>
      <th class="rprint_total">{saldo_actual}</th>
      <th colspan="3" class="rprint_total">&nbsp;</th>
    </tr>
</table>
<!-- END BLOCK : agentes -->
<!-- START BLOCK : rezagos -->
<table width="100%">
  <tr>
    <td>&nbsp;</td>
    <td class="print_encabezado" align="center">Oficinas Administrativas Mollendo S. de R.L. y C.V. </td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td width="20%">&nbsp;</td>
    <td width="60%" class="print_encabezado" align="center">Rezagos de Expendios <br>
      del {dia1} de {mes1} de {anio1} al {dia2} de {mes2} de {anio2} </td>
    <td width="20%">&nbsp;</td>
  </tr>
</table>
<br>
<table align="center" class="print">
	<tr>
		<th class="print" scope="col">Compa&ntilde;&iacute;a</th>
		<th class="print" scope="col">Rezago inicial</th>
		<th class="print" scope="col">Rezago final</th>
		<th class="print" scope="col">Diferencia</th>
		<th class="print" scope="col">Variaci&oacute;n</th>
	</tr>
	<!-- START BLOCK : fila_rezago -->
	<tr>
		<td class="vprint">{num_cia} {nombre_cia}</td>
		<td class="rprint">{rezago_ini}</td>
		<td class="rprint">{rezago_fin}</td>
		<td class="rprint" style="color:#{color_dif}">{dif}</td>
		<td class="vprint" style="color:#{color_dif}">{var}</td>
	</tr>
	<!-- END BLOCK : fila_rezago -->
	<tr>
		<th class="rprint">Totales</th>
		<th class="rprint">{rezago_ini}</th>
		<th class="rprint">{rezago_fin}</th>
		<th class="rprint">{dif}</th>
		<th class="print">&nbsp;</th>
	</tr>
</table>
<!-- END BLOCK : rezagos -->
</body>
</html>
