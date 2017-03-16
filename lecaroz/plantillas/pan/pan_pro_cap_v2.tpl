<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Untitled Document</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../../styles/tablas.css" rel="stylesheet" type="text/css">
<link href="../../styles/pages.css" rel="stylesheet" type="text/css">
</head>

<body>
<!-- START BLOCK : datos -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Captura de Producci&oacute;n</p>
  <form action="./pan_pro_cap_v2.php" method="get" name="form">
    <input name="tmp" type="hidden" id="tmp">
    <table class="tabla">
    <tr>
      <th class="vtabla" scope="row">Panader&iacute;a</th>
      <td class="vtabla"><input name="num_cia" type="text" class="insert" id="num_cia" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) next.focus()" size="3" maxlength="3"></td>
    </tr>
  </table>  <p>
    <input name="next" type="button" class="boton" id="next" value="Siguiente" onClick="validar()">
  </p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
<!--
var f = document.form, cia = new Array();
<!-- START BLOCK : cia -->
cia[{num_cia}] = true;
<!-- END BLOCK : cia -->

function validar() {
	if (cia[f.num_cia.value] == null) {
		alert("La panadería seleccionada no pertenece a la capturista");
		f.num_cia.value = "";
		f.num_cia.select();
		return false;
	}
	else
		f.submit();
}

window.onload = f.num_cia.select();
-->
</script>
<!-- END BLOCK : datos -->
<!-- START BLOCK : fecha -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Captura de Producci&oacute;n</p>
  <form action="./pan_pro_cap_v2.php" method="get" name="form">
    <input name="num_cia" type="hidden" id="num_cia" value="{num_cia}">
    <table class="tabla">
    <tr>
      <th class="vtabla" scope="row">Fecha de captura inicial </th>
      <td class="vtabla"><input name="fecha" type="text" class="insert" id="fecha" value="{fecha}" size="10" maxlength="10"></td>
    </tr>
  </table>  <p>
    <input type="button" class="boton" value="Regresar" onClick="document.location='./pan_pro_cap_v2.php'">
&nbsp;&nbsp;    
<input type="button" class="boton" value="Siguiente" onClick="validar(this.form)"> 
    </p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
<!--
function validar(f) {
	if (f.fecha.value.length < 8) {
		alert("Debe especificar la fecha inicial de captura");
		f.fecha.select();
		return false;
	}
	else if (confirm("¿Es correcta la fecha inicial de captura?"))
		f.submit();
	else
		f.fecha.select();
}

window.onload = f.fecha.select();
-->
</script>
<!-- END BLOCK : fecha -->
<!-- START BLOCK : captura -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Captura de Producci&oacute;n</p>
  <table class="tabla">
    <tr>
      <th class="tabla" scope="col">Compa&ntilde;&iacute;a</th>
      <th class="tabla" scope="col">Fecha</th>
    </tr>
    <tr>
      <td class="tabla" style="font-size: 12pt; font-weight: bold;">{num_cia} {nombre} </td>
      <td class="tabla" style="font-size: 12pt; font-weight: bold;">{fecha}</td>
    </tr>
  </table>  
  <br>
  <form action="./pan_pro_cap_v2.php" method="post" name="form">
    <input name="num_cia" type="hidden" id="num_cia" value="{num_cia}">
    <input name="fecha" type="hidden" id="fecha" value="{fecha}">
    <input name="tmp" type="hidden" id="tmp">    
    <table width="100%" class="tabla">
    <!-- START BLOCK : turno -->
	<tr>
      <th colspan="7" class="vtabla" scope="col" style="font-size: 12pt;">{turno}
        <input name="ini[]" type="hidden" id="ini" value="{ini}">
        <input name="fin[]" type="hidden" id="fin" value="{fin}"></th>
      </tr>
    <tr>
      <th width="5%" class="tabla"><img src="./menus/insert.gif" width="16" height="16"></th>
      <th width="35%" class="tabla">Producto</th>
      <th width="12%" class="tabla">Piezas</th>
      <th width="12%" class="tabla">Precio<br>
        de Raya</th>
      <th width="12%" class="tabla">Importe<br>
        de Raya</th>
      <th width="12%" class="tabla">Precio<br>
        de Venta </th>
      <th width="12%" class="tabla">Importe de<br>
        Producci&oacute;n </th>
      </tr>
    <!-- START BLOCK : fila -->
	<tr id="row{i}" onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
      <td class="tabla"><img src="./menus/edit.gif" width="16" height="16">&nbsp;<img src="./menus/delete.gif" width="16" height="16"></td>
      <td class="vtabla" style="font-weight: bold;"><input name="cod_pro[]" type="hidden" id="cod_pro" value="{cod}">
        <input name="cod_turno[]" type="hidden" id="cod_turno" value="{cod_turno}">        
        {cod} {nombre} </td>
      <td class="tabla"><input name="piezas[]" type="text" class="rinsert" id="piezas" style="width: 100%;" onFocus="tmp.value=this.value;colorRow({i},'#ACD2DD');this.select()" onBlur="colorRow({i},'')" onChange="if (input_format(this,-1,true)) calcula_produccion({i},{turno})" onKeyDown="if (event.keyCode == 13) piezas[{next}].select()" value="{piezas}" size="8"></td>
      <td class="tabla"><input name="p_raya[]" type="text" class="rnombre" id="p_raya" style="width: 100%;" value="{p_raya}" size="8" readonly="true"></td>
      <td class="tabla"><input name="raya[]" type="text" class="rnombre" id="raya" style="width: 100%; color: #CC0000;" value="{raya}" size="10" readonly="true"></td>
      <td class="tabla"><input name="p_venta[]" type="text" class="rnombre" id="p_venta" style="width: 100%;" value="{p_venta}" size="8" readonly="true"></td>
      <td class="tabla"><input name="pro[]" type="text" class="rnombre" id="pro" style="width: 100%; color: #0000CC;" value="{pro}" size="10" readonly="true"></td>
      </tr>
	<!-- END BLOCK : fila -->
    <tr>
      <th colspan="4" class="rtabla">Totales</th>
      <th class="tabla"><input name="total_raya[]" type="text" class="rnombre" id="total_raya" style="width: 100%; color: #CC0000; font-size: 12pt;" value="{total_raya}" size="10" readonly="true"></th>
      <th class="tabla">&nbsp;</th>
      <th class="tabla"><input name="total_pro[]" type="text" class="rnombre" id="total_pro" style="width: 100%; color: #0000CC; font-size: 12pt;" value="{total_pro}" size="10" readonly="true"></th>
      </tr>
    <tr>
      <th colspan="4" class="rtabla">Raya Pagada </th>
      <th class="tabla"><input name="raya_p[]" type="text" class="rinsert" id="raya_p" style="width: 100%; font-size: 12pt;" value="{raya_p}" size="10"></th>
      <th colspan="2" class="tabla">&nbsp;</th>
      </tr>
    <tr>
      <td colspan="7" class="tabla">&nbsp;</td>
      </tr>
	  <!-- END BLOCK : turno -->
  </table>  
    <br>
    <table class="tabla">
      <tr>
        <th class="tabla" style="font-size: 14pt;" scope="col">Raya Ganada </th>
        <th class="tabla" style="font-size: 14pt;" scope="col">Raya Pagada </th>
        <th class="tabla" style="font-size: 14pt;" scope="col">Producci&oacute;n</th>
      </tr>
      <tr>
        <th class="tabla"><input name="raya_ganada" type="text" disabled="true" class="nombre" id="raya_ganada" style="width: 100%; font-size: 14pt;" value="{raya_ganada}" size="10"></th>
        <th class="tabla"><input name="raya_pagada" type="text" disabled="true" class="nombre" id="raya_pagada" style="width: 100%; font-size: 14pt;" value="{raya_pagada}" size="10"></th>
        <th class="tabla"><input name="produccion" type="text" disabled="true" class="nombre" id="produccion" style="width: 100%; font-size: 14pt;" value="{produccion}" size="10"></th>
      </tr>
    </table>
    <p>
    <input type="button" class="boton" value="Regresar">
    &nbsp;&nbsp;
    <input type="button" class="boton" value="Siguiente">
  </p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
<!--
var f = document.form;

function colorRow(i, color) {
	document.getElementById("row" + i).style.backgroundColor = color;
}

function calcula_produccion(i, turno) {
	var piezas, p_raya, porc, raya, p_venta, pro;
	
	if (f.piezas[i].value == "" || f.piezas[i].value == "0") {
		f.piezas[i].value = "";
		f.raya[i].value   = "";
		f.pro[i].value    = "";
	}
	else {
		piezas  = get_val(f.piezas[i]);
		p_raya  = f.p_raya[i].value.indexOf('%', 0) < 0 ? get_val(f.p_raya[i]) : 0;
		porc    = f.p_raya[i].value.indexOf('%', 0) >= 0 ? get_val(f.p_raya[i]) / 100 : 0;
		p_venta = get_val(f.p_venta[i]);
		pro     = piezas * p_venta;
		
		if (p_raya > 0)
			raya = piezas * p_raya;
		else if (porc > 0)
			raya = pro * porc;
		
		f.raya[i].value = raya > 0 ? number_format(raya, 2) : "";
		f.pro[i].value  = pro > 0 ? number_format(pro, 2) : "";
	}
	
	totales(turno);
}

function totales(turno) {
	var total_raya = 0, total_pro = 0;
	var ini = f.ini.length == undefined ? parseInt(f.ini.value) : parseInt(f.ini[turno].value);
	var fin = f.fin.length == undefined ? parseInt(f.fin.value) : parseInt(f.fin[turno].value);
	
	for (var i = ini; i <= fin; i++)
		if (get_val(f.piezas[i]) > 0) {
			total_raya += get_val(f.raya[i]);
			total_pro  += get_val(f.pro[i]);
		}
	
	if (f.total_raya.length == undefined) {
		f.total_raya.value = number_format(total_raya, 2);
		f.total_pro.value  = number_format(total_pro, 2);
	}
	else {
		f.total_raya[turno].value = number_format(total_raya, 2);
		f.total_pro[turno].value  = number_format(total_pro, 2);
	}
	
	gran_total();
}

function gran_total() {
	var raya_ganada = 0, raya_pagada = 0, prod = 0;
	
	if (f.total_raya.length == undefined) {
		raya_ganada += get_val(f.total_raya);
		raya_pagada += get_val(f.raya_p);
		prod  += get_val(f.total_pro);
	}
	else
		for (var i = 0; i < f.total_raya.length; i++) {
			raya_ganada += get_val(f.total_raya[i]);
			raya_pagada += get_val(f.raya_p[i]);
			prod  += get_val(f.total_pro[i]);
		}
	
	f.raya_ganada.value = number_format(raya_ganada, 2);
	f.raya_pagada.value = number_format(raya_pagada, 2);
	f.produccion.value  = number_format(prod, 2);
}

window.onload = f.piezas[0].select();
-->
</script>
<!-- END BLOCK : captura -->
<!-- START BLOCK : agua -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p style="font-weight:bold; font-family:Arial, Helvetica, sans-serif; font-size:12pt; color:#FF0000">Se le recuerda que no a capturado sus registros de consumo de agua. <br>
  Hasta no haberlo hecho no prodra seguir capturando la producci&oacute;n. </p>
  <p>
    <input type="button" class="boton" value="Regresar" onClick="document.location='./pan_pro_cap_v2.php'">
  </p></td>
</tr>
</table>
<!-- END BLOCK : agua -->
</body>
</html>
