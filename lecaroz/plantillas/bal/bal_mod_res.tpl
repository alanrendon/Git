<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Untitled Document</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../../styles/tablas.css" rel="stylesheet" type="text/css">
<link href="../../styles/pages.css" rel="stylesheet" type="text/css">
<style>
@media print {
	#mes {
		display: none;
	}
	
	.boton {
		display: none;
	}
}
</style>
</head>

<body>
<!-- START BLOCK : datos -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Modificaci&oacute;n de Reservas</p>
  <form action="./bal_mod_res.php" method="get" name="form">
    <input name="tmp" type="hidden" id="tmp">
    <table class="tabla">
    <tr>
      <th class="vtabla" scope="row">Compa&ntilde;&iacute;a</th>
      <td class="vtabla"><input name="num_cia" type="text" class="insert" id="num_cia" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) anio.select()" size="3"></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">A&ntilde;o</th>
      <td class="vtabla"><input name="anio" type="text" class="insert" id="anio" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) num_cia.select()" value="{anio}" size="4" maxlength="4"></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">C&oacute;digo</th>
      <td class="vtabla"><select name="cod_reserva" class="insert" id="cod_reserva">
        <!-- START BLOCK : cod -->
		<option value="{cod}"{selected}>{cod} {nombre}</option>
		<!-- END BLOCK : cod -->
      </select>
      <span style="font-size:6pt;">(
      <input name="por_promedio" type="checkbox" id="por_promedio" value="1" checked>
      usar promedio del a&ntilde;o pasado en IMSS)</span></td>
    </tr>
  </table>  <p>
    <input type="button" class="boton" value="Siguiente" onClick="validar(this.form)">
  </p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
<!--
function validar(form) {
	if (form.num_cia.value <= 0) {
		alert("Debe especificar la compañía");
		form.num_cia.select();
		return false;
	}
	else if (form.anio.value <= 0) {
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
<!-- START BLOCK : reservas -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Modificaci&oacute;n de Reservas</p>
  <form action="./bal_mod_res.php" method="post" name="form">
    <input name="tmp" type="hidden" id="tmp">
	<input name="promedio_ant" type="hidden" id="promedio_ant" value="{promedio_ant}">
    <table class="tabla">
    <tr>
      <th class="vtabla" scope="row">Compa&ntilde;&iacute;a</th>
      <td class="vtabla"><input name="num_cia" type="text" class="insert" id="num_cia" value="{num_cia}" size="3" readonly>
        <input name="nombre" type="text" disabled="true" class="vnombre" id="nombre" value="{nombre_cia}" size="30"></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">A&ntilde;o</th>
      <td class="vtabla"><input name="anio" type="text" class="insert" id="anio" value="{anio}" size="4" maxlength="4" readonly></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">C&oacute;digo</th>
      <td class="vtabla"><input name="cod_reserva" type="text" class="insert" id="cod_reserva" value="{cod_reserva}" size="3">
        <input name="nombre_reserva" type="text" disabled="true" class="vnombre" id="nombre_reserva" value="{nombre_reserva}" size="30"></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Importe</th>
      <td class="vtabla"><input name="importe_gral" type="text" class="rinsert" id="importe_gral" onFocus="tmp.value=this.value;this.select()" onBlur="if (input_format(this,2,true)) updateInputs()" onKeyDown="if (event.keyCode == 13) this.blur()" size="10"></td>
    </tr>
  </table>  
  <br>
  <table>
  <tr><td>
  <table class="tabla">
    <tr>
      <th class="tabla" scope="col">Mes<br>
        &nbsp;</th>
      <th class="tabla" scope="col">Reserva<br>
        &nbsp;</th>
      <th class="tabla" scope="col">Pagado<br>
        &nbsp;</th>
      <th class="tabla" scope="col">Promedio<br>
        &nbsp;</th>
      </tr>
    <tr>
      <td class="vtabla"><input name="mes[]" type="checkbox" id="mes" onClick="updateInputs()" value="1"{checked1}>
        Enero</td>
      <td class="tabla"><input name="importe[]" type="text" class="rnombre" id="importe" style="color:#0000CC;" value="{importe1}" size="10" readonly></td>
      <td class="tabla"><input name="pagado[]" type="text" disabled="true" class="rnombre" id="pagado" style="color:#CC0000;" value="{pagado1}" size="10"></td>
      <td class="tabla"><input name="promedio[]" type="text" disabled="true" class="rnombre" id="promedio" style="color:#660099;" value="{promedio1}" size="10"></td>
      </tr>
    <tr>
      <td class="vtabla"><input name="mes[]" type="checkbox" id="mes" onClick="updateInputs()" value="2"{checked2}>
        Febrero</td>
      <td class="tabla"><input name="importe[]" type="text" class="rnombre" id="importe" style="color:#0000CC;" value="{importe2}" size="10" readonly></td>
      <td class="tabla"><input name="pagado[]" type="text" disabled="true" class="rnombre" id="pagado" style="color:#CC0000;" value="{pagado2}" size="10"></td>
      <td class="tabla"><input name="promedio[]" type="text" disabled="true" class="rnombre" id="promedio" style="color:#660099;" value="{promedio2}" size="10"></td>
      </tr>
    <tr>
      <td class="vtabla"><input name="mes[]" type="checkbox" id="mes" onClick="updateInputs()" value="3"{checked3}>
        Marzo</td>
      <td class="tabla"><input name="importe[]" type="text" class="rnombre" id="importe" style="color:#0000CC;" value="{importe3}" size="10" readonly></td>
      <td class="tabla"><input name="pagado[]" type="text" disabled="true" class="rnombre" id="pagado" style="color:#CC0000;" value="{pagado3}" size="10"></td>
      <td class="tabla"><input name="promedio[]" type="text" disabled="true" class="rnombre" id="promedio" style="color:#660099;" value="{promedio3}" size="10"></td>
      </tr>
    <tr>
      <td class="vtabla"><input name="mes[]" type="checkbox" id="mes" onClick="updateInputs()" value="4"{checked4}>
        Abril</td>
      <td class="tabla"><input name="importe[]" type="text" class="rnombre" id="importe" style="color:#0000CC;" value="{importe4}" size="10" readonly></td>
      <td class="tabla"><input name="pagado[]" type="text" disabled="true" class="rnombre" id="pagado" style="color:#CC0000;" value="{pagado4}" size="10"></td>
      <td class="tabla"><input name="promedio[]" type="text" disabled="true" class="rnombre" id="promedio" style="color:#660099;" value="{promedio4}" size="10"></td>
      </tr>
    <tr>
      <td class="vtabla"><input name="mes[]" type="checkbox" id="mes" onClick="updateInputs()" value="5"{checked5}>
        Mayo</td>
      <td class="tabla"><input name="importe[]" type="text" class="rnombre" id="importe" style="color:#0000CC;" value="{importe5}" size="10" readonly></td>
      <td class="tabla"><input name="pagado[]" type="text" disabled="true" class="rnombre" id="pagado" style="color:#CC0000;" value="{pagado5}" size="10"></td>
      <td class="tabla"><input name="promedio[]" type="text" disabled="true" class="rnombre" id="promedio" style="color:#660099;" value="{promedio5}" size="10"></td>
      </tr>
    <tr>
      <td class="vtabla"><input name="mes[]" type="checkbox" id="mes" onClick="updateInputs()" value="6"{checked6}>
        Junio</td>
      <td class="tabla"><input name="importe[]" type="text" class="rnombre" id="importe" style="color:#0000CC;" value="{importe6}" size="10" readonly></td>
      <td class="tabla"><input name="pagado[]" type="text" disabled="true" class="rnombre" id="pagado" style="color:#CC0000;" value="{pagado6}" size="10"></td>
      <td class="tabla"><input name="promedio[]" type="text" disabled="true" class="rnombre" id="promedio" style="color:#660099;" value="{promedio6}" size="10"></td>
      </tr>
    <tr>
      <td class="vtabla"><input name="mes[]" type="checkbox" id="mes" onClick="updateInputs()" value="7"{checked7}>
        Julio</td>
      <td class="tabla"><input name="importe[]" type="text" class="rnombre" id="importe" style="color:#0000CC;" value="{importe7}" size="10" readonly></td>
      <td class="tabla"><input name="pagado[]" type="text" disabled="true" class="rnombre" id="pagado" style="color:#CC0000;" value="{pagado7}" size="10"></td>
      <td class="tabla"><input name="promedio[]" type="text" disabled="true" class="rnombre" id="promedio" style="color:#660099;" value="{promedio7}" size="10"></td>
      </tr>
    <tr>
      <td class="vtabla"><input name="mes[]" type="checkbox" id="mes" onClick="updateInputs()" value="8"{checked8}>
        Agosto</td>
      <td class="tabla"><input name="importe[]" type="text" class="rnombre" id="importe" style="color:#0000CC;" value="{importe8}" size="10" readonly></td>
      <td class="tabla"><input name="pagado[]" type="text" disabled="true" class="rnombre" id="pagado" style="color:#CC0000;" value="{pagado8}" size="10"></td>
      <td class="tabla"><input name="promedio[]" type="text" disabled="true" class="rnombre" id="promedio" style="color:#660099;" value="{promedio8}" size="10"></td>
      </tr>
    <tr>
      <td class="vtabla"><input name="mes[]" type="checkbox" id="mes" onClick="updateInputs()" value="9"{checked9}>
        Septiembre</td>
      <td class="tabla"><input name="importe[]" type="text" class="rnombre" id="importe" style="color:#0000CC;" value="{importe9}" size="10" readonly></td>
      <td class="tabla"><input name="pagado[]" type="text" disabled="true" class="rnombre" id="pagado" style="color:#CC0000;" value="{pagado9}" size="10"></td>
      <td class="tabla"><input name="promedio[]" type="text" disabled="true" class="rnombre" id="promedio" style="color:#660099;" value="{promedio9}" size="10"></td>
      </tr>
    <tr>
      <td class="vtabla"><input name="mes[]" type="checkbox" id="mes" onClick="updateInputs()" value="10"{checked10}>
        Octubre</td>
      <td class="tabla"><input name="importe[]" type="text" class="rnombre" id="importe" style="color:#0000CC;" value="{importe10}" size="10" readonly></td>
      <td class="tabla"><input name="pagado[]" type="text" disabled="true" class="rnombre" id="pagado" style="color:#CC0000;" value="{pagado10}" size="10"></td>
      <td class="tabla"><input name="promedio[]" type="text" disabled="true" class="rnombre" id="promedio" style="color:#660099;" value="{promedio10}" size="10"></td>
      </tr>
    <tr>
      <td class="vtabla"><input name="mes[]" type="checkbox" id="mes" onClick="updateInputs()" value="11"{checked11}>
        Noviembre</td>
      <td class="tabla"><input name="importe[]" type="text" class="rnombre" id="importe" style="color:#0000CC;" value="{importe11}" size="10" readonly></td>
      <td class="tabla"><input name="pagado[]" type="text" disabled="true" class="rnombre" id="pagado" style="color:#CC0000;" value="{pagado11}" size="10"></td>
      <td class="tabla"><input name="promedio[]" type="text" disabled="true" class="rnombre" id="promedio" style="color:#660099;" value="{promedio11}" size="10"></td>
      </tr>
    <tr>
      <td class="vtabla"><input name="mes[]" type="checkbox" disabled="true" id="mes" onClick="updateInputs()" value="12"{checked12}>
        Diciembre</td>
      <td class="tabla"><input name="importe[]" type="text" class="rnombre" id="importe" style="color:#0000CC;" value="{importe12}" size="10" readonly></td>
      <td class="tabla"><input name="pagado[]" type="text" disabled="true" class="rnombre" id="pagado" style="color:#CC0000;" value="{pagado12}" size="10"></td>
      <td class="tabla"><input name="promedio[]" type="text" disabled="true" class="rnombre" id="promedio" style="color:#660099;" value="{promedio12}" size="10"></td>
      </tr>
    <tr>
      <th class="rtabla">Total</th>
      <th class="tabla"><input name="total_reserva" type="text" class="rnombre" id="total_reserva" value="{total_reserva}" size="10"></th>
      <th class="tabla"><input name="total_pagado" type="text" disabled="true" class="rnombre" id="total_pagado" value="{total_pagado}" size="10"></th>
      <th class="tabla">&nbsp;</th>
      </tr>
    <tr>
      <th class="rtabla">Diferencia</th>
      <th colspan="3" class="tabla"><input name="gran_total" type="text" disabled="true" class="nombre" id="gran_total" style="width: 100%;" value="{gran_total}" size="10"></th>
      </tr>
  </table>
  </td>
  <td valign="top">
  	<!-- START BLOCK : costo_emp -->
	<table class="tabla">
  <tr>
    <th valign="top" class="tabla" scope="col">Empleados<br>
      &nbsp;</th>
    <th valign="top" class="tabla" scope="col">Infonavit<br>
      &nbsp;</th>
    <th valign="top" class="tabla" scope="col">% <br>
      Riesgo </th>
    <th valign="top" class="tabla" scope="col">Costo x <br>
      Empleado </th>
  </tr>
  <tr>
    <td class="tabla"><input name="empleados[]" type="text" disabled class="nombre" style="width:99%;color:#00C;" id="empleados" value="{empleados1}" size="3"></td>
    <td class="tabla"><input name="infonavit[]" type="text" disabled class="rnombre" style="width:99%;color:#F30;" id="infonavit" value="{infonavit1}" size="8"></td>
    <td class="tabla"><input name="prima[]" type="text" disabled class="rnombre" style="width:99%;color:#606;" id="prima[]" value="{prima1}" size="9"></td>
    <td class="tabla"><input name="costo_emp[]" type="text" disabled class="rnombre" style="width:99%;color:#C00;" id="costo_emp" value="{costo_emp1}" size="8"></td>
  </tr>
  <tr>
    <td class="tabla"><input name="empleados[]" type="text" disabled class="nombre" style="width:99%;color:#00C;" id="empleados" value="{empleados2}" size="3"></td>
    <td class="tabla"><input name="infonavit[]" type="text" disabled class="rnombre" style="width:99%;color:#F30;" id="infonavit" value="{infonavit2}" size="8"></td>
    <td class="tabla"><input name="prima[]" type="text" disabled class="rnombre" style="width:99%;color:#606;" id="prima[]" value="{prima2}" size="9"></td>
    <td class="tabla"><input name="costo_emp[]" type="text" disabled class="rnombre" style="width:99%;color:#C00;" id="costo_emp" value="{costo_emp2}" size="8"></td>
  </tr>
  <tr>
    <td class="tabla"><input name="empleados[]" type="text" disabled class="nombre" style="width:99%;color:#00C;" id="empleados" value="{empleados3}" size="3"></td>
    <td class="tabla"><input name="infonavit[]" type="text" disabled class="rnombre" style="width:99%;color:#F30;" id="infonavit" value="{infonavit3}" size="8"></td>
    <td class="tabla"><input name="prima[]" type="text" disabled class="rnombre" style="width:99%;color:#606;" id="prima[]" value="{prima3}" size="9"></td>
    <td class="tabla"><input name="costo_emp[]" type="text" disabled class="rnombre" style="width:99%;color:#C00;" id="costo_emp" value="{costo_emp3}" size="8"></td>
  </tr>
  <tr>
    <td class="tabla"><input name="empleados[]" type="text" disabled class="nombre" style="width:99%;color:#00C;" id="empleados" value="{empleados4}" size="3"></td>
    <td class="tabla"><input name="infonavit[]" type="text" disabled class="rnombre" style="width:99%;color:#F30;" id="infonavit" value="{infonavit4}" size="8"></td>
    <td class="tabla"><input name="prima[]" type="text" disabled class="rnombre" style="width:99%;color:#606;" id="prima[]" value="{prima4}" size="9"></td>
    <td class="tabla"><input name="costo_emp[]" type="text" disabled class="rnombre" style="width:99%;color:#C00;" id="costo_emp" value="{costo_emp4}" size="8"></td>
  </tr>
  <tr>
    <td class="tabla"><input name="empleados[]" type="text" disabled class="nombre" style="width:99%;color:#00C;" id="empleados" value="{empleados5}" size="3"></td>
    <td class="tabla"><input name="infonavit[]" type="text" disabled class="rnombre" style="width:99%;color:#F30;" id="infonavit" value="{infonavit5}" size="8"></td>
    <td class="tabla"><input name="prima[]" type="text" disabled class="rnombre" style="width:99%;color:#606;" id="prima[]" value="{prima5}" size="9"></td>
    <td class="tabla"><input name="costo_emp[]" type="text" disabled class="rnombre" style="width:99%;color:#C00;" id="costo_emp" value="{costo_emp5}" size="8"></td>
  </tr>
  <tr>
    <td class="tabla"><input name="empleados[]" type="text" disabled class="nombre" style="width:99%;color:#00C;" id="empleados" value="{empleados6}" size="3"></td>
    <td class="tabla"><input name="infonavit[]" type="text" disabled class="rnombre" style="width:99%;color:#F30;" id="infonavit" value="{infonavit6}" size="8"></td>
    <td class="tabla"><input name="prima[]" type="text" disabled class="rnombre" style="width:99%;color:#606;" id="prima[]" value="{prima6}" size="9"></td>
    <td class="tabla"><input name="costo_emp[]" type="text" disabled class="rnombre" style="width:99%;color:#C00;" id="costo_emp" value="{costo_emp6}" size="8"></td>
  </tr>
  <tr>
    <td class="tabla"><input name="empleados[]" type="text" disabled class="nombre" style="width:99%;color:#00C;" id="empleados" value="{empleados7}" size="3"></td>
    <td class="tabla"><input name="infonavit[]" type="text" disabled class="rnombre" style="width:99%;color:#F30;" id="infonavit" value="{infonavit7}" size="8"></td>
    <td class="tabla"><input name="prima[]" type="text" disabled class="rnombre" style="width:99%;color:#606;" id="prima[]" value="{prima7}" size="9"></td>
    <td class="tabla"><input name="costo_emp[]" type="text" disabled class="rnombre" style="width:99%;color:#C00;" id="costo_emp" value="{costo_emp7}" size="8"></td>
  </tr>
  <tr>
    <td class="tabla"><input name="empleados[]" type="text" disabled class="nombre" style="width:99%;color:#00C;" id="empleados" value="{empleados8}" size="3"></td>
    <td class="tabla"><input name="infonavit[]" type="text" disabled class="rnombre" style="width:99%;color:#F30;" id="infonavit" value="{infonavit8}" size="8"></td>
    <td class="tabla"><input name="prima[]" type="text" disabled class="rnombre" style="width:99%;color:#606;" id="prima[]" value="{prima8}" size="9"></td>
    <td class="tabla"><input name="costo_emp[]" type="text" disabled class="rnombre" style="width:99%;color:#C00;" id="costo_emp" value="{costo_emp8}" size="8"></td>
  </tr>
  <tr>
    <td class="tabla"><input name="empleados[]" type="text" disabled class="nombre" style="width:99%;color:#00C;" id="empleados" value="{empleados9}" size="3"></td>
    <td class="tabla"><input name="infonavit[]" type="text" disabled class="rnombre" style="width:99%;color:#F30;" id="infonavit" value="{infonavit9}" size="8"></td>
    <td class="tabla"><input name="prima[]" type="text" disabled class="rnombre" style="width:99%;color:#606;" id="prima[]" value="{prima9}" size="9"></td>
    <td class="tabla"><input name="costo_emp[]" type="text" disabled class="rnombre" style="width:99%;color:#C00;" id="costo_emp" value="{costo_emp9}" size="8"></td>
  </tr>
  <tr>
    <td class="tabla"><input name="empleados[]" type="text" disabled class="nombre" style="width:99%;color:#00C;" id="empleados" value="{empleados10}" size="3"></td>
    <td class="tabla"><input name="infonavit[]" type="text" disabled class="rnombre" style="width:99%;color:#F30;" id="infonavit" value="{infonavit10}" size="8"></td>
    <td class="tabla"><input name="prima[]" type="text" disabled class="rnombre" style="width:99%;color:#606;" id="prima[]" value="{prima10}" size="9"></td>
    <td class="tabla"><input name="costo_emp[]" type="text" disabled class="rnombre" style="width:99%;color:#C00;" id="costo_emp" value="{costo_emp10}" size="8"></td>
  </tr>
  <tr>
    <td class="tabla"><input name="empleados[]" type="text" disabled class="nombre" style="width:99%;color:#00C;" id="empleados" value="{empleados11}" size="3"></td>
    <td class="tabla"><input name="infonavit[]" type="text" disabled class="rnombre" style="width:99%;color:#F30;" id="infonavit" value="{infonavit11}" size="8"></td>
    <td class="tabla"><input name="prima[]" type="text" disabled class="rnombre" style="width:99%;color:#606;" id="prima[]" value="{prima11}" size="9"></td>
    <td class="tabla"><input name="costo_emp[]" type="text" disabled class="rnombre" style="width:99%;color:#C00;" id="costo_emp" value="{costo_emp11}" size="8"></td>
  </tr>
  <tr>
    <td class="tabla"><input name="empleados[]" type="text" disabled class="nombre" style="width:99%;color:#00C;" id="empleados" value="{empleados12}" size="3"></td>
    <td class="tabla"><input name="infonavit[]" type="text" disabled class="rnombre" style="width:99%;color:#F30;" id="infonavit" value="{infonavit12}" size="8"></td>
    <td class="tabla"><input name="prima[]" type="text" disabled class="rnombre" style="width:99%;color:#606;" id="prima[]" value="{prima12}" size="9"></td>
    <td class="tabla"><input name="costo_emp[]" type="text" disabled class="rnombre" style="width:99%;color:#C00;" id="costo_emp" value="{costo_emp12}" size="8"></td>
  </tr>
  <tr>
    <th class="tabla"><input name="filler1" type="text" disabled class="nombre" size="1"></th>
    <th class="tabla">&nbsp;</th>
    <th class="tabla">&nbsp;</th>
    <th class="tabla">&nbsp;</th>
  </tr>
  <tr>
    <th class="tabla"><input name="filler2" type="text" disabled class="nombre" size="1"></th>
    <th class="tabla">&nbsp;</th>
    <th class="tabla">&nbsp;</th>
    <th class="tabla">&nbsp;</th>
  </tr>
</table>
<!-- END BLOCK : costo_emp -->
  </td>
  </tr>
  </table>
  <p>
    <input type="button" class="boton" value="Regresar" onClick="document.location='./bal_mod_res.php'">
&nbsp;&nbsp;    
<input type="button" class="boton" value="Siguiente" onClick="validar()">
  </p>
  </form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
<!--
var form = document.form, cia = new Array();

function updateInputs() {
	for (var i = 0; i < form.importe.length; i++)
		if (form.mes[i].checked && form.importe_gral.value != "")
			form.importe[i].value = form.importe_gral.value;
	
	total();
}

function total() {
	var tmp, total_reserva = 0, total_pagado, gran_total;
	
	for (var i = 0; i < form.importe.length; i++)
		if ((!form.mes[i].checked && form.mes[i].disabled) || !form.mes[i].disabled)
		//if (!form.mes[i].checked)
			total_reserva += get_val(form.importe[i]);
	
	form.total_reserva.value = number_format(total_reserva, 2);
	
	total_pagado = get_val(form.total_pagado);
	gran_total = total_reserva - total_pagado;
	form.gran_total.value = number_format(gran_total, 2);
	form.gran_total.style.color = gran_total < 0 ? "#CC0000" : "#0000CC";
}

function validar() {
	var meses = 0;
	
	for (var i = 0; i < form.mes.length; i++)
		meses += form.mes[i].checked ? 1 : 0;
	
	if (meses == 0) {
		alert("Debe seleccionar al menos un mes");
		return false;
	}
	else if (get_val(form.total_reserva) <= 0) {
		alert("El total de las reservas no puede ser cero");
		return false;
	}
	else if (confirm("¿Son correctos los datos?"))
		form.submit();
	else
		return false;
}

window.onload = form.importe_gral.select();
-->
</script>
<!-- END BLOCK : reservas -->
</body>
</html>
